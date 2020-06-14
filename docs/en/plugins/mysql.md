Plugin MySQL
============

Connects to a given MySQL server and runs a selection of queries.

Configuration
-------------

### Options

#### Build Settings options

* **host** [string, optional, default: '127.0.0.1'] - MySQL host.
* **port** [int, optional, default: 3306] - MySQL port.
* **dbname** [string, optional] - MySQL database name.
* **charset** [string, optional] - MySQL charset ('UTF8' for example).
* **options** [array, optional] - Additional PDO connection options ('PDO::ATTR_*').
* **user** [string] - MySQL username.
* **password** [string] - MySQL password.

#### Plugin options

* **queries** [array, optional, default: empty array] - Array of queries.
* **imports** [array, optional, default: empty array] - Array of imports.

### Examples

```yaml
build_settings:
    mysql:
        host:     'localhost'
        user:     'testuser'
        password: '12345678'

setup:
    mysql:
        queries:
            - "CREATE DATABASE my_app_test;"

complete:
    mysql:
        queries:
            - "DROP DATABASE my_app_test;"
```

Import SQL from file:
```yaml
setup:
    mysql:
        imports:
            - database: "foo"             # Database name
              file:     "path/dump.sql"  # Relative path in build folder
```
