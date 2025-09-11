# Recruitment Task
 
Create a simple page with a table of records stored in a database.
 
| No. | Registration Number | Brand    | Model   | Vehicle Type | Creation Date       | Modification Date  | Actions  |
|----|------------------|----------|---------|--------------|--------------------|------------------|---------|
| 1  | DW22233         | Mercedes | MP-4    | Truck        | 2020-05-05 12:25   | 2021-06-07 15:05 | (e) (d) |
| 2  | BORQ4500        | MAN      | TGE     | Bus          | 2021-06-06 14:01   | 2021-06-06 14:01 | (e) (d) |
| 3  | DW33445         | Toyota   | Corolla | Passenger    | 2021-07-05 10:25   | 2021-08-07 12:05 | (e) (d) |
 
The table should contain the following columns:
- No.
- Vehicle registration number
- Brand
- Model
- Vehicle type (passenger, bus, truck)
- Creation date
- Modification date
- Actions (without sorting)
 
## Requirements:
 
1. The registration number must be in uppercase letters.
2. The date format should be: `YYYY-MM-DD HH:MM`.
3. The actions column should contain two action icons: edit + delete.
4. Use Vue for the table, adding, and editing records (along with the Vuetify library - [Vuetify v2](https://v2.vuetifyjs.com/en/components/data-tables/)). Data should be entered in a popup and saved.
5. Our company uses Composer (PHP), Yarn, Axios, Webpack (JS), and SASS (CSS).
6. The table should support sorting and pagination (handled on the frontend).
7. The DDD approach is required (without using CQRS - see directory structure below).
8. Upload the code to **a public GitHub/GitLab repository**. Do not fork our repository. Download our code, initialize Git in it, and push it to your own repository.
 
## Simplified DDD Structure (example from our company):
 
```
src
│── Domain
│   ├── Entity
│   │   ├── Vehicle.php
│   ├── Repository
│   │   ├── VehicleRepositoryInterface.php (repository interfaces)
│   ├── Service
│   │   ├── VehiclesBuilder.php (list building)
│   │   ├── VehiclesReader.php (reading, e.g., a single record)
│   │   ├── VehiclesWriter.php (saving and updating)
│   ├── ValueObject
│── Persistence
│   ├── Repository
│   │   ├── VehicleRepository.php (repository implementation)
```
 
## Project Setup
```sh
composer install
touch db/assqlite.db  # create a database file
yarn install
yarn dev
php -S localhost:8008  # start the server
```
 
### Creating the `vehicles` table in SQLite:
```sql
CREATE TABLE vehicles (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  registration_number TEXT(16),
  brand TEXT(60),
  model TEXT(60),
  "type" TEXT,
  created_at INTEGER,
  updated_at INTEGER
);
```
 
## API Endpoints
- `/vehicles` **[GET]** - index, main template
- `/vehicles/list` **[GET]** - vehicle list
- `/vehicles/save/{id}` **[POST]** - save vehicle
- `/vehicles/delete/{id}` **[POST]** - delete vehicle
 
## Working with the Code - What Needs to Be Done?
The controller `/app/Controller/VehicleController.php` and the classes in the DDD structure in the `/src` directory need to be completed.
 
The entire table and Vuetify components should be completed in the `/resources` directory.  
Use the already installed `axios` to call the API endpoints. Use the `VDataTable` component from Vuetify and **do not use** sorting and pagination on the backend.  
 
Please note that we are using **Vuetify v2**, not v3, so make sure to check the correct version of the documentation.
 
Write the best code you can and try to apply the latest best practices. 🚀  
 
**Good luck!** 🎉
