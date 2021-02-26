<?php 
namespace WCMystore\Import;

use WP_Background_Process;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	return;
}

class ImportMyStoreManufacturers extends WP_Background_Process {

	  protected $action = 'mystore_manufacturers_import';

	  public function __construct() {
		  parent::__construct();
	  }

	  protected function task( $item ) {
  		wcystore()->wc_manufacturers->create( $item );

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