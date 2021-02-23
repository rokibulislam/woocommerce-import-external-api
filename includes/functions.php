<?php 


function mystore_get_settings( $key = '' ) {
    $settings = get_option( 'contactum_settings', [] );

    if ( empty( $key ) ) {
        return $settings;
    }

    if ( isset( $settings[ $key ] ) ) {
        return $settings[ $key ];
    }
}


function mystore_get_options( $option_name ,$key = '' ) {
    $settings = get_option( $option_name, [] );

    if ( empty( $key ) ) {
        return $settings;
    }

    if ( isset( $settings[ $key ] ) ) {
        return $settings[ $key ];
    }
}