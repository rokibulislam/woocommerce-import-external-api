<?php 
namespace WCMystore;

use WCMystore\Mystore_Settings_API;

class Admin {

	public function __construct() {
		$this->settings_api = new Mystore_Settings_API();

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
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