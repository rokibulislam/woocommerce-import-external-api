<?php 
namespace WCMystore;

class Api {

    protected $class_map;

    public function __construct() {

        add_action( 'rest_api_init', [ $this, 'register_routes' ] );

        add_filter( 'woocommerce_rest_prepare_product_object', 'change_product_response', 99, 3 );
        add_filter( 'woocommerce_rest_prepare_product_variation_object', 'change_product_response', 99, 3 );
    }


    public function register_routes() {
        new \WCMystore\API\ProductController();
        new \WCMystore\API\CategoriesController();
        new \WCMystore\API\OrderController();
    }

    public function custom_change_product_response( $response, $object, $request ) {

        return $response;
    }   
}