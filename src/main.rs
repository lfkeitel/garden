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

#[derive(Clone, Deserialize)]
struct Config {
    #[serde(default)]
    dev_mode: bool,

    #[serde(default)]
    server: ServerConfig,

    #[serde(default)]
    mongo_db: MongoDBConfig,

    openweather: Option<OpenWeatherConfig>,
}

fn default_server_address() -> String {
    "127.0.0.1".to_string()
}

fn default_server_port() -> u16 {
    3000
}

#[derive(Clone, Deserialize)]
struct ServerConfig {
    #[serde(default = "default_server_address")]
    address: String,

    #[serde(default = "default_server_port")]
    port: u16,
}

impl Default for ServerConfig {
    fn default() -> Self {
        Self {
            address: default_server_address(),
            port: default_server_port(),
        }
    }
}

#[derive(Clone, Deserialize)]
struct MongoDBConfig {
    hostname: String,
    port: u16,
    database: String,
}

impl Default for MongoDBConfig {
    fn default() -> Self {
        Self {
            hostname: "127.0.0.1".to_string(),
            port: 27017,
            database: "garden".to_string(),
        }
    }
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

    let server_bind_address = format!(
        "{host}:{port}",
        host = config.server.address,
        port = config.server.port
    );

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

    let listener = tokio::net::TcpListener::bind(&server_bind_address)
        .await
        .unwrap();

    println!("Server running on {}", &server_bind_address);
    axum::serve(listener, app).await.unwrap();
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

async fn index(
    State(state): State<AppState>,
    Extension(template): Extension<Templates>,
) -> Html<String> {
    let bed_collection = state.database.beds();
    let beds = bed_collection
        .all(None)
        .await
        .unwrap()
        .into_iter()
        .map(|item| text!("{}", item.name));

    let main_layout = template.layout("main").unwrap();
    main_layout
        .exec(
            html!(
                <p>"Hello world!"</p>
                <p>
                { beds }
                </p>
            ),
            Params::new(),
        )
        .into()
}
