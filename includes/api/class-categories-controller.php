<?php 

namespace WCMystore\API;

use WP_REST_Controller;
use WP_REST_Server;

class CategoriesController extends WP_REST_Controller {

	protected $namespace = 'wcmystore/v1';
	protected $rest_base = 'categories';

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
    }



    public function create_item( $request ) {

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
}