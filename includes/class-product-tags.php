<?php 

namespace WCMystore;

class ProductTags {

	public function __construct() {
		add_action( 'create_product_tag', [ $this, 'on_tag_create' ], 10, 2 );
		add_action( 'edited_product_tag', [ $this, 'on_tag_edit' ], 10, 2 ); 
		add_action( 'delete_product_tag', [ $this, 'on_tag_delete' ], 10, 4 );
	}

	public function on_tag_create( $term_id, $tt_id ) {
		$product_cat = get_term_by( 'id', $term_id, 'product_tag' );
		$response = wcystore()->http->post( '/product-tags', $data );
	}

	public function on_tag_edit( $term_id, $tt_id ) {
		$product_cat = get_term_by( 'id', $term_id, 'product_tag' );
		$response = wcystore()->http->put( '/product-tags', $data );
	}

	public function on_tag_delete( $term, $tt_id, $deleted_term, $object_ids ) {
		
	}

	public function get_tag( $term_id, $tt_id, $type ) {
		$product_tag  = get_term_by( 'id', $term_id, $type );

		$data = [
			'data' => [
				"type"  => "product-tags",
				"attributes" => [	

				]
			]
		];

		return $data;
	}
}