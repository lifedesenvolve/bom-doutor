<?php

// Register Custom Post Type Exame
// Post Type Key: Exame
function create_exame_cpt()
{

    $labels = array(
        'name' => _x('Exames', 'Post Type General Name', 'textdomain'),
        'singular_name' => _x('Exame', 'Post Type Singular Name', 'textdomain'),
        'menu_name' => _x('Exames', 'Admin Menu text', 'textdomain'),
        'name_admin_bar' => _x('Exame', 'Add New on Toolbar', 'textdomain'),
        'archives' => __('Exame', 'textdomain'),
        'attributes' => __('Exame', 'textdomain'),
        'parent_item_colon' => __('Exame', 'textdomain'),
        'all_items' => __('All Exames', 'textdomain'),
        'add_new_item' => __('Add New Exame', 'textdomain'),
        'add_new' => __('Add New', 'textdomain'),
        'new_item' => __('New Exame', 'textdomain'),
        'edit_item' => __('Edit Exame', 'textdomain'),
        'update_item' => __('Update Exame', 'textdomain'),
        'view_item' => __('View Exame', 'textdomain'),
        'view_items' => __('View Exames', 'textdomain'),
        'search_items' => __('Search Exame', 'textdomain'),
        'not_found' => __('Not found', 'textdomain'),
        'not_found_in_trash' => __('Not found in Trash', 'textdomain'),
        'featured_image' => __('Featured Image', 'textdomain'),
        'set_featured_image' => __('Set featured image', 'textdomain'),
        'remove_featured_image' => __('Remove featured image', 'textdomain'),
        'use_featured_image' => __('Use as featured image', 'textdomain'),
        'insert_into_item' => __('Insert into Exame', 'textdomain'),
        'uploaded_to_this_item' => __('Uploaded to this Exame', 'textdomain'),
        'items_list' => __('Exames list', 'textdomain'),
        'items_list_navigation' => __('Exames list navigation', 'textdomain'),
        'filter_items_list' => __('Filter Exames list', 'textdomain'),
    );

    $args = array(
        'label' => __('Exame', 'textdomain'),
        'description' => __('description', 'textdomain'),
        'labels' => $labels,
        'menu_icon' => 'dashicons-welcome-write-blog',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'comments', 'trackbacks', 'page-attributes', 'post-formats', 'custom-fields'),
        'taxonomies' => array(),
        'hierarchical' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'has_archive' => true,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'can_export' => true,
        'show_in_nav_menus' => true,
        'menu_position' => 5,
        'capability_type' => 'post',
        'show_in_rest' => true,
    );

    register_post_type('bd-exames', $args);
}
add_action('init', 'create_exame_cpt', 0);