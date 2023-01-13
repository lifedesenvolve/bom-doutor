<?php

/**
 * Plugin Name: Bom Doutor
 * Plugin URI: https://github.com/spalmeida
 * Description: Plugin dedicado ao site bom doutor.
 * Version: 1.0.7
 * Author: Samuel Almeida
 * Author URI: https://github.com/spalmeida
 */


define('PLUGIN_URL', plugin_dir_url(__FILE__));
date_default_timezone_set('America/Sao_Paulo');
require_once 'includes/custom-post-type-exames.php';
require_once 'includes/shortcodes.php';
require_once 'options-page/options-page.php';
require_once 'api/class-Api.php';
require_once 'api/route.php';

function remove_admin_access()
{
    if (
        !current_user_can('administrator') &&
        !(defined('DOING_AJAX') && DOING_AJAX)
    ) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('admin_init', 'remove_admin_access', 100);

function remove_admin_bar()
{
    if (!current_user_can('manage_options')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');
