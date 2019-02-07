Plugin Grunt
============

This plugin runs [Grunt](http://gruntjs.com/) tasks.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **grunt** - **[DEPRECATED]** Option `grunt` is deprecated and will be deleted in version 2.0. Use the options 
`binary_path` and `binary_name` instead.
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
