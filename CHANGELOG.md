# Change Log


The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to 
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [0.22.0](https://github.com/php-censor/php-censor/tree/0.22.0) (2018-05-02)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.21.0...0.22.0)

### Added

- Global application options `php-censor.ssh.strength` and `php-censor.ssh.comment` for SSH key generation. Issue 
[#154](https://github.com/php-censor/php-censor/issues/154).
- Pull request remote branch to GUI.
- PHPDocBlockChecker plugin detailed error log. Thanks to [@Dave13h](https://github.com/Dave13h). Pull request 
[#159](https://github.com/php-censor/php-censor/pull/159).
- More unit/integration tests for DB logic (Connections, Stores, Models).
- PHPUnit code coverage log output and chart in information tab for PHPUnit coverage. Issue 
[#148](https://github.com/php-censor/php-censor/issues/148).
- Global application option `php-censor.build.allow_public_artifacts` for allow/deny to generate public artifacts 
(PHPUnit code coverage html report, Pdepend html reports). Issue 
[#107](https://github.com/php-censor/php-censor/issues/107).
- Web notifications (Initial frontend part). Web notifications are disabled by default (Global application option 
`php-censor.notifications.enabled`). Issue [#156](https://github.com/php-censor/php-censor/issues/156). Thanks to 
[@prezire](https://github.com/prezire). Pull request [#167](https://github.com/php-censor/php-censor/pull/167).
- Github Enterprise support (Improved Github build type). Issue 
[#163](https://github.com/php-censor/php-censor/issues/163).
- Ability to merge in-database project config over in-repository config (`.php-censor.yml`) instead of overwrite only 
(Checkbox in the project add/edit page). Issues: [#14](https://github.com/php-censor/php-censor/issues/14), 
[#70](https://github.com/php-censor/php-censor/issues/70), [#106](https://github.com/php-censor/php-censor/issues/106), 
[#121](https://github.com/php-censor/php-censor/issues/121).
- Phan plugin. Thanks to [@caouecs](https://github.com/caouecs). Pull requests 
[#171](https://github.com/php-censor/php-censor/pull/171), [#175](https://github.com/php-censor/php-censor/pull/175).
- New command `php-censor:check-localizations` for search missing translated strings for non-english languages. Thanks 
to [@caouecs](https://github.com/caouecs). Pull requests [#173](https://github.com/php-censor/php-censor/pull/173), 
[#174](https://github.com/php-censor/php-censor/pull/174).

### Changed

- Improved coverage report by Codecov (edited `.codecov.yml` config file in application).
- Massive refactored: pull requests, models, stores, database, config, controllers, application, forms, views etc. 
Changed project structure.
- Code style fixes: replaced `func_get_args()` by `...$params` (PHP 5.6+), `Lang::out` to `Lang::get` etc.
- RemoteGitBuild (`remote`) renamed to GitBuild (`git`), MercurialBuild (`hg`) renamed to HgBuild (`hg`), 
SubversionBuild (`svn`) renamed to SvnBuild (`svn`), BitbucketHgBuild (`bitbuckethg`) renamed to BitbucketHgBuild 
(`bitbucket-hg`). DB data will refresh by migration automatically.
- Merged PostgreSQL and MySQL tests inti one PHPUnit XML config.
- Documentation improvements.
- Improved translations for Russian and French. Thanks to [@caouecs](https://github.com/caouecs). Pull requests 
[#167](https://github.com/php-censor/php-censor/pull/167), [#169](https://github.com/php-censor/php-censor/pull/169), 
[#172](https://github.com/php-censor/php-censor/pull/172).
- Improvements for PHPUnit plugin. Thanks to [@SimonHeimberg](https://github.com/SimonHeimberg). Pull request 
[#160](https://github.com/php-censor/php-censor/pull/160).

### Removed

- Useless field `last_commit` from table `project`.

### Fixed

- PhpCodeSniffer plugin `path` option. Thanks to [@AlexisFinn](https://github.com/AlexisFinn). Issue 
[#153](https://github.com/php-censor/php-censor/issues/153). Pull requests 
[#155](https://github.com/php-censor/php-censor/pull/155).
- TechnicalDebt plugin `allowed_errors` option. Thanks to [@glennmcewan](https://github.com/glennmcewan). Pull 
requests [#158](https://github.com/php-censor/php-censor/pull/158).
- Build creation by webhook. Issue [#162](https://github.com/php-censor/php-censor/issues/162).
- Rebuild project. Thanks to [@Caffe1neAdd1ct](https://github.com/Caffe1neAdd1ct). Issue 
[#164](https://github.com/php-censor/php-censor/issues/164). Pull request 
[#166](https://github.com/php-censor/php-censor/pull/166).
- Project directory path for SSH key generation. Issue [#165](https://github.com/php-censor/php-censor/issues/165).
- SVN build type additional options (Like: `username`, `password` etc.). Issue 
[#70](https://github.com/php-censor/php-censor/issues/70).
- PHPDockblockChecker plugin error on `count` function for PHP 7.2. Issue 
[#170](https://github.com/php-censor/php-censor/issues/170).


## [0.21.0](https://github.com/php-censor/php-censor/tree/0.21.0) (2018-02-21)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.20.0...0.21.0)

### Added

- Gogs pull request webhook for manage environments branches. Thanks to [@ss-gxp](https://github.com/ss-gxp). 
Pull request [#142](https://github.com/php-censor/php-censor/pull/142).
- Access to Pdepend plugin artifacts. Issue [#107](https://github.com/php-censor/php-censor/issues/107).
- Code coverage report for PHPUnit plugin. Issue [#86](https://github.com/php-censor/php-censor/issues/86).
- PHP Censor logo and version to console and web interfaces.

### Changed

- Small improvements in README.md. Thanks to [@lex111](https://github.com/lex111). Pull request 
[#144](https://github.com/php-censor/php-censor/pull/144).
- Renamed 'Webhook' to 'Webhook (Push)' in according to 'Webhook (Pull request)'.
- Improved small-box block icon appearance.
- Improved Pdepend plugin documentation. Issue [#143](https://github.com/php-censor/php-censor/issues/143).
- Code style fixes.
- Updated dependencies.

### Removed

- Application Cache class and replaced by Symfony/Cache component.
- Useless Template class and refactored View.
- User model from `$_SESSION`.

### Fixed

- Versions of dependencies.
- PHPUnit assert calls from public to static.
- Error page (Only admin access to error page now).
- Build log for non-unicode characters. Issue [#145](https://github.com/php-censor/php-censor/issues/145). Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). Pull request 
[#146](https://github.com/php-censor/php-censor/pull/146), [#149](https://github.com/php-censor/php-censor/pull/149).
- PhpUnitJson fail on empty trace and unfinished tests. Thanks to [@SimonHeimberg](https://github.com/SimonHeimberg). 
Pull request [#147](https://github.com/php-censor/php-censor/pull/147).
- PhpParallelLint Short tags Option. Thanks to [@Dave13h](https://github.com/Dave13h). Pull request 
[#151](https://github.com/php-censor/php-censor/pull/151).
- Exception handler for PHP7+.


## [0.20.0](https://github.com/php-censor/php-censor/tree/0.20.0) (2018-01-10)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.19.0...0.20.0)

### Added

- "New" label for build errors in "Errors" tab, filtration by "New" label and new errors count indicators on dashboard 
and builds list. The feature partial based on [@dancryer](https://github.com/dancryer)'s 
[code in PHPCI](https://github.com/Block8/PHPCI/commit/2a99f10a24340109042eb1d603171cea5e42aee5).
- PHP 7.2 to TravisCI config.
- Committer email updating after cloning for Git builds (Like commit massage and commit hash before).
- New build source "Webhook (Pull request)" for builds.
- Application config options `github.status.commit` and `bitbucket.status.commit` for allow/deny to post build status 
to Github/Bitbucket.

### Changed

- Allowed public build status for archived projects.
- Refactored and improved TechnicalDebt plugin. Issue [#82](https://github.com/php-censor/php-censor/issues/82). Thanks 
to [@vinpel](https://github.com/vinpel). Pull request [#141](https://github.com/php-censor/php-censor/pull/141).
- Improved CHANGELOG.md file (See: [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)).
- Updated dependencies.

### Removed

- Useless Image class.

### Fixed

- PHP 7.2 unpack user from session. Issue [#136](https://github.com/php-censor/php-censor/issues/136). Thanks to 
[@oln0ry](https://github.com/oln0ry). Pull request [#137](https://github.com/php-censor/php-censor/pull/137).


## [0.19.0](https://github.com/php-censor/php-censor/tree/0.19.0) (2017-11-18)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.18.0...0.19.0)

### Added

- Paginator helper. Issue [#123](https://github.com/php-censor/php-censor/issues/123).
- Paginator for build errors. Issue [#85](https://github.com/php-censor/php-censor/issues/85).
- Filtration for errors by severity and plugin. Issue [#85](https://github.com/php-censor/php-censor/issues/85).
- Links to errors from summary block (Information tab). Issue [#85](https://github.com/php-censor/php-censor/issues/85).
- New dashboard widget with only failed projects (See 
[documentation](https://github.com/php-censor/php-censor/blob/master/docs/en/configuring-application.md#dashboard-widgets)). 
Thanks to [@ss-gxp](https://github.com/ss-gxp). Pull request [#131](https://github.com/php-censor/php-censor/pull/131).
- Ability to call Git webhook by project name instead id. Thanks to [@ss-gxp](https://github.com/ss-gxp). Pull request 
[#132](https://github.com/php-censor/php-censor/pull/132).

### Changed

- Dashboard on the index page, now dashboard more flexible and include separated widgets (See 
[documentation](https://github.com/php-censor/php-censor/blob/master/docs/en/configuring-application.md#dashboard-widgets)). 
Thanks to [@ss-gxp](https://github.com/ss-gxp). Pull request [#131](https://github.com/php-censor/php-censor/pull/131).

### Fixed

- Error with build log. Issue [#130](https://github.com/php-censor/php-censor/issues/130).
- Excessive build meta inserts in the DB.
- Bootstrap grid responsive classes for dashboard.


## [0.18.0](https://github.com/php-censor/php-censor/tree/0.18.0) (2017-10-22)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.17.0...0.18.0)

### Added

- Mage v3 plugin for deployment. See 
[documentation](https://github.com/php-censor/php-censor/blob/master/docs/en/plugins/mage3.md). Thanks to 
[@ss-gxp](https://github.com/ss-gxp). PullRequest [#118](https://github.com/php-censor/php-censor/pull/118).
- The option to pass the short tags (-s) argument to PHP Parallel Lint so that files using PHP Short Tags can be 
linted. Used [@Dave13h](https://github.com/Dave13h) [code](https://github.com/Block8/PHPCI/pull/1338/files).
- Checkbox to build only the default branch specified in the project. Used 
[@suwalski](https://github.com/suwalski) [code](https://github.com/Block8/PHPCI/pull/1055/files).
- Command to schedule tasks if not ran for a specified X days. Thanks to 
[@Vincentv92](https://github.com/Vincentv92). PullRequest [#126](https://github.com/php-censor/php-censor/pull/126).
- Column for Build `source` instead of 'Manual' word in `commit_id` and `commit_message`.
- Column `user_id` to `build` table (created by) + Renamed columns `created` -> `create_date`, 
`started` -> `start_date` and `finished` -> `finish_date`.
- Columns `user_id` (created by) and `create_date` to `project_group` table.
- Columns `user_id` (created by) and `create_date` to `project` table.

### Changed

- Improved documentation for SystemD worker, Nginx virtual host.
- Improved GUI for Codeception plugin, PHPSpec plugin and charts.
- Updated dependencies.

### Removed

- File `console.bat` for Windows installation.
- Useless '/' from build status cache path.
- Useless `project_id` column from `build_meta` table, removed useless code from models.

### Fixed

- Environments - omit checkout exact commit. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#119](https://github.com/php-censor/php-censor/pull/119).
- Non-unicode binary log output. Issue [#116](https://github.com/php-censor/php-censor/issues/116).
- Queue's parameter `lifetime` in installation.
- Installation command. Thanks to [@lscortesc](https://github.com/lscortesc). PullRequest 
[#128](https://github.com/php-censor/php-censor/pull/128).


## [0.17.0](https://github.com/php-censor/php-censor/tree/0.17.0) (2017-09-03)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.16.0...0.17.0)

### Added

- Ability to create comments on Bitbucket for commits and pull requests (Like on Github). Thanks to 
[@StudioMaX](https://github.com/StudioMaX). PullRequest [#112](https://github.com/php-censor/php-censor/pull/112).
- "Remember me" functionality on login page. Issue [#81](https://github.com/php-censor/php-censor/issues/81).
- Daily rotate logger for console commands. Issue [#108](https://github.com/php-censor/php-censor/issues/108).
- Param `priority_path` (For all plugins) for control paths priority when we search plugin binary. 
Issue [#104](https://github.com/php-censor/php-censor/issues/104).
- Regex pattern for branch specific config. Issue [#97](https://github.com/php-censor/php-censor/issues/97).
- JUnit result parser for PHPUnit plugin (for PHPUnit >= 6.0). Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#102](https://github.com/php-censor/php-censor/pull/102),
[#105](https://github.com/php-censor/php-censor/pull/105).
- New PHP Censor logo.

### Changed

- Improved public status page UI (Added environment and duration, fixed table cell height).
- Improved Shell plugin documentation. Thanks to [@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest 
[#103](https://github.com/php-censor/php-censor/pull/103).
- Improved documentation. Thanks to [@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest 
[#110](https://github.com/php-censor/php-censor/pull/110), [#111](https://github.com/php-censor/php-censor/pull/111).
- Improved Worker (Daemon) documentation about `nohug` and `systemd`. Thanks to 
[@ketchoop](https://github.com/ketchoop). PullRequest [#98](https://github.com/php-censor/php-censor/pull/98), 
[#100](https://github.com/php-censor/php-censor/pull/100).
- Improved documentation about PHP Censor update.
- Updated dependencies.

### Fixed

- Build stages workflow. If `setup`, `test` or `deploy` stage failed then next stages (`setup`, `test` or 
`deploy`) skip.
- Failures for notification plugins (Now notification failures doesn't fail all build). Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#113](https://github.com/php-censor/php-censor/pull/113).
- Error with `allowed_errors` / `allowed_warnings` in PhpCodeSniffer plugin. Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#101](https://github.com/php-censor/php-censor/pull/101).


## [0.16.0](https://github.com/php-censor/php-censor/tree/0.16.0) (2017-07-16)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.15.0...0.16.0)

### Added

- Config option `php-censor.build.writer_buffer_size` for configuring `BuildErrorWriter->buffer_size` 
property (Count of inserts in the one SQL query). Default value: 500. Thanks to 
[@LEXASOFT](https://github.com/LEXASOFT) for the idea.
- Params 'email' and 'message' for `php-censor:create-build` console command. Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). 
PullRequest [#92](https://github.com/php-censor/php-censor/pull/92).

### Changed

- Improved build log build directory appearence ('/' -> './'). Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#93](https://github.com/php-censor/php-censor/pull/93).
- Improved documentation. Thanks to [@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest 
[#83](https://github.com/php-censor/php-censor/pull/83), [#84](https://github.com/php-censor/php-censor/pull/84), 
[#96](https://github.com/php-censor/php-censor/pull/96). Issue [#2](https://github.com/php-censor/php-censor/issues/2).
- Improved email address format for notifications (Field 'from').
- Updated dependencies. Issue [#79](https://github.com/php-censor/php-censor/issues/79).

### Removed

- HttpClient class and changed it to Guzzle library.

### Fixed

- Project create/edit form fields order.
- Debug mode for 'Build now' button.
- `FileLink` for builds (Link to branch -> link to commit). Thanks to 
[@SimonHeimberg](https://github.com/SimonHeimberg). PullRequest [#90](https://github.com/php-censor/php-censor/pull/90).
- Error in `sendStatusPostback` in the build.
- Column `build_meta.meta_value` type (`TEXT` -> `LONGTEXT`) for MySQL. Issue 
[#94](https://github.com/php-censor/php-censor/issues/94).


## [0.15.0](https://github.com/php-censor/php-censor/tree/0.15.0) (2017-06-10)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.14.0...0.15.0)

### Changed

- Improved logging configuration (Now logging autostart without special config `loggerconfig.php`). Issue 
[#59](https://github.com/php-censor/php-censor/issues/59).
- Improved build-status/view page (Added build links, icons, date etc.). Issue 
[#23](https://github.com/php-censor/php-censor/issues/23).
- Improved default branch for SVN (Added ability to set branch full name like `branches/branch-1` or 
`/branch/branch-2`). Issue [#67](https://github.com/php-censor/php-censor/issues/67).

### Removed

- PollCommand console command.
- Application config option`using_custom_file` (`app/config.yml`).

### Fixed

- Worker fail with eternal log writing. Issue [#68](https://github.com/php-censor/php-censor/issues/68).
- Bulk error writing error (`SQLSTATE[HY000]: General error: 7 number of parameters must be between 0 and 
65535`). Issue [#66](https://github.com/php-censor/php-censor/issues/66).
- PDO PostgreSQL connection without installed `pdo_mysql` extension. Issue 
[#73](https://github.com/php-censor/php-censor/issues/73).
- Directory `/app` in Git repository. Issue [#73](https://github.com/php-censor/php-censor/issues/73).
- Branches for SVN build. Issue [#65](https://github.com/php-censor/php-censor/issues/65).
- PhpCsFixer plugin `directory` option. Issue [#75](https://github.com/php-censor/php-censor/issues/75).
- Webhook for GitHub pull requests from private repositories. Thanks to 
[@StudioMaX](https://github.com/StudioMaX). PullRequest [#76](https://github.com/php-censor/php-censor/pull/76), 
[#78](https://github.com/php-censor/php-censor/pull/78).


## [0.14.0](https://github.com/php-censor/php-censor/tree/0.14.0) (2017-05-15)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.13.0...0.14.0)

### Added

- Text wrap for build log.

### Changed

- Improved webhook for GitHub: builds only one head commit per push.
- Improved webhook for GitHub: added tag build and UI information about tag.
- Improved error page.
- Improved UI and code style.

### Fixed

- Build branch in dashboard timeline. Thanks to [@JoolsMcFly](https://github.com/JoolsMcFly). PullRequest 
[#62](https://github.com/php-censor/php-censor/pull/62).
- Project clone to working directory in Alpine Linux 3.5. Issue 
[#61](https://github.com/php-censor/php-censor/issues/61).
- Environment field in build table.
- `Database::lastInsertId` call for PostgreSQL.
- SensioLabs Security Checker warning: squizlabs/php_codesniffer (2.7.1) - Arbitrary shell execution (Updated 
squizlabs/php_codesniffer).
- Pagination for environments in project/view page and ajax builds update.
- Builds for branches with special chars (like '#, /' etc.).
- Plugin PhpCsFixer. Issue [#63](https://github.com/php-censor/php-censor/issues/63).


## [0.13.0](https://github.com/php-censor/php-censor/tree/0.13.0) (2017-04-10)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.12.0...0.13.0)

### Added

- Environments. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#41](https://github.com/php-censor/php-censor/pull/41), [#47](https://github.com/php-censor/php-censor/pull/47), 
[#52](https://github.com/php-censor/php-censor/pull/52). For details see 
[documentation](/docs/en/environments.md).
- Write cache for build log (It's increase build speed). Thanks to [@ss-gxp](https://github.com/ss-gxp). 
PullRequest [#45](https://github.com/php-censor/php-censor/pull/45), [#48](https://github.com/php-censor/php-censor/pull/48).
- Write cache for build errors (It's increase build speed). Thanks to [@ss-gxp](https://github.com/ss-gxp). 
Issue [#49](https://github.com/php-censor/php-censor/issues/49). PullRequest 
[#50](https://github.com/php-censor/php-censor/pull/50).
- SensioLabs Security Checker Plugin (This plugin is "zero-config" and used in builds without config). 
Issue [#27](https://github.com/php-censor/php-censor/issues/27). Config example:

    ```yml
    test:
      security_checker:
        allowed_warnings: -1
    ```

- Allowed fail status for plugins (See build summary in the build page).
- `suggest` section to `composer.json`. Issue [#53](https://github.com/php-censor/php-censor/issues/53).

### Changed

- Improved plugins code.
- Improved UI.

### Fixed

- Build execution with many workers. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#51](https://github.com/php-censor/php-censor/pull/51).
- Build view (Added html encoding for build errors output). Thanks to [@ss-gxp](https://github.com/ss-gxp). 
PullRequest [#54](https://github.com/php-censor/php-censor/pull/54).
- Exception when plugin runs without options (Like "php_parallel_lint: "). Issue 
[#44](https://github.com/php-censor/php-censor/issues/44).
- TechnicalDebt Plugin configuration parameters. Thanks to [@bochkovprivate](https://github.com/bochkovprivate). 
PullRequest [#55](https://github.com/php-censor/php-censor/pull/55).
- PHPCpd plugin documentation. Thanks to [@bochkovprivate](https://github.com/bochkovprivate). PullRequest 
[#56](https://github.com/php-censor/php-censor/pull/56).


## [0.12.0](https://github.com/php-censor/php-censor/tree/0.12.0) (2017-03-25)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.11.0...0.12.0)

### Added

- Stage 'deploy' for build. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#34](https://github.com/php-censor/php-censor/pull/34). Build config example:

    ```yml
    test:
      ...
    deploy:
      deployer:
        webhook_url: "http://deployer.local/deploy/xxxxx"
        reason:      "PHP Censor Build #%BUILD% - %COMMIT_MESSAGE%"
        update_only: true
    ```

- Magallanes (Mage) deployment plugin. Thanks to [@ss-gxp](https://github.com/ss-gxp). PullRequest 
[#36](https://github.com/php-censor/php-censor/pull/36), [#40](https://github.com/php-censor/php-censor/pull/40). 
Build config example:

    ```yml
    deploy:
        mage:
            env: production
            bin: /usr/local/bin/mage
    ```

- Build duration on Dashboard Timeline. Thanks to [@JoolsMcFly](https://github.com/JoolsMcFly). PullRequest 
[#33](https://github.com/php-censor/php-censor/pull/33)
- Support for Mercurial (Hg) based repos in Bitbucket (BitbucketHgBuild). Used 
[@bochkovprivate](https://github.com/bochkovprivate) code.

### Changed

- Code style fixes, fixes for tests, improvements for documentation
- Improved PhpCodeSniffer plugin. Thanks to [@ValerioOnGithub](https://github.com/ValerioOnGithub). PullRequest 
[#31](https://github.com/php-censor/php-censor/pull/31), [#35](https://github.com/php-censor/php-censor/pull/35), 
[#42](https://github.com/php-censor/php-censor/pull/42)
- Improved French localization. Thanks to [@JoolsMcFly](https://github.com/JoolsMcFly). PullRequest 
[#39](https://github.com/php-censor/php-censor/pull/39)

### Removed

- Useless daterangepicker and datepicker. Issue [#37](https://github.com/php-censor/php-censor/issues/37)

### Fixed

- Parameter 'CommitterEmail' in bitbucket webhook. Used [@bochkovprivate](https://github.com/bochkovprivate) code.
- Parameter 'branch' in Mercurial (Hg) build. Used [@bochkovprivate](https://github.com/bochkovprivate) code.
- Language select on user/edit page.
- Localization for 'project_group' string. Thanks to [@JoolsMcFly](https://github.com/JoolsMcFly). PullRequest 
[#39](https://github.com/php-censor/php-censor/pull/39).
- PHPUnit plugin behavior for case without tests.


## [0.11.0](https://github.com/php-censor/php-censor/tree/0.11.0) (2017-03-12)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.10.0...0.11.0)

### Added

- Duration column to the project page.

### Changed

- Code style fixes.
- Improved README, Docs and CHANGELOG.

### Fixed

- `build.log` column size for MySQL (removed "NOT NULL").
- PhpCpd ignore option. Used [@ZinitSolutionsGmbH](https://github.com/ZinitSolutionsGmbH) code.
- Shell plugin execution. Issue [#30](https://github.com/php-censor/php-censor/issues/30).
- Pagination position in the project view (UI).
- Branch link in the timeline (UI).


## [0.10.0](https://github.com/php-censor/php-censor/tree/0.10.0) (2017-02-24)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.9.0...0.10.0)

### Added

- 'Build with debug' button to the project page (For admin user). Issue 
[#22](https://github.com/php-censor/php-censor/issues/22).

### Changed

- Improved Gogs support. Thanks to [@vinpel](https://github.com/vinpel). PullRequest 
[#25](https://github.com/php-censor/php-censor/pull/25).
- Improved TravisCI build settings (Added DB tests for PostgreSQL and MySQL).
- Improved README.

### Fixed

- Quoting for database entities.
- Project config reload for worker between builds. Issue [#17](https://github.com/php-censor/php-censor/issues/17).
- Problem with runtime/status_cache directory. Issue [#19](https://github.com/php-censor/php-censor/issues/19).
- Add/edit project page. Issue [#21](https://github.com/php-censor/php-censor/issues/21).
- Form name pattern. Thanks to [@ket4yii](https://github.com/ket4yii). PullRequest 
[#24](https://github.com/php-censor/php-censor/pull/24).
- `build.log` column size for MySQL (text -> longtext). Issue [#26](https://github.com/php-censor/php-censor/issues/26).
- `build_error.message` column size (varchar(255) -> text).
- Profile language saving. Issue [#11](https://github.com/php-censor/php-censor/issues/11).
- Builds for branches which start with a hash character. Used [@soulflyman](https://github.com/soulflyman) code.


## [0.9.0](https://github.com/php-censor/php-censor/tree/0.9.0) (2017-02-11)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.8.0...0.9.0)

### Added

- Yaml highlight for build config in project page.

### Changed

- Improved Gogs support. Thanks to [@vinpel](https://github.com/vinpel). PullRequest 
[#18](https://github.com/php-censor/php-censor/pull/18).
- Improved dashboard UI.

### Fixed

- Multiple install command execution (Now admin and project group don't duplicate).


## [0.8.0](https://github.com/php-censor/php-censor/tree/0.8.0) (2017-02-09)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.7.0...0.8.0)

### Added

- Parameter `config-from-file` for installing application with prepared config:

    ```bash
    cd ./php-censor.local
    
    # Non-interactive installation with prepared config.yml file
    ./bin/console php-censor:install --config-from-file=yes --admin-name=admin --admin-password=admin --admin-email='admin@php-censor.local'
    ```

- Parameters for non-interactive admin creating:

    ```bash
    cd ./php-censor.local
    
    # Non-interactive admin creating
    ./bin/console php-censor:create-admin --admin-name=admin --admin-password=admin --admin-email='admin@php-censor.local'
    ```

- Caching for public build status badge. Issue [#15](https://github.com/php-censor/php-censor/issues/15).
- Build from Gogs (build type and webhook). The feature is based on [@denji](https://github.com/denji)'s code. 
Issue [#13](https://github.com/php-censor/php-censor/issues/13).

### Changed

- Refactored console/commands. Removed localization from logs.
- Improved README and Documentation.
- Improved Codeception plugin. Thanks to [@vinpel](https://github.com/vinpel). PullRequest 
[#16](https://github.com/php-censor/php-censor/pull/16).
- Updated French translation. Thanks to [@vinpel](https://github.com/vinpel). PullRequest 
[#16](https://github.com/php-censor/php-censor/pull/16).

### Removed

- Hacks for Windows (IS_WIN constant). Because it doesn't work on Windows normally anyway.

### Fixed

- Init language. Issue [#9](https://github.com/php-censor/php-censor/issues/9).


## [0.7.0](https://github.com/php-censor/php-censor/tree/0.7.0) (2017-01-29)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.6.0...0.7.0)

### Added

- PostgreSQL support as application DB. Changed DB configuration. The old way to configure DB:

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

    Type of DB (`type`) should be `mysql` or `pgsql`.

### Changed

- Application closed for search robots.
- Renamed application configuration (`app/config.yml`) section for work with queue. The old way to configure queue:

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

- Improved README.md and added CHANGELOG.md file.


## [0.6.0](https://github.com/php-censor/php-censor/tree/0.6.0) (2017-01-22)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.5.0...0.6.0)

### Added

- Added pluggable authentication and LDAP authentication provider:

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

### Changed

- Unified application configuration (`app/config.yml`) authentication options.

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


## [0.5.0](https://github.com/php-censor/php-censor/tree/0.5.0) (2017-01-21)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.4.0...0.5.0)

### Added

- Option to the application configuration (`app/config.yml`) to allow/deny removing the build directory after 
build (`php-censor.build.remove_builds`):

    ```yml
    php-censor:
      build:
        remove_builds: true
    ```

- Options to the application configuration (`app/config.yml`) to allow/deny sending errors in the commits/pull 
requests as comments on Github (`php-censor.github.comments.commit` and `php-censor.github.comments.pull_request`):

    ```yml
    php-censor:
      github:
        token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
        comments:
          commit:       false
          pull_request: false
    ```

### Changed

- Improved plugin Codeception

### Removed

- Agent/worker Daemon mode (You should use Worker mode instead).
- `pluginconfig` configuration file (You should use plugin full name including the namespace):

    ```yml
    test:
      \PluginNamespace\Plugin:
        allow_failures: true
    ```

### Fixed

- Fixed projects archive (Archived projects can not be built and projects moved to the archive section).


## [0.4.0](https://github.com/php-censor/php-censor/tree/0.4.0) (2017-01-15)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.3.0...0.4.0)

### Added

- Ajax update for the main page (Dashboard).
- Public status information to the project page.

### Fixed

- UI and localizations.
- Delete confirmation for all items.


## [0.3.0](https://github.com/php-censor/php-censor/tree/0.3.0) (2017-01-11)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.2.0...0.3.0)

### Changed

- Improved UI.
- Updated dependencies.
- Updated PHPUnit from 4.8 to 5.7.
- Improved build without config.


## [0.2.0](https://github.com/php-censor/php-censor/tree/0.2.0) (2017-01-07)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.1.0...0.2.0)

### Added

- Login by name (name or email).

### Changed

- Improved PHPUnit plugin.
- Improved UI.

### Fixed

- Fixed public build status page.


## [0.1.0](https://github.com/php-censor/php-censor/tree/0.1.0) (2017-01-04)

Initial release. Changes from [PHPCI](https://www.phptesting.org/) v1.7.1:

### Added

- More debug info into the build log.
- Item per page parameter for build list.

### Changed

- Moved CSS/JS dependencies from sources to Composer dependencies ([asset-packagist.org](https://asset-packagist.org/)).
- Redesigned project structure.
- Upped PHP minimal version from 5.3 to 5.6.

### Fixed

- Tests and other small fixes.


## 0.0.0 (2016-06-23)

Project started.
