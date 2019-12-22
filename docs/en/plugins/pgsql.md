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
* **pass** [string] - **[DEPRECATED]** Option `pass` is deprecated and will be deleted in version 2.0. Use the option 
`password` instead.
* **password** [string] - PostgreSQL password.

#### Plugin options

### Examples

```yaml
build_settings:
    pgsql:
        host:     'localhost'
        user:     'testuser'
        password: '12345678'

setup:
    pgsql:
        - "CREATE DATABASE my_app_test;"

complete:
    pgsql:
        - "DROP DATABASE my_app_test;"
```
