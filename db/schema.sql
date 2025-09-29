CREATE TABLE vehicles (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  registration_number TEXT(16) UNIQUE,
  brand TEXT(60),
  model TEXT(60),
  "type" TEXT,
  created_at INTEGER,
  updated_at INTEGER
);