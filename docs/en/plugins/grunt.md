Plugin Grunt
============

This plugin runs [Grunt](http://gruntjs.com/) tasks.

Configuration
-------------

### Options

* **directory** [string, optional] - The directory in which to run Grunt (defaults to build root.)
* **grunt** - **[DEPRECATED]** - Use instead option `binary_path` and `binary_name`!
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary
* **binary_path** [string, optional] - Allows you to provide a path to the binary
* **gruntfile** [string, optional] - Gruntfile to run (defaults to `Gruntfile.js`).
* **task** [string, optional] - The Grunt task to run.

### Example

```yml
  test:
    grunt:
      directory: "path to run grunt in"
      grunt: "path to grunt executable"
      gruntfile: "gruntfile.js"
      task: "css"
```
