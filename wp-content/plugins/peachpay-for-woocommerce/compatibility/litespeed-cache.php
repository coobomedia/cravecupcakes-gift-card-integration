<?php
/**
 * Support for the litespeed plugin.
 * Plugin: https://wordpress.org/plugins/litespeed-cache/
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

add_filter( 'litespeed_optimize_js_excludes', 'peachpay_exclude_litespeed' );
add_action( 'peachpay_post_plugin_update_actions', 'purge_ls_cache' );

/**
 * Add our js to be excluded.
 *
 * @param array $excluded the other items to be excluded.
 */
function peachpay_exclude_litespeed( $excluded ) {
	$js_dir = new DirectoryIterator( PEACHPAY_ABSPATH . 'public/js' );
	if ( empty( $excluded ) ) {
		$excluded = array();
		foreach ( $js_dir as $file ) {
			if ( ! $file->isDot() ) {
				array_push( $excluded, $file->getFilename() );
			}
		}
		array_push( $excluded, 'bundle.js' );
		return $excluded;
	}
	if ( is_array( $excluded ) ) {
		foreach ( $js_dir as $file ) {
			if ( ! $file->isDot() ) {
				array_push( $excluded, $file->getFilename() );
			}
		}
	}
	array_push( $excluded, 'bundle.js' );
	return $excluded;
}

/**
 * Purge ls cache on peachpay update.
 */
function purge_ls_cache() {
	$purge = new LiteSpeed\Purge();
	$purge->purge_all();
}

