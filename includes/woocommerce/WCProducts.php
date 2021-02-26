<?php 
namespace WCMystore\WooCommerce;
use WC_Product_Simple;

class WCProducts {

	public function create( $args = [] ) {
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