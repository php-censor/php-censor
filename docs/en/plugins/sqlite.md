Plugin SQLite
=============

Connects to a given SQLite DB and runs a list of queries.

Configuration
-------------

#### Plugin options

* **queries** [array, optional, default: empty array] - Array of queries.

#### Build Settings options

* **sqlite** - All child properties are required.
    * **path** [string] - SQLite database path.
    * **options** [array, optional] - Additional PDO connection options ('PDO::ATTR_*').

### Examples

```yaml
build_settings:
    sqlite:
        path: '/path/to/sqlite.sqlite'

setup:
    sqlite:
        queries:
            - "CREATE DATABASE my_app_test;"

complete:
    sqlite:
        queries:
            - "DROP DATABASE my_app_test;"
```
