<?php

/**
 * Class to perform a GET Method API Call
 *
 * @link       https://www.apdevops.com/
 * @since      1.0.0
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/includes/
 */

class ctfAPICall extends ctfConfig {
    
    public $prop, $method;
    protected $token;

    public function __construct( $prop ) {
        $this->prop = $prop;
        parent::__construct();
    }

    public function response() {
        $url = 'https://app.clickup.com/api/v2/' . $this->prop . '/';
        $args = array(
            'headers' => array(
                'Authorization' => $this->token
            )
        );
        $response = wp_remote_get( $url, $args );
        return json_decode( wp_remote_retrieve_body( $response ), true );
    }
}
