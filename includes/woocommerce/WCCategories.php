<?php 

namespace WCMystore\WooCommerce;


class WCCategories {


	public function create( $args = [] ) {
		$attributes  = $args['attributes'];
		$name 		 = $attributes['name']['no'];
		$slug 		 = $attributes['slug']['no'];
		$description = $attributes['description']['no'];

		$data = [];
		$data['description'] = $description;
		$data['slug'] = $slug;

		if( isset( $args['relationships']['parent']['data'] ) && $args['relationships']['parent']['data'] != null ) {
			$data['parent'] = $args['relationships']['parent']['data']['id'];
		}

		if( !term_exists( $name, 'product_cat' ) ) {
	   	$term = wp_insert_term( $name, 'product_cat', $data );
      	// update_term_meta( $term['term_id'], 'mystore_product_cat_id', $args['id'] );
	   } else {
		   $term = get_term_by( 'name', $name, 'product_cat' );
		   // update_term_meta( $term->term_id, 'mystore_product_cat_id', $args['id'] );
	   }

      return $term;
	}
}