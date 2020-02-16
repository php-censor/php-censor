Changelog 2.0
=============

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to 
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [2.0.0 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.0) (2019-xx-xx)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.2.0...2.0.0)

### Changed

- **Minimal PHP version increased to 7.1 (from 5.6)**.

### Removed

- **Deprecatins from versions 1.x**:
    - Cronjob worker: "php-censor:run-builds" (Use daemon worker instead: "php-censor:worker").
    - Project configs "phpci.yml" and ".phpci.yml" (use ".php-censor.yml" instead).
    - "PHPCI_*" interpolation and env variables (Use "PHP_CENSOR_*" instead).
    - Global application config section "b8.database" (Use "php-censor.database" instead).
    - [Codeception] Option "path" (Use option "output_path" instead).
    - [Grunt] Option "grunt" (Use options "binary_path" and "binary_name" instead).
    - [Gulp] Option "gulp" (Use options "binary_path" and "binary_name" instead).
    - [PHPCodeSniffer] Option "path" (Use option "directory" instead).
    - [PHPCpd] Option "path" (Use option "directory" instead).
    - [PHPDocblockChecker] Option "path" (Use option "directory" instead).
    - [PHPMessDetector] Option "path" (Use option "directory" instead).
    - [PHPUnit] Option "directory" (Use option "directories" instead).
    - [Shell] Option "command" and commands list without any named option. Use option "commands" instead.
    - [PackageBuild] Special variables for plugin ("%build.commit%", "%build.id%", "%build.branch%", "%project.title%", "%date%" and "%time%"). Use interpolated variables instead ("%COMMIT_ID%", "%BUILD_ID%", "%BRANCH%", "%PROJECT_TITLE%", "%CURRENT_DATE%", "CURRENT_TIME").
    - [MySQL and PostgreSQL] Options "pass" for plugins MySQL and PostgreSQL. Use option "password" instead.
    - [MySQL, PostgreSQL, SQLite] Queries list without option for plugins MySQL, PostgreSQL and SQLite. Use the options "queries" instead.
    - [MySQL] Imports list without option for plugin MySQL. Use the options "imports" instead.
    - [Mage, Mage3] Section "mage" and "mage3" in the global application config and option "bin". Use the plugin options "binary_path" and "binary_name" instead.


## Other versions

- [0.x Changelog](/docs/CHANGELOG_0.x.md)
- [1.0 Changelog](/docs/CHANGELOG_1.0.md)
- [1.1 Changelog](/docs/CHANGELOG_1.1.md)
- [1.2 Changelog](/docs/CHANGELOG_1.2.md)
