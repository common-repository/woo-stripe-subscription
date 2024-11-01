<?php
/**
 * Woo Stripe Subscription Stripe Checkout
 *
 * Custom Checkout.js integration.
 *
 * @since       1.0.0
 * @package     wss
 * @subpackage  wss/includes
 * @author      Ramkumar
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright   (c) 2019 http://ramkumarelumalai.com/wss
 */

if( ! defined( 'ABSPATH' ) ) exit; // exit if accessed directly.

$current_user = wp_get_current_user();
$user_email = $current_user->user_email;
$cart_total = WC()->cart->total;
$attributes = array(
	'key' => esc_attr( Wss::get_api_key( 'publishable' ) ),
	'label' => esc_attr( $this->stripe_checkout_button ),
	'email' => esc_attr( $user_email ),
	'amount' => esc_attr( $this->wss_get_zero_decimal( $cart_total ) ),
	'name' => esc_attr( $this->wss_get_store_name() ),
	'currency' => esc_attr( strtolower( $this->currency ) ),
	'image' => esc_attr( $this->stripe_checkout_thumbnail ),
	'bitcoin' => $this->wss_supports('bitcoin') ? 'true' : 'false',
	'locale' => esc_attr( $this->stripe_checkout_locale ),
	'remember-me' => $this->stripe_checkout_remember_me ? 'true' : 'false',
	'refund-mispayments' => $this->bitcoin_refund_mispayments ? 'true' : 'false',
	'alipay' => $this->wss_supports('alipay') ? 'true' : 'false',
);

/**
 * Wss Stripe Checkout HTML
 *
 * @since  1.0.0
 */
?>

<div id="wss-data" <?php foreach( $attributes as $key => $attr ): ?>
<?php echo 'data-'.$key.'="'.$attr.'" '; ?>
<?php endforeach; ?>
></div>
