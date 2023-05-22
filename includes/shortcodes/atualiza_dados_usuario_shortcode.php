<?php

function atualiza_dados_usuario_shortcode()
{
    wp_enqueue_style('dados-usuario-css');

    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();

?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

        <section class="container-fluid py-3 py-sm-4 py-md-5 dados-usuario atualiza-dados">

            <div class="container px-3 px-md-5 my-5">

                <div class="row justify-content-center align-items-left">
                    <div class="col-12 col-md-12 text-left mb-5">
                        <h2>Atualize suas informações</h2>
                    </div>
                </div>

                <div class="row mt-4 mt-md-5">
                    <div class="col-12 col-md-6 text-left text-md-left">
                        <h2>Dados pessoais</h2>
                        <span>Ut tellus elementum sagittis vitae et leo</span>
                    </div>
                    <div class="col-12 col-md-6 pt-3 pt-md-0">
                        <form action="<?php echo admin_url('admin-ajax.php?action=acf/update_user_meta'); ?>" method="POST">
                            <div class="form-group mt-2">
                                <label for="user_nome">Nome</label>
                                <input type="text" class="form-control" id="user_nome" name="user_nome" placeholder="Informe seu nome" value="<?php echo esc_attr($current_user->display_name); ?>">
                            </div>
                            <div class="form-group mt-2">
                                <label for="user_genero">Gênero</label>
                                <select class="form-control" id="user_genero" name="user_genero">
                                    <option value="nao_informar" <?php if (get_field('user_genero', 'user_' . get_current_user_id()) === 'nao_informar') {
                                                                        echo ' selected';
                                                                    } ?>>Não informar</option>
                                    <option value="feminino" <?php if (get_field('user_genero', 'user_' . get_current_user_id()) === 'feminino') {
                                                                    echo ' selected';
                                                                } ?>>Feminino</option>
                                    <option value="masculino" <?php if (get_field('user_genero', 'user_' . get_current_user_id()) === 'masculino') {
                                                                    echo ' selected';
                                                                } ?>>Masculino</option>
                                </select>
                            </div>

                            <div class="form-group mt-2">
                                <label for="user_data_de_nascimento">Data de nascimento</label>
                                <input type="date" class="form-control" id="user_data_de_nascimento" name="user_data_de_nascimento" placeholder="Informe sua data de nascimento" value="<?php echo esc_attr(date('Y-m-d', strtotime(get_user_meta(get_current_user_id(), 'user_data_de_nascimento', true)))); ?>">
                            </div>

                            <?php wp_nonce_field('update_user_fields', 'update_user_nonce'); ?>
                            <button type="submit" class="btn btn-primary mt-3">Atualizar informações</button>
                        </form>
                    </div>
                </div>


            </div>

            <div class="container px-3 px-md-5 my-5">
                <div class="row mt-4 mt-md-5">
                    <div class="col-12 col-md-6 text-left text-md-left">
                        <h2>Contato</h2>
                        <span>Informe seus dados de contato</span>
                    </div>
                    <div class="col-12 col-md-6 pt-3 pt-md-0">
                        <form action="<?php echo admin_url('admin-ajax.php?action=update_contato_fields'); ?>" method="POST">
                            <div class="form-group mt-2">
                                <label for="user_email">E-mail</label>
                                <input type="hidden" class="form-control" id="user_email" name="user_email" placeholder="Informe seu e-mail" value="<?php echo esc_attr($current_user->user_email); ?>">
                                <input type="text" class="form-control" disabled placeholder="Informe seu e-mail" value="<?php echo esc_attr($current_user->user_email); ?>">
                            </div>
                            <div class="form-group mt-2">
                                <label for="user_telefone">Telefone</label>
                                <input type="tel" class="form-control" id="user_telefone" name="user_telefone" placeholder="Informe seu telefone" value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'user_telefone', true)); ?>">
                            </div>

                            <?php wp_nonce_field('update_contato_fields', 'update_contato_nonce'); ?>
                            <button type="submit" class="btn btn-primary mt-3">Atualizar informações</button>
                        </form>
                    </div>
                </div>
            </div>

            <?php echo do_shortcode('[alterar_senha]'); ?>

        </section>

    <?php

    }else{
        header('location: '.site_url());
    }
}
// Registra ação para atualização dos campos do usuário
add_action('wp_ajax_acf/update_user_meta', 'update_user_fields');
add_action('wp_ajax_nopriv_acf/update_user_meta', 'update_user_fields');

function update_user_fields()
{
    // Verifica se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_nome'])) {

        // Obtém o ID do usuário atual
        $user_id = get_current_user_id();
        $user = wp_get_current_user();

        // Atualiza o nome do usuário
        wp_update_user(array(
            'ID' => $user->ID,
            'display_name' => $_POST['user_nome'],
        ));

        // Atualiza o valor do campo "user_genero"
        update_field('user_genero', $_POST['user_genero'], 'user_' . $user_id);

        // Atualiza o valor do campo "user_data_de_nascimento"
        update_field('user_data_de_nascimento', $_POST['user_data_de_nascimento'], 'user_' . $user_id);

        // Redireciona para a página atual
        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }
}
// Registra ação para atualização dos campos do usuário
add_action('wp_ajax_acf/update_user_meta', 'update_user_fields');
add_action('wp_ajax_nopriv_acf/update_user_meta', 'update_user_fields');

function update_contato_fields()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_email'])) {
        $user_id = get_current_user_id();

        //$email = sanitize_email($_POST['user_email']);
        //update_user_meta($user_id, 'user_email', $email);

        $telefone = sanitize_text_field($_POST['user_telefone']);
        update_user_meta($user_id, 'user_telefone', $telefone);

        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }
}
add_action('wp_ajax_update_contato_fields', 'update_contato_fields');
add_action('wp_ajax_nopriv_update_contato_fields', 'update_contato_fields');


add_shortcode('atualiza_dados_usuario', 'atualiza_dados_usuario_shortcode');


function alterar_senha_process_form()
{
    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $errors = array();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!wp_verify_nonce($_POST['alterar_senha_nonce'], 'alterar_senha_fields')) {
            die('Erro de segurança. Por favor, tente novamente.');
        }

        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $repetir_nova_senha = $_POST['repetir_nova_senha'];

        if (empty($senha_atual) || empty($nova_senha) || empty($repetir_nova_senha)) {
            $errors[] = 'Todos os campos são obrigatórios.';
        }

        if (!wp_check_password($senha_atual, $current_user->user_pass, $user_id)) {
            $errors[] = 'Senha atual incorreta.';
        }

        if ($nova_senha !== $repetir_nova_senha) {
            $errors[] = 'As novas senhas não conferem.';
        }

        if (empty($errors)) {
            wp_set_password($nova_senha, $user_id);
            $success = true;

            wp_redirect(home_url());
            exit;
        }
    }

    return array($errors, isset($success) ? $success : false);
}

function alterar_senha_shortcode()
{
    list($errors, $success) = alterar_senha_process_form();
    ob_start();
    ?>

    <div class="container px-3 px-md-5 my-5 atualiza-dados">
        <div class="row">
            <div class="col-md-6 justify-content-center align-items-left">
                <div class="text-left mb-5">
                    <h2>Alterar senha</h2>
                    <span>Ut tellus elementum sagittis vitae et leo</span>
                </div>

                <?php if (isset($success) && $success) : ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-success" role="alert">
                                Senha alterada com sucesso!
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)) : ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-danger" role="alert">
                                <?php foreach ($errors as $error) : ?>
                                    <div><?php echo $error; ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <div class="row mt-4 mt-md-5">
                    <div class="col-12 pt-3 pt-md-0">
                        <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="POST">
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                            <div class="form-group mt-2">
                                <label for="senha_atual">Senha atual</label>
                                <input type="password" class="form-control" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual">
                            </div>
                            <div class="form-group mt-2">
                                <label for="nova_senha">Nova senha</label>
                                <input type="password" class="form-control" id="nova_senha" name="nova_senha" placeholder="Digite sua nova senha">
                            </div>
                            <div class="form-group mt-2">
                                <label for="repetir_nova_senha">Repita a nova senha</label>
                                <input type="password" class="form-control" id="repetir_nova_senha" name="repetir_nova_senha" placeholder="Repita a nova senha">
                            </div>
                            <?php wp_nonce_field('alterar_senha_fields', 'alterar_senha_nonce'); ?>
                            <button type="submit" class="btn btn-primary mt-3">Atualizar Senha</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php
    return ob_get_clean();
}
add_shortcode('alterar_senha', 'alterar_senha_shortcode');
