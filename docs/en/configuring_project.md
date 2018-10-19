Project Build Configurations in PHP Censor
=====================================

Configuration methods
--------------------

For builds configuration *PHP Censor* uses description in YAML (Like in [Travis CI](https://travis-ci.org)).

There are several ways of configuring build in *PHP Censor* project:

1. Adding a project without any config (The easiest way).  

    In this case build launches with the default config, which includes dependency plugin
    ([Composer](plugins/composer.md)), code static analysis plugin (
    [TechnicalDept](plugins/technical_dept.md), [PHPLoc](plugins/php_loc.md), [PHPCpd](plugins/php_cpd.md), 
    [PHPCodeSniffer](plugins/php_code_sniffer.md), [PHPMessDetector](plugins/php_mess_detector.md), 
    [PHPDocblockChecker](plugins/php_docblock_checker.md), [PHPParallelLint](plugins/php_parallel_lint.md)), and test
     plugins([PHPUnit](plugins/php_unit.md), [Codeception](plugins/codeception.md)).  

    **Test plugins will launch if there are tests and configuration files by default paths**.
    
    Default config will look like this:

    ```yml
    build_settings:
      ignore:
        - "vendor"
    setup:
      composer:
        action: "install"
    test:
      technical_debt:
        allowed_errors: -1
      php_code_sniffer:
        allowed_warnings: -1
        allowed_errors: -1
      php_mess_detector:
        allowed_warnings: -1
      php_docblock_checker:
        allowed_warnings: -1
      security_checker:
        allowed_warnings: -1
      php_parallel_lint:
      php_loc:
      php_cpd:
      codeception:
      php_unit:
    ```

2. Adding a config `.php-censor.yml` (to accomplish backward compatibility with [PHPCI](https://www.phptesting.org) names 
`phpci.yml`/`.phpci.yml` are also supported) to the root of the project.

3. Adding a config via web-interface.

    By default a config from web-interface replaces a config from repository (`.php-censor.yml`). But if you uncheck the 
    option "Replace the configuration from file with the configuration from the data base", configurations will be 
    merged (the config from web-interface will have priority over the config from the repository). 
    
    Setting config via web-interface and merging it with config from the repo may be useful if you want to hide some
    secret data (passwords, keys) in case of using public repository. The most of the configuration can be stored as a 
    public file in the repo, and passwords and keys may be added via web-interface.   

**Config added via web-interface has the highest priority.**


Config file format
------------------------------

Config example:

```yml
build_settings:
  clone_depth: 1
  ignore:
    - "vendor"
    - "tests"
  mysql:
    host: "localhost"
    user: "root"
    pass: ""

setup:
  mysql:
    - "DROP DATABASE IF EXISTS test;"
    - "CREATE DATABASE test;"
    - "GRANT ALL PRIVILEGES ON test.* TO test@'localhost' IDENTIFIED BY 'test';"
  composer:
    action: "install"

test:
  php_unit:
    config:
      - "PHPUnit-all.xml"
      - "PHPUnit-ubuntu-fix.xml"
    directory:
      - "tests/"
    run_from: "phpunit/"
    coverage: "tests/logs/coverage"
  php_mess_detector:
    allow_failures: true
  php_code_sniffer:
    standard: "PSR2"
  php_cpd:
    allow_failures: true
  grunt:
    task: "build"

deploy:
  deployer:
    webhook_url: "http://deployer.local/deploy/QZaF1bMIUqbMFTmKDmgytUuykRN0cjCgW9SooTnwkIGETAYhDTTYoR8C431t"
    reason:      "PHP Censor Build #%BUILD% - %COMMIT_MESSAGE%"
    update_only: true

complete:
  mysql:
    host: "localhost"
    user: "root"
    pass: ""
    - "DROP DATABASE IF EXISTS test;"

branch-dev:
  run-option: replace
  test:
    grunt:
      task: "build-dev"
```
Build Stages
------

As mentioned earlier, PHP Censor is powered by plugins, there are several phases in which plugins can be run:

* `setup` - This phase is designed to initialise the build procedure.

* `test` - The tests that should be run during the build. Plugins run during this phase will contribute to the success 
or failure of the build.

* `deploy` - The deploy that should be run after the build. Plugins run during this phase will contribute to the 
success or failure of the build.

* `complete` - Always called when the `test` phase completes, regardless of success or failure. **Note** that is you 
do any DB stuff here, you will need to add the DB credentials to this section as well, as it runs in a separate 
instance.

* `success` - Called upon success of the `test` phase.

* `failure` - Called upon failure of the `test` phase.

* `fixed` - Called upon success of the `test` phase if the previous build of the branch was a failure.

* `broken` - Called upon failure of the `test` phase if the previous build of the branch was a success.

The `ignore` section is merely an array of paths that should be ignored in all tests (where possible).


Branch specific stages
----------------------

PHP Censor allows you configure plugins depending on the branch you configure in the project settings in the UI. 
You can replace a complete stage for a branch, or add extra plugins to a stage that run before or after the default 
plugins.  

### Example config

```yml
test: # Test stage config for all branches
  php_cs_fixer:
    allowed_warnings: -1
success: # Success stage config for all branches
  shell: ./notify

branch-regex:^feature\-\d$:
  run-option: replace
  test:
    php_cs_fixer:
      allowed_warnings: 5

branch-release: # Test config for release branch
  run-option: replace # This can be set to either before, after or replace
  test:

php_cs_fixer:
      allowed_warnings: 0
branch-master: # Test config for release branch
  run-option: after # This can be set to either before, after or replace
  success:
    shell:
      - "rsync ..."
```

### How it works

When you have configured a branch eg "stable" in the project settings in the UI. Add a new config named 
`branch-<branch>` (or use regular expression like: `branch-regex:^stable*`), in this case "branch-stable" to the 
`.php-censor.yml`. In this config, specify all stages and plugins you wish to run.

Also add a new config value `run-option`, that can have 3 values:

* `before` - will cause the branch specific plugins to run before the default ones.
* `after` - will cause the branch specific plugins to run after the default ones.
* `replace` - will cause the branch specific plugins to run and the default ones not.

Useful YAML features
--------------------

Some features of YAML could be very handy. Here is a demonstration of multi line strings, and of anchors and aliases.
See more details on [symfonys yaml document](https://symfony.com/doc/current/components/yaml/yaml_format.html) on in the [specification](http://yaml.org/spec/1.0/#id2563922).

```yml
setup:
    # yaml comment
    shell:
        - |
            echo a long shell command, multiple lines
            scriptPath=%PROJECT_BUILD_PATH%/../../hook-path/prepare-test5.sh
            if [ -f $scriptPath ]
            then
                "$scriptPath" '%PROJECT%' '%PROJECT_TITLE%' # script can read its path from $scriptPath
                mkdir ../outputs_to_keep/%COMMIT%
            fi
        - >
            echo this is a very long message I must write here, and it is much too long to allow good editing
            on only one line, therefore we break it up onto multiple lines, but the result will be on a single
            line.
        - echo a short command ...

branch-master:
    complete: &xmpp
        xmpp:
            username: &userName "login@gmail.com"
            password: &password "AZERTY123"
            recipients:
                - "builds-infos@jabber.org"
            server: &xmppServer "gtalk.google.com"
            alias: "build infos for project"
            date_format: "%d/%m/%Y"
    broken:
        xmpp:
            username: *userName
            password: *password
            recipients:
                - "build-alters-infos@jabber.org"
            server: *xmppServer
branch-bugfix1.9:
    complete: *xmpp
branch-bugfix2.0:
    complete: *xmpp
```
