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

```yaml
failure:
    webhook_notify:
        url: "http://example.com/webhook-handler"
```

Send a message if the build is successful:

```yaml
success:
    webhook_notify:
        url: "http://example.com/webhook-handler"
```

Send a message every time the build runs:

```yaml
complete:
    webhook_notify:
        url: "http://example.com/webhook-handler"
```
