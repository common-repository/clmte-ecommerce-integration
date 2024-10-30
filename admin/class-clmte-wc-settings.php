<?php
/**
 * Extends the WC_Settings_Page class
 *
 * @link        https://paulmiller3000.com
 * @since       1.0.0
 *
 * @package     clmte
 * @subpackage  clmte/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Clmte_WC_Settings' ) ) {

    /**
     * Settings class
     *
     * @since 1.0.0
     * 
     */
    class Clmte_WC_Settings extends WC_Settings_Page {

        /**
         * Constructor
         * 
         * @since  1.0
         */
        public function __construct() {

            $this->id    = 'clmte';
            $this->label = __( 'CLMTE', 'clmte' );

            // Define all hooks instead of inheriting from parent.
            add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
            add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
            add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
            add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
            
        }


        /**
         * Get sections.
         *
         * @return array
         */
        public function get_sections() {
            $sections = array(
                ''          => __( 'Settings', 'clmte' ),
                'log'       => __( 'Logs', 'clmte' ),
                'purchases' => __( 'Purchases', 'clmte' ),
            );

            return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
        }


        /**
         * Get settings array
         *
         * @return array
         */
        public function get_settings() {

            global $current_section;
            $prefix = 'clmte_';

            switch ( $current_section ) {
                case 'log':
                    $settings = array(
                        array(),
                    );
                    break;
                case 'purchases':
                    $settings = array(
                        array(),
                    );
                    break;
                default:
                    include 'partials/clmte-settings-main.php';
            } 

            return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
        }

        /**
         * Output the settings
         */
        public function output() {
            global $current_section;

            switch ( $current_section ) {
                case 'usage':
                    include 'partials/clmte-settings-usage.php';
                    break;
                case 'log':
                    include 'partials/clmte-settings-log.php';
                    break;
                case 'purchases':
                    include 'partials/clmte-settings-purchases.php';
                    break;
                default:
                    // Check if API key and Organisation ID are correct.
                    $has_correct_credentials = get_option( 'clmte_has_correct_credentials', false );

                    // If not, display error warning.
                    if ( false == $has_correct_credentials ) {
                        echo '<div class="notice notice-warning is-dismissible">';
                        echo ( '<p>' . esc_html__( 'The API key and/or the Organisation ID seem to be missing or be incorrect.', 'clmte' ) . '</p>' );
                        echo '</div>';
                    }

                    $settings = $this->get_settings();
                    WC_Admin_Settings::output_fields( $settings );

                    clmte_align_offset_price();

                    ?>
                    <h2><?php esc_html_e( 'CLMTE Offsets', 'clmte' ); ?></h3>
                    <p><b><?php esc_html_e( 'Offset Price:', 'clmte' ); ?></b> <?php echo esc_html( ( $has_correct_credentials ) ? get_option( 'clmte_offset_price' ) . ' ' . get_woocommerce_currency() : __( 'Price not available', 'clmte' ) ); ?></p>
                    <button id="update-offset-price"><?php esc_html_e( 'Update Price', 'clmte') ; ?></button>

                    <h2 id="shortcodes"><?php esc_html_e( 'Shortcodes', 'clmte' ); ?></h3>
                    <p><b><?php esc_html_e( 'CLMTE Offset Box:', 'clmte' ); ?></b> [clmte-offset]</p>
                    <p><b><?php esc_html_e( 'CLMTE Receipt:', 'clmte' ); ?></b> [clmte-receipt]</p>
                    <?php
            }
        }

        /**
         * Save settings
         *
         * @since 1.0
         */
        public function save() {
            $settings = $this->get_settings();

            WC_Admin_Settings::save_fields( $settings );

            // Check if credentials are correct.
            clmte_check_credentials();
        }
    }
}


return new Clmte_WC_Settings();
