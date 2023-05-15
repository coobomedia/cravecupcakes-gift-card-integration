<?php
/**
 * PayPal admin actions.
 *
 * @phpcs:disable WordPress.Security.NonceVerification.Recommended
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * Handles PayPal settings actions.
 *
 * @param array $plugin_capabilities The plugin capabilities.
 */
function peachpay_paypal_handle_admin_actions( $plugin_capabilities ) {
		// Update paypal capabilities and config info.
	if ( peachpay_plugin_has_capability_config( 'paypal', $plugin_capabilities ) && isset( peachpay_plugin_get_capability( 'paypal', $plugin_capabilities )['config'] ) ) {
		$paypal_config = peachpay_plugin_get_capability( 'paypal', $plugin_capabilities )['config'];

		update_option( 'peachpay_connected_paypal_config', $paypal_config );
	} else {
		delete_option( 'peachpay_connected_paypal_config' );
	}

	// Update paypal capabilities and account info.
	if ( peachpay_plugin_has_capability( 'paypal', $plugin_capabilities ) ) {
		$paypal_account = peachpay_plugin_get_capability( 'paypal', $plugin_capabilities )['account'];

		update_option( 'peachpay_connected_paypal_account', $paypal_account );
	} else {
		delete_option( 'peachpay_connected_paypal_account' );
	}

	if ( isset( $_GET['connected_paypal'] ) && 'true' === $_GET['connected_paypal'] ) {
		add_settings_error(
			'peachpay_messages',
			'peachpay_message',
			__( 'You have successfully connected your PayPal account. You may set up other payment methods in the "Payment methods" tab.', 'peachpay-for-woocommerce' ),
			'success'
		);
	}

	if ( isset( $_GET['unlink_paypal'] ) && PeachPay_PayPal_Integration::connected() ) {
		peachpay_unlink_paypal();
	}
}
add_action( 'peachpay_settings_admin_action', 'peachpay_paypal_handle_admin_actions', 10 );

/**
 * Unlink merchant PayPal Account
 */
function peachpay_unlink_paypal() {
	if ( ! peachpay_unlink_paypal_request() ) {
		add_settings_error( 'peachpay_messages', 'peachpay_message', __( 'Unable to unlink PayPal account. Please try again or contact us if you need help.', 'peachpay-for-woocommerce' ), 'error' );
		return;
	}

	delete_option( 'peachpay_connected_paypal_account' );

	add_settings_error(
		'peachpay_messages',
		'peachpay_message',
		__( 'You have successfully unlinked your PayPal account. Please revoke the API permissions in your PayPal account settings as well.', 'peachpay-for-woocommerce' ),
		'success'
	);
}

/**
 * Get unlink merchant PayPal status
 */
function peachpay_unlink_paypal_request() {
	$merchant_hostname = preg_replace( '(^https?://)', '', home_url() );
	$response          = wp_remote_get( peachpay_api_url( 'prod' ) . 'api/v1/paypal/merchant/unlink?merchantHostname=' . $merchant_hostname );

	if ( ! peachpay_response_ok( $response ) ) {
		return 0;
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( is_wp_error( $data ) ) {
		return 0;
	}
	return $data['unlink_success'];
}
