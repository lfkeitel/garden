use super::Params;

use html_node::{html, text, Node};

pub fn main_page(body: Node, params: Params) -> Node {
    let request_uri = params.get_string("request_uri");
    html! (
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=Edge">
                <meta name="viewport" content="width=device-width, initial-scale=0.75">

                <title>"The Garden"</title>

                <link rel="stylesheet" href="/static/styles/main.css">
                { params.get_vec("styles").into_iter().map(|item| html!{
                    <link rel="stylesheet" href=format!("/static/styles/{item}.css")>
                }) }
            </head>
            <body>
                <div class="sidebar">
                    <nav>
                        <ul>
                            <li><a href="/" class={if request_uri == "/" { "active-page" } else {""}}>Home</a></li>
                            <li><a href="/logs" class={if request_uri == "/logs" { "active-page" } else {""}}>Logs</a></li>
                            <li><a href="/plantings?filter=Active" class={if request_uri == "/plantings" { "active-page" } else {""}}>Plantings</a></li>
                            <li><a href="/seeds" class={if request_uri == "/seeds" { "active-page" } else {""}}>Seeds</a></li>
                            <li><a href="/wishlist" class={if request_uri == "/wishlist" { "active-page" } else {""}}>Wishlist</a></li>
                            <li><a href="/beds" class={if request_uri == "/beds" { "active-page" } else {""}}>Beds</a></li>
                            { if params.get_bool("is_logged_in") {
                                html!{<li><a href="/logout">Logout</a></li>}
                            } else {
                                html!{<li><a href="/login" class={if request_uri == "/login" { "active-page" } else {""}}>Login</a></li>}
                            }}
                        </ul>
                    </nav>
                </div>

                <div class="content">
                    { if params.has_key("toast") {
                        html!{<div class="toast">text!(params.get_string("toast"))</div>}
                    } else {
                        text!("")
                    }}

                    <div class="main-content">
                        {body}
                    </div>
                </div>

                <script type="text/javascript" src="/static/scripts/common.js"></script>
                { params.get_vec("scripts").into_iter().map(|item| html!{
                    <script type="text/javascript" src=format!("/static/scripts/{item}.js")></script>
                }) }
            </body>
        </html>
    )
}
