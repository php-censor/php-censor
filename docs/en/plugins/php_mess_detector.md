Plugin PHP Mess Detector
========================

Runs PHP Mess Detector against your build. Records some key metrics, and also reports errors and warnings.

Configuration
-------------

### Options

* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: 0). 
  Use -1 to allow unlimited warnings.
* **suffixes** [array, optional] - An array of file extensions to check (default: 'php').
* **rules** [array, optional] - Array of rulesets that PHPMD should use when checking your build or a string containing 
at least one slash, will be treated as path to PHPMD ruleset. See http://phpmd.org/rules/index.html for complete 
details on the rules. (default: ['codesize', 'unusedcode', 'naming']).

### Examples

```yaml
test:
    php_mess_detector:
        directory: 'app'
        ignore:
            - 'vendor'
        allowed_warnings: -1
        rules:
            - "cleancode"
            - "controversial"
            - "codesize"
            - "design"
            - "naming"
            - "unusedcode"
            - "somedir/customruleset.xml"
```

### Additional Options

The following general options can also be used: 

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **directory** [string, optional] - This option lets you specify the tests directory to run.
* **ignore** [optional] - A list of files / paths to ignore (default: build_settings > ignore).
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary vendor/bin, or a system-provided.
* **priority_path** [string, optional] - Priority path for locating the plugin binary (Allowable values: 
  `local` (Local current build path) | 
  `global` (Global PHP Censor 'vendor/bin' path) |
  `system` (OS System binaries path, /bin:/usr/bin etc.). 
  Default order: local -> global -> system)
