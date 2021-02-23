<?php 

namespace WCMystore;
use WP_Async_Request;
use WP_Background_Process;

class Mystore_Request extends WP_Async_Request {

	protected $action = 'example_request';

	protected function handle() {
		// Actions to perform
	}

}

class Mystore_Process extends WP_Background_Process {

	protected $action = 'example_process';

	protected function task( $item ) {

		return false;
	}


	protected function complete() {
		parent::complete();
	}

}


function mystore_http_request_args( $r, $url ) {
	// $r['headers']['Authorization'] = 'Basic ' . base64_encode( USERNAME . ':' . PASSWORD );

	return $r;
}

// add_filter( 'http_request_args', 'mystore_http_request_args', 10, 2);