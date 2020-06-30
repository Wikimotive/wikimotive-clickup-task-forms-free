<?php

/**
 * Functions for Admin Use
 * 
 * @link       https://www.apdevops.com/
 * @since      1.0.0
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/admin/partials
 */

require_once plugin_dir_path( __FILE__ ) . 'setup-wizard.php';

function ctf_display_help_page() {
		echo '<h1 class="ctf-admin-title"><span class="ctf-rainbow">Setup Guide for ClickUp<img src="'.plugin_dir_url(__DIR__).'css/clickup-symbol_color.png" width="25" height="auto" style="margin: 0px 6px" />Task Forms!</span></h1>';
		include_once plugin_dir_path( __FILE__ ) . 'help-page.php';
}

function ctf_display_settings_page() {
	add_action( 'admin_init', 'ctf_settings_init' );
	ctf_options_page_html();
}

function ctf_sanitize_id( $str = '', $delimiter = '-' ) {
    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    return $slug;
}

function ctf_display_welcome_page() {
	echo '<h1 class="ctf-admin-title"><span class="ctf-rainbow">Welcome to ClickUp<img src="'.plugin_dir_url(__DIR__).'css/clickup-symbol_color.png" width="25" height="auto" style="margin: 0px 6px" />Task Forms!</span></h1>';
	echo '<p>For setup instructions please see the <a href="/wp-admin/admin.php?page=ctf_help">Help Page</a>.</p>';
}