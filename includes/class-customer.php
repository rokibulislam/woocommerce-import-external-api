<?php 
namespace WCMystore;

class Customer {

	public function __construct() {
	 	add_action( 'woocommerce_created_customer', [ $this, 'customer_create' ], 10, 3 );
	 	add_action( 'woocommerce_update_customer', [ $this, 'customer_update' ] , 10, 1 ); 
	 	add_action( 'woocommerce_delete_customer', [ $this, 'customer_delete'], 10, 1 );
	}

	public function customer_create( $customer_id, $new_customer_data, $password_generated ) {

	}

	public function customer_update( $customer_id ) { 


	}

	public function delete_customer( $customer_id ) {

	}
}