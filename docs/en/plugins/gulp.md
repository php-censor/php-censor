Plugin gulp
============

This plugin runs [gulpjs](https://gulpjs.com/) tasks.

Configuration
-------------

### Options

- **directory** [string, optional] - The directory in which to run Grunt (defaults to build root.)
- **gulp** (deprecated) : replaced by **executable**, same functionality
- **executable** [string, optional] -  Allows you to provide a path to Grunt (defaults to PHP Censor root, vendor/bin, or a 
system-provided Grunt).
- **gulpfile** [string, optional] - GulpFile to run (defaults to `gulpfile.js`).
- **task** [string, optional] - The Gulp task to run.

### Example

```yml
  test:
    gulp:
      directory: "path to run gulp in"
      grunt: "path to grunt executable"
      gruntfile: "gruntfile.js"
      task: "css"
```
