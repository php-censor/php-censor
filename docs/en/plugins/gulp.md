Plugin gulp
============

This plugin runs [gulpjs](https://gulpjs.com/) tasks.

Configuration
-------------

### Options

* **directory** [string, optional] - The directory in which to run Grunt (defaults to build root.)
* **gulp** - **[DEPRECATED]** - Option `gulp` deprecated and will be deleted in version 2.0 (Use options 
`binary_path` and `binary_name` instead)!
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary
* **binary_path** [string, optional] - Allows you to provide a path to the binary
* **gulpfile** [string, optional] - GulpFile to run (defaults to `gulpfile.js`).
* **task** [string, optional] - The Gulp task to run.

### Example

```yml
  test:
    gulp:
      directory: "path to run gulp in"
      grunt: "path to grunt executable"
      gruntfile: "gruntfile.js"
      task: "css"
```
