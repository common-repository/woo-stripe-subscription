<?php
/**
 * Woo Stripe Subscription Subscription Product
 *
 * Extends the WooCommerce simple product class.
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
 * Woo Stripe Subscription Subscription Product Class
 *
 * @since   1.0.0
 * @class   Wss_Subscription_Product
 * @extend  WC_Product_Simple
 */
class WC_Product_Wss_Subscription extends WC_Product_Simple {
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 */
	public function __construct( $product ) {
		
		parent::__construct( $product );
		if( version_compare( WC_VERSION, '3.0.0', '<' ) ) $this->product_type = 'wss_subscription';
		$this->product_custom_fields = get_post_meta( $this->id );
	}
	
	/**
	 * Declare the product type.
	 *
	 * @since   1.0.4
	 * @return  string
	 */
	public function get_type() {
	
		return 'wss_subscription';
	}
	
	/**
	 * Return the Stripe Plan ID string.
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function get_plan_id() {
		
		return $this->product_custom_fields['_wss_stripe_plan_id'][0];
	}
}
