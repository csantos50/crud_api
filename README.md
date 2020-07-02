# CRUD API

## Installation

Clone this repository and run Composer as follows:
To create your new Zend Framework project:

```bash
git clone git@github.com:csantos50/crud_api.git

composer install
```

## Run

```bash
composer serve
```
 You can then visit the site at http://localhost:8080/


## Endpoints

List all Categories
```bash
[GET] http://localhost:8080/category
```

View Category
```bash
[GET] http://localhost:8080/view[:id]
```

Edit Categories
```bash
[POST] http://localhost:8080/edit[:id]

{
    "name":"nameCatehory"
}
```

Delete Category
```bash
[GET] http://localhost:8080/delete[:id]
```

Search Category
```bash
[POST] http://localhost:8080/category/search

{
    "name":"nameCatehory"
}
```
The search can be done by "id","name","created","modified"
