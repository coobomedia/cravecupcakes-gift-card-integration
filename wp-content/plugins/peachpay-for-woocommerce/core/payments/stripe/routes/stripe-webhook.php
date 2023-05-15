<?php
/**
 * PeachPay stripe order-status endpoints hooks.
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * Stripe webhook payment success hook.
 *
 * @param WP_REST_Request $request The webhook request data.
 */
function peachpay_rest_api_stripe_webhook( $request ) {
	$order = wc_get_order( $request['order_id'] );
	if ( ! $order ) {
		wp_send_json_error( 'Required field "order_id" was invalid or missing', 400 );
		return;
	}

	$reason = '';
	if ( isset( $request['status_message'] ) ) {
		$reason = $request['status_message'];
	}

	PeachPay_Stripe::calculate_payment_state( $order, $request, $reason );

	wp_send_json_success(
		array(
			'status' => $order->get_status(),
		)
	);
}
