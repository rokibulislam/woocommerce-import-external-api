<?php 

namespace WCMystore\API;

use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;

class ProductController extends WP_REST_Controller  {

	protected $namespace = 'wcmystore/v1';
	protected $rest_base = 'product';

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
                        'id' => array(
                            'description' => __( 'Unique identifier for the object.', '' ),
                            'type'        => 'integer',
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
        // error_log('create');
        // error_log(print_r($request,true));

        $product = wcystore()->wc_products->create( $request['data'] );

        $response = [
            'data' => [
                'id' => $product
            ],

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
        // error_log('update');
        // error_log(print_r($request,true));

        // $product = wcystore()->wc_products->update( $request['data'] );


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