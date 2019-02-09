Plugin Mage v3
==============

Triggers a deployment of the project to run via [Mage v3](https://github.com/andres-montanez/Magallanes) (Magallanes).

Configuration
-------------

Mage must be installed locally in your project as it is not provided by PHP Censor.

### Options

* **env** [string, required] - The environment name.
* **bin** [string, required] - The mage executable path
* **log_dir** [string, optional] - The mage logs path

### Examples

```yaml
success:
    mage3:
        env: production
        bin: bin/mage
```
