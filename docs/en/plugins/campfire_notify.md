Plugin Campfire Notify
======================

This plugin joins a [Campfire](https://campfirenow.com/) room and sends a user-defined message, for example a 
"Build Succeeded" message.

Configuration
-------------

### Build Settings options

* **campfire_notify** - All child properties are required
    * **auth_token** [string, required] - Your Campfire user authentication token.
    * **room** [string, required] - Your Campfire room ID number.
    * **url** [string, required] - Your Campfire chat room URL.

### Plugin options

* **message** [string, required] - The message to send to the room.
* **verbose** [boolean, optional] - Whether to run in verbose mode (default: false).

### Examples

```yml
build_settings:
    campfire_notify:
        auth_token: "campfire auth token"
        room:       "campfire room ID"
        url:        "campfire URL"

success:
    campfire_notify:
        verbose: true
        message: "Build succeeded!"
```
