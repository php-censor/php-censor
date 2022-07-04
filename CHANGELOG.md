Changelog 2.1
=============

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to 
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [2.1.0 (Mr. Meeseeks)](https://github.com/php-censor/php-censor/tree/2.1.0) (2022-xx-xx)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.10...2.1.0)

### Added

- Secrets storage with UI and secret variables in build interpolation (you can use it like `%SECRET:secret_name%`. See [documentation](https://github.com/php-censor/php-censor/blob/master/docs/en/interpolation.md)). Issue [#14](https://github.com/php-censor/php-censor/issues/#14).
- Optional logging into database webhook requests payloads (option `php-censor.webhook.log_requests`). Issue [#384](https://github.com/php-censor/php-censor/issues/#384).
- Steps inside stages (`test`, `deploy` etc.) which allow to have several same plugins into one stage. Issue [#91](https://github.com/php-censor/php-censor/issues/#91). Pull request [#417](https://github.com/php-censor/php-censor/pull/417). Thanks to [@KieranFYI](https://github.com/KieranFYI). Usage example:
    ```yml
    setup: # <--- stage
      setup_env: # <--- step 1
        plugin: shell # <--- step 1 plugin name
        commands:
          - "php -r \"copy('.env.ci', '.env');\""
          - "php artisan key:generate"
          - "chmod -R 777 storage bootstrap/cache"
      migrate: # <--- step 2
        plugin: shell # <--- step 2 same plugin name
        commands:
          - "php artisan migrate"
    ```
- `GET`-parameter `environment` for Git webhook. Issue [#407](https://github.com/php-censor/php-censor/issues/#407).
- Cloning/coping projects ability.
- **[PHP Unit]** Coverage trand for builds in the timeline on dashboard.

### Changed

- Massive refactoring: added types, dependency injection, new tests, documentation, fixed code style etc. Issue [#413](https://github.com/php-censor/php-censor/issues/#413). Pull requests [#412](https://github.com/php-censor/php-censor/pull/412), [#424](https://github.com/php-censor/php-censor/pull/424) and [#425](https://github.com/php-censor/php-censor/pull/425). Thanks to [@KieranFYI](https://github.com/KieranFYI) and [@Ooypunk](https://github.com/Ooypunk).
- Integrated `symfony/http-foundation` library as a new HTTP part of project.
- Integrate some features from `php-censor/common` library.
- Improved UI: fixed colors and ratio for `Chart.js` charts, added ability to disable AJAX UI reloading (option `php-censor.realtime_ui`), improved error trends view. Pull request [#426](https://github.com/php-censor/php-censor/pull/426). Thanks to [@KieranFYI](https://github.com/KieranFYI).
- Improved Ukrainian localization. Pull request [#419](https://github.com/php-censor/php-censor/pull/419). Thanks to [@oshka](https://github.com/oshka).

### Fixed

- Install command return code.
- [PHPUnit] Xdebug settings for coverage option. Pull request [#427](https://github.com/php-censor/php-censor/pull/427). Thanks to [@KieranFYI](https://github.com/KieranFYI).

## Other versions

- [0.x Changelog](/docs/CHANGELOG_0.x.md)
- [1.0 Changelog](/docs/CHANGELOG_1.0.md)
- [1.1 Changelog](/docs/CHANGELOG_1.1.md)
- [1.2 Changelog](/docs/CHANGELOG_1.2.md)
- [1.3 Changelog](/docs/CHANGELOG_1.3.md)
- [2.0 Changelog](/docs/CHANGELOG_2.0.md)
