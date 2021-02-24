<?php 

namespace WCMystore\API;

use WP_REST_Controller;
use WP_REST_Server;

class OrderController extends WP_REST_Controller {

	protected $namespace = 'wcmystore/v1';
	protected $rest_base = 'order';

    public function __construct() {
        $this->register_routes();
    }
    
	public function register_routes() {

		register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items' ],
                    'permission_callback' => [ $this, 'get_items_permissions_check' ],
                ],

                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'create_item' ],
                    'permission_callback' => [ $this, 'create_item_permissions_check' ],
                ]
            ]
        );

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/', array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'Unique identifier for the object.', '' ),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_item' ),
                    'args'                => $this->get_collection_params(),
                    'permission_callback' => array( $this, 'get_item_permissions_check' ),
                ),

                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_item' ),
                    'args'                => array(
                        'status' => array(
                            'type'        => 'string',
                            'description' => __( 'Order Status', '' ),
                            'required'    => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ),
                    ),
                    'permission_callback' => array( $this, 'update_item_permissions_check' ),
                ),
            )
        );


        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'Unique identifier for the object.', '' ),
                        'type'        => 'integer',
                    )
                ),

                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_item' ),
                    'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                ),
            )
        );
	}



    public function create_item( $request ) {
        $data       = $request['data'];
        $attributes =  $data['attributes'];

       /*
        $city                     = $attributes['customer_address_city'];
        $country                  = $attributes['customer_address_country'];
        $shipping_address_name    = $attributes['shipping_address_name'];
        $shipping_address_city    = $attributes['shipping_address_city'];
        $shipping_address_country = $attributes['shipping_address_country'];
        $billing_address_name     = $attributes['billing_address_name']; 
        $billing_address_city     = $attributes['billing_address_city']; 
        $billing_address_country  = $attributes['billing_address_country']; 
        $currency_value           = $attributes['currency_value'];
        */

        $response = [
            'data' => [],
            'message' => __( 'Unknown source.', '' ),
        ];

        $response = rest_ensure_response( $response );

        return $response;
    }


    public function get_items( $request ) {

        $response = [
            'data' => [],
            'message' => __( 'Unknown source.', '' ),
        ];

        $response = rest_ensure_response( $response );

        return $response;
    }


    public function get_item( $request ) {

        $response = [
            'data' => [],
            'message' => __( 'Unknown source.', '' ),
        ];

        $response = rest_ensure_response( $response );

        return $response;
    }

    
    public function update_item( $request ) {

        $response = [
            'data' => [],
            'message' => __( 'Unknown source.', '' ),
        ];

        $response = rest_ensure_response( $response );

        return $response;
    }

    public function delete_item( $request ) {

        $response = [
            'data' => [],
            'message' => __( 'Unknown source.', '' ),
        ];

        $response = rest_ensure_response( $response );

        return $response;
    }

    public function get_items_permissions_check( $request ) {
        return true;
    }

    public function create_item_permissions_check( $request ) {
        return true;
    }

    public function update_item_permissions_check( $request ) {
        return true;
    }

    public function delete_item_permissions_check( $request ) {
        return true;  
    }
}