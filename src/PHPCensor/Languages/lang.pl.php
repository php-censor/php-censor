<?php

return [
    'language_name' => 'Polski',
    'language' => 'Język',

    // Log in:
    'log_in_to_app' => 'Zaloguj się do PHP Censor',
    'login_error' => 'Nieprawidłowy email lub hasło',
    'forgotten_password_link' => 'Zapomniałeś hasła?',
    'reset_emailed' => 'Email z linkiem resetującym hasło został wysłany.',
    'reset_header' => '<strong>Spokojnie!</strong><br>Wpisz swój adres email w polu poniżej a my wyślemy Ci link
resetujący hasło.',
    'reset_email_address' => 'Podaj swój adres email:',
    'reset_send_email' => 'Wyślij reset hasła emailem',
    'reset_enter_password' => 'Wpisz nowe hasło',
    'reset_new_password' => 'Nowe hasło:',
    'reset_change_password' => 'Zmień hasło',
    'reset_no_user_exists' => 'Użytkownik o takim emailu nie istnieje. Spróbuj jeszcze raz.',
    'reset_email_body' => 'Witaj %s,

Otrzymałeś ten email ponieważ Ty, lub ktoś inny, wysłał prośbę o zmianę hasła w PHP Censor.

Jeśli to faktycznie Ty, kliknij w następujący link aby zresetować hasło: %ssession/reset-password/%d/%s

Jeśli nie, zignoruj tego emaila i wszystko pozostanie bez zmian,

Pozdrawiamy,

PHP Censor',

    'reset_email_title' => 'Reset Hasła PHP Censor dla %s',
    'reset_invalid' => 'Prośba o zmianę hasła jest nieważna.',
    'email_address' => 'Adres email',
    'login' => 'Login / Email Address',
    'password' => 'Hasło',
    'log_in' => 'Zaloguj się',


    // Top Nav
    'toggle_navigation' => 'Otwórz/zamknij nawigację',
    'n_builds_pending' => '%d budowań w kolejce',
    'n_builds_running' => '%d budowań w toku',
    'edit_profile' => 'Edytuj Profil',
    'sign_out' => 'Wyloguj się',
    'branch_x' => 'Gałąź: %s',
    'created_x' => 'Utworzono: %s',
    'started_x' => 'Rozpoczęto: %s',

    // Sidebar
    'hello_name' => 'Witaj, %s',
    'dashboard' => 'Panel administracyjny',
    'admin_options' => 'Opcje Administratora',
    'add_project' => 'Dodaj Projekt',
    'settings' => 'Ustawienia',
    'manage_users' => 'Zarządaj Uzytkownikami',
    'plugins' => 'Pluginy',
    'view' => 'Podgląd',
    'build_now' => 'Zbuduj',
    'edit_project' => 'Edytuj Projekt',
    'delete_project' => 'Usuń Projekt',

    // Project Summary:
    'no_builds_yet' => 'Brak budowań!',
    'x_of_x_failed' => '%d z ostatnich %d budowań nie powiodło się',
    'x_of_x_failed_short' => '%d / %d nie powiodło się',
    'last_successful_build' => 'Ostatnie budowanie zakończone sukesem odbyło się %s',
    'never_built_successfully' => 'Projekt nie został zbudowany z powodzeniem.',
    'all_builds_passed' => 'Wszystkie z ostatnich %d budowań przeszły.',
    'all_builds_passed_short' => '%d / %d przeszło.',
    'last_failed_build' => 'Ostatnie budowanie zakończone niepowodzeniam było %s.',
    'never_failed_build' => 'Ten projekt nigdy nie zakończył się niepowodzeniem budowania',
    'view_project' => 'Podgląd Projektu',

    // Timeline:
    'latest_builds' => 'Ostatnie Budowania',
    'pending' => 'Oczekujące',
    'running' => 'W toku',
    'success' => 'Sukces',
    'failed' => 'Nieudane',
    'manual_build' => 'Budowanie Manualne',

    // Add/Edit Project:
    'new_project' => 'Nowy Projekt',
    'project_x_not_found' => 'Projekt o ID %d nie istnieje.',
    'project_details' => 'Szczegóły Projektu',
    'public_key_help' => 'Aby łatwiej zacząć, wygenerowaliśmy parę kluczy SSH, które możesz użyć
do tego projektu. Żeby je użyć, wystarczy dodać następujący klucz publiczny do sekcji "wdrożyć klucze"
od wybranego kodu źródłowego platformy hostingowej.',
    'select_repository_type' => 'Wybierz typ repozytorium...',
    'github' => 'GitHub',
    'bitbucket' => 'Bitbucket',
    'gitlab' => 'GitLab',
    'remote' => 'Zdalny URL ',
    'local' => 'Lokalna Ścieżka ',
    'hg'    => 'Mercurial',
    'svn' => 'Subversion',

    'where_hosted' => 'Gdzie hostowany jest Twój projekt?',
    'choose_github' => 'Wybierz repozytorium GitHub:',

    'repo_name' => 'Nazwa repozytorium / URL (Zdalne) lub Ścieżka (Lokalne)',
    'project_title' => 'Tytuł Projektu',
    'project_private_key' => 'Prywanty klucz dostępu do repozytoriów
(pozostaw puste pole dla zdalnych lokalnych i/lub anonimowych)',
    'build_config' => 'PHP Censor zbudowało config dla tego projektu
(jeśli nie możesz dodać pliku .php-censor.yml (.phpci.yml|phpci.yml) do repozytorium projektu)',
    'default_branch' => 'Domyślna nazwa gałęzi',
    'allow_public_status' => 'Włączyć status publiczny dla tego projektu?',
    'archived' => 'W archiwum',
    'archived_menu' => 'W archiwum',
    'save_project' => 'Zachowaj Projekt',

    'error_mercurial' => 'URL repozytorium Mercurialnego powinno zaczynać się od http:// and https://',
    'error_remote' => 'URL repozytorium powinno zaczynać się od git://, http:// lub https://',
    'error_gitlab' => 'Nazwa Repozytorium GitLab powinna być w następującym formacie:  "user@domain.tld:owner/repo.git"',
    'error_github' => 'Nazwa repozytorium powinna być w formacie: "użytkownik/repo"',
    'error_bitbucket' => 'Nazwa repozytorium powinna być w formacie: " użytkownik/repo\'',
    'error_path' => 'Wybrana sieżka nie istnieje',

    // View Project:
    'all_branches' => 'Wszystkie Gałęzie',
    'builds' => 'Budowania',
    'id' => 'ID',
    'date' => 'Data',
    'project' => 'Projekt',
    'commit' => 'Commit',
    'branch' => 'Gałąź',
    'status' => 'Status',
    'prev_link' => '&laquo; Poprzedni',
    'next_link' => 'Następny &raquo;',
    'public_key' => 'Klucz Publiczny',
    'delete_build' => 'Usuń Budowanie',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'Aby automatycznie uruchomić nową budowę po wysłaniu commitów dodaj poniższy adres URL
 jako nowy "WebHook" w sekcji <a href="https://github.com/%s/settings/hooks">Webhooks and Services</a>
 Twojego repozytoria GitLab.',

    'webhooks_help_gitlab' => 'Aby automatycznie uruchomić nową budowę po wysłaniu commitów dodaj poniższy adres URL
 jako "WebHook URL" w sekcji Web Hook Twojego repozytoria GitLab.',

    'webhooks_help_bitbucket' => 'Aby automatycznie uruchomić nową budowę po wysłaniu commitów, dodaj poniższy adres URL
 jako usługę "POST" w sekcji
<a href="https://bitbucket.org/%s/admin/services">
Services</a> repozytoria Bitbucket.',

    // View Build
    'build_x_not_found' => 'Budowanie o ID %d nie istnieje.',
    'build_n' => 'Budowanie %d',
    'rebuild_now' => 'Przebuduj Teraz',


    'committed_by_x' => 'Commitowane przez %s',
    'commit_id_x' => 'Commit: %s',

    'chart_display' => 'Ten wykres wyświetli się po zakończeniu budowy.',

    'build' => 'Budowanie',
    'lines' => 'Linie',
    'comment_lines' => 'Linie Komentarza',
    'noncomment_lines' => 'Linie Bez Komentarza',
    'logical_lines' => 'Lokalne Linie',
    'lines_of_code' => 'Linie Kodu',
    'build_log' => 'Log Budowania',
    'quality_trend' => 'Trend Jakości',
    'codeception_errors' => 'Błędy Codeception',
    'phpmd_warnings' => 'Alerty PHPMD',
    'phpcs_warnings' => 'Alerty PHPCS',
    'phpcs_errors' => 'Błędy PHPCS',
    'phplint_errors' => 'Błędy Lint',
    'phpunit_errors' => 'Błędy PHPUnit',
    'phpdoccheck_warnings' => 'Brakuje sekcji DocBlock',
    'issues' => 'Problemy',

    'codeception' => 'Codeception',
    'phpcpd' => 'PHP Copy/Paste Detector',
    'phpcs' => 'PHP Code Sniffer',
    'phpdoccheck' => 'Brakuje sekcji DocBlock',
    'phpmd' => 'PHP Mess Detector',
    'phpspec' => 'PHPSpec',
    'phpunit' => 'PHPUnit',
    'technical_debt' => 'Dług technologiczny',
    'behat' => 'Behat',

    'file' => 'Plik',
    'line' => 'Linia',
    'class' => 'Klasa',
    'method' => 'Metoda',
    'message' => 'Wiadomość',
    'start' => 'Początek',
    'end' => 'Koniec',
    'from' => 'Od',
    'to' => 'Do',
    'result' => 'Wynik',
    'ok' => 'OK',
    'took_n_seconds' => 'Zajęło %d sekund',
    'build_started' => 'Budowanie Rozpoczęte',
    'build_finished' => 'Budowanie Zakończone',
    'test_message' => 'Wiadomość',
    'test_no_message' => 'Brak wiadomości',
    'test_success' => 'Powodzenie: %d',
    'test_fail' => 'Niepowodzenia: %d',
    'test_skipped' => 'Pominęte: %d',
    'test_error' => 'Błędy: %d',
    'test_todo' => 'Do zrobienia: %d',
    'test_total' => '%d test(ów)',

    // Users
    'name' => 'Nazwa',
    'password_change' => 'Hasło (pozostaw puste jeśli nie chcesz zmienić hasła)',
    'save' => 'Zapisz &raquo;',
    'update_your_details' => 'Aktualizuj swoje dane',
    'your_details_updated' => 'Twoje dane zostały zaktualizowane.',
    'add_user' => 'Dodaj Użytkownika',
    'is_admin' => 'Jest Adminem?',
    'yes' => 'Tak',
    'no' => 'Nie',
    'edit' => 'Edytuj',
    'edit_user' => 'Edytuj Użytkownika',
    'delete_user' => 'Usuń Użytkownika',
    'user_n_not_found' => 'Użytkownik z ID %d nie istnieje.',
    'is_user_admin' => 'Czy użytkownik jest administratorem?',
    'save_user' => 'Zapisz Użytkownika',

    // Settings:
    'settings_saved' => 'Ustawienia zostały zapisane.',
    'settings_check_perms' => 'Twoje ustawienia nie mogły zostać zapisane. Sprawdź uprawnienia do pliku config.yml.',
    'settings_cannot_write' => 'PHP Censor nie może zapisać do pliku config.yml. Dopóty nie będzie można poprawnie zachować ustawie,
dopóki nie będzie to naprawione.',
    'settings_github_linked' => 'Połaczono z Twoim kontem Github',
    'settings_github_not_linked' => 'Nie udało się połaczyć z Twoim kontem Github',
    'build_settings' => 'Ustawienia budowania',
    'github_application' => 'Aplikacja GitHub',
    'github_sign_in' => 'Zanim będzie można zacząć korzystać z GitHub, musisz najpierw  <a href="%s">Sign in</a>, a następnie udzielić dostęp dla PHP Censor do Twojego konta.',
    'github_app_linked' => 'PHP Censor zostało pomyślnie połączone z konten GitHub.',
    'github_where_to_find' => 'Gdzie można znaleźć...',
    'github_where_help' => 'Jeśli to jest Twoja aplikacjia i chcesz jej użyć to więcej informacji znajdziesz w sekcji ustawień:
 <a href="https://github.com/settings/applications">applications</a>',

    'email_settings' => 'Ustawienia Email',
    'email_settings_help' => 'Aby PHP Censor mógł wysyłać emaile z stanem budowy, musisz najpierw skonfigurować poniższe ustawienia SMTP.',

    'application_id' => 'ID Aplikacji',
    'application_secret' => 'Klucz Secret aplikacji',

    'smtp_server' => 'Serwer SMTP',
    'smtp_port' => 'Port SMTP',
    'smtp_username' => 'SMTP Login',
    'smtp_password' => 'Hasło SMTP',
    'from_email_address' => 'E-mail adres Od:',
    'default_notification_address' => 'Domyślny adres email powiadamiania',
    'use_smtp_encryption' => 'Użyć szyfrowane SMTP?',
    'none' => 'Żadne',
    'ssl' => 'SSL',
    'tls' => 'TLS',

    'failed_after' => 'Uznaj, że budowanie nie powiodło się po',
    '5_mins' => '5 Minutach',
    '15_mins' => '15 Minutach',
    '30_mins' => '30 Minutach',
    '1_hour' => '1 Godzinie',
    '3_hours' => '3 Godzinach',

    // Plugins
    'cannot_update_composer' => 'PHP Censor nie może zaktualizować copmposer.json, ponieważ nie ma uprawnień do zapisu.',
    'x_has_been_removed' => 'Usunięto %s. ',
    'x_has_been_added' => 'Dodano %s do composer.json. Zostanie zainstalowane po
wywołaniu polecenia composer update.',
    'enabled_plugins' => 'Aktywne Pluginy',
    'provided_by_package' => 'Dostarczone w pakiecie',
    'installed_packages' => 'Zainstalowane Pakiety',
    'suggested_packages' => 'Sugerowane Pakiety',
    'title' => 'Tytuł',
    'description' => 'Opis',
    'version' => 'Wersja',
    'install' => 'Zainstaluj &raquo;',
    'remove' => 'Usuń &raquo;',
    'search_packagist_for_more' => 'Przeszukaj Packagist po więcej pakietów',
    'search' => 'Szukaj &raquo;',

    // Update
    'update_app' => 'Zaktualizuj bazę danych zgodnie ze zmodyfikowanymi modelami.',
    'updating_app' => 'Aktualizacja bazy danych PHP Censor:',
    'not_installed' => 'Wygląda na to, że PHP Censor nie jest zainstalowane.',
    'install_instead' => 'Proszę zainstalować PHP Censor poprzez php-censor:install',

    // Build Plugins:
    'passing_build' => 'Pomijanie Budowania',
    'failing_build' => 'Niepowodzenie Budowania',
    'log_output' => 'Log Wyjściowy:',
];
