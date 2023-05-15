<?php
/**
 * PeachPay PayPal functions.
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * Adds the feature flag for enabling stripe gateways.
 *
 * @param array $feature_list The list of features.
 */
function peachpay_paypal_register_feature( $feature_list ) {
	$feature_list['peachpay_paypal_gateways'] = array(
		'enabled'  => PeachPay_PayPal_Integration::connected(),
		'metadata' => array(
			'merchant_id'            => PeachPay_PayPal_Integration::merchant_id(),
			'client_id'              => PeachPay_PayPal_Integration::client_id(),
			'partner_attribution_id' => PeachPay_PayPal_Integration::partner_attribution_id(),

			'update_order_url'       => home_url() . '?wc-ajax=pp-paypal-update-order',
			'update_order_security'  => wp_create_nonce( 'peachpay-paypal-update-order' ),

			'approve_order_url'      => home_url() . '?wc-ajax=pp-paypal-approve-order',
			'approve_order_security' => wp_create_nonce( 'peachpay-paypal-approve-order' ),
		),
	);

	return $feature_list;
}

/**
 * Creates a PayPal signup link.
 */
function peachpay_paypal_signup_url() {
	$response = wp_remote_get( peachpay_api_url( 'prod' ) . 'api/v1/paypal/signup?merchant_url=' . get_home_url() . '&wp_admin_url=' . get_site_url() );

	if ( ! peachpay_response_ok( $response ) ) {
		return;
	}

	$data = wp_remote_retrieve_body( $response );

	if ( is_wp_error( $data ) ) {
		return;
	}
	return $data;
}

/**
 * WC Ajax request to update a PayPal order.
 */
function peachpay_paypal_handle_update_order() {
	if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'peachpay-paypal-update-order' ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'Invalid nonce',
			),
			401
		);
	}

	if ( ! isset( $_POST['order_id'] ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "order_id" is missing.',
			),
			400
		);
	}
	$order_id = floatval( wp_unslash( $_POST['order_id'] ) );

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "order_id" did not match any WooCommerce orders.',
			),
			404
		);
	}

	wp_send_json( PeachPay_PayPal::update_order( $order ) );
}

/**
 * WC Ajax request to capture a PayPal order.
 */
function peachpay_paypal_handle_approve_order() {
	if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'peachpay-paypal-approve-order' ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'Invalid nonce',
			),
			401
		);
	}

	if ( ! isset( $_POST['order_id'] ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "order_id" is missing.',
			),
			400
		);
	}
	$order_id = floatval( wp_unslash( $_POST['order_id'] ) );

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "order_id" did not match any WooCommerce orders.',
			),
			404
		);
	}

	wp_send_json( PeachPay_PayPal::capture_order( $order ) );
}

/**
 * Display some PayPay transaction details for an order.
 *
 * @param WC_Order $order The given order to display information for.
 */
function peachpay_paypal_display_order_transaction_details( $order ) {
	if ( did_action( 'woocommerce_admin_order_data_after_billing_address' ) > 1 ) {
		return;
	}
	if ( ! PeachPay_PayPal_Integration::is_payment_gateway( $order->get_payment_method() ) ) {
		return;
	}

	$order_id = PeachPay_PayPal_Order_Data::get_order_transaction_details( $order, 'id' );
	if ( null !== $order_id ) {
		include PeachPay::get_plugin_path() . '/core/payments/paypal/admin/views/html-paypal-payment-info.php';
	}
}

/**
 * Displays the PayPal fee and net payout lines for the order dashboard.
 *
 * @param int $order_id Id for the order.
 */
function peachpay_paypal_display_fee_lines( $order_id ) {
	if ( did_action( 'woocommerce_admin_order_totals_after_total' ) > 1 ) {
		return;
	}

	$order    = wc_get_order( $order_id );
	$refunded = floatval( $order->get_total_refunded() );

	if ( ! PeachPay_PayPal_Integration::is_payment_gateway( $order->get_payment_method() ) || ( ! ( $order->is_paid() ) && 0 === $refunded ) ) {
		return;
	}

	$status = PeachPay_PayPal_Order_Data::get_order_transaction_details( $order, 'status' );
	if ( 'COMPLETED' === $status ) {
		include PeachPay::get_plugin_path() . '/core/payments/paypal/admin/views/html-paypal-transaction-fees.php';
	}
}

/**
 * Callback function that gets activated when merchant changes order status to cancelled.
 *
 * @param string $order_id The order id of the order that was cancelled.
 */
function peachpay_paypal_handle_order_cancelled( $order_id ) {
	$order = wc_get_order( $order_id );

	if ( ! $order || ! PeachPay_PayPal_Integration::is_payment_gateway( $order->get_payment_method() ) ) {
		return;
	}

	if ( PeachPay_PayPal_Advanced::get_setting( 'refund_on_cancel' ) !== 'yes' ) {
		return;
	}

	if ( $order->get_transaction_id() && $order->get_total() > 0 ) {
		$refund_result = PeachPay_PayPal::refund_payment(
			$order,
			array(
				'amount'        => array(
					'currency_code' => $order->get_currency(),
					'value'         => $order->get_total(),
				),
				'note_to_payer' => 'Order was canceled or removed.',
			)
		);

		if ( ! $refund_result['success'] ) {
			return;
		}

		$refund = new WC_Order_Refund();
		$refund->set_amount( $order->get_total() - $order->get_total_refunded() );
		$refund->set_parent_id( $order->get_id() );
		$refund->set_reason( 'Order was canceled or removed.' );
		$refund->set_refunded_by( get_current_user_id() );
		$refund->save();

		$order->set_status( 'refunded' );
		$order->save();
	}
}

/**
 * Makes sure if a PayPal gateway is selected on checkout to auto hide the button so the "Place Order" is not shown for a brief amount of time.
 *
 * @param string $button The existing button html.
 */
function peachpay_paypal_custom_order_button_html( $button ) {
	if ( ! WC()->session ) {
		return $button;
	}

	$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
	if ( peachpay_starts_with( $chosen_payment_method, 'peachpay_paypal_' ) ) {
		return str_replace( '<button', '<button style="display:none;" ', $button );
	}

	return $button;
}
