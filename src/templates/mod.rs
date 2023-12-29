pub mod layouts;
pub mod pages;

use html_node::Node;
use std::collections::HashMap;

#[derive(Clone)]
pub enum Param {
    String(String),
    Vec(Vec<String>),
    Number(i64),
    Bool(bool),
}

impl From<&str> for Param {
    fn from(value: &str) -> Self {
        Self::String(value.to_string())
    }
}

impl From<String> for Param {
    fn from(value: String) -> Self {
        Self::String(value)
    }
}

impl From<i64> for Param {
    fn from(value: i64) -> Self {
        Self::Number(value)
    }
}

impl From<i32> for Param {
    fn from(value: i32) -> Self {
        Self::Number(value.into())
    }
}

impl From<Vec<String>> for Param {
    fn from(value: Vec<String>) -> Self {
        Self::Vec(value)
    }
}

impl From<bool> for Param {
    fn from(value: bool) -> Self {
        Self::Bool(value)
    }
}

#[derive(Clone)]
pub struct Params {
    map: HashMap<String, Param>,
}

impl Params {
    pub fn new() -> Self {
        Self {
            map: HashMap::new(),
        }
    }

    pub fn add(&mut self, key: &str, val: Param) {
        self.map.insert(key.to_string(), val);
    }

    pub fn merge(&mut self, other: &Params) {
        for (name, value) in &other.map {
            self.map.insert(name.to_string(), value.clone());
        }
    }

    fn has_key(&self, key: &str) -> bool {
        self.map.contains_key(key)
    }

    fn get_string(&self, key: &str) -> String {
        if let Some(v) = self.map.get(key) {
            match v {
                Param::String(s) => s.to_owned(),
                Param::Number(n) => n.to_string(),
                Param::Vec(_) => "".to_owned(),
                Param::Bool(b) => b.to_string(),
            }
        } else {
            "".to_owned()
        }
    }

    fn get_bool(&self, key: &str) -> bool {
        if let Some(v) = self.map.get(key) {
            match v {
                Param::String(s) => s == "true",
                Param::Number(n) => *n != 0,
                Param::Vec(v) => !v.is_empty(),
                Param::Bool(b) => *b,
            }
        } else {
            false
        }
    }

    fn get_vec(&self, key: &str) -> Vec<String> {
        if let Some(v) = self.map.get(key) {
            match v {
                Param::Vec(v) => v.to_owned(),
                Param::String(s) => vec![s.to_owned()],
                Param::Number(n) => vec![n.to_string()],
                Param::Bool(b) => vec![b.to_string()],
            }
        } else {
            Vec::new()
        }
    }
}

impl<const N: usize> From<[(String, Param); N]> for Params {
    fn from(value: [(String, Param); N]) -> Self {
        Self {
            map: HashMap::from(value),
        }
    }
}

impl<const N: usize> From<[(&'static str, Param); N]> for Params {
    fn from(value: [(&'static str, Param); N]) -> Self {
        let mut map = HashMap::new();
        for v in value {
            map.insert(v.0.to_string(), v.1);
        }
        Self { map }
    }
}

type TemplateFn = fn(Node, Params) -> Node;

#[derive(Clone)]
pub struct Templates {
    global_params: Params,
    layouts: HashMap<String, TemplateFn>,
}

impl Templates {
    pub fn new() -> Self {
        Templates {
            layouts: HashMap::from([("main".to_owned(), layouts::main_page as TemplateFn)]),
            global_params: Params::new(),
        }
    }

    pub fn with_params(&self, params: Params) -> Self {
        let mut t = self.clone();
        t.global_params.merge(&params);
        t
    }

    pub fn layout(&self, name: &str) -> Option<Layout> {
        match self.layouts.get(name) {
            Some(l) => Some(Layout {
                template: *l,
                global_params: &self.global_params,
            }),
            None => None,
        }
    }

    pub fn set_param<T: Into<Param>>(&mut self, name: &str, value: T) {
        self.global_params
            .map
            .insert(name.to_string(), value.into());
    }
}

pub struct Layout<'a> {
    template: TemplateFn,
    global_params: &'a Params,
}

impl<'a> Layout<'a> {
    pub fn exec(&self, content: Node, mut params: Params) -> String {
        params.merge(self.global_params);

        (self.template)(content, params).to_string()
    }
}
