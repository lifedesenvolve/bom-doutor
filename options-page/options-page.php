<?php

/* page and menu */
function bom_doutor_add_settings_page()
{
    add_menu_page(
        'Configurações do Bom Doutor',
        'Bom Doutor',
        'manage_options',
        'bom_doutor_settings',
        'bom_doutor_settings_page',
        'dashicons-admin-generic',
        4
    );
    add_options_page(
        'Configurações do Bom Doutor',
        'Bom Doutor',
        'manage_options',
        'bom_doutor_settings',
        'bom_doutor_settings_page'
    );
}
add_action('admin_menu', 'bom_doutor_add_settings_page');

/* style page options */
function bom_doutor_settings_styles()
{
?>
    <style>
        .title {
            margin-top: 2%;
        }

        .form-table input[type="text"] {
            width: 50%;
        }
    </style>
<?php
}
add_action('admin_head', 'bom_doutor_settings_styles');

/* options */
function bom_doutor_register_settings()
{
    register_setting('bom_doutor_settings', 'bom_doutor_api_url');
    register_setting('bom_doutor_settings', 'bom_doutor_api_token');
}
add_action('admin_init', 'bom_doutor_register_settings');

/* render page options */
function bom_doutor_settings_page()
{
?>
    <h1 class="title">Configurações</h1>
    <hr>
    <form action="options.php" method="post">
        <?php
        settings_fields('bom_doutor_settings');
        do_settings_sections('bom_doutor_settings');
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">API Url</th>
                <td>
                    <input type="text" name="bom_doutor_api_url" value="<?php echo esc_attr(get_option('bom_doutor_api_url')); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">API Token</th>
                <td>
                    <input type="text" name="bom_doutor_api_token" value="<?php echo esc_attr(get_option('bom_doutor_api_token')); ?>" />
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>

    <div class="container">
        <div class="row">
            <h3>Informações</h3>
            <p>
                1 - Para listar especialidades é preciso vincular a especialidade a um profissional:
                <a target="_blank" href="https://app2.feegow.com/v8/?P=Profissionais">https://app2.feegow.com/v8/?P=Profissionais</a>
            </p>
            <p>
                2 - Para listar as especialidades de acordo com o procedimento é preciso adicionar as especialidades aos procedimentos:
                <a target="_blank" href="https://app2.feegow.com/v8/?P=Procedimentos&Pers=Follow">https://app2.feegow.com/v8/?P=Procedimentos&Pers=Follow</a>
            </p>
        </div>
    </div>
    <hr>
    <div class="wrap">
        <h1>Atualizar dados</h1>

        <form method="post" action="">
            <input type="hidden" name="action" value="update_database">
            <?php wp_nonce_field('update_database', 'update_db_nonce'); ?>
            <p>Clique no botão abaixo para atualizar os dados:</p>
            <p>
                <input type="submit" class="button button-primary" name="update_db" value="Atualizar dados">
            </p>
        </form>
    </div>

<?php
$api = new Api();
echo '<pre>';
print_r($api->listUnidades());
echo '</pre>';


    function update_db_tipos_procedimentos($api)
    {
        global $wpdb;
        $prefix = $wpdb->prefix . 'feegow_';
        $table_tipo_procedimento = $prefix . 'tipo_procedimento';

        $api = new Api();
        $tipos_procedimentos = $api->list('tipos_procedimentos');

        $wpdb->query("ALTER TABLE $table_tipo_procedimento ADD UNIQUE KEY `id_tipo_procedimento` (`id_tipo_procedimento`)");

        if ($tipos_procedimentos != '404') {
            foreach ($tipos_procedimentos as $tipos) {
                // Inserir ou atualizar dados
                $wpdb->query($wpdb->prepare("INSERT INTO $table_tipo_procedimento (id_tipo_procedimento, nome) VALUES (%d, %s) ON DUPLICATE KEY UPDATE nome = VALUES(nome)", $tipos['id'], $tipos['tipo']));
            }
            return 'dados atualizados';
        }else{
            return 'Falha na api';
        }
    }

    function update_db_procedimentos($api)
    {
        global $wpdb;
        $prefix = $wpdb->prefix . 'feegow_';
        $table_procedimento = $prefix . 'procedimento';

        $api = new Api();
        $procedimentos = $api->list('procedimentos');

        $wpdb->query("ALTER TABLE $table_procedimento ADD UNIQUE KEY `id_procedimento` (`id_procedimento`)");

        if ($procedimentos != '404') {
            foreach ($procedimentos as $procedimento) {

                $wpdb->query($wpdb->prepare("INSERT INTO $table_procedimento (id_procedimento, id_tipo_procedimento, nome) VALUES (%d, %d, %s) ON DUPLICATE KEY UPDATE nome = VALUES(nome)", $procedimento['procedimento_id'], $procedimento['tipo_procedimento'], $procedimento['nome']));
            }
            return 'dados atualizados';
        }else{
            return 'Falha na api';
        }
    }

    function update_db_profissional($api)
    {
        global $wpdb;
        $prefix = $wpdb->prefix . 'feegow_';
        $table_profissional = $prefix . 'especialista';

        $api = new Api();
        $profissionais = $api->list('profissionais');

        $wpdb->query("ALTER TABLE $table_profissional ADD UNIQUE KEY `id_especialista` (`id_especialista`)");

        if ($profissionais != '404') {
            foreach ($profissionais as $profissional) {

                $wpdb->query($wpdb->prepare("INSERT INTO $table_profissional (id_especialista, nome) VALUES (%d, %s) ON DUPLICATE KEY UPDATE nome = VALUES(nome)", $profissional['profissional_id'], $profissional['nome']));
            }
            return 'dados atualizados';
        }else{
            return 'Falha na api';
        }
    }

    if (isset($_POST['update_db'])) {
        // Verifica o nonce de segurança
        if (!isset($_POST['update_db_nonce']) || !wp_verify_nonce($_POST['update_db_nonce'], 'update_database')) {
            wp_die('Ação não permitida.', 'Erro de segurança');
        }

        // Verifica se o botão "Atualizar dados" foi clicado
        if (isset($_POST['update_db'])) {
            $api = new Api();
            echo 'Status Tipos_procedimentos: ' . update_db_tipos_procedimentos($api) . '<br>';
            echo 'Status Procedimentos: ' . update_db_procedimentos($api) . '<br>';
            echo 'Status Profissional: ' . update_db_profissional($api) . '<br>';
            exit;
        }
    }
}
