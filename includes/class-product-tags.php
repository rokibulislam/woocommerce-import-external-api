<?php 

namespace WCMystore;

class ProductTags {

	public function __construct() {
		add_action( "create_product_tag",  [ $this, 'sync_tag_save' ], 10, 2 );
	}

	public function sync_tag_save( $term_id, $tt_id ) {
		error_log('tag save');	
	}
}