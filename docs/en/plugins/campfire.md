Plugin Campfire
===============

This plugin joins a [Campfire](https://campfirenow.com/) room and sends a user-defined message, for example a 
"Build Succeeded" message.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **authToken** [string, required] - Your Campfire user authentication token.
* **message** [string, required] - The message to send to the room.
* **roomId** [string, required] - Your Campfire room ID number.
* **url** [string, required] - Your Campfire chat room URL.

### Examples
```yml
  build_settings:
    campfire:
      authToken: "campfire auth token"
      roomId: "campfire room ID"
      url: "campfire URL"
  success:
    campfire:
      message: "Build succeeded!"
```
