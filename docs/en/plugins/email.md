Plugin Email
============

Sends a build status email.

Configuration
-------------

### Options

* **committer** - Whether or not to send the email to the committer that prompted this build.
* **addresses** - A list of addresses to send to.
* **default_mailto_address** - A default address to send to.
* **cc** - A list of addresses that will receive a copy of every emails sent.
* **template** - The template to use, options are short and long. Default is short on success and long otherwise.

**Note:** _This plugin will only work if you configured email settings during installation or configured them later in 
[`config.yml`](../configuring-application.md)._
### Examples

See [Adding PHP Censor Support to Your Projects](../configuring_project.md) for more information about how to configure plugins.

Send an email to the committer as well as one@exameple.com if a build fails:
```yml
failure:
    email:
        committer: true
        default_mailto_address: one@example.com
```

Send an email to one@example.com every time a build is run:
```yml
complete:
    email:
        default_mailto_address: one@example.com
```
