<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'UT_WOO_TAX_COUPONS' ) ) {
	define( 'UT_WOO_TAX_COUPONS', __FILE__ );
}

if ( ! defined( 'UT_WOO_TAX_COUPONS_VERSION' ) ) {
	define( 'UT_WOO_TAX_COUPONS_VERSION', '1.0.0' );
}

if ( ! defined( 'UT_WOO_TAX_COUPONS_ABS' ) ) {
	define( 'UT_WOO_TAX_COUPONS_ABS', dirname( UT_WOO_TAX_COUPONS ) );
}

if ( ! defined( 'UT_DATE_FORMAT' ) ) {
	define( 'UT_DATE_FORMAT', 'd/m/Y' );
}



/**
 * Translation connection
 */
add_action( 'plugins_loaded', function() {

	load_plugin_textdomain( 'woo-taxonomies-coupons', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
} );



/**
 * @param $directory
 */
function ut_load_files_from_directory( $directory ) {

	if ( ! file_exists( UT_WOO_TAX_COUPONS_ABS . '/' . $directory ) ) {
		return;
	}
	$path  = UT_WOO_TAX_COUPONS_ABS . '/' . $directory . '/*.php';
	$files = glob( $path );

	if ( ! is_null( $files ) AND is_array( $files ) AND count( $files ) > 0 ) {
		foreach ( $files as $file ) {
			/** @noinspection PhpIncludeInspection */
			require_once $file;
		}
	}
}



function ut_load_files() {
	ut_load_files_from_directory( 'includes' );
}



ut_load_files();