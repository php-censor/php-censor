<?php

return [
    'language_name' => 'Español',
    'language'      => 'Lenguaje',
    'per_page'      => 'Elementos por página',
    'default'       => 'Predeterminada',

    // Log in:
    'log_in_to_app'           => 'Ingresar a PHP Censor',
    'login_error'             => 'Email o contraseña incorrectos',
    'forgotten_password_link' => '¿Olvidaste tu contraseña?',
    'reset_emailed'           => 'Te hemos enviado un email para reiniciar tu contraseña.',
    'reset_header'            => '<strong>¡No te preocupes!</strong><br>Solo tienes que ingresar tu dirección de email
                                  y te enviaremos por email un enlace para reiniciar tu contraseña.',
    'reset_email_address'     => 'Ingresa tu dirección de email:',
    'reset_send_email'        => 'Enviar enlace',
    'reset_enter_password'    => 'Ingresa una nueva contraseña',
    'reset_new_password'      => 'Nueva contraseña:',
    'reset_change_password'   => 'Cambiar contraseña',
    'reset_no_user_exists'    => 'No existe ningún usuario con ese email, por favor intenta  nuevamente.',
    'reset_email_body'        => 'Hola %s,

Has recibido este correo porque tú, o alguien más, ha solicitado reiniciar la contraseña de PHP Censor

Si fuiste tú, por favor haz click en el siguiente enlace para reiniciar tu contraseña: %ssession/reset-password/%d/%s

De lo contrario, por favor ignora este correo y ninguna acción será realizada.

Gracias,

PHP Censor',

    'reset_email_title' => 'Reiniciar contraseña de PHP Censor para %s',
    'reset_invalid'     => 'Pedido inválido.',
    'email_address'     => 'Correo Electronico',
    'login'             => 'Ingresar / Correo Electronico',
    'password'          => 'Contraseña',
    'remember_me'       => 'Recuérdame',
    'log_in'            => 'Ingresar',


    // Top Nav
    'toggle_navigation' => 'Activar navegación',
    'n_builds_pending'  => '%d compilaciones pendientes',
    'n_builds_running'  => '%d compilaciones ejecutándose',
    'edit_profile'      => 'Editar Perfil',
    'sign_out'          => 'Cerrar Sesión',
    'branch_x'          => 'Rama: %s',
    'created_x'         => 'Creada el: %s',
    'started_x'         => 'Comenzó: %s',
    'environment_x'     => 'Entorno: %s',

    // Sidebar
    'hello_name'        => 'Hola, %s',
    'dashboard'         => 'Escritorio',
    'admin_options'     => 'Opciones de Admin.',
    'add_project'       => 'Agregar Proyecto',
    'project_groups'    => 'Grupos de Projectos',
    'settings'          => 'Configuración',
    'manage_users'      => 'Administrar Usuarios',
    'plugins'           => 'Plugins',
    'view'              => 'Vista',
    'build_now'         => 'Ejecutar Compilacion',
    'build_now_debug'   => 'Compilar ahora con depuración',
    'edit_project'      => 'Editar Proyecto',
    'delete_project'    => 'Eliminar Proyecto',
    'delete_old_builds' => 'Eliminar compilaciones viejas',
    'delete_all_builds' => 'Eliminar todas las compilaciones',

    // Project Summary:
    'no_builds_yet'              => '¡No existen builds aún!',
    'x_of_x_failed'              => '%d de los últimos %d builds fallaron.',
    'last_successful_build'      => ' El último build exitoso fue %s.',
    'never_built_successfully'   => ' Este proyecto nunca tuvo un build exitoso.',
    'all_builds_passed'          => 'Todos los últimos %d builds pasaron.',
    'last_failed_build'          => ' El último build en fallar fue %s.',
    'never_failed_build'         => ' Este proyecto no tiene ningún build fallido.',
    'view_project'               => 'Ver Proyecto',
    'projects_with_build_errors' => 'Errores de ejecución',
    'no_build_errors'            => 'Sin errores de ejecución',

    // Timeline:
    'latest_builds'  => 'Últimos builds',
    'pending'        => 'Pediente',
    'running'        => 'Ejecutando',
    'success'        => 'Éxito',
    'failed'         => 'Falló',
    'failed_allowed' => 'Falló (Permitido)',
    'error'          => 'Error',
    'skipped'        => 'Omitido',
    'trace'          => 'Traza de pila',
    'manual_build'   => 'Ejecución Manual',

    // Add/Edit Project:
    'new_project'            => 'Nuevo Proyecto',
    'project_x_not_found'    => 'El Proyecto con ID %d no existe.',
    'project_details'        => 'Detalles del Proyecto',
    'public_key_help'        => 'Para facilitarte, hemos generado un par de llaves SSH para que uses en este proyecto.
    Para usarlo, sólo agrega la siguiente llave pública a la sección de "deploy keys"
                            de tu plataforma de hosting de versionado de código.',
    'select_repository_type' => 'Selecciona tipo de repositorio...',
    'github'                 => 'GitHub',
    'bitbucket'              => 'Bitbucket',
    'gitlab'                 => 'GitLab',
    'git'                    => 'Git',
    'local'                  => 'Path local',
    'hg'                     => 'Mercurial',
    'svn'                    => 'Subversion',

    'where_hosted'  => '¿Dónde está alojado tu proyecto?',
    'choose_github' => 'Selecciona un repositorio de GitHub:',

    'repo_name'              => 'Nombre del repositorio / URL (Remoto) o Ruta (Local)',
    'project_title'          => 'Titulo del proyecto',
    'project_private_key'    => 'Clave privada a usar para acceder al repositorio
                                (dejar en blanco para remotos locales o anónimos)',
    'build_config'           => 'Configuración PHP Censor para builds del proyecto
                                (en caso que no puedas agregar el archivo .php-censor.yml al repositorio)',
    'default_branch'         => 'Nombre de la rama por defecto',
    'default_branch_only'    => 'Solo compilar la rama predeterminada',
    'overwrite_build_config' => '¿Sobrescribir la configuración del archivo en el repositorio por la configuración en la base de datos? Si la casilla de verificación no está marcada, la configuración en la base de datos se fusionará con la configuración del repositorio.',
    'allow_public_status'    => '¿Activar página pública con el estado del proyecto?',
    'archived'               => 'Archivado',
    'archived_menu'          => 'Archivado',
    'save_project'           => 'Guardar Proyecto',
    'environments_label'     => 'Entornos (yaml)',

    'error_hg'        => 'La URL del repositorio de Mercurial debe comenzar con http:// or https://',
    'error_git'       => 'La URL del repositorio debe comenzar con git://, http:// or https://',
    'error_gitlab'    => 'El nombre del repositorio de GitLab debe tener el formato "user@domain.tld:owner/repo.git"',
    'error_github'    => 'El nombre del repositorio debe tener el formato "owner/repo"',
    'error_bitbucket' => 'El nombre del repo debe tener el formato "owner/repo"',
    'error_path'      => 'La ruta especificada no existe.',

    // View Project:
    'all_branches' => 'Todas las ramas',
    'all'          => 'Todas',
    'builds'       => 'Compilaciones',
    'id'           => 'ID',
    'date'         => 'Fecha',
    'project'      => 'Proyecto',
    'commit'       => 'Confirmaciónn',
    'branch'       => 'Rama',
    'environment'  => 'Entorno',
    'status'       => 'Estado',
    'prev_link'    => '&laquo; Anterior',
    'next_link'    => 'Siguiente &raquo;',
    'public_key'   => 'Llave pública',
    'delete_build' => 'Eliminar Compilacion',
    'build_source' => 'Fuente de compilacion',

    'source_unknown'                       => 'Desconocido',
    'source_manual_web'                    => 'Manual (de la Web)',
    'source_manual_console'                => 'Manual (de Consola)',
    'source_manual_rebuild_web'            => 'Reejecución de %s (de la Web)',
    'source_manual_rebuild_console'        => 'Reejecución de %s (de Consola)',
    'source_periodical'                    => 'Programado',
    'source_webhook_push'                  => 'Webhook (Subido)',
    'source_webhook_pull_request_created'  => 'Webhook (Solicitud extracción creada)',
    'source_webhook_pull_request_updated'  => 'Webhook (Solicitud extracción actualizada)',
    'source_webhook_pull_request_approved' => 'Webhook (Solicitud extracción aprobada)',
    'source_webhook_pull_request_merged'   => 'Webhook (Solicitud extracción fusionada)',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'Para compilar automáticamente este proyecto cada vez que se realiza un commit, agreagar la siguiente URL
                                como un nuevo "webhook" en la sección <a href="https://github.com/%s/settings/hooks">Webhooks
                                and Services</a>  de tu repositorio en GitHub.',

    'webhooks_help_gitlab' => 'Para compilar automáticamente este proyecto, cada vez que se realiza un commit, agreagar la siguiente URL
                                como una "WebHook URL" en la sección "web hooks" de tu repositorio en GitLab.',

    'webhooks_help_gogs' => 'Para compilar automáticamente este proyecto, cada vez que se realiza un commit, agreagar la siguiente URL
                                como una "WebHook URL" en la sección "web hooks" de tu repositorio en Gogs.',

    'webhooks_help_bitbucket' => 'Para compilar automáticamente este proyecto, cada vez que se realiza un commit, agreagar la siguiente URL
                                como un servicio "POST" en la sección
                                <a href="https://bitbucket.org/%s/admin/services">
                                Services</a> de tu repositorio en Bitbucket.',

    // Project Groups
    'group_projects' => 'Grupos de proyectos',
    'project_group'  => 'Grupo de proyecto',
    'group_count'    => 'Numero de proyectos',
    'group_edit'     => 'Editar',
    'group_delete'   => 'Eliminar',
    'group_add'      => 'Añadir Grupo',
    'group_add_edit' => 'Añadir / Eliminar Grupo',
    'group_title'    => 'Título del grupo',
    'group_save'     => 'Guardar grupo',

    // View Build
    'errors'            => 'Errores',
    'information'       => 'Información',
    'is_new'            => 'Es nuevo?',
    'new'               => 'Nuevo',
    'build_x_not_found' => 'El build con ID %d no existe.',
    'build_n'           => 'Compilacion %d',
    'rebuild_now'       => 'Rebuild Ahora',
    'rebuild_now_debug' => 'Reejecucar ahora con depuración',

    'all_errors'   => 'Todos los errores',
    'only_new'     => 'Solo errors nuevos',
    'only_old'     => 'Solo errors viejos',
    'new_errors'   => 'Nuevos errores',
    'total_errors' => 'Errores',

    'committed_by_x' => 'Confirmación hecha por %s',
    'commit_id_x' => 'Confirmación: %s',

    'chart_display' => 'Este gráfico será mostrado una vez que el build se haya completado.',

    'build' => 'Build',
    'lines' => 'Líneas',
    'classes' => 'Clases',
    'methods' => 'Métodos',
    'comment_lines' => 'Líneas de comentario',
    'noncomment_lines' => 'Líneas no comentario',
    'logical_lines' => 'Líneas lógicas',
    'lines_of_code' => 'Líneas de código',
    'coverage' => 'Cobertura de código de PHPUnit',
    'build_log' => 'Registro',
    'quality_trend' => 'Tendencia de calidad',
    'codeception_errors' => 'Errores de Codeception',
    'php_docblock_checker_warnings' => 'Docblocks faltantes',
    'php_tal_lint_warnings' => 'Advertencias de PHP Tal Lint',
    'php_tal_lint_errors' => 'Errores de PHP Tal Lint',
    'behat_warnings' => 'Advertencias de Behat',
    'sensiolabs_insight_warnings' => 'Advertencias de Sensiolabs Insight',
    'technical_debt_warnings' => 'Advertencias de deudas técnicas',
    'issues' => 'Incidencias',
    'merged_branches' => 'Ramas fusionadas',

    'codeception_feature' => 'Característica',
    'codeception_suite' => 'Suite',
    'codeception_time' => 'Tiempo',
    'codeception_synopsis' => '<strong>%1$d</strong> pruebas ejecutada en <strong>%2$f</strong> segundos.
                               <strong>%3$d</strong> fallaron.',

    'php_cpd' => 'PHP Copy/Paste Detector',
    'php_code_sniffer' => 'PHP Code Sniffer',
    'php_docblock_checker' => 'Missing Docblocks',
    'php_mess_detector' => 'PHP Mess Detector',
    'php_unit' => 'PHP Unit',

    'file'            => 'Archivo',
    'line'            => 'Línea',
    'class'           => 'Clase',
    'method'          => 'Método',
    'message'         => 'Mensaje',
    'start'           => 'Inicio',
    'end'             => 'Fin',
    'from'            => 'De',
    'to'              => 'Para',
    'suite'           => 'Suite',
    'test'            => 'Test',
    'result'          => 'Resultado',
    'ok'              => 'OK',
    'took_n_seconds'  => 'Tomó %d segundos',
    'build_started'   => 'Build Comenzado',
    'build_finished'  => 'Build Terminado',
    'test_message'    => 'Mensaje',
    'test_no_message' => 'Sin mensaje',
    'test_success'    => 'Exitoso: %d',
    'test_fail'       => 'Fallido: %d',
    'test_skipped'    => 'Omitido: %d',
    'test_error'      => 'Errores: %d',
    'test_todo'       => 'Que haceres: %d',
    'test_total'      => '%d prueba(s)',

    // Users
    'name'                 => 'Nombre',
    'password_change'      => 'Contraseña (dejar en blanco si no quiere cambiarla)',
    'save'                 => 'Guardar &raquo;',
    'update_your_details'  => 'Actualizar los detalles',
    'your_details_updated' => 'Tus detalles han sido actualizados.',
    'add_user'             => 'Agregar Usuario',
    'is_admin'             => '¿Es Admin?',
    'yes'                  => 'Si',
    'no'                   => 'No',
    'edit'                 => 'Editar',
    'edit_user'            => 'Editar Usuario',
    'delete_user'          => 'Delete Usuario',
    'user_n_not_found'     => 'Usuario con ID %d no existe.',
    'is_user_admin'        => '¿Es un usuario administrador?',
    'save_user'            => 'Guardar Usuario',

    // Settings:
    'settings_saved'             => 'Tu configuración ha sido guardada.',
    'settings_check_perms'       => 'Tu configuración no fue guardada, verificar los permisos del archivo config.yml.',
    'settings_cannot_write'      => 'PHP Censor no puede escribir en el archivo config.yml file, la configuración no será guardada correctamente
                                hasta no corregir esto.',
    'settings_github_linked'     => 'Tu cuenta GitHub ha sido conectada.',
    'settings_github_not_linked' => 'No se pudo conectar a tu cuenta GitHub.',
    'build_settings'             => 'Configuración del Build ',
    'github_application'         => 'Aplicación GitHub',
    'github_sign_in'             => 'Antes de comenzar a utilizar GitHub, tienes que <a href="%s">ingresar</a> y permitir
                            el acceso a tu cuenta a PHP Censor.',
    'github_app_linked'          => 'PHP Censor ha sido conectado a tu cuenta GitHub.',
    'github_where_to_find'       => 'Donde encontrar estos...',
    'github_where_help'          => 'Si eres priopietario de la aplicaión que quieres usar, puedes encontrar esta información en
                            el área de configuración de <a href="https://github.com/settings/applications">aplicaciones</a>.',

    'email_settings'               => 'Configuraciones de Email',
    'email_settings_help'          => 'Para que PHP Censor pueda enviar email con el status de los builds,
                                       debes configurar las siguientes propiedades SMTP.',
    'application_id'               => 'ID de aplicación',
    'application_secret'           => 'Secreto de Aplicación',
    'smtp_server'                  => 'Servidor SMTP',
    'smtp_port'                    => 'Puerto SMTP',
    'smtp_username'                => 'Usuario SMTP',
    'smtp_password'                => 'Contraseña SMTP',
    'from_email_address'           => 'Dirección de email DE',
    'default_notification_address' => 'Dirección de correo de notificación por defecto',
    'use_smtp_encryption'          => 'Usar encriptación SMTP?',
    'none'                         => 'Ninguna',
    'ssl'                          => 'SSL',
    'tls'                          => 'TLS',

    'failed_after' => 'Considerar el build como fallido luego de ',
    '5_mins'       => '5 Minutos',
    '15_mins'      => '15 Minutos',
    '30_mins'      => '30 Minutos',
    '1_hour'       => '1 Hora',
    '3_hours'      => '3 Horas',

    // Plugins
    'cannot_update_composer'    => 'PHP Censor no puede actualizar composer.json porque no tiene permisos de escritura.',
    'x_has_been_removed'        => '%s ha sido elimiando.',
    'x_has_been_added'          => '%s ha sido agregado a composer.json y será instalado la próxima vez que ejecutes composer update.',
    'enabled_plugins'           => 'Activar Plugins',
    'provided_by_package'       => 'Provisto por Paquete',
    'installed_packages'        => 'Paquetes Instalados',
    'suggested_packages'        => 'Paquetes Sugeridos',
    'title'                     => 'Título',
    'description'               => 'Descripción',
    'version'                   => 'Versión',
    'install'                   => 'Instalar &raquo;',
    'remove'                    => 'Eliminar &raquo;',
    'search_packagist_for_more' => 'Buscar más paquetes en Packagist',
    'search'                    => 'Buscar &raquo;',

    // Summary plugin
    'build-summary'  => 'Resumen',
    'stage'          => 'Etapa',
    'duration'       => 'Duración',
    'seconds'        => 'segundos',
    'plugin'         => 'Plugin',
    'stage_setup'    => 'Preparación',
    'stage_test'     => 'Prueba',
    'stage_deploy'   => 'Desplegué',
    'stage_complete' => 'Completo',
    'stage_success'  => 'Éxito',
    'stage_failure'  => 'Fallido',
    'stage_broken'   => 'Roto',
    'stage_fixed'    => 'Arreglado',
    'severity'       => 'Gravedad',

    'all_plugins'     => 'Todos los plugins',
    'all_severities'  => 'Todas gravedades',
    'filters'         => 'Filtros',
    'errors_selected' => 'Errores seleccionados',

    'build_details'  => 'Detalles de ejecución',
    'commit_details' => 'Detalles de confirmación',
    'committer'      => 'Autor de confirmación',
    'commit_message' => 'Mensaje de confirmación',
    'timing'         => 'Sincronización',
    'created'        => 'Creado',
    'started'        => 'Empezado',
    'finished'       => 'Terminado',

    // Update
    'update_app' => 'Actuliza la base de datos para reflejar los modelos actualizados.',
    'updating_app' => 'Actualizando base de datos PHP Censor: ',
    'not_installed' => 'PHP Censor no está instalado.',
    'install_instead' => 'Por favor, instala PHP Censor via php-censor:install.',

    // Create Build Command
    'add_to_queue_failed' => 'La ejecución fue creada exitosamente, pero no se pudo agregar a la cola de ejecución.
                              Esto suele pasar cuando PHP Censor es configurado para usar un servidor de beanstalkd que
                              no existe, o su servidor de beanstalkd no esta corriendo.',

    // Build Plugins:
    'passing_build' => 'Compilacion Exitosa',
    'failing_build' => 'Compilacion Fallida',
    'log_output'    => 'Log de Salida: ',

    // Error Levels:
    'critical' => 'Crítico',
    'high' => 'Alto',
    'normal' => 'Normal',
    'low' => 'Bajo',

    'php_mess_detector_warnings' => 'Advertencias de PHPMD',
    'php_code_sniffer_warnings'  => 'Advertencias de PHPCS',
    'php_code_sniffer_errors'    => 'Errores de PHPCS',
    'phan_warnings'              => 'Advertencias de Phan',
    'php_cs_fixer_warnings'      => 'Advertencias de PHP CS Fixer',
    'php_parallel_lint_errors'   => 'Errores de Lint',
    'php_unit_errors'            => 'Errores de PHPUnit',
    'php_cpd_warnings'           => 'Advertencias de PHP Copy/Paste Detector',

    // Plugins that generate errors:
    'composer'             => 'Composer',
    'php_loc'              => 'PHP LOC',
    'php_parallel_lint'    => 'PHP Parallel Lint',
    'email'                => 'Correo Electrónico',
    'atoum'                => 'Atoum',
    'behat'                => 'Behat',
    'campfire'             => 'Campfire',
    'clean_build'          => 'Limpiar Compilacion',
    'codeception'          => 'Codeception',
    'copy_build'           => 'Copiar Compilacion',
    'deployer'             => 'Deployer',
    'env'                  => 'Env',
    'grunt'                => 'Grunt',
    'hipchat_notify'       => 'Hipchat Notify',
    'irc'                  => 'IRC',
    'lint'                 => 'Lint',
    'mysql'                => 'MySQL',
    'package_build'        => 'Compilacion de paquete',
    'pdepend'              => 'PDepend',
    'pgsql'                => 'PostgreSQL',
    'phan'                 => 'Phan',
    'phar'                 => 'Phar',
    'phing'                => 'Phing',
    'php_cs_fixer'         => 'PHP Coding Standards Fixer',
    'php_spec'             => 'PHP Spec',
    'shell'                => 'Shell',
    'slack_notify'         => 'Slack Notify',
    'technical_debt'       => 'Deuda Técnica',
    'xmpp'                 => 'XMPP',
    'security_checker'     => 'SensioLabs Security Checker',

    'confirm_message' => 'El elemento se eliminará de forma permanente. ¿Estás seguro?',
    'confirm_title'   => 'Confirmación de eliminación de elemento',
    'confirm_ok'      => 'Eliminar',
    'confirm_cancel'  => 'Cancelar',
    'confirm_success' => 'El elemento fue eliminado correctamente.',
    'confirm_failed'  => 'Eliminar el elemento ah fallado! El servidor dice: ',

    'public_status_title' => 'Estado público',
    'public_status_image' => 'Imagen de estado',
    'public_status_page'  => 'Pagina de estado público',
];
