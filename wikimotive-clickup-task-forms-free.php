<?php

/**
 * 
 * @link              https://github.com/apratt86
 * @since             1.0.0
 * @package           Clickup_Task_Forms
 *
 * @wordpress-plugin
 * Plugin Name:       Wikimotive's Task Forms for ClickUp - Free
 * Plugin URI:        https://wordpress.org/plugins/wikimotive-clickup-task-forms-free/
 * Description:       This plugin allows you to add Task Submission Forms for ClickUp to your Wordpress website via the use of shortcodes and ClickUp's Cloud API Connection.
 * Version:           1.0.1
 * Author:            Aaron Pratt
 * Author URI:        https://profiles.wordpress.org/apdevops/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wikimotive-clickup-task-forms-free
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CLICKUP_TASK_FORMS_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-clickup-task-forms-activator.php
 */
function activate_clickup_task_forms_free() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clickup-task-forms-activator.php';
	Clickup_Task_Forms_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-clickup-task-forms-deactivator.php
 */
function deactivate_clickup_task_forms_free() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clickup-task-forms-deactivator.php';
	Clickup_Task_Forms_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_clickup_task_forms_free' );
register_deactivation_hook( __FILE__, 'deactivate_clickup_task_forms_free' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-clickup-task-forms.php';

/**
 * Required Plugins
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tgm-plugin-activation.php';
function clickup_task_forms_register_required_plugins_free() {
	$plugins = array(
		/**
		 * Meta Box
		 */
		array(
			'name'      => 'Meta Box',
			'slug'      => 'meta-box',
			'required'  => true,
		)
	);

	$config = array(
		'id'           => 'clickup-task-forms',
		'default_path' => '',
		'menu'         => 'ctf-req-plugins',
		'parent_slug'  => 'clickup',
		'capability'   => 'manage_options',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',
	);

	tgmpa( $plugins, $config );
}

// include the groups extension once metabox is active and groups is not already active

// wp-content/plugins/clickup-task-forms/libs/meta-box-group/meta-box-group.php

function ctf_meta_box_extensions() {

	$libs_path = plugin_dir_path( __FILE__ ) . 'libs/';

	$plugin_deps = array(
		'meta-box-group/meta-box-group.php',
	);

	if ( ! function_exists( 'is_plugin_active' ) ) :
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	endif;

	$metabox = 'meta-box/meta-box.php';

	if ( is_plugin_active( $metabox ) ) :
		foreach ( $plugin_deps as $plugin ) :
			if ( ! is_plugin_active( $plugin ) ) :
				require_once $libs_path . $plugin;
			endif;
		endforeach;
	endif;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_clickup_task_forms_free() {

	ctf_meta_box_extensions();

	add_action( 'tgmpa_register', 'clickup_task_forms_register_required_plugins_free' );

	$plugin = new Clickup_Task_Forms();
	$plugin->run();

}
run_clickup_task_forms_free();
