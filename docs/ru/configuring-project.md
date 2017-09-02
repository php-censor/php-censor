Настройка сборки проекта в PHP Censor
=====================================

Способы конфигурации
--------------------

Для конфигурации сборки проектов *PHP Censor* использует декларативное описание в формате YAML (Подобно 
[Travis CI](https://travis-ci.org)).

Есть несколько способов настроить сборку проекта в *PHP Censor*:

1. Добавить проект вообще без какой-либо конфигурации (Самый простой способ).  

    В этом случае сборка будет запущена со стандартной конфигурацией, которая включает в себя плагины для установки 
    зависимостей ([Composer](plugins/composer.md)), статического анализа кода (
    [TechnicalDept](plugins/technical_dept.md), [PHPLoc](plugins/php_loc.md), [PHPCpd](plugins/php_cpd.md), 
    [PHPCodeSniffer](plugins/php_code_sniffer.md), [PHPMessDetector](plugins/php_mess_detector.md), 
    [PHPDocblockChecker](plugins/php_docblock_checker.md), [PHPParallelLint](plugins/php_parallel_lint.md)), а также 
    плагины для запуска тестов ([PHPUnit](plugins/php_unit.md), [Codeception](plugins/codeception.md)).  

    **Плагины для запуска тестов запустятся, если смогут найти конфигурации и тесты по стандартным путям**.  

    При сборке без конфигурации будет сгенерирован примерно такой конфигурационный файл:

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

2. Добавить конфигурационный файл `.php-censor.yml` (поддерживается так же названия `phpci.yml`/`.phpci.yml` для 
обеспечения обратной совместимости c [PHPCI](https://www.phptesting.org)) в корень репозитория проекта.

3. Добавить конфигурационный файл в веб-интерфейсе при добавлении проекта в *PHP Censor*.

**Наивысший приоритет имеет конфигурация заданная через веб-интерфейс, затем конфигурация, которая расположена в корне 
проекта.**


Формат конфигурационного файла
------------------------------

Ниже приведен пример конфигурационного файла проекта:

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


### Общие настройки

Секция `build_settings` содержит общие настройки сборки:

* Опция `verbose` позволяет отключить подробный вывод плагинов (По-умолчанию `verbose: true`, то есть выводится 
все возможные логи).

* Опция `ignore` задает массив путей, которые будут игнорироваться при анализе кода плагинами. Например:

    ```yml
    build_settings:
      ignore:
        - vendor
        - tests
    ```

* Опция `prefer_symlink` позволяет использовать ссылку (symlink) в качестве источника для сборки из локальной 
директории.

* Так же существуют опции для глобальной конфигурации некоторых плагинов ([Campfire](plugins/campfire.md), 
[Irc](plugins/irc.md), [Mysql](plugins/mysql.md), [Pgsql](plugins/pgsql.md) и [Sqlite](plugins/sqlite.md)), 
информацию он них можно посмотреть в [документации к соответсвующим плагинам](README.md).


### Этапы сборки

Сборка проекта проходит несколько этапов. Во время каждого этапа можно выполнять некоторые плагины:

* `setup` - **Этап настройки сборки** (создание тестовой базы данных, установка зависимостей и т.д.).

* `test` - **Этап тестирования**. Вызывается после `setup`, если он был успешным. В этапе тестирования запускаются 
все основные тестовые плагины и статические анализаторы кода.  

    **Завершение отдельного плагина с ошибками не всегда обозначает провал всего этапа**, т.к. можно 
    использовать опцию `allow_failures` доступную всем плагинам, которая позволяет игнорировать ошибки конкретного 
    плагина в статусе сборки (Пример: `allow_failures: true`).

    **Так же можно ограничить количество допустимых ошибок и предупреждений, которое приводит к провалу конкретного 
    плагина** с помощью опций `allowed_errors` и `allowed_warnings` (Например: `allowed_warnings: 2`). Значение `-1` 
    будет означать неограниченное кол-во. **Эти опции доступны не для всех плагинов**, подробности можно посмотреть в 
    [документации к конкретному плагину](README.md).

    Так же существует опция `priority_path` доступная всем плагинам. **Она позволяет поменять порядок поиска 
    исполняемого файла плагина**. Возможные значения опции:

    * `local` - В первую очередь искать в директории `vendor/bin` самой сборки, затем в `global`, затем в `system`;

    * `global` - В первую очередь искать в директории `vendor/bin` *PHP Censor*, затем в `local`, затем в `system`;

    * `sysmem` - В первую очередь искать среди системных утилит (В директориях `/bin`, `/usr/bin` и т.д., используется 
    `which`. ), затем в `local`, затем в `global`;

    **Порядок поиска исполняемого файла по умолчанию**: `local` -> `global` -> `system`.

* `deploy` - **Этап деплоя проекта**. Вызывается после `test`, если он был успешным. В этом этапе должны 
вызываться плагины для деплоя приложения ([Shell](plugins/shell.md), [Deployer](plugins/deployer.md), 
[Mage](plugins/mage.md) и т.д.). В целом этот этап работает аналогично `test`.

* `complete` - **Этап завершения сборки**. Вызывается **всегда** после `deploy` (или `test`, если `deploy` 
отсутствует), вне зависимости от того, была ли сборка успешной или провалилась. В этой фазе можно отсылать нотификации 
или очищать тестовую базу данных и т.д.

* `success` - **Этап успешной сборки**. Вызывается лишь тогда, когда сборка завершилась успешно.

* `failure` - **Этап проваленной сборки**. Вызывается лишь тогда, когда сборка провалилась.

* `fixed` - **Этап восстановления сборки**. Вызывается лишь тогда, когда сборка завершилась успешно после проваленной 
предыдущей сборки.

* `broken` - **Этап поломки сборки**. Вызывается лишь тогда, когда сборка провалилась после успешной предыдущей сборки.

**Некоторые плагины имеют ограничения на этапы, в которых они могут быть запущены**. Например плагины 
[TechnicalDept](plugins/technical_dept.md), [PHPLoc](plugins/php_loc.md), [PHPCpd](plugins/php_cpd.md), 
[PHPCodeSniffer](plugins/php_code_sniffer.md), [PHPMessDetector](plugins/php_mess_detector.md), 
[PHPDocblockChecker](plugins/php_docblock_checker.md), [PHPParallelLint](plugins/php_parallel_lint.md), 
[Codeception](plugins/codeception.md), [PhpUnit](plugins/php_unit.md) могут быть запущены только на этапе `test`, 
а плагин [Composer](plugins/composer.md), - только на этапе `setup`.


### Переопределение конфигурации для конкретных веток

Директива `branch-<branch-name>` (Например: `branch-feature-1` для ветки `feature-1`) **позволяет переопределять или 
дополнять основную конфигурацию сборки для отдельных веток**.

Существует так же директива `branch-regex:<branch-name-regex>` **для сопоставления ветки по регулярному выражению** 
для тех же целей (Например: `branch-regex:^feature\-\d$` для веток: `feature-1`, `feature-2` и т.д.).

**Если используется несколько директив `branch-regex:`/`branch-`, то будет использована первая, которая 
совпадет с названием ветки сборки.**

Обязательный параметр `run-option` позволяет указать, переопределять или дополнять конфигурацию и может принимать 
следующие значения:

* `replace` - Позволяет перекрывать некоторые настройки для отдельных веток.

* `before` - Позволяет дополнить конфигурацию для конкретной ветки настройками, которые будут запущены до основной 
конфигурации.

* `after` - Позволяет дополнить конфигурацию для конкретной ветки настройками, которые будут запущены после основной 
конфигурации.

Пример конфигурации:

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

### Некоторые полезные особенности формата YAML

Некоторые особенности формата YAML могут быть очень полезными, например: комментарии, многострочные выражения и 
подстановки с помощью якорей (`&`) и алиасов (`*`).

Пример комментария:

```yml
# YAML-комментарий
setup:
  shell: echo 1
```

Пример организации многострочного выражения c сохранением перевода строки:

```yml
setup:
  shell: |
    echo 1
    echo 2
```

Пример организации многострочного выражения без сохранения перевода строки:

```yml
setup:
  shell: >
    echo 1
    echo 2
```

Пример подстановки:

```yml
setup:
  # Якорь (метка)
  shell: &anchor1 echo 1
test:
  # Алиас (подстановка по имени якоря)
  shell: *anchor1
```

Больше информации о YAML формате можно узнать на [официальном сайте](http://www.yaml.org/spec/1.2/spec.html).
