Configuring PHP Censor
======================

The PHP Censor configuration on the server is automatically generated into the `config.yml` file during installation.
One might need to also edit the file manually.

There is `config.yml` example:

```yml
php-censor:
  database:
    servers:
      read:
        - host:          localhost
          port:          3306
          pgsql-sslmode: prefer # disable, allow, prefer, require, verify-ca, or verify-full. See https://www.postgresql.org/docs/8.4/libpq-connect.html#LIBPQ-CONNECT-SSLMODE for details
      write:
        - host:          localhost
          port:          3306
          pgsql-sslmode: prefer
    type:     mysql # Database type: "mysql" or "pgsql"
    name:     php-censor-db
    username: php-censor-user
    password: php-censor-password
  language: en
  per_page: 10
  email_settings:
    from_address:    'PHP Censor <no-reply@php-censor.local>'
    smtp_address:    null
    smtp_port:       null
    smtp_username:   null
    smtp_password:   null
    smtp_encryption: false
  queue:
    use_queue: true
    host:      localhost
    port:      11300
    name:      php-censor-queue
    lifetime:  600
  log:
    rotate:    true
    max_files: 10
  ssh:
    strength: 4096                  # SSH keys strength (default: 2048)
    comment: admin@php-censor.info  # SSH keys comment (default: admin@php-censor)
  bitbucket:
    username: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    app_password: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
    comments:
      commit:       false # This option allow/deny to post comments to Bitbucket commit
      pull_request: false # This option allow/deny to post comments to Bitbucket Pull Request
    status:
      commit: false # This option allow/deny to post status to Bitbucket commit
  github:
    token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
    comments:
      commit:       false # This option allow/deny to post comments to Github commit
      pull_request: false # This option allow/deny to post comments to Github Pull Request
    status:
      commit: false # This option allow/deny to post status to Github commit
  build:
    remove_builds:          true  # This option allow/deny build cleaning
    writer_buffer_size:     500   # BuildErrorWriter buffer size (count of inserts in one SQL query)
    allow_public_artifacts: false # This option allow/deny to generate public artifacts (PHPUnit code coverage html report, Pdepend html reports)
    keep_builds:            100   # How much builds is keeping when cleaning old builds.
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
  dashboard_widgets:
    all_projects:
      side: left
    last_builds:
      side: right
```

Dashboard widgets
-----------------

* `all_projects` - all projects build status
* `last_builds` - last builds
* `build_errors` - not successful builds

Each widget can be located in the left or right column, use the `side` option for this:

```yml
    all_projects:
      side: left
```
