Plugin FlowdockNotify
=====================

This plugin joins a [Flowdock](https://www.flowdock.com/) room and sends a user-defined message, for example a 
"Build Succeeded" message.

### Options

* **api_key** [string, required] - **[DEPRECATED]** Option "api_key" is deprecated and will be deleted in version 2.0. 
Use the option "auth_token" instead.
* **auth_token** [string, required] - API token.
* **message** [string, optional] - Message.
* **email** [string, optional] - Email.
