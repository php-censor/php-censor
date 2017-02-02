Configuring PHP Censor
======================

The PHP Censor configuration on the server is automatically generated into the `config.yml` file during installation.
One might need to also edit the file manually.

There is `config.yml` example:

```yml
b8:
  database:
    servers:
      read:
        - host: localhost
          port: 3306
      write:
        - host: localhost
          port: 3306
    type:     mysql # Database type: "mysql" or "pgsql"
    name:     php-censor-db
    username: php-censor-user
    password: php-censor-password
php-censor:
  language: en
  per_page: 10
  url:      'http://php-censor.local'
  email_settings:
    from_address: 'no-reply@php-censor.local'
    smtp_address:
  queue:
    use_queue: true
    host:      localhost
    name:      php-censor-queue
    lifetime:  600
  github:
    token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
    comments:
      commit:       false # This option allow/deny to post comments to Github commit
      pull_request: false # This option allow/deny to post comments to Github Pull Request
  build:
    remove_builds: true # This option allow/deny build cleaning
  security:
    disable_auth:    false # This option allows/deny you to disable authentication for PHP Censor
    default_user_id: 1     # Default user when authentication disabled
    auth_providers:        # Authentication providers
      internal:
        type: internal # Default provider (PHP Censor internal authentication)
      ldap:
        type: ldap # Your LDAP provider
        data:
          host:           'ldap.php-censor.local'
          port:           389
          base_dn:        'dc=php-censor,dc=local'
          mail_attribute: mail
```
