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

function ctf_make_options( $data = false, $key = 'id', $value = 'name' ) {
    if ( is_array( $data ) ) :
        foreach ( $data as $data_type => $data_set ) :
            if ( is_array( $data_set ) ) :
                foreach ( $data_set as $property ) :
                    $options["{$property[$key]}"] = $property[$value];
                endforeach;
            endif;
        endforeach;
    endif;
    return $options;
}
