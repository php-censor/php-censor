# Change Log

## [PHP Censor v0.18.0](https://github.com/corpsee/php-censor/tree/0.18.0) (2017-10-22)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.17.0...0.18.0)

* Added Mage v3 plugin for deployment. See 
[documentation](https://github.com/php-censor/php-censor/blob/master/docs/en/plugins/mage3.md). Thanks to 
[@ss-gxp](https://github.com/ss-gxp). PullRequest [#118](https://github.com/corpsee/php-censor/pull/118).
* Added the option to pass the short tags (-s) argument to PHP Parallel Lint so that files using PHP Short Tags can be 
linted. Used [@Dave13h](https://github.com/Dave13h) [code](https://github.com/Block8/PHPCI/pull/1338/files).
* Added a checkbox to build only the default branch specified in the project. Used 
[@suwalski](https://github.com/suwalski) [code](https://github.com/Block8/PHPCI/pull/1055/files).
* Added command to schedule tasks if not ran for a specified X days. Thanks to 
[@Vincentv92](https://github.com/Vincentv92). PullRequest [#126](https://github.com/corpsee/php-censor/pull/126).
* Added column for Build `source` instead of 'Manual' word in `commit_id` and `commit_message`.
* Added `user_id` column to `build` table (created by) + Renamed columns `created` -> `create_date`, 
`started` -> `start_date` and `finished` -> `finish_date`.
* Added `user_id` (created by) and `create_date` columns to `project_group` table.
* Added `user_id` (created by) and `create_date` columns to `project` table.
* Improved documentation for SystemD worker, Nginx virtual host.
* Improved GUI for Codeception plugin, PHPSpec plugin and charts.
* Fixed env build - omit checkout exact commit. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#119](https://github.com/corpsee/php-censor/pull/119).
* Fixed non-unicode binary log output. Issue [#116](https://github.com/corpsee/php-censor/issues/116).
* Fixed `lifetime` parameter for queue on installation.
* Fixed installation. Thanks to [@lscortesc](https://github.com/lscortesc). PullRequest 
[#128](https://github.com/corpsee/php-censor/pull/128).
* Removed `console.bat` file.
* Removed useless '/' from build status cache path.
* Removed useless `project_id` column from `build_meta` table, removed useless code from models.
* Updated dependencies.


## [PHP Censor v0.17.0](https://github.com/corpsee/php-censor/tree/0.17.0) (2017-09-03)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.16.0...0.17.0)

* Fixed build stages workflow. If `setup`, `test` or `deploy` stage failed then next stages (`setup`, `test` or 
`deploy`) skip.
* Fixed failures for notification plugins (Now notification failures doesn't fail all build). Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#113](https://github.com/corpsee/php-censor/pull/113).
* Fixed error with `allowed_errors` / `allowed_warnings` in PhpCodeSniffer plugin. Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#101](https://github.com/corpsee/php-censor/pull/101).
* Added ability to create comments on Bitbucket for commits and pull requests (Like on Github). Thanks to 
[@StudioMaX](https://github.com/StudioMaX). PullRequest [#112](https://github.com/corpsee/php-censor/pull/112).
* Added "Remember me functionality on login page. Issue [#81](https://github.com/corpsee/php-censor/issues/81).
* Added daily rotate logger for console commands. Issue [#108](https://github.com/corpsee/php-censor/issues/108).
* Added param `priority_path` (For all plugins) for control paths priority when we search plugin binary. 
Issue [#104](https://github.com/corpsee/php-censor/issues/104).
* Added regex pattern for branch specific config. Issue [#97](https://github.com/corpsee/php-censor/issues/97).
* Added JUnit result parser for PHPUnit plugin (for PHPUnit >= 6.0). Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#102](https://github.com/corpsee/php-censor/pull/102),
[#105](https://github.com/corpsee/php-censor/pull/105).
* Improved public status page UI (Added environment and duration, fixed table cell height).
* Improved Shell plugin documentation. Thanks to [@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest 
[#103](https://github.com/corpsee/php-censor/pull/103).
* Improved documentation. Thanks to [@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest 
[#110](https://github.com/corpsee/php-censor/pull/110), [#111](https://github.com/corpsee/php-censor/pull/111).
* Improved Worker (Daemon) documentation about `nohug` and `systemd`. Thanks to 
[@ketchoop](https://github.com/ketchoop). PullRequest [#98](https://github.com/corpsee/php-censor/pull/98), 
[#100](https://github.com/corpsee/php-censor/pull/100).
* Improved documentation about PHP Censor update.
* Added new PHP Censor logo.
* Updated dependencies.


## [PHP Censor v0.16.0](https://github.com/corpsee/php-censor/tree/0.16.0) (2017-07-16)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.15.0...0.16.0)

* **Removed HttpClient class and changed it to Guzzle library.**
* **Added config option `php-censor.build.writer_buffer_size` for configuring `BuildErrorWriter->buffer_size` 
property (Count of inserts in the one SQL query).** Default value: 500. Thanks to 
[@LEXASOFT](https://github.com/LEXASOFT) for the idea.
* Added params 'email' and 'message' for `php-censor:create-build` console command. Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). 
PullRequest [#92](https://github.com/corpsee/php-censor/pull/92).
* Fixed project create/edit form fields order.
* Fixed debug mode for 'Build now' button.
* Fixed `FileLink` for builds (Link to branch -> link to commit). Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#90](https://github.com/corpsee/php-censor/pull/90).
* Fixed error in `sendStatusPostback` in the build.
* Fixed build_meta.meta_value column type (`TEXT` -> `LONGTEXT`) for MySQL. Issue 
[#94](https://github.com/corpsee/php-censor/issues/94).
* Improved build log build directory appearence ('/' -> './'). Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#93](https://github.com/corpsee/php-censor/pull/93).
* Improved documentation. Thanks to [@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest 
[#83](https://github.com/corpsee/php-censor/pull/83), [#84](https://github.com/corpsee/php-censor/pull/84), 
[#96](https://github.com/corpsee/php-censor/pull/96). Issue [#2](https://github.com/corpsee/php-censor/issues/2).
* Improved email address format for notifications (Field 'from').
* Updated dependencies. Issue [#79](https://github.com/corpsee/php-censor/issues/79).


## [PHP Censor v0.15.0](https://github.com/corpsee/php-censor/tree/0.15.0) (2017-06-10)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.14.0...0.15.0)

* **Removed PollCommand**.
* **Fixed worker fail with eternal log writing**. Issue [#68](https://github.com/corpsee/php-censor/issues/68).
* **Fixed bulk error writing error** (`SQLSTATE[HY000]: General error: 7 number of parameters must be between 0 and 
65535`). Issue [#66](https://github.com/corpsee/php-censor/issues/66).
* **Fixed PDO PostgreSQL connection without installed `pdo_mysql` extension**. Issue 
[#73](https://github.com/corpsee/php-censor/issues/73).
* Fixed `/app` directory in Git repository. Issue [#73](https://github.com/corpsee/php-censor/issues/73).
* Fixed branches for SVN build. Issue [#65](https://github.com/corpsee/php-censor/issues/65).
* Fixed PhpCsFixer directory option. Issue [#75](https://github.com/corpsee/php-censor/issues/75).
* Fixed webhook for GitHub pull requests from private repositories. Thanks to 
[@StudioMaX](https://github.com/StudioMaX). PullRequest [#76](https://github.com/corpsee/php-censor/pull/76), 
[#78](https://github.com/corpsee/php-censor/pull/78).
* Improved logging configuration (Now logging autostart without special config `loggerconfig.php`). Issue 
[#59](https://github.com/corpsee/php-censor/issues/59).
* Removed `using_custom_file` application config (`app/config.yml`) option.
* Improved build-status/view page (Added build links, icons, date etc.). Issue 
[#23](https://github.com/corpsee/php-censor/issues/23).
* Improved default branch for SVN (Added ability to set branch full name like `branches/branch-1` or 
`/branch/branch-2`). Issue [#67](https://github.com/corpsee/php-censor/issues/67).


## [PHP Censor v0.14.0](https://github.com/corpsee/php-censor/tree/0.14.0) (2017-05-15)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.13.0...0.14.0)

* Added text wrap for build log.
* Fixed build branch in dashboard timeline. Thanks to [@JoolsMcFly](https://github.com/JoolsMcFly). PullRequest 
[#62](https://github.com/corpsee/php-censor/pull/62).
* Fixed project clone to working directory in Alpine Linux 3.5. Issue 
[#61](https://github.com/corpsee/php-censor/issues/61).
* Fixed environment field in build table.
* Fixed Database::lastInsertId call for PostgreSQL.
* Fixed SensioLabs Security Checker warning: squizlabs/php_codesniffer (2.7.1) - Arbitrary shell execution (Updated 
squizlabs/php_codesniffer).
* Fixed pagination for environments in project/view page and ajax builds update.
* Fixed builds for branches with special chars (like '#, /' etc.).
* **Fixed and refactored plugin PhpCsFixer. Issue [#63](https://github.com/corpsee/php-censor/issues/63).**
* **Improved webhook for GitHub: builds only one head commit per push**.
* **Improved webhook for GitHub: added tag build and UI information about tag**.
* Improved error page.
* Improved UI and code style.


## [PHP Censor v0.13.0](https://github.com/corpsee/php-censor/tree/0.13.0) (2017-04-10)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.12.0...0.13.0)

* **Added Environments**. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#41](https://github.com/corpsee/php-censor/pull/41), [#47](https://github.com/corpsee/php-censor/pull/47), 
[#52](https://github.com/corpsee/php-censor/pull/52). For details see 
[documentation](/docs/en/environments.md).
* **Added write cache for build log (It's increase build speed)**. Thanks to [@ss-gxp](https://github.com/ss-gxp). 
PullRequest [#45](https://github.com/corpsee/php-censor/pull/45), [#48](https://github.com/corpsee/php-censor/pull/48).
* **Added write cache for build errors (It's increase build speed)**. Thanks to [@ss-gxp](https://github.com/ss-gxp). 
Issue [#49](https://github.com/corpsee/php-censor/issues/49). PullRequest 
[#50](https://github.com/corpsee/php-censor/pull/50).
* **Added SensioLabs Security Checker Plugin** (This plugin is "zero-config" and used in builds without config). 
Issue [#27](https://github.com/corpsee/php-censor/issues/27). Config example:

```yml
test:
  security_checker:
    allowed_warnings: -1
```

* Added allowed fail status for plugins (See build summary in the build page).
* Added `suggest` section to `composer.json`. Issue [#53](https://github.com/corpsee/php-censor/issues/53).
* Fixed build execution with many workers. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#51](https://github.com/corpsee/php-censor/pull/51).
* Fixed build view (Added html encoding for build errors output). Thanks to [@ss-gxp](https://github.com/ss-gxp). 
PullRequest [#54](https://github.com/corpsee/php-censor/pull/54).
* Fixed exception when plugin runs without options (Like "php_parallel_lint: "). Issue 
[#44](https://github.com/corpsee/php-censor/issues/44).
* Fixed TechnicalDebt Plugin configuration parameters. Thanks to [@bochkovprivate](https://github.com/bochkovprivate). 
PullRequest [#55](https://github.com/corpsee/php-censor/pull/55).
* Fixed PHPCpd plugin documentation. Thanks to [@bochkovprivate](https://github.com/bochkovprivate). PullRequest 
[#56](https://github.com/corpsee/php-censor/pull/56).
* Improved plugins code.
* Improved UI.


## [PHP Censor v0.12.0](https://github.com/corpsee/php-censor/tree/0.12.0) (2017-03-25)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.11.0...0.12.0)

* Added 'deploy' stage for build. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#34](https://github.com/corpsee/php-censor/pull/34). Build config example:

```yml
test:
  ...
deploy:
  deployer:
    webhook_url: "http://deployer.local/deploy/xxxxx"
    reason:      "PHP Censor Build #%BUILD% - %COMMIT_MESSAGE%"
    update_only: true
```
* Added Magallanes (Mage) deployment plugin. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#36](https://github.com/corpsee/php-censor/pull/36), [#40](https://github.com/corpsee/php-censor/pull/40). 
Build config example:

```yml
deploy:
    mage:
        env: production
        bin: /usr/local/bin/mage
```
* Added build duration on Dashboard Timeline. Thanks to [@JoolsMcFly](https://github.com/JoolsMcFly). PullRequest 
[#33](https://github.com/corpsee/php-censor/pull/33)
* Added support for Mercurial (Hg) based repos in Bitbucket (BitbucketHgBuild). Used 
[@bochkovprivate](https://github.com/bochkovprivate) code.
* Fixed 'CommitterEmail' parameter in bitbucket webhook. Used [@bochkovprivate](https://github.com/bochkovprivate) code.
* Fixed 'branch' parameter in Mercurial (Hg) build. Used [@bochkovprivate](https://github.com/bochkovprivate) code.
* Fixed language select on user/edit page
* Fixed localization for 'project_group' string. Thanks to [@JoolsMcFly](https://github.com/JoolsMcFly). PullRequest 
[#39](https://github.com/corpsee/php-censor/pull/39)
* Fixed PHPUnit plugin behavior for case without tests
* Code style fixes, fixes for tests, improvements for documentation
* Removed useless daterangepicker and datepicker. Issue [#37](https://github.com/corpsee/php-censor/issues/37)
* Improved PhpCodeSniffer plugin. Thanks to [@ValerioOnGithub](https://github.com/ValerioOnGithub). PullRequest 
[#31](https://github.com/corpsee/php-censor/pull/31), [#35](https://github.com/corpsee/php-censor/pull/35), 
[#42](https://github.com/corpsee/php-censor/pull/42)
* Improved French localization. Thanks to [@JoolsMcFly](https://github.com/JoolsMcFly). PullRequest 
[#39](https://github.com/corpsee/php-censor/pull/39)


## [PHP Censor v0.11.0](https://github.com/corpsee/php-censor/tree/0.11.0) (2017-03-12)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.10.0...0.11.0)

* Added duration column to the project page
* Fixed Build.log column size for MySQL (removed "NOT NULL")
* Fixed PhpCpd ignore option. Used [@ZinitSolutionsGmbH](https://github.com/ZinitSolutionsGmbH) code.
* Fixed shell plugin execution. Issue [#30](https://github.com/corpsee/php-censor/issues/30).
* Fixed pagination position in the project view (UI)
* Fixed branch link in the timeline (UI)
* Code style fixes
* Improved README, Docs and CHANGELOG


## [PHP Censor v0.10.0](https://github.com/corpsee/php-censor/tree/0.10.0) (2017-02-24)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.9.0...0.10.0)

* Added 'Build with debug' button to the project page (For admin user). Issue 
[#22](https://github.com/corpsee/php-censor/issues/22).
* Fixed quoting for database entities.
* Fixed project config reload for worker between builds. Issue [#17](https://github.com/corpsee/php-censor/issues/17).
* Fixed problem with runtime/status_cache directory. Issue [#19](https://github.com/corpsee/php-censor/issues/19).
* Fixed add/edit project page. Issue [#21](https://github.com/corpsee/php-censor/issues/21).
* Fixed form name pattern. Thanks to [@ket4yii](https://github.com/ket4yii). PullRequest 
[#24](https://github.com/corpsee/php-censor/pull/24).
* Fixed build.log column size for MySQL (text -> longtext). Issue [#26](https://github.com/corpsee/php-censor/issues/26).
* Fixed build_error.message column size (varchar(255) -> text).
* Fixed profile language saving. Issue [#11](https://github.com/corpsee/php-censor/issues/11).
* Fixed builds for branches which start with a hash character. Used [@soulflyman](https://github.com/soulflyman) code.
* Improved Gogs support. Thanks to [@vinpel](https://github.com/vinpel). PullRequest 
[#25](https://github.com/corpsee/php-censor/pull/25).
* Improved TravisCI build settings (Added DB tests for PostgreSQL and MySQL).
* Improved README.


## [PHP Censor v0.9.0](https://github.com/corpsee/php-censor/tree/0.9.0) (2017-02-11)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.8.0...0.9.0)

* **Fixed multiple install command execution (Now admin and project group don't duplicate).**
* Added yaml highlight for build config in project page.
* Improved Gogs support. Thanks to [@vinpel](https://github.com/vinpel). PullRequest 
[#18](https://github.com/corpsee/php-censor/pull/18).
* Improved dashboard UI.


## [PHP Censor v0.8.0](https://github.com/corpsee/php-censor/tree/0.8.0) (2017-02-09)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.7.0...0.8.0)

* **Refactored console/commands. Removed localization from logs.**
* **Removed hacks for Windows (IS_WIN constant). Because it doesn't work on Windows normally anyway.**
* Improved README and Documentation.
* Added param `config-from-file` for installing application with prepared config:

```bash
cd ./php-censor.local

# Non-interactive installation with prepared config.yml file
./bin/console php-censor:install --config-from-file=yes --admin-name=admin --admin-password=admin --admin-email='admin@php-censor.local'
```

* Added params for non-interactive admin creating:

```bash
cd ./php-censor.local

# Non-interactive admin creating
./bin/console php-censor:create-admin --admin-name=admin --admin-password=admin --admin-email='admin@php-censor.local'
```

* Added caching for public build status badge. Issue [#15](https://github.com/corpsee/php-censor/issues/15).
* Added build from Gogs (build type and webhook). The feature is based on [@denji](https://github.com/denji)'s code. 
Issue [#13](https://github.com/corpsee/php-censor/issues/13).
* Improved Codeception plugin. Thanks to [@vinpel](https://github.com/vinpel). PullRequest 
[#16](https://github.com/corpsee/php-censor/pull/16).
* Updated french translation. Thanks to [@vinpel](https://github.com/vinpel). PullRequest 
[#16](https://github.com/corpsee/php-censor/pull/16).
* Fixed init language. Issue [#9](https://github.com/corpsee/php-censor/issues/9).


## [PHP Censor v0.7.0](https://github.com/corpsee/php-censor/tree/0.7.0) (2017-01-29)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.6.0...0.7.0)

* Application closed for search robots
* Improved README.md and added CHANGELOG.md file
* **Renamed application configuration (`app/config.yml`) section for work with queue**

The old way to configure queue:

```yml
php-censor:
  worker:
    host:        localhost
    queue:       php-censor-queue
    job_timeout: 600
```

And a new way:

```yml
php-censor:
  queue:
    host:     localhost
    name:     php-censor-queue
    lifetime: 600
```

* **Added PostgreSQL support as application DB. Changed DB configuration**

The old way to configure DB:

```yml
b8:
  database:
    servers:
      read: 'localhost:3306'
      write: 'localhost:3306'
    name:     php-censor-db
    username: php-censor-user
    password: php-censor-password
```

And a new way:

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
    type:     mysql
    name:     php-censor-db
    username: php-censor-user
    password: php-censor-password
```

Type of DB (`type`) should be `mysql` or `pgsql`


## [PHP Censor v0.6.0](https://github.com/corpsee/php-censor/tree/0.6.0) (2017-01-22)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.5.0...0.6.0)

* Added pluggable authentication and LDAP authentication provider

```yml
php-censor:
  security:
    auth_providers:
      internal:
        type: internal
      ldap-php-censor:
        type: ldap
        data:
          host:           'ldap.php-censor.local'
          port:           389
          base_dn:        'dc=php-censor,dc=local'
          mail_attribute: mail
```

If you enter by new LDAP-user, the record in the DB will be created automatically. The basement of the feature is 
[@Adirelle](https://github.com/Adirelle) and [@dzolotov](https://github.com/dzolotov) code.

* **Unified application configuration (app/config.yml) authentication options**

The old way to disable authentication:

```yml
php-censor:
  autentication_settings:
    state:   true
    user_id: 1
```

And a new way:

```yml
php-censor:
  security:
    disable_auth:    true
    default_user_id: 1
```


## [PHP Censor v0.5.0](https://github.com/corpsee/php-censor/tree/0.5.0) (2017-01-21)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.4.0...0.5.0)

* Fixed projects archive (Archived projects can not be built and projects moved to the archive section)
* Added option to the application configuration (`app/config.yml`) to allow/deny removing the build directory after 
build (`php-censor.build.remove_builds`)

```yml
php-censor:
  build:
    remove_builds: true
```

* Added options to the application configuration (`app/config.yml`) to allow/deny sending errors in the commits/pull 
requests as comments on Github (`php-censor.github.comments.commit` and `php-censor.github.comments.pull_request`)

```yml
php-censor:
  github:
    token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
    comments:
      commit:       false
      pull_request: false
```

* Improved plugin Codeception
* **Removed agent/worker Daemon mode (You should use Worker mode instead)**
* **Removed pluginconfig configuration file (You should use plugin full name including the namespace)**

```yml
test:
  \PluginNamespace\Plugin:
    allow_failures: true
```


## [PHP Censor v0.4.0](https://github.com/corpsee/php-censor/tree/0.4.0) (2017-01-15)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.3.0...0.4.0)

* Fixed delete confirmation for all items
* Added ajax update for the main page (dashboard)
* Added public status information to the project page
* UI and localization fixes


## [PHP Censor v0.3.0](https://github.com/corpsee/php-censor/tree/0.3.0) (2017-01-11)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.2.0...0.3.0)

* Improved UI
* Updated dependencies
* Updated PHPUnit from 4.8 to 5.7
* Improved build without config


## [PHP Censor v0.2.0](https://github.com/corpsee/php-censor/tree/0.2.0) (2017-01-07)

[Full Changelog](https://github.com/corpsee/php-censor/compare/0.1.0...0.2.0)

* Improved PHPUnit plugin
* Improved UI
* Added login by name (name or email)
* Fixed public build status page


## [PHP Censor v0.1.0](https://github.com/corpsee/php-censor/tree/0.1.0) (2017-01-04)

Initial release. Changes from PHPCI (1.7.1):

* Upped PHP minimal version from 5.3 to 5.6
* Fixed tests and other small fixes
* Redesigned project structure
* Added more debug info into the build log
* Moved CSS/JS dependencies from sources to Composer dependencies (asset-packagist.org)
* Added item per page parameter for build list

## PHP Censor v0 (2016-06-23)

Project started
