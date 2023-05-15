<?php
/**
 * Stripe admin actions.
 *
 * @phpcs:disable WordPress.Security.NonceVerification.Recommended
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * Handles stripe settings actions.
 *
 * @param array $plugin_capabilities The current capabilities of the PeachPay plugin.
 */
function peachpay_stripe_handle_admin_actions( $plugin_capabilities ) {
	if ( peachpay_plugin_has_capability_config( 'stripe', $plugin_capabilities ) && isset( peachpay_plugin_get_capability( 'stripe', $plugin_capabilities )['config'] ) ) {
		$stripe_config = peachpay_plugin_get_capability( 'stripe', $plugin_capabilities )['config'];
		update_option( 'peachpay_connected_stripe_config', $stripe_config );
	} else {
		delete_option( 'peachpay_connected_stripe_config' );
	}

	// Update stripe capabilities and account info.
	if ( peachpay_plugin_has_capability( 'stripe', $plugin_capabilities ) ) {
		$stripe_account = peachpay_plugin_get_capability( 'stripe', $plugin_capabilities )['account'];

		update_option( 'peachpay_connected_stripe_account', $stripe_account );
	} else {
		delete_option( 'peachpay_connected_stripe_account' );
	}

	// Display stripe connect message.
	if ( isset( $_GET['connected_stripe'] ) && 'true' === $_GET['connected_stripe'] ) {
		// See PayPal version of this below for commentary.
		if ( ! is_array( get_option( 'peachpay_payment_options' ) ) ) {
			update_option( 'peachpay_payment_options', array() );
		}

		peachpay_set_settings_option( 'peachpay_payment_options', 'enable_stripe', 1 );

		add_settings_error(
			'peachpay_messages',
			'peachpay_message',
			__( 'You have successfully connected your Stripe account. You may set up other payment methods in the "Payment methods" tab.', 'peachpay-for-woocommerce' ),
			'success'
		);
	}

	if ( isset( $_GET['unlink_stripe'] ) && PeachPay_Stripe_Integration::connected() ) {
		peachpay_unlink_stripe();
	}
}
add_action( 'peachpay_settings_admin_action', 'peachpay_stripe_handle_admin_actions', 10, 1 );



/**
 * Unlink merchant Stripe Account
 */
function peachpay_unlink_stripe() {
	if ( ! peachpay_unlink_stripe_request() ) {
		add_settings_error( 'peachpay_messages', 'peachpay_message', __( 'Unable to unlink Stripe account. Please try again or contact us if you need help.', 'peachpay-for-woocommerce' ), 'error' );
		return;
	}

	delete_option( 'peachpay_connected_stripe_account' );
	peachpay_set_settings_option( 'peachpay_payment_options', 'enable_stripe', 0 );

	add_settings_error(
		'peachpay_messages',
		'peachpay_message',
		__( 'You have successfully unlinked your Stripe account.', 'peachpay-for-woocommerce' ),
		'success'
	);
}


/**
 * Get unlink merchant Stripe status
 */
function peachpay_unlink_stripe_request() {
	$stripe_id = PeachPay_Stripe_Integration::connect_id();
	// Followup(refactor): Use merchantid and wp_remote_post
	$response = wp_remote_get( peachpay_api_url( 'prod' ) . 'api/v1/stripe/merchant/unlink?stripeAccountId=' . $stripe_id . '&merchantStore=' . get_home_url() );

	if ( ! peachpay_response_ok( $response ) ) {
		return 0;
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( is_wp_error( $data ) ) {
		return 0;
	}

	// Clear Apple Pay registration.
	delete_option( 'peachpay_apple_pay_settings_v2' );

	return $data['unlink_success'];
}

