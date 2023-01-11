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
<?php

    $api = new Api();


    /* testes api */

    $api = new Api();

    echo '<pre>';
    print_r($api->listProcedimentos());
    echo '</pre>';
    /* echo '<pre>';
print_r($api->getPacienteByID(1));
echo '</pre>'; */

    /* $api->createPaciente(
    'teste',
    '74275703081',
    'teste@teste123.com',
    '03-09-1998',
    'M',
    '15999999999'
); */
}
