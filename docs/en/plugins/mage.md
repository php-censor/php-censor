Plugin Mage
===========

Triggers a deployment of the project to run via [Mage](https://github.com/andres-montanez/Magallanes)

Configuration
-------------

### Options

* **env** [string, required] - The environment name.
* **bin** [string, optional] - The mage executable path

### Examples

```yaml
success:
    deploy:
        mage:
            env: production
```
