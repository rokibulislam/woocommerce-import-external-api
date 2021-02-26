<?php 

namespace WCMystore\WooCommerce;


class WCManufacturers {
	
	public function create( $args = [] ) {
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
	}
}