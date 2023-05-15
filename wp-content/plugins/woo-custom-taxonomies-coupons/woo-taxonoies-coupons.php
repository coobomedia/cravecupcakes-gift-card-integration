<?php
/**
 * Plugin Name:       Woo Custom Taxonomies Coupons
 * Plugin URI:        https://wordpress.org/plugins/woo-taxonomies-coupons/
 * Description:       Issue Coupons for your selected taxonomies including tags and all of your custom taxonomies
 * Version:           2.1
 * Author:            Roman Bondarenko
 * Author URI:        https://unrealthemes.site/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-taxonomies-coupons
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require plugin_dir_path( __FILE__ ) . 'loader.php';