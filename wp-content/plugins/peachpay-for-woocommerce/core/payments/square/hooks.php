<?php
/**
 * PeachPay Square hooks
 *
 * @package PeachPay
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'peachpay_register_feature', 'peachpay_square_register_feature' );
add_action( 'wp_ajax_pp-square-applepay-domain-register', 'peachpay_square_handle_applepay_domain_registration' );
