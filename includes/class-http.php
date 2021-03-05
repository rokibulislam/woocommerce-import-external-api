<?php 
namespace WCMystore;

use WP_Error;

class Http {

    public  $root    = 'https://api.mystore.no/shops/';
	public  $shop    = '';
    private $api_key = '';
	private $url = '';
	private $query = [];

    public function __construct() {
       $this->api_key = mystore_get_options( 'mystore_fields', 'mystore_token');
       $this->shop    = mystore_get_options( 'mystore_fields', 'mystore_name');
       $this->root    = $this->root . $this->shop;
    }

    private function args( $args ) {
        
        $defaults = [
            'headers' => [
                'Content-Type'  => 'application/vnd.api+json',
                'Accept'        => 'application/vnd.api+json',
                'Authorization' => 'Bearer '. $this->api_key 
            ],
            'timeout'     => 10000,
        ];

        return wp_parse_args( $args, $defaults );
    }



    private function build_url( $url = '', $query = [] ) {
        if ( $url ) {
            $url = $this->root . $url;
        } elseif ( $this->url ) {
            $url = $this->root . $this->url;
        }

        if ( ! empty( $query ) ) {
            $this->query = array_merge( $query, $this->query );
        }

        if ( ! empty( $this->query ) ) {
            $url .= '?' . http_build_query( $this->query );
        }

        $this->url   = '';
        $this->query = [];

        return $url;
    }


	public function get( $url = '', $query = [], $args = [] ) {
        $args = $this->args( $args );
        $url  = $this->build_url( $url, $query );
		  
        // error_log(print_r($url,true));

        $response = wp_remote_get( $url, $args );

     	return $this->response( $response );
	}

	public function post( $url = '', $data = [], $args = [] ) {
        $args = $this->args( $args );

        $args['body'] = ! empty( $data ) ? json_encode( $data ) : null;

        $url = $this->build_url( $url );

        // error_log(print_r($url,true));
        // error_log(print_r($args,true));

        $response = wp_remote_post( $url, $args );

        return $this->response( $response );
	}

	public function put( $url = '', $data, $args = [] ) {
        $data['_method'] = 'PATCH';
        
        return $this->post( $url, $data, $args );
	}

	public function delete( $data = [], $args = [] ) {
        $args = $this->args( $args );
        $args['method'] = 'delete';
        $args['body'] = ! empty( $data ) ? $data : null;

        $url = $this->build_url();

        $response = wp_remote_request( $url, $args );

        return $this->response( $response );
	}


	private function response( $response ) {
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( $response['body'], true );

        if ( $response_code >= 200 && $response_code <= 299 ) {
            return $body;
        } else {
            $message = is_array( $body ) && array_key_exists( 'message', $body )
                ? $body['message']
                : __( 'Something went wrong', 'wemail' );

            $error_data = [
                'status' => $response_code,
            ];

            if (
                isset( $body['errors'] ) &&
                ! empty( $body['errors'] ) &&
                is_array( $body['errors'] )
            ) {
                $error_data['errors'] = $body['errors'];
            }

            return new WP_Error( 'error', $message, $error_data );
        }
    }
}