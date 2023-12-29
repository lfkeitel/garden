use mongodb::bson::Document;
use mongodb::options::FindOneOptions;
use mongodb::{Client, Collection};
use serde::de::DeserializeOwned;

use crate::models;

#[derive(Clone)]
pub struct Database {
    db: mongodb::Database,
}

impl Database {
    pub async fn connect(mongo_uri: &str, database: &str) -> Result<Self, String> {
        let client = match Client::with_uri_str(mongo_uri).await {
            Ok(c) => c,
            Err(_) => {
                return Err(
                    "Failed connecting to MongoDB. Please check connection settings.".to_string(),
                );
            }
        };

        let database = client.database(database);

        match database.list_collection_names(None).await {
            Ok(_) => {}
            Err(e) => {
                return Err(format!(
                    "Failed connecting to MongoDB. Please check connection settings.\n{}",
                    e
                ));
            }
        }
        Ok(Database { db: database })
    }

    pub fn beds(&self) -> Store<models::Bed> {
        Store {
            collection: self.db.collection("bed"),
        }
    }
}

pub struct Store<T> {
    collection: Collection<T>,
}

impl<T> Store<T>
where
    T: DeserializeOwned + Unpin + Send + Sync,
{
    pub async fn find_one(
        &self,
        filter: impl Into<Option<Document>>,
        options: impl Into<Option<FindOneOptions>>,
    ) -> mongodb::error::Result<Option<T>> {
        self.collection.find_one(filter, options).await
    }
}
