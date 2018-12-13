Plugin PHPLoc
=============

Runs [PHPLoc](https://github.com/sebastianbergmann/phploc) against your project and records some key metrics.

Configuration
-------------

### Options

* **directory** - Optional - The directory in which phploc should run. 
* **executable** [string, optional] -  Allows you to provide a path to php_loc executable

### Example

Run PHPLoc against the app directory only. This will prevent inclusion of code from 3rd party libraries that are 
included outside of the app directory.

```yml
test:
  php_loc:
    directory: "app"
```
