<?php 
namespace WCMystore;

use WP_Error;

class Http {

	public  $root = 'https://api.mystore.no/shops/fitnessgrossisten';
	private $api_key = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNTdmZDA5ODJiMzVkZTNlNzZlMGQ1Mzk4MDJiOTY1ZDQ3MGFkZjY1MTAwZmMxODUwZjQ3ZjVjY2FlM2YwNWViNjA5YjNkOWI4ZGI1NzQ4YzQiLCJpYXQiOjE2MTM0MTIxMDYsIm5iZiI6MTYxMzQxMjEwNiwiZXhwIjoxOTI4OTQ0OTA2LCJzdWIiOiJmaXRuZXNzZ3Jvc3Npc3Rlbl8xIiwic2NvcGVzIjpbInJlYWQ6cHJvZHVjdHMiLCJyZWFkOmNhdGVnb3JpZXMiLCJyZWFkOmltYWdlcyIsInJlYWQ6cHJvZHVjdC1hdHRyaWJ1dGVzIiwicmVhZDpwcm9kdWN0LXZhcmlhbnRzIiwicmVhZDpwcm9kdWN0LXNwZWNpYWxzIiwicmVhZDpwcm9kdWN0LXJldmlld3MiLCJyZWFkOnJlZGlyZWN0cyIsInJlYWQ6cHJvZHVjdC1vcHRpb25zIiwicmVhZDptYW51ZmFjdHVyZXJzIiwicmVhZDpzdXBwbGllcnMiLCJyZWFkOnRheC1jbGFzc2VzIiwicmVhZDpsYW5ndWFnZXMiLCJyZWFkOmN1cnJlbmNpZXMiLCJyZWFkOnNldHRpbmdzIiwicmVhZDpzaGlwcGluZyIsInJlYWQ6cmVsYXRpb25zaGlwcyIsInJlYWQ6YmF0Y2giLCJyZWFkOnByb2R1Y3QtcHJvcGVydGllcyIsInJlYWQ6d2ViaG9va3MiLCJyZWFkOnByb2R1Y3QtdGFncyIsImhpZGU6cHJvZHVjdHM6Y29zdCJdLCJub19teXN0b3JlX2hvc3RzIjpbImZpdG5lc3Nncm9zc2lzdGVuIl19.uH4fMUtgDvJyidXnXsWAM-Uml-GXYazKci1hBdQjZK8KiktsgG2QU74XESVUsTEQ0kAIgoGBYAuJC9g7LvPkqOAwRTGv4h5gSyR07mh6I8cRrjpXY6pJ_AhV0pauo8kS5s2b7ocgjgOgrWUKkiDo38zBFg6q8oAVm8EzwGNjHCdWofXrocu9OUCw3qZm7ODNowH9byvo02v54MyJWUGW7bIX3ZFBoLg_3Qs2I8el48i0P0cdtB7S6D56utMtD8eYstnjrxWz09UXEN3mDrmaNinl1zSyB7gRdPo1HUgzWiqULkqBLDq0AVhW9M1uaUvyWiG2_uQHzzBCGF6MXwiuzUE0EJz4VQvi2KB80jEbxwhkSgdtaMKHGx105paOVliUD98H7VDhD9G22sYBJh5z8lRXKJVuZDRivp4MywqA52vM0RDI8GFlTAJSkHmdTQvvT-NheWY56QwVscKJGbLTKXirxB9ADwdvw545SkUB162_R44K619FqYsQO8cYZAPvXoINg3Ue0MP_l0z_Yp4a4QRSUDjsLvlMnWGDjUJeP3lE7VCZ-Rx31HqbBUtp2dUD3DwDc2ztcIup_R7i0GqskVfAs0YtxDJAbUEYLRUkYpCFaa799Jkgc1uKQwcdYY926nNV9fayUZV0VY5bGV4dIOTFsNFoWLFj-omq-Iif2qw';
	private $url = '';
	private $query = [];


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
		
		$response = wp_remote_get( $this->root .'/categories', 
			array(
		        'headers' => array(
		                'Accept' => 'application/vnd.api+json', 
   						'Authorization' => 'Bearer '. $this->api_key 
		        )
		    )
     	);

     	return $this->response( $response );
	}

	public function post( $data = [], $args = [] ) {

	}

	public function put( $data, $args = [] ) {

	}

	public function delete( $data = [], $args = [] ) {

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