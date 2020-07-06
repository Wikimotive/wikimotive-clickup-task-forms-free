<?php

/**
 * API Integration Setup Wizard
 *
 * @link       https://www.apdevops.com/
 * @since      1.0.0
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/admin/partials
 */

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

function ctf_get_token( $client_id = null, $client_secret = null, $code = null ) {
    if ( isset($client_id) && isset($client_secret) && isset($code) ) :
        $response = wp_remote_retrieve_body( wp_remote_post( "https://app.clickup.com/api/v2/oauth/token?client_id={$client_id}&client_secret={$client_secret}&code={$code}" ) );
        return json_decode( $response, true );
	endif;
}

/**
 * custom option and settings
 */
function ctf_settings_init() {
    register_setting( 'ctf', 'ctf_options' );

    add_settings_section(
        'ctf_section_api_credentials', // id
        __( 'Let\'s get started by adding your ClickUp Cloud API app credentials', 'ctf' ), // section title
        'ctf_section_callback', // callback
        'ctf' // page
    );

    /**
     * The Client ID Field
     */
    add_settings_field(
        'ctf_client_id', // Field ID
        __( 'Client ID', 'ctf' ), // Title
        'ctf_field', // Callback
        'ctf', // Page > Slug (first argument) from the register_settings function
        'ctf_section_api_credentials', // Section ID
        [
            'option_name' => 'ctf_options', // Required: The option name (second argument) from register_settings
            'field_id' => 'ctf_client_id', // Required: Must Match the first argument of add_settings_field function (Field ID)
            'field_type' => 'text', // ENUM - Available Options: text (default)
            'size' => 60, // Input size
            'placeholder' => 'API Client ID', // Optional
            'class' => 'ctf-field-row',
            'field_class' => 'ctf-input',
        ] // Arguments Passable to Callback Function as Array
    );

    /**
     * The Client Secret
     */
    add_settings_field(
        'ctf_client_secret', // Field ID
        __( 'Client Secret', 'ctf' ), // Title
        'ctf_field', // Callback
        'ctf', // Page > Slug (first argument) from the register_settings function
        'ctf_section_api_credentials', // Section ID
        [
            'option_name' => 'ctf_options', // Required: The option name (second argument) from register_settings
            'field_id' => 'ctf_client_secret', // Required: Must Match the first argument of add_settings_field function (Field ID)
            'field_type' => 'password', // ENUM - Available Options: text (default)
            'size' => 60, // Input size
            'placeholder' => 'API Client Secret', // Optional
            'class' => 'ctf-field-row',
            'field_class' => 'ctf-input',
        ] // Arguments Passable to Callback Function as Array
    );

    add_settings_field(
        'ctf_access_token', // Field ID
        __( 'Access Token', 'ctf' ), // Title
        'ctf_access_token_cb', // Callback
        'ctf', // Page > Slug (first argument) from the register_settings function
        'ctf_section_api_credentials', // Section ID
        [
            'label_for' => 'ctf_access_token',
            'field_id' => 'ctf_access_token',
            'option_name' => 'ctf_options', // Required: The option name (second argument) from register_settings
            'class' => 'ctf-field-row',
            'field_class' => 'ctf-input',
            'size' => 60,
            'placeholder' => 'Access Token : Click Update Access Token to get started',
        ] // Arguments Passable to Callback Function as Array
    );

}

add_action( 'admin_init', 'ctf_settings_init' );

/**
 * Add Settings Section Callback
 */
function ctf_section_callback( $args ) {

}

function ctf_field( $args ) {
    // get the value of the setting we've registered with register_setting()
    $options = get_option( $args['option_name'] );
    // output the field
    $field_type = ( isset( $args['field_type'] ) ) ? $args['field_type'] : 'text';

    switch ( $field_type ) :
        case 'text':
            echo '<input type="text" id="'. esc_attr( $args['field_id'] ) .'" name="'.$args['option_name'].'['. esc_attr( $args['field_id'] ) .']" ';
            echo (isset($args['size'])) ? 'size="' . esc_attr( $args['size'] ) . '" ' : '';
            echo ( isset( $options[ $args['field_id'] ] ) ) ? 'value="' . $options[ $args['field_id'] ]. '" ' : '';
            echo ( isset( $args['field_class'] ) ) ? 'class="' . $args['field_class'] . '" ' : '';
            echo ( isset( $args['placeholder'] ) ) ? 'placeholder="' . $args['placeholder'] . '" ' : '';
            echo ' />';
        break;

        case 'password' :
            echo '<input type="password" id="'. esc_attr( $args['field_id'] ) .'" name="'.$args['option_name'].'['. esc_attr( $args['field_id'] ) .']" ';
            echo (isset($args['size'])) ? 'size="' . esc_attr( $args['size'] ) . '" ' : '';
            echo ( isset( $options[ $args['field_id'] ] ) ) ? 'value="' . $options[ $args['field_id'] ]. '" ' : '';
            echo ( isset( $args['field_class'] ) ) ? 'class="' . $args['field_class'] . '" ' : '';
            echo ( isset( $args['placeholder'] ) ) ? 'placeholder="' . $args['placeholder'] . '" ' : '';
            echo ' />';
        break;

        default:
            echo '<input type="text" id="'. esc_attr( $args['field_id'] ) .'" name="'.$args['option_name'].'['. esc_attr( $args['field_id'] ) .']" ';
            echo (isset($args['size'])) ? 'size="' . esc_attr( $args['size'] ) . '"' : '';
            echo ( isset( $options[ $args['field_id'] ] ) ) ? 'value="' . $options[ $args['field_id'] ]. '" ' : '';
            echo ( isset( $args['field_class'] ) ) ? 'class="' . $args['field_class'] . '" ' : '';
            echo ( isset( $args['placeholder'] ) ) ? 'placeholder="' . $args['placeholder'] . '" ' : '';
            echo ' />';
        break;
    endswitch;
}
 
function ctf_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // add error/update messages

    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'ctf_messages', 'ctf_message', __( 'Settings Saved', 'ctf' ), 'updated' );
    }
 
    // show error/update messages
    settings_errors( 'ctf_messages' );
    ?>
    <div class="ctf-admin-container">
        <h1 class="ctf-admin-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
        <?php
        $options = get_option( 'ctf_options' );
        // output security fields for the registered setting "ctf"
        settings_fields( 'ctf' );
        // output setting sections and their fields
        // (sections are registered for "ctf", each field is registered to a specific section)
        do_settings_sections( 'ctf' );
        // output save settings button
        echo '<div class="ctf-submit-button-wrapper">';
            submit_button( 'Save', 'ctf-submit-button', 'submit', false );
            
            if ( isset( $options['ctf_client_id'] ) && isset( $options['ctf_client_secret'] ) && !empty( $options['ctf_client_id'] ) && !empty( $options['ctf_client_secret'] ) ) :
                ctf_get_token_button( $options['ctf_client_id'] );
            endif;
        echo '</div>';
        ?>
        </form>
    </div>
    <?php
}

function ctf_get_token_button( $client_id ) {
    $api_uri = 'https://app.clickup.com/api';
    $redirect_uri = urlencode( admin_url( 'admin.php?page=ctf_options' ) );
    echo "<a href='{$api_uri}?client_id={$client_id}&redirect_uri={$redirect_uri}' class='button ctf-get-token'>Update Access Token</a>";
}

function ctf_access_token_cb( $args ) {

    $options = get_option( $args['option_name'] );
    $access_response = ( isset( $_GET['code'] ) ) ? ctf_get_token( $options['ctf_client_id'], $options['ctf_client_secret'], $_GET['code'] ) : false;
    $access_token = null;

    if ( isset( $access_response['access_token'] ) ) :
        $ctf_success_message = __( "SUCCESS: Click 'Save' to commit changes | Your New Access Token is: {$access_response['access_token']}", 'ctf_options');
        printf( '<div class="ctf-success-message"><p>%1$s</p></div>', esc_html( $ctf_success_message ) );
        $access_token = sanitize_text_field( $access_response['access_token'] );
    endif;

    $update_access_token_input = ( isset( $access_token ) ) ? $access_token : false;
    $database_access_token = ( isset( $options[ $args['field_id'] ] ) ) ? $options[ $args['field_id'] ] : '';

    echo '<input type="password" id="'. esc_attr( $args['field_id'] ) .'" name="'.$args['option_name'].'['. esc_attr( $args['field_id'] ) .']" ';
    echo ( isset( $args['size'] ) ) ? 'size="' . esc_attr( $args['size'] ) . '"' : '';
    echo ( false !== $update_access_token_input ) ? 'value="' . $update_access_token_input . '"' : 'value="' . $database_access_token . '" ';
    echo ( isset( $args['field_class'] ) ) ? 'class="' . $args['field_class'] . '" ' : '';
    echo ( isset( $args['placeholder'] ) ) ? 'placeholder="' . $args['placeholder'] . '" ' : '';
    echo ' />';

    if( isset( $_GET['code'] ) && isset( $access_response['err'] ) && false === $update_access_token_input && !isset( $_GET['settings-updated'] ) ) :
        $ctf_response_error_code = ( isset( $access_response['ECODE'] ) ) ? $access_response['ECODE'] : 'Error Message Unknown';
        $ctf_response_error_desc = ( isset( $access_response['err'] ) ) ? $access_response['err'] : 'ERROR UNKNOWN';
        $ctf_error_message = __( "AUTHORIZATION ERROR CODE: {$ctf_response_error_code} - {$ctf_response_error_desc}", 'ctf_options');
        printf( '<div class="ctf-error-message"><p>%1$s</p></div>', esc_html( $ctf_error_message ) );
    endif;
}
