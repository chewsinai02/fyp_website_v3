/* global use, db */
// MongoDB Playground

const database = 'nurse_tasks';
const collection = 'tasks';  // or whatever collection name you want to start with

// Switch to the database
use(database);

// Create a collection
db.createCollection(collection, {
    validator: {
        $jsonSchema: {
            bsonType: "object",
            required: ["title", "created_at", "updated_at"],
            properties: {
                title: {
                    bsonType: "string",
                    description: "must be a string and is required"
                },
                created_at: {
                    bsonType: "date",
                    description: "must be a date and is required"
                },
                updated_at: {
                    bsonType: "date",
                    description: "must be a date and is required"
                }
            }
        }
    }
}); 