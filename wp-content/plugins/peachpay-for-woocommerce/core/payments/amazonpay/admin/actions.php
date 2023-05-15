<?php
/**
 * Square admin actions.
 *
 * @phpcs:disable WordPress.Security.NonceVerification.Recommended
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * Handles Square settings actions.
 *
 * @param array $plugin_capabilities The current peachpay plugin capabilities.
 */
function peachpay_amazonpay_handle_admin_actions( $plugin_capabilities ) {
	if ( peachpay_plugin_has_capability( 'amazonpay', $plugin_capabilities ) ) {
		$amazonpay_account = peachpay_plugin_get_capability( 'amazonpay', $plugin_capabilities )['account'];
		update_option( 'peachpay_connected_amazonpay_account', $amazonpay_account );
	} else {
		delete_option( 'peachpay_connected_amazonpay_account' );
	}

	if ( isset( $_GET['unlink_amazonpay'] ) ) {
		if ( peachpay_unlink_amazonpay() ) {
			add_settings_error(
				'peachpay_messages',
				'peachpay_message',
				__( 'You have successfully unlinked your Amazon Pay account.', 'peachpay-for-woocommerce' ),
				'success'
			);
		} else {
			add_settings_error(
				'peachpay_messages',
				'peachpay_message',
				__( 'An error occurred while unlinking your Amazon Pay account.', 'peachpay-for-woocommerce' ),
				'error'
			);
		}
	}
}

/**
 * Unlinks Amazon Pay from PeachPay
 */
function peachpay_unlink_amazonpay() {
	$body     = array(
		'body' => array(
			'session' => array(
				'merchant_id'  => peachpay_plugin_merchant_id(),
				'merchant_url' => home_url(),
			),
		),
	);
	$response = wp_remote_post( peachpay_api_url() . 'api/v1/amazonpay/unlink', $body );

	if ( ! peachpay_response_ok( $response ) ) {
		return 0;
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( is_wp_error( $data ) ) {
		return 0;
	}

	if ( true !== $data['success'] ) {
		return 0;
	}

	delete_option( 'peachpay_connected_amazonpay_account' );
	peachpay_set_settings_option( 'peachpay_payment_options', 'amazonpay_enable', false );

	return 1;
}

add_action( 'peachpay_settings_admin_action', 'peachpay_amazonpay_handle_admin_actions', 10, 1 );
