Plugin Wipe
===========

The plugin deletes a directory.

Configuration
-------------

### Options

* **directory** [string, required] - The directory path to delete.

### Example

```yml
complete:
    wipe_step:
        plugin: wipe
        directory: "/path/to/directory"
```
