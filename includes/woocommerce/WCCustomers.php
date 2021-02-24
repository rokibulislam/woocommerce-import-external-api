<?php 

namespace WCMystore\WooCommerce;


class WCCustomers {

	public function __construct() {
		
	}

	public function all( $args ) {

	}

	public function create( $customer ) {
		
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

	public function update() {
		
	}
}