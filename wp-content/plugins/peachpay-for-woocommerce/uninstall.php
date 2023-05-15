<?php
/**
 * PeachPay uninstall script.
 *
 * @package PeachPay
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$payment_options = get_option( 'peachpay_payment_options' );

// When data_retention is true, that means they checked the box to remove plugin
// data upon uninstall.
if ( is_array( $payment_options ) && array_key_exists( 'data_retention', $payment_options ) && $payment_options['data_retention'] ) {
	// Old, deprecated options which were all in one group.
	delete_option( 'peachpay_options' );

	// Payment
	delete_option( 'peachpay_payment_options' );

	// Currency
	delete_option( 'peachpay_currency_options' );


	// Field editor
	delete_option( 'peachpay_field_editor' );
	delete_option( 'peachpay_field_editor_additional' );
	delete_option( 'peachpay_field_editor_billing' );
	delete_option( 'peachpay_field_editor_shipping' );

	// Recommended products
	delete_option( 'peachpay_related_products_options' );

	// Express Checkout
	delete_option( 'peachpay_express_checkout_branding' );
	delete_option( 'peachpay_express_checkout_window' );
	delete_option( 'peachpay_express_checkout_product_recommendations' );
	delete_option( 'peachpay_express_checkout_button' );
	delete_option( 'peachpay_express_checkout_advanced' );

	// Floating options.
	delete_option( 'peachpay_merchant_id' );
	delete_option( 'peachpay_payment_settings_initialized' );
	delete_option( 'peachpay_set_default_button_settings' );
	delete_option( 'peachpay_migrate_button_position' );
	delete_option( 'peachpay_migrated_float_button_icon' );
	delete_option( 'peachpay_connected_stripe_account' );
	delete_option( 'peachpay_connected_square_config' );
	delete_option( 'peachpay_connected_square_account' );
	delete_option( 'peachpay_square_apple_pay_config_live' );
	delete_option( 'peachpay_square_apple_pay_config_test' );
	delete_option( 'peachpay_api_access_denied' );
	delete_option( 'peachpay_valid_key' );
	delete_option( 'peachpay_deny_add_to_cart_redirect' );
	delete_option( 'peachpay_migrated_to_enable_stripe_checkbox' );
	delete_option( 'peachpay_apple_pay_settings' );
	delete_option( 'peachpay_apple_pay_settings_v2' );
	delete_option( 'peachpay_migrated_settings_after_reorg' );
}
