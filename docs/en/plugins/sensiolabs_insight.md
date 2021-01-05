Plugin SensioLabs Insight
=========================

Runs SensioLabs Insight against your build.

Configuration
-------------

### Options

* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: 0). 
  Use -1 to allow unlimited warnings.
* **user_uuid** [string, required] - https://insight.sensiolabs.com/account Your user.
* **auth_token** [string, required] - https://insight.sensiolabs.com/account Your password (API token).
* **project_uuid** [string, required] - Your Project UUID.

### Examples

```yaml
test:
    sensiolabs_insight:
        allow_failures: true
        user_uuid:      'xxx-xxx-xxx-xxx-xxx'
        auth_token:     'xxxx'
        project_uuid:   'xxx-xxx-xxx-xxx-xxx'
```
