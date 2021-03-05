<?php 
namespace WCMystore\WooCommerce;
use WC_Product_Simple;

class WCProducts {

	public function create( $args = [] ) {
        error_log('create method');
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

        $this->attachImagePost( $attributes, $product->get_id() );

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


    public function attachImagePost( $attributes, $post_id ) {
        $shop    = mystore_get_options( 'mystore_fields', 'mystore_name');
        $image   = $attributes['image'];

        if( $image != null ){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $upload_dir = wp_upload_dir();
            
            $image_url  = "https://{$shop}.mystore4.no/users/{$shop}_mystore_no/images/$image";
            $image_data = file_get_contents( $image_url);
            $filename   = $upload_dir['basedir'] . '/' . strtotime("now") . $image ;
            
            file_put_contents( $filename, $image_data);
            
            $wp_filetype = wp_check_filetype( $filename, null );

            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name( basename( $filename ) ),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
         
           
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
            $res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
            $res2        = set_post_thumbnail( $post_id, $attach_id );
        }
    }
}