<?php 
namespace WCMystore\Import;

use WP_Background_Process;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	return;
}

class ImportMyStorePost extends WP_Background_Process {

	protected $action = 'mystore_post_import';

	public function __construct() {
		parent::__construct();
	}

	protected function task( $product ) {

	  		$product_id = $product['id'];

	  		$pro = wcystore()->wc_products->create( $product );
	  	
	  		$categories = wcystore()->http->get( "/products/{$product_id}/categories");
	  		
			if( isset( $categories['data'] ) && !empty( $categories['data'] ) ) {

				$terms_list = [];
			
				foreach ( $categories['data'] as $category ) {

	  				$term = wcystore()->wc_categories->create( $category );;

      				if( is_object( $term ) ) {
      					array_push($terms_list, $term->term_id);
      					update_term_meta( $term->term_id, 'mystore_product_cat_id', $category['id'] );
      				} else if( is_array( $term ) ) {
      					array_push($terms_list, $term['term_id']);
      					update_term_meta( $term['term_id'], 'mystore_product_cat_id', $category['id'] );
      				}
				}

				if( !empty( $terms_list ) ) {
					wp_set_post_terms( $pro->get_id(), $terms_list,'product_cat' );
				}

			}
				
		return false;
	}


	protected function complete() {
		parent::complete();
	}

    public function handle_cron_healthcheck() {
        if ( $this->is_process_running() ) {
            // Background process already running.
            return;
        }

        if ( $this->is_queue_empty() ) {
            // No data to process.
            $this->clear_scheduled_event();
            return;
        }

        $this->handle();
    }
}