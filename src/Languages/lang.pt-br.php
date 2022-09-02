<?php

return [
    'language_name' => 'Português do Brasil',
    'language'      => 'Idioma',
    'per_page'      => 'Itens por página',
    'default'       => 'Padrão',

    // Log in:
    'log_in_to_app'           => 'Acessar o PHP Censor',
    'login_error'             => 'E-mail ou senha incorretos',
    'forgotten_password_link' => 'Esqueceu sua senha?',
    'reset_emailed'           => 'Nós enviamos um e-mail com um link para redefinir a sua senha.',
    'reset_header'            => '<strong>Não se preocupe!</strong><br>Basta digitar o seu endereço de e-mail abaixo e
                                nós lhe enviaremos um e-mail com um link para redefinir sua senha.',
    'reset_email_address'     => 'Digite seu endereço de e-mail:',
    'reset_send_email'        => 'Solicitar nova senha',
    'reset_enter_password'    => 'Por favor, digite uma nova senha',
    'reset_new_password'      => 'Nova senha:',
    'reset_change_password'   => 'Alterar senha',
    'reset_no_user_exists'    => 'Nenhum usuário existe com esse endereço de e-mail, por favor, tente novamente.',
    'reset_email_body'        => 'Olá %s,

Você recebeu este e-mail porque você, ou alguém, pediu a redefinição da sua senha do PHP Censor.

Se foi você, por favor clique no link a seguir para redefinir a sua senha: %ssession/reset-password/%d/%s

Caso contrário, por favor ignore este e-mail e nenhuma alteração irá ocorrer.

Obrigado,

PHP Censor',

    'reset_email_title' => 'PHP Censor Redefinição de Senha para %s',
    'reset_invalid'     => 'Requisição inválida para redefinição de senha.',
    'email_address'     => 'Endereço de e-mail',
    'login'             => 'Login / Endereço de e-mail',
    'password'          => 'Senha',
    'remember_me'       => 'Lembrar de mim',
    'log_in'            => 'Acessar',


    // Top Nav
    'toggle_navigation' => 'Alternar Navegação',
    'n_builds_pending'  => '%d compilações pendentes',
    'n_builds_running'  => '%d compilações sendo executadas',
    'edit_profile'      => 'Editar Perfil',
    'sign_out'          => 'Sair',
    'branch_x'          => 'Branch: %s',
    'created_x'         => 'Criado: %s',
    'started_x'         => 'Iniciado: %s',
    'environment_x'     => 'Ambiente: %s',

    // Sidebar
    'hello_name'        => 'Olá, %s',
    'dashboard'         => 'Painel',
    'admin_options'     => 'Opções do Admin',
    'add_project'       => 'Adicionar Projeto',
    'project_groups'    => 'Grupos de Projetos',
    'settings'          => 'Configurações',
    'manage_users'      => 'Gerenciar Usuários',
    'plugins'           => 'Plugins',
    'view'              => 'View',
    'build_now'         => 'Compilar Agora',
    'build_now_debug'   => 'Compilar Agora com Depurador',
    'edit_project'      => 'Editar Projeto',
    'delete_project'    => 'Remover Projeto',
    'delete_old_builds' => 'Remover compilações antigas',
    'delete_all_builds' => 'Remover todas as compilações',

    // Project Summary:
    'no_builds_yet'            => 'Nenhuma compilação ainda!',
    'x_of_x_failed'            => '%d das últimas %d compilações falharam.',
    'last_successful_build'    => ' A última compilação bem-sucedida foi %s.',
    'never_built_successfully' => ' Este projeto nunca teve uma compilação bem-sucedida.',
    'all_builds_passed'        => 'Todas as últimas %d compilações passaram.',
    'last_failed_build'        => ' A última compilação malsucedida foi %s.',
    'never_failed_build'       => ' Este projeto nunca teve uma compilação malsucedida.',
    'view_project'             => 'Ver projeto',

    // Timeline:
    'latest_builds'  => 'Últimas compilações',
    'pending'        => 'Pendente',
    'running'        => 'Executando',
    'success'        => 'Sucesso',
    'failed'         => 'Malsucedida',
    'failed_allowed' => 'Malsucedida (Permitido)',
    'error'          => 'Erro',
    'skipped'        => 'Ignorado',
    'trace'          => 'Stack trace',
    'manual_build'   => 'Compilação manual',

    // Add/Edit Project:
    'new_project'            => 'Novo Projeto',
    'project_x_not_found'    => 'Projeto com ID %d não existe.',
    'project_details'        => 'Detalhes do Projeto',
    'public_key_help'        => 'Para tornar mais fácil começar, geramos um par de chaves SSH para você usar neste
                            projeto. Para usá-lo, basta adicionar a seguinte chave pública na seção "deploy keys" no
                            provedor onde hospeda seu código.',
    'select_repository_type' => 'Selecione o tipo de repositório...',
    'github'                 => 'GitHub',
    'bitbucket'              => 'Bitbucket',
    'gitlab'                 => 'GitLab',
    'git'                    => 'Git',
    'local'                  => 'Diretório Local',
    'hg'                     => 'Mercurial',
    'svn'                    => 'Subversion',

    'where_hosted'  => 'Onde seu projeto está hospedado?',
    'choose_github' => 'Escolha um repositório do GitHub:',

    'repo_name'              => 'Nome do repositório / URL (Remota) ou Caminho (Local)',
    'project_title'          => 'Titulo do projeto',
    'project_private_key'    => 'Chave privada usada para acessar o repositório
                                (Deixe em branco para controles remotos locais e/ou anônimos)',
    'build_config'           => 'PHP Censor construir configuração para este projeto
                                (caso você não possa adicionar um arquivo .php-censor.yml ao seu repositório)',
    'default_branch'         => 'Nome da branch padrão',
    'default_branch_only'    => 'Compilar apenas a branch padrão',
    'overwrite_build_config' => 'Sobrescrever arquivo de configuração do repositório com as configurações do banco de
                            dados? Se a checkbox não exister marcada, então as configurações do banco de dados serão
                            incorporadas as configurações do arquivo.',
    'allow_public_status'    => 'Habilitar página de status pública e imagem para este projeto?',
    'archived'               => 'Arquivado',
    'archived_menu'          => 'Arquivado',
    'save_project'           => 'Salvar Projeto',
    'environments_label'     => 'Ambientes (yaml)',

    'error_hg'        => 'URL para repositórios Mercurial deve começar com http:// ou https://',
    'error_git'       => 'URL para repositórios GIT deve começar com git://, http:// ou https://',
    'error_gitlab'    => 'Nome para repositórios GitLab devem seguir o formato
                    "usuario@dominio.tld:dono/repositorio.git"',
    'error_github'    => 'Nome para repositórios GitHub devem seguir o formato "dono/repositorio"',
    'error_bitbucket' => 'Nome para repositórios Bitbucket devem seguir o formato "dono/repositorio"',
    'error_path'      => 'O caminho de diretório informado não existe.',

    // View Project:
    'all_branches' => 'Todas as Branches',
    'all'          => 'Tudo',
    'builds'       => 'Compilações',
    'id'           => 'ID',
    'date'         => 'Data',
    'project'      => 'Projeto',
    'commit'       => 'Commit',
    'branch'       => 'Branch',
    'environment'  => 'Ambiente',
    'status'       => 'Estado',
    'prev_link'    => '&laquo; Anterior',
    'next_link'    => 'Próximo &raquo;',
    'public_key'   => 'Chave Pública',
    'delete_build' => 'Apagar Compilação',
    'build_source' => 'Compilar o código',

    'source_unknown'                       => 'Desconhecido',
    'source_manual_web'                    => 'Manual (a partir da Web)',
    'source_manual_console'                => 'Manual (a partir da linha de comando)',
    'source_manual_rebuild_web'            => 'Recompilar para %s (a partir da Web)',
    'source_manual_rebuild_console'        => 'Recompilar para %s (a partir da linha de comando)',
    'source_periodical'                    => 'Periódico',
    'source_webhook_push'                  => 'Webhook (Push)',
    'source_webhook_pull_request_created'  => 'Webhook (PR Criado)',
    'source_webhook_pull_request_updated'  => 'Webhook (PR Atualizado)',
    'source_webhook_pull_request_approved' => 'Webhook (PR Aprovado)',
    'source_webhook_pull_request_merged'   => 'Webhook (PR Incorporado)',

    'webhooks'                => 'Webhooks',
    'webhooks_help_github'    => 'Para compilar este projeto automaticamente quando novas alterações são feitas,
                                adicione a URL abaixo como um novo "Webhook" na seção
                                <a href="https://github.com/%s/settings/hooks">Webhooks e Serviços</a> do seu
                                repositório no GitHub.',

    'webhooks_help_gitlab'    => 'Para compilar este projeto automaticamente quando novas alterações são feitas,
                                adicione a URL abaixo como um novo "WebHook URL" na seção Web Hooks do seu repositório
                                no GitLab.',

    'webhooks_help_gogs'      => 'Para compilar este projeto automaticamente quando novas alterações são feitas,
                                adicione a URL abaixo como um novo "WebHook URL" na seção Web Hooks do seu repositório
                                no Gogs.',

    'webhooks_help_bitbucket' => 'Para compilar este projeto automaticamente quando novas alterações são feitas,
                                adicione a URL abaixo como um novo serviço "POST" na seção
                                <a href="https://bitbucket.org/%s/admin/services">Serviços</a> do seu repositório no
                                Bitbucket.',

    // Project Groups
    'group_projects' => 'Grupos de Projetos',
    'project_group'  => 'Grupo de Projetos',
    'group_count'    => 'Quantidade de Projetos',
    'group_edit'     => 'Editar',
    'group_delete'   => 'Remover',
    'group_add'      => 'Adicionar Grupo',
    'group_add_edit' => 'Adicionar / Editar Grupo',
    'group_title'    => 'Título do Grupo',
    'group_save'     => 'Salvar Grupo',

    // View Build
    'errors'            => 'Erros',
    'information'       => 'Informação',
    'is_new'            => 'É novo?',
    'new'               => 'Novo',
    'build_x_not_found' => 'Compilação com ID %d não existe.',
    'build_n'           => 'Compilação %d',
    'rebuild_now'       => 'Recompilar Agora',
    'rebuild_now_debug' => 'Recompilar Agora com Depurador',

    'all_errors'   => 'Todos os erros',
    'only_new'     => 'Apenas erros novos',
    'only_old'     => 'Apenas erros antigos',
    'new_errors'   => 'Novos erros',
    'total_errors' => 'Erros',

    'committed_by_x' => 'Enviado por %s',
    'commit_id_x'    => 'Commit: %s',

    'chart_display' => 'Este gráfico será exibido uma vez que a compilação esteja completa.',

    'build'                         => 'Compilação',
    'lines'                         => 'Linhas',
    'classes'                       => 'Classes',
    'methods'                       => 'Métodos',
    'comment_lines'                 => 'Linhas de Comentário',
    'noncomment_lines'              => 'Linhas de Não-Comentário',
    'logical_lines'                 => 'Linhas de Lógica',
    'lines_of_code'                 => 'Linhas de Código',
    'coverage'                      => 'PHPUnit Cobertura de Código',
    'build_log'                     => 'Log de Compilação',
    'quality_trend'                 => 'Tendência de Qualidade',
    'codeception_errors'            => 'Erros do Codeception',
    'php_mess_detector_warnings'    => 'Alertas do PHPMD',
    'php_code_sniffer_warnings'     => 'Alertas do PHPCS',
    'php_code_sniffer_errors'       => 'Erros do PHPCS',
    'phan_warnings'                 => 'Alertas do Phan',
    'php_cs_fixer_warnings'         => 'Alertas do PHP CS Fixer',
    'php_parallel_lint_errors'      => 'Erros de Lint',
    'php_unit_errors'               => 'Erros do PHPUnit',
    'php_cpd_warnings'              => 'Alertas do Detector de Cópia/Cola',
    'php_docblock_checker_warnings' => 'Docblocks faltando',
    'php_tal_lint_warnings'         => 'Alertas do PHP Tal Lint',
    'php_tal_lint_errors'           => 'Erros do PHP Tal Lint',
    'behat_warnings'                => 'Alertas do Behat',
    'sensiolabs_insight_warnings'   => 'Alertas do Sensiolabs Insight',
    'technical_debt_warnings'       => 'Alertas de Débito Técnico',
    'issues'                        => 'Problemas',
    'merged_branches'               => 'Branches mescladas',

    'codeception_feature'  => 'Feature',
    'codeception_suite'    => 'Suite',
    'codeception_time'     => 'Tempo',
    'codeception_synopsis' => '<strong>%1$d</strong> testes executados em <strong>%2$f</strong> segundos.
                               <strong>%3$d</strong> falhas.',

    'suite'           => 'Suíte',
    'test'            => 'Teste',
    'file'            => 'Arquivo',
    'line'            => 'Linha',
    'class'           => 'Classe',
    'method'          => 'Método',
    'message'         => 'Mensagem',
    'start'           => 'Início',
    'end'             => 'Fim',
    'from'            => 'De',
    'to'              => 'Para',
    'result'          => 'Resultado',
    'ok'              => 'OK',
    'took_n_seconds'  => 'Levou %d segundos',
    'build_started'   => 'Compilação Iniciada',
    'build_finished'  => 'Compilação Concluída',
    'test_message'    => 'Mensagem',
    'test_no_message' => 'Nenhuma mensagem',
    'test_success'    => 'Bem-sucedidos: %d',
    'test_fail'       => 'Falhas: %d',
    'test_skipped'    => 'Ignorados: %d',
    'test_error'      => 'Erros: %d',
    'test_todo'       => 'Todos: %d',
    'test_total'      => '%d teste(s)',

    // Users
    'name'                 => 'Nome',
    'password_change'      => 'Senha (deixe vazio se não quiser alterar)',
    'save'                 => 'Salvar &raquo;',
    'update_your_details'  => 'Atualize seus detalhes',
    'your_details_updated' => 'Seus detalhes foram atualizados.',
    'add_user'             => 'Adicionar Usuário',
    'is_admin'             => 'É Administrador?',
    'yes'                  => 'Sim',
    'no'                   => 'Não',
    'edit'                 => 'Editar.',
    'edit_user'            => 'Editar Usuário',
    'delete_user'          => 'Remover Usuário',
    'user_n_not_found'     => 'Usuário com ID %d não existe.',
    'is_user_admin'        => 'Este usuário é um administrador?',
    'save_user'            => 'Salvar Usuário',

    // Settings:
    'settings_saved'             => 'Suas configurações foram salvas.',
    'settings_check_perms'       => 'Suas configurações não puderam ser salvas, verifique as permissões do seu arquivo
                                config.yml.',
    'settings_cannot_write'      => 'PHP Censor não pôde escrever o arquivo config.yml, as configurações não serão
                                salvas corretamente  até que isso seja resolvido.',
    'settings_github_linked'     => 'Sua conta do GitHub foi vinculada.',
    'settings_github_not_linked' => 'Sua conta do GitHub não pôde ser vinculada.',
    'build_settings'             => 'Configurações de Compilação',
    'github_application'         => 'Aplicação do GitHub',
    'github_sign_in'             => 'Antes de começar a usar o GitHub, você precisa <a href="%s">se autenticar</a> e
                                permitir que o PHP Censor acesse a sua conta.',
    'github_linked'              => 'PHP Censor foi vinculado à sua conta do GitHub.',
    'github_where_to_find'       => 'Onde encontrar...',
    'github_where_help'          => 'Se você é dono da aplicação que você gostaria de usar, você pode encontrar essa
                                informação na <a href="https://github.com/settings/applications">área de configurações
                                da sua aplicação</a>.',

    'email_settings'      => 'Configurações de E-mail',
    'email_settings_help' => 'Antes que o PHP Censor possa enviar e-mails com atualizações de estado, você deve
                            configurar os dados do seu SMTP abaixo.',

    'application_id'     => 'ID da Aplicação',
    'application_secret' => 'Segredo da Aplicação',

    'smtp_server'                  => 'Servidor SMTP',
    'smtp_port'                    => 'Porta SMTP',
    'smtp_username'                => 'Usuário SMTP',
    'smtp_password'                => 'Senha SMTP',
    'from_email_address'           => 'Endereço de E-mail',
    'default_notification_address' => 'Endereço de E-mail Padrão para Notificações',
    'use_smtp_encryption'          => 'Usar criptografia SMTP?',
    'none'                         => 'Nenhum',
    'ssl'                          => 'SSL',
    'tls'                          => 'TLS',

    'failed_after' => 'Considere uma compilação como malsucedida após',
    '5_mins'       => '5 Minutos',
    '15_mins'      => '15 Minutos',
    '30_mins'      => '30 Minutos',
    '1_hour'       => '1 Hora',
    '3_hours'      => '3 Horas',

    // Plugins
    'cannot_update_composer'    => 'PHP Censor não pôde atualizar o composer.json por você pois o arquivo não é
                                gravável.',
    'x_has_been_removed'        => '%s foi removido.',
    'x_has_been_added'          => '%s foi adicionado ao composer.json por você e será instalado da próxima vez que você
                            executar composer update.',
    'enabled_plugins'           => 'Plugins Habilitados',
    'provided_by_package'       => 'Fornecido por Pacote',
    'installed_packages'        => 'Pacotes instalados',
    'suggested_packages'        => 'Pacotes Sugeridos',
    'title'                     => 'Título',
    'description'               => 'Descrição',
    'version'                   => 'Versão',
    'install'                   => 'Instalar &raquo;',
    'remove'                    => 'Remover &raquo;',
    'search_packagist_for_more' => 'Buscar mais pacotes no Packagist',
    'search'                    => 'Buscar &raquo;',

    // Summary plugin
    'build-summary'  => 'Resumo',
    'stage'          => 'Estágio',
    'duration'       => 'Duração',
    'seconds'        => 'seg.',
    'plugin'         => 'Plugin',
    'stage_setup'    => 'Configuração',
    'stage_test'     => 'Teste',
    'stage_deploy'   => 'Deploy',
    'stage_complete' => 'Completo',
    'stage_success'  => 'Sucesso',
    'stage_failure'  => 'Falhou',
    'stage_broken'   => 'Quebrado',
    'stage_fixed'    => 'Resolvido',
    'severity'       => 'Severidade',

    'all_plugins'     => 'Todos os plugins',
    'all_severities'  => 'Todas as severidades',
    'filters'         => 'Filtros',
    'errors_selected' => 'Erros selecionados',

    'build_details'  => 'Detalhes da Compilação',
    'commit_details' => 'Detalhes do Commit',
    'committer'      => 'Autor do Commit',
    'commit_message' => 'Mensagem de Commit',
    'timing'         => 'Timing',
    'created'        => 'Created',
    'started'        => 'Started',
    'finished'       => 'Finished',

    // Update
    'update_app'      => 'Atualize o banco de dados para refletir os modelos alterados.',
    'updating_app'    => 'Atualizando o banco de dados do PHP Censor: ',
    'not_installed'   => 'PHP Censor não aparenta estar instalado.',
    'install_instead' => 'Por favor, instale o PHP Censor usando php-censor:install.',

    // Create Build Command
    'add_to_queue_failed' => 'Compilação criada com sucesso, mas não foi possível adicionar a fila de compilação. Isso
                            normalmente ocorre quando o PHP Censor está configurado para usar um servidor beanstalkd
                            que não existe ou o seu servidor beanstalkd está parado.',

    // Build Plugins:
    'passing_build' => 'Compilação Bem-sucedida',
    'failing_build' => 'Compilação Malsucedida',
    'log_output'    => 'Saída do Log: ',

    // Error Levels:
    'critical' => 'Crítico',
    'high'     => 'Alto',
    'normal'   => 'Normal',
    'low'      => 'Baixo',

    // Plugins that generate errors:
    'php_mess_detector'    => 'PHP Mess Detector',
    'php_code_sniffer'     => 'PHP Code Sniffer',
    'php_unit'             => 'PHP Unit',
    'php_cpd'              => 'PHP Copy/Paste Detector',
    'php_docblock_checker' => 'PHP Docblock Checker',
    'composer'             => 'Composer',
    'php_loc'              => 'PHP LOC',
    'php_parallel_lint'    => 'PHP Parallel Lint',
    'email'                => 'E-mail',
    'atoum'                => 'Atoum',
    'behat'                => 'Behat',
    'campfire'             => 'Campfire',
    'clean_build'          => 'Compilação Limpa',
    'codeception'          => 'Codeception',
    'copy_build'           => 'Copiar Compilação',
    'deployer'             => 'Deployer',
    'env'                  => 'Env',
    'grunt'                => 'Grunt',
    'irc'                  => 'IRC',
    'lint'                 => 'Lint',
    'mysql'                => 'MySQL',
    'package_build'        => 'Compilar Pacote',
    'pdepend'              => 'PDepend',
    'pgsql'                => 'PostgreSQL',
    'phan'                 => 'Phan',
    'phar'                 => 'Phar',
    'phing'                => 'Phing',
    'php_cs_fixer'         => 'PHP Coding Standards Fixer',
    'php_spec'             => 'PHP Spec',
    'shell'                => 'Shell',
    'slack_notify'         => 'Slack Notify',
    'technical_debt'       => 'Débito Técnico',
    'xmpp'                 => 'XMPP',
    'security_checker'     => 'SensioLabs Security Checker',

    'confirm_message' => 'O item será removido permanentemente. Você tem certeza?',
    'confirm_title'   => 'Confirmar remoção de item',
    'confirm_ok'      => 'Remover',
    'confirm_cancel'  => 'Cancelar',
    'confirm_success' => 'Item removido com sucesso.',
    'confirm_failed'  => 'Remoção falhou! Mensagem do Servidor: ',

    'public_status_title' => 'Public status',
    'public_status_image' => 'Status image',
    'public_status_page'  => 'Public status page',
];
