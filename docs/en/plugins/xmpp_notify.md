Plugin XMPP Notify
==================

Requirements
------------

- sendxmpp package 

Installation
------------

1. On Debian system (for example) use aptitude command to install `sendxmpp` 
2. Add XMPP plugin in "complete" section of your `php-censor.yml`

Configuration
-------------

### Options

* **username** : Username of your XMPP sender account. (example : "login@server.com")
* **password** : Password of your XMPP sender account.
* **recipients** : List of your XMPP recipients account.
* **server** : If your server is not the same that your login server (optional, example : gtalk.google.com)
* **tls** : Set 1 to enable TLS connection or 0 to disable it. (optional, default is 0)
* **alias** : Alias of your sender account. (optional)
* **date_format** : `strftime` mask date format display in notification message. (optional, default is %c of strftime) 

### Examples

```yaml
complete:
    xmpp_notify:
        username: "login@gmail.com"
        password: "AZERTY123"
        recipients:
            - "recipient1@jabber.org"
            - "recipient2@jabber.org"    
        server:      "gtalk.google.com"
        tls:         1
        alias:       "PHP Censor Notification"
        date_format: "%d/%m/%Y"
```

### Additional Options

The following general options can also be used: 

* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary vendor/bin, or a system-provided.
