Plugin SlackNotify
==================

This plugin joins a [Slack](https://www.slack.com/) room and sends a user-defined message, for example a "Build 
Succeeded" message.

Configuration
-------------

### Options

| Field | Required? | Description |
|-------|-----------|-------------|
| `webhook_url` | Yes | The URL to your Slack WebHook |
| `room`      | No | Your Slack room name. Default - #php-censor |
| `username`  | No | The name to send the message as. Default - PHP Censor |
| `icon`      | No | The URL to the user icon or an emoji such as :ghost:. Default - The value configured on Slack's 
WebHook setup |
| `message`   | No | The message to send to the room. Default - `<%PROJECT_LINK%|%PROJECT_TITLE%> - <%BUILD_LINK%|Build 
#%BUILD_ID%> has finished for commit <%COMMIT_LINK%|%SHORT_COMMIT_ID% (%COMMITTER_EMAIL%)> on branch <%BRANCH_LINK%|%BRANCH%>` |
| `show_status` | No | Whether or not to append the build status as an attachment in slack. Default - true

### Examples

Send a message if the build fails:

```yml
failure:
    slack_notify:
        webhook_url: "https://hooks.slack.com/services/R212T827A/G983UY31U/aIp0yuW9u0iTqwAMOEwTg"
        room:        "#php-censor"
        username:    "PHP Censor"
        icon:        ":ghost:"
        message:     "%PROJECT_TITLE% - build %BUILD_ID% failed! :angry:"
        show_status: false
```

Send a message if the build is successful:

```yml
success:
    slack_notify:
        webhook_url: "https://hooks.slack.com/services/R212T827A/G983UY31U/aIp0yuW9u0iTqwAMOEwTg"
        room:        "#php-censor"
        username:    "PHP Censor"
        icon:        ":ghost:"
        message:     "%PROJECT_TITLE% - build %BUILD_ID% succeeded! :smiley:"
        show_status: false
```

Send a message every time the build runs:

```yml
complete:
    slack_notify:
        webhook_url: "https://hooks.slack.com/services/R212T827A/G983UY31U/aIp0yuW9u0iTqwAMOEwTg"
        room:        "#php-censor"
        username:    "PHP Censor"
        icon:        ":ghost:"
        message:     "%PROJECT_TITLE% - build %BUILD_ID% completed"
        show_status: true
```
