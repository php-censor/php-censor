Plugin Deployer
===============

Triggers a deployment of the project to run via [Deployer](http://phpdeployment.org)

Configuration
-------------

### Options

* **webhook_url** [string, required] - The URL to your Deployer WebHook.
* **reason** [string, optional] - Your deployment message. Default - PHP Censor Build #%BUILD_ID% - %COMMIT_MESSAGE%
* **update_only** [bool, optional] - Whether the deployment should only be run if the currently deployed 
branches matches the one being built (Default: true).

### Examples

```yml
success:
    deployer_step:
        plugin:      deployer
        webhook_url: "https://deployer.example.com/deploy/QZaF1bMIUqbMFTmKDmgytUuykRN0cjCgW9SooTnwkIGETAYhDTTYoR8C431t"
        reason:      "PHP Censor Build #%BUILD_ID% - %COMMIT_MESSAGE%"
        update_only: true
```
