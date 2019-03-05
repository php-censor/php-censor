Plugin Deployer
===============

Triggers a deployment of the project to run via [Deployer](http://phpdeployment.org)

Configuration
-------------

### Options

* **webhook_url** [string, required] - The URL to your Deployer WebHook.
* **reason** [string, optional] - Your deployment message. Default - PHP Censor Build #%BUILD% - %COMMIT_MESSAGE%
* **update_only** [bool, optional] - Whether the deployment should only be run if the currently deployed 
branches matches the one being built (Default: true).

### Examples

```yaml
success:
    deployer:
        webhook_url: "https://deployer.example.com/deploy/QZaF1bMIUqbMFTmKDmgytUuykRN0cjCgW9SooTnwkIGETAYhDTTYoR8C431t"
        reason:      "PHP Censor Build #%BUILD% - %COMMIT_MESSAGE%"
        update_only: true
```
