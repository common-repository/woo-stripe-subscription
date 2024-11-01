<?php
/**
 * Woo Stripe Subscription Settings
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
 * Woo Stripe Subscription Settings
 *
 * @since   1.0.0
 * @return  array
 */
return array(
	
	/**
	 * Is the gateway enabled.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'enabled' => array(
		'title' => esc_html__( 'Enable/Disable', 'woo-stripe-subscription' ),
		'label' => esc_html__( 'Enable&nbsp;', 'woo-stripe-subscription' ).WSS_PLUGIN_TITLE,
		'type' => 'checkbox',
		'description' => '',
		'default' => 'no',
	),
	
	/**
	 * Gateway mode: live or test.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'mode' => array(
		'title' => esc_html__( 'Mode', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Run the plugin in test mode or go live.', 'woo-stripe-subscription' ),
		'desc_tip' => true,
		'default' => 'test',
		'options' => array(
			'test' => esc_html__( 'Test', 'woo-stripe-subscription' ),
			'live' => esc_html__( 'Live', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * Authorize or capture.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'payment_action' => array(
		'title' => esc_html__( 'Payment Action', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Capture funds immediately or authorize payment only.', 'woo-stripe-subscription' ),
		'default' => 'capture',
		'desc_tip' => true,
		'options' => array(
			'capture' => esc_html__( 'Capture', 'woo-stripe-subscription' ),
			'authorize' => esc_html__( 'Authorize', 'woo-stripe-subscription' )
		),
	),
	
	/**
	 * Enabled saved payment methods for registered users.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'save_payment_method' => array(
		'title' => esc_html__( 'Save Payment Method', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Allow registered customers to save their (tokenized) payment method(s) for future use.', 'woo-stripe-subscription' ),
		'default' => 'enabled',
		'desc_tip' => true,
		'options' => array(
			'enabled' => esc_html__( 'Enabled', 'woo-stripe-subscription' ),
			'disabled' => esc_html__( 'Disabled', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * Section: API Keys
	 *
	 * @since  1.0.0
	 */
	'api_keys' => array(
		'title' => sprintf( esc_html__( '%sAPI Keys', 'woo-stripe-subscription' ), '<hr><br>' ),
		'type' => 'title',
		'description' => sprintf( esc_html__( 'You can find your API keys on your %sStripe dashboard%s.', 'woo-stripe-subscription' ), '<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">', '</a>' ),
	),
	
	/**
	 * Live secret key.
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'live_secret_key' => array(
		'title' => esc_html__( 'Live Secret Key', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'Live Secret Key', 'woo-stripe-subscription' ),
		'default' => esc_html__( '', 'woo-stripe-subscription' ),
		'desc_tip' => true,
	),
	
	/**
	 * Live publishable key.
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'live_publishable_key' => array(
		'title' => esc_html__( 'Live Publishable Key', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'Live Publishable Key', 'woo-stripe-subscription' ),
		'default' => esc_html__( '', 'woo-stripe-subscription' ),
		'desc_tip' => true,
	),
	
	/**
	 * Test secret key.
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'test_secret_key' => array(
		'title' => esc_html__( 'Test Secret Key', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'Test Secret Key', 'woo-stripe-subscription' ),
		'default' => esc_html__( '', 'woo-stripe-subscription' ),
		'desc_tip' => true,
	),
	
	/**
	 * Test publishable key.
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'test_publishable_key' => array(
		'title' => esc_html__( 'Test Publishable Key', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'Test Publishable Key', 'woo-stripe-subscription' ),
		'default' => esc_html__( '', 'woo-stripe-subscription' ),
		'desc_tip' => true,
	),
	
	/**
	 * Section: Subscriptions
	 *
	 * @since  1.0.0
	 */
	'subscriptions' => array(
		'title' => sprintf( esc_html__( '%sSubscriptions', 'woo-stripe-subscription' ), '<hr><br>' ),
		'type' => 'title',
		'description' => esc_html__( 'Woo Stripe Subscription defines a new product type that connects your store to Stripe\'s Subscriptions API.', 'woo-stripe-subscription' ),
	),
	
	/**
	 * Are subscriptions enabled ?
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'subscriptions_enabled' => array(
		'title' => esc_html__( 'Enable/Disable', 'woo-stripe-subscription' ),
		'label' => esc_html__( 'Enable Subscriptions', 'woo-stripe-subscription' ),
		'type' => 'checkbox',
		'description' => '',
		'default' => 'no',
	),
	
	/**
	 * Enable guest subscriptions.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'guest_subscriptions' => array(
		'title' => esc_html__( 'Guest Subscriptions', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Allow un-registered customers to view and/or purchase subscription products.', 'woo-stripe-subscription' ),
		'default' => 'disabled',
		'desc_tip' => true,
		'options' => array(
			'disabled' => esc_html__( 'Disabled', 'woo-stripe-subscription' ),
			'read_only' => esc_html__( 'View Subscriptions Only', 'woo-stripe-subscription' ),
			'enabled' => esc_html__( 'View and Purchase Subscriptions', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * Section: My Account
	 *
	 * @since  1.0.0
	 */
	'myaccount' => array(
		'title' => sprintf( esc_html__( '%sMy Account', 'woo-stripe-subscription' ), '<hr><br>' ),
		'type' => 'title',
		'description' => esc_html__( 'Allow registered customers to view/cancel their purchased subscriptions.', 'woo-stripe-subscription' ),
	),
	
	/**
	 * Enable guest subscriptions.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'myaccount_subscriptions' => array(
		'title' => esc_html__( 'Enable/Disable', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Allow registered customers to view/cancel their subscriptions.', 'woo-stripe-subscription' ),
		'default' => 'disabled',
		'desc_tip' => true,
		'options' => array(
			'disabled' => esc_html__( 'Disabled', 'woo-stripe-subscription' ),
			'read_only' => esc_html__( 'View Subscriptions Only', 'woo-stripe-subscription' ),
			'enabled' => esc_html__( 'View and Cancel Subscriptions', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * Payment method title (frontend).
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'myaccount_subscriptions_endpoint' => array(
		'title' => esc_html__( 'Endpoint', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'URL suffix to handle My Account actions. Must be unique and contain no special characters.', 'woo-stripe-subscription' ),
		'default' => esc_html__( 'wss-custom-endpoint', 'woo-stripe-subscription' ),
		'desc_tip' => true,
	),
	
	/**
	 * Payment method title (frontend).
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'myaccount_subscriptions_title' => array(
		'title' => esc_html__( 'Endpoint Title', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'The endpoint title. ', 'woo-stripe-subscription' ),
		'default' => esc_html__( 'Subscriptions', 'woo-stripe-subscription' ),
		'desc_tip' => true,
	),
	
	/**
	 * Section: Customize
	 *
	 * @since  1.0.0
	 */
	'customize' => array(
		'title' => sprintf( esc_html__( '%sCustomize', 'woo-stripe-subscription' ), '<hr><br>' ),
		'type' => 'title',
		'description' => sprintf( esc_html__( 'Change the plugin\'s appearance.', 'woo-stripe-subscription' ), '<a href="/docs" target="_blank">', '</a>' ),
	),
	
	/**
	 * Payment method title (frontend).
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'title' => array(
		'title' => esc_html__( 'Title', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'The payment method title.', 'woo-stripe-subscription' ),
		'default' => WSS_PLUGIN_TITLE,
		'desc_tip' => true,
	),
	
	/**
	 * A description of the payment method (frontend).
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'description' => array(
		'title' => esc_html__( 'Description', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'A description of the payment method which appears on the checkout.', 'woo-stripe-subscription' ),
		'default' => '',
		'desc_tip' => true,
	),
	
	/**
	 * Overrides the text displayed in the #place_order button on the checkout page (frontend).
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'order_button_text' => array(
		'title' => esc_html__( 'Place Order Button', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'Text displayed on the checkout place order button.', 'woo-stripe-subscription' ),
		'default' => esc_html__( 'Place Order', 'woo-stripe-subscription' ),
		'desc_tip' => true,
	),
	
	/**
	 * Section: Stripe Checkout
	 *
	 * @since  1.0.0
	 */
	'stripe_checkout' => array(
		'title' => sprintf( esc_html__( '%sStripe Checkout', 'woo-stripe-subscription' ), '<hr><br>' ),
		'type' => 'title',
		'description' => sprintf( esc_html__( '%sStripe Checkout%s is an embeddable payment form that, if enabled, replaces the inline credit card form on the checkout page.', 'woo-stripe-subscription' ), '<a href="https://stripe.com/checkout" target="_blank">', '</a>' ),
	),
	
	/**
	 * Enable/disable Stripe Checkout.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'stripe_checkout_enabled' => array(
		'title' => esc_html__( 'Enable/Disable', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Enable or disable the Stripe Checkout embedded form.', 'woo-stripe-subscription' ),
		'desc_tip' => true,
		'default' => 'disabled',
		'options' => array(
			'disabled' => esc_html__( 'Disabled', 'woo-stripe-subscription' ),
			'enabled' => esc_html__( 'Enabled', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * Gateway mode: live or test.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'stripe_checkout_remember_me' => array(
		'title' => esc_html__( 'Remember Me', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Specify whether to include the option to "Remember Me" for future purchases.', 'woo-stripe-subscription' ),
		'desc_tip' => true,
		'default' => 'enabled',
		'options' => array(
			'disabled' => esc_html__( 'Disabled', 'woo-stripe-subscription' ),
			'enabled' => esc_html__( 'Enabled', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * The label of the payment button in the Checkout form (e.g. Subscribe, Pay {{amount}}, etc.).
	 * If you include {{amount}} in the label value, it will be replaced by a localized version of data-amount. 
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'stripe_checkout_button' => array(
		'title' => esc_html__( 'Pay Button', 'woo-stripe-subscription' ),
		'type' => 'text',
		'description' => esc_html__( 'Displayed on the pop-up modal submit button.', 'woo-stripe-subscription' ),
		'default' => esc_html__( 'Pay {{amount}}', 'woo-stripe-subscription' ),
		'desc_tip' => true,
	),
	
	/**
	 * Specify auto to display Checkout in the user's preferred language, if available. English will be used by default.
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'stripe_checkout_locale' => array(
		'title' => esc_html__( 'Locale', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Specify auto to display Checkout in the user\'s preferred language, if available.', 'woo-stripe-subscription' ),
		'default' => 'en',
		'desc_tip' => true,
		'options' => array(
			'auto' => esc_html__( 'Auto', 'woo-stripe-subscription' ),
			'zh' => esc_html__( 'Simplified Chinese', 'woo-stripe-subscription' ),
			'nl' => esc_html__( 'Dutch', 'woo-stripe-subscription' ),
			'en' => esc_html__( 'English', 'woo-stripe-subscription' ),
			'fr' => esc_html__( 'French', 'woo-stripe-subscription' ),
			'de' => esc_html__( 'German', 'woo-stripe-subscription' ),
			'it' => esc_html__( 'Italian', 'woo-stripe-subscription' ),
			'ja' => esc_html__( 'Japanese', 'woo-stripe-subscription' ),
			'es' => esc_html__( 'Spanish', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * A relative or absolute URL pointing to a square image of your brand or product.
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'stripe_checkout_thumbnail' => array(
		'title' => esc_html__( 'Thumbnail', 'woo-stripe-subscription' ),
		'description' => esc_html__( 'A relative or absolute URL pointing to a square image of your brand or product. The recommended minimum size is 128x128px. The supported image types are: .gif, .jpeg, and .png.', 'woo-stripe-subscription' ),
		'type' => 'text',
		'default' => '',
		'desc_tip' => true,
	),
	
	/**
	 * Enable Bitcoin support.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'bitcoin' => array(
		'title' => esc_html__( 'Bitcoin', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Accept Bitcoin through Stripe Checkout (only available for USD). Note: funds in Bitcoin transactions are captured immediately.', 'woo-stripe-subscription' ),
		'default' => 'disabled',
		'desc_tip' => true,
		'options' => array(
			'enabled' => esc_html__( 'Enabled', 'woo-stripe-subscription' ),
			'disabled' => esc_html__( 'Disabled', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * Automatically refund Bitcoin mispayments after one hour.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'bitcoin_refund_mispayments' => array(
		'title' => esc_html__( 'Refund Mispayments', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Automatically refund Bitcoin mispayments after one hour.', 'woo-stripe-subscription' ),
		'default' => 'enabled',
		'desc_tip' => true,
		'options' => array(
			'enabled' => esc_html__( 'Enabled', 'woo-stripe-subscription' ),
			'disabled' => esc_html__( 'Disabled', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * Enable Bitcoin support.
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'alipay' => array(
		'title' => esc_html__( 'Alipay', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => esc_html__( 'Accept Alipay through Stripe Checkout (only available for USD). Note: funds in Alipay transactions are captured immediately.', 'woo-stripe-subscription' ),
		'default' => 'disabled',
		'desc_tip' => true,
		'options' => array(
			'enabled' => esc_html__( 'Enabled', 'woo-stripe-subscription' ),
			'disabled' => esc_html__( 'Disabled', 'woo-stripe-subscription' ),
		),
	),
	
	/**
	 * Section: Advanced Options
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	'advanced_options' => array(
		'title' => sprintf( esc_html__( '%sAdvanced Options', 'woo-stripe-subscription' ), '<hr><br>' ),
		'type' => 'title',
		'description' => esc_html__( '', 'woo-stripe-subscription' ),
	),
	
	/**
	 * Debug
	 *
	 * @since  1.0.0
	 * @var    boolean
	 */
	'debug' => array(
		'title' => esc_html__( 'Debug', 'woo-stripe-subscription' ),
		'type' => 'select',
		'class' => 'wc-enhanced-select',
		'description' => sprintf( esc_html__( 'Log events at: <code>%s</code>', 'woo-stripe-subscription' ), wc_get_log_file_path( 'woo-stripe-subscription' ) ),
		'default' => 'disabled',
		'desc_tip' => true,
		'options' => array(
			'disabled' => esc_html__( 'Disabled', 'woo-stripe-subscription' ),
			'enabled' => esc_html__( 'Enabled', 'woo-stripe-subscription' ),
		),
	),
	
);
