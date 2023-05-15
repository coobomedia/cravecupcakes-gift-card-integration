<?php
/**
 * Cart Abandonment DB
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Cart Abandonment DB class.
 */
class PeachPay_Database {
	use PeachPay_Singleton;

	/**
	 * A list of the tables in wpdb.
	 *
	 * @var Array
	 */
	private static $tables = array(
		'peachpay_carts',
		'peachpay_abandonment_emails',
		'peachpay_cart_has_item',
	);

	/**
	 *  Create tables
	 */
	public function initialize_tables() {
		$this->create_unintialized_tables();
	}

	// Preparing queries is a waste of cycles in the case of this class's usage-- there is no user input, just hardcoded values.
	// Caching is likewise of minimal usage here. Queries are only called once per page load at max.
	//phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery

	/**
	 *  Checks if all our tables are created and creates any that are missing.
	 */
	public function create_unintialized_tables() {
		global $wpdb;

		foreach ( self::$tables as $table ) {
			$is_table_exist = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . $table ) );

			if ( empty( $is_table_exist ) ) {
				// Create table.
				if ( 'peachpay_carts' === $table ) {
					$this->create_cart_table();
				} elseif ( 'peachpay_abandonment_emails' === $table ) {
					$this->create_email_table();
				} elseif ( 'peachpay_cart_has_item' === $table ) {
					$this->create_cart_has_item_table();
				}
			}
		}
	}

	/**
	 *  Create tables for analytics.
	 */
	private function create_cart_table() {
		global $wpdb;

		$cart_table      = $wpdb->prefix . 'peachpay_carts';
		$charset_collate = $wpdb->get_charset_collate();

		// Cart abandonment tracking db sql command.
		$sql = "CREATE TABLE IF NOT EXISTS {$cart_table} (
			session_id VARCHAR(60) NOT NULL,
			cart_hash VARCHAR(60),
			email VARCHAR(100),
			cart_total DECIMAL(10,2),
			order_status ENUM( 'normal','abandoned','completed','lost' ) NOT NULL DEFAULT 'normal',
   			expiry_time DATETIME DEFAULT NULL,
			PRIMARY KEY  (`session_id`)
		) $charset_collate;\n";

		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 *  Create tables for analytics.
	 */
	private function create_email_table() {
		global $wpdb;

		$abandonment_emails_table = $wpdb->prefix . 'peachpay_abandonment_emails';
		$cart_table               = $wpdb->prefix . 'peachpay_carts';
		$charset_collate          = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$abandonment_emails_table} (
			email_id BIGINT(20) NOT NULL AUTO_INCREMENT,
			cart_id VARCHAR(60) NOT NULL,
			email_contents LONGTEXT,
			coupon_code VARCHAR(50),
			email_number INT NOT NULL,
			PRIMARY KEY  (`email_id`),
			FOREIGN KEY  (`cart_id`) REFERENCES {$cart_table}(`session_id`)
		) $charset_collate;\n";

		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

	}

	/**
	 *  Create tables for analytics.
	 */
	private function create_cart_has_item_table() {
		global $wpdb;

		$abandonment_emails_table = $wpdb->prefix . 'peachpay_cart_has_item';
		$cart_table               = $wpdb->prefix . 'peachpay_carts';
		$charset_collate          = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$abandonment_emails_table} (
			cart_id VARCHAR(60) NOT NULL,
			item_id VARCHAR(60) NOT NULL,
			qty INTEGER NOT NULL DEFAULT 0,
			PRIMARY KEY  (`cart_id`, `item_id`),
			FOREIGN KEY  (`cart_id`) REFERENCES {$cart_table}(`session_id`) ON DELETE CASCADE
		) $charset_collate;\n";

		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

	}

	/**
	 * Retrieves all data from the peachpay_carts table.
	 */
	public function get_all_cart_data() {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$cart_data  = $wpdb->get_results( "SELECT * FROM {$cart_table};" );

		return $cart_data;
	}

	/**
	 * Retrieves all data for carts marked 'abandoned' in the peachpay_carts table.
	 */
	public function get_abandoned_cart_data() {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$cart_data  = $wpdb->get_results( "SELECT * FROM {$cart_table} WHERE order_status = 'abandoned';" );

		return $cart_data;
	}

	/**
	 * Changes the status of all carts in peachpay_carts to abandoned who have expired and not yet been tagged as abandoned.
	 */
	public function abandon_expired_carts() {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$cart_data  = $wpdb->get_results( "SELECT session_id FROM {$cart_table} WHERE order_status = 'normal' AND expiry_time < NOW();" );

		foreach ( $cart_data as $cart ) {
			$wpdb->update(
				$cart_table,
				array(
					'order_status' => 'abandoned',
				),
				array( // where-
					'session_id' => $cart->session_id,
				)
			);
		}

		return $cart_data;
	}

	/**
	 * Culls completed orders from our database. WooCommerce tracks all orders that went through the checkout, so there's no need for us
	 * to do repeat work.
	 */
	public function cull_completed_orders() {
		$last_checked = get_option( 'peachpay_db_last_checked' );

		$query  = new WC_Order_Query(
			array(
				'limit'        => -1, // Get all.
				'date_created' => '>=' . $last_checked,
				'orderby'      => 'date',
				'order'        => 'DESC',
				'return'       => 'objects',
			)
		);
		$orders = $query->get_orders();
		update_option( 'peachpay_db_last_checked', gmdate( 'm/d/Y h:i:s a', time() ) );

		global $wpdb;
		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$cart_data  = $wpdb->get_results( "SELECT session_id, cart_hash FROM {$cart_table};" );

		foreach ( $orders as $order ) {
			foreach ( $cart_data as $cart ) {
				if ( $order instanceof WC_Order && $cart->cart_hash === $order->get_cart_hash() ) {
					self::remove_cart( $cart->session_id );
				}
			}
		}
	}

	/**
	 * Recoverable Revenue summed by month for YTD.
	 */
	public function get_recoverable_revenue_ytd() {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$cart_data  = $wpdb->get_results( "SELECT monthname(expiry_time), sum(cart_total) FROM {$cart_table} WHERE order_status = 'abandoned' AND expiry_time >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND email != '' GROUP BY month(expiry_time);" );

		$formatted_graph_data = array();
		foreach ( $cart_data as $cart ) {
			$formatted_graph_data[ $cart->{'monthname(expiry_time)'} ] = $cart->{'sum(cart_total)'};
		}
		return $formatted_graph_data;
	}

	/**
	 * Recoverable Revenue total YTD.
	 */
	public function get_recoverable_total_ytd() {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$cart_data  = $wpdb->get_results( "SELECT sum(cart_total) FROM {$cart_table} WHERE order_status = 'abandoned' AND expiry_time >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND email != '';" );

		return $cart_data[0]->{'sum(cart_total)'} ? $cart_data[0]->{'sum(cart_total)'} : 0.00;
	}

	/**
	 * Abandoned Revenue summed by month for YTD.
	 */
	public function get_abandoned_revenue_ytd() {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$cart_data  = $wpdb->get_results( "SELECT monthname(expiry_time), sum(cart_total) FROM {$cart_table} WHERE order_status = 'abandoned' AND expiry_time >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND email = '' GROUP BY month(expiry_time);" );

		$formatted_graph_data = array();
		foreach ( $cart_data as $cart ) {
			$formatted_graph_data[ $cart->{'monthname(expiry_time)'} ] = $cart->{'sum(cart_total)'};
		}
		return $formatted_graph_data;
	}

	/**
	 * Abandoned Revenue total YTD.
	 */
	public function get_abandoned_total_ytd() {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$cart_data  = $wpdb->get_results( "SELECT sum(cart_total) FROM {$cart_table} WHERE order_status = 'abandoned' AND expiry_time >= DATE_SUB(NOW(),INTERVAL 1 YEAR);" );

		return $cart_data[0]->{'sum(cart_total)'} ? $cart_data[0]->{'sum(cart_total)'} : 0.00;
	}

	/**
	 * Returns the total abandoned cart count grouped by month, ytd. Also grouped by recoverable (has email) or not.
	 *
	 * @param Boolean $recoverable Determines whether to receive recoverable carts ytd or unrecoverable.
	 */
	public function get_abandoned_carts_ytd( $recoverable ) {
		global $wpdb;

		$cart_table               = $wpdb->prefix . 'peachpay_carts';
		$recoverable_carts_data   = $wpdb->get_results( "SELECT monthname(expiry_time), count(*) FROM {$cart_table} WHERE order_status = 'abandoned' AND expiry_time >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND email != '' GROUP BY month(expiry_time);" );
		$unrecoverable_carts_data = $wpdb->get_results( "SELECT monthname(expiry_time), count(*) FROM {$cart_table} WHERE order_status = 'abandoned' AND expiry_time >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND email = '' GROUP BY month(expiry_time);" );

		$formatted_data = array();
		if ( $recoverable ) {
			foreach ( $recoverable_carts_data as $cart ) {
				$formatted_data[ $cart->{'monthname(expiry_time)'} ] = $cart->{'count(*)'};
			}
		} else {
			foreach ( $unrecoverable_carts_data as $cart ) {
				$formatted_data[ $cart->{'monthname(expiry_time)'} ] = $cart->{'count(*)'};
			}
		}
		return $formatted_data;
	}

	/**
	 * Returns the count of recovereable abandoned carts.
	 */
	public function get_recoverable_cart_count() {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$data       = $wpdb->get_results( "SELECT count(*) FROM {$cart_table} WHERE order_status = 'abandoned' AND expiry_time >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND email != '';" );

		return $data[0]->{'count(*)'} ? $data[0]->{'count(*)'} : 0;
	}

	/**
	 * Returns the count of all abandoned carts.
	 */
	public function get_abandoned_cart_count() {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$data       = $wpdb->get_results( "SELECT count(*) FROM {$cart_table} WHERE order_status = 'abandoned' AND expiry_time >= DATE_SUB(NOW(),INTERVAL 1 YEAR);" );

		return $data[0]->{'count(*)'} ? $data[0]->{'count(*)'} : 0;
	}

	/**
	 * Returns the total number of completed orders via WC.
	 */
	public function get_completed_count() {
		$query       = new WC_Order_Query(
			array(
				'limit'        => -1, // Get all.
				'orderby'      => 'date',
				'order'        => 'DESC',
				'return'       => 'objects',
				'date_created' => '>= DATE_SUB(NOW(),INTERVAL 1 YEAR)',
				'status'       => 'wc-completed',
			)
		);
		$orders      = $query->get_orders();
		$orders_json = array();
		foreach ( $orders as $order ) {
			$orders_json[] = $order->get_data();
		}

		return count( $orders_json );
	}

	/**
	 * Returns the % abandoned to completed orders.
	 */
	public function get_percent_abandoned() {
		$num_orders    = self::get_completed_count();
		$num_abandoned = self::get_abandoned_cart_count();

		if ( $num_abandoned < 1 ) {
			return 0;
		} elseif ( $num_orders < 1 ) {
			return 100;
		} else {
			return ( $num_abandoned / ( $num_abandoned + $num_orders ) ) * 100;
		}
	}

	/**
	 * Returns true if the cart by session_id exists in the database, false otherwise.
	 *
	 * @param String $session_id The session id for the current cart/user instance.
	 */
	public function cart_exists( $session_id ) {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$cart       = $wpdb->get_results( "SELECT * FROM {$cart_table} WHERE session_id = '{$session_id}';" );

		if ( $cart ) {
			return true;
		}
		return false;
	}

	/**
	 * Updates the contents, total, and status of a cart in the local database. Creates one if it did not
	 * already exist.
	 *
	 * @param Array $data The WC cart data organized by our Cart Tracker class.
	 */
	public function update_cart( $data ) {
		// Throw out this update attempt if we don't have required data.
		if ( ! $data['session_id'] ) {
			return;
		}

		global $wpdb;
		// This section may cause occassional error warnings due to db queries, but we don't want those to be displayed
		// to the customer. Change this to 'false' for debugging.
		$wpdb->suppress_errors( true );

		$cart_table = $wpdb->prefix . 'peachpay_carts';

		$cart_exists = $wpdb->get_results( "SELECT * FROM {$cart_table} WHERE session_id = '{$data['session_id']}';" );

		$cart_total = $data['cart_data']->get_cart_total();
		$cart_total = substr( $cart_total, strpos( $cart_total, '</span>' ) + 7 ); // Trim out WC's formatting from the front...
		$cart_total = strstr( $cart_total, '<', true ); // And after.

		if ( $cart_exists ) {
			// Update existing cart data.
			$wpdb->update(
				$cart_table,
				array( // update row with data-
					'cart_hash'    => $data['cart_hash'],
					'email'        => $data['email'],
					'cart_total'   => $cart_total,
					'order_status' => $data['status'],
				),
				array( // where-
					'session_id' => $data['session_id'],
				)
			);
		} else {
			// Insert a new cart into the table.
			$wpdb->insert(
				$cart_table,
				array(
					'session_id'   => $data['session_id'],
					'cart_hash'    => $data['cart_hash'],
					'email'        => $data['email'],
					'cart_total'   => $cart_total,
					'order_status' => $data['status'],
					'expiry_time'  => gmdate( 'y-m-d h:i:s', strtotime( '+2 days' ) ),
				)
			);
		}

		// Add/update cart contents.
		foreach ( $data['cart_data']->get_cart() as $cart_item ) {
			$item_id  = $cart_item['data']->get_id();
			$quantity = $cart_item['quantity'];

			self::add_item_to_cart_db( $data['session_id'], $item_id, $quantity );
		}

		$wpdb->suppress_errors( false );
	}

	/**
	 * Adds or updates items in the cart-item-qty relational table.
	 *
	 * @param String  $session_id The session id of the cart to update.
	 * @param String  $item_id The id of the item being updated/added.
	 * @param Integer $quantity The quantity of item to set.
	 */
	private function add_item_to_cart_db( $session_id, $item_id, $quantity ) {
		global $wpdb;
		$cart_has_item_table = $wpdb->prefix . 'peachpay_cart_has_item';

		// If relation exists, update qty
		$relation_exists = $wpdb->get_results( "SELECT * FROM {$cart_has_item_table} WHERE cart_id = '{$session_id}' AND item_id = '{$item_id}';" );
		if ( $relation_exists ) {
			$wpdb->update(
				$cart_has_item_table,
				array(
					'qty' => $quantity,
				),
				array( // where
					'cart_id' => $session_id,
					'item_id' => $item_id,
				)
			);
			return;
		}

		// Otherwise, insert a new row
		$wpdb->insert(
			$cart_has_item_table,
			array(
				'cart_id' => $session_id,
				'item_id' => $item_id,
				'qty'     => $quantity,
			)
		);
	}

	/**
	 * Updates the email of a cart.
	 *
	 * @param String $session_id The session id of the cart to update.
	 * @param String $email The email to set.
	 */
	public function update_email( $session_id, $email ) {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';

		return $wpdb->update(
			$cart_table,
			array( // update row with data-
				'email' => $email,
			),
			array( // where-
				'session_id' => $session_id,
			)
		);
	}

	/**
	 * Removes a cart from the database.
	 *
	 * @param String $session_id The session id of the cart to update.
	 */
	public function remove_cart( $session_id ) {
		global $wpdb;

		$cart_table = $wpdb->prefix . 'peachpay_carts';
		$wpdb->delete(
			$cart_table,
			array(
				'session_id' => $session_id,
			)
		);
	}

	/**
	 * Drops all PeachPay tables from the local database.
	 */
	public function drop_tables() {
		global $wpdb;

		// Tables need to be dropped in reverse order to satisfy foreign key constraints.
		for ( $i = count( self::$tables ) - 1; $i >= 0; $i-- ) {
			$table      = self::$tables[ $i ];
			$table_name = $wpdb->prefix . $table;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
			$response = $wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			if ( ! $response ) {
				return 'There was an error dropping tables. Check your foreign constraints and table add order? Failed on table: ' . $table_name;
			}
		}

		return $response;
	}

	//phpcs:enable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
}
