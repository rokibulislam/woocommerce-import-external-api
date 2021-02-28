<?php 
namespace WCMystore\WooCommerce;

class WCProductsOption {

	public function create( $args = [] ) {
		$attribute = $args['attributes'];
		$name = $attribute['name']['no'];
		
		if ( ! in_array( $name, wc_get_attribute_taxonomies(), true ) ) {
			wc_create_attribute( [ 'name' => $name ] );
		}
	}	

	public function CreateValue( $args = [] ) {
		$attribute = $args['attributes'];
		$name = $attribute['name']['no'];
	}

}