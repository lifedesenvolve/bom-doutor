<?php

/**
 * Plugin Name: Bom Doutor
 * Plugin URI: https://github.com/lifedesenvolve/bom-doutor/
 * Description: Plugin dedicado ao site bom doutor.
 * Version: 1.2.2
 * Author: Agencia Life
 * Author URI: https://github.com/lifedesenvolve/
 */

define('PLUGIN_URL', plugin_dir_url(__FILE__));
date_default_timezone_set('America/Sao_Paulo');
require_once 'includes/custom-post-type-exames.php';
require_once 'includes/shortcodes.php';
require_once 'options-page/options-page.php';
require_once 'api/class-Api.php';
require_once 'api/route.php';

function remove_admin_bar()
{
    if (!current_user_can('manage_options')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');


wp_register_style('page-agendamento-css', plugins_url('/assets/css/page-agendamento.css', __FILE__));
wp_register_style('pesquisa-agendamento-css', plugins_url('/assets/css/pesquisa-agendamento.css', __FILE__));
wp_register_style('filtro-agendamento-css', plugins_url('/assets/css/filtro-agendamento.css', __FILE__));
