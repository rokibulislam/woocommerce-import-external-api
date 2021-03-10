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
				// $this->getProduct();
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


	public function getProduct() {
		$product = wcystore()->http->get( "/products/2576/");

		if ( is_wp_error( $product ) ) {
	  		return ;
	  	}

	  	$pro 	 = wcystore()->wc_products->create( $product['data'] );
	  	$post_id = $pro->get_id();

	  	$gallery = [];
			
		$image2 = $product['data']['attributes']['image2'];
		$image3 = $product['data']['attributes']['image3'];
		$image4 = $product['data']['attributes']['image4'];
		$image5 = $product['data']['attributes']['image5'];
		$image6 = $product['data']['attributes']['image6'];
		$image7 = $product['data']['attributes']['image7'];
		$image8 = $product['data']['attributes']['image8'];

		if( $image2 != null ){
			$gallery[] = $this->attachGallery( $image2, $post_id );
		}

		if( $image3 != null ){
			$gallery[] = $this->attachGallery( $image3, $post_id );
		}

		if( $image4 != null ){
			$gallery[] = $this->attachGallery( $image4, $post_id );
		}

		if( $image5 != null ){
			$gallery[] = $this->attachGallery( $image5, $post_id );
		}

		if( $image5 != null ){
			$gallery[] = $this->attachGallery( $image5, $post_id );
		}

		if( $image6 != null ){
			$gallery[] = $this->attachGallery( $image5, $post_id );
		}

		if( $image7 != null ){
			$gallery[] = $this->attachGallery( $image7, $post_id );
		}

		if( $image8 != null ){
			$gallery[] = $this->attachGallery( $image8, $post_id );
		}

		$pro->set_gallery_image_ids( $gallery );

		$pro->save();
	}

	public function attachGallery( $image, $post_id ) {
        $shop    = mystore_get_options( 'mystore_fields', 'mystore_name');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $upload_dir = wp_upload_dir();
        
        $image_url  = "https://{$shop}.mystore4.no/users/{$shop}_mystore_no/images/$image";
        $image_data = file_get_contents( $image_url);
        $filename   = $upload_dir['basedir'] . '/' . strtotime("now") . $image ;
        
        file_put_contents( $filename, $image_data);
        
        $wp_filetype = wp_check_filetype( $filename, null );

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name( basename( $filename ) ),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $filename, $post_id );

        return $attach_id;
    }

	public function getCategory() {
		$category = wcystore()->http->get( "/categories/58/");

	  	if ( is_wp_error( $category ) ) {
	  		return ;
	  	}

	  	$term = wcystore()->wc_categories->create( $category['data'] );

	  	if( $category['data']['relationships']['parent']['data'] != null ) {
	  		$parent = $category['data']['relationships']['parent']['data']['id'];
	  	}

	  	$image = $category['data']['attributes']['image'];

	  	
	  	$this->attachImageCategory( $image, $term );
	}


	public function attachImageCategory( $image, $term ) {
        $shop    = mystore_get_options( 'mystore_fields', 'mystore_name');

        if( $image != null ){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $upload_dir = wp_upload_dir();
            
            $image_url  = "https://{$shop}.mystore4.no/users/{$shop}_mystore_no/images/$image";
            $image_data = file_get_contents( $image_url);
            $filename   = $upload_dir['basedir'] . '/' . strtotime("now") . $image ;
            
            file_put_contents( $filename, $image_data);
            
            $wp_filetype = wp_check_filetype( $filename, null );

            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name( basename( $filename ) ),
                'post_content' => '',
                'post_status' => 'inherit'
            );


            if( is_array( $term ) ){
            	$term_id = $term['term_id'];
            } else {
            	$term_id = $term->term_id;
            }

            $attach_id = wp_insert_attachment( $attachment, $filename, $term_id );

         	update_term_meta( $term_id, 'thumbnail_id', $attach_id );
        }
    }

	public function get_products() {
		$params = [
			'page' => [
				'number' => 31,
				'size'  => 50
			]
		];

	  	$products = wcystore()->http->get( '/products', $params );

	  	error_log(print_r($products,true));

	  	if ( is_wp_error( $products ) ) {
	  		return ;
	  	}

	  	$this->queuePost( $products );


	 //  	$importmystorePost = new ImportMyStorePost();
	  	
	 //  	if( isset( $products['data'] ) && !empty( $products['data'] ) ) {

	 //  		error_log('import start');
		 
		//   	foreach ( $products['data'] as $product ) {
		// 		$product_id = $product['id'];
		// 		$importmystorePost->push_to_queue( $product );
  // 				// WC()->queue()->schedule_single( time(), 'wc_mystore_product_import', array( 'product' => $product ) );
		//   	}
		

		//   	$importmystorePost->save()->dispatch();
		// }
		
	  	// $this->nextProduct( $products );

	}


	public function nextProduct( $products ) {
		
		if( isset($products['links']) && isset( $products['links']['next'] ) ) {
	  		$next = $products['links']['next'];
	  		$components = parse_url( $next );
	  		parse_str( $components['query'], $params );
	  		
	  		$nextproducts = wcystore()->http->get( '/products', $params );

	  		if ( is_wp_error( $nextproducts ) ) {
	  			return ;
	  		}

			$this->queuePost( $nextproducts );
	  		error_log('next product');
	  		$this->nextProduct( $nextproducts );
	  	}
	}


	public function queuePost( $products ) {
		
		$importmystorePost = new ImportMyStorePost();
	  	
	  	if( isset( $products['data'] ) && !empty( $products['data'] ) ) {

		  	foreach ( $products['data'] as $product ) {
				$product_id = $product['id'];
				$importmystorePost->push_to_queue( $product );
  				// WC()->queue()->schedule_single( time(), 'wc_mystore_product_import', array( 'product' => $product ) );
		  	}
		

		  	$importmystorePost->save()->dispatch();
		}
	}

	public function queueCategory( $categories ) {

		$importmystoreCategories = new ImportMyStoreCategories();

	    if( isset( $categories['data'] ) ) {

		    foreach ( $categories['data'] as $category ) {
		    	$importmystoreCategories->push_to_queue($category);
	  			// WC()->queue()->schedule_single( time(), 'wc_mystore_category_import', array( 'category' => $category ) );
		   	}

		   	$importmystoreCategories->save()->dispatch();
		}
	}

	public function get_categories() {
	    $categories = wcystore()->http->get( '/categories');

	    if ( is_wp_error( $categories ) ) {
	  		return ;
	  	}

	  	$this->queueCategory( $categories );

	  	$this->nextCategory( $categories );

	}



	public function nextCategory( $categories ) {
		
		if( isset( $categories['links'] ) && isset( $categories['links']['next'] ) ) {
	  		$next = $categories['links']['next'];
	  		$components = parse_url( $next );
	  		parse_str( $components['query'], $params );
	  		
	  		$nextcategories = wcystore()->http->get( '/categories', $params );

	  		if ( is_wp_error( $nextcategories ) ) {
	  			return ;
	  		}

	  		$this->queueCategory( $nextcategories );

	  		error_log('next Categories');
	  		
	  		$this->nextProduct( $nextcategories );
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
	  			// WC()->queue()->schedule_single( time(), 'wc_mystore_manufacturer_import', array( 'manufacturer' => $manufacturer ) );
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