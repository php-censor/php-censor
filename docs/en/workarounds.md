Known issues and workarounds
============================

PhpLoc with PHP < 7.2 and XDebug > 3.0
--------------------------------------

If you are should support version of PHP < 7.2 with XDebug version > 3.0, you may patch PHPLoc 4.0.1 (Latest PHPLoc 
version with PHP < 7.2 support) for support XDebug > 3.0. Do following:

1. Install plugin `composer-patches` for patching dependencies:

```bash
composer require cweagans/composer-patches
```

2. Configure patching in `composer.json`:

```json
{
    ...
    "config": {
        "preferred-install": "source"
    },
    "extra": {
        "patches": {
            "phploc/phploc": {
                "PhpLoc4 XDebug3 Fix": "<path_to_patch>/phploc4-xdebug3-fix.patch"
            }
        },
        "enable-patching": true
    },
    ...
}
```

3. Create patch file (For example: `phploc4-xdebug3-fix.patch`):

```
diff --git a/src/CLI/Application.php b/src/CLI/Application.php
index faab87f..fa5c87d 100644
--- a/src/CLI/Application.php
+++ b/src/CLI/Application.php
@@ -109,6 +109,8 @@ private function disableXdebug()
         \ini_set('xdebug.show_exception_trace', 0);
         \ini_set('xdebug.show_error_trace', 0);
 
-        \xdebug_disable();
+        if (\function_exists('xdebug_disable')) {
+            \xdebug_disable();
+        }
     }
 }
```

4. Update dependencies:

```bash
composer update
```
