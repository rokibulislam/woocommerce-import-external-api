<?php 
namespace WCMystore;

use WP_Background_Process;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	return;
}

class ImportMyStoreCategories extends WP_Background_Process {

	protected $action = 'mystore_categories_import';

	public function __construct() {
		parent::__construct();
	}

	protected function task( $category ) {
  		
  		$attributes  = $category['attributes'];
   		$name 		 = $attributes['name']['no'];
   		$slug 		 = $attributes['slug']['no'];
   		$description = $attributes['description']['no'];

   		if( !term_exists( $name, 'product_cat' ) ) {

		   	$term = wp_insert_term( $name, 'product_cat', [
		   		'description' => $description,
    			'slug'        => $slug
		   	] );
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