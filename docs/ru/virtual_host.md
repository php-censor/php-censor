Настройка веб-интерфейса
========================

Для доступа к веб-интерфейсу PHP Censor нужно настроить виртуальный хост на веб-сервере. Ниже описана несколько 
примеров конфигурации для различных веб-серверов:

Nginx
-----

```
server {
    charset utf-8;
    client_max_body_size 128M;

    listen *:80;

    server_name php-censor.local www.php-censor.local;
    root /var/www/php-censor.local/public;

    #access_log /var/www/php-censor.local/runtime/nginx_access.log;
    error_log  /var/www/php-censor.local/runtime/nginx_errors.log warn;

    location ~* \.(htm|html|xhtml|jpg|jpeg|gif|png|css|zip|tar|tgz|gz|rar|bz2|doc|xls|exe|pdf|ppt|wav|bmp|rtf|swf|ico|flv|txt|docx|xlsx)$ {
        try_files $uri @fpm;
        expires    30d;
    }

    location / {
        try_files $uri @fpm;
    }

    location @fpm {
        fastcgi_pass  unix:/var/run/php/php-fpm.sock;

        include fastcgi_params;

        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_param SCRIPT_NAME index.php;
    }
}
```

Apache
------

В случае Apache, вы можете использовать виртуальный хост, если ваш сервер поддерживает PHP. Все что вам нужно сделать 
- добавить сдедующий  `.htaccess` файл в `/public` директорию PHP Censor.

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

- Создайте виртуальный хост для Apache:

```
<VirtualHost *:80>
    ServerAdmin  admin@php-censor.local
    DocumentRoot /var/www/php-censor.local/public
    ServerName   php-censor.local
    ServerAlias  www.php-censor.local

    <Directory /var/www/php-censor.local/public/ >
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/php-censor-error_log
    CustomLog ${APACHE_LOG_DIR}/php-censor-access_log combined
</VirtualHost>
```

- Добавьте в файл `/etc/hosts` следующий текст:

```
127.0.0.1 php-censor.local www.php-censor.local
```

Встроенный сервер PHP
---------------------

Вы можете использовать встроенный в PHP тестовый веб-сервер, запустив его командой 
`php -S localhost:8080 -t ./public/routing.php` и добавив файл `./public/routing.php` следующего содержания:

```php
<?php

if (file_exists(__DIR__ . '/' . $_SERVER['REQUEST_URI'])) {
    return false;
} else {
    include_once __DIR__ . 'index.php';
}
```
