<?php

/**
 * Creates the Post Type to control the Task Forms
 *
 * @link       https://www.apdevops.com/
 * @since      1.0.0
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/admin/partials
 */

/**
 * Registers the form custom post type called "ctf_form".
 *
 * @see get_post_type_labels() for label keys.
 */
function ctf_register_forms_cpt() {
    $labels = array(
        'name'                  => _x( 'ClickUp Forms', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'ClickUp Form', 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( 'ClickUp Forms', 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( 'ClickUp Form', 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New ClickUp Form', 'textdomain' ),
        'new_item'              => __( 'New ClickUp Form', 'textdomain' ),
        'edit_item'             => __( 'Edit ClickUp Form', 'textdomain' ),
        'view_item'             => __( 'View ClickUp Form', 'textdomain' ),
        'all_items'             => __( 'ClickUp Forms', 'textdomain' ),
        'search_items'          => __( 'Search ClickUp Forms', 'textdomain' ),
        'parent_item_colon'     => __( 'Parent ClickUp Forms:', 'textdomain' ),
        'not_found'             => __( 'No ClickUp Forms found.', 'textdomain' ),
        'not_found_in_trash'    => __( 'No ClickUp Forms found in Trash.', 'textdomain' ),
        'featured_image'        => _x( 'ClickUp Form Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'archives'              => _x( 'ClickUp Form archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
        'insert_into_item'      => _x( 'Insert into ClickUp Form', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this ClickUp Form', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
        'filter_items_list'     => _x( 'Filter ClickUp Forms list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
        'items_list_navigation' => _x( 'ClickUp Forms list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
        'items_list'            => _x( 'ClickUp Forms list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => 'clickup',
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'supports'           => array( 'title', 'editor' ),
    );
 
    register_post_type( 'ctf_form', $args );
}
 
add_action( 'init', 'ctf_register_forms_cpt' );