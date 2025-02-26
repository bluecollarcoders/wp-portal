<?php
/**
 * Class for Clients Repository.
 */

declare( strict_types=1 );

namespace WP_Portal\Repositories;

/**
 * Class Clients Repository 
 *
 * Handles displaying the clients list table in the admin panel.
 */
class Clients_Repository {

    /**
     * Undocumented variable
     *
     * @var \wpdb
     */
    protected \wpdb $wpdb;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected string $table;

    /**
     * Construct method.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'portal_clients';
    }

    /**
     * Insert a new client record
     *
     * @param array $data (company_name, contact_name, email, etc.)
     * @return int|false The inserted ID, or false on failure
     */
    public function insert_clients ( array $data ) {
        $this->wpdb->insert( $this->table, $data );
        return $this->wpdb->insert_id;
    }

    /**
     * Get all clients
     *
     * @return array
     */
    public function get_all_clients(): array {
        return $this->wpdb->get_results( "SELECT * FROM {$this->table}", ARRAY_A );
    }

    /**
     * Get a single client
     *
     * @param int $id
     * @return array|null
     */
    public function get_clients( $id ): array {
        return $this->wpdb->get_row(
            $this->wpdb->get_row(
                $this->wpdb->prepare( "SELECT * FROM {$this->table} WHERE id %d", $id ),
            ),
            ARRAY_A
        );
    }

    /**
     * Update a client
     *
     * @param int   $id
     * @param array $data
     * @return bool|int
     */
    public function update_client( $id, $data ) {
        return $this->wpdb->update(
            $this->table,
            $data,
            [ 'id' => $id ]
        );
    }

    /**
     * Delete a client
     *
     * @param int $id
     * @return bool|int
     */
    public function delete_client( $id ) {
        return $this->wpdb->delete(
            $this->table,
            [ 'id' => $id ]
        );
    }

    /**
     * Count total clients for pagination.
     *
     * @return void
     */
    public function count_clients() {
        return (int) $this->wpdb->get_var( "SELECT COUNT(*) FROM {$this->table}" );
    }

    /**
     * Get clients with pagination & sorting
     */
    public function get_clients_paginated( $order_by, $order, $limit, $offset ) {
        // Validate / sanitize $orderby if you only allow certain columns.
        $allowed_columns = [ 'id', 'company_name', 'email', 'created_at' ];

        if ( ! in_array( $order_by, $allowed_columns ) ) {
            $order_by = 'id';
        }

        $order = $order === 'DESC' ? 'DESC' : 'ASC';

        return $this->wpdb->get_results(
            $this->wpdb->prepare(
            "SELECT * FROM {$this->table} ORDER BY {$order_by} {$order} LIMIT %d OFFSET %d",
            $limit,
            $offset
            ),
            ARRAY_A 
        );

    }
}
