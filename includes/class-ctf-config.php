<?php

/**
 * Class to get API call credentials
 * 
 * To be used in extended classes
 *
 * @link       https://www.apdevops.com/
 * @since      1.0.0
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/includes/
 */

class ctfConfig {

    protected $opts, $token;

    public function __construct() {
        $this->opts = get_option( 'ctf_options' );
        $this->token = $this->getToken();
    }

    protected function getToken() {
        return ( false !== $this->opts && isset( $this->opts['ctf_access_token'] ) && !empty( $this->opts['ctf_access_token'] ) ) ? $this->opts['ctf_access_token'] : false;
    }

}