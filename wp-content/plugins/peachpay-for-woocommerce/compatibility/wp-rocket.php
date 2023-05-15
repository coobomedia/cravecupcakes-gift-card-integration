<?php
/**
 * Functions for compatability with WP-Rocket
 * Plugin: https://wp-rocket.me/ .
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

add_action( 'peachpay_post_plugin_update_actions', 'peachpay_clear_rocket_cache' );
add_action( 'peachpay_init_compatibility', 'rocket_compatability_init' );

/**
 * Clear everything in the wp cache cache so we can update our js files.
 */
function peachpay_clear_rocket_cache() {
	if ( function_exists( 'rocket_clean_domain' ) ) {
		rocket_clean_domain();
	}
}

/**
 * We run into issues when our files are minify'd and defered so just exclude us if we aren't already.
 */
function rocket_compatability_init() {
	$settings = get_option( 'wp_rocket_settings' );
	if ( ! $settings ) {
		return;
	}
	$excluded = $settings['exclude_js'];
	$deferred = $settings['exclude_defer_js'];
	$delayed  = $settings['delay_js_exclusions'];
	$cookies  = $settings['cache_reject_cookies'];

	if ( is_array( $excluded ) && ! in_array( '/wp-content/plugins/peachpay-for-woocommerce', $excluded, true ) ) {
		array_push( $excluded, '/wp-content/plugins/peachpay-for-woocommerce' );
		$settings['exclude_js'] = $excluded;
	}
	if ( is_array( $deferred ) && ! in_array( '/wp-content/plugins/peachpay-for-woocommerce', $deferred, true ) ) {
		array_push( $deferred, '/wp-content/plugins/peachpay-for-woocommerce' );
		$settings['exclude_defer_js'] = $deferred;
	}
	if ( is_array( $delayed ) && ! in_array( '/wp-content/plugins/peachpay-for-woocommerce', $delayed, true ) ) {
		array_push( $delayed, '/wp-content/plugins/peachpay-for-woocommerce' );
		$settings['delay_js_exclusions'] = $delayed;
	}
	if ( is_array( $cookies ) && ! in_array( 'pp_active_currency', $cookies, true ) ) {
		array_push( $cookies, 'pp_active_currency' );
		$settings['cache_reject_cookies'] = $cookies;
	}

	update_option( 'wp_rocket_settings', $settings );
}
