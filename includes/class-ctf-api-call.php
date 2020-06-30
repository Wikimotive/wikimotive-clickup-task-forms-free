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

    public function __construct( $prop, $method, $return = false ) {
        $this->prop = $prop;
        $this->method = $method;
        $this->return = $return;
        parent::__construct();
    }

    public function response() {
        $curl = curl_init();
        curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.clickup.com/api/v2/' . $this->prop . '/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "{$this->method}",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: {$this->token}",
                ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode( $response, $this->return );
    }
}
