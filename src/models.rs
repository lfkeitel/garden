use mongodb::bson::doc;
use mongodb::bson::oid::ObjectId;
use serde::{Deserialize, Serialize};

#[derive(Serialize, Deserialize)]
pub struct Bed {
    #[serde(alias = "_id")]
    pub id: ObjectId,
    pub added: String,
    pub cols: i32,
    pub rows: i32,
    pub notes: String,
    pub name: String,
}
