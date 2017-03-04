<?php

return [
    'language_name' => 'Українська',
    'language' => 'Мова',

    // Log in:
    'log_in_to_app' => 'Увійти до PHP Censor',
    'login_error' => 'Невірний email або пароль',
    'forgotten_password_link' => 'Забули свій пароль?',
    'reset_emailed' => 'Ми відправили вам посилання для скидання вашого паролю.',
    'reset_header' => '<strong>Не хвилюйтесь!</strong><br>Просто введіть ваш email
і вам буде надіслано листа із посиланням на скидання паролю.',
    'reset_email_address' => 'Введіть свою email адресу:',
    'reset_send_email' => 'Скидання пароля',
    'reset_enter_password' => 'Введіть, будь ласка, новий пароль',
    'reset_new_password' => 'Новий пароль:',
    'reset_change_password' => 'Змінити пароль',
    'reset_no_user_exists' => 'Не існує користувача з такою email адресою, будь ласка, повторіть знову.',
    'reset_email_body' => 'Привіт, %s,

Ви отримали цей лист, тому що ви або хтось інший запросили скидання пароля в PHP Censor.

Якщо це були ви, будь ласка, перейдіть за посиланням нижче для скидання пароля: %ssession/reset-password/%d/%s,

або ж проігноруйте цей лист та нічого не робіть.

Дякуємо,

PHP Censor',

    'reset_email_title' => 'Скидання пароль PHP Censor для %s',
    'reset_invalid' => 'Невірний запит скидання паролю.',
    'email_address' => 'Email адреса',
    'login' => 'Логин / Email адреса',
    'password' => 'Пароль',
    'log_in' => 'Увійти',


    // Top Nav
    'toggle_navigation' => 'Сховати/відобразити панель навігації',
    'n_builds_pending' => '%d збірок очікує',
    'n_builds_running' => '%d збірок виконується',
    'edit_profile' => 'Редагувати профіль',
    'sign_out' => 'Вийти',
    'branch_x' => 'Гілка: %s',
    'created_x' => 'Створено: %s',
    'started_x' => 'Розпочато: %s',

    // Sidebar
    'hello_name' => 'Привіт, %s',
    'dashboard' => 'Панель управління',
    'admin_options' => 'Меню адміністратора',
    'add_project' => 'Додати проект',
    'settings' => 'Налаштування',
    'manage_users' => 'Управління користувачами',
    'plugins' => 'Плагіни',
    'view' => 'Переглянути',
    'build_now' => 'Зібрати',
    'edit_project' => 'Редагувати проект',
    'delete_project' => 'Видалити проект',

    // Project Summary:
    'no_builds_yet' => 'Немає збірок!',
    'x_of_x_failed' => '%d із останніх %d збірок були провалені.',
    'x_of_x_failed_short' => '%d / %d провалені.',
    'last_successful_build' => 'Останнью успішною збіркою була %s.',
    'never_built_successfully' => 'У цього проекта ніколи не було успішних збірок.',
    'all_builds_passed' => 'Усі із останніх %d збірок успішні.',
    'all_builds_passed_short' => '%d / %d успішні.',
    'last_failed_build' => 'Останньою проваленою збіркою була %s.',
    'never_failed_build' => 'У цього проекта ніколи не було провалених збірок.',
    'view_project' => 'Переглянути проект',

    // Timeline:
    'latest_builds' => 'Останні збірки',
    'pending' => 'Очікує',
    'running' => 'Виконується',
    'success' => 'Успіх',
    'failed' => 'Провалена',
    'manual_build' => 'Ручна збірка',

    // Add/Edit Project:
    'new_project' => 'Новий проект',
    'project_x_not_found' => 'Проект із ID %d не існує.',
    'project_details' => 'Деталі проекта',
    'public_key_help' => 'Для полегшення початку, ми згенерували пару SSH-ключів для вас для використання в цьому проекті.
Для їх використання - просто додайте наступний публічний ключ у розділ "deploy keys" обраної вами системи зберігання програмного коду.',
    'select_repository_type' => 'Оберіть тип репозиторію...',
    'github' => 'GitHub',
    'bitbucket' => 'Bitbucket',
    'gitlab' => 'GitLab',
    'remote' => 'Віддалений URL',
    'local' => 'Локальний шлях',
    'hg'    => 'Mercurial',

    'where_hosted' => 'Де зберігається ваш проект?',
    'choose_github' => 'Оберіть GitHub репозиторій:',

    'repo_name' => 'Ім’я репозиторія / URL (зовнішній) / Шлях (локальний)',
    'project_title' => 'Заголовок проекту',
    'project_private_key' => 'Приватний ключ доступу до репозиторія
(залишити поле порожнім для локального використання та/або анонімного доступу)',
    'build_config' => 'Конфігурація збірки цього проекта для PHP Censor
(якщо ви не додали файл .php-censor.yml (.phpci.yml|phpci.yml) до репозиторію вашого проекту)',
    'default_branch' => 'Назва гілки за замовчуванням',
    'allow_public_status' => 'Увімкнути публічну сторінку статусу та зображення для цього проекта?',
    'archived' => 'Архівний',
    'archived_menu' => 'Архів',
    'save_project' => 'Зберегти проект',

    'error_mercurial' => 'URL репозиторію Mercurial повинен починатись із http:// або https://',
    'error_remote' => 'URL репозиторію повинен починатись із git://, http:// або https://',
    'error_gitlab' => 'Ім’я репозиторія GitLab повинно бути у форматі "user@domain.tld:owner/repo.git"',
    'error_github' => 'Ім’я репозиторія повинно відповідати формату "owner/repo"',
    'error_bitbucket' => 'Ім’я репозиторія повинно відповідати формату "owner/repo"',
    'error_path' => 'Вказаний шлях не існує.',

    // View Project:
    'all_branches' => 'Усі гілки',
    'builds' => 'Збірки',
    'id' => 'ID',
    'date' => 'Дата',
    'project' => 'Проект',
    'commit' => 'Комміт',
    'branch' => 'Гілка',
    'status' => 'Статус',
    'prev_link' => '&laquo; Попер.',
    'next_link' => 'Наст. &raquo;',
    'public_key' => 'Публічний ключ',
    'delete_build' => 'Видалити збірку',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'Для автоматичної збірки цього проекту, при надходженні нових комітів, додайте наступний URL
у якості нового "Webhook" у розділі налаштувань
<a href="https://github.com/%s/settings/hooks">Webhooks and Services</a>
вашого GitHub репозиторію.',

    'webhooks_help_gitlab' => 'Для автоматичної збірки цього проекту, при надходженні нових комітів, додайте наступний URL
у якості нового "WebHook URL" у розділі "Web Hooks" вашого GitLab репозиторію.',

    'webhooks_help_bitbucket' => 'Для автоматичної збірки цього проекту, при надходженні нових комітів, додайте наступний URL
у якості нового "POST" сервісу у розділі
<a href="https://bitbucket.org/%s/admin/services">Services</a>
вашого Bitbucket репозиторію.',

    // View Build
    'build_x_not_found' => 'Збірка із ID %d не існує.',
    'build_n' => 'Збірка %d',
    'rebuild_now' => 'Перезібрати зараз',


    'committed_by_x' => 'Комміт від %s',
    'commit_id_x' => 'Комміт: %s',

    'chart_display' => 'Цей графік відобразиться після завершення збірки.',

    'build' => 'Збірка',
    'lines' => 'Рядків',
    'comment_lines' => 'Рядків коментарів',
    'noncomment_lines' => 'Рядків не коментарів',
    'logical_lines' => 'Рядків логіки',
    'lines_of_code' => 'Рядки коду',
    'build_log' => 'Лог збірки',
    'quality_trend' => 'Тенденція якості',
    'codeception_errors' => 'Помилки Codeception',
    'phpmd_warnings' => 'Попередження PHPMD',
    'phpcs_warnings' => 'Попередження PHPCS',
    'phpcs_errors' => 'Помилки PHPCS',
    'phplint_errors' => 'Помилки Lint',
    'phpunit_errors' => 'Помилки PHPUnit',
    'phpdoccheck_warnings' => 'Відсутні Docblocks',
    'issues' => 'Проблеми',

    'codeception' => 'Codeception',
    'phpcpd' => 'PHP Copy/Paste Detector',
    'phpcs' => 'PHP Code Sniffer',
    'phpdoccheck' => 'Відсутні Docblocks',
    'phpmd' => 'PHP Mess Detector',
    'phpspec' => 'PHP Spec',
    'phpunit' => 'PHP Unit',

    'file' => 'Файл',
    'line' => 'Рядок',
    'class' => 'Клас',
    'method' => 'Метод',
    'message' => 'Повідомлення',
    'start' => 'Запуск',
    'end' => 'Кінець',
    'from' => 'Від',
    'to' => 'До',
    'result' => 'Результат',
    'ok' => 'OK',
    'took_n_seconds' => 'Зайняло %d секунд',
    'build_started' => 'Збірка розпочата',
    'build_finished' => 'Збірка завершена',
    'test_message' => 'Message',
    'test_no_message' => 'No message',
    'test_success' => 'Successful: %d',
    'test_fail' => 'Failures: %d',
    'test_skipped' => 'Skipped: %d',
    'test_error' => 'Errors: %d',
    'test_todo' => 'Todos: %d',
    'test_total' => '%d test(s)',

    // Users
    'name' => 'Ім’я',
    'password_change' => 'Пароль (залишити порожнім, якщо не бажаєте змінювати його)',
    'save' => 'Зберегти &raquo;',
    'update_your_details' => 'Оновити ваші деталі',
    'your_details_updated' => 'Ваші деталі були оновлені.',
    'add_user' => 'Додати користувача',
    'is_admin' => 'Адміністратор?',
    'yes' => 'Так',
    'no' => 'Ні',
    'edit' => 'Редагувати',
    'edit_user' => 'Редагувати користувача',
    'delete_user' => 'Видалити користувача',
    'user_n_not_found' => 'Користувач із ID %d не існує.',
    'is_user_admin' => 'Чи є цей користувач адміністратором?',
    'save_user' => 'Зберегти користувача',

    // Settings:
    'settings_saved' => 'Ваші налаштування були збережені.',
    'settings_check_perms' => 'Ваші налаштування не можуть бути збережені, перевірте права на ваш файл налаштувань config.yml.',
    'settings_cannot_write' => 'PHP Censor не може записати файл config.yml, налаштування не будуть коректно збережені,
доки це не буде виправлено.',
    'settings_github_linked' => 'Ваш GitHub аккаунт було підключено.',
    'settings_github_not_linked' => 'Ваш GitHub аккаунт не може бути підключеним.',
    'build_settings' => 'Налаштування збірки',
    'github_application' => 'GitHub додаток',
    'github_sign_in' => 'Перед початком користування GitHub, вам необхідно <a href="%s">увійти</a> та надати
доступ для PHP Censor до вашого аккаунту.',
    'github_app_linked' => 'PHP Censor успішно зв\'язаний з аккаунтом GitHub.',
    'github_where_to_find' => 'Де це знайти...',
    'github_where_help' => 'Якщо ви є власником додатку, який би ви хотіли використовувати, то ви можете знайти інформацію про це у розділі
налаштувань ваших <a href="https://github.com/settings/applications">додатків</a>.',

    'email_settings' => 'Налаштування Email',
    'email_settings_help' => 'Перед тим, як PHP Censor почне надсилати статуси збірок на email,
вам необхідно налаштувати параметри SMTP нижче.',

    'application_id' => 'ID додатка',
    'application_secret' => 'Таємний ключ додатка',

    'smtp_server' => 'Сервер SMTP',
    'smtp_port' => 'Порт SMTP',
    'smtp_username' => 'Ім’я користувача SMTP',
    'smtp_password' => 'Пароль SMTP',
    'from_email_address' => 'Відправляти з Email',
    'default_notification_address' => 'Email для повідомлень за замовчуванням',
    'use_smtp_encryption' => 'Використовувати SMTP шифрування?',
    'none' => 'Ні',
    'ssl' => 'SSL',
    'tls' => 'TLS',

    'failed_after' => 'Вважати збірку проваленою після',
    '5_mins' => '5 хвилин',
    '15_mins' => '15 хвилин',
    '30_mins' => '30 хвилин',
    '1_hour' => '1 година',
    '3_hours' => '3 години',

    // Plugins
    'cannot_update_composer' => 'PHP Censor не може оновити composer.json, оскільки він не є доступним для запису.',
    'x_has_been_removed' => '%s було видалено.',
    'x_has_been_added' => '%s був доданий до composer.json і буде встановлений, як тільки
ви виконаєте composer update.',
    'enabled_plugins' => 'Увімкнені плагіни',
    'provided_by_package' => 'Наданий пакетом',
    'installed_packages' => 'Встановлені пакети',
    'suggested_packages' => 'Запропоновані пакети',
    'title' => 'Заголовок',
    'description' => 'Опис',
    'version' => 'Версія',
    'install' => 'Встановити &raquo;',
    'remove' => 'Видалити &raquo;',
    'search_packagist_for_more' => 'Знайти більше пакетів на Packagist',
    'search' => 'Знайти &raquo;',

    // Update
    'update_app' => 'Оновити базу даних для відображення змінених моделей.',
    'updating_app' => 'Оновлення бази даних PHP Censor:',
    'not_installed' => 'Неможливо встановити PHP Censor.',
    'install_instead' => 'Будь ласка, встановіть PHP Censor через команду php-censor:install.',

    // Build Plugins:
    'passing_build' => 'Успішно збірка',
    'failing_build' => 'Невдала збірка',
    'log_output' => 'Вивід лога:',
];
