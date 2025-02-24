<?php

use WP_Portal\Migrations\WP_Portal_Migrations;



if( !defined( 'ABSPATH') ) {
    exit;
}



class WP_Portal_Plugin {

    public function __construct() {

        // Load dependencies.
        $this->load_dependencies();

        // Intialize hooks.
        $this->init_hooks();
    }

    public static function activate(): void {
        // Activation logic for database creation
        require_once plugin_dir_path(__FILE__) . 'class-wp-portal-migrations.php';
    
        WP_Portal_Migrations::create_tables();
        self::upgrade();
    }
    

    private function load_dependencies(): void {
        // Example: require files for Repositories, Admin UI, etc.
        // e.g., 
        // require_once plugin_dir_path(__FILE__) . 'class-wp-portal-repository.php';
        // require_once plugin_dir_path(__FILE__) . 'admin/class-wp-portal-admin.php';
    }

    private function init_hooks(): void {
        // Add action hooks or filters
        // Example:
        add_action('admin_menu', [$this, 'register_admin_menus']);
    }

    public function register_admin_menus(): void {
        // e.g.,
        // add_menu_page(
        //     'Portal', 
        //     'Portal', 
        //     'manage_options', 
        //     'wp-portal-dashboard', 
        //     [$this, 'render_dashboard'], 
        //     'dashicons-clipboard'
        // );
    }

    public static function upgrade(): void {
  
        $installed_version = get_option( 'wp_portal_db_version' ); // Fetch the current version

        if ( false === $installed_version ) {
            $installed_version = '1.0.0';
        }
  
        if ( version_compare( $installed_version, '1.0.0', '<' ) ) {
            //Run a 1.0.0 upgrade if needed
        }
  
         if ( version_compare( $installed_version, '1.0.1', '<' ) ) {

            //Add our foreign keys
            WP_Portal_Migrations::add_foreign_keys();
  
            update_option( 'wp_portal_db_version', '1.0.1' ); // Update the database version
        }
  
         if ( version_compare( $installed_version, '1.0.2', '<' ) ) {
            //Run a 1.0.2 upgrade if needed
  
            update_option( 'wp_portal_db_version', '1.0.2' ); // Update the database version
        }
  
       //More version upgrade conditionals as needed
    }
    
}
