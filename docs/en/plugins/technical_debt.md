Plugin Technical Debt
=====================

Checks all files in your project for TODOs and other technical debt.

Configuration
-------------

### Options
* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **allowed_errors** [int, optional] - Allow `n` errors in a successful build (default: 0). 
  Use -1 to allow unlimited errors.
* **suffixes** [array, optional] - An array of file extensions to check (default: 'php').
* **directory** [string, optional] - directory to inspect (default: build root).
* **ignore** [array, optional] - directory to ignore (default: inherits ignores specified in setup).
* **searches** [array, optional] - Optional - Case-insensitive array of terms to search for. Defaults to TODO, TO DO, 
FIXME and FIX ME.
