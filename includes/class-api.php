<?php 
namespace WCMystore;

class Api {

    protected $class_map;

    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }


    public function register_routes() {
        new \WCMystore\API\ProductController();
        new \WCMystore\API\CategoriesController();
        new \WCMystore\API\OrderController();
    } 
}