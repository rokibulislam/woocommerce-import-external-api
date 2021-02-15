<?php 

namespace WCMystore;

class Customer {

	public function __construct() {

	 	add_action( 'woocommerce_created_customer', [ $this, 'customer_create' ], 10, 3 );
	}

	public function customer_create( $customer_id, $new_customer_data, $password_generated ) {

	}
}