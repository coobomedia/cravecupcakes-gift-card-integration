<?php
/**
 * PeachPay Admin settings secondary navigation HTML view.
 *
 * @var PeachPay_Admin_Section $admin_section The section this navigation belongs to.
 * @var PeachPay_Admin_Tab[] $admin_tab_views The array of tabs to render links for.
 *
 * @package PeachPay
 */

defined( 'PEACHPAY_ABSPATH' ) || exit;

?>
<div class="peachpay-secondary-navigation">
	<div class="row">
		<?php foreach ( $admin_tab_views as $admin_tab ) { ?>
			<div class="link <?php echo ( $admin_tab->is_active() ? 'current' : '' ); ?>">
				<a href="<?php echo $admin_tab::get_url(); // PHPCS:ignore ?>">
					<?php echo esc_html( $admin_tab->get_title() ); ?>
				</a>
			</div>
		<?php } ?>
	</div>
	<hr>
</div>
