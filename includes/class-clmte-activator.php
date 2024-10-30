<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/aliceheiman/clmte-ecommerce-integration
 * @since      1.0.0
 *
 * @package    Clmte
 * @subpackage Clmte/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Clmte
 * @subpackage Clmte/includes
 * @author     CLMTE <info@clmte.com>
 */
class Clmte_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;

		// Create compensation product if not already added.
		$product_id = get_option( 'clmte_compensation_product_id' );
		if ( ! $product_id || '' == $product_id ) {
			// Add compensation product on plugin activation.
			$post_id = wp_insert_post(
				array(
					'post_title'   => __( 'Carbon Offset', 'clmte' ),
					'post_content' => __( 'Carbon offset by CLMTE.', 'clmte' ),
					'post_status'  => 'publish',
					'post_type'    => 'product',
				)
			);

			// Publish product and set default price to 0.
			wp_set_object_terms( $post_id, 'simple', 'product_type' );
			update_post_meta( $post_id, '_price', '0' );

			// Save product id and option added.
			update_option( 'clmte_compensation_product_id', $post_id );
			update_option( 'clmte_offset_price', '' );
		}

		$post_id = get_option( 'clmte_compensation_product_id' );

		// Add img to product.
		if ( ! get_option( 'clmte_img_id' ) || '' == get_option( 'clmte_img_id' ) ) {

			// Upload img to media library.
			$desc = 'Carbon Offset powered by CLMTE';
			$file = 'https://i.postimg.cc/VLmp0crQ/compensation-Image.jpg';

			$file_array = array(
				'name'     => wp_basename( $file ),
				'tmp_name' => download_url( $file ),
			);

			// If error storing temporarily, return the error.
			if ( is_wp_error( $file_array['tmp_name'] ) ) {
				return $file_array['tmp_name'];
			}

			// Do the validation and storage stuff.
			$img_id = media_handle_sideload( $file_array, 0, $desc );

			// If error storing permanently, unlink.
			if ( is_wp_error( $img_id ) ) {
				@unlink( $file_array['tmp_name'] );
				return $img_id;
			}	

			// Do not show as product in shop or search.
			$terms = array( 'exclude-from-catalog', 'exclude-from-search' );
			wp_set_object_terms( $post_id, $terms, 'product_visibility' );

			// Save image id.
			update_option( 'clmte_img_id', $img_id );

		}

		// Get old or new img_id.
		$img_id = get_option( 'clmte_img_id' );

		// Set product image.
		update_post_meta( $post_id, '_thumbnail_id', $img_id );

		// ****************************
		// Add Custom Tables
		// ****************************

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();

		// Add log table.
		$table_name = $wpdb->prefix . 'clmte_log';

		// TYPES: error, activity.
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT CURRENT_TIMESTAMP,
			type VARCHAR(100),
			description text NOT NULL,
			PRIMARY KEY (id) 
		) $charset_collate;";

		dbDelta( $sql );

		// Add offsets purchased table.
		$table_name = $wpdb->prefix . 'clmte_offsets_purchased';

		/*
		Parameters:
			offset_id - id of purchased carbon offset
			tracking_id - tracking id of purchased carbon offset
			carbon_dioxide - CO2 compensated by the purchased carbon offset
			amount - how many carbon offsets purchased
			status - [CREATED or PENDING], if the purchase has been logged
		*/
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT CURRENT_TIMESTAMP,
            offset_id VARCHAR(200),
            tracking_id VARCHAR(300),
            carbon_dioxide INT,
			amount SMALLINT,
			status VARCHAR(10) DEFAULT 'PENDING',
			PRIMARY KEY (id) 
		) $charset_collate;";

		dbDelta( $sql );

		// Insert activated log.
		clmte_create_log( 'Plugin activated', 'activity' );
	}
}
