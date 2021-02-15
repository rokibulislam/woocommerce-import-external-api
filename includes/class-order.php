<?php 

namespace WCMystore;

class Order {

	public function __construct() {
		add_action( 'woocommerce_thankyou', [ $this, 'wc_order_received' ], 10, 1 );
        add_action( 'woocommerce_order_status_changed', [ $this, 'wc_order_status_updated' ], 10, 3 );

	}


	public function wc_order_received( $order_id ) {

	}

	public function wc_order_status_updated( $order_id, $old_status, $new_status ) {

	}
}