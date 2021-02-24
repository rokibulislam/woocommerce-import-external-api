<?php 

namespace WCMystore\WooCommerce;


class WCOrders {

	protected $wc_products;

    public function __construct() {
        $this->wc_products = new WCOrderProducts();
    }
    

	public function all( $args ) {

	}

	public function get( $id ) {
		$order 		= wc_get_order( $id );
		$line_items = $order->get_items();

		foreach ( $line_items as $item ) {
			$product = $order->get_product_from_item( $item );
			$sku 	 = $product->get_sku();
			$total 	 = $order->get_line_total( $item, true, true );
			$subtotal = $order->get_line_subtotal( $item, true, true );
		}


		$order = new \WC_Order( $id );
        $date_completed = $order->get_date_completed();

        return [
            'id'                   => $order->get_id(),
            'parent_id'            => $order->get_parent_id(),
            'customer'             => $this->getCustomerInfo( $order ),
            'status'               => $order->get_status(),
            'currency'             => $order->get_currency(),
            'total'                => $order->get_total(),
            'payment_method_title' => $order->get_payment_method_title(),
            'date_created'         => $order->get_date_created()->format( 'Y-m-d H:m:s' ),
            'date_completed'       => $date_completed ? $date_completed->format( 'Y-m-d H:m:s' ) : '',
            'permalink'            => htmlspecialchars_decode( get_edit_post_link( $order->get_id() ) ),
            'products'             => $this->wc_products->get_ordered_products( $order ),
        ];

	}

    public function create( $order ) {
        // $attributes = $order['attributes'];
    }

    private function getCustomerInfo( $order ) {
        $user = $order->get_user();

        if ( $user ) {
            $customer = [
                'wp_user_id' => $user ? $user->id : '',
                'first_name' => $user ? $user->first_name : '',
                'last_name'  => $user ? $user->last_name : '',
                'email'      => $user ? ( $user->user_email ? $user->user_email : $order->get_billing_email() ) : '',
            ];
        } elseif ( intval( $order->get_parent_id() ) !== 0 ) {
            $order = new \WC_Order( $order->get_parent_id() );

            return $this->getCustomerInfo( $order );
        } else {
            $customer = [
                'wp_user_id' => '',
                'first_name' => $order->get_billing_first_name(),
                'last_name'  => $order->get_billing_last_name(),
                'email'      => $order->get_billing_email(),
            ];
        }

        $customer['phone']  = $order->get_billing_phone();
        $customer['address_1']  = $order->get_billing_address_1();
        $customer['address_2']  = $order->get_billing_address_2();
        $customer['city']  = $order->get_billing_city();
        $customer['state']  = $order->get_billing_state();
        $customer['postcode']  = $order->get_billing_postcode();
        $customer['country']  = $order->get_billing_country();

        return $customer;
    }
}