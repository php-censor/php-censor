Plugin Shell
============

Runs a given Shell command.

Configuration
-------------

### Options

* **command** [string, required] - The shell command to run.

```yaml
setup:
    shell:
        command: "bin/console build"
```

You should understand, that in old configuration type, you can run only one command!

#### New format of Configuration Options

```yaml
setup:
   shell:
       - "[ -d /www ]"
       - "chmod u+x %BUILD_PATH%/bin/console"
       - "%BUILD_PATH%/bin/console build"
```

When a command fails, the remaining ones are not run.

#### Each new command forgets about what was before

So if you want cd to directory and then run script there, combine those two commands into one like:

```yaml
setup:
    shell:
        - "cd %BUILD_PATH% && php artisan migrate" # Laravel Migrations
```

[See variables which you can use in shell commands](../interpolation.md)

### Additional Options

The following general options can also be used: 

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
