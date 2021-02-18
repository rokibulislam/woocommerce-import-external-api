<?php 

namespace WCMystore\API;
use WP_REST_Controller;

abstract class WCMystore_REST_Controller extends WP_REST_Controller {

    public function get_item_permissions_check( $request ) {
        return $this->get_items_permissions_check( $request );
    }

    public function get_items_permissions_check( $request ) {
        if ( !current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_weforms_forbidden_context', __( 'Sorry, you are not allowed', '' ), [ 'status' => rest_authorization_required_code() ] );
        }

        return true;
    }

    public function create_item_permissions_check( $request ) {
        return $this->get_items_permissions_check( $request );
    }

    public function update_item_permissions_check( $request ) {
        return $this->get_items_permissions_check( $request );
    }

    public function delete_item_permissions_check( $request ) {
        return $this->create_item_permissions_check( $request );
    }

    public function prepare_item_for_response( $item, $request, $additional_fields = [] ) {
        $response = rest_ensure_response( $item );
        $response = $this->add_links( $response, $item );

        return $response;
    }

    protected function add_links( $response, $item ) {
        $response->data['_links'] = $this->prepare_links( $item );

        return $response;
    }

    protected function prepare_links( $item ) {
        $links = [
            'self' => [
                'href' => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $item['id'] ) ),
            ],
            'collection' => [
                'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
            ],
        ];

        return $links;
    }

    public function format_collection_response( $response, $request, $total_items ) {
        if ( $total_items === 0 ) {
            return $response;
        }

        // Store pagation values for headers then unset for count query.
        $per_page = (int) ( !empty( $request['per_page'] ) ? $request['per_page'] : 20 );
        $page     = (int) ( !empty( $request['page'] ) ? $request['page'] : 1 );

        $response->header( 'X-WP-Total', (int) $total_items );

        $max_pages = ceil( $total_items / $per_page );

        $response->header( 'X-WP-TotalPages', (int) $max_pages );
        $base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

        if ( $page > 1 ) {
            $prev_page = $page - 1;

            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }
            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }

        if ( $max_pages > $page ) {
            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        return $response;
    }
}