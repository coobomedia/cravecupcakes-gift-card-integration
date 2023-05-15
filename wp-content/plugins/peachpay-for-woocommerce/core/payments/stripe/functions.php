<?php
/**
 * PeachPay Stripe integration hook functions. Utility functions should not be defined here.
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

/**
 * .
 *
 * @param array $gateways .
 */
function peachpay_stripe_init_gateways( $gateways ) {
	return array_merge( $gateways, PeachPay_Stripe_Integration::instance()->payment_gateways() );
}

/**
 * Adds the feature flag for enabling stripe gateways.
 *
 * @param array $feature_list The list of features.
 */
function peachpay_stripe_register_feature( $feature_list ) {
	$feature_list['peachpay_stripe_gateways'] = array(
		'enabled'  => (bool) PeachPay_Stripe_Integration::connected(),
		'metadata' => array(
			'connect_id'         => PeachPay_Stripe_Integration::connect_id(),
			'public_key'         => PeachPay_Stripe_Integration::public_key(),
			'country'            => PeachPay_Stripe_Integration::connect_country(),
			'locale'             => 'auto',
			'setup_intent_url'   => home_url() . '?wc-ajax=pp-stripe-setup-intent',
			'setup_intent_nonce' => wp_create_nonce( 'peachpay-stripe-setup-intent' ),

		),
	);

	return $feature_list;
}

/**
 * Displays some stripe charge details for a order.
 *
 * @param WC_Order $order The given order to display information for.
 */
function peachpay_stripe_display_order_transaction_details( $order ) {

	if ( did_action( 'woocommerce_admin_order_data_after_billing_address' ) > 1 ) {
		return;
	}
	if ( ! PeachPay_Stripe_Integration::is_payment_gateway( $order->get_payment_method() ) ) {
		return;
	}

	$payment_intent_id = PeachPay_Stripe_Order_Data::get_payment_intent( $order, 'id' );
	$payment_method_id = PeachPay_Stripe_Order_Data::get_payment_method( $order, 'id' );
	if ( null !== $payment_intent_id || null !== $payment_method_id ) {
		include PeachPay::get_plugin_path() . '/core/payments/stripe/admin/views/html-stripe-payment-info.php';
	}
}

/**
 * Displays the stripe capture/void form on the order dashboard.
 *
 * @param int $order_id Id for the order.
 */
function peachpay_stripe_display_order_transaction_capture_void_form( $order_id ) {
	if ( did_action( 'woocommerce_admin_order_totals_after_total' ) > 1 ) {
		return;
	}

	$order = wc_get_order( $order_id );

	if ( ! PeachPay_Stripe_Integration::is_payment_gateway( $order->get_payment_method() ) || $order->is_paid() ) {
		return;
	}

	$amount_capturable = PeachPay_Stripe_Order_Data::get_payment_intent( $order, 'amount_capturable' );
	if ( null !== $amount_capturable && $amount_capturable > 0 ) {
		include PeachPay::get_plugin_path() . '/core/payments/stripe/admin/views/html-stripe-capture-void-form.php';
	}
}

/**
 * Displays the Stripe balance transfer fee and net payout lines for the order dashboard.
 *
 * @param int $order_id Id for the order.
 */
function peachpay_stripe_display_balance_transaction_fee_lines( $order_id ) {
	if ( did_action( 'woocommerce_admin_order_totals_after_total' ) > 1 ) {
		return;
	}

	$order    = wc_get_order( $order_id );
	$refunded = floatval( $order->get_total_refunded() );

	if ( ! PeachPay_Stripe_Integration::is_payment_gateway( $order->get_payment_method() ) || ( ! ( $order->is_paid() ) && 0 === $refunded ) ) {
		return;
	}

	$balance_transaction = PeachPay_Stripe_Order_Data::get_charge( $order, 'balance_transaction' );
	if ( null !== $balance_transaction ) {
		include PeachPay::get_plugin_path() . '/core/payments/stripe/admin/views/html-stripe-transaction-fees.php';
	}
}

/**
 * Captures the payment if the order payment was authorized and not captured immediately.
 *
 * @param int      $order_id The order id.
 * @param string   $status_from The order status the order is being changed from.
 * @param string   $status_to The order status the order is being changed too.
 * @param WC_Order $order The instance of the order.
 */
function peachpay_stripe_handle_order_complete( $order_id, $status_from, $status_to, $order ) {
	if ( ! PeachPay_Stripe_Integration::is_payment_gateway( $order->get_payment_method() ) ) {
		return;
	}

	$transaction_id = $order->get_transaction_id();
	if ( ! $transaction_id || 'on-hold' !== $status_from || 'completed' !== $status_to ) {
		return;
	}

	$amount_capturable = PeachPay_Stripe_Order_Data::get_payment_intent( $order, 'amount_capturable' );
	if ( ! is_numeric( $amount_capturable ) || $amount_capturable <= 0 ) {
		return;
	}

	PeachPay_Stripe::capture_payment( $order, $amount_capturable );

	$charge_id = PeachPay_Stripe_Order_Data::get_charge( $order, 'id' );

	// translators: %1$s Payment method title,  %2$s charge id.
	$order->add_order_note( sprintf( __( 'Stripe %1$s payment captured. Payment is now complete (Charge Id: %2$s)', 'peachpay-for-woocommerce' ), $order->get_payment_method_title(), $charge_id ) );
}

/**
 * Callback function that gets activated when merchant changes order status to cancelled.
 *
 * @param string $order_id The order id of the order that was cancelled.
 */
function peachpay_stripe_handle_order_cancelled( $order_id ) {
	if ( ! peachpay_get_settings_option( 'peachpay_payment_options', 'refund_on_cancel', false ) ) {
		return;
	}

	$order = wc_get_order( $order_id );

	if ( $order->meta_exists( '_pp_stripe_auto_cancelled_on_dispute' ) && 'true' === $order->get_meta( '_pp_stripe_auto_cancelled_on_dispute', true ) ) {
		return;
	}

	if ( ! PeachPay_Stripe_Integration::is_payment_gateway( $order->get_payment_method() ) ) {
		return;
	}

	PeachPay_Stripe::refund_payment( $order );
}

/**
 * Captures the payment if the order payment was authorized and not captured immediately.
 *
 * @param int      $order_id The order id.
 * @param string   $status_from The order status the order is being changed from.
 * @param string   $status_to The order status the order is being changed too.
 * @param WC_Order $order The instance of the order.
 */
function peachpay_stripe_handle_order_processing( $order_id, $status_from, $status_to, $order ) {
	if ( ! PeachPay_Stripe_Integration::is_payment_gateway( $order->get_payment_method() ) ) {
		return;
	}

	$transaction_id = $order->get_transaction_id();
	if ( ! $transaction_id || 'on-hold' !== $status_from || 'processing' !== $status_to ) {
		return;
	}

	$amount_authorized = PeachPay_Stripe_Order_Data::get_payment_intent( $order, 'amount_capturable' );
	if ( ! is_numeric( $amount_authorized ) || $amount_authorized <= 0 ) {
		return;
	}

	PeachPay_Stripe::capture_payment( $order, $amount_authorized );

	$charge_id = PeachPay_Stripe_Order_Data::get_charge( $order, 'id' );

	// translators: %1$s Payment method title,  %2$s charge id.
	$order->add_order_note( sprintf( __( 'Stripe %1$s payment captured. Payment is now complete (Charge Id: %2$s)', 'peachpay-for-woocommerce' ), $order->get_payment_method_title(), $charge_id ) );
}

/**
 * Indicates if Stripe Apple Pay domain is registered or not.
 */
function peachpay_stripe_apple_pay_domain_registered() {
	$config = peachpay_stripe_get_apple_pay_config();

	return $config['registered'];
}

/**
 * Gets Stripe Apple Pay domain config.
 */
function peachpay_stripe_get_apple_pay_config() {
	$config = get_option(
		'peachpay_stripe_applepay_config',
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
function peachpay_stripe_update_apple_pay_config( $config ) {
	if ( ! $config || ! is_array( $config ) ) {
		return;
	}

	update_option( 'peachpay_stripe_applepay_config', $config );
}

/**
 * Handles Stripe ApplePay domain registration from TS side.
 */
function peachpay_stripe_handle_applepay_domain_registration() {
	if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'peachpay-applepay-domain-register' ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'Invalid nonce',
			)
		);
	}

	wp_send_json( peachpay_stripe_applepay_domain_register( true ) );
}

/**
 * Makes the request for registering the domain for Apple Pay for Stripe.
 *
 * @param bool $force Forces the domain registration attempt even if the auto_attempt has been done.
 */
function peachpay_stripe_applepay_domain_register( $force = false ) {
	if ( ! PeachPay_Stripe_Integration::connected() ) {
		return array(
			'success' => false,
			'message' => __( 'Stripe is not connected.', 'peachpay-for-woocommerce' ),
		);
	}

	$current_domain = wp_parse_url( get_site_url(), PHP_URL_HOST );
	$config         = peachpay_stripe_get_apple_pay_config();

	// Domain is different so reset registration.
	if ( $current_domain !== $config['domain'] ) {
		$config['domain']       = $current_domain;
		$config['registered']   = false;
		$config['auto_attempt'] = false;

		peachpay_stripe_update_apple_pay_config( $config );
	}

	if ( ! $force ) {
		if ( $config['registered'] ) {
			return array(
				'success' => true,
				'message' => __( 'Domain is already registered.', 'peachpay-for-woocommerce' ),
			);
		}

		if ( $config['auto_attempt'] ) {
			return array(
				'success' => false,
				'message' => __( 'Automatic attempt has already occured. Use force=true to force a registration attempt.', 'peachpay-for-woocommerce' ),
			);
		}
	}

	update_option( 'peachpay_attempt_applepay', 'stripe' );
	$response = wp_remote_post(
		'https://prod.peachpay.app/api/v1/stripe/apple-pay/merchant/register',
		array(
			'headers' => array( 'Content-Type' => 'application/json' ),
			'timeout' => 60,
			'body'    => wp_json_encode(
				array(
					'session' => array(
						'merchant_url'    => peachpay_get_site_url(),
						'merchant_domain' => $current_domain,
						'stripe'          => array(
							'connect_id' => PeachPay_Stripe_Integration::connect_id(),
						),
					),
				)
			),
		)
	);

	$data = wp_remote_retrieve_body( $response );
	if ( is_wp_error( $data ) ) {
		$config['registered']   = false;
		$config['auto_attempt'] = true;
		peachpay_stripe_update_apple_pay_config( $config );
		return array(
			'success' => false,
			'message' => __( 'Failed to retrieve the response body.', 'peachpay-for-woocommerce' ),
		);
	}

	$data = json_decode( $data, true );
	if ( ! isset( $data['success'] ) || ! $data['success'] ) {
		return array(
			'success' => false,
			'message' => isset( $data['message'] ) ? $data['message'] : __( 'Failed to register domain.', 'peachpay-for-woocommerce' ),
		);
	}

	$config['registered']   = true;
	$config['auto_attempt'] = true;
	peachpay_stripe_update_apple_pay_config( $config );
	return array(
		'success' => true,
		'message' => $data['message'],
	);
}

/**
 * WP ajax request to capture a stripe payment.
 */
function peachpay_stripe_handle_capture_payment() {
	if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'peachpay-stripe-capture-payment' ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'Invalid nonce',
			)
		);
	}

	if ( ! isset( $_POST['order_id'] ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "order_id" is missing.',
			)
		);
	}
	$order_id = floatval( wp_unslash( $_POST['order_id'] ) );

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "order_id" did not match any orders.',
			)
		);
	}

	if ( ! isset( $_POST['amount_to_capture'] ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "amount_to_capture" is missing.',
			)
		);
	}
	$amount_to_capture = PeachPay_Stripe::format_amount( floatval( wp_unslash( $_POST['amount_to_capture'] ) ), $order->get_currency() );

	wp_send_json( PeachPay_Stripe::capture_payment( $order, $amount_to_capture ) );
}

/**
 * WP ajax request to void a stripe payment.
 */
function peachpay_stripe_handle_void_payment() {
	if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'peachpay-stripe-void-payment' ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'Invalid nonce',
			)
		);
	}

	if ( ! isset( $_POST['order_id'] ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "order_id" is missing.',
			)
		);
	}
	$order_id = floatval( wp_unslash( $_POST['order_id'] ) );

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "order_id" did not match any orders.',
			)
		);
	}

	wp_send_json( PeachPay_Stripe::void_payment( $order ) );
}

/**
 * WC ajax request to create a stripe setup intent
 */
function peachpay_stripe_handle_setup_intent() {
	if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'peachpay-stripe-setup-intent' ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'Invalid nonce',
			)
		);
	}

	if ( ! isset( $_POST['session_id'] ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "session_id" was invalid or missing.',
			)
		);
	}
	$session_id = sanitize_text_field( wp_unslash( $_POST['session_id'] ) );

	if ( ! isset( $_POST['payment_method_type'] ) ) {
		return wp_send_json(
			array(
				'success' => false,
				'message' => 'The field "payment_method_type" was invalid or missing.',
			)
		);
	}
	$payment_method_type = sanitize_text_field( wp_unslash( $_POST['payment_method_type'] ) );

	$payment_method = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : null;

	wp_send_json(
		PeachPay_Stripe::setup_payment(
			$session_id,
			array(
				'customer'               => PeachPay_Stripe::get_customer( get_current_user_id() ),
				'payment_method'         => $payment_method,
				'payment_method_types'   => array( $payment_method_type ),
				'usage'                  => 'off_session',
				'payment_method_options' => array(
					'us_bank_account' => array(
						'verification_method' => 'instant',
					),
				),
			)
		)
	);
}

/**
 * Controls the output for PeachPay stripe tokenized payment methods on the my account page.
 *
 * @param  array            $item         Individual list item from woocommerce_saved_payment_methods_list.
 * @param  WC_Payment_Token $payment_token The payment token associated with this method entry.
 * @return array                           Filtered item.
 */
function peachpay_stripe_get_account_saved_payment_methods_list_item( $item, $payment_token ) {
	if ( 'peachpay_stripe_card' === strtolower( $payment_token->get_type() ) ) {

		$item['method']['last4'] = $payment_token->get_last4();
		$item['method']['brand'] = $payment_token->get_card_type();
		$item['expires']         = sprintf( '%s / %s', $payment_token->get_expiry_month(), $payment_token->get_expiry_year() );

		return $item;
	}

	if ( 'peachpay_stripe_achdebit' === strtolower( $payment_token->get_type() ) ) {

		$item['method']['last4'] = $payment_token->get_last4();
		$item['method']['brand'] = 'ACH: ' . $payment_token->get_bank();

		return $item;
	}

	return $item;
}

/**
 * Filters display payment tokens.
 *
 * @param array $tokens the list of tokens to filter.
 */
function peachpay_get_customer_payment_tokens( $tokens ) {

	$current_mode = PeachPay_Stripe_Integration::mode();
	$connect_id   = PeachPay_Stripe_Integration::connect_id();

	foreach ( $tokens as $id => $token ) {
		if ( $token instanceof WC_Payment_Token_PeachPay_Stripe_Achdebit ) {
			if ( $token->get_mode() !== $current_mode ) {
				unset( $tokens[ $id ] );
			}
			if ( $token->get_connect_id() !== $connect_id ) {
				unset( $tokens[ $id ] );
			}
		}

		if ( $token instanceof WC_Payment_Token_PeachPay_Stripe_Card ) {
			if ( $token->get_mode() !== $current_mode ) {
				unset( $tokens[ $id ] );
			}
			if ( $token->get_connect_id() !== $connect_id ) {
				unset( $tokens[ $id ] );
			}
		}
	}

	return $tokens;
}

