<?php

/**
 * Plugin Name: Bom Doutor
 * Plugin URI: https://github.com/lifedesenvolve/bom-doutor/
 * Description: Plugin dedicado ao site bom doutor.
 * Version: 1.2.7
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
wp_register_style('dados-usuario-css', plugins_url('/assets/css/dados-usuario.css', __FILE__));

function feegow_create_tables() {
    global $wpdb;

    // Definir o prefixo da tabela
    $prefix = $wpdb->prefix . 'feegow_';

    // Tabela Tipo de Procedimento
    $table_tipo_procedimento = $prefix . 'tipo_procedimento';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_tipo_procedimento'") != $table_tipo_procedimento) {
        $sql = "CREATE TABLE $table_tipo_procedimento (
            id INT(11) NOT NULL AUTO_INCREMENT,
            id_tipo_procedimento INT(11) NOT NULL,
            nome VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY (id_tipo_procedimento)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Tabela Procedimento
    $table_procedimento = $prefix . 'procedimento';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_procedimento'") != $table_procedimento) {
        $sql = "CREATE TABLE $table_procedimento (
            id INT(11) NOT NULL AUTO_INCREMENT,
            id_procedimento INT(11) NOT NULL,
            id_tipo_procedimento INT(11) NOT NULL,
            nome VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY (id_procedimento)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Tabela Especialista
    $table_especialista = $prefix . 'especialista';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_especialista'") != $table_especialista) {
        $sql = "CREATE TABLE $table_especialista (
            id INT(11) NOT NULL AUTO_INCREMENT,
            id_especialista INT(11) NOT NULL,
            nome VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY (id_especialista)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Registrar função de ativação do plugin
register_activation_hook(__FILE__, 'feegow_create_tables');

// Função para excluir as tabelas
function feegow_delete_tables() {
    global $wpdb;

    // Definir o prefixo da tabela
    $prefix = $wpdb->prefix . 'feegow_';

    // Excluir tabela Tipo de Procedimento
    $table_tipo_procedimento = $prefix . 'tipo_procedimento';
    $wpdb->query("DROP TABLE IF EXISTS $table_tipo_procedimento");

    // Excluir tabela Procedimento
    $table_procedimento = $prefix . 'procedimento';
    $wpdb->query("DROP TABLE IF EXISTS $table_procedimento");

    // Excluir tabela Especialista
    $table_especialista = $prefix . 'especialista';
    $wpdb->query("DROP TABLE IF EXISTS $table_especialista");
}

// Registrar função de desativação do plugin
//register_deactivation_hook(__FILE__, 'feegow_delete_tables');