<?php 

namespace WCMystore;

class Assets {

	public function __construct() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ), 10 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 10 );

	}


	public function register_admin_scripts() {

	}

	public function enqueue_admin_scripts() {

	}
}