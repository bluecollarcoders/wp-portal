<?php
/**
 * WP Portal Plugin
 *
 * This file contains the main class for initializing the WP Portal plugin.
 *
 * @package   WP_Portal
 * @author    Caleb Matteis
 * @license   GPL-2.0+
 * @link      https://example.com
 * @since     1.0.0
 */

use WP_Portal\Migrations\WP_Portal_Migrations;
use WP_Portal\Admin\WP_Portal_Admin;



if( ! defined( 'ABSPATH') ) {
    exit;
}

/**
 * Class WP_Portal_Plugin
 *
 * Handles the initialization of the WP Portal plugin.
 */
class WP_Portal_Plugin {


    /**
     * WP_Portal_Plugin constructor.
     *
     * Loads dependencies and initializes hooks.
     */
    public function __construct() {

        // Load dependencies.
        $this->load_dependencies();
    }

    /**
     * Handles plugin activation tasks such as database creation.
     *
     * This function ensures that the necessary database tables are created
     * and runs any upgrade scripts as needed.
     *
     * @return void
     */
    public static function activate(): void {

        require_once plugin_dir_path(dirname(__FILE__)) . '/class-wp-portal-migrations.php';
    
        WP_Portal_Migrations::create_tables();
        self::upgrade();
    }
    

    /**
     * Loads required class files and initializes dependencies.
     *
     * @return void
     */
    private function load_dependencies(): void {
        require_once plugin_dir_path(__FILE__) . '/class-wp-portal-admin.php';
        new WP_Portal_Admin();
    }

    /**
     * Handles database upgrades based on versioning.
     *
     * Checks the stored database version and applies necessary migrations.
     *
     * @return void
     */
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
