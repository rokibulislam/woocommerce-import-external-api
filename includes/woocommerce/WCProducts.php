<?php 
namespace WCMystore\WooCommerce;
use WC_Product_Simple;
use WP_Query;

class WCProducts {

	public function create( $args = [] ) {
        $product_id = $args['id'];
        $query =  wcystore()->wc_products->exist( $product_id );
        if( $query->found_posts <= 0 ) {
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

            if ( isset( $attributes['weight'] ) ) {
                $product->set_weight( $attributes['weight'] );
            }

            if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {

                // Manage stock.
                if ( isset( $args['manage_stock'] ) ) {
                    $product->set_manage_stock("yes");
                }

                if( isset( $attributes['quantity'] ) ) {
                    $product->set_stock_quantity( $attributes['quantity'] );
                }
            }

            $gallery = [];
            

            if( isset( $attributes ['image2'] ) && $attributes ['image2']!= null ){
                $gallery[] = $this->attachGallery( $attributes ['image2'], $product->get_id() );
            }

            if( isset( $attributes ['image3'] ) && $attributes ['image3']!= null ){
                $gallery[] = $this->attachGallery( $attributes ['image3'], $product->get_id() );
            }

            if( isset( $attributes ['image4'] ) && $attributes ['image4']!= null ){
                $gallery[] = $this->attachGallery( $attributes ['image4'], $product->get_id() );
            }

            if( isset( $attributes ['image5'] ) && $attributes ['image5']!= null ){
                $gallery[] = $this->attachGallery( $attributes ['image5'], $product->get_id() );
            }

            if( isset( $attributes ['image6'] ) && $attributes ['image6']!= null ){
                $gallery[] = $this->attachGallery( $attributes ['image6'], $product->get_id() );
            }

            if( isset( $attributes ['image7'] ) && $attributes ['image7']!= null ){
                $gallery[] = $this->attachGallery( $attributes ['image7'], $product->get_id() );
            }

            if( isset( $attributes ['image8'] ) && $attributes ['image8']!= null ){
                $gallery[] = $this->attachGallery( $attributes ['image8'], $product->get_id() );
            }

            $product->set_gallery_image_ids( $gallery );

            $product->save();

            update_post_meta( $product->get_id(), 'mystore_product_id', $args['id']  );

            $this->attachCategoryPost( $args['id'], $product->get_id()  );
            $this->attachImagePost( $attributes, $product->get_id() );


            // if( !empty( $args['relationships'] ) ) {
                if( $args['relationships']['manufacturer']['data'] != null ) {
                    $manufacture_id = $args['relationships']['manufacturer']['data']['id'];
                    $this->attachManufactorPost( $manufacture_id, $product->get_id() );
                }
            // }

            return $product;
        } else {

            if ( $query->have_posts() ) : 
                while ( $query->have_posts() ) : $query->the_post(); 
                    global $post;
                    $product = wc_get_product( $post->ID );
                    if ( isset( $attributes['weight'] ) ) {
                        $product->set_weight( $attributes['weight'] );
                    }

                    if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {

                        // Manage stock.
                        if ( isset( $args['manage_stock'] ) ) {
                            $product->set_manage_stock("yes");
                        }

                        if( isset( $attributes['quantity'] ) ) {
                            $product->set_stock_quantity( $attributes['quantity'] );
                        }
                    }

                    $product->save();
                endwhile; 
            endif;

            error_log('not create product');
            return [];
        }
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


    public function attachCategoryPost( $mystore_id, $product_id ) {

        $categories = wcystore()->http->get( "/products/{$mystore_id}/categories");
            
        if( isset( $categories['data'] ) && !empty( $categories['data'] ) ) {
            error_log('attach category');

            $terms_list = [];
        
            foreach ( $categories['data'] as $category ) {

                $term = wcystore()->wc_categories->create( $category );;

                if( is_object( $term ) ) {
                    array_push($terms_list, $term->term_id);
                    update_term_meta( $term->term_id, 'mystore_product_cat_id', $category['id'] );
                } else if( is_array( $term ) ) {
                    array_push($terms_list, $term['term_id']);
                    update_term_meta( $term['term_id'], 'mystore_product_cat_id', $category['id'] );
                }
            }

            if( !empty( $terms_list ) ) {
                wp_set_post_terms( $product_id, $terms_list,'product_cat' );
            }

        }
    }


    public function attachManufactorPost( $mystore_id, $product_id ) {
        error_log('attache manufacture');
        $manufactures = get_terms([ 
            'taxonomy' => 'product_manufacture', 
            'hide_empty' => false, 
            'meta_query' => [ 
                [
                    'key' => 'mystore_product_manufacture_id', 
                    'value' => $mystore_id, 
                    'compare' => '=' 
                ] 
            ] 
        ]);

        if( !empty( $manufactures ) ) {
            $manufactures_ids = wp_list_pluck( $manufactures, 'term_id' );;
            wp_set_post_terms( $product_id, $manufactures_ids,'product_manufacture' );
        }
    }


    public function exist( $id ) {

        $args = array(
            'post_type' => array('product'),
            'post_status' =>  'any',
            'meta_query'     => array(
                array(
                    'key'     => 'mystore_product_id',
                    'value'   => $id,
                    'type'    => 'NUMERIC',
                    'compare' => '=',
                )
            )    
        );
    
        $query = new WP_Query( $args );
        
        // return $query->found_posts;   
        return $query;   
    }


    public function attachGallery( $image, $post_id ) {
        error_log('attach gallery');
        $shop    = mystore_get_options( 'mystore_fields', 'mystore_name');
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

        return $attach_id;
    }
}