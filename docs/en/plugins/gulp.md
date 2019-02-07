Plugin gulp
===========

This plugin runs [gulpjs](https://gulpjs.com/) tasks.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **gulp** - **[DEPRECATED]** Option `gulp` is deprecated and will be deleted in version 2.0. Use the option 
`binary_path` and `binary_name` instead.
* **gulpfile** [string, optional] - GulpFile to run (defaults to `gulpfile.js`).
* **task** [string, optional] - The Gulp task to run.

### Example

```yml
  test:
    gulp:
      directory: "/path/to/run/gulp/from"
      gulpfile:  "gulpfile.js"
      task:      "css"
```
