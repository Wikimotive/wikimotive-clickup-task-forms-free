<?php

/**
 * Functions for Admin Messages
 * 
 * @link       https://www.apdevops.com/
 * @since      1.0.1
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/admin/partials
 */

function ctf_survey_message() {
    $screen = get_current_screen();
    if ( in_array( $screen->id, [ 'edit-ctf_form', 'ctf_form', 'clickup-forms_page_ctf_help', 'clickup-forms_page_ctf_options', 'toplevel_page_clickup', 'plugins', 'dashboard' ] ) ) :
    ?>
    <div class="notice notice-info notice-ctf is-dismissible">
        <p><?php _e( '<strong>Wikimotive\'s ClickUp Task Forms:</strong> Your Feedback is Important! Please be sure to take a moment to fill out our survey. For a <strong>LIMITED TIME</strong> we are offering a considerable <strong>LIFETIME DISCOUNT</strong> on coming premium features! <a href="https://forms.gle/VKaoajjfhRXE6Vis8" target="_blank">Click Here to take the survey!</a>', 'clickup' ); ?></p>
    </div>
    <?php
    endif;
}
add_action( 'admin_notices', 'ctf_survey_message' );