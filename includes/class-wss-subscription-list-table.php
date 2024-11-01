<?php
/**
 * Woo Stripe Subscription Subscription List Table
 *
 * Handles the Stripe subscription process.
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
 * Wss Subscription List Table Class
 *
 * @since  1.0.0
 * @class  Wss_Subscription_List_Table
 */
class Wss_Subscription_List_Table extends WP_List_Table {

	/**
	 * Field names and column headers.
	 *
	 * @since  1.0.0
	 */
	private $fields;

	/**
	 * A list of subscriptions.
	 *
	 * @since  1.0.0
	 */
	private $subscriptions;

	/**
	 * Initialize the (parent) class and set its properties.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		global $status, $page;
		$this->fields = array(
			'subscription' => esc_html__( 'Subscription', 'woo-stripe-subscription' ),
			'customer'     => esc_html__( 'Customer', 'woo-stripe-subscription' ),
			'plan'         => esc_html__( 'Plan', 'woo-stripe-subscription' ),
			'created'      => esc_html__( 'Created', 'woo-stripe-subscription' ),
		);
		$this->subscriptions = Wss_Api::request( 'subscriptions', Wss::get_api_key('secret'), null, 'GET' );
		parent::__construct( array(
			'singular' => esc_html__( 'Subscription', 'woo-stripe-subscription' ),
			'plural'   => esc_html__( 'Subscriptions', 'woo-stripe-subscription' ),
			'ajax'     => false
		));
	}

	/**
	 * Get columns.
	 *
	 * @since  1.0.0
	 */
	public function get_columns() {

		return $this->fields;
	}

	/**
	 * Declare sortable columns.
	 *
	 * @since   1.0.0
	 * @return  array
	 */
	public function get_sortable_columns() {

		$columns = array();
		foreach( $this->fields as $key => $value ) {
			$columns[ $key ] = array( $value, true );
		}
		return $columns;
	}

	/**
	 * Prepare items.
	 *
	 * @since  1.0.0
	 */
	public function prepare_items() {
                $search = wp_filter_post_kses( $_REQUEST['s'] );
		$search = ( isset( $search ) ) ? trim( $search ) : false;
		$per_page = 7;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable) ;
		$this->process_bulk_action();
		$post_script = '';
		$data = array();
		$i = 0;
		if( ! isset( $this->subscriptions->data ) ) return null;
		foreach( $this->subscriptions->data as $subscription ) {
			$user = get_users( array(
				'meta_key' => WSS_PLUGIN_MODE.'_stripe_id',
				'meta_value' => $subscription->customer,
				'fields' => array(
					'ID',
					'display_name',
					'user_email'
				),
				'number' => 1,
			) );
			if( isset( $search ) ) {
				$match = null;
				$string = json_encode( array_merge( (array) $subscription, (array) $user ) );
				$match = preg_match( "/{$search}/i", $string );
				if( ! $match ) {
					continue;
				}
			}
			$user_ps = '';
			if( ! is_null( $user ) && count( $user ) > 0 ) {
				$user_link = get_edit_user_link( $user[0]->ID );
				$user_ps .= "<p><a href='{$user_link}'>{$user[0]->display_name}</a>&nbsp;|&nbsp;<a href='mailto:{$user[0]->user_email}'>{$user[0]->user_email}</a></p>";
			}
			$data[] = array(
				'ID' => $i + 1,
				'subscription' => sprintf(
					'<a href="https://dashboard.stripe.com/subscriptions/%s" target="_blank">%s</a><p>%s</p>',
					$subscription->id,
					$subscription->id,
					$subscription->status
				),
				'customer' => sprintf(
					'<a href="https://dashboard.stripe.com/customers/%s" target="_blank">%s</a>%s',
					$subscription->customer,
					$subscription->customer,
					$user_ps
				),
				'plan' => sprintf(
					'<a href="https://dashboard.stripe.com/plans/%s" target="_blank">%s</a>%s<p>%s</p>',
					$subscription->plan->id,
					$subscription->plan->name,
					$subscription->quantity > 1 ? '<strong> x ' . $subscription->quantity . '</strong>' : '' ,
					$this->get_display_amount( $subscription )
				),
				'created' => date( DATE_RFC2822, $subscription->created ),
			);
			$i++;
		}
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		$this->items = $data;
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}

	/**
	 * Column default.
	 *
	 * @since  1.0.0
	 */
	public function column_default( $item, $column_name ) {

		if( isset( $item[ $column_name ] ) ) {
			return $item[ $column_name ];
		}
	}

	/**
	 * Return currency display.
	 *
	 * @since  1.0.5
	 * @param  object
	 */
	public function get_display_amount( $subscription ) {

		$amount = 0;
		if( Wss::is_zero_decimal( $subscription->plan->currency ) ) {
			$amount = $subscription->plan->amount * $subscription->quantity;
		} else {
			$amount = ( $subscription->plan->amount * $subscription->quantity ) / 100;
		}
		return sprintf(
			'%s%.02f per %s %s(s)',
			get_woocommerce_currency_symbol( strtoupper( $subscription->plan->currency ) ),
			( $amount * ( 100 + $subscription->tax_percent ) ) / 100,
			$subscription->plan->interval_count,
			$subscription->plan->interval
		);
	}
}
