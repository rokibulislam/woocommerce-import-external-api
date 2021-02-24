<?php 

namespace WCMystore;
use WCMystore\ImportMyStoreCategories;
use WCMystore\ImportMyStorePost;

class Importer {

	public function __construct() {
		
		$api_key = mystore_get_options( 'mystore_fields', 'mystore_token');
       	$shop    = mystore_get_options( 'mystore_fields', 'mystore_name');
       	$import    = get_option( 'mystore_import' );
		
       	if( !empty( $shop ) && !empty( $api_key ) && !$import  ) {
			
			add_action( 'init', function() {
				
				$this->get_products();
				$this->get_categories();
				$this->get_product_variants();
				$this->get_product_reviews();
				$this->get_product_tags();
				$this->get_orders();
				$this->get_shipping();
				$this->get_tax_classes();
				$this->get_suppliers();
				$this->get_manufacturers();
				$this->get_product_options();
				$this->get_product_specials();
				$this->get_customers();
				$this->get_customer_groups();
				// $this->get_customer();	

				update_option( 'mystore_import', true );
			});
		}

	}

	public function get_products() {
	 
	 	 	
	  	$products = wcystore()->http->get( '/products');

	  	if ( is_wp_error( $products ) ) {
	  		return ;
	  	}

	  	$importmystorePost = new ImportMyStorePost();
	  	
	  	if( isset( $products['data'] ) && !empty( $products['data'] ) ) {

		  	foreach ( $products['data'] as $product ) {
		  		
		  		$importmystorePost->push_to_queue( $product );

		  	/*
		  		$product_id = $product["id"];

		  		wcystore()->wc_products->create( $product );
		  	
		  		$categories = wcystore()->http->get( "/products/{$product_id}/categories");

				if( isset( $categories['data'] ) && !empty( $categories['data'] ) ) {
				
					foreach ( $categories['data'] as $category ) {

						$attributes  = $category['attributes'];
				   		$name 		 = $attributes['name']['no'];
				   		$slug 		 = $attributes['slug']['no'];
				   		$description = $attributes['description']['no'];

				   		if( !term_exists( $name, 'product_cat' ) ) {
						   	$term = wp_insert_term( $name, 'product_cat' );
							wp_set_post_terms( $product_id, $term['term_id'],'product_cat' );
							update_term_meta( $term['term_id'], 'mystore_product_cat_id', $category['id'] );
						} else {
							$term = get_term_by('name', $name, 'product_cat');
							wp_set_post_terms( $product_id, $term->term_id,'product_cat' );
							update_term_meta( $term->term_id, 'mystore_product_cat_id', $category['id'] );
						}
					}
				}
			*/
		  	}

		  	$importmystorePost->save()->dispatch();
		
		}
	
	}

	public function get_categories() {
	    
	    $categories = wcystore()->http->get( '/categories');

	    $importmystoreCategories = new ImportMyStoreCategories();

	    if( isset( $categories['data'] ) ) {

		    foreach ( $categories['data'] as $category ) {

		    	$importmystoreCategories->push_to_queue($category);
		   		
		  //  		$attributes  = $category['attributes'];
		  //  		$name 		 = $attributes['name']['no'];
		  //  		$slug 		 = $attributes['slug']['no'];
		  //  		$description = $attributes['description']['no'];

		  //  		if( !term_exists( $name, 'product_cat' ) ) {

				//    	$term = wp_insert_term( $name, 'product_cat', [
				//    		'description' => $description,
	   //      			'slug'        => $slug
				//    	] );
				// }
		   	}

		   	$importmystoreCategories->save()->dispatch();
		}
	}

	public function get_product_variants() {
	   $product_variants = wcystore()->http->get( '/product-variants');
	}

	public function get_product_reviews() {
	  $product_reviews  = wcystore()->http->get( '/product-reviews');
	}

	public function get_product_tags() {
	    $product_tags = wcystore()->http->get( '/product-tags' );

	  	foreach ($product_tags['data'] as $category ) {

	   	}
	}

	public function get_orders( ) {
	  $orders = wcystore()->http->get( '/orders' );
	}

	public function get_shipping() {
		$shipping = wcystore()->http->get( '/shipping' );
	}

	public function get_tax_classes() {
		$tax_classes = wcystore()->http->get( '/tax-classes' );
	}

	public function get_suppliers() {
		$suppliers = wcystore()->http->get( '/suppliers' );
	}

	public function get_manufacturers() {
		$manufacturers = wcystore()->http->get( '/manufacturers' );
	}

	public function get_product_options() {
		$product_options = wcystore()->http->get( '/product-options' );
	}

	public function get_product_specials() {
		$product_specials = wcystore()->http->get( '/product-specials' );
	}

	public function get_customers() {
		$customers = wcystore()->http->get( '/customers' );

		if( !empty( $customers['data '] ) ) {
			foreach ( $customers['attributes'] as $customer ) {
				wcystore()->wc_products->create( $product );	
			}
		}
	}

	public function get_customer($id) {
		$customers = wcystore()->http->get( '/customers',  [ 'id' => $id ] );
	}

	public function get_customer_groups() {
		$customer_groups = wcystore()->http->get( '/customer-groups' );
	}
}