<?php

function dados_usuario_shortcode()
{

    $plugin_dir_path = plugin_dir_path(dirname(__FILE__));
    $image_path = plugins_url('assets/image/', $plugin_dir_path);
    wp_enqueue_style('dados-usuario-css');

    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

        <section class="container-fluid py-3 py-sm-4 py-md-5 dados-usuario">

            <div class="container px-3 px-md-5 my-5">
                <div class="row justify-content-center align-items-left">
                    <div class="col-12 col-md-6 text-left">
                        <h1><?= $current_user->display_name ?></h1>
                    </div>
                    <div class="col-12 col-md-6 text-left text-md-left">
                        <h5><img src="<?= $image_path . 'edit.png' ?>" alt=""> <a href="">Editar dados</a></h5>
                    </div>
                </div>

                <div class="row mt-4 mt-md-5">
                    <div class="col-12 col-md-6 text-left text-md-left">
                        <h2>Dados pessoais</h2>
                        <span>Ut tellus elementum sagittis vitae et leo</span>
                    </div>
                    <div class="col-12 col-md-6 pt-3 pt-md-0">
                        <div class="item pt-2">
                            <h5><img src="<?= $image_path . 'user.png' ?>" alt=""> <?= $current_user->display_name ?></h5>
                        </div>
                        <div class="item pt-2">
                            <div class="row">
                                <div class="col-sm-6 col-md-12">
                                    <h5><img src="<?= $image_path . 'key.png' ?>" alt=""> <?= get_field('user_genero', 'user_' . get_current_user_id()); ?></h5>
                                </div>
                                <div class="col-sm-6 col-md-12">
                                    <h5><img src="<?= $image_path . 'calendar.png' ?>" alt=""> <?= get_field('user_data_de_nascimento', 'user_' . get_current_user_id()); ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 mt-md-5">
                    <div class="col-12 col-md-6 text-left text-md-left">
                        <h2>Contato</h2>
                        <span>Ut tellus elementum sagittis vitae et leo</span>
                    </div>
                    <div class="col-12 col-md-6 pt-3 pt-md-0">
                        <div class="item pt-2">
                            <h5><img src="<?= $image_path . 'phone.png' ?>" alt=""> <?= get_field('user_telefone', 'user_' . get_current_user_id()); ?></h5>
                        </div>
                        <div class="item pt-2">
                            <h5><img src="<?= $image_path . 'at-sign.png' ?>" alt=""> <?= $current_user->user_email ?></h5>
                        </div>
                    </div>
                </div>
            </div>

        </section>

<?php

    }else{
        header('location: '.site_url());
    }
}

add_shortcode('dados_usuario', 'dados_usuario_shortcode');
