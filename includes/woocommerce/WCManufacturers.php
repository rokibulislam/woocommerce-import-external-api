<?php 

namespace WCMystore\WooCommerce;


class WCManufacturers {
	
	public function create( $args = [] ) {
		error_log('call WCManufacturers');
		$attributes  = $args['attributes'];
   		$name 		 = $attributes['name'];
   		$slug 		 = $attributes['slug']['no'];
   		$description = $attributes['description']['no'];

   		$data = [];
   		$data['description'] = $description;
   		$data['slug'] = $slug;

   		if( !term_exists( $name, 'product_manufacture' ) ) {
		   	$term = wp_insert_term( $name, 'product_manufacture', $data );
        	update_term_meta( $term['term_id'], 'mystore_product_manufacture_id', $args['id'] );
		} else {
			$term = get_term_by( 'name', $name, 'product_manufacture' );
			update_term_meta( $term->term_id, 'mystore_product_manufacture_id', $args['id'] );
		}

	    $image = $attributes['image'];

	    if( $image != null ){
	    	$this->attachImage( $image, $term );
	    }

		return $term;
	}

	public function attachImage( $image, $term ) {
        $shop    = mystore_get_options( 'mystore_fields', 'mystore_name');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $upload_dir = wp_upload_dir();
        
        $image_url  = "https://{$shop}.mystore4.no/users/{$shop}_mystore_no/images/$image";
        $image_data = file_get_contents( $image_url);
        $filename   = $upload_dir['basedir'] . '/' . strtotime("now") . $image ;
        
        file_put_contents( $filename, $image_data);
        
        $wp_filetype = wp_check_filetype( $filename, null );

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name( basename( $filename ) ),
            'post_content' => '',
            'post_status' => 'inherit'
        );


        if( is_array( $term ) ){
        	$term_id = $term['term_id'];
        } else if( is_object( $term ) ) {
        	$term_id = $term->term_id;
        }

        $attach_id = wp_insert_attachment( $attachment, $filename, $term_id );

     	update_term_meta( $term_id, 'thumbnail_id', $attach_id );
    }
}