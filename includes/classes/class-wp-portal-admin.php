<?php
/**
 * WP Portal Admin Class
 *
 * This class handles the registration of admin menus and pages
 * for the WP Portal plugin.
 *
 * @package   WP_Portal
 * @author    Caleb Matteis
 * @license   GPL-2.0+
 * @link      https://example.com
 * @since     1.0.0
 */

namespace WP_Portal\Admin;

use WP_Portal\Tables\Client_Table;
use WP_Portal\Repositories\Clients_Repository;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class WP_Portal_Admin
 *
 * Handles admin menu and page registration.
 */
class WP_Portal_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->_setup_hooks();
    }

    /**
     * Setup hooks
     */
    public function _setup_hooks() {
        add_action( 'admin_menu', [$this, 'register_admin_menus'] );
        add_action( 'admin_init', [$this, 'handle_insert_client'] );
    }

    /**
     * Registers the admin menus and submenus for the plugin.
     *
     * Creates the main Client Portal dashboard along with submenus
     * for managing clients, projects, credentials, and updates.
     *
     * @return void
     */
    public function register_admin_menus(): void {
        add_menu_page(
            'Client Portal', 
            'Client Portal', 
            'manage_options', 
            'wp-portal-dashboard', 
            [$this, 'render_dashboard'], 
            'dashicons-clipboard', 
            25
        );

        add_submenu_page(
            'wp-portal-dashboard',
            'Clients',
            'Clients',
            'manage_options',
            'wp-portal-clients',
            [$this, 'render_clients_page']
        );

        add_submenu_page(
            'wp-portal-dashboard',
            'Projects',
            'Projects',
            'manage_options',
            'wp-portal-projects',
            [$this, 'render_projects_page']
        );

        add_submenu_page(
            'wp-portal-dashboard',
            'Credentials',
            'Credentials',
            'manage_options',
            'wp-portal-credentials',
            [$this, 'render_credentials_page']
        );

        add_submenu_page(
            'wp-portal-dashboard',
            'Updates',
            'Updates',
            'manage_options',
            'wp-portal-updates',
            [$this, 'render_updates_page']
        );
    }

    /**
     * Displays the main dashboard page.
     *
     * @return void
     */
    public function render_dashboard(): void {
        echo '<div class="wrap"><h1>Client Portal Dashboard</h1></div>';
    }

    /**
     * Displays the Clients page.
     *
     * @return void
     */
    public function render_clients_page() {
        echo '<div class="wrap"><h1>Clients</h1>';
    
        // Simple form
        echo '<form method="post">';
        wp_nonce_field('insert_client_action', 'insert_client_nonce');
        echo '<input type="text" name="company_name" placeholder="Company Name" required>';
        echo '<input type="text" name="contact_name" placeholder="Contact Name" required>';
        echo '<input type="email" name="email" placeholder="Email" required>';
        echo '<button type="submit" name="submit_client" class="button button-primary">Add Client</button>';
        echo '</form>';
    
        // Display the WP_List_Table
        $client_table = new Client_Table();
        $client_table->prepare_items();
        echo '<form method="post">';
        $client_table->display();
        echo '</form>';
    
        echo '</div>';
    }
    
    /**
     * Undocumented function
     *
     * @return void
     */
    public function handle_insert_client() {
        if (isset($_POST['submit_client'])) {
            check_admin_referer('insert_client_action', 'insert_client_nonce');
    
            $data = [
                'company_name' => sanitize_text_field($_POST['company_name']),
                'contact_name' => sanitize_text_field($_POST['contact_name']),
                'email'        => sanitize_email($_POST['email']),
                'created_at'   => current_time('mysql'),
            ];
    
            $repo = new Clients_Repository();
            $repo->insert_clients( $data );
    
            // Refresh page to show new client
            wp_redirect(admin_url('admin.php?page=wp-portal-clients'));
            exit;
        }
    }
    

    /**
     * Displays the Projects page.
     *
     * @return void
     */
    public function render_projects_page(): void {
        echo '<div class="wrap"><h1>Projects</h1></div>';
    }

    /**
     * Displays the Credentials page.
     *
     * @return void
     */
    public function render_credentials_page(): void {
        echo '<div class="wrap"><h1>Credentials</h1></div>';
    }

    /**
     * Displays the Updates page.
     *
     * @return void
     */
    public function render_updates_page(): void {
        echo '<div class="wrap"><h1>Updates</h1></div>';
    }
}
