<?php

/**
 * Registers the Admin Menu
 *
 * @link       https://www.apdevops.com/
 * @since      1.0.0
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/admin/partials
 */

/**
 * Register the custom menu page.
 */
function ctf_register_menu_page(){
    add_menu_page(
        __( 'ClickUp Forms', 'textdomain' ),
        'ClickUp Forms',
        'manage_options',
        'clickup',
        'ctf_display_welcome_page',
        'dashicons-clickup',
        79
    );
    add_submenu_page(
        'clickup',
        'Help',
        'Help',
        'manage_options',
        'ctf_help',
        'ctf_display_help_page'
    );
    add_submenu_page(
        'clickup',
        'API Integration',
        'API Integration',
        'manage_options',
        'ctf_options',
        'ctf_display_settings_page'
    );
}
add_action( 'admin_menu', 'ctf_register_menu_page' );
