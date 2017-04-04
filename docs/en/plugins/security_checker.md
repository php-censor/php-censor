SensioLabs Security Checker Plugin
==================================

Runs [SensioLabs Security Checker](https://github.com/sensiolabs/security-checker) against your project.

Configuration
-------------

### Options

- **allowed_warnings** [int, optional] - The warning limit for a successful build (default: 0). -1 disables warnings.

### Example

Run PHPLoc against the app directory only. This will prevent inclusion of code from 3rd party libraries that are included outside of the app directory.

```yml
test:
  security_checker:
    allowed_warnings: -1
```
