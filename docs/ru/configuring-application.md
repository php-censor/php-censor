Настройка PHP Censor
====================

Формат конфигурационного файла
------------------------------

Ниже приведен пример конфигурационного файла приложения:

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
    from_address:    'PHP Censor <no-reply@php-censor.local>'
    smtp_address:    null
    smtp_port:       null
    smtp_username:   null
    smtp_password:   null
    smtp_encryption: false
  queue:
    use_queue: true
    host:      localhost
    name:      php-censor-queue
    lifetime:  600
  log:
    rotate:    true
    max_files: 10
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
    remove_builds:      true # This option allow/deny build cleaning
    writer_buffer_size: 500  # BuildErrorWriter buffer size (count of inserts in one SQL query)
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

### Настройки базы данных

### Общие настройки

### Почтовые настройки

### Настройки очереди сборок

### Настройки логирования

### Настройки интеграции с BitBucket

### Настройки интеграции с GitHub

### Настройки сборки

### Настройки безопасности

### Настройки панели управления

* `all_projects` - all projects build status
* `last_builds` - last builds
* `build_errors` - not successful builds

Each widget can be located in the left or right column, use the `side` option for this:

```yml
    all_projects:
      side: left
```
