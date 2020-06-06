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
    webook_notify:
        url: "http://example.com/webhook-handler"
```

Send a message if the build is successful:

```yaml
success:
    webook_notify:
        url: "http://example.com/webhook-handler"
```

Send a message every time the build runs:

```yaml
complete:
    webook_notify:
        url: "http://example.com/webhook-handler"
```
