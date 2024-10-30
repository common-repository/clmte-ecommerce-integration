<?php
/**
 * Displays a table in the WC Settings page
 *
 * @link        https://paulmiller3000.com
 * @since       1.0.0
 *
 * @package     P3k_Galactica
 * @subpackage  P3k_Galactica/admin
 */

$GLOBALS['hide_save_button'] = true;

global $wpdb;

$table_name = $wpdb->prefix . 'clmte_log';
$log_data   = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY time DESC" );

?>

<h1><?php esc_html_e( 'CLMTE Plugin Logs', 'clmte' ); ?></h1>

<table class="clmte-table">
    <tr>
        <th><?php esc_html_e( 'Type', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Description', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Time', 'clmte' ); ?></th>
    </tr>
    <?php foreach ( $log_data as $log ) { ?>
    <tr class="<?php echo esc_attr( $log->type ); ?>">
        <td><?php echo esc_html( $log->type ); ?></td>
        <td><?php echo esc_html( $log->description ); ?></td>
        <td><?php echo esc_html( $log->time ); ?></td>
    </tr>
    <?php } // End foreach. ?>
</table>

