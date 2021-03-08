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

// Setting a custom timeout value for cURL. Using a high value for priority to ensure the function runs after any other added to the same action hook.
add_action('http_api_curl', 'sar_custom_curl_timeout', 9999, 1);
function sar_custom_curl_timeout( $handle ){
    curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
    curl_setopt( $handle, CURLOPT_TIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
}

// Setting custom timeout for the HTTP request
add_filter( 'http_request_timeout', 'sar_custom_http_request_timeout', 9999 );
function sar_custom_http_request_timeout( $timeout_value ) {
    return 30; // 30 seconds. Too much for production, only for testing.
}

// Setting custom timeout in HTTP request args
add_filter('http_request_args', 'sar_custom_http_request_args', 9999, 1);
function sar_custom_http_request_args( $r ){
    $r['timeout'] = 30; // 30 seconds. Too much for production, only for testing.
    return $r;
}



add_action( 'wc_mystore_product_import', 'wc_mystore_product_import', 10, 1 );

function wc_mystore_product_import( $product ) {
    $pro = wcystore()->wc_products->create( $product );
}


add_action( 'wc_mystore_manufacturer_import', 'wc_mystore_manufacturer_import', 10, 1 );


function wc_mystore_manufacturer_import( $manufacturer ) {
    wcystore()->wc_manufacturers->create( $manufacturer );
}

add_action( 'wc_mystore_category_import', 'wc_mystore_category_import', 10, 1 );


function wc_mystore_category_import( $category ) {
    wcystore()->wc_categories->create( $category );
}