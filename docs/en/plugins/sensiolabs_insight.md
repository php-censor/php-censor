Plugin SensioLabs Insight
=========================

Runs SensioLabs Insight against your build.

Configuration
-------------

### Options

* **allowed_warnings** [int, optional] - The warning limit for a successful build.
* **user_uuid** [string, required] - https://insight.sensiolabs.com/account Your user.
* **api_token** [string, required] - https://insight.sensiolabs.com/account Your password (API token).
* **project_uuid** [string, required] - Your Project UUID.
* **binary_name** [string, optional] - Allows you to provide a name of the binary
* **binary_path** [string, optional] - Allows you to provide a path to the binary


### Examples

```yml
test:
    sensiolabs_insight:
        allow_failures: true
        user_uuid: 'xxx-xxx-xxx-xxx-xxx'
        api_token: 'xxxx'
        project_uuid: 'xxx-xxx-xxx-xxx-xxx'
```
