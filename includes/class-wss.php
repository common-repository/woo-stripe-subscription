<?php
/**
 * The core plugin class.
 *
 * Loads the plugin's dependencies and adds action and filter hooks.
 *
 * Also contains some public static methods for event logging and access
 * to stored settings, such as the checkout method, or the API keys.
 *
 * @since       1.0.0
 * @package     wss
 * @subpackage  wss/includes
 * @author      Ramkumar
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright   (c) 2019 http://ramkumarelumalai.com/wss
 */

if( ! defined( 'ABSPATH' ) ) exit; // exit if accessed directly.

/**
 * Wss Class
 *
 * @since  1.0.0
 * @class  Wss
 */
class Wss {

	/**
	 * The loader that's responsible for maintaining and registering the hooks that power the plugin.
	 *
	 * @since  1.0.0
	 * @var    Wss_Loader
	 */
	private $loader;

	/**
	 * Static reference to the gateway settings.
	 *
	 * @since  1.0.0
	 * @var    array
	 */
	private static $settings;

	/**
	 * Static reference to the log class.
	 *
	 * @since  1.0.0
	 * @var    WC_Logger
	 */
	private static $log;

	/**
	 * Load the dependencies and set the hooks.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_gateway_hooks();
		$this->define_endpoint_hooks();
		$this->define_subscription_hooks();
	}

	/**
	 * Load the required dependencies for this plugin:
	 *
	 * - Wss_Loader                   Action and filter hook organiser.
	 * - Wss_i18n                     Internationalization.
	 * - Wss_API                      Handles Stripe API transactions.
	 * - Wss_Endpoints                Registers endpoints.
	 * - Wss_Customer                 Handles all our customer data.
	 * - Wss_Gateway                  Defines the Wss WooCommerce payment gateway.
	 * - Wss_Admin                    Admin-specific functions.
	 * - Wss_Public                   Public-specific functions.
	 * - WC_Product_Wss_Subscription  Defines a new product type for subscriptions.
	 * - Wss_Subscriptions            Handles the subscription process.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wss-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wss-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wss-api.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wss-endpoints.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wss-customer.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wss-gateway.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wss-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wss-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-product-wss-subscription.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wss-subscriptions.php';
		$this->loader = new Wss_Loader();
	}

	/**
	 * Register endpoint hooks.
	 *
	 * @since  1.0.0
	 */
	private function define_endpoint_hooks() {

		$plugin_endpoints = new Wss_Endpoints();
		$this->loader->add_filter(
			'query_vars',
			$plugin_endpoints,
			'wss_endpoints_query_vars',
			0
		);
		$this->loader->add_filter(
			'woocommerce_account_menu_items',
			$plugin_endpoints,
			'wss_endpoints_woocommerce_account_menu_items'
		);
		$this->loader->add_filter(
			'woocommerce_account_' . $plugin_endpoints->get_endpoint() . '_endpoint',
			$plugin_endpoints,
			'wss_endpoints_woocommerce_account_wss_custom_endpoint_endpoint'
		);
		$this->loader->add_filter(
			'the_title',
			$plugin_endpoints,
			'wss_endpoints_wss_custom_endpoint_title'
		);
		$this->loader->add_filter(
			'woocommerce_saved_payment_methods_list',
			$plugin_endpoints,
			'wss_endpoints_woocommerce_saved_payment_methods_list',
			10,
			2
		);
		$this->loader->add_action(
			'woocommerce_payment_token_updated',
			$plugin_endpoints,
			'wss_endpoints_woocommerce_payment_token_updated',
			10,
			1
		);
	}

	/**
	 * Register all of the hooks related to the admin area.
	 *
	 * @since  1.0.0
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wss_Admin();
		$this->loader->add_filter(
			'plugin_action_links_'.WSS_PLUGIN_BASENAME,
			$plugin_admin,
			'wss_plugin_action_links'
		);
		$this->loader->add_filter(
			'plugin_row_meta',
			$plugin_admin,
			'wss_plugin_row_meta',
			10,
			2
		);
		$this->loader->add_filter(
			'woocommerce_payment_gateways',
			$plugin_admin,
			'wss_woocommerce_payment_gateways'
		);
		$this->loader->add_action(
			'admin_menu',
			$plugin_admin,
			'wss_admin_admin_menu'
		);
		$this->loader->add_action(
			'admin_enqueue_scripts',
			$plugin_admin,
			'wss_admin_enqueue_scripts'
		);
		$this->loader->add_filter(
			'woocommerce_get_price_html',
			$plugin_admin,
			'wss_woocommerce_get_price_html',
			100,
			2
		);
		$this->loader->add_filter(
			'woocommerce_cart_item_price',
			$plugin_admin,
			'wss_woocommerce_cart_item_price',
			20,
			3
		);
		$this->loader->add_filter(
			'woocommerce_cart_item_subtotal',
			$plugin_admin,
			'wss_woocommerce_cart_item_subtotal',
			20,
			3
		);
		$this->loader->add_filter(
			'woocommerce_order_formatted_line_subtotal',
			$plugin_admin,
			'wss_woocommerce_order_formatted_line_subtotal',
			20,
			3
		);
	}

	/**
	 * Register all of the hooks related to the gateway.
	 *
	 * @since  1.0.0
	 */
	private function define_gateway_hooks() {

		$plugin_gateway = new Wss_Payment_Gateway();
		$this->loader->add_action(
			'woocommerce_order_status_processing',
			$plugin_gateway,
			'wss_payment_gateway_woocommerce_order_status_processing'
		);
		$this->loader->add_action(
			'woocommerce_order_status_completed',
			$plugin_gateway,
			'wss_payment_gateway_woocommerce_order_status_completed'
		);
		$this->loader->add_action(
			'woocommerce_order_status_cancelled',
			$plugin_gateway,
			'wss_payment_gateway_woocommerce_order_status_cancelled'
		);
		$this->loader->add_action(
			'woocommerce_order_status_refunded',
			$plugin_gateway,
			'wss_payment_gateway_woocommerce_order_status_refunded'
		);
		$this->loader->add_filter(
			'woocommerce_coupon_get_discount_amount',
			$plugin_gateway,
			'wss_payment_gateway_woocommerce_coupon_get_discount_amount',
			10,
			4
		);
		$this->loader->add_action(
			'woocommerce_payment_token_set_default',
			$plugin_gateway,
			'wss_payment_gateway_woocommerce_payment_token_set_default'
		);
		$this->loader->add_action(
			'woocommerce_payment_token_deleted',
			$plugin_gateway,
			'wss_payment_gateway_woocommerce_payment_token_deleted',
			10,
			2
		);
	}

	/**
	 * Register all of the hooks related to the public area.
	 *
	 * @since  1.0.0
	 */
	private function define_public_hooks() {

		$plugin_public = new Wss_Public();
		$this->loader->add_action(
			'wp_enqueue_scripts',
			$plugin_public,
			'wss_enqueue_scripts'
		);
	}

	/**
	 * Register all of the hooks related to subscriptions.
	 *
	 * @since  1.0.0
	 */
	private function define_subscription_hooks() {

		$plugin_subscriptions = new Wss_Subscriptions();
		$this->loader->add_filter(
			'woocommerce_product_is_visible',
			$plugin_subscriptions,
			'wss_subscriptions_woocommerce_product_is_visible',
			10,
			2
		);
		$this->loader->add_filter(
			'woocommerce_is_sold_individually',
			$plugin_subscriptions,
			'wss_subscriptions_woocommerce_is_sold_individually',
			10,
			2
		);
		$this->loader->add_filter(
			'woocommerce_add_to_cart_validation',
			$plugin_subscriptions,
			'wss_subscriptions_woocommerce_add_to_cart_validation',
			10,
			2
		);
		$this->loader->add_filter(
			'woocommerce_is_purchasable',
			$plugin_subscriptions,
			'wss_subscriptions_woocommerce_is_purchasable',
			10,
			2
		);
		$this->loader->add_filter(
			'product_type_selector',
			$plugin_subscriptions,
			'wss_subscriptions_product_type_selector'
		);
		$this->loader->add_filter(
			'woocommerce_product_data_tabs',
			$plugin_subscriptions,
			'wss_subscriptions_woocommerce_product_data_tabs'
		);
		$this->loader->add_action(
			'woocommerce_product_data_panels',
			$plugin_subscriptions,
			'wss_subscriptions_woocommerce_product_data_panels'
		);
		$this->loader->add_action(
			'woocommerce_process_product_meta',
			$plugin_subscriptions,
			'wss_subscriptions_woocommerce_process_product_meta',
			100
		);
		$this->loader->add_action(
			'save_post',
			$plugin_subscriptions,
			'wss_subscriptions_save_post'
		);
		$this->loader->add_action(
			'woocommerce_wss_subscription_add_to_cart',
			$plugin_subscriptions,
			'wss_subscriptions_woocommerce_wss_subscription_add_to_cart',
			30
		);
		$this->loader->add_action(
			'pre_get_posts',
			$plugin_subscriptions,
			'wss_subscriptions_pre_get_posts',
			10,
			1
		);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since  1.0.0
	 */
	public function run() {

		$this->loader->run();
	}

	/**
	 * Return the checkout method.
	 *
	 * @since   1.0.0
	 * @return  string | null
	 */
	public static function checkout_method() {

		$settings = self::get_settings();
		if( isset( $settings['stripe_checkout_enabled'] ) ) {
			return $settings['stripe_checkout_enabled'] === 'enabled' ? 'stripe' : 'inline' ;
		}
		return null;
	}

	/**
	 * Are subscriptions enabled ?
	 *
	 * @since   1.0.0
	 * @return  boolean
	 */
	public static function subscriptions_enabled() {

		$settings = self::get_settings();
		if( isset( $settings['subscriptions_enabled'] ) ) {
			if( ! is_user_logged_in() && $settings['guest_subscriptions'] === 'disabled' ) {
				return false;
			}
			return $settings['subscriptions_enabled'] === 'yes' ? true : false ;
		}
		return false;
	}

	/**
	 * Return the appropriate API key.
	 *
	 * @since   1.0.0
	 * @param   string
	 * @return  string | null
	 */
	public static function get_api_key( $switch = null ) {

		$settings = self::get_settings();
		$mode = WSS_PLUGIN_MODE === 'wss_test' || ! is_ssl() ? 'test' : 'live';
		switch( $switch ) {
			case 'secret':
				return $mode === 'test' ? $settings['test_secret_key'] : $settings['live_secret_key'];
				break;
			case 'publishable':
				return $mode === 'test' ? $settings['test_publishable_key'] : $settings['live_publishable_key'];
				break;
			default:
				break;
		}
		return null;
	}

	/**
	 * Return the plugin settings.
	 *
	 * @since   1.0.0
	 * @return  array
	 */
	public static function get_settings() {

		if( empty( self::$settings ) )
			self::$settings = get_option( "woocommerce_". WSS_PLUGIN_ID . "_settings" );
		return self::$settings;
	}

	/**
	 * Static error and event logging.
	 *
	 * Usage: Wss::log( $message, $type );
	 *
	 * @since  1.0.0
	 * @param  string
	 */
	public static function log( $message, $type = null  ) {

		$datetime = new DateTime();
		$datetime = $datetime->format( 'Y-m-d H:i:s' );
		$timezone = date_default_timezone_get();
		$message = print_r( $message, true );

		if( defined( 'WSS_PLUGIN_DEBUG' ) && WSS_PLUGIN_DEBUG ) {
			if( empty( self::$log ) ) {
				self::$log = new WC_Logger();
			}
			self::$log->add( 'woo-stripe-subscription', $message );
			if( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				if( defined( 'WSS_PLUGIN_DEV' ) && WSS_PLUGIN_DEV ) {
					$type = preg_replace('/\s+/','',$type);
					if( ! isset( $type ) || empty( $type ) || is_null( $type ) ) {
						$type = 'error';
					}
					$filepath = WSS_PLUGIN_DIR_PATH."/log/$type.log";
					error_log(
						"[$datetime $timezone]\t$message\n",
						3,
						$filepath
					);
				} else {
					error_log( $message );
				}
			}
		}
	}

	/**
	 * Returns true if $currency is zero decimal currency.
	 *
	 * @since   1.0.5
	 * @param   string
	 * @return  boolean
	 */
	public static function is_zero_decimal( $currency ) {

		$zdcs = array(
			'BIF',
			'CLP',
			'DJF',
			'GNF',
			'JPY',
			'KMF',
			'KRW',
			'MGA',
			'PYG',
			'RWF',
			'VND',
			'VUV',
			'XAF',
			'XOF',
			'XPF',
		);
		return in_array( strtoupper( $currency ), $zdcs ) ? true : false;
	}

	/**
	 * Returns the order total in the smallest currency unit.
	 *
	 * @since   1.0.5
	 * @param   float
	 * @param   string
	 * @return  int
	 */
	public static function get_zero_decimal( $total = null, $currency ) {

		if( isset( $total ) ) {
			if( Wss::is_zero_decimal( $currency ) ) {
				$total = absint( $total );
			} else {
				$total = absint( round( $total, 2 ) * 100 );
			}
		}
		return $total;
	}

	/**
	 * Formats an amount from the smallest currency unit to the largest.
	 *
	 * 
	 *
	 * @since   1.0.5
	 * @param   float
	 * @param   string
	 * @return  float
	 */
	public static function format_currency_unit( $amount, $currency ) {

		if( ! Wss::is_zero_decimal( $currency ) ) {
			$amount = sprintf( '%0.2f', $amount / 100 );
		}
		return $amount;
	}
}
