<?php 
namespace WCMystore;

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

  		$product_id = $product["id"];

  		wcystore()->wc_products->create( $product );
  	
  		$categories = wcystore()->http->get( "/products/{$product_id}/categories");

		if( isset( $categories['data'] ) && !empty( $categories['data'] ) ) {
		
			foreach ( $categories['data'] as $category ) {

				$attributes  = $category['attributes'];
		   		$name 		 = $attributes['name']['no'];
		   		$slug 		 = $attributes['slug']['no'];
		   		$description = $attributes['description']['no'];

		   		if( !term_exists( $name, 'product_cat' ) ) {
				   	$term = wp_insert_term( $name, 'product_cat' );
					wp_set_post_terms( $product_id, $term['term_id'],'product_cat' );
					update_term_meta( $term['term_id'], 'mystore_product_cat_id', $category['id'] );
				} else {
					$term = get_term_by('name', $name, 'product_cat');
					wp_set_post_terms( $product_id, $term->term_id,'product_cat' );
					update_term_meta( $term->term_id, 'mystore_product_cat_id', $category['id'] );
				}
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