<?php 
/**
 * Plugin Name: Woocommerce MyStore Integration
 * Description: Description
 * Plugin URI: http://#
 * Author: Author
 * Author URI: http://#
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: text-domain
 * Domain Path: domain/path
 */

if ( !defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/vendor/autoload.php';

final class WCMystore {

    public $version    = '1.0.0';
    private $container = [];

    public function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'woocommerce_loaded', array( $this, 'init_plugin' ) );
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Self();
        }

        return $instance;
    }

    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    public function define_constants() {
        define( 'WCMYSTORE_VERSION', $this->version );
        define( 'WCMYSTORE_SEPARATOR', ' | ');
        define( 'WCMYSTORE_FILE', __FILE__ );
        define( 'WCMYSTORE_ROOT', __DIR__ );
        define( 'WCMYSTORE_PATH', dirname( WCMYSTORE_FILE ) );
        define( 'WCMYSTORE_INCLUDES', WCMYSTORE_PATH . '/includes' );
        define( 'WCMYSTORE_URL', plugins_url( '', WCMYSTORE_FILE ) );
        define( 'WCMYSTORE_ASSETS', WCMYSTORE_URL . '/assets' );
    }

    public function includes() {
        require_once WCMYSTORE_INCLUDES . '/class-http.php';
        require_once WCMYSTORE_INCLUDES . '/class-admin.php';
        require_once WCMYSTORE_INCLUDES . '/class-settings-api.php';
        require_once WCMYSTORE_INCLUDES . '/class-import.php';
       
        require_once WCMYSTORE_INCLUDES . '/import/class-import-post.php';
        require_once WCMYSTORE_INCLUDES . '/import/class-import-categories.php';
        require_once WCMYSTORE_INCLUDES . '/import/class-import-manufactures.php';
        require_once WCMYSTORE_INCLUDES . '/import/class-import-product-option.php';
        require_once WCMYSTORE_INCLUDES . '/import/class-import-product-value.php';


    	require_once WCMYSTORE_INCLUDES . '/class-order.php';
    	require_once WCMYSTORE_INCLUDES . '/class-product.php';
    	require_once WCMYSTORE_INCLUDES . '/class-product-tags.php';
    	require_once WCMYSTORE_INCLUDES . '/class-product-categories.php';

        require_once WCMYSTORE_INCLUDES . '/woocommerce/WCCustomers.php';
        require_once WCMYSTORE_INCLUDES . '/woocommerce/WCOrders.php';
        require_once WCMYSTORE_INCLUDES . '/woocommerce/WCProducts.php';
        require_once WCMYSTORE_INCLUDES . '/woocommerce/WCOrderProducts.php';
    	require_once WCMYSTORE_INCLUDES . '/woocommerce/WCManufacturers.php';


        require_once WCMYSTORE_INCLUDES . '/class-api.php';
        require_once WCMYSTORE_INCLUDES . '/api/class-api-rest-controller.php';
        require_once WCMYSTORE_INCLUDES . '/api/class-product-controller.php'; 
        require_once WCMYSTORE_INCLUDES . '/api/class-order-controller.php';
        require_once WCMYSTORE_INCLUDES . '/api/class-categories-controller.php'; 
    }

    public function init_plugin() {
    	$this->includes();
        $this->init_classes();
    }

    public function activate() {

    }

    public function deactivate() {

    }

    public function init_classes() {

        // $cat = get_terms( [ 
        //     'taxonomy' => 'product_cat', 
        //     'hide_empty' => false, 
        //     'meta_query' => [
        //         [
        //             'key'       => 'mystore_product_cat_id',
        //             'value'     => 57,
        //             'compare'   => '='
        //         ]
        //     ] 
        // ]);

       new WCMystore\Admin();
      
       // new WCMystore\Order();
       // new WCMystore\Product();
       // new WCMystore\ProductCategories();
       // new WCMystore\ProductTags();

       $this->container[ 'wc_customers' ]             = new WCMystore\WooCommerce\WCCustomers();
       $this->container[ 'wc_products' ]              = new WCMystore\WooCommerce\WCProducts();
       $this->container[ 'wc_orders' ]                = new WCMystore\WooCommerce\WCOrders();
       $this->container[ 'wc_categories' ]            = new WCMystore\WooCommerce\WCCategories();
       $this->container[ 'wc_manufacturers' ]         = new WCMystore\WooCommerce\WCManufacturers();
       $this->container[ 'wc_product_options' ]       = new WCMystore\WooCommerce\WCProductsOption();
       
       $this->container[ 'http' ]     = new WCMystore\Http();
       $this->container[ 'api' ]      = new WCMystore\Api();
       $this->container[ 'importer' ] = new WCMystore\Importer();
    }
}

if( !function_exists('wcystore') ) {
    function wcystore() {
        return WCMystore::init();
    }
}

wcystore();