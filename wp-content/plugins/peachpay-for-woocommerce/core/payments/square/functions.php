<?php
/**
 * PeachPay Square utility functions
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * Square signup URL.
 */
function peachpay_square_signup_url() {
	// PHPCS:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	$state = base64_encode(
		wp_json_encode(
			array(
				'return_url'         => admin_url( 'admin.php?page=peachpay&tab=payment#square' ),
				'merchant_id'        => peachpay_plugin_merchant_id(),
				'merchant_url'       => home_url(),
				'permission_version' => peachpay_square_permission_version(),
			)
		)
	);

	return peachpay_square_connect_url() . "&state=$state";
}

/**
 * Gets the correct square application id for signup purposes.
 */
function peachpay_square_application_id() {
	$config = peachpay_square_config();

	if ( ! $config ) {
		return '';
	}

	return $config['application_id'];
}

/**
 * Gets the square location id for the signed up store.
 */
function peachpay_square_location_id() {
	$account = peachpay_square_connected();

	if ( ! $account ) {
		return '';
	}

	return $account['location_id'];
}

/**
 * Gets the square country for the signed up store.
 */
function peachpay_square_country() {
	$account = peachpay_square_connected();

	if ( ! $account ) {
		return '';
	}

	return $account['country'];
}

/**
 * Gets the square currency for the signed up store.
 */
function peachpay_square_currency() {
	$account = peachpay_square_connected();

	if ( ! $account ) {
		return '';
	}

	return $account['currency'];
}

/**
 * Determines if square is connected.
 */
function peachpay_square_connected() {
	return get_option( 'peachpay_connected_square_account', 0 );
}

/**
 * Gets square configuration values.
 */
function peachpay_square_config() {
	return get_option( 'peachpay_connected_square_config', 0 );
}

/**
 * Gets the square merchant id or returns an empty string if not already connected.
 */
function peachpay_square_merchant_id() {
	$account = peachpay_square_connected();

	if ( ! $account ) {
		return '';
	}

	return $account['merchant_id'];
}

/**
 * Gets Square Apple Pay domain config.
 */
function peachpay_square_get_apple_pay_config() {

	$suffix = peachpay_is_test_mode() ? '_test' : '_live';

	$config = get_option(
		'peachpay_square_apple_pay_config' . $suffix,
		array(
			'domain'       => wp_parse_url( get_site_url(), PHP_URL_HOST ),
			'registered'   => false,
			'auto_attempt' => false,
		)
	);

	return $config;
}

/**
 * Updates the Apple Pay domain registration config.
 *
 * @param array $config The Apple Pay domain config.
 */
function peachpay_square_update_apple_pay_config( $config ) {
	if ( ! $config || ! is_array( $config ) ) {
		return;
	}

	$suffix = peachpay_is_test_mode() ? '_test' : '_live';

	update_option( 'peachpay_square_apple_pay_config' . $suffix, $config );
}

/**
 * Indicates if Square Apple Pay domain is registered or now.
 */
function peachpay_square_apple_pay_domain_registered() {
	$config = peachpay_square_get_apple_pay_config();

	return $config['registered'];
}

/**
 * Handles Square ApplePay domain registration from TS side.
 */
function peachpay_square_handle_applepay_domain_registration() {
	//phpcs:ignore
	if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'peachpay-applepay-domain-register' ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'Invalid nonce',
			)
		);
	}

	wp_send_json( peachpay_square_register_apple_pay_domain( true ) );
}

/**
 * Attempts to automatically register the domain for Square Apple Pay.
 *
 * @param boolean $force Determined whether ApplePay domain register is forced (called from frontend side) or not.
 */
function peachpay_square_register_apple_pay_domain( $force = false ) {
	if ( ! peachpay_square_connected() ) {
		return array(
			'success' => false,
			'message' => __( 'Square is not connected.', 'peachpay-for-woocommerce' ),
		);
	}

	$current_domain = wp_parse_url( get_site_url(), PHP_URL_HOST );
	$config         = peachpay_square_get_apple_pay_config();

	// Domain is different so reset registration.
	if ( $current_domain !== $config['domain'] ) {
		$config['domain']       = $current_domain;
		$config['registered']   = false;
		$config['auto_attempt'] = false;

		peachpay_square_update_apple_pay_config( $config );
	}

	if ( ! $force ) {
		if ( $config['registered'] || $config['auto_attempt'] ) {
			return array(
				'success' => false,
				'message' => __( 'Domain is already registered or the automatic attempt has already occured. Use force=true to force a registration attempt.', 'peachpay-for-woocommerce' ),
			);
		}
	}

	update_option( 'peachpay_attempt_applepay', 'square' );
	$response = wp_remote_post(
		peachpay_api_url() . 'api/v1/square/applepay/verify-domain',
		array(
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => wp_json_encode(
				array(
					'domain'      => $current_domain,
					'merchant_id' => peachpay_plugin_merchant_id(),
				)
			),
		)
	);

	$data = wp_remote_retrieve_body( $response );

	if ( is_wp_error( $data ) ) {
		$config['registered']   = false;
		$config['auto_attempt'] = true;
		peachpay_square_update_apple_pay_config( $config );
		return array(
			'success' => false,
			'message' => __( 'Failed to retrieve the response body.', 'peachpay-for-woocommerce' ),
		);
	}

	$data = json_decode( $data, true );

	if ( ! isset( $data['success'] ) || ! $data['success'] ) {
		$config['registered']   = false;
		$config['auto_attempt'] = true;
		peachpay_square_update_apple_pay_config( $config );
		return array(
			'success' => false,
			'message' => $data['message'],
		);
	}

	$config['registered']   = true;
	$config['auto_attempt'] = true;
	peachpay_square_update_apple_pay_config( $config );
	return array(
		'success' => true,
		'message' => $data['message'],
	);
}

/**
 * Gets a stripe payment capability status.
 *
 * @param string $payment_key The payment capability to retrieve a status for.
 */
function peachpay_square_capability( $payment_key ) {
	$account = peachpay_square_connected();

	if ( ! $account ) {
		return false;
	}

	if ( ! array_key_exists( 'capabilities', $account ) ) {
		return false;
	}

	$capabilities = $account['capabilities'];

	switch ( $payment_key ) {
		case 'square_card_payments':
		case 'square_google_pay_payments':
		case 'square_apple_pay_payments':
			return in_array( 'CREDIT_CARD_PROCESSING', $capabilities, true );
		default:
			return false;
	}
}

/**
 * Returns latest square connect URL from config
 */
function peachpay_square_connect_url() {
	return peachpay_get_settings_option( 'peachpay_connected_square_config', 'connect_url' );
}

/**
 * Returns the latest server square permissions version from config.
 */
function peachpay_square_permission_version() {
	return peachpay_get_settings_option( 'peachpay_connected_square_config', 'permission_version', 0 );
}


/**
 * Returns the square permissions version for the merchant.
 */
function peachpay_square_merchant_permission_version() {
	return peachpay_get_settings_option( 'peachpay_connected_square_account', 'permission_version', 0 );
}

/**
 * Square webhook payment success hook.
 *
 * @param WC_Order $order The order to operate on.
 * @param array    $request The webhook request data.
 */
function peachpay_handle_square_success_status( $order, $request ) {
	$square = peachpay_array_value( $request, 'square' );
	if ( ! $square ) {
		wp_send_json_error( 'Required field "square" is missing or invalid', 400 );
	}

	$payment_id = peachpay_array_value( $square, 'payment_id' );
	if ( ! $payment_id ) {
		wp_send_json_error( 'Required field "square.payment_id" is missing or invalid', 400 );
	}

	$order->set_transaction_id( $payment_id );
}

/**
 * Adds the feature flag for enabling square gateways.
 *
 * @param array $feature_list The list of features.
 */
function peachpay_square_register_feature( $feature_list ) {
	$feature_list['peachpay_square_gateways'] = array(
		'enabled'  => (bool) peachpay_square_connected(),
		'metadata' => array(
			'country'        => peachpay_square_country(),
			'location_id'    => peachpay_square_location_id(),
			'application_id' => peachpay_square_application_id(),
		),
	);

	return $feature_list;
}

/**
 * Gets a link of the stripe order transaction id link.
 *
 * @param WC_Order $order The order object.
 */
function peachpay_square_transaction_url( $order ) {
	if ( ! $order->get_transaction_id() ) {
		return '';
	}

	if ( $order->get_meta( 'peachpay_is_test_mode' ) === 'true' || peachpay_is_staging_site() || peachpay_is_local_development_site() ) {
		return sprintf( 'https://squareupsandbox.com/dashboard/sales/transactions/%s', $order->get_transaction_id() );
	} else {
		return sprintf( 'https://squareup.com/dashboard/sales/transactions/%s', $order->get_transaction_id() );
	}
}
