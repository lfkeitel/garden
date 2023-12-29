use super::Params;

use html_node::{html, text, Node};

pub fn login_get(body: Node, params: Params) -> Node {
    html!(<p>"Login"</p>)
}
