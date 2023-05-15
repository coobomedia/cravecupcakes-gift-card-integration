<?php
/**
 * Support for the SG-Cachepress plugin.
 * Plugin: https://wordpress.org/plugins/sg-cachepress/
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

add_action( 'peachpay_init_compatibility', 'peachpay_cachepress_support' );

/**
 * Use this to exclude all our scripts from minifying. All new scripts must be added manually since they dont' exist in $wp_scripts at this point.
 */
function peachpay_cachepress_support() {
	add_filter( 'sgo_javascript_combine_exclude', 'peachpay_exclude_js' );
	add_filter( 'sgo_js_minify_exclude', 'peachpay_exclude_js' );
}

/**
 * Used by hooks for SVG-Cachepress to not minify or combine our js.
 *
 * @param array $data the current list of not allowed js.
 */
function peachpay_exclude_js( $data ) {
	$peachpay_excludes = array(
		'pp-sentry-lib',
		'pp-stripe',
		'pp-translations',
		'pp-translations-terms',
		'pp-giftcards',
		'pp-coupons',
		'pp-button-product-page',
		'pp-button-cart-page',
		'pp-button-checkout-page',
		'pp-button-core',
		'pp-button-shortcode',
		'pp-upsell',
		'pp-quantity-changer',
		'peachpay_currency_widget',
		'pp-sentry',
	);

	if ( ! $data ) {
		$data = $peachpay_excludes;
	} else {
		foreach ( $peachpay_excludes as $script ) {
			if ( ! in_array( $script, $data, true ) ) {
				array_push( $data, $script );
			}
		}
	}

	return $data;
}
