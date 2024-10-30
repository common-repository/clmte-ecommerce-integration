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

// Get the purchases.
$table_name = $table_name = $wpdb->prefix . 'clmte_offsets_purchased';
$log_data   = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY time DESC" );

// Get how many pruchased which have not been synced with the CLMTE database.
$not_synced = count( $wpdb->get_results( "SELECT * FROM $table_name WHERE status = 'PENDING'" ) );

?>

<h1><?php esc_html_e( 'Purchases', 'clmte' ); ?></h1>

<?php if ( 0 !== $not_synced ) { // Not all purchases are synced. ?>

<p><?php echo esc_html( ( 1 == $not_synced ) ? ( __( '1 offset', 'clmte' ) ) : ( $not_synced . __( ' offsets', 'clmte' ) ) ); ?> <?php esc_html_e( 'not synchronised with CLMTE’s servers.', 'clmte' ); ?></p>
<p><i><?php esc_html_e( 'Note: A maximum of 2 PENDING offsets will be synced automatically at each subsequent offset purchase.', 'clmte' ); ?></i></p>
<button id="clmte-sync-offsets"><?php esc_html_e( 'Manual Synchronisation', 'clmte' ); ?></button>

<?php } ?>

<table class="clmte-table">
    <tr>
        <th><?php esc_html_e( 'Time', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Amount', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Status', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Kg CO2 offsetted', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Tracking id', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Offset id', 'clmte' ); ?></th>
    </tr>
    <?php foreach ( $log_data as $log ) { ?>
    <tr class="<?php echo esc_attr( $log->status ); ?>">
        <td><?php echo esc_html( $log->time ); ?></td>
        <td><?php echo esc_html( $log->amount ); ?></td>
        <td><?php echo esc_html( $log->status ); ?></td>
        <td><?php echo esc_html( $log->carbon_dioxide ); ?></td>
        <td><?php echo esc_html( $log->tracking_id ); ?></td>
        <td><?php echo esc_html( $log->offset_id ); ?></td>
    </tr>
    <?php } // End foreach. ?>
</table>

