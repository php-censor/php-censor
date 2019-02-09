Plugin Telegram
===============

Telegram plugin for PHP Censor.

Add to project
--------------

In the PHP Censor Project config section add the Telegram trigger

```yaml
complete:
    telegram:
        api_key: "<YOUR_BOT_TOKEN_HERE>"
        message: "[%ICON_BUILD%] [%PROJECT_TITLE%](%PROJECT_URI%) - [Build #%BUILD%](%BUILD_URI%) has finished for commit [%SHORT_COMMIT% (%COMMIT_EMAIL%)](%COMMIT_URI%) on branch [%BRANCH%](%BRANCH_URI%)"
        recipients:
            - "<user id>"
            - "-<group id>"
            - "@<channel id>"
        send_log: true
```
