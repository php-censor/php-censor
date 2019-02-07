Plugin Mage
===========

Triggers a deployment of the project to run via [Mage](https://github.com/andres-montanez/Magallanes)

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **env** [required, string] - The environment name.

### Examples

```yaml
deploy:
    mage:
        env: production
```

### Options for config.yml

* **bin** [optional, string] - The mage executable path

### Examples

```yaml
mage:
    bin: /usr/local/bin/mage
```
