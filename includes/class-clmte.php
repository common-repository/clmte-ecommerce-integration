<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/aliceheiman/clmte-ecommerce-integration
 * @since      1.0.0
 *
 * @package    Clmte
 * @subpackage Clmte/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Clmte
 * @subpackage Clmte/includes
 * @author     CLMTE <info@clmte.com>
 */
class Clmte {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Clmte_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CLMTE_VERSION' ) ) {
			$this->version = CLMTE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'clmte';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Clmte_Loader. Orchestrates the hooks of the plugin.
	 * - Clmte_i18n. Defines internationalization functionality.
	 * - Clmte_Admin. Defines all hooks for the admin area.
	 * - Clmte_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-clmte-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-clmte-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-clmte-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-clmte-public.php';

		$this->loader = new Clmte_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Clmte_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Clmte_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Clmte_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add plugin settings to WooCommerce.
		$this->loader->add_filter( 'woocommerce_get_settings_pages', $plugin_admin, 'clmte_add_settings' );

		// Update offset price.
		$this->loader->add_action(
			'wp_ajax_clmte_update_offset_price',
			$plugin_admin,
			'clmte_update_offset_price',
		);
		$this->loader->add_action(
			'wp_ajax_nopriv_clmte_update_offset_price',
			$plugin_admin,
			'clmte_update_offset_price'
		);

		// Sync offsets.
		$this->loader->add_action(
			'wp_ajax_clmte_trigger_sync_offsets',
			$plugin_admin,
			'clmte_trigger_sync_offsets'
		);
		$this->loader->add_action(
			'wp_ajax_nopriv_clmte_trigger_sync_offsets',
			$plugin_admin,
			'clmte_trigger_sync_offsets'
		);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Clmte_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Add checkbox with climte compensation to checkout cart.
		$this->loader->add_action( 'woocommerce_after_cart_table', $plugin_public, 'clmte_add_offset_box' );

		// Add compensation to checkbox.
		$this->loader->add_action(
			'wp_ajax_add_compensation_to_cart',
			$plugin_public,
			'add_compensation_to_cart'
		);
		$this->loader->add_action(
			'wp_ajax_nopriv_add_compensation_to_cart',
			$plugin_public,
			'add_compensation_to_cart'
		);

		// Change price.
		$this->loader->add_action(
			'woocommerce_before_calculate_totals',
			$plugin_public,
			'before_calculate_totals'
		);

		// Remove compensation from checkout.
		$this->loader->add_action(
			'wp_ajax_remove_compensation_from_cart',
			$plugin_public,
			'remove_compensation_from_cart'
		);
		$this->loader->add_action(
			'wp_ajax_nopriv_remove_compensation_from_cart',
			$plugin_public,
			'remove_compensation_from_cart'
		);

		// Send request to CLMTE server if a carbon offset has been purchased.
		$this->loader->add_action(
			'woocommerce_payment_complete',
			$plugin_public,
			'clmte_purchase_carbon_offset'
		);

		// Display the clmte receipt in order details section.
		$this->loader->add_action(
			'woocommerce_thankyou',
			$plugin_public,
			'clmte_thank_you'
		);

		// Custom shortcodes.
		add_shortcode(
			'clmte-offset',
			'clmte_create_offset_box'
		);
		add_shortcode(
			'clmte-receipt',
			'clmte_create_receipt'
		);

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Clmte_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
