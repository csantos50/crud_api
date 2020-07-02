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
http://localhost:8080/category
```

View Category
```bash
http://localhost:8080/view[:id]
```

Edit Categories
```bash
http://localhost:8080/edit[:id]

{
    "name":"nameCatehory"
}

```

Delete Category
```bash
http://localhost:8080/delete[:id]
```
