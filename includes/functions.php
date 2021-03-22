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



add_filter( 'manage_product_posts_columns', 'set_custom_edit_product_columns' );

add_filter( 'manage_edit-product_sortable_columns', 'set_custom_edit_product_columns' );
 
function set_custom_edit_product_columns($columns) {
    $columns['mystore'] = __( 'MyStore Id', 'your_text_domain' );
    $columns['manufacture'] = __( 'Manufacture', 'your_text_domain' );
 
    return $columns;
}

function product_custom_column_values( $column, $post_id ) {
    switch ( $column ) {
        case 'mystore':
            echo get_post_meta( $post_id ,'mystore_product_id', true );
        case 'manufacture':
            $terms = wp_get_post_terms( $post_id, 'product_manufacture' );
            foreach ( $terms as $key => $term ) {
                echo sprintf( '<a href="%1$s"> %2$s </a>', get_term_link( $term ), $term->name  );
            }
        break;
    }
}

add_action( 'manage_product_posts_custom_column' , 'product_custom_column_values', 10, 2 );



add_filter( 'manage_edit-product_cat_columns', 'set_custom_edit_product_cat_columns' );



function set_custom_edit_product_cat_columns( $columns ) {
    $columns['mystore_cat'] = __( 'MyStore Category Id', 'your_text_domain' );
 
    return $columns;
}

add_action( 'manage_product_cat_custom_column', 'wh_customFieldsListDisplay' , 10, 3); 


function wh_customFieldsListDisplay( $columns, $column, $id ) {
    if ( 'mystore_cat' == $column ) {
        $columns = esc_html( get_term_meta($id, 'mystore_product_cat_id', true) );
    }

    return $columns;
}


add_filter( 'manage_edit-product_manufacture_columns', 'set_custom_edit_product_manufacture_columns' );

function set_custom_edit_product_manufacture_columns( $columns ) {
    $columns['mystore_manufacture'] = __( 'MyStore Manufacture Id', 'your_text_domain' );
 
    return $columns;
}

add_action( 'manage_product_manufacture_custom_column', 'wh_product_manufacture' , 10, 3); 


function wh_product_manufacture( $columns, $column, $id ) {
    if ( 'mystore_manufacture' == $column ) {
        $columns = esc_html( get_term_meta($id, 'mystore_product_manufacture_id', true) );
    }

    return $columns;
}



add_action( 'woocommerce_product_options_general_product_data', 'save_location_field' );


function my_woo_custom_fields() {

    $field = array(
        'id' => 'product_location',
        'label' => __( 'Product Location', 'textdomain' ),
    );
  
   woocommerce_wp_text_input( $field );
}

add_action( 'woocommerce_process_product_meta', 'save_location_field' );

function save_custom_field( $post_id ) {
  
  $product_location = isset( $_POST['product_location'] ) ? $_POST['product_location'] : '';
  
  $product = wc_get_product( $post_id );
  $product->update_meta_data( 'product_location', $product_location );
  $product->save();

}

