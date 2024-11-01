<?php
/**
 * Woo Stripe Subscription
 *
 * @package    wss
 * @author     Ramkumar
 * @version    1.0.0
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright  (c) 2019 http://ramkumarelumalai.com/wss
 *
 * @wordpress-plugin
 * Plugin Name:        Woo Stripe Subscription
 * Plugin URI:         http://ramkumarelumalai.com/woo-stripe-subscription
 * Version:            1.0.0
 * Author:             Ramkumar
 * Author URI:         http://ramkumarelumalai.com
 * Description:        Accept <strong>Credit Cards</strong>, <strong>Bitcoin</strong>, <strong>Alipay</strong>, and connect your <strong>WooCommerce</strong> store to <strong>Stripe</strong>'s Subscription API.
 * Tags:               wss, stripe, subscription, subscriptions, woocommerce, pci, dss,
 * Text Domain:        woo-stripe-subscription
 * Requires at least:  4.5.3
 * Tested up to:       5.1.1
 * License:            GNU General Public License, version 3 (GPL-3.0)
 * License URI:        https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Copyright (c) 2019 http://ramkumarelumalai.com/wss
 *
 * Woo Stripe Subscription is free software: 
 * you can redistribute it and/or modify it under the terms of the 
 * GNU General Public License as published by the Free Software 
 * Foundation, either version 3 of the License, or any later version.
 *
 * Woo Stripe Subscription is distributed in the 
 * hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A 
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Woo Stripe Subscription. 
 * If not, see <https://www.gnu.org/licenses/gpl-3.0.txt>.
 */

if( ! defined( 'ABSPATH' ) ) exit; // exit if accessed directly.

/**
 * Activate the plugin.
 *
 * @since  1.0.0
 * @hook   register_activation_hook
 */
function activate_wss() {

	require_once plugin_dir_path( __FILE__ ).'includes/class-wss-activator.php';
	Wss_Activator::activate();
}

/**
 * Deactivate the plugin.
 *
 * @since  1.0.0
 * @hook   register_deactivation_hook
 */
function deactivate_wss() {

	require_once plugin_dir_path( __FILE__ ).'includes/class-wss-deactivator.php';
	Wss_Deactivator::deactivate();
}

/**
 * Localize the plugin.
 *
 * @since  1.0.0
 * @hook   plugins_loaded
 */
function localize_wss() {

	require_once plugin_dir_path( __FILE__ ).'includes/class-wss-i18n.php';
	Wss_i18n::load_textdomain();
}

/**
 * Run the plugin.
 *
 * @since  1.0.0
 */
function run_wss() {

	require_once plugin_dir_path( __FILE__ ).'includes/class-wss.php';
	$plugin = new Wss();
	$plugin->run();
}

/**
 * The plugin failed pre-flight checks.
 *
 * @since  1.0.0
 * @hook   admin_init
 */
function fail_wss() {

	deactivate_plugins( plugin_basename( __FILE__ ) );
        $wss_active = wp_filter_post_kses( $_GET['activate'] );
	if( isset( $wss_active ) ) unset( $wss_active );
	add_action( 'admin_notices', 'wss_failed' );
}

/**
 * Let the user know that the plugin failed.
 *
 * @since  1.0.0
 * @hook   admin_notices
 */
function wss_failed() {

	echo sprintf(
		esc_html__( '%sOops:%s Woo Stripe Subscription encountered an error.%s', 'woo-stripe-subscription' ),
		'<div class="notice notice-error is-dismissible"><p><strong>','</strong>','</p></div>'
	);
}

/**
 * Initiate the plugin.
 *
 * Do pre-flight checks, define constants, setup the plugin environment, then run the plugin.
 *
 * @since  1.0.0
 * @hook   init
 */
function init_wss() {

	if( ! class_exists( 'WC_Payment_Gateway' ) ) {
		add_action( 'admin_init', 'fail_wss' );
	} else {
		$_version   = '1.0.5';
		$_id        = 'wss';
		$_title     = 'Stripe (wss)';
		$_desc      = sprintf(
			esc_html__( 'Accept %sCredit Cards%s, %sBitcoin%s, %sAlipay%s, and connect your %sWooCommerce%s store to %sStripe%s\'s Subscription API.', 'woo-stripe-subscription' ),
			'<strong>','</strong>',
			'<strong>','</strong>',
			'<strong>','</strong>',
			'<strong>','</strong>',
			'<strong>','</strong>'
		);
		$_plugin    = __FILE__;
		$_dir       = plugin_dir_path( $_plugin );
		$_url       = plugin_dir_url( $_plugin );
		$_base      = plugin_basename( $_plugin );
		$_settings  = get_option( "woocommerce_{$_id}_settings" );
		$_mode      = $_settings['mode'] === 'test' || ! is_ssl() ? 'wss_test' : 'wss_live';
		$_debug     = $_settings['debug'] === 'enabled' ? true : false;
		$_dev       = SCRIPT_DEBUG;
		$_api       = 'https://api.stripe.com/v1/';
		defined( 'WSS_PLUGIN_VERSION' )    or define( 'WSS_PLUGIN_VERSION',    $_version );
		defined( 'WSS_PLUGIN_ID' )         or define( 'WSS_PLUGIN_ID',         $_id );
		defined( 'WSS_PLUGIN_TITLE' )      or define( 'WSS_PLUGIN_TITLE',      $_title );
		defined( 'WSS_PLUGIN_DESC' )       or define( 'WSS_PLUGIN_DESC',       $_desc );
		defined( 'WSS_PLUGIN_FILE' )       or define( 'WSS_PLUGIN_FILE',       $_plugin );
		defined( 'WSS_PLUGIN_DIR_PATH' )   or define( 'WSS_PLUGIN_DIR_PATH',   $_dir );
		defined( 'WSS_PLUGIN_DIR_URL' )    or define( 'WSS_PLUGIN_DIR_URL',    $_url );
		defined( 'WSS_PLUGIN_BASENAME' )   or define( 'WSS_PLUGIN_BASENAME',   $_base );
		defined( 'WSS_PLUGIN_MODE' )       or define( 'WSS_PLUGIN_MODE',       $_mode );
		defined( 'WSS_PLUGIN_DEBUG' )      or define( 'WSS_PLUGIN_DEBUG',      $_debug );
		defined( 'WSS_PLUGIN_DEV' )        or define( 'WSS_PLUGIN_DEV',        $_dev );
		defined( 'WSS_PLUGIN_API' )        or define( 'WSS_PLUGIN_API',        $_api );
		if( current_user_can( 'activate_plugins' ) ) {
			register_activation_hook( $_plugin, 'activate_wss' );
			register_deactivation_hook( $_plugin, 'deactivate_wss' );
		}
		run_wss();
	}
}

/**
 * Apply localization once everything has been loaded.
 *
 * @since  1.0.0
 */
add_action( 'plugins_loaded', 'localize_wss' );

/**
 * Run on init hook.
 *
 * @since  1.0.0
 */
add_action( 'init', 'init_wss' );
