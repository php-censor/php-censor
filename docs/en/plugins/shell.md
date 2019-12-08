Plugin Shell
============

Runs a given Shell command.

Configuration
-------------

### Options

* **commands** [array, required] - The shell commands to run.
* **execute_all** [bool, optional, default: false] - If is true all commands will be execute if one of the commands 
failed.

```yaml
setup:
   shell:
       execute_all: true
       commands:
           - "[ -d /www ]"
           - "chmod u+x %BUILD_PATH%/bin/console"
           - "%BUILD_PATH%/bin/console build"
```

When a one of commands fails, the remaining ones are not run.

#### Each new command forgets about what was before

So if you want cd to directory and then run script there, combine those two commands into one like:

```yaml
setup:
    shell:
        commands:
            - "cd %BUILD_PATH% && php artisan migrate"
```

[See variables which you can use in shell commands](../interpolation.md)

### Additional Options

The following general options can also be used:

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
