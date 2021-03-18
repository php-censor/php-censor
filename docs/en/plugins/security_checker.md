SensioLabs Security Checker Plugin
==================================

Runs SensioLabs Security Checker by [Local PHP Security Checker](https://github.com/fabpot/local-php-security-checker) or [Symfony](https://symfony.com/download) against your project.

Configuration
-------------

### Options

* **binary_type** [string, required] - Type of checker ("symfony" or "local-php-security-checker", default value: "symfony").
* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: 0). 
  Use -1 to allow unlimited warnings.

### Additional Options

* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary vendor/bin, or a system-provided.
