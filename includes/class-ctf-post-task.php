<?php

class ctfPostTask extends ctfConfig {

    public $formID, $list_id;

    public function __construct( $formID = null ) {
        $this->formID = $formID;
        $this->listID = self::getListID();
        $this->customFields = self::getCustomFields();
        parent::__construct();
    }

    public function getAllPostMeta() {
        return get_post_meta( $this->formID );
    }

    public function getListID() {
        return array_keys( json_decode( get_post_meta( $this->formID, 'ctf_form_settings', true )['list'], true ) )[0];
    }

    public function getUsers() {

        $url = "https://api.clickup.com/api/v2/list/{$this->listID}/member";
        $args = array(
            'headers' => array(
                'Authorization' => $this->token
            )
        );
        $response = wp_remote_get( $url, $args );
        return json_decode( wp_remote_retrieve_body( $response ), true );

    }

    public function getCustomFields() {
        return get_post_meta( $this->formID, 'ctf_form_settings_custom_fields', true );
    }

    public function outputCustomFields() {
        if ( false !== $this->customFields ) :
            $fpre = "ctf_{$this->formID}";
            foreach( $this->customFields as $field ) :
                $id = ( isset( $field['id'] ) ) ? ctf_sanitize_id( $field['id'], '_' ) : false;
                $label = ( false !== $id && isset( $field['label'] ) ) ? "<label for='{$fpre}[{$id}]'>{$field['label']}</label>" : '';
                $type = ( isset( $field['type'] ) ) ? $field['type'] : 'text';
                $required = ( isset( $field['required'] ) && false !== $field['required'] ) ? ' required' : '';

                if ( false !== $id ) :
                    switch ( $type ) :
                        /** Text */
                        case 'text':
                            echo $label;
                            echo "<input type='text' id='{$fpre}[{$id}]' name='{$fpre}[{$id}]'{$required}/>";
                        break;

                        /** Textarea */
                        case 'textarea':
                            echo $label;
                            echo "<textarea id='{$fpre}[{$id}]' name='{$fpre}[{$id}]'{$required}></textarea>";
                        break;

                        /** Phone */
                        case 'phone':
                            echo $label;
                            echo "<input type='tel' id='{$fpre}[{$id}]' name='{$fpre}[{$id}]'{$required}/>";
                        break;

                        /** Email */
                        case 'email':
                            echo $label;
                            echo "<input type='email' id='{$fpre}[{$id}]' name='{$fpre}[{$id}]' pattern='^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$' title='Must Match Email Format'{$required}/>";
                        break;

                        /** Default */
                        default:
                            echo $label;
                            echo "<input type='text' id='{$fpre}[{$id}]' name='{$fpre}[{$id}]'{$required}/>";
                        break;
                    endswitch;
                endif;
            endforeach;
        endif;
    }

    public function fieldEnabled( $setting_key = '' ) {
        $opt = get_post_meta( $this->formID, $setting_key, true );

        if ( isset( $opt ) && false !== (bool)$opt ) :
            return $opt;
        else:
            return false;
        endif;
    }

    protected function makeTaskContent( $form_data ) {
        $format = get_post_meta( $this->formID, 'ctf_form_settings_task_format', true );
        
        $replace_vars = $this->getInbetweenStrings( '{%', '%}', $format );

        $vars = [];

        foreach ( $replace_vars as $var ) :
            if ( 'assignees' === $var ) :
                foreach( $form_data['assignees'] as $user ) :
                    $vars["{%assignees%}"] .= "{$user}, ";
                endforeach;
            else:
            $vars["{%$var%}"] = $form_data["{$var}"];
            endif;
        endforeach;
        $vars["{%assignees%}"] = rtrim( $vars["{%assignees%}"], ', ' );

        return wp_strip_all_tags( strtr( $format, $vars ) );
    }

    protected function getInbetweenStrings($start, $end, $str){
        $matches = array();
        $regex = "/$start([a-zA-Z0-9_]*)$end/";
        preg_match_all($regex, $str, $matches);
        return $matches[1];
    }


    /**
     * REPLACE THIS FUNCTION WITH wp_remote_post() SEE: https://developer.wordpress.org/plugins/http-api/#posting-data-to-an-api
     */

    public function sendTask( $form_data ) {

        /**
         * Field ID => Error Message
         */
        $required_fields = [
            'ctf_validate' => 'Session Expired or Unauthorized form submission.',
            'assigner' => 'Name must be filled.', // Your Name
            'title' => 'Task Title must be filled.', // Title of the Task
            'priority' => 'Priority must be filled.', // Priority Level of the Task
            'desc'  => 'Task Description must be filled.', // Task Description
            'submit' => 'Task not submitted.', // The submit button
        ];

        // Cookie Validation
        $cookie_key = 'Now listen here, you hacking bois can just get along little doggie!';
        $cookie = hash( 'ripemd160', $_SERVER['HTTP_USER_AGENT'] . $cookie_key );

        if ( $cookie !== $form_data['ctf_validate'] || $cookie !== $_COOKIE['ctf_session'] || ! isset( $form_data['ctf_validate'] ) ) :
            echo "<script>alert('Session Expired or Unauthorized form submission.');</script>";
            return;
        endif;

        /**
         * Validate the form fields if isset and not empty continue, else return.
         * Validate the session cookie, must be present and correct. Expires after 5 minutes.
         */
        foreach ( $required_fields as $field_id => $field_label ) :
            if ( ! isset( $form_data[$field_id] ) || empty( $form_data[$field_id] ) ) :
                echo "<script>confirm('{$field_label}');</script>";
                return;
            endif;
        endforeach;

        /**
         * Initiate the CUrl Request or API Call
         */
        $ch = curl_init();

        /**
         * Set the enpoint of the API Call
         */
        curl_setopt($ch, CURLOPT_URL, "https://api.clickup.com/api/v2/list/{$this->listID}/task");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        /**
         * Instantiating Post Data to be Sent
         */
        curl_setopt($ch, CURLOPT_POST, TRUE);

        /**
         * Field Formatting
         */
        $content_format = $this->makeTaskContent( $form_data );
        $content = ( isset( $form_data['desc'] ) ) ? $content_format . "\n### Task Description:\n" . wp_strip_all_tags( $form_data['desc'] ) : '';

        $assignees = [];

        foreach ( $form_data['assignees'] as $assignee ) :
            array_push( $assignees, (int)$assignee );
        endforeach;

        if ( isset( $form_data['duedate'] ) ) :
            $duedate_create = date_create( $form_data['duedate'] );
            $timezone_offset = ( get_option('gmt_offset') * -1 ) * 3600;
            $duedate = ( strtotime( date_format( $duedate_create, 'r' ) ) + $timezone_offset ) * 1000;
        else:
            $duedate = null;
        endif;

        $custom_fields = [];
        foreach ( $this->getCustomFields() as $c_field ) :
            if ( isset( $c_field['posts_to'] ) && !empty( $c_field['posts_to'] ) ) :
                $custom_field_data = [ 'id' => $c_field['id'], 'value' => $form_data["{$c_field['id']}"] ];
                array_push( $custom_fields, $custom_field_data );
            endif;
        endforeach;

        /** 
         * Field Mapping:
         */
        $map_array = [
            "name" => sanitize_text_field( $form_data['title'] ),
            "markdown_content" => stripslashes( sanitize_textarea_field( $content ) ),
            "assignees" => $assignees,
            "priority" => (int)$form_data['priority'],
            "due_date" => $duedate,
            "due_date_time" => true,
            "notify_all" => true,
            "parent" => null,
            "links_to" => null,
            'custom_fields' => $custom_fields,
        ];

        $post_fields = json_encode( $map_array );

        /**
         * Set the Post Field Options
         */
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields );

        /**
         * Set the API Call Headers
         */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: {$this->token}",
            "Content-Type: application/json"
        ));

        /**
         * The response data
         */
        $response = curl_exec($ch); // Execute the API Call
        curl_close($ch); // Reset the request

        $return_data = json_decode( $response, true );

        /**
         * Response Message
         */
        if ( ! isset( $return_data['err'] ) ) :
            echo '<div class="ctf-submit-success">Created Task Successfully</div>';
        else:
            echo "<div class='ctf-submit-error'><strong>Failed to create task. Error:</strong> {$return_data['err']} - {$return_data['ECODE']}</div>";
        endif;
    }
}
