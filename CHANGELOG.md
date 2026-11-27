Changelog 3.0
=============

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## Unreleased

### Changed

- **Minimal PHP version increased to 8.3 (from 8.1)**. PHP 8.4 and 8.5 are supported.
- Dependencies upgraded: Symfony components `^7.4 || ^8.0` (from `^5.4`), Monolog 3, `psr/log` 3, Phinx 0.16,
  Pheanstalk 8, Guzzle 7.9/8, `sensiolabs/ansi-to-html` 2, `sebastian/diff` 7-9. Dev tools: PHPUnit 12.5/13, Rector 2,
  Psalm 6/7, Infection 0.35, PHP_CodeSniffer 4, PHPMD 3.
- Emails are sent with Symfony Mailer instead of SwiftMailer (abandoned). The `email_settings` config is unchanged:
  `smtp_encryption: ssl` is implicit TLS, `smtp_encryption: tls` is mandatory STARTTLS, no encryption is plain SMTP.
- The `php-censor/common` library (which required `symfony/mailer ^5.4`) is now part of the application
  (`src/Common`, same `PHPCensor\Common\` namespace; only PHP 8.2+ deprecation fixes).
- Phinx 0.16 is configured to keep the Phinx 0.12 schema defaults used by the existing migrations
  (signed primary keys, NOT NULL columns). On a new installation, MySQL tables are created with the `utf8mb4`
  charset and PostgreSQL primary keys as identity columns.

### Fixed

- Worker watched the "default" queue tube for the "is a job ready?" check (since Pheanstalk 4) instead of the
  configured one, and did not recover from a lost queue connection.
- `Request::get()` (removed in Symfony 8) replaced in `Controller::getParam()`, same lookup order.
- **[PhpCsFixer]** Report parsing adapted to the `sebastian/diff` 7+ API (the `get*()` getters were removed).
- Webhooks always failed with HTTP 500 (`Controller::handleAction()` return type did not allow the webhook arrays).
- Sending an email outside of a build (password reset) no longer fails on a missing build logger.
- Invalid `app/periodical.yml` triggered a `TypeError` while logging the error.
- PHP 8.4/8.5 deprecations (implicitly nullable parameters, `${var}` interpolation, `E_STRICT`,
  `PDO::MYSQL_ATTR_INIT_COMMAND` on PHP 8.4+, `is_dir(null)`, `null` array offsets).

### Removed

- Dev dependencies `sebastian/phpcpd`, `phploc/phploc` (abandoned) and `php-censor/phpdoc-checker`
  (not compatible with Symfony 7+).

## [3.0.0](https://github.com/php-censor/php-censor/tree/3.0.0) (2025-03-30)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.1.6...3.0.0)

### [How tp upgrade from v1 to v2](/docs/en/upgrades/UPGRADE_2.0.md)
### [How tp upgrade from v2 to v3](/docs/en/upgrades/UPGRADE_3.0.md)

### Added

- CLI commands for secrets.
- PHP executable path in "build_settings" config.
- **[TelegramNotify]** Notifications in telegram group topics. Pull request [#440](https://github.com/php-censor/php-censor/pull/440). Thanks to [@yugeon](https://github.com/yugeon).

### Changed

- **Minimal PHP version increased to 8.1 (from 7.4)**.

### Removed

- **[HipchatNotify]** HipChat notification plugin (It was shut down in 2019). Pull request [#433](https://github.com/php-censor/php-censor/pull/433). Thanks to [@StudioMaX](https://github.com/StudioMaX).

## Other versions

- [0.x Changelog](/docs/en/changelogs/CHANGELOG_0.x.md)
- [1.0 Changelog](/docs/en/changelogs/CHANGELOG_1.0.md)
- [1.1 Changelog](/docs/en/changelogs/CHANGELOG_1.1.md)
- [1.2 Changelog](/docs/en/changelogs/CHANGELOG_1.2.md)
- [1.3 Changelog](/docs/en/changelogs/CHANGELOG_1.3.md)
- [2.0 Changelog](/docs/en/changelogs/CHANGELOG_2.0.md)
- [2.1 Changelog](/docs/en/changelogs/CHANGELOG_2.1.md)
