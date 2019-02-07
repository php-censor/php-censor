Plugin IRC
==========

Connects to an IRC server and sends a defined message.

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **message** - Required - The message to send.

#### Build Settings

* **irc** - All child properties are required
    * **server** - IRC server to connect to.
    * **port** - IRC server port, defaults to 6667.
    * **room** - The room you wish to send your message to (must start with a #)
    * **nick** - The nickname you want the bot to use.
