Plugin PostgreSQL
=================

Connects to a given PostgreSQL server and runs a list of queries.

Configuration
-------------

### Options

#### Build Settings options

* **host** [string, optional, default: '127.0.0.1'] - PostgreSQL host.
* **port** [int, optional, default: 5432] - PostgreSQL port.
* **dbname** [string, optional] - PostgreSQL database name.
* **options** [array, optional] - Additional PDO connection options ('PDO::ATTR_*').
* **user** [string] - PostgreSQL username.
* **password** [string] - PostgreSQL password.

#### Plugin options

* **queries** [array, optional, default: empty array] - Array of queries.

### Examples

```yaml
build_settings:
    pgsql:
        host:     'localhost'
        user:     'testuser'
        password: '12345678'

setup:
    pgsql:
        queries:
            - "CREATE DATABASE my_app_test;"

complete:
    pgsql:
        queries:
            - "DROP DATABASE my_app_test;"
```
