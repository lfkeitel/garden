pub mod layouts;

use std::collections::HashMap;

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

pub struct Params {
    map: HashMap<&'static str, Param>,
}

impl Params {
    fn has_key(&self, key: &str) -> bool {
        self.map.contains_key(key)
    }

    fn get(&self, key: &str) -> Option<&Param> {
        self.map.get(key)
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
impl<const N: usize> From<[(&'static str, Param); N]> for Params {
    fn from(value: [(&'static str, Param); N]) -> Self {
        Self {
            map: HashMap::from(value),
        }
    }
}
