<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/aliceheiman/clmte-ecommerce-integration
 * @since      1.0.0
 *
 * @package    Clmte
 * @subpackage Clmte/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Clmte
 * @subpackage Clmte/public
 * @author     CLMTE <info@clmte.com>
 */

global $woocommerce;

/**
 * Public CLMTE class.
 */
class Clmte_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since        1.0.0
	 * @param string $plugin_name   The name of the plugin.
	 * @param string $version       The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'wp_ajax_add_compensation_to_cart', 'add_compensation_to_cart' );
		add_action( 'wp_ajax_remove_compensation_from_cart', 'remove_compensation_from_cart' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Clmte_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Clmte_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/clmte-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Clmte_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Clmte_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/clmte-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'clmte',
			array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'reload_cart' => get_option( 'clmte_reload_cart_on_update' ),
			)
		);

	}

	/**
	 * Change cart data compensation product price based on settings
	 *
	 * @since    1.0.0
	 * @param string $cart_obj WooCommerce cart.
	 */
	public function before_calculate_totals( $cart_obj ) {

		$compensation_id = get_option( 'clmte_compensation_product_id' );

		foreach ( $cart_obj->get_cart() as $key => $item ) {
			if ( $item['product_id'] == $compensation_id ) {
				// Get product price.
				$offset_price = get_offset_price();

				// Update the price of compensation product.
				$item['data']->set_price( ( $offset_price ) );
			}
		}
	}

	/**
	 * Add compensation product to cart
	 *
	 * @since    1.0.0
	 */
	public function add_compensation_to_cart() {
		// Add compensation product to cart.
		WC()->cart->add_to_cart( get_option( 'clmte_compensation_product_id' ) );

		wp_die();
	}

	/**
	 * Remove compensation products from cart
	 *
	 * @since    1.0.0
	 */
	public function remove_compensation_from_cart() {
		// Remove all compensation products.
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( get_option( 'clmte_compensation_product_id' ) == $cart_item['product_id'] ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}

		wp_die();
	}

	/**
	 * Add checkbox with CLMTE compensation to cart
	 *
	 * @since    1.0.0
	 */
	public function clmte_add_offset_box() {

		// If custom placement is no, automatically add clmte offset box in cart.
		if ( get_option( 'clmte_custom_offset_placement' ) == false || get_option( 'clmte_custom_offset_placement' ) == 'no' ) {

			// Create the clmte offset box.
			clmte_create_offset_box();

		}
	}

	/**
	 * Add clmte receipt with tracking QR-Code automatically if custom placement has not been specified.
	 *
	 * @since    1.0.0
	 */
	public function clmte_thank_you() {

		// If custom placement is no, automatically add clmte receipt in the order details section.
		if ( get_option( 'clmte_custom_receipt_placement' ) == false || get_option( 'clmte_custom_receipt_placement' ) == 'no' ) {

			// Create CLMTE offset receipt with QR-code.
			clmte_create_receipt();

		}

	}

	/**
	 * Check if clmte carbon offset is purchased and send API post request to Tundra API.
	 *
	 * @since    1.0.0
	 * @param int $order_id The Woocommerce order identifier.
	 */
	public function clmte_purchase_carbon_offset( $order_id ) {
		// If not order_id, return.
		if ( ! $order_id ) {
			return;
		}

		// Allow code execution only once.
		if ( ! get_post_meta( $order_id, '_clmte_offset_purchased', true ) ) {

			// Reset CLMTE options.
			update_option( 'clmte-purchase', null );

			// Get an instance of the WC_Order object.
			$order = wc_get_order( $order_id );

			// Check if order is payed fully.
			if ( $order->is_paid() ) {

				// Loop through all order items to check if offset was purchased.
				foreach ( $order->get_items() as $item_id => $item ) {

					// Get the product object.
					$product = $item->get_product();

					// Get the product Id.
					$product_id = $product->get_id();

					// Check if product is carbon offset.
					if ( get_option( 'clmte_compensation_product_id' ) == $product_id ) {

						// Get the product quantity.
						$product_quantity = $item->get_quantity();

						// //////////////////////////////////
						// Send request to CLMTE tundra API
						// //////////////////////////////////

						// Get correct api url (production or sandbox).
						$url = get_clmte_url(
							'https://api.tundra.clmte.com/compensation',
							'https://api-sandbox.tundra.clmte.com/compensation'
						);

						// Get organisations api_key.
						$api_key = get_option( 'clmte_api_key' );

						$clmte_purchase = clmte_send_offset_request( $url, $api_key, $product_quantity );

						if ( array_key_exists( 'clmte-offset-error', $clmte_purchase ) ) { // Purchase failed.

							// Add log.
							clmte_create_log( "API POST request error: " . $clmte_purchase['clmte-offset-error'], 'error' );

							// Add purchase log.
							clmte_create_purchase_log( $product_quantity );

						} else { // Purchases succeeded.

							// Add purchase log.
							clmte_create_purchase_log(
								$product_quantity,
								'CREATED',
								$clmte_purchase['clmte-offset-id'] ?? null,
								$clmte_purchase['clmte-tracking-id'] ?? null,
								$clmte_purchase['clmte-offsets-carbon'] ?? null,
							);

							// Flag the action as done (to avoid repetitions on reload for example).
							$order->update_meta_data( '_clmte_offset_purchased', true );
							$order->save();

							// Sync PENDING offsets QUESTION.
							clmte_sync_offsets( 2 );

						}

						// Save clmte purchase options.
						update_option( 'clmte-purchase', $clmte_purchase );

						// Break!
						break;

					}
				}
			}
		}
	}
}
