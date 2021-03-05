<?php 

namespace WCMystore;

class Product {

    protected $source = 'woocommerce';

	public function __construct() {
	
    	add_action( 'save_post_product', [ $this, 'sync_on_product_save' ] , 10, 3 );
        add_action( 'woocommerce_new_product', [ $this, 'sync_on_product_create' ], 10, 2 );
		add_action( 'woocommerce_update_product', [ $this, 'sync_on_product_update' ], 10, 2 );
		add_action( 'woocommerce_delete_product', [ $this, 'sync_on_product_delete' ], 10, 1 );


        // stock update actions
        add_action( 'woocommerce_product_set_stock', [ $this, 'handle_stock_update' ] );
        add_action( 'woocommerce_variation_set_stock', [ $this, 'handle_stock_update' ] );

        // stock update status
        add_action( 'woocommerce_variation_set_stock_status', [ $this, 'handle_stck_status_update' ], 10, 3 );      
        add_action( 'woocommerce_product_set_stock_status', [ $this, 'handle_stock_status_update' ], 10, 3 );
	}

    public function sync_on_product_create( $product_id, $product ) { 
        // $data     = $this->get_data( $product_id, $product );
        // $response = wcystore()->http->post( '/products', $data );
        // update_post_meta( $product_id, 'mystore_product_id', $args['id']  );
    }

	public function sync_on_product_save( $post_id, $post, $update ) {

        if ( $post->post_status !== 'publish' || $post->post_type !== 'product' ) {
            return;
        }

        $product = wc_get_product( $post );

        if ( ! $product ) {
            return;
        }

        $is_new = $post->post_date === $post->post_modified;

        if ( $is_new ) {
            $data     = $this->get_data( $product_id, $product );
            $response = wcystore()->http->post( '/products', $data );
        } else {
            $id       = get_post_meta( $post_id, 'mystore_product_id', true  );
            $data     = $this->get_data( $post_id, $product );
            $data['data']['id'] = $id;
            $response = wcystore()->http->put( "/products/{$id}", $data );
        }

	}


	public function sync_on_product_update(  $product_id,$product ) {
        $id = get_post_meta( $product_id, 'mystore_product_id', true  );
	}

	public function sync_on_product_delete( $product_id ) {
        $id = get_post_meta( $product_id, 'mystore_product_id', true  );
        $response = wcystore()->http->delete( "/products/{$id}", $data );
	}


	public function get_product_images( $product ) {
        $attachment_ids = array();
        $images = $attachment_ids;
        $product_image = $product->get_image_id();
        // Add featured image.
        if ( ! empty( $product_image ) ) {
            $attachment_ids[] = $product_image;
        }
        // add gallery images.
        $attachment_ids = array_merge( $attachment_ids, $product->get_gallery_image_ids() );

        $images = [];
        foreach ( $attachment_ids as $attachment_id ) {
            $attachment_post = get_post( $attachment_id );
            if ( is_null( $attachment_post ) ) {
                continue;
            }
            $attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
            if ( ! is_array( $attachment ) ) {
                continue;
            }
            $images[] = [
                'id' => (int) $attachment_id,
                'src' => current( $attachment ),
                'title' => get_the_title( $attachment_id ),
                'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
            ];
        }
        // Set a placeholder image if the product has no images set.
        if ( empty( $images ) ) {
            $images[] = [
                'id' => 0,
                'src' => wc_placeholder_img_src(),
                'title' => __( 'Placeholder', 'wemail' ),
                'alt' => __( 'Placeholder', 'wemail' ),
            ];
        }

        return $images;
    }

    public function handle_stock_update( $product  ) {
        $data = $this->get_data( $product->get_id(), $product );
    }

	public function get_product_categories( $product_id ) {
        $product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
      
        $categories = [];
      
        foreach ( $product_cats_ids as $cat_id ) {
            $term = get_term_by( 'id', $cat_id, 'product_cat' );

            $mystore_category_id = get_term_meta( $term->term_id, 'mystore_category_id', true );

            $categories[] = [
                'id' => $cat_id,
                'name' => $term->name,
                "type" => "categories",
                'meta_id' => $mystore_category_id
            ];
        }

        return $categories;
    }


    public function get_data( $product_id, $product ) {

        $data = [    
            'data' => [
                "type"  => "products",
                "attributes" => [   
                    "name" => [
                        "no" => $product->get_name()
                    ],
                    "slug"  => [
                        "no" => $product->get_slug()
                    ],
                    "description" => [
                        "no" => $product->get_description()
                    ],
                    "images"     => $this->get_product_images( $product ),
                    "sku"        => $product->get_sku(),
                    "quantity"   => $product->get_stock_quantity(),
                    "price"      => $product->get_price(),
                    "weight"     => $product->get_weight(),
                    'status'     => $product->get_status(),
                    // 'total_sales' => $product->get_total_sales(),
                    // 'rating'      => $product->get_average_rating(),
                    // 'permalink'   => get_permalink( $product->get_id() ),
                ],
                "relationships" => [
                  "categories" => [
                    "data" => $this->get_product_categories( $product->get_id() )
                  ]
                ]
            ]
        ];

        return $data;
    }

    public function handle_stock_status_update( $product_id, $product_status, $product ) {

    }
}