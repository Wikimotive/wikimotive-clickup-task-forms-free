<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.apdevops.com/
 * @since      1.0.0
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/public/partials
 */


add_action( 'wp_headers', 'ctf_session');

function ctf_session() {
    $cookie_key = 'Now listen here, you hacking bois can just get along little doggie!';
    $cookie_value = hash( 'ripemd160', $_SERVER['HTTP_USER_AGENT'] . $cookie_key );

    setcookie( 'ctf_session', $cookie_value, time()+300, "/" );
}

add_shortcode( 'ctf_form', 'ctf_get_from_api' );
function ctf_get_from_api( $atts ) {

    $atts = shortcode_atts(
        array(
            'id' => null
        ),
        $atts,
        'ctf_form'
    );

    $fid = $atts['id'];
    $fpre = "ctf_{$fid}";

    $ins = new ctfPostTask( $fid );

    $users_list = ctf_make_options( $ins->getUsers(), 'id', 'username' );

    echo '<div class="ctf-form-wrapper">';

    /** Form Description */
    if ( false !== $ins->fieldEnabled( 'ctf_form_settings_inc_desc' ) ) :
        echo wpautop( do_shortcode( get_the_content( null, false, $atts['id'] ) ) );
    endif;

    /** Form ::START:: */
    echo "<form id='{$fpre}' method='post' action='' class='ctf-form'>";
    echo '<div class="ctf-legend"><p><strong><span class="ctf-required">*</span></strong> Indicates required fields.</p><p><strong>Note:</strong> This form has a 5 minute time limit for submission from the time the page loads.</p></div>';
    
    /** Assigner's Name */
    echo   "<label for='{$fpre}[assigner]'>Your Name<span class='ctf-required'>*</span></label>
            <input type='text' id='{$fpre}[assigner]' name='{$fpre}[assigner]' placeholder='Your Name...' required/>";
    
    /** Task Name */
    echo   "<label for='{$fpre}[title]'>Task Title<span class='ctf-required'>*</span></label>
            <input type='text' id='{$fpre}[title]' name='{$fpre}[title]' placeholder='Task Title...' required/>";
    
    /** Priority */
    echo   "<label for='{$fpre}[priority]'>Priority<span class='ctf-required'>*</span></label>
            <select id='{$fpre}[priority]' name='{$fpre}[priority]' required>
                <option value=''>Select Priority...</option>
                <option value='4'>Low</option>
                <option value='3'>Normal</option>
                <option value='2'>High</option>
                <option value='1'>Urgent</option>
            </select>";

    /** Assign Task To */
    if ( false !== $ins->fieldEnabled( 'ctf_form_settings_enable_assignment' ) ) :
        echo   "<label for='{$fpre}[assignees][]'>Assign Task To:</label>
                <select id='{$fpre}[assignees][]' name='{$fpre}[assignees][]' multiple>
                    <option value=''>Leave Blank</option>";
            foreach( $users_list as $user_id => $user_name ) :
              echo "<option value='{$user_id}'>{$user_name}</option>";
            endforeach;
        echo   "</select>";
    endif;

    /** Due Date */
    if ( false !== $ins->fieldEnabled( 'ctf_form_settings_enable_duedate' ) ):
        echo   "<label for='{$fpre}[duedate]'>Due Date</label>
                <input type='datetime-local' id='{$fpre}[duedate]' name='{$fpre}[duedate]' />";
    endif;

    /** Task Description */
    echo   "<label for='{$fpre}[desc]'>Task Description<span class='ctf-required'>*</span></label>
            <textarea id='{$fpre}[desc]' name='{$fpre}[desc]' required></textarea>";
    
    /** Output custom fields */
            $ins->outputCustomFields();

    /** Pull in session data */
    /** This function must be very very secure, so we're going to set some session data to validate against when the API call is instantiated */

    $butDatSessionHashThoMmmmWink = ( isset( $_COOKIE['ctf_session'] ) ) ? $_COOKIE['ctf_session'] : false;

    echo   "<input type='hidden' id='{$fpre}[ctf_validate]' name='{$fpre}[ctf_validate]' value='{$butDatSessionHashThoMmmmWink}' required/>";

    /** Submit Button */
    echo   "<input type='submit' id='{$fpre}[submit]' name='{$fpre}[submit]' value='Create Task' />";

    echo '</form></div>';
    /** Form ::END:: */

    /** Submit */
    if ( isset( $_POST[$fpre]['submit'] ) ) :
        $ins->sendTask( $_POST[$fpre] );
    endif;
}
