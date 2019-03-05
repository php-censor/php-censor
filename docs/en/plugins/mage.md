Plugin Mage
===========

Triggers a deployment of the project to run via [Mage](https://github.com/andres-montanez/Magallanes) (Magallanes).

Configuration
-------------

Mage must be installed locally in your project as it is not provided by PHP Censor.

### Options

* **env** [string, required] - The environment name.
* **bin** [string, required] - The mage executable path

### Examples

```yaml
success:
    mage:
        env: production
        bin: bin/mage
```
