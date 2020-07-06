<?php

/**
 * Creates the settings to control the task forms
 *
 * @link       https://www.apdevops.com/
 * @since      1.0.0
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/admin/partials
 */


function ctf_form_settings() {
    add_meta_box(
        'ctf_form_settings', // string $id,
        'Form Settings', // string $title,
        'ctf_form_fields', // callable $callback,
        'ctf_form', // string|array|WP_Screen $screen = null,
        'after_title', // string $context = 'advanced',
        'default', // string $priority = 'default',
        null // array $callback_args = null
    );
}

add_action('add_meta_boxes', 'ctf_form_settings');

function ctf_form_fields( $post ) {
    
    $id = get_the_ID();
    echo "<h3>Your Shortcode: <input value='[ctf_form id=\"{$id}\"]' type='text' disabled/></h3>";

    // This is where we can add our search and other settings
    ?>
    <div class="ctf-form-selector-row">
        <!-- Team field | https://api.clickup.com/api/v2/team -->
        <div class="ctf-form-selector-column-3rd">
            <label for="ctf_form_settings[team]" class="ctf-selector-label">Select Team:</label>
            <p class="ctf-label-desc">After you have selected your team, click "Save Draft" to continue.</p>
            <select name="ctf_form_settings[team]" id="ctf_form_settings[team]">
                <option value="">Clear Selection &times;</option>
                <?php
                    $team_value = ( isset( get_post_meta( $post->ID, 'ctf_form_settings', true )['team']) ) ? get_post_meta( $post->ID, 'ctf_form_settings', true )['team'] : '';

                    if ( empty( $team_value ) ) :
                        // If value is empty use this method, else just print the option
                        $teams = new ctfAPICall( 'team' );
                        $teams_list = ctf_make_options( $teams->response(), 'id', 'name');
                        foreach( $teams_list as $team_id => $team_name ) :
                            $team_val = json_encode( [$team_id => $team_name] );
                            echo "<option value='{$team_val}' ".selected($team_value, $team_val).">$team_name</option>";
                        endforeach;
                    else:
                        $team_value_name = array_values( json_decode( $team_value, true ) )[0];
                        echo "<option value='{$team_value}' selected>{$team_value_name}</option>";
                    endif;
                ?>
            </select>
        </div>
        
        <?php if ( !empty( $team_value ) ) : $team_value_id = array_keys( json_decode( $team_value, true ) )[0]; ?>
            <!-- Spaces Field | https://api.clickup.com/api/v2/team/team_id/space?archived=false -->
            <div class="ctf-form-selector-column-3rd">
                <label for="ctf_form_settings[space]" class="ctf-selector-label">Select Space:</label>
                <p class="ctf-label-desc">After you have selected your space, click "Save Draft" to continue.</p>
                <select name="ctf_form_settings[space]" id="ctf_form_settings[space]">
                    <option value="">Clear Selection &times;</option>
                    <?php
                        $space_value = ( isset( get_post_meta( $post->ID, 'ctf_form_settings', true )['space']) ) ? get_post_meta( $post->ID, 'ctf_form_settings', true )['space'] : '';

                        if ( empty( $space_value ) ) :
                            $spaces = new ctfAPICall( "team/{$team_value_id}/space?archive=false" );
                            $spaces_list = ctf_make_options( $spaces->response(), 'id', 'name');
                            foreach( $spaces_list as $space_id => $space_name ) :
                                $space_val = json_encode( [$space_id => $space_name] );
                                echo "<option value='{$space_val}' ".selected($space_value, $space_val).">$space_name</option>";
                            endforeach;
                        else:
                            $space_value_name = array_values( json_decode( $space_value, true ) )[0];
                            echo "<option value='{$space_value}' selected>{$space_value_name}</option>";
                        endif;
                    ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if ( !empty( $space_value ) ) : $space_value_id = array_keys( json_decode( $space_value, true ) )[0]; ?>
            <!-- List Field -->
            <div class="ctf-form-selector-column-3rd">
                <label for="ctf_form_settings[list]" class="ctf-selector-label">Select The List You Wish to Post Tasks To:</label>
                <p class="ctf-label-desc">After you have selected your list, click "Save Draft" to continue. Note: Folders are only listed for organization. They are not selectable, you must select a list item.</p>
                <select name="ctf_form_settings[list]" id="ctf_form_settings[list]">
                    <option value="">Clear Selection &times;</option>
                    <?php
                        $list_value = ( isset( get_post_meta( $post->ID, 'ctf_form_settings', true )['list']) ) ? get_post_meta( $post->ID, 'ctf_form_settings', true )['list'] : '';
                        
                        // https://api.clickup.com/api/v2/space/space_id/list?archived=false
                        $folderless = new ctfAPICall( "space/{$space_value_id}/list?archive=false" );
                        $folderless_list = ctf_make_options( $folderless->response(), 'id', 'name' );
                        
                        if ( empty( $list_value ) ) :
                            foreach ( $folderless_list as $list_item_id => $list_item_name ) :
                                $list_item_val = json_encode( [$list_item_id => $list_item_name] );
                                echo "<option value='{$list_item_val}' " . selected( $list_value, $list_item_val ) . ">List: {$list_item_name}</option>";
                            endforeach;

                            // https://api.clickup.com/api/v2/space/space_id/folder?archived=false
                            $folders = new ctfAPICall( "space/{$space_value_id}/folder?archive=false" );
                            $folder_list = ctf_make_options( $folders->response(), 'id', 'name' );

                            foreach( $folder_list as $folder_id => $folder_name ) :
                                echo "<option value='NULL'>Folder: {$folder_name}</option>";

                                // https://api.clickup.com/api/v2/folder/folder_id/list?archived=false
                                $folder_sublists = new ctfAPICall( "folder/{$folder_id}/list?archived=false" );
                                $folder_sublists_items = ctf_make_options( $folder_sublists->response(), 'id', 'name' );
                                foreach ( $folder_sublists_items as $folder_sublist_id => $folder_sublist_name ) :
                                    $folder_sublist_val = json_encode( [ $folder_sublist_id => $folder_sublist_name ] );
                                    echo "<option value='{$folder_sublist_val}' " . selected( $list_value, $folder_sublist_val ) . ">--List: {$folder_sublist_name}</option>";
                                endforeach;
                            endforeach;
                        else:
                            $list_value_name = array_values( json_decode( $list_value, true ) )[0];
                            echo "<option value='{$list_value}' selected>{$list_value_name}</option>";
                        endif;
                    ?>
                </select>
            </div>
        <?php endif;?>
    </div>
    <?php
}


/**
 * Meta Boxes
 */
add_filter( 'rwmb_meta_boxes', 'ctf_form_builder_meta_boxes' );
function ctf_form_builder_meta_boxes( $meta_boxes ) {

    /** Prefix Global */
    $prefix = 'ctf_form_settings_';
    /**
     * Advertisements and Survey Info
     */

    $feedback_html = '<h4>Premium Features Coming Soon!</h4>';
    $feedback_html .= '
        <p>We\'re working hard to improve this plugin, but we want to know what you think! For a <strong>limited time</strong>, we\'re offering a considerable <strong>lifetime discount</strong> on the premium version of this plugin! All you have to do is take a minute or two to fill out the survey below.</p>
        <p><a href="https://forms.gle/VKaoajjfhRXE6Vis8" target="_blank">Click Here to take the survey!</a></p>
        <h3>What kind of features are we talking about?</h3>
        <p><strong>We\'re glad you asked...</strong></p>
        <ul>
            <li><strong>Fully Customizable Design Options</strong><br><small>Adds a design settings page that allows you to customize your form design</small></li>
            <li><strong>Default Team Member Form Assignment</strong><br><small>Assign task submissions directly to a team member from the form options page</small></li>
            <li><strong>Task Status Updates</strong><br><small>Adds a users accessible page to view their submitted tasks, includes status updates, comments, and time tracked on the task</small></li>
            <li><strong>Comment Submissions</strong><br><small>Allows users to submit comments on their submitted tasks from the status page</small></li>
            <li><strong>Check List Submissions</strong><br><small>Allows users to create subtask checklists</small></li>
            <li><strong>Task Description Templates</strong><br><small>Adds form templates for various task submissions. ie. Graphics Requests, Reports Due, Support Ticket Submissions, etc.</small></li>
            <li><strong>Seamless ClickUp task list selection in form configuration</strong><br><small>Adds AJAX updates to drop down lists on the form configuration admin pages</small></li>
        </ul>
        ';

    $meta_boxes[] = array(
        'title' => 'Your Feedback is Important!',
        'post_types' => 'ctf_form',
        'context' => 'side',
        'priority' => 'low',
        'fields' => array(
            array(
                'type' => 'custom_html',
                'std' => $feedback_html,
            ),
        ),
    );

    /**
     * Form Design
     */

    $meta_boxes[] = array(
        'title' => 'Form Design Settings',
        'post_types' => 'ctf_form',
        'context' => 'after_title',
        'priority' => 'low',
        'fields' => array(
            array(
                'id' => $prefix . 'enable_assignment',
                'name' => 'Enable "Assign Task To" Field?',
                'type' => 'switch',
                'on_label' => 'Enabled',
                'off_label' => 'Disabled',
            ),
            array(
                'id' => $prefix . 'enable_duedate',
                'name' => 'Enable Due Date Field?',
                'type' => 'switch',
                'on_label' => 'Enabled',
                'off_label' => 'Disabled',
            ),
            array(
                'id' => $prefix . 'inc_desc',
                'name' => 'Display Form Description?',
                'type' => 'switch',
                'on_label' => 'Enabled',
                'off_label' => 'Disabled',
            ),
        ),
    );

    /**
     * Form Submit Settings
     */

    $meta_boxes[] = array(
        'title'      => 'Form Submit Settings',
        'post_types' => 'ctf_form',
        'context' => 'after_title',
        'priority' => 'low',
        'fields' => array(
            array(
                'id' => $prefix . 'task_format',
                'name' => 'Task Content Format',
                'label_description' => 'Use this field to format the description of the tasks that will be posted to ClickUp. <strong>Note: The Task Description field content will always appear below this content.</strong><br><br><strong>Accepts Markdown Syntax Formatting</strong><br><a href="https://www.markdownguide.org/getting-started/" target="_blank">Learn more about Markdown here.</a><br><br>You may add specific submission fields dynamically by inserting their html id attributes in between a pairs of curly brackets and percentage symbols ie. {%FIELD_ID%}.<br><br>For example: "{%assigner%} has requested the following task be completed by {%duedate%}: {%custom_field%}"<br><br>Note: Custom fields can be used by the standardized ID you give the field.<br><br>For more information, please see the "Help" page from the Plugin Menu.',
                'type' => 'wysiwyg',
                'raw' => true,
                'options' => array(
                    'teeny' => true,
                    'wpautop' => false,
                    'media_buttons' => false,
                    'textarea_rows' => 20,
                ),
            ),
            /** Custom fields really needs to include required fields etc. */
            array(
                'id' => $prefix . 'custom_fields',
                'name' => 'Custom Fields',
                'label_description' => 'The ID fields will be standardized. All non-alphanumeric characters will be converted to underscores including spaces and dashes.<br>ie. ID: "foo bar" will be converted to "foo_bar".',
                'type'    => 'group',
                'fields' => array(
                    array(
                        'id' => 'id',
                        'name' => 'Field ID',
                        'type' => 'text',
                    ),
                    array(
                        'id' => 'label',
                        'name' => 'Field Label',
                        'type' => 'text',
                    ),
                    array(
                        'id' => 'type',
                        'name' => 'Field Type',
                        'type' => 'select',
                        'options' => array(
                            'text' => 'Single Line Text',
                            'textarea' => 'Text Area',
                            'phone' => 'Phone Number',
                            'email' => 'Email Address',
                        ),
                    ),
                    // array(
                    //     'id' => 'required',
                    //     'name' => 'Require this field?',
                    //     'type' => 'switch',
                    //     'on_label' => 'Yes',
                    //     'off_label' => 'No',
                    // ),
                    array(
                        'id' => 'posts_to',
                        'name' => 'Posts To',
                        'type' => 'text',
                        'label_description' => 'Optional: If you want the field to post to a specific custom field in clickup, enter the custom_field ID here, otherwise leave blank',
                    ),
                ),
                'clone' => true,
                'sort_clone' => true,
                'add_button' => '+ Add New Custom Field',
            ),
            array(
                'type' => 'heading',
                'name' => 'Use the content area below to add a description to the top of your form.',
            ),
        ),
    );

    return $meta_boxes;
}


function ctf_save_fields( $post_id ) {
    if ( array_key_exists( 'ctf_form_settings', $_POST ) ) {

        $allowed_keys = [ 'team', 'space', 'list' ];
        $ctf_form_settings = $_POST['ctf_form_settings'];

        if ( is_array( $ctf_form_settings ) ) :
            foreach( $ctf_form_settings as $field_key => $field_value ) :
                if ( ! in_array( $field_key, $allowed_keys ) ) :
                    unset( $ctf_form_settings[$field_key] );
                else:
                    $ctf_form_settings[$field_key] = sanitize_text_field( $field_value );
                endif;
            endforeach;
        endif;

        update_post_meta(
            $post_id, // int $post_id
            'ctf_form_settings', // string $meta_key
            $ctf_form_settings // mixed $meta_value,
            // mixed $prev_value = ''
        );
    }
}
add_action('save_post', 'ctf_save_fields');