Adding a Virtual Host
---------------------

In order to access the PHPCI web interface, you need to set up a virtual host in your web server. 

Below are a few examples of how to do this for various different web servers.

Nginx Example
=============

```
server {
        ... standard virtual host ...

        location / {
                try_files $uri @phpci;
        }

        location @phpci {
                # Pass to FastCGI:
                fastcgi_pass    unix:/path/to/phpfpm.sock;
                fastcgi_index   index.php;
                fastcgi_buffers 256 4k;
                include         fastcgi_params;
                fastcgi_param   SCRIPT_FILENAME $document_root/index.php;
                fastcgi_param   SCRIPT_NAME index.php;
        }
}
```

Apache Example
==============

For Apache, you can use a standard virtual host, as long as your server supports PHP. All you need to do is add the following to a `.htaccess` file in your PHPCI `/public` directory.

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

- Edit virtual host in apache2.
```
<VirtualHost *:80>
    ServerAdmin user@domain.com
    DocumentRoot /var/www/phpci/public
    ServerName phpci.vagrant
    ServerAlias phpci.vagrant

    <Directory /var/www/phpci/public/>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/phpci-error_log
    CustomLog ${APACHE_LOG_DIR}/phpci-access_log combined
</VirtualHost>
```

- Add in /etc/hosts
```
127.0.0.1   phpci.vagrant
```

Built-in PHP Server Example
===========================

You can use the built-in PHP server `php -S localhost:8080` by adding `public/routing.php`.

```php
<?php

if (file_exists(__DIR__ . '/' . $_SERVER['REQUEST_URI'])) {
    return false; // serve the requested resource as-is.
} else {
    include_once 'index.php';
}
```
