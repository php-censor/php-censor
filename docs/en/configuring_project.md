Project Build Configurations in PHP Censor
==========================================

Configuration methods
---------------------

For builds configuration *PHP Censor* uses description in YAML (Like in [Travis CI](https://travis-ci.org)).

There are several ways of configuring build in *PHP Censor* project:

1. Adding a project without any config (The easiest way).  

    In this case build launches with the default config, which includes dependency plugin
    ([Composer](plugins/composer.md)), code static analysis plugin (
    [TechnicalDebt](plugins/technical_debt.md), [PHPLoc](plugins/php_loc.md), [PHPCpd](plugins/php_cpd.md), 
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
            allow_failures: true
          php_loc:
          php_cpd:
          codeception:
          php_unit:
    ```

2. Adding a config `.php-censor.yml` to the root of the project.

3. Adding a config via web-interface.

    By default a config from web-interface replaces a config from repository (`.php-censor.yml`). But if you uncheck 
    the option "Replace the configuration from file with the configuration from the data base", configurations will be 
    merged (the config from web-interface will have priority over the config from the repository). 
    
    Setting config via web-interface and merging it with config from the repo may be useful if you want to hide some
    secret data (passwords, keys) in case of using public repository. The most of the configuration can be stored as a 
    public file in the repo, and passwords and keys may be added via web-interface.   

**NOTE:** Config added via web-interface has the highest priority.

Config file format
------------------

Config example:

```yml
build_settings:
  clone_depth: 1
  priority_path: binary_path
  binary_path:   /home/user/bin/
  directory:     /home/project
  build_priority: 1 # only in web-interface
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
    priority_path: binary_path
    binary_path:   /home/user/sbin/
    binary_name:   phpmd-local
  php_code_sniffer:
    standard: "PSR2"
  php_cpd:
    allow_failures: true
  grunt:
    task: "build"

deploy:
  deployer:
    webhook_url: "http://deployer.local/deploy/QZaF1bMIUqbMFTmKDmgytUuykRN0cjCgW9SooTnwkIGETAYhDTTYoR8C431t"
    reason:      "PHP Censor Build #%BUILD_ID% - %COMMIT_MESSAGE%"
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

### Common build settings

Section `build_settings` contents common build settings:

* Option `verbose` enable/disable verbosity of plugins output (Default value: `verbose: true`).

* Option `clone_depth: N` allows to clone repository with partial history (Git clone option `--depth=N`). Option 
supports Git (GitHub, GitLab, BitBucket, Gogs) and Svn (Subversion) builds.

    **ATTENTION!:** Option `clone_depth` should be set only from web-interface (Project edit page) because it should 
    be known before repository cloning. Also you should understand that some features or plugins with the option may 
    work with unpredictable result.

* Option `directory` sets default directory path for all plugins (It may be overloaded by the plugin option 
`directory`).

* Option `ignore` sets default ignore list for all plugins (It may be completed by the plugin option `ignore`). For 
example config returns ignore list: `vendor, tests, docs`:

    ```yml
    build_settings:
      ignore:
        - vendor
        - tests
    ...
    test:
      example_plugin:
        ignore:
          - ./vendor
          - ./docs
    ```

* Option `priority_path` sets default searching binary priority path for all plugins (It may be overloaded by the 
plugin option `priority_path`).

* Option `binary_path` sets default binary path for all plugins (It may be overloaded by the plugin option 
`priority_path`). For example: `binary_path: /usr/local/bin`.

* Option `prefer_symlink` allows to use symlinks as a source build path. The option works only for local build source 
(`LocalBuild`).

* Option `build_priority: N` builds of a project with a higher value (like 4) are handled after builds of projects 
with lower values (like -2). Default is 0.

    **ATTENTION!:** Option `build_priority` should be set only from web-interface (Project edit page) because it only 
    has an effect before the repository is cloned.

* Also we have global options (Usually connection settings) for some plugins like: ([Campfire](plugins/campfire.md), 
[Irc](plugins/irc.md), [Mysql](plugins/mysql.md), [Pgsql](plugins/pgsql.md) и [Sqlite](plugins/sqlite.md)). See 
documentation of the plugins for more details.

* Also we have options for configuring connection parameters for Svn (Subversion) project source type. For example:

    ```yml
    build_settings:
      svn:
        username: "username"
        password: "password"
    ```

    **ATTENTION!:** Section `svn` should be set only from web-interface (Project edit page) because it should 
    be known before repository cloning.

Build Stages
------------

The build goes through some stages. During each stage some plugins can be executed.

* `setup` - The stage of setting up the build (creating test database, setting dependencies, etc.).

* `test` - The stage of testing. Runs after the setup stage if the setup was successful. In this stage all the main plugins and statistical code analyzers are executed.

There is also a priority_path option available to all plugins. It allows you to change the search order of the plugin executable file. Possible option values are:

* `local` - In the first place search in the buid directory vendor/bin, then - in global, then - in system, then - in priority_path;

* `global` - In the first place search in the directory vendor/bin *PHP Censor*,  then - in local, then - in system, then - in priority_path;

* `system` - In the first place search among the system utilities ( /bin, /usr/bin etc., use  which), then - in local, then - in global, then - in priority_path;

* `binary_path` - First of all, look for the specific path specified in the binary_path option, then - in local, then - in global, then - in system;

The binary_path option allows you to set a specific path to the directory with the executable plugin file. There is also a binary_name option which alows to set an alternative name for the executable file (a string or an array of strings).

Example:
````
yaml
    setup:
      composer:
        priority_path: binary_path
        binary_path: /home/user/bin/
        # Search will be by executable file name: composer-1.4, composer-local, composer, composer.phar
        binary_name:
          - composer-1.4
          - composer-local
        action: install

````

Search order of the executable file by default: local -> global -> system -> binary_path.

* `deploy` - The stage of  the project deployment. Runs after the stage of testing, if the tests were successful. In this stage deployment plugins should be called ([Shell](plugins/shell.md), [Deployer](plugins/deployer.md), [Mage](plugins/mage.md) и etc.). This stage is very similar to test.

* `complete` - Build completion stage. Always executes after the deploy (or after the test, in case deploy is missing), regardless of whether the buid was successful or failed. In this stage it is possible to send notifications, to clear a database, etc.

* `success` - Successful Build Stage. Called only when the build completed successfully.

* `failure` - This stage is called only when the build failed.

* `fixed` - Build recovery stage. Called only when the build completed successfully after a failed previous build.

* `broken` - Build failure stage. Called only when the build failed after a successful previous build .


Some plugins have restrictions on the stages in which they can be launched.
For example, plugins
[TechnicalDept](plugins/technical_dept.md), [PHPLoc](plugins/php_loc.md), [PHPCpd](plugins/php_cpd.md), 
[PHPCodeSniffer](plugins/php_code_sniffer.md), [PHPMessDetector](plugins/php_mess_detector.md), 
[PHPDocblockChecker](plugins/php_docblock_checker.md), [PHPParallelLint](plugins/php_parallel_lint.md), 
[Codeception](plugins/codeception.md), [PhpUnit](plugins/php_unit.md) can only be launched at test stage. The plugin [Composer](plugins/composer.md), can only be launched at setup stage


### Redefining configuration for the specific branches.

The directive `branch-<branch-name>` (For example: `branch-feature-1` for the branch `feature-1`) **allows to redefine or
to complete the main build configuration for the specific branches**.

There is also a directive `branch-regex:<branch-name-regex>` **which allows to compare branches by regexp** 
for the same purposes (For example: `branch-regex:^feature\-\d$` for the branches: `feature-1`, `feature-2` etc.).

**If there are several directives `branch-regex:`/`branch-`, the first directive that matches up with the name of the branch will be used.**

The required parameter `run-option` allows to define, to redefine and to complete the configuration and can take different values:

* `replace` - will cause the branch specific plugins to run and the default ones not.

* `before` - will cause the branch specific plugins to run before the default ones.

* `after` - will cause the branch specific plugins to run after the default ones.

Configuration example:

```yml
test

...

branch-regex:^feature\-\d$:
  run-option: replace
  test:
    grunt:
      task: "build-feature"
branch-dev:
  run-option: replace
  test:
    grunt:
      task: "build-dev"
branch-codeception:
  run-option: after
  test:
    codeception:
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
            scriptPath=%BUILD_PATH%/../../hook-path/prepare-test5.sh
            if [ -f $scriptPath ]
            then
                "$scriptPath" '%PROJECT_ID%' '%PROJECT_TITLE%' # script can read its path from $scriptPath
                mkdir ../outputs_to_keep/%COMMIT_ID%
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
