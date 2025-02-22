<?php
/**
 * Woo Stripe Subscription Public
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
 * Wss Public Class
 *
 * @since  1.0.0
 * @class  Wss_Public
 */
class Wss_Public {
	
	/**
	 * @since  1.0.0
	 */
	public function __construct() {}
	
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * Enqueues a different set of scripts depending on the checkout method specified.
	 *
	 * @since  1.0.0
	 */
	public function wss_enqueue_scripts() {
		
		wp_enqueue_style(
			'wss-public-style',
			WSS_PLUGIN_DIR_URL . 'assets/css/style.css'
		);
		$settings = Wss::get_settings();
		$endpoint = $settings['myaccount_subscriptions_endpoint'];
		$endpoint_css = ".woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--{$endpoint} a::before {
    content: '\\f01e';
}";
		wp_add_inline_style( 'wss-public-style', $endpoint_css );
		//	load scripts on the 'checkout' and 'add payment method' pages.
		if( is_checkout() || is_add_payment_method_page() ) {
			$min = WSS_PLUGIN_DEBUG ? '' : '.min';
			//	get checkout method: either 'inline' or 'stripe'
			$checkout_method = Wss::checkout_method();
			if( $checkout_method === 'inline' || is_add_payment_method_page() ) {
				//	wc cc form js
				wp_enqueue_script( 'wc-credit-card-form' );
				//	wss public js
				wp_enqueue_script(
					'wss-inline-cc',
					WSS_PLUGIN_DIR_URL . 'assets/js/wss-inline-cc' . $min . '.js',
					array( 'jquery', 'stripe', 'wc-credit-card-form' ),
					WSS_PLUGIN_VERSION,
					true
				);
				//	stripe js
				wp_enqueue_script(
					'stripe',
					'https://js.stripe.com/v2/',
					array( 'jquery' ),
					WSS_PLUGIN_VERSION,
					true
				);
			} elseif( $checkout_method === 'stripe' ) {
				//	wss checkout js
				wp_enqueue_script(
					'wss-stripe-checkout',
					WSS_PLUGIN_DIR_URL . 'assets/js/wss-stripe-checkout' . $min . '.js',
					array( 'jquery', 'stripe-checkout' ),
					WSS_PLUGIN_VERSION,
					true
				);
				//	stripe checkout js
				wp_enqueue_script(
					'stripe-checkout',
					'https://checkout.stripe.com/checkout.js',
					array( 'jquery' ),
					WSS_PLUGIN_VERSION,
					true
				);
			}
		}
	}
}
