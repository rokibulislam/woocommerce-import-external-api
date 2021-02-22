<?php 

namespace WCMystore;

class Importer {

	public function __construct() {
		$this->get_products();
	}

	public function get_products() {
	  error_log(print_r( wcystore()->http->get( '/products'), true ) );
	}
}