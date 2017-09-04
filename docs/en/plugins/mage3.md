Plugin Mage v3
==============

Triggers a deployment of the project to run via [Mage v3](https://github.com/andres-montanez/Magallanes)

Configuration
-------------

### Options

* **env** [required, string] - The environment name

### Examples

```yaml
deploy:
    mage3:
        env: production
```

### Options for config.yml

* **bin** [optional, string] - The mage executable path
* **log_dir** [optional, string] - The mage logs path

### Examples

```yaml
mage:
    bin: /usr/local/bin/mage
    log_dir: ./var/log
```
