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

    echo '<pre>';
    print_r($api->listProfissionaisHorarios(3, [
        0 => 131,
        1 => 187,
        2 => 271,
    ], 1, '23-01-2023', '23-01-2023'));
    echo '</pre>';
    //->procedimentos->where('tipo_procedimento', 4)
    //177.820.767-73
    /*
    Array
(
    [0] => Array
        (
            [procedimento_id] => 1
            [procedimento_nome] => Cirurgia
        )

    [1] => Array
        (
            [procedimento_id] => 2
            [procedimento_nome] => Consulta
        )

    [2] => Array
        (
            [procedimento_id] => 3
            [procedimento_nome] => Exame
        )

    [3] => Array
        (
            [procedimento_id] => 4
            [procedimento_nome] => Procedimento
        )

    [4] => Array
        (
            [procedimento_id] => 9
            [procedimento_nome] => Retorno
        )

)
    */
}
