Plugin SQLite
=============

Connects to a given SQLite DB and runs a list of queries.

Configuration
-------------

### Examples

```yaml
build_settings:
    sqlite:
        path: '/path/to/sqlite.sqlite'

setup:
    sqlite:
        - "CREATE DATABASE my_app_test;"

complete:
    sqlite:
        - "DROP DATABASE my_app_test;"
```
