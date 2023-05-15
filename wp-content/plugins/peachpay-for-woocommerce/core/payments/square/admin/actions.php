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
function peachpay_square_handle_admin_actions( $plugin_capabilities ) {
	// Set square config. These values are always supplied because they may be required to signup square.(Public keys etc..)
	if ( peachpay_plugin_has_capability_config( 'square', $plugin_capabilities ) ) {
		$square_config = peachpay_plugin_get_capability_config( 'square', $plugin_capabilities );
		update_option( 'peachpay_connected_square_config', $square_config );
	} else {
		delete_option( 'peachpay_connected_square_config' );
	}

	// Set connected square account details. These values only exists if a square account is connected.
	if ( peachpay_plugin_has_capability( 'square', $plugin_capabilities ) ) {
		$square_account = peachpay_plugin_get_capability( 'square', $plugin_capabilities )['account'];
		update_option( 'peachpay_connected_square_account', $square_account );

		if ( peachpay_square_merchant_permission_version() < peachpay_square_permission_version() ) {
			add_settings_error(
				'peachpay_messages',
				'peachpay_message',
				__( 'New features have been added to Square which require your action to enable. Please navigate to Square settings under the Payment tab for more details.', 'peachpay-for-woocommerce' ),
				'error'
			);
		}
	} else {
		delete_option( 'peachpay_connected_square_account' );
	}

	// Handle Square connection.
	if ( isset( $_GET['connected_square'] ) && 'true' === $_GET['connected_square'] ) {

		peachpay_set_settings_option( 'peachpay_payment_options', 'square_enable', 1 );

		add_settings_error(
			'peachpay_messages',
			'peachpay_message',
			__( 'You have successfully connected your Square account. You may set up other payment methods in the "Payment methods" tab.', 'peachpay-for-woocommerce' ),
			'success'
		);
	} elseif ( isset( $_GET['connected_square'] ) && 'false' === $_GET['connected_square'] ) {
		add_settings_error(
			'peachpay_messages',
			'peachpay_message',
			__( 'Square was not connected.', 'peachpay-for-woocommerce' ),
			'success'
		);
	}

	// Handle Square unlink.
	if ( isset( $_GET['unlink_square'] ) ) {
		if ( peachpay_unlink_square() ) {
			add_settings_error(
				'peachpay_messages',
				'peachpay_message',
				__( 'You have successfully unlinked your Square account.', 'peachpay-for-woocommerce' ),
				'success'
			);
		} else {
			add_settings_error(
				'peachpay_messages',
				'peachpay_message',
				__( 'An error occurred while unlinking your Square account.', 'peachpay-for-woocommerce' ),
				'error'
			);
		}
	}
}
add_action( 'peachpay_settings_admin_action', 'peachpay_square_handle_admin_actions', 10, 1 );

/**
 * Unlinks square from PeachPay.
 */
function peachpay_unlink_square() {
	$response = wp_remote_get( peachpay_api_url() . 'api/v1/square/unlink/oauth?merchant_id=' . peachpay_plugin_merchant_id() . '&merchant_url=' . get_site_url() );

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

	delete_option( 'peachpay_connected_square_account' );

	$suffix = peachpay_is_test_mode() ? '_test' : '_live';
	delete_option( 'peachpay_square_apple_pay_config' . $suffix );

	return 1;
}
