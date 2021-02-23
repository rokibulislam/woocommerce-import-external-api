<?php 

namespace WCMystore;

class Product {

    protected $source = 'woocommerce';

	public function __construct() {
		add_action( 'save_post_product', [ $this, 'sync_on_product_save' ] , 10, 3 );
		add_action( 'woocommerce_update_product', [ $this, 'sync_on_product_update' ], 10, 2 );
		add_action( 'woocommerce_delete_product', [ $this, 'sync_on_product_delete' ], 10, 1 );
	}

	public function sync_product_category() {

	}

	public function sync_on_product_save( $post_id, $post, $update ) {

        if ( $post->post_status !== 'publish' || $post->post_type !== 'product' || ! class_exists( $this->source ) ) {
            return;
        }

        $product = wc_get_product( $post );

        if ( ! $product ) {
            return;
        }

        $is_new = $post->post_date === $post->post_modified;


        if ( $is_new ) {
            
            $data = [
                'data' => [
                    "type"  => "products",
                    "attributes" => [   
                        // 'id'          => $product->get_id(),
                        "name" => [
                            "no" => $product->get_name()
                        ],
                        "slug"  => [
                            "no" => $product->get_slug()
                        ]
                        // 'images'      => $this->get_product_images( $product ),
                        // 'status'      => $product->get_status(),
                        // 'price'       => $product->get_price(),
                        // 'total_sales' => $product->get_total_sales(),
                        // 'rating'      => $product->get_average_rating(),
                        // 'permalink'   => get_permalink( $product->get_id() ),
                        // 'categories'  => $this->get_product_categories( $product->get_id() ),
                    ],

                    "relationships" => [
                      "categories" => [
                        "data" => [
                          [
                            "type" => "categories",
                            "id" =>  "97"
                          ]
                        ]
                      ]
                    ]
                ]
            ];

            $response = wcystore()->http->post( '/products', $data );
        
        } else {

            $data = [
                'data' => [
                    "type"  => "products",
                    "attributes" => [  
                        // 'id'          => $product->get_id(),
                        "name" => [
                            "no" => $product->get_name()
                        ],
                        "slug"  => [
                            "no" => $product->get_slug()
                        ]
                        // 'images'      => $this->get_product_images( $product ),
                        // 'status'      => $product->get_status(),
                        // 'price'       => $product->get_price(),
                        // 'total_sales' => $product->get_total_sales(),
                        // 'rating'      => $product->get_average_rating(),
                        // 'permalink'   => get_permalink( $product->get_id() ),
                        // 'categories'  => $this->get_product_categories( $product->get_id() )
                    ],

                    "relationships" => [
                      "categories" => [
                        "data" => [
                          [
                            "type" => "categories",
                            "id" =>  "97"
                          ]
                        ]
                      ]
                    ]
                ]
            ];

            $response = wcystore()->http->put( '/products', $data );

            error_log(print_r($response,true));
        }

	}


	public function sync_on_product_update(  $product_id,$product ) {

	}

	public function sync_on_product_delete( $product_id ) {

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


	public function get_product_categories( $product_id ) {
        $product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
        $categories = [];
        foreach ( $product_cats_ids as $cat_id ) {
            $term = get_term_by( 'id', $cat_id, 'product_cat' );

            $categories[] = [
                'id' => $cat_id,
                'name' => $term->name,
            ];
        }

        return $categories;
    }
}