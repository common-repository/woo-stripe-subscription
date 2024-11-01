<?php
/**
 * Woo Stripe Subscription Internationalization
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
 * Wss i18n Class
 *
 * @since  1.0.0
 * @class  Wss_i18n
 */
class Wss_i18n {
	
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since  1.0.0
	 */
	public static function load_textdomain() {
		
		load_textdomain( 'woo-stripe-subscription', WP_LANG_DIR . '/wss/wss-' . get_locale() . '.mo' );
		load_plugin_textdomain(
			'woo-stripe-subscription',
			false,
			'woo-stripe-subscription/i18n/languages/'
		);
	}
}
