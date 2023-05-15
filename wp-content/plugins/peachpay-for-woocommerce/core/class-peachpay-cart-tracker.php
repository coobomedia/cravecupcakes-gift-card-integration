<?php
/**
 * The PeachPay Cart Tracker object to manage cart tracking at page load.
 *
 * @package PeachPay
 */

/**
 * Support for abandoned cart tracking and related functionality.
 */
final class PeachPay_Cart_Tracker {

	/**
	 * Local WordPress Database Manager for PeachPay.
	 *
	 * @var PeachPay_Database
	 */
	private static $local_db;

	/**
	 * Constructor method. This PHP magic method is called automatically as the class is instantiated.
	 *
	 * @param PeachPay_Database $db The active database object.
	 */
	public function __construct( $db = null ) {
		if ( ! $db ) {
			return null;
		}

		// Subscribe database.
		self::$local_db = $db;

		// Begin detecting cart hooks.
		add_action( 'woocommerce_add_to_cart', array( $this, 'update_cart_local' ), 100 );
		add_action( 'woocommerce_cart_item_removed', array( $this, 'update_cart_local' ), 100 );
		add_action( 'woocommerce_cart_item_set_quantity', array( $this, 'update_cart_local' ), 100 );

		// On page load, check if any carts need to be updated to the "abandoned" status.
		add_action( 'init', array( $this, 'update_abandonment' ) );

		// Stop tracking any carts when their order is placed; WooCommerce already does this.
		add_action( 'woocommerce_order_status_completed', array( $this, 'stop_tracking_cart' ) );
	}

	/**
	 * Updates the localdb cart object whenever this object's hooks are triggered.
	 */
	public function update_cart_local() {
		if ( ! function_exists( 'WC' ) || ! isset( WC()->session ) || ! WC()->session->get_session_cookie() ) {
			// Since the rest of the code for keeping track of carts relies on the session ID,
			// if we don't have a session cookie from which to grab the session ID, exit.
			// See https://woocommerce.github.io/code-reference/classes/WC-Session-Handler.html#method_get_session_cookie
			return;
		}

		$data = array();

		// Get cart data.
		$cart              = WC()->cart;
		$data['cart_data'] = $cart;

		$session_id         = WC()->session->get_session_cookie()[3];
		$data['session_id'] = $session_id;

		$data['cart_hash'] = $cart->get_cart_hash();

		// Get user data.
		$current_user  = wp_get_current_user();
		$email         = $current_user->user_email;
		$data['email'] = $email;

		$data['status'] = 'normal';

		// Update database.
		self::$local_db->update_cart( $data );
	}

	/**
	 * Updates the localdb cart object with an email when an unlogged-in user enters an email
	 * into the PeachPay checkout.
	 *
	 * @param String $email The user's email to assign in the database.
	 */
	public function assign_email( $email ) {
		$session_id = WC()->session->get_session_cookie()[3];

		return self::$local_db->update_email( $session_id, $email );
	}

	/**
	 * Called periodically, removes completed orders from the db and updates expired carts' status to abandoned.
	 */
	public function update_abandonment() {
		self::$local_db->cull_completed_orders();
		self::$local_db->abandon_expired_carts();
	}

	/**
	 * Removes a cart from the db/tracking.
	 *
	 * @param String $order_id The id of the order to remove.
	 */
	public function stop_tracking_cart( $order_id ) {
		$order = wc_get_order( $order_id );

		self::$local_db->remove_cart( $order );
	}
}
