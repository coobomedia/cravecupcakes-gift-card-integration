<?php
/**
 * Support for the auto optimize plugin.
 * Plugin: https://wordpress.org/plugins/autoptimize/
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

add_action( 'peachpay_init_compatibility', 'peachpay_autoptimize_integration' );

/**
 * Autoptimize integration function.
 */
function peachpay_autoptimize_integration() {
	$list = get_option( 'autoptimize_js_exclude', false );
	// This option for some reason will nullify all your excluded code so always set it to false so that doesn't happen.
	update_option( 'autoptimize_minify_excluded', false );

	$list = explode( ',', $list );

	if ( ! in_array( '/wp-content/plugins/peachpay-for-woocommerce', $list, true ) ) {
		array_push( $list, '/wp-content/plugins/peachpay-for-woocommerce' );
	}
	update_option( 'autoptimize_js_exclude', implode( ',', $list ) );
}
