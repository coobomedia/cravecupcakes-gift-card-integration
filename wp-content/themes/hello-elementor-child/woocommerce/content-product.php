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

$custom = get_post_custom($product->get_id());

/*echo "<pre>";
print_r($one_post);
print_r($custom);
echo "</pre>";*/

$visibility_date = $custom['visibility_start_date'][0];
$availability_start_date = $custom['availability_start_date'][0];
$availability_end_date = $custom['availability_end_date'][0];

//$cur_date = time();

$vis_date = strtotime($visibility_date);

$avl_date = strtotime($availability_start_date);
$end_date = strtotime($availability_end_date);

$selected_date = strtotime($_SESSION['date_sec']);

//echo $selected_date;

//print_r($_SESSION);

//alog('product', $product. __FILE__, __LINE__);

if($selected_date<=$end_date && $selected_date>=$avl_date) {
?>
<li id="product_<?php echo $product->get_id() ?>" <?php wc_product_class( '', $product ); ?>>
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
				<?php echo get_woocommerce_currency_symbol(); echo $product->get_price(); ?>
			</div>


			  <!-- <div class="qnt-btn-wrapper">

				<input type="button" value="&minus;" class="button-minus" data-field="quantity">
				<input type="number" step="1" max="12" value="0" name="quantity" class="quantity-field" data-id="<?php //echo $product->get_id(); ?>">

				<input type="button" value="&plus;" class="button-plus" data-field="quantity">
			</div> -->

			<div class="number">
				<!-- <span class="minus button-minus">-</span> -->
				<input type="button" value="−" class="button-minus minus">
				<input class="qty_input quantity-field" type="number" min value="0" name="quantity" data-id="<?php echo $product->get_id(); ?>" />
				<!-- <span class="plus button-plus">+</span> -->
				<input type="button" value="+" class="button-plus plus">
			</div>

			<!-- <form action="<?php echo esc_url( $product->add_to_cart_url() ) ?>" class="cart" data-id="<?php echo $product->get_id(); ?>" method="post" enctype="multipart/form-data">
				<?php // echo woocommerce_quantity_input( array(), $product, false ); ?>

			</form> -->

		</div>
	</div>
</li>

<?php } else if ($visibility_date==NULL || $selected_date==NULL){ ?>

	<li id="product_<?php echo $product->get_id() ?>" <?php wc_product_class( '', $product ); ?>>
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
				<?php echo get_woocommerce_currency_symbol();echo $product->get_price(); ?>
			</div>


			  <!-- <div class="qnt-btn-wrapper">

				<input type="button" value="&minus;" class="button-minus" data-field="quantity">
				<input type="number" step="1" max="12" value="0" name="quantity" class="quantity-field" data-id="<?php // echo $product->get_id(); ?>">

				<input type="button" value="&plus;" class="button-plus" data-field="quantity">
			</div> -->

			<div class="number">
				<!-- <span class="minus button-minus">-</span> -->
				<input type="button" value="−" class="button-minus minus">
				<input class="qty_input quantity-field" type="number" min value="0" name="quantity" data-id="<?php echo $product->get_id(); ?>" />
				<!-- <span class="plus button-plus">+</span> -->
				<input type="button" value="+" class="button-plus plus">
			</div>

			<!-- <form action="<?php echo esc_url( $product->add_to_cart_url() ) ?>" class="cart" data-id="<?php echo $product->get_id(); ?>" method="post" enctype="multipart/form-data">
				<?php // echo woocommerce_quantity_input( array(), $product, false ); ?>

			</form> -->

		</div>
	</div>
</li>

<?php } ?>
<!--  -->