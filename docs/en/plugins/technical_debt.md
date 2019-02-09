Plugin Technical Debt
=====================

Checks all files in your project for TODOs and other technical debt.

Configuration
-------------

### Options

* **allowed_errors** [int, optional] - Allow `n` errors in a successful build (default: 0). 
  Use -1 to allow unlimited errors.
* **suffixes** [array, optional] - An array of file extensions to check (default: 'php').
* **searches** [array, optional] - Optional - Case-insensitive array of terms to search for. Defaults to `TODO`, `TO DO`, 
`FIXME` and `FIX ME`.

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
