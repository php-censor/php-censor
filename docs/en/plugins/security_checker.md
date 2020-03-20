SensioLabs Security Checker Plugin
==================================

Runs [SensioLabs Security Checker](https://github.com/sensiolabs/security-checker) against your project.

Configuration
-------------

### Options

* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: 0). 
  Use -1 to allow unlimited warnings.

### Additional Options

When you set **binary_name** (probably symfony), then instead of directly contacting the online security checker, this 
binary is run like `symfony security:check`. The same security data is used, but it is downloaded and cached. 
See [checking for vulnerabilites](https://github.com/FriendsOfPHP/security-advisories#checking-for-vulnerabilities)

* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary vendor/bin, or a system-provided.
