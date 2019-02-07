Plugin PHP Mess Detector
========================

Runs PHP Mess Detector against your build. Records some key metrics, and also reports errors and warnings.

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: 0). 
  Use -1 to allow unlimited warnings.
* **suffixes** [array, optional] - An array of file extensions to check (default: 'php').
* **ignore** [array, optional] - An array of files/paths to ignore (default: build_settings > ignore).
* **path** - **[DEPRECATED]** Option `path` is deprecated and will be deleted in version 2.0. Use the option 
`directory` instead.
* **directory** - Optional - directory in which to run PHPMD (default: `%BUILD_PATH%`).
* **rules** [array, optional] - Array of rulesets that PHPMD should use when checking your build or a string containing 
at least one slash, will be treated as path to PHPMD ruleset. See http://phpmd.org/rules/index.html for complete 
details on the rules. (default: ['codesize', 'unusedcode', 'naming']).

### Examples

```yml
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
