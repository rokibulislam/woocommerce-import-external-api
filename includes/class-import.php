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
       			// $this->getCategory();
				// $this->get_categories();
				// $this->get_manufacturers();
				$this->get_products();

				// $this->get_option();
				// $this->get_suboption();
				// $this->get_option_values();
 
				update_option( 'mystore_import', true );
			});
		}

	}

	public function getCategory() {
		$products = wcystore()->http->get( "/products/266/");

	  	if ( is_wp_error( $products ) ) {

	  		return ;
	  	}

	  	$importmystorePost = new ImportMyStorePost();
	  	
	  	if( isset( $products['data'] ) && !empty( $products['data'] ) ) {

	  		$product = wcystore()->wc_products->create( $products['data'] );
	  		$product_id = $products['data']['id'];
	  		$shop    = mystore_get_options( 'mystore_fields', 'mystore_name');
	  		$image = $products['data']['attributes']['image'];

	  		if( $image != null ){
	  			$upload_dir = wp_upload_dir();
	  			$image_url = "https://{$shop}.mystore4.no/users/{$shop}_mystore_no/images/$image";
	  			$image_data = file_get_contents( $image_url);
	  			$filename = $upload_dir['basedir'] . '/' . strtotime("now") . $image ;
	  			file_put_contents( $filename, $image_data);
	  			$wp_filetype = wp_check_filetype( $filename, null );
	  			$name = basename($filename);
	  			error_log(print_r($wp_filetype,true));
	  			error_log(print_r($name,true));
/*
	  			if ( true === function_exists( 'curl_init' ) ) {
					  $save = $upload_dir['basedir'] . '/rokib.jpg';
					  $start = curl_init($image_url);
					  // curl_setopt($start, CURLOPT_URL, $image_url);
					  curl_setopt($start, CURLOPT_HEADER, 0);
					  curl_setopt($start, CURLOPT_RETURNTRANSFER, 1);
					  curl_setopt($start, CURLOPT_BINARYTRANSFER, 3);
					  $file_data = curl_exec($start);
					  curl_close($start);

					  $fp = fopen($save, 'x');
					  fwrite($fp, $file_data);
					  fclose($fp);
				}
				*/
	  		
    
			    $attachment = array(
			        'post_mime_type' => $wp_filetype['type'],
			        'post_title' => sanitize_file_name($name),
			        'post_content' => '',
			        'post_status' => 'inherit'
			    );

			    $attach_id = wp_insert_attachment( $attachment, $filename, $product->get_id() );
			    require_once(ABSPATH . 'wp-admin/includes/image.php');
			    $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
			    $res2= set_post_thumbnail( $product->get_id(), $attach_id );

	  		}
	  	/*
	  		$categories = wcystore()->http->get( "/products/{$product_id}/categories");

			if( isset( $categories['data'] ) && !empty( $categories['data'] ) ) {

				$terms_list = [];
			
				foreach ( $categories['data'] as $category ) {

	  				$term = wcystore()->wc_categories->create( $category );

      				if( is_object( $term ) ) {
      					array_push($terms_list, $term->term_id);
      					update_term_meta( $term->term_id, 'mystore_product_cat_id', $category['id'] );
      				} else if( is_array( $term ) ) {
      					array_push($terms_list, $term['term_id']);
      				}
				}

				wp_set_post_terms( $product->get_id(), $terms_list,'product_cat' );
			}
		*/
		}
	}

	public function get_products() {
	 
	  	$products = wcystore()->http->get( '/products');

	  	if ( is_wp_error( $products ) ) {
	    	// error_log(print_r($products,true));
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

	    if ( is_wp_error( $categories ) ) {
	    	error_log(print_r($categories,true));
	  		return ;
	  	}

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

	    if ( is_wp_error( $manufacturers ) ) {
	  		return ;
	  	}

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

		if ( is_wp_error( $attribute_values ) ) {
	    	error_log(print_r($attribute_values,true));

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
	    	error_log(print_r($suboptions,true));

	  		return ;
	  	}

		// $importmystoreProductOption = new ImportMyStoreProductOption();

		if( isset( $suboptions['data'] ) ) {
	
			foreach ( $suboptions['data'] as $suboption ) {
				
			}
			
		}

	}

	public function get_option_values() {
		$attribute_values = wcystore()->http->get( '/product-option-values' );

		if ( is_wp_error( $attribute_values ) ) {
	    	error_log(print_r($attribute_values,true));

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