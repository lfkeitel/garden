mod templates;

use std::path::PathBuf;

use axum::extract::OriginalUri;
use templates::{layouts::main_page, Params};
use tower_http::services::ServeDir;

use axum::{response::Html, routing::get, Router};
use html_node::html;

async fn home(OriginalUri(uri): OriginalUri) -> Html<String> {
    main_page(
        html!(
            <p>"Hello world!"</p>
        ),
        Params::from([("styles", "seed-form".into())]),
        uri,
    )
    .to_string()
    .into()
}

#[tokio::main]
async fn main() {
    // build our application with a route
    let app = Router::new()
        // `GET /` goes to `root`
        .route("/", get(home))
        .nest_service("/static", ServeDir::new(PathBuf::from("static")));

    // run our app with hyper, listening globally on port 3000
    let listener = tokio::net::TcpListener::bind("0.0.0.0:3000").await.unwrap();
    axum::serve(listener, app).await.unwrap();
}
