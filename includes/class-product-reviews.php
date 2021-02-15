<?php 

namespace WCMystore;

class ProductReviews {

	public function __construct() {
		add_action( 'wp_insert_comment', [ $this, 'sync_comment_save' ], 10, 2 );
		add_action( 'deleted_comment', [ $this, 'sync_comment_delete' ], 10, 2 );	
		add_action( 'edit_comment',[ $this, 'sync_comment_update' ], 10, 2 );	
	}

	public function sync_comment_save( $id, $comment  ) {

	}

	public function sync_comment_update( $id, $comment  ) {

	}

	public function sync_comment_delete( $id, $comment  ) {

	}
}