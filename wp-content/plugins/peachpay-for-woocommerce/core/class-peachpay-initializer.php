<?php
/**
 * Class PeachPay_Initializer
 *
 * @package PeachPay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Classes
require_once PEACHPAY_ABSPATH . 'core/class-peachpay-dependency-service.php';
require_once PEACHPAY_ABSPATH . 'core/class-peachpay-test-mode-service.php';
require_once PEACHPAY_ABSPATH . 'core/class-peachpay-cart-tracker.php';
require_once PEACHPAY_ABSPATH . 'core/class-peachpay-database.php';
require_once PEACHPAY_ABSPATH . 'core/class-peachpay-alert-service.php';
require_once PEACHPAY_ABSPATH . 'core/routes/class-peachpay-routes-manager.php';

// Utilities

/**
 * Main class for the PeachPay plugin. Its responsibility is to initialize the extension.
 *
 * @deprecated Moving to the class PeachPay.
 */
final class PeachPay_Initializer {

	/**
	 * Dependency Checking Service for PeachPay.
	 *
	 * @var PeachPay_Dependency_Service
	 */
	private static $dependency_service;

	/**
	 * Cart Tracker for PeachPay.
	 *
	 * @var PeachPay_Cart_Tracker
	 */
	private static $cart_tracker;

	/**
	 * Constructor entry point, non-static for WP hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'post_init' ), 11 );
	}

	/**
	 * Entry point to the initialization logic.
	 */
	public static function init() {

		// Check dependencies and update the PeachPay admin error notice.
		self::$dependency_service = new PeachPay_Dependency_Service();

		if ( ! self::$dependency_service->all_dependencies_valid() ) {
			// If any dependencies are invalid, PeachPay will not run properly. Return without further initialization.
			return false;
		}

		// Initialize all other services after dependencies are checked.
		new PeachPay_Test_Mode_Service();
		new PeachPay_Alert_Service();

		// phpcs:ignore
		// $db = PeachPay_Database::instance();
		// phpcs:ignore
		// $db->initialize_tables();

		// phpcs:ignore
		// self::$cart_tracker = new PeachPay_Cart_Tracker( $db );

		return true;
	}

	/**
	 * Initializes modules that need to be initialized later than the init action.
	 */
	public static function post_init() {
		if ( peachpay_gateway_available() && ( ! is_admin() || peachpay_is_rest() ) ) {
			new PeachPay_Routes_Manager( self::$cart_tracker );
		}
	}
}
