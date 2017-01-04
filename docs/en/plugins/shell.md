Plugin Shell
============

Runs a given Shell command.

Configuration
-------------

### Options

* **command** - Required - The shell command to run.

```yml
setup:
    shell:
        command: "bin/console build"
```
 You should understand, that in old configuration type, you can run only one command!

#### New format of Configuration Options

```yml
setup:
   shell:
       - "cd /www"
       - "chmod u+x %BUILD_PATH%/bin/console"
       - "%BUILD_PATH%/bin/console build"
```

#### Each new command forgets about what was before

So if you want cd to directory and then run script there, combine those two commands into one like:

```yml
setup:
    shell:
        - "cd %BUILD_PATH% && php artisan migrate" # Laravel Migrations
```

[See variables which you can use in shell commands](../interpolation.md)
