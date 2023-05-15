<?php
/**
 * PeachPay utility functions
 *
 * @package PeachPay
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets updated script fragments.
 *
 * @param array $fragments .
 */
function peachpay_native_checkout_data_fragment( $fragments ) {
	$fragments['script#peachpay-native-checkout-js-extra'] = '<script id="peachpay-native-checkout-js-extra">var peachpay_checkout_data = ' . wp_json_encode( peachpay()->native_checkout_data() ) . ';</script>';
	return $fragments;
}
