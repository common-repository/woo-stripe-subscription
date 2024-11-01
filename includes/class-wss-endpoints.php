<?php
/**
 * Woo Stripe Subscription Endpoints
 *
 * Register endpoints.
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
 * Wss Endpoints Class
 *
 * @since  1.0.0
 * @class  Wss_Endpoints
 */
class Wss_Endpoints {
	
	/**
	 * My-account subscriptions mode: disabled, read_only, or enabled.
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	private $mode;
	
	/**
	 * The URL suffix to use for My Account actions.
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	private $endpoint;
	
	/**
	 * The endpoint title.
	 *
	 * @since  1.0.0
	 * @var    string
	 */
	private $title;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 * @param  string
	 */
	public function __construct() {
		
		if( Wss::subscriptions_enabled() ) {
			$settings = get_option( "woocommerce_". WSS_PLUGIN_ID . "_settings" );
			if( isset( $settings['myaccount_subscriptions'] ) ) {
				$this->mode = $settings['myaccount_subscriptions'];
			}
			if( empty( $this->mode ) ) {
				$this->mode = 'disabled';
			}
			if( isset( $settings['myaccount_subscriptions_endpoint'] ) ) {
				$this->endpoint = $settings['myaccount_subscriptions_endpoint'];
				$this->endpoint = str_replace( ' ', '-', $this->endpoint );
				$this->endpoint = preg_replace( '/[^A-Za-z0-9\-]/', '', $this->endpoint );
			}
			if( empty( $this->endpoint ) ) {
				$this->endpoint = 'wss-custom-endpoint';
			}
			if( isset( $settings['myaccount_subscriptions_title'] ) ) {
				$this->title = $settings['myaccount_subscriptions_title'];
			}
			if( empty( $this->title ) ) {
				$this->title = esc_html__( 'Subscriptions1', 'woo-stripe-subscription' );
			}
			if( $this->mode !== 'disabled' ) {
				add_rewrite_endpoint( $this->endpoint, EP_ROOT | EP_PAGES );
			}
		}
		flush_rewrite_rules();
	}
	
	/**
	 * Return the endpoint.
	 *
	 * @since  1.0.0
	 */
	public function get_endpoint() {
		
		return $this->endpoint;
	}
	
	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @since  1.0.0
	 */
	public function wss_endpoints_init() {}
	
	/**
	 * Add new query var.
	 *
	 * @since   1.0.0
	 * @param   array
	 * @return  array
	 */
	public function wss_endpoints_query_vars( $vars ) {
		
		if( Wss::subscriptions_enabled() && isset( $this->mode ) && $this->mode !== 'disabled' ) {
			$vars[] = $this->endpoint;
		}
		return $vars;
	}
	
	/**
	 * Insert endpoints into the My Account menu.
	 *
	 * @since   1.0.0
	 * @param   array
	 * @return  array
	 */
	public function wss_endpoints_woocommerce_account_menu_items( $items ) {
		
		if( Wss::subscriptions_enabled() && isset( $this->mode ) && $this->mode !== 'disabled' ) {
			$new_items = array();
			$new_items[ $this->endpoint ] = $this->title;
			return $this->wss_endpoints_insert_after_helper( $items, $new_items, 'orders' );
		}
		return $items;
	}
	
	/**
	 * Insert endpoints into the My Account menu.
	 *
	 * @since   1.0.0
	 * @param   array
	 * @return  array
	 */
	public function wss_endpoints_insert_after_helper( $items, $new_items, $after ) {
		
		$position = array_search( $after, array_keys( $items ) ) + 1;
		$array = array_slice( $items, 0, $position, true );
		$array += $new_items;
		$array += array_slice( $items, $position, count( $items ) - $position, true );
		return $array;
	}
	
	/**
	 * Returns a non-unique array of user roles from the metadata of
	 * a list of subscriptions.
	 *
	 * @since   1.0.4
	 * @param   object
	 * @return  array
	 */
	private function get_subscribed_roles( $subscriptions = null ) {
		
		$purchased_roles = array();
		if( isset( $subscriptions->data ) ) {
			foreach( $subscriptions->data as $susbcription ) {
				if( isset( $susbcription->metadata->roles ) ) {
					$roles = explode( ',', $susbcription->metadata->roles );
					foreach( $roles as $role ) {
						$purchased_roles[] = $role;
					}
				}
			}
		}
		return $purchased_roles;
	}
	
	/**
	 * Returns a non-unique array of subscribed plans from a list of subscriptions.
	 *
	 * @since   1.0.4
	 * @param   object
	 * @return  array
	 */
	private function get_subscribed_plans( $subscriptions = null ) {
		
		$subscribed_plans = array();
		if( isset( $subscriptions->data ) ) {
			foreach( $subscriptions->data as $susbcription ) {
				if( isset( $susbcription->plan->id ) ) {
					$subscribed_plans[] = $susbcription->plan->id;
				}
			}
		}
		return $subscribed_plans;
	}
	
	/**
	 * Endpoint content.
	 *
	 * - IF subscriptions are enabled.
	 * - IF there is $_POST data.
	 * - verify the nonce.
	 * - loop through $_POST data for a subscription id.
	 * - submit a DELETE request to Stripe.
	 * - remove associated user roles (if any).
	 * - GET a list of the user's subscriptions.
	 * - output html (or not).
	 *
	 * @since  1.0.0
	 */
	public function wss_endpoints_woocommerce_account_wss_custom_endpoint_endpoint() {
		
		if( Wss::subscriptions_enabled() && isset( $this->mode ) && $this->mode !== 'disabled' ) {
			
			$uid = get_current_user_id();
			$user = new WP_User( $uid );
			$stripe = get_user_meta( $uid, WSS_PLUGIN_MODE.'_stripe_id', true );
			if( isset( $stripe ) ) {
				$params = array(
					"customer" => $stripe,
				);
				$subscriptions = Wss_Api::request( 'subscriptions', Wss::get_api_key('secret'), $params, 'GET' );
				if( ! empty( $_POST[ 'action' ] ) ) {
					if( 'wss_nonce' !== $_POST[ 'action' ] ) return;
					if( empty( $_POST['_wpnonce'] ) ) return;
					if( ! wp_verify_nonce( wp_filter_post_kses( $_POST['_wpnonce'] ), 'wss_nonce' ) ) return;
					foreach( $_POST as $key => $value ) {
						if( preg_match( '/wss_subscription_id_/', wp_filter_post_kses( $key ) ) ) {
							$subscription_id = preg_replace( '/wss_subscription_id_/', '', wp_filter_post_kses( $key ));
							$subscription = Wss_Api::request( "subscriptions/{$subscription_id}", Wss::get_api_key('secret'), null, 'DELETE' );
							if( isset( $subscription ) ) {
								$subscriptions = Wss_Api::request( 'subscriptions', Wss::get_api_key('secret'), $params, 'GET' );
								if( isset( $subscription->metadata->roles ) ) {
									$cancel_roles = explode( ',', $subscription->metadata->roles );
									foreach( $cancel_roles as $cancel_role ) {
										if( $cancel_role !== 'administrator' ) {
											if( in_array( $cancel_role, $user->roles ) ) {
												if( ! in_array( $cancel_role, $this->get_subscribed_roles( $subscriptions ) ) ) {
													$user->remove_role( $cancel_role );
												}
											}
										}
									}
								}
								if( ! in_array( $subscription->plan->id, $this->get_subscribed_plans( $subscriptions ) ) ) {
									$meta_subscriptions = array_unique(
										maybe_unserialize(
											get_user_meta( $uid, WSS_PLUGIN_MODE.'_subscriptions', true )
										)
									);
									if( ( $key = array_search( $subscription->plan->id, $meta_subscriptions ) ) !== false ) {
										unset( $meta_subscriptions[ wp_filter_post_kses( $key ) ] );
										update_user_meta( $uid, WSS_PLUGIN_MODE.'_subscriptions', $meta_subscriptions );
									}
								}
							}
						}
					}
				}
				include WSS_PLUGIN_DIR_PATH . 'templates/myaccount/my-subscriptions.php';
			}
		}
	}
	
	/**
	 * Change endpoint title.
	 *
	 * @since   1.0.0
	 * @param   string
	 * @return  string
	 */
	public function wss_endpoints_wss_custom_endpoint_title( $title ) {
		
		if( Wss::subscriptions_enabled() && isset( $this->mode ) && $this->mode !== 'disabled' ) {
			global $wp_query;
			$is_endpoint = isset( $wp_query->query_vars[ $this->endpoint ] );
			if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				$title = $this->title;
				remove_filter( 'the_title', 'wss_custom_endpoint_title' );
			}
		}
		return $title;
	}
	
	/**
	 * List saved payment methods.
	 *
	 * @since   1.0.0
	 * @param   array
	 * @param   int
	 * @return  array
	 */
	public function wss_endpoints_woocommerce_saved_payment_methods_list( $list, $customer_id ) {
		$list = array();
		$payment_tokens = WC_Payment_Tokens::get_customer_tokens( $customer_id );
		foreach ( $payment_tokens as $payment_token ) {
			if( $payment_token->get_gateway_id() === WSS_PLUGIN_ID && $payment_token->get_meta( 'mode') !== WSS_PLUGIN_MODE ) continue;
			$delete_url      = wc_get_endpoint_url( 'delete-payment-method', $payment_token->get_id() );
			$delete_url      = wp_nonce_url( $delete_url, 'delete-payment-method-' . $payment_token->get_id() );
			$set_default_url = wc_get_endpoint_url( 'set-default-payment-method', $payment_token->get_id() );
			$set_default_url = wp_nonce_url( $set_default_url, 'set-default-payment-method-' . $payment_token->get_id() );
			$type            = strtolower( $payment_token->get_type() );
			$list[ $type ][] = array(
				'method' => array(
					'gateway' => $payment_token->get_gateway_id(),
				),
				'expires'    => esc_html__( 'N/A', 'woocommerce' ),
				'is_default' => $payment_token->is_default(),
				'actions'    => array(
					'delete' => array(
						'url'  => $delete_url,
						'name' => esc_html__( 'Delete', 'woocommerce' ),
					),
				),
			);
			$key = key( array_slice( $list[ $type ], -1, 1, true ) );
			if ( ! $payment_token->is_default() ) {
				$list[ $type ][$key]['actions']['default'] = array(
					'url' => $set_default_url,
					'name' => esc_html__( 'Make Default', 'woocommerce' ),
				);
			}
			$list[ $type ][ $key ] = apply_filters( 'woocommerce_payment_methods_list_item', $list[ $type ][ $key ], $payment_token );
		}
		return $list;
	}
	
	/**
	 * Update customer's default source.
	 *
	 * @since  1.0.0
	 */
	public function wss_endpoints_woocommerce_payment_token_set_default( $token_id ) {
		
		$token = $token = WC_Payment_Tokens::get( $token_id );
		if( $token->get_gateway_id() === WSS_PLUGIN_ID && $token->is_default() ) {
			$customer = $token->get_meta( 'customer' );
			$params = array(
				'default_source' => $token->get_token(),
			);
			Wss_Api::request( "customers/{$customer}", Wss::get_api_key('secret'), $params, 'POST' );
		}
	}
}
