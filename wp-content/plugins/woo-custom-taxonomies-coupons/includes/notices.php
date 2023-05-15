<?php

/**
 * Show notice
 */
function ut_display_woo_is_not_active() { 
?>

    <div class="error notice">
        <p><?php _e( 'WooCommerce is not Active!, WooCommerce Taxonomies Coupons Requires WooCommerce to Operate.', 'woo-taxonomies-coupons' ); ?></p>
    </div>

<?php 
}

/**
 * Check if the plugin WooCommerce is activated
 */
function ut_check_dependencies() {

	if ( ! class_exists( 'WooCommerce' ) ) {

		add_action( 'admin_notices', 'ut_display_woo_is_not_active' );

	} else {

		new Ut_Woo_Taxonomies_Coupons();
        
	}
}

add_action( 'plugins_loaded', 'ut_check_dependencies' );