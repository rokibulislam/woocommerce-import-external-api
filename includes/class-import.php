<?php 

namespace WCMystore;

class Importer {

	public function __construct() {
		// $this->get_products();
		// $this->get_categories();
		// $this->get_product_variants();
		// $this->get_product_reviews();
		// $this->get_product_tags();
		// $this->get_orders();
		// $this->get_shipping();
		// $this->get_tax_classes();
		// $this->get_suppliers();
		// $this->get_manufacturers();
		// $this->get_product_options();
		// $this->get_product_specials();
		// $this->get_customers();
		// $this->get_customer_groups();
		$this->get_customer(1);
	}

	public function get_products() {
	  $products = wcystore()->http->get( '/products');

	  if( !empty( $products['data'] ) ) {
	  
		  foreach ( $products['data'] as $product ) {
		  
		  	$attributes 	= $product['attributes'];
		  	$price 			= isset( $attributes['price'] ) ? $attributes['price'] : 0.00;
		  	$name 			= isset( $attributes['name']['no'] ) ? $attributes['name']['no'] : '';
		  	$slug 			= isset( $attributes['slug']['no'] ) ? $attributes['slug']['no'] : '';
		  	$description 	= isset( $attributes['description']['no'] ) ? $attributes['description']['no'] : '';


		  	$postarr = array(
			  'post_title'    => $name,
			  'post_content'  => $description,
			  'post_status'   => 'draft',
			  'post_type'	  => 'product'
			);
	 	
		  	$post_id = wp_insert_post( $postarr );

		  	update_post_meta( $post_id, 'mystore_product_id', $product['id']  );

		  }
		}
	
	}

	public function get_categories() {
	    $categories = wcystore()->http->get( '/categories');
	    
	    foreach ($categories['data'] as $category ) {
	   		$attributes  = $category['attributes'];
	   		$name 		 = $attributes['name']['no'];
	   		$slug 		 = $attributes['slug']['no'];
	   		$description = $attributes['description']['no'];

	   		if( !term_exists( $name, 'product_cat' ) ) {

			   	// $term = wp_insert_term( $name, 'product_cat' );
			}

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
				$name 	  		  = $customer['name'];
				$lastname 		  = $customer['lastname'];
				$email 	  		  = $customer['email'];
				$phone 	  		  = $customer['phone'];
				$billing_email 	  = $customer['billing_email'];
				$dob 	  		  = $customer['dob'];
				$address_name 	  = $customer['address_name'];
				$address_name 	  = $customer['address_lastname'];
				$address_address  = $customer['address_address'];
				$address_zipcode  = $customer['address_zipcode'];
				$address_city  	  = $customer['address_city'];
				$address_country  = $customer['address_country'];

				$billing_address_name  	   = $customer['billing_address_name'];
				$billing_address_lastname  = $customer['billing_address_lastname'];
				$billing_address_address   = $customer['billing_address_address'];
				$billing_address_zipcode   = $customer['billing_address_zipcode'];
				$billing_address_city  	   = $customer['billing_address_city'];
				$billing_address_country   = $customer['billing_address_country'];
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