mod database;
mod models;
mod templates;

use std::fs::read_to_string;
use std::path::PathBuf;

use axum::extract::{Request, State};
use axum::middleware::{self, Next};
use axum::response::Response;
use axum::Extension;
use axum::{response::Html, routing::get, Router};
use html_node::{html, text};
use mongodb::bson::doc;
use serde::Deserialize;
use templates::Params;
use tower_http::services::ServeDir;

use database::Database;
use templates::Templates;

async fn index(
    State(state): State<AppState>,
    Extension(template): Extension<Templates>,
) -> Html<String> {
    let bed_collection = state.database.beds();
    let pot = bed_collection
        .find_one(
            doc! {
                "name": "Small Pot"
            },
            None,
        )
        .await
        .unwrap()
        .unwrap();

    let main_layout = template.layout("main").unwrap();
    main_layout
        .exec(
            html!(
                <p>"Hello world!"</p>
                <p>{text!("{}", pot.name)}</p>
            ),
            Params::new(),
        )
        .into()
}

async fn req_template_params(
    State(state): State<AppState>,
    mut req: Request,
    next: Next,
) -> Response {
    let t = state
        .templates
        .with_params(Params::from([("request_uri", req.uri().path().into())]));
    req.extensions_mut().insert(t);

    next.run(req).await
}

/*
dev_mode = true

[mongo_db]
hostname = 'localhos'
port = 27017
database = 'garden'

[openweather]
apikey = ''
location = {lat: '', lon: ''}
*/
#[derive(Clone, Deserialize)]
struct Config {
    dev_mode: bool,
    mongo_db: MongoDBConfig,
    openweather: OpenWeatherConfig,
}

#[derive(Clone, Deserialize)]
struct MongoDBConfig {
    hostname: String,
    port: i16,
    database: String,
}

#[derive(Clone, Deserialize)]
struct OpenWeatherConfig {
    apikey: String,
    location: LocationConfig,
}

#[derive(Clone, Deserialize)]
struct LocationConfig {
    lat: String,
    lon: String,
}

#[derive(Clone)]
struct AppState {
    templates: Templates,
    config: Config,
    database: Database,
}

#[tokio::main]
async fn main() {
    let config: Config = toml::from_str(&read_to_string("private/config.toml").unwrap()).unwrap();

    let mongo_connect = format!(
        "mongodb://{host}:{port}",
        host = config.mongo_db.hostname,
        port = config.mongo_db.port
    );

    println!("Connecting to MongoDB: {}", mongo_connect);
    let database = match Database::connect(&mongo_connect, &config.mongo_db.database).await {
        Ok(d) => d,
        Err(e) => {
            println!("{}", e);
            return;
        }
    };
    println!("Connected to MongoDB");

    let state = AppState {
        templates: Templates::new(),
        config,
        database,
    };

    let app = Router::new()
        .route("/", get(index))
        .nest_service("/static", ServeDir::new(PathBuf::from("static")))
        .route_layer(middleware::from_fn_with_state(
            state.clone(),
            req_template_params,
        ))
        .with_state(state);

    let listener = tokio::net::TcpListener::bind("127.0.0.1:3000")
        .await
        .unwrap();
    println!("Server running on 127.0.0.1:3000");
    axum::serve(listener, app).await.unwrap();
}
