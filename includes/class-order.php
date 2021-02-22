<?php 

namespace WCMystore;

class Order {

	public function __construct() {
		add_action( 'woocommerce_thankyou', [ $this, 'wc_order_received' ], 10, 1 );
        add_action( 'woocommerce_order_status_changed', [ $this, 'wc_order_status_updated' ], 10, 3 );

        add_action( 'woocommerce_payment_complete',  [ $this, 'order_create' ] );
		add_action( 'woocommerce_order_refunded', [ $this, 'order_refunded' ] , 10, 2 ); 
		add_action( 'woocommerce_order_partially_refunded', [ $this, 'order_refunded' ], 10, 2 );

		add_action( 'woocommerce_restock_refunded_item', [ $this, 'restock_refunded_item' ], 10, 5);
	}


	public function wc_order_received( $order_id ) {

	}

	public function wc_order_status_updated( $order_id, $old_status, $new_status ) {

	}

	public function order_create( $order_id ) {

	}

	public function order_refunded( $order_id, $refund_id ) { 

	} 

	public function restock_refunded_item( $product_id, $old_stock, $new_stock, $order, $product ) {

	}
}