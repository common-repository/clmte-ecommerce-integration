<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://github.com/aliceheiman/clmte-ecommerce-integration
 * @since      1.0.0
 *
 * @package    Clmte
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete multiple options.
$options = array(
	'clmte_compensation_product_id',
	'clmte_offset_price',
	'clmte_img_id',
	'clmte_api_key',
	'clmte_organisation_id',
	'clmte_production_mode',
	'clmte_reload_cart_on_update',
	'clmte_custom_offset_placement',
	'clmte_custom_receipt_placement',
	'clmte_has_correct_credentials',
	'clmte-purchase',
);

foreach ( $options as $option ) {
	if ( get_option( $option ) ) {
		delete_option( $option );
	}
}

// Remove compensation product.
$product_id = get_option( 'clmte_compensation_product_id' );
if ( $product_id && '' !== $product_id ) {
	wp_delete_post( $product_id );
}

// Remove img.
$img_id = get_option( 'clmte_img_id' );
if ( $img_id && '' !== $img_id ) {
	wp_delete_attachment( $img_id );
}

// REMOVE TABLES.
global $wpdb;

// Remove log table.
$table_name = $wpdb->prefix . 'clmte_log';

$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query( $sql );

// Remove purchases table.
$table_name = $wpdb->prefix . 'clmte_offsets_purchased';

$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query( $sql );
