<?php 

namespace WCMystore;

class ProductCategories {

	public function __construct() {
		add_action( "create_product_cat",  [ $this, 'sync_category_save' ], 10, 2 );
	}

	public function sync_category_save( $term_id, $tt_id ) {
		
	}
}