<?php
/**
 * PeachPay Stripe Afterpay / Clearpay gateway.
 *
 * @package PeachPay
 */

defined( 'PEACHPAY_ABSPATH' ) || exit;

/**
 * .
 */
class PeachPay_Stripe_Afterpay_Gateway extends PeachPay_Stripe_Payment_Gateway {

	/**
	 * .
	 */
	public function __construct() {
		$this->id                                    = 'peachpay_stripe_afterpay';
		$this->stripe_payment_method_type            = 'afterpay_clearpay';
		$this->stripe_payment_method_capability_type = 'afterpay_clearpay';
		$this->icon                                  = PeachPay::get_asset_url( 'img/marks/afterpay.svg' );
		$this->settings_priority                     = 5;

		// Customer facing title and description.
		$this->title = __( 'Afterpay', 'peachpay-for-woocommerce' );
		// translators: %s Button text name.
		$this->description = __( 'After selecting %s you will be redirected to complete your payment.', 'peachpay-for-woocommerce' );

		$this->currencies            = array( 'USD', 'CAD', 'GBP', 'AUD', 'NZD', 'EUR' );
		$this->countries             = array( 'US', 'CA', 'GB', 'AU', 'NZ', 'FR', 'ES' );
		$this->payment_method_family = __( 'Buy now, Pay later', 'peachpay-for-woocommerce' );
		$this->min_amount            = 1;
		$this->max_amount            = 2000;

		$this->form_fields = self::capture_method_setting( $this->form_fields );

		parent::__construct();
	}

	/**
	 * Setup future settings for payment intent.
	 */
	protected function setup_future_usage() {
		return null;
	}

	/**
	 * AfterPay does not support virtual product purchases.
	 */
	public function is_available() {
		$is_available = parent::is_available();

		// Availability for cart/checkout page
		if ( WC()->cart ) {
			if ( ! WC()->cart->needs_shipping() ) {
				$is_available = false;
			}
		}

		// Availability for only the order pay page.
		if ( $is_available && is_wc_endpoint_url( 'order-pay' ) ) {
			$order_id = absint( get_query_var( 'order-pay' ) );
			$order    = wc_get_order( $order_id );

			if ( ! $order instanceof WC_Order || ! $order->has_shipping_address() ) {
				$is_available = false;
			}
		}

		return $is_available;
	}
}
