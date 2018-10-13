Periodical builds
=================

You can create periodical builds for your projects. For starting use periodical builds you should create config 
`app/periodical.yml` and run **one of the workers** with option `--periodical-work|-p`: 
`./bin/console php-censor:worker -v --periodical-work` (With cronjob worker feature starts work automatically).

Periodical builds config example:

```yaml
projects:
    1:                    # Project id
        branches:         # Branch list for periodical build
            - master
            - release-1.0
            - release-2.0
        interval: P1W     # Interval to build project if no other builds (from webhook etc.).Used format of PHP DateInterval class. See: http://php.net/manual/ru/dateinterval.construct.php
    12:                   # Another project id
        branches:
            - master
        interval: PT12H
```
