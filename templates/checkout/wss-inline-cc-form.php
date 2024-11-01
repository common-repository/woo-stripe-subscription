<?php
/**
 * Woo Stripe Subscription Inline Credit Card Form
 *
 * The frontend markup for the credit card form.
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
 * Wss Inline Credit Card Form HTML
 * 
 * @since  1.0.0
 */
?>

<fieldset id="wss-cc-fieldset" class="wc-credit-card-form wc-payment-form" data-pkey="<?php echo esc_attr( Wss::get_api_key( 'publishable' ) ); ?>">
	<p class="form-row form-row-wide validate-required">
		<label for="wss-cc-number">
			<?php echo esc_html__( 'Card Number', 'woo-stripe-subscription' ); ?>
			<span class="required">*</span>
		</label>
		<input id="wss-cc-number" value="" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" data-stripe="number" />
	</p>
	<p class="form-row form-row-first validate-required">
		<label for="wss-cc-exp-month">
			<?php echo esc_html__( 'Expiry (MM)', 'woo-stripe-subscription' ); ?>
			<span class="required">*</span>
		</label>
		<input id="wss-cc-exp-month" value="" class="input-text" type="text" autocomplete="off" placeholder="<?php echo esc_attr( esc_html__( 'MM', 'woo-stripe-subscription' ) ); ?>" data-stripe="exp-month" />
	</p>
	<p class="form-row form-row-last validate-required">
		<label for="wss-cc-exp-year">
			<?php echo esc_html__( 'Expiry (YY)', 'woo-stripe-subscription' ); ?>
			<span class="required">*</span>
		</label>
		<input id="wss-cc-exp-year" value="" class="input-text" type="text" autocomplete="off" placeholder="<?php echo esc_attr( esc_html__( 'YY', 'woo-stripe-subscription' ) ); ?>" data-stripe="exp-year" />
	</p>
	<p class="form-row form-row-wide validate-required">
		<label for="wss-cc-cvc">
			<?php echo esc_html__( 'CVC', 'woo-stripe-subscription' ); ?>
			<span class="required">*</span>
		</label>
		<input id="wss-cc-cvc" value="" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="<?php echo esc_attr( esc_html__( 'CVC', 'woo-stripe-subscription' ) ); ?>" data-stripe="cvc" />
	</p>
<div class="clear"></div>
</fieldset>

