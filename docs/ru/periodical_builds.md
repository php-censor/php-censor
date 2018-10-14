Периодические сборки
====================

Вы можете запускать периодические сборки для ваших проектов. Для запуска периодических сборок необходимо создать файл 
конфигурации `app/periodical.yml` и запустить один из демонов-воркеров с орпцией `--periodical-work|-p`: 
`./bin/console php-censor:worker -v --periodical-work` (Для сборщика по crontab все будет работать автоматически).

Пример конфигурации для периодических сборок:

```yaml
projects:
    1:                    # Id проекта
        branches:         # Список веток, которые необходимо собирать периодически
            - master
            - release-1.0
            - release-2.0
        interval: P1W     # Интервал сборки, если за этот период не было других сборок (webhook, ручных и т.д.). Используется формат PHP-класса DateInterval. Смотри: http://php.net/manual/ru/dateinterval.construct.php
    12:                   # Еще один Id проекта
        branches:
            - master
        interval: PT12H
```
