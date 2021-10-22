Plugin WebhookNotify
==================

This plugin sends a JSON web hook to a configured URL.

Configuration
-------------

### Options

| Field | Required? | Description |
|-------|-----------|-------------|
| `url` | Yes | The URL to send the webhook to |

### Examples

Send a message if the build fails:

```yml
failure:
    webhook_notify_step:
        plugin: webhook_notify
        url: "http://example.com/webhook-handler"
```

Send a message if the build is successful:

```yml
success:
    webhook_notify_step:
        plugin: webhook_notify
        url: "http://example.com/webhook-handler"
```

Send a message every time the build runs:

```yml
complete:
    webhook_notify_step:
        plugin: webhook_notify
        url: "http://example.com/webhook-handler"
```
