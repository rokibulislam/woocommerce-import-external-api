<?php 
namespace WCMystore;

use WCMystore\Mystore_Settings_API;

class Admin {

	public function __construct() {
		$this->settings_api = new Mystore_Settings_API();

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );

        add_action( 'init', [ $this, 'add_manufacture' ], 0 );
	}


    public function add_manufacture() {
        
        $labels = array(
            'name'                       => _x( 'Manufactures', 'taxonomy general name', 'textdomain' ),
            'singular_name'              => _x( 'Manufacture', 'taxonomy singular name', 'textdomain' ),
            'search_items'               => __( 'Search Manufactures', 'textdomain' ),
            'popular_items'              => __( 'Popular Manufactures', 'textdomain' ),
            'all_items'                  => __( 'All Manufactures', 'textdomain' ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __( 'Edit Manufacture', 'textdomain' ),
            'update_item'                => __( 'Update Manufacture', 'textdomain' ),
            'add_new_item'               => __( 'Add New Manufacture', 'textdomain' ),
            'new_item_name'              => __( 'New Manufacture Name', 'textdomain' ),
            'separate_items_with_commas' => __( 'Separate writers with commas', 'textdomain' ),
            'add_or_remove_items'        => __( 'Add or remove writers', 'textdomain' ),
            'choose_from_most_used'      => __( 'Choose from the most used writers', 'textdomain' ),
            'not_found'                  => __( 'No manufactures found.', 'textdomain' ),
            'menu_name'                  => __( 'Manufactures', 'textdomain' ),
        );
     
        $args = array(
            'hierarchical'          => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'manufacture' ),
        );

        register_taxonomy( 'product_manufacture', 'product', $args );


    }


	public function admin_menu() {

		global $submenu;

        $capability = 'manage_options';
        $slug       = 'mystore';

        $hook = add_menu_page( __( 'Mystore App', 'mystore' ), __( 'Mystore App', 'contactum' ), $capability, $slug, [ $this, 'mystore_page' ], 'dashicons-text' );
         
	}


	public function admin_init() {
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settin;
        $this->settings_api->admin_init();
	}

	public function mystore_page() {
		require_once WCMYSTORE_INCLUDES . '/html/settings.html';
	}


	public function get_settings_sections() {

        $sections = array(
            array(
                'id'    => 'mystore_fields',
                'title' => '',
                'name' => __( 'MyStore', 'contactum' ),
                'icon'  => 'dashicons-admin-appearance'
            )
        );

        return apply_filters( 'mystore_settings_sections', $sections );
    }


    public function get_settings_fields() {
    
        $settings_fields = array(
            'mystore_fields' => array(
                array(
                    'name'    => 'key',
                    'label'   => __( 'Site Key', '' ),
                    'desc'    => __( '', '' ),
                    'type'    => 'text',
                    'default' => __( '', '' )
                ),
                array(
                    'name'    => 'secret',
                    'label'   => __( 'Site Secret', '' ),
                    'desc'    => __( '', '' ),
                    'type'    => 'text',
                    'default' => __( '', '' )
                ),
                array(
                    'name'    => 'mystore_name',
                    'label'   => __( 'Store Name', '' ),
                    'desc'    => __( '', '' ),
                    'type'    => 'text',
                    'default' => __( '', '' )
                ),
                array(
                    'name'    => 'mystore_token',
                    'label'   => __( 'Mystore Token', '' ),
                    'desc'    => __( '', '' ),
                    'type'    => 'textarea',
                    'default' => __( '', '' )
                ),
            ),
        );

        return apply_filters( 'mystore_settings_fields', $settings_fields );
    }
}