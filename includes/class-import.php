<?php 

namespace WCMystore;

use WCMystore\Import\ImportMyStoreCategories;
use WCMystore\Import\ImportMyStorePost;
USE WCMystore\Import\ImportMyStoreManufacturers;
USE WCMystore\Import\ImportMyStoreProductOption;
USE WCMystore\Import\ImportMyStoreProductValue;

class Importer {

	public function __construct() {
		
		$api_key = mystore_get_options( 'mystore_fields', 'mystore_token');
       	$shop    = mystore_get_options( 'mystore_fields', 'mystore_name');
       	$import  = get_option( 'mystore_import' );


       	if( !empty( $shop ) && !empty( $api_key ) && !$import  ) {
			
			add_action( 'init', function() {
				
				// $this->get_products();
				// $this->get_categories();
				// $this->get_manufacturers();
				// $this->get_option();
				// $this->get_suboption();
				// $this->get_option_values(); 
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
		   	}

		   	$importmystoreCategories->save()->dispatch();
		}
	}

	public function get_manufacturers() {
	    $manufacturers = wcystore()->http->get( '/manufacturers');

	    $importmystoreManufacturers = new ImportMyStoreManufacturers();

	    if( isset( $manufacturers['data'] ) ) {

		    foreach ( $manufacturers['data'] as $manufacturer ) {
		    	$importmystoreManufacturers->push_to_queue( $manufacturer );
		   	}

		   	$importmystoreManufacturers->save()->dispatch();
		}
	}

	public function get_option() {
		$attribute_values = wcystore()->http->get( '/product-options' );

		$importmystoreProductOption = new ImportMyStoreProductOption();

		if( isset( $attribute_values['data'] ) ) {
	
			foreach ( $attribute_values['data'] as $attribute_value ) {
				$importmystoreProductOption->push_to_queue( $attribute_value );
			}
			
		   	$importmystoreProductOption->save()->dispatch();
		}

	}

	public function get_suboption() {
		$suboptions = wcystore()->http->get( '/product-suboptions' );

		// $importmystoreProductOption = new ImportMyStoreProductOption();

		if( isset( $suboptions['data'] ) ) {
	
			foreach ( $suboptions['data'] as $suboption ) {
				
			}
			
		}

	}

	public function get_option_values() {
		$attribute_values = wcystore()->http->get( '/product-option-values' );

		$importmystoreProductValue = new ImportMyStoreProductValue();

		if( isset( $attribute_values['data'] ) ) {
	
			foreach ( $attribute_values['data'] as $attribute_value ) {
				$importmystoreProductValue->push_to_queue( $attribute_value );
			}
			
		   	$importmystoreProductValue->save()->dispatch();
		}

	}
}