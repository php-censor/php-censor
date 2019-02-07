Plugin Technical Debt
=====================

Checks all files in your project for TODOs and other technical debt.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **allowed_errors** [int, optional] - Allow `n` errors in a successful build (default: 0). 
  Use -1 to allow unlimited errors.
* **suffixes** [array, optional] - An array of file extensions to check (default: 'php').
* **searches** [array, optional] - Optional - Case-insensitive array of terms to search for. Defaults to TODO, TO DO, 
FIXME and FIX ME.
