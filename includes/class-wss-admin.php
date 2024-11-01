<?php
/**
 * Woo Stripe Subscription Admin
 *
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, enqueues the
 * admin-specific styles and scripts, adds plugin action links,
 * plugin meta row links, admin notices, settings pages, tabs.
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
 * Wss Admin Class
 *
 * @since  1.0.0
 * @class  Wss_Admin
 */
class Wss_Admin {
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}
	
	/**
	 * Plugin action links.
	 *
	 * @since   1.0.0
	 * @param   array
	 * @return  array
	 */
	public function wss_plugin_action_links( $links ) {
		
		$wss_action_links_before = array(
			'settings' => array(
				'url' => esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section='.WSS_PLUGIN_ID ) ),
				'title' => esc_attr( esc_html__( 'View Wss Settings', 'woo-stripe-subscription' ) ),
				'text' => esc_html__( 'Settings', 'woo-stripe-subscription' ),
			),
		);
		$wss_action_links_after = array(
			'edit' => array(
				'url' => esc_url( admin_url( 'plugin-editor.php?file='.WSS_PLUGIN_BASENAME.'&plugin='.WSS_PLUGIN_BASENAME.'' ) ),
				'title' => esc_attr( esc_html__( 'Edit Wss', 'woo-stripe-subscription' ) ),
				'text' => esc_html__( 'Edit', 'woo-stripe-subscription' ),
			),
		);
		$before_links = array();
		foreach( $wss_action_links_before as $key => $value ) {
			$before_links[ $key ] = "<a href='".$value['url']."' title='".$value['title']."'>".$value['text']."</a>";
		}
		$after_links = array();
		foreach( $wss_action_links_after as $key => $value ) {
			$after_links[ $key ] = "<a href='".$value['url']."' title='".$value['title']."'>".$value['text']."</a>";
		}
		return array_merge( $before_links, $links, $after_links );
	}
	
	/**
	 * Plugin meta links.
	 *
	 * @since   1.0.0
	 * @param   array
	 * @param   mixed
	 * @return  array
	 */
	public function wss_plugin_row_meta( $links, $file ) {
		
		if( $file === WSS_PLUGIN_BASENAME ) {
			$wss_meta_links = array(
				'docs' => array(
					'url' => esc_url( '/docs' ),
					'title' => esc_attr( esc_html__( 'View Wss Documentation', 'woo-stripe-subscription' ) ),
					'text' => esc_html__( 'Docs', 'woo-stripe-subscription' ),
					'enabled' => false,
				),
				'api' => array(
					'url' => esc_url( 'https://dashboard.stripe.com/account/apikeys' ),
					'title' => esc_attr( esc_html__( 'Get Stripe API Keys', 'woo-stripe-subscription' ) ),
					'text' => esc_html__( 'Get Stripe API Keys', 'woo-stripe-subscription' ),
					'enabled' => true,
				),
				'support' => array(
					'url' => esc_url( '/support' ),
					'title' => esc_attr( esc_html__( 'Get support for Wss', 'woo-stripe-subscription' ) ),
					'text' => esc_html__( 'Support', 'woo-stripe-subscription' ),
					'enabled' => false,
				),
			);
			$row_meta = array();
			foreach( $wss_meta_links as $key => $value ) {
				if( $value['enabled'] ) {
					$row_meta[ $key ] = "<a href='".$value['url']."' title='".$value['title']."' target='_blank'>".$value['text']."</a>";
				}
			}
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}
	
	/**
	 * Adds the gateway class to array of payment methods.
	 *
	 * @since   1.0.0
	 * @param   array
	 * @return  array
	 */
	public function wss_woocommerce_payment_gateways( $methods ) {
		
		$methods[] = 'Wss_Payment_Gateway';
		return $methods;
	}
	
	/**
	 * Add submenu page to 'WooCommerce'.
	 *
	 * @since  1.0.0
	 */
	public function wss_admin_admin_menu() {
		
		$page_hook = add_submenu_page(
			'woocommerce',
			esc_html__( 'Manage Stripe Subscriptions', 'woo-stripe-subscription' ), 
			esc_html__( 'Stripe Subscriptions', 'woo-stripe-subscription' ),
			'manage_woocommerce',
			'woo-stripe-subscription',
			__CLASS__ . '::wss_admin_manage_stripe_subscriptions'
		);
	}
	
	/**
	 * Add css / js to admin pages.
	 *
	 * @since  1.0.0
	 */
	public function wss_admin_enqueue_scripts() {
		
		$min = WSS_PLUGIN_DEBUG ? '' : '.min';
		wp_enqueue_style(
			'wss-admin-style',
			WSS_PLUGIN_DIR_URL . 'assets/css/admin.css'
		);
		wp_enqueue_script(
			'wss-admin',
			WSS_PLUGIN_DIR_URL . 'assets/js/wss-admin' . $min . '.js',
			array( 'jquery'  ),
			WSS_PLUGIN_VERSION,
			true
		);
	}
	
	/**
	 * Filter product price html.
	 *
	 * @since   1.0.0
	 * @param   string
	 * @param   object
	 * @return  string
	 */
	public function wss_woocommerce_get_price_html( $price, $product ) {
		
		if( !empty( $product ) && $product->is_type( 'wss_subscription' ) ) {
			$meta = version_compare( WC_VERSION, '3.0.0', '<' ) ? get_post_meta( $product->post->ID ) : get_post_meta( $product->get_id() );
			if( ! isset( $meta['_wss_stripe_plan_interval'][0] ) ) {
				return $price;
			} elseif( ! isset( $meta['_wss_stripe_plan_interval_count'][0] ) ) {
				return sprintf(
					esc_html( __( '%s per %s', 'woo-stripe-subscription' ) ),
					$price,
					$meta['_wss_stripe_plan_interval'][0]
				);
			} else {
				if( $meta['_wss_stripe_plan_interval_count'][0] > 1 ) {
					return sprintf(
						esc_html( __( '%s every %.0f %ss', 'woo-stripe-subscription' ) ),
						$price,
						$meta['_wss_stripe_plan_interval_count'][0],
						$meta['_wss_stripe_plan_interval'][0]
					);
				} else {
					return sprintf(
						esc_html( __( '%s per %s', 'woo-stripe-subscription' ) ),
						$price,
						$meta['_wss_stripe_plan_interval'][0]
					);
				}
			}
		}
		return $price;
	}
	
	/**
	 * Filter cart item price html.
	 *
	 * @since   1.0.0
	 * @param   string
	 * @param   object
	 * @param   string
	 * @return  string
	 */
	public function wss_woocommerce_cart_item_price( $price, $item, $item_key ) {
		
		$item = $item['data'];
		if( ! empty( $item ) && $item->is_type( 'wss_subscription' ) ) {
			$meta = version_compare( WC_VERSION, '3.0.0', '<' ) ? get_post_meta( $item->post->ID ) : get_post_meta( $item->get_id() );
			if( ! isset( $meta['_wss_stripe_plan_interval'][0] ) ) {
				return $price;
			} elseif( ! isset( $meta['_wss_stripe_plan_interval_count'][0] ) ) {
				return sprintf(
					esc_html( __( '%s per %s', 'woo-stripe-subscription' ) ),
					$price,
					$meta['_wss_stripe_plan_interval'][0]
				);
			} else {
				if( $meta['_wss_stripe_plan_interval_count'][0] > 1 ) {
					return sprintf(
						esc_html( __( '%s every %.0f %ss', 'woo-stripe-subscription' ) ),
						$price,
						$meta['_wss_stripe_plan_interval_count'][0],
						$meta['_wss_stripe_plan_interval'][0]
					);
				} else {
					return sprintf(
						esc_html( __( '%s per %s', 'woo-stripe-subscription' ) ),
						$price,
						$meta['_wss_stripe_plan_interval'][0]
					);
				}
			}
		}
		return $price;
	}
	
	/**
	 * Filter checkout cart item line subtotal html.
	 *
	 * @since   1.0.1
	 * @param   string
	 * @param   object
	 * @param   string
	 * @return  string
	 */
	public function wss_woocommerce_cart_item_subtotal( $price, $item, $item_key ) {
		
		$item = $item['data'];
		if( ! empty( $item ) && $item->is_type( 'wss_subscription' ) ) {
			$meta = version_compare( WC_VERSION, '3.0.0', '<' ) ? get_post_meta( $item->post->ID ) : get_post_meta( $item->get_id() );
			if( ! isset( $meta['_wss_stripe_plan_interval'][0] ) ) {
				return $price;
			} elseif( ! isset( $meta['_wss_stripe_plan_interval_count'][0] ) ) {
				return sprintf(
					esc_html( __( '%s per %s', 'woo-stripe-subscription' ) ),
					$price,
					$meta['_wss_stripe_plan_interval'][0]
				);
			} else {
				if( $meta['_wss_stripe_plan_interval_count'][0] > 1 ) {
					return sprintf(
						esc_html( __( '%s every %.0f %ss', 'woo-stripe-subscription' ) ),
						$price,
						$meta['_wss_stripe_plan_interval_count'][0],
						$meta['_wss_stripe_plan_interval'][0]
					);
				} else {
					return sprintf(
						esc_html( __( '%s per %s', 'woo-stripe-subscription' ) ),
						$price,
						$meta['_wss_stripe_plan_interval'][0]
					);
				}
			}
		}
		return $price;
	}
	
	/**
	 * Filter order details line subtotal html.
	 *
	 * @since   1.0.1
	 * @param   string
	 * @param   object
	 * @return  string
	 */
	public function wss_woocommerce_order_formatted_line_subtotal( $subtotal, $item ) {
	
		$_product_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $item['item_meta']['_product_id'][0] : $item->get_id();
		$_product = wc_get_product( $_product_id );
		$item = $_product;
		if( ! empty( $item ) && $item->is_type( 'wss_subscription' ) ) {
			$meta = get_post_meta( $_product_id );
			if( ! isset( $meta['_wss_stripe_plan_interval'][0] ) ) {
				return $subtotal;
			} elseif( ! isset( $meta['_wss_stripe_plan_interval_count'][0] ) ) {
				return sprintf(
					esc_html( __( '%s per %s', 'woo-stripe-subscription' ) ),
					$subtotal,
					$meta['_wss_stripe_plan_interval'][0]
				);
			} else {
				if( $meta['_wss_stripe_plan_interval_count'][0] > 1 ) {
					return sprintf(
						esc_html( __( '%s per %.0f %ss', 'woo-stripe-subscription' ) ),
						$subtotal,
						$meta['_wss_stripe_plan_interval_count'][0],
						$meta['_wss_stripe_plan_interval'][0]
					);
				} else {
					return sprintf(
						esc_html( __( '%s per %s', 'woo-stripe-subscription' ) ),
						$subtotal,
						$meta['_wss_stripe_plan_interval'][0]
					);
				}
			}
		}
		return $subtotal;
	}
	
	/**
	 * Outputs a sortable list table of subscriptions.
	 *
	 * @since  1.0.0
	 */
	public static function wss_admin_manage_stripe_subscriptions() {
		
		if( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		if( ! class_exists( 'Wss_Subscription_List_Table' ) ) {
			require_once( WSS_PLUGIN_DIR_PATH . 'includes/class-wss-subscription-list-table.php' );
		}
                $search = wp_filter_post_kses( $_REQUEST['s'] );
		$search = ( isset( $search ) ) ? $search : null;
		$search = ( isset( $search ) && ! empty( $search ) ) ? "&nbsp;[ {$search} ]" : '';
		?>
		<div class="wrap">
		<h2><?php echo sprintf(
			esc_html__( 'Stripe Subscriptions%s', 'woo-stripe-subscription' ),
			$search
		); ?></h2>
		<?php $sslt = new Wss_Subscription_List_Table(); ?>
		<?php $sslt->prepare_items(); ?>
		<form method="post">
		<?php wp_nonce_field( 'my-action' ); ?>
		<?php $sslt->search_box( 'search', 'wss-list-table' ); ?>
		<?php $sslt->display(); ?>
		</form>
		</div>
		<?php
	}	
}
