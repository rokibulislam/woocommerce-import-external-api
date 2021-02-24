<?php 

namespace WCMystore;

class ProductCategories {

	public function __construct() {
		add_action ( 'edited_product_cat', [ $this, 'on_category_edit' ], 10,2 ); 
		add_action(  'create_product_cat',  [ $this, 'on_category_create' ], 10, 2 );
		add_action ( 'delete_product_cat', [ $this, 'on_category_delete' ], 10, 4 );
	}

	public function on_category_create( $term_id, $tt_id ) {
		$data = $this->get_category( $term_id, $tt_id, 'product_cat' );
		$response = wcystore()->http->post( '/categories', $data );
		error_log(print_r($response,true));
		if( $response['data'] ) {
			update_term_meta( $term_id, 'mystore_category_id', $response['data']['id'] );
		}
	}

	public function on_category_edit( $term_id, $tt_id ) {
		$data = $this->get_category( $term_id, $tt_id, 'product_cat' );
		$id = get_term_meta( $term_id,'mystore_category_id', true );

		if( !empty( $id ) ) {
			$data['data']['id'] = $id;
		
			$response = wcystore()->http->put( "/categories/{$id}", $data, [ 'id' => $id ] );
		}
	}

	public function on_category_delete( $term, $tt_id, $deleted_term, $object_ids ) {
		$id = get_term_meta( $term,'mystore_category_id', true );
		
		if( !empty( $id ) ) {
			$response = wcystore()->http->delete( "/categories/{$id}");
			error_log(print_r($response,true));
		}
	}

	public function get_category( $term_id, $tt_id, $type ) {
		$product_cat  = get_term_by( 'id', $term_id, $type );
		$thumbnail_id = get_term_meta( $product_cat->term_id, 'thumbnail_id', true );
        $image 		  = wp_get_attachment_url( $thumbnail_id );

		$data = [
			'data' => [
				"type"  => "categories",
				"attributes" => [	
					"image" =>  $image,

					"name"	=> [
						"no"  => $product_cat->name
					],

					"slug" => [
						"no"  => $product_cat->slug
					],

					"description" => [
						"no"  => $product_cat->description
					]
				]
			]
		];

		return $data;
	}
}