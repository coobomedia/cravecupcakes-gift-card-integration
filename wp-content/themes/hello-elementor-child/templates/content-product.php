<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

//alog('product', $product. __FILE__, __LINE__);
?>
<li <?php wc_product_class( '', $product ); ?>>
	<div class="cupcakes-wrapper">
		<div class="cupcake">
			<div class="cc-thumb">
				<div class="cc-overlay">
					<?php echo $product->get_description(); ?>
				</div>
				<?php echo $product->get_image(); ?>
			</div>
			<div class="cc-title">
				<?php echo $product->get_name(); ?>
			</div>
			<div class="cc-price">
				<?php 
				echo get_woocommerce_currency_symbol();
				echo $product->get_price(); ?>
			</div>
			
		
			<!--  <div class="qnt-btn-wrapper">
				<button class="button-minus" data-field="quantity"><i class="fa fa-minus" aria-hidden="true"></i> </button>				 
				<input type="button" value="&minus;" class="button-minus" data-field="quantity">
				<input type="number" step="1" max="12" value="0" name="quantity" class="quantity-field" data-id="<?php // echo $product->get_id(); ?>">
				 <button class="button-plus" data-field="quantity"><i class="fa fa-plus" aria-hidden="true"></i> </button> 
				<input type="button" value="&plus;" class="button-plus" data-field="quantity">
			</div>
			-->
			
			<form action="<?php echo esc_url( $product->add_to_cart_url() ) ?>" class="cart" method="post" enctype="multipart/form-data">
				<?php echo woocommerce_quantity_input( array(), $product, false ); ?>
				<button type="submit" class="button alt"><?php echo esc_html( $product->add_to_cart_text() ) ?></button>
			</form>

		</div>
	</div>
</li>
<!--  -->