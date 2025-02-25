<?php
/**
 * WP Portal - Database Migrations
 *
 * Handles the creation of custom tables and addition of foreign key constraints.
 *
 * @package   WP_Portal
 * @author    Caleb Matteis
 * @license   GPL2
 * @link      https://example.com
 */

namespace WP_Portal\Migrations;

if (! defined( 'ABSPATH' )) {
    exit;
}

use wpdb; // Not strictly necessary, but clarifies usage for IDEs.

/**
 * Class WP_Portal_Migrations
 *
 * Responsible for creating/updating custom database tables
 * and manually adding foreign key constraints (since dbDelta() won't).
 */
class WP_Portal_Migrations {

    /**
     * Create all tables using dbDelta, then add foreign keys.
     *
     * @return void
     */
    public static function create_tables(): void {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset_collate = $wpdb->get_charset_collate();

        // 1) Clients Table
        $clients_table = $wpdb->prefix . 'portal_clients';
        $sql_clients   = "CREATE TABLE IF NOT EXISTS {$clients_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            company_name VARCHAR(255) NOT NULL,
            contact_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        dbDelta( $sql_clients );

        // 2) Projects Table.
        $projects_table = $wpdb->prefix . 'portal_projects';
        $sql_projects   = "CREATE TABLE IF NOT EXISTS {$projects_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            client_id BIGINT(20) UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            status VARCHAR(50) NOT NULL,
            due_date DATE,
            assigned_staff_user_id BIGINT(20) UNSIGNED,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        dbDelta( $sql_projects );

        // 3) Credentials Table.
        $credentials_table = $wpdb->prefix . 'portal_credentials';
        $sql_credentials   = "CREATE TABLE IF NOT EXISTS {$credentials_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            project_id BIGINT(20) UNSIGNED,
            label VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            password_enc TEXT,
            encryption_iv VARCHAR(255) NOT NULL,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        dbDelta( $sql_credentials );

        // 4) Updates Table.
        $updates_table = $wpdb->prefix . 'portal_updates';
        $sql_updates   = "CREATE TABLE IF NOT EXISTS {$updates_table} (
            id BIGINT (20) UNSIGNED NOT NULL AUTO_INCREMENT,
            project_id BIGINT (20) UNSIGNED,
            user_id BIGINT (20) UNSIGNED,
            message TEXT,
            type VARCHAR(50),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        dbDelta( $sql_updates );

        // Now add foreign keys.
        self::add_foreign_keys();
    }

    /**
     * Manually add FOREIGN KEY constraints after tables are created/updated.
     *
     * @return void
     */
    public static function add_foreign_keys(): void {
        global $wpdb;

        $clients_table     = $wpdb->prefix . 'portal_clients';
        $projects_table    = $wpdb->prefix . 'portal_projects';
        $credentials_table = $wpdb->prefix . 'portal_credentials';
        $updates_table     = $wpdb->prefix . 'portal_updates';

        // 1) projects_table.client_id → clients_table.id.
        if ( ! self::constraint_exists( $projects_table, 'fk_projects_clients') ) {
            $sql_fk_projects_clients = "ALTER TABLE {$projects_table}
                ADD CONSTRAINT fk_projects_clients
                FOREIGN KEY (client_id)
                REFERENCES {$clients_table}(id)
                ON DELETE CASCADE;";
            $wpdb->query( $sql_fk_projects_clients );

            if ( ! empty( $wpdb->last_error ) ) {
                error_log( 'WP_Portal: Error adding foreign key (fk_projects_clients): ' . $wpdb->last_error );
            }
        }

        // 2) projects_table.assigned_staff_user_id → wp_users(ID).
        // Use $wpdb->users to handle custom prefixes.
        $wp_users_table = $wpdb->users;
        if ( ! self::constraint_exists( $projects_table, 'fk_projects_wp_users' ) ) {
            $sql_fk_projects_wp_users = "ALTER TABLE {$projects_table}
                ADD CONSTRAINT fk_projects_wp_users
                FOREIGN KEY (assigned_staff_user_id)
                REFERENCES {$wp_users_table}(ID)
                ON DELETE SET NULL;";
            $wpdb->query( $sql_fk_projects_wp_users );

            if ( ! empty( $wpdb->last_error ) ) {
                error_log( 'WP_Portal: Error adding foreign key (fk_projects_wp_users): ' . $wpdb->last_error );
            }
        }

        // 3) credentials_table.project_id → projects_table.id.
        if ( ! self::constraint_exists( $credentials_table, 'fk_projects_credentials' ) ) {
            $sql_fk_projects_credentials = "ALTER TABLE {$credentials_table}
                ADD CONSTRAINT fk_projects_credentials
                FOREIGN KEY (project_id)
                REFERENCES {$projects_table}(id)
                ON DELETE CASCADE;";
            $wpdb->query( $sql_fk_projects_credentials );

            if ( ! empty( $wpdb->last_error ) ) {
                error_log( 'WP_Portal: Error adding foreign key (fk_projects_credentials): ' . $wpdb->last_error );
            }
        }

        // 4) updates_table.project_id → projects_table.id.
        if ( ! self::constraint_exists( $updates_table, 'fk_projects_project' )) {
            $sql_fk_projects_project = "ALTER TABLE {$updates_table}
                ADD CONSTRAINT fk_projects_project
                FOREIGN KEY (project_id)
                REFERENCES {$projects_table}(id)
                ON DELETE CASCADE;";
            $wpdb->query( $sql_fk_projects_project );

            if ( ! empty( $wpdb->last_error ) ) {
                error_log( 'WP_Portal: Error adding foreign key (fk_projects_project): ' . $wpdb->last_error );
            }
        }

        // 5) updates_table.user_id → wp_users.ID (optional).
        // If you want to track which WP user posted the update.
        if ( ! self::constraint_exists( $updates_table, 'fk_projects_user' ) ) {
            $sql_fk_projects_user = "ALTER TABLE {$updates_table}
                ADD CONSTRAINT fk_projects_user
                FOREIGN KEY (user_id)
                REFERENCES {$wp_users_table}(ID)
                ON DELETE SET NULL;";
            $wpdb->query( $sql_fk_projects_user );

            if ( ! empty( $wpdb->last_error ) ) {
                error_log( 'WP_Portal: Error adding foreign key (fk_projects_user): ' . $wpdb->last_error );
            }
        }
    }

    /**
     * Check if a specific constraint already exists on a table
     *
     * @param string $table_name
     * @param string $constraint_name
     * @return bool
     */
    private static function constraint_exists( string $table_name, string $constraint_name ): bool {
        global $wpdb;

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = %s
                AND TABLE_NAME = %s
                AND CONSTRAINT_NAME = %s
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
                DB_NAME,
                $table_name,
                $constraint_name
            )
        );

        return ( $result > 0 );
    }
}
