<?php
/**
 * Support for the WP-Optimize plugin.
 * Plugin: https://wordpress.org/plugins/wp-optimize/
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

// This function might be helpful if we run into cache issues purge_all_minify_cache.

add_action( 'peachpay_init_compatibility', 'peachpay_wp_optimize_compatability' );

/**
 * Init wp-optimize compatability.
 */
function peachpay_wp_optimize_compatability() {
	add_filter( 'wp-optimize-minify-default-exclusions', 'peachpay_exclude_wp_optimize' );
}

/**
 * Filter function to exclude oru scripts from wp-optimize.
 *
 * @param array $data already excluded scripts.
 */
function peachpay_exclude_wp_optimize( $data ) {
	array_push( $data, '/wp-content/plugins/peachpay-for-woocommerce' );
	return $data;
}
