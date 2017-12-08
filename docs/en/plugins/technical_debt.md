Plugin Technical Debt
=====================

Checks all files in your project for TODOs and other technical debt.

Configuration
-------------

### Options
* **allowed_errors** [int, optional] - The error limit for a successful build (default: 0). -1 disables errors. Setting 
allowed_errors in conjunction with zero_config will override zero_config.
* **suffixes** [array, optional] - An array of file extensions to check (default: 'php')
* **directory** [string, optional] - directory to inspect (default: build root)
* **ignore** [array, optional] - directory to ignore (default: inherits ignores specified in setup)
* **searches** [array, optional] - Optional - Case-insensitive array of terms to search for. Defaults to TODO, TO DO, 
FIXME and FIX ME.
