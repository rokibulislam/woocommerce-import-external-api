<?php 

namespace WCMystore\WooCommerce;
use WC_Product_Simple;

class WCProducts {

	public function all( $args = [] ) {
		$post_statuses = apply_filters( 'mystore_get_product_status', [ 'publish', 'draft', 'pending', 'future' ] );

        $defaults = [
            'post_type'      => 'product',
            'post_status'    => $post_statuses,
            'posts_per_page' => -1,
            'orderby'        => 'post_date',
            'order'          => 'DESC',
            'paged'          => 1,
        ];

        $args = wp_parse_args( $args, $defaults );

        return new WP_Query( apply_filters( 'mystore_all_products_query', $args ) );
	}

    public function get( $product_id ) {
        return wc_get_product( $product_id );
    }

	public function create( $args = [] ) {
	 /*
	  	$attributes 	= $product['attributes'];
	  	$price 			= isset( $attributes['price'] ) ? $attributes['price'] : 0.00;
	  	$name 			= isset( $attributes['name']['no'] ) ? $attributes['name']['no'] : '';
	  	$slug 			= isset( $attributes['slug']['no'] ) ? $attributes['slug']['no'] : '';
	  	$description 	= isset( $attributes['description']['no'] ) ? $attributes['description']['no'] : '';
	  	$cost 			= isset( $attributes['cost'] ) ? $attributes['cost'] : 0.00;
	  	$quantity		= isset( $attributes['quantity'] ) ? $attributes['quantity'] : 0;
	  	$weight			= isset( $attributes['weight'] ) ? $attributes['weight'] : 0;

	  	$postarr = array(
		  'post_title'    => $name,
		  'post_content'  => $description,
		  'post_status'   => 'draft',
		  'post_type'	  => 'product'
		);
 	
	  	$post_id = wp_insert_post( $postarr );

	  	update_post_meta( $post_id, 'mystore_product_id', $product['id']  );


	  	return $post_id;
	  */

	  	$attributes 	= $args['attributes'];

	  	$product = new WC_Product_Simple();
        
        if ( isset( $attributes['name']['no'] ) ) {
            $product->set_name( wp_filter_post_kses( $attributes['name']['no'] ) );
        }

        // Post content.
        if ( isset( $attributes['description']['no'] ) ) {
            $product->set_description( wp_filter_post_kses( $attributes['description']['no'] ) );
        }

        // Post slug.
        if ( isset( $attributes['slug']['no'] ) ) {
            $product->set_slug( $attributes['slug']['no'] );
        }

        if ( isset( $attributes['sku'] ) ) {
            $product->set_sku( $attributes['sku'] );
        }

        if ( isset( $attributes['price'] ) ) {
            $product->set_regular_price( $attributes['price'] );
        }

        if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {

            if( isset( $attributes['quantity'] ) ) {
                $product->set_stock_quantity( $attributes['quantity'] );
            }
        }

        $product->save();


        update_post_meta( $product->get_id(), 'mystore_product_id', $args['id']  );

        return $product;
	}

	public function update( $args = [] ) {
        $id = isset( $args['id'] ) ? absint( $args['id'] ) : 0;

        if ( empty( $id ) ) {
            return new WP_Error( 'no-id-found', __( 'No product ID found for updating' ), [ 'status' => 401 ] );
        }

        return $this->create( $args );
    }

	public function delete( $product_id, $force = false ) {
        $product = $this->get( $product_id );
    
        if ( $product ) {
            $product->delete( [ 'force_delete' => $force ] );
        }

        return $product;
    }
}