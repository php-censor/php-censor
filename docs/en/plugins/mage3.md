Plugin Mage v3
==============

Triggers a deployment of the project to run via [Mage v3](https://github.com/andres-montanez/Magallanes)

Configuration
-------------

### Options

* **env** [string, required] - The environment name.
* **bin** [string, optional] - The mage executable path
* **log_dir** [string, optional] - The mage logs path

### Examples

```yaml
success:
	deploy:
	    mage3:
	        env: production
```
