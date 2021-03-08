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
			
			add_action( 'admin_init', function() {
				$this->get_categories();
				$this->get_manufacturers();
				$this->get_products();

				// $this->get_option();
				// $this->get_suboption();
				// $this->get_option_values();
 
				update_option( 'mystore_import', true );
			});
		}

	}

	public function getCategory() {
		$categories = wcystore()->http->get( "/categories/75/");

	  	if ( is_wp_error( $categories ) ) {

	  		return ;
	  	}

	  	if( $categories['data']['relationships']['parent']['data'] != null ) {
	  		$parent = $categories['data']['relationships']['parent']['data']['id'];
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
				$product_id = $product['id'];
				// $importmystorePost->push_to_queue( $product );
  				WC()->queue()->schedule_single( time(), 'wc_mystore_product_import', array( 'product' => $product ) );
		  	}
		

		  	$importmystorePost->save()->dispatch();
		}
	
	}

	public function get_categories() {
	    $categories = wcystore()->http->get( '/categories');

	    if ( is_wp_error( $categories ) ) {
	  		return ;
	  	}

	    $importmystoreCategories = new ImportMyStoreCategories();

	    if( isset( $categories['data'] ) ) {

		    foreach ( $categories['data'] as $category ) {
		    	// $importmystoreCategories->push_to_queue($category);
	  			WC()->queue()->schedule_single( time(), 'wc_mystore_category_import', array( 'category' => $category ) );
		   	}

		   	$importmystoreCategories->save()->dispatch();
		}
	}

	public function get_manufacturers() {
	    $manufacturers = wcystore()->http->get( '/manufacturers');

	    if ( is_wp_error( $manufacturers ) ) {
	  		return ;
	  	}

	    $importmystoreManufacturers = new ImportMyStoreManufacturers();

	    if( isset( $manufacturers['data'] ) ) {

		    foreach ( $manufacturers['data'] as $manufacturer ) {
		    	// $importmystoreManufacturers->push_to_queue( $manufacturer );
	  			WC()->queue()->schedule_single( time(), 'wc_mystore_manufacturer_import', array( 'manufacturer' => $manufacturer ) );
		   	}

		   	$importmystoreManufacturers->save()->dispatch();
		}
	}

	public function get_option() {
		$attribute_values = wcystore()->http->get( '/product-options' );

		if ( is_wp_error( $attribute_values ) ) {
	  		return ;
	  	}

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

		if ( is_wp_error( $suboptions ) ) {
	  		return ;
	  	}


		if( isset( $suboptions['data'] ) ) {
	
			foreach ( $suboptions['data'] as $suboption ) {
				
			}
			
		}

	}

	public function get_option_values() {
		$attribute_values = wcystore()->http->get( '/product-option-values' );

		if ( is_wp_error( $attribute_values ) ) {
	  		return ;
	  	}

		$importmystoreProductValue = new ImportMyStoreProductValue();

		if( isset( $attribute_values['data'] ) ) {
	
			foreach ( $attribute_values['data'] as $attribute_value ) {
				$importmystoreProductValue->push_to_queue( $attribute_value );
			}
			
		   	$importmystoreProductValue->save()->dispatch();
		}

	}
}