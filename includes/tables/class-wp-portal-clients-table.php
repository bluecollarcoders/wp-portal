<?php

namespace WP_Portal\Tables;

use WP_List_Table;

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class Client_Table
 *
 * Handles displaying the clients list table in the admin panel.
 */
class Client_Table extends WP_List_Table {

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Client', 'wp-portal' ),
            'plural'   => __( 'Clients', 'wp-portal' ),
            'ajax'     => false
        ] );     
    }

    /**
     * Define the table columns
     *
     * @return array
     */
    public function get_columns(): array {
        return [
            'cb'           => '<input type="checkbox" />', // Checkbox for bulk actions
            'id'           => __( 'ID', 'wp-portal' ),
            'company_name' => __( 'Company Name', 'wp-portal' ),
            'contact_name' => __( 'Contact Name', 'wp-portal' ),
            'email'        => __( 'Email', 'wp-portal' ),
            'phone'        => __( 'Phone', 'wp-portal' ),
            'created_at'   => __( 'Created At', 'wp-portal' )
        ];
    }

    /**
     * Define sortable columns
     *
     * @return array
     */
    public function get_sortable_columns(): array {
        return [
            'id'           => [ 'id', false ],
            'company_name' => [ 'company_name', false ],
            'contact_name' => [ 'contact_name', false ],
            'email'        => [ 'email', false ],
            'created_at'   => [ 'created_at', false ],
        ];
    }

    /**
     * Prepares the items for displaying in the table
     *
     * @return void
     */
    public function prepare_items(): void {
        global $wpdb;

            $table_name   = $wpdb->prefix . 'portal_clients';
            $per_page     = 10;
            $current_page = $this->get_pagenum();
            $off_set      = ( $current_page - 1 ) * $per_page;

            // Sorting.
            $order_by = ! empty( $_GET['orderby'] ) ? sanitize_sql_orderby( $_GET['orderby'] ) : 'id';
            $order    = ! empty( $_GET['order'] ) ? strtoupper( sanitize_text_field( $_GET['order'] ) ) : 'ASC';

            // Get total count.
            $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );

            // Fetch data.
            $data = $wpdb->get_results(
                $wpdb->prepare( "SELECT * FROM {$table_name} ORDER BY {$order_by} {$order} LIMIT %d", $per_page, $off_set ),
                ARRAY_A
            );

            $this->items = $data;

            // Set pagination.
            $this->set_pagination_args( [
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page )
            ] );
    }

    /**
     * Default column rendering
     *
     * @param array  $item
     * @param string $column_name
     *
     * @return mixed|string
     */
    public function column_default( $item, $column_name ) {
        return $item[ $column_name ] ?? '';
    }

        /**
     * Checkbox column for bulk actions
     *
     * @param array $item
     * @return string
     */
    protected function column_cb( $item ): string {
        return sprintf( '<input type="checkbox" name="client[]" value="%s" />', $item['id'] );
    }

    /**
     * Get bulk actions
     *
     * @return array
     */
    protected function get_bulk_actions(): array {
        return [
            'delete' => __( 'Delete', 'wp-portal' ),
        ];
    }
}
