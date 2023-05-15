    <?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.4.0
 */

defined('ABSPATH') || exit;
session_start();
// $_SESSION['test_value'] = "abc";
// unset($_SESSION);
$current_cat_id = $_SESSION['current_cat_id'];
$decor_type = $_SESSION['decor_type'];
$decor_color_1 = $_SESSION['decor_color_1'];
$decor_color_2 = $_SESSION['decor_color_2'];
$decor_image_2 = $_SESSION['decor_input_image_2'];

if (!isset($_SESSION['packaging_price'])) {
    $_SESSION['packaging_price'] = 0;
}
$total_packaging = 0;
$cats_qty = array();
$packaging = 0;
$grouping_session = array();
$group_product_ids = array();


do_action('woocommerce_before_cart'); ?>
<div class="cart-wrapper">
    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>
        <div class="cravecupshop-cart">
            <div class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                <div>
                    <?php do_action('woocommerce_before_cart_contents'); ?>

                    <?php



                    $cat_wisw_pros = array();
                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                        $product_id = $cart_item['product_id'];
                        $cat_ids = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
                        foreach ($cat_ids as $id) {
                            $cat_wisw_pros[$id][$cart_item_key] = $cart_item;
                        }
                    }
                    ksort($cat_wisw_pros); // Cat ID wise sort
                    $grouped_cart_items = array();
                    foreach ($cat_wisw_pros as $cat_id => $cart_items) {

// grouping Functionailty by cart


    $grouping = array();
    $groupCheck = true;
    $firstgroupID = '';

    //  echo '<pre>';
    // print_r( $_SESSION);
    // echo '</pre>';



    foreach ($cart_items as $one_item) {

             $product_ID = $one_item['product_id'];
             $nextgroupID = $_SESSION['group_carts'][$product_ID];
            // $nextgroupID = wc_get_order_item_meta( $product_ID, 'group_cart', true );
            array_push($grouping, $nextgroupID);
                array_push($grouping_session,$nextgroupID);

                $group_product_ids['group_carts'][$product_ID] = $_SESSION['group_carts'][$product_ID];
                // array_push($group_product_ids )


}
$grouping = array_unique($grouping);


 // End Grouping Functionailty


                        $term = get_term($cat_id, 'product_cat');
                        ?>
                        <div class="<?php echo $term->slug; ?> group-wrapper">
                            <h2 class="section-heading"><?php echo $term->name; ?> <?php if($term->term_id == 35){ echo "<span style='color:green;font-size:18px;'>For these cupcakes 1 Qty = 12 (1 Dzn) </span>"; } ?></h2>


                            <?php
                            // foreach ($cart_items as $one_item) {

                            //     if (!isset($cats_qty[$cat_id]['qty'])) {
                            //         $cats_qty[$cat_id]['qty'] = $one_item['quantity'];
                            //     } else {
                            //         $cats_qty[$cat_id]['qty'] += $one_item['quantity']; //print_r($cart_items);
                            //     }
                            // }


                            ?>

                            <?php





     foreach($grouping as $group){
        $checktopping = false;
        echo '<div data-group-id="'.$group.'" style="margin-top:20px">';
        $packaging = $group;
                            foreach ($cart_items as $cart_item_key => $cart_item) {

                                        $cart_item['woosb_keys'];
                                  
                                        // echo '<pre>';
                                        // print_r( $cart_item['woosb_parent_id']);
                                        // echo '</pre>';
                                        if(!empty($cart_item['woosb_parent_id'])){
                                            $checktopping = true;
                                        }

                                if (!array_key_exists($cart_item_key, $grouped_cart_items)) {
                                    $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                                    // $groupByID = wc_get_order_item_meta( $product_id, 'group_cart', true );
                                        $groupByID = $_SESSION['group_carts'][$product_id];
                                  if($groupByID == $group){

                                    if (!isset($cats_qty[$cat_id][$group]['qty'])) {
                                        $cats_qty[$cat_id][$group]['qty'] = $cart_item['quantity'];
                                    } else {
                                        $cats_qty[$cat_id][$group]['qty'] += $cart_item['quantity']; //print_r($cart_items);
                                    }

                                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                        ?>
                                        <div  class="cart-record woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">



                                            <div class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                                                <div class="cart-product-thumb">
                                                    <?php

                                                    $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                                                    if (!$product_permalink) {
                                                        echo $thumbnail; // PHPCS: XSS ok.
                                                    } else {
                                                        echo $thumbnail;
                                                        //printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                                    }
                                                    ?>
                                                </div>
                                                <div class="product-name-title">
                                                    <?php

                                                    if (!$product_permalink) {
                                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                                    } else {
                                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                                        //echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                                    }

                                                    do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                                                    // Meta data.
                                                    echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                                                    // Backorder notification.
                                                    if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                            <div class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                                                <?php
                                                echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                                ?>
                                            </div>

                                            <div class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                                                <?php
                                                if ($_product->is_sold_individually()) {
                                                    $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                                } else {
                                                    $product_quantity = woocommerce_quantity_input(
                                                        array(
                                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                                            'input_value'  => $cart_item['quantity'],
                                                            'max_value'    => $_product->get_max_purchase_quantity(),
                                                            'min_value'    => '0',
                                                            'product_name' => $_product->get_name(),
                                                        ),
                                                        $_product,
                                                        false
                                                    );
                                                }

                                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
                                                ?>
                                            </div>

                                            <div class="product-subtotal" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>">
                                                <?php
                                                echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                                ?>
                                            </div>

                                            <div class="product-remove">
                                                <?php
                                                echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                    'woocommerce_cart_item_remove_link',
                                                    sprintf(
                                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                                        esc_html__('Remove this item', 'woocommerce'),
                                                        esc_attr($product_id),
                                                        esc_attr($_product->get_sku())
                                                    ),
                                                    $cart_item_key
                                                );
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }

                                }
                            }
                        }



                            //  echo "<pre>";
                            //  print_r($cats_qty);
                            //  echo $cat_id;
                            //  echo "</pre>";
                            // change color if quatity less thn 12
                            $cat_qty = $cats_qty[$cat_id][$group]['qty'];
                            $currentDate = date("Y-m-d");
                            $color_code = "#55B7B3";
                            $op = 1;
                            $decor_type_text = $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_type'];
                            $disabled = "";
                            $color_1 = $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_input_1'];
                            $color_2 = $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_input_2'];
                            if ($cat_qty < 12 || $currentDate == $_SESSION['date_sec']) {
                                if($cat_id!=35){
                                    $color_code = "#E43945";
                                    $disabled = "disabled='disabled'";
                                    $op = 0.4;
                                    $decor_type_text = 'Select Decoration';
                                    $color_1 = 'Select 1st Color';
                                    $color_2 = 'Select 2nd Color';

                                    $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_type'] = "";
                                    $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_input_1'] ="";
                                    $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_input_2']="";

                                    $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_1'] ="";
                                    $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_2']="";
                                }else{
                                    if($currentDate == $_SESSION['date_sec'])
                                    $color_code = "#E43945";


                                }
                            }

                            //echo $cat_qty;




                            if(isset($_SESSION[$group]['cats']['wp_'.$term->term_id]['packaging'])){
                                $boxing_status = $_SESSION[$group]['cats']['wp_'.$term->term_id]['packaging'];

                                if($boxing_status=="individual_box"){
                                    if($cat_id==35){
                                        $boxing_price = (int)$cat_qty*12;
                                    } else {
                                        $boxing_price = (int)$cat_qty;
                                    }
                                    $boxing = "checked='checked'";
                                    $not_boxing = "";
                                } else {
                                    $boxing_price = 0;
                                    $boxing = "";
                                    $not_boxing = "checked='checked'";
                                }

                            } else {
                                $boxing_status = "";
                                $boxing_price = 0;
                                $boxing = "";
                                $not_boxing = "checked='checked'";
                            }
                            $term_id_acf = "product_cat_" . $term->term_id;

                            $packaging_opt = get_field('packaging_opt', $term_id_acf);
                            if ($packaging_opt == 1 &&  $checktopping == false) :
                                ?>
                                <div class="packaging-cart">
                                    <div class="packaging-title">
                                        Packaging
                                    </div>
                                    <div class="standardbox">
                                        <input class="packaging_status" type="radio" data-group-id="<?php echo $group ?>" data-term_id="<?php echo $term->term_id; ?>" value="standard_box" name="packaging_<?php echo $packaging; ?>" id="standard-box-cart_<?php echo $packaging; ?>" <?php echo $not_boxing; ?>>
                                        <label for="standard-box-cart_<?php echo $packaging; ?>">Standard Box</label>
                                    </div>
                                    <div class="individuallyboxed ">
                                        <input class="packaging_status" type="radio" data-group-id="<?php echo $group ?>" data-term_id="<?php echo $term->term_id; ?>" value="individual_box"  name="packaging_<?php echo $packaging; ?>" id="individuallyboxed_<?php echo $packaging; ?>" <?php echo $boxing; ?>>
                                        <label for="individuallyboxed_<?php echo $packaging; ?>">Individually Boxed</label>
                                        <p class="additional-box">(Additional $1/box)</p>
                                    </div>
                                    <div class="packaging-price">
                                        $<?php echo $boxing_price; $total_packaging = $total_packaging + $boxing_price; ?>
                                    </div>
                                </div>
                            <?php
                            endif;

                            /*if(isset($_SESSION['packaging_price'])){
                                WC()->cart->add_fee(__('Individual Boxing Price', 'hello-elementor'), $_SESSION['packaging_price']);
                            }*/
                            
                            //  echo "<pre>";
                            //  print_r($cart_items['data']);
                            //   echo "</pre>";

                            $check_topping = get_field('toppingdecoration', $term_id_acf);
                            if ($check_topping == 1 &&  $checktopping == false) :
                                ?>

                                <div class="decorations-cart">
                                    <div class="decorations-details">
                                        <h3>Decorations</h3>
                                        <p class="subheading" style="color:<?php echo $color_code; ?>;">Requires Minimum of 1 dozen. Not Available for Same Day Orders. </p>
                                        <p class="description">Without special decorations your cupcakes will come with the standard Crave design. Not available on Sugar-free, gluten-free, or Vegan Flavors</p>
                                    </div>
                                    <?php

                                    // $decoration = get_field("decorations", "option");
                                    // $types = array();
                                    // $colors = array();
                                    // $decoration_array = array();

                                    // foreach($decoration as $decor){
                                    // 	$types[] = $decor['type'];
                                    // 	$colors[] = $decor['color'];
                                    // 	$decoration_array[$decor['type']][$decor['color']] = $decor['image']['url'];
                                    // }
                                    $customize_topper = $_SESSION[$group]['cats']["wp_" . $term->term_id]['customize_topper'];
                                    $customize_topper_style = "";
                                    if( $customize_topper =="" || $customize_topper == NULL ):
                                        $customize_topper_style = "visibility: hidden";
                                    endif;
                                    ?>

                                    <div class="decor_type_wrapper" id="decor_type_input_<?php echo $group ?>" style="<?php echo $customize_topper_style; ?>">

                                        <?php $decor_type = get_field("_decor_type", "option");

                                        ?>

                                        <select name="decor_type" data-group_id="<?php echo $group ?>" data-cat_id="<?php echo $term->term_id; ?>" class="decor_type_<?php echo $group; ?> cart_decoration" id="decor_type_<?php echo $packaging; ?>" <?php echo $disabled; ?> style="border-color:<?php echo $color_code; ?>; opacity: <?php echo $op; ?>;">
                                            <option value="" selected>Select Decoration</option>
                                            <?php foreach ($decor_type as $type) { ?>
                                                <option data-image="<?php echo $type['decor_image']['url']; ?>" value="<?php echo $type['type_label']; ?>" <?php if($decor_type_text==$type['type_label']){echo "selected"; }?>>
                                                    <?php echo $type['type_label']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="decor_color_wrapper" id="decor_color_input_<?php echo $group ?>" style="<?php echo $customize_topper_style; ?>">
                                        <div class="color_1">
                                            <?php $decoration_colors = get_field("decoration_colors", "option"); ?>
                                            <select
                                            data-group_id="<?php echo $group ?>"
                                                    name="decor_color_1"
                                                    data-cat_id="<?php echo $term->term_id; ?>"
                                                    class="decor_color_1_<?php echo $group; ?> cart_decoration"
                                                    id="decor_color_1" <?php echo $disabled; ?>
                                                    style="border-color:<?php echo $color_code; ?>; opacity: <?php echo $op; ?>;">
                                                <option value="" selected>Select 1st Color</option>
                                                <?php foreach ($decoration_colors as $key => $color) { ?>
                                                    <option
                                                            data-color="<?php echo $color['color']; ?>"
                                                            value="<?php echo $color['label']; ?>"
                                                        <?php if($color_1==$color['label']){echo "selected"; }?>><?php echo $color['label']; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                        <div class="color_2">

                                            <select name="decor_color_2" data-group_id="<?php echo $group ?>" data-cat_id="<?php echo $term->term_id; ?>" class="decor_color_2_<?php echo $group; ?> cart_decoration" id="decor_color_2" <?php echo $disabled; ?> style="border-color:<?php echo $color_code; ?>; opacity: <?php echo $op; ?>;">

                                                <option value="" selected>Select 2nd Color</option>

                                                <?php foreach ($decoration_colors as $key => $color) { ?>

                                                    <option data-color="<?php echo $color['color']; ?>" value="<?php echo $color['label']; ?>" <?php if($color_2==$color['label']){echo "selected"; }?>><?php echo $color['label']; ?></option>
                                                <?php } ?>
                                            </select>


                                        </div>
                                    </div>

                                    <?php if($cat_id==35){ ?>
                                        <div class="decor-preview" id="decor_images_input_<?php echo $group?>" style="<?php echo $customize_topper_style; ?>">
                                            <div class="decor_image">
                                                <div class="special-deco-bx" style="min-height: auto">
                                                    <div data-kind="image_included" class="clonecake changeColor_1" id="cake_deco_base" style="background-color: <?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_1']; ?>; float: left; width: 50px;height: 50px;position: relative;">
                                                        <img src="/wp-content/uploads/2022/10/base.png" class="cakeimages" style="padding: 0; position: absolute; top: 0; left: 0; max-width: 100%" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
                                                        <div id="cake_deco_top" class="changeColor_1" style="background-color: <?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_1']; ?>; width: 35.0px;height: 35.0px;position: absolute;top: 8.0px;left: 8.0px;border-radius: 45.0px">
                                                            <img src="<?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_input_image_2']; //$decor_image_1;
                                                            ?>" class="cakeimages changeImage" style="width: 30.0px; height: 30.0px; position: absolute; top: 3.0px; left: 3.0px;" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--<img src="<?php // echo $_SESSION['cats']["wp_".$term->term_id]['decor_input_image_1']; //$decor_image_1;
                                            ?>" alt="">-->
                                            <div class="special-deco-bx" style="min-height: auto">
                                                <div data-kind="image_included" class="clonecake changeColor_2" id="cake_deco_base_2" style="background-color: <?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_2']; ?>; float: left; width: 50px;height: 50px;position: relative;">
                                                    <img src="/wp-content/uploads/2022/10/base.png" class="cakeimages" style="padding: 0; position: absolute; top: 0; left: 0; max-width: 100%" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
                                                    <div id="cake_deco_top_2" class="changeColor_2" style="background-color: <?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_2']; ?>; width: 35.0px;height: 35.0px;position: absolute;top: 8.0px;left: 8.0px;border-radius: 45.0px">
                                                        <img src="<?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_input_image_2']; ?>" class="cakeimages changeImage" style="width: 30.0px; height: 30.0px; position: absolute; top: 3.0px; left: 3.0px;" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
                                                    </div>
                                                </div>
                                            </div>

                                            <!--<img src="<?php //echo $_SESSION['cats']["wp_" . $term->term_id]['decor_input_image_2'];
                                            ?>" alt="">-->
                                        </div>
                                    <?php } else { ?>

                                        <?php if ($cat_qty >= 12) : ?>
                                            <div class="decor-preview" id="decor_images_input_<?php echo $group ?>" style="<?php echo $customize_topper_style; ?>">
                                                <div class="decor_image">
                                                    <div class="special-deco-bx" style="min-height: auto">
                                                        <div data-kind="image_included" class="clonecake changeColor_1" id="cake_deco_base" style="background-color: <?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_1']; ?>; float: left; width: 50px;height: 50px;position: relative;">
                                                            <img src="/wp-content/uploads/2022/10/base.png" class="cakeimages" style="padding: 0; position: absolute; top: 0; left: 0; max-width: 100%" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
                                                            <div id="cake_deco_top" class="changeColor_1" style="background-color: <?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_1']; ?>; width: 35.0px;height: 35.0px;position: absolute;top: 8.0px;left: 8.0px;border-radius: 45.0px">
                                                                <img src="<?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_input_image_2']; //$decor_image_1;
                                                                ?>" class="cakeimages changeImage" style="width: 30.0px; height: 30.0px; position: absolute; top: 3.0px; left: 3.0px;" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--<img src="<?php // echo $_SESSION['cats']["wp_".$term->term_id]['decor_input_image_1']; //$decor_image_1;
                                                ?>" alt="">-->
                                                <div class="special-deco-bx" style="min-height: auto">
                                                    <div data-kind="image_included" class="clonecake changeColor_2" id="cake_deco_base_2" style="background-color: <?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_2']; ?>; float: left; width: 50px;height: 50px;position: relative;">
                                                        <img src="/wp-content/uploads/2022/10/base.png" class="cakeimages" style="padding: 0; position: absolute; top: 0; left: 0; max-width: 100%" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
                                                        <div id="cake_deco_top_2" class="changeColor_2" style="background-color: <?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_color_2']; ?>; width: 35.0px;height: 35.0px;position: absolute;top: 8.0px;left: 8.0px;border-radius: 45.0px">
                                                            <img src="<?php echo $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_input_image_2']; ?>" class="cakeimages changeImage" style="width: 30.0px; height: 30.0px; position: absolute; top: 3.0px; left: 3.0px;" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--<img src="<?php //echo $_SESSION['cats']["wp_" . $term->term_id]['decor_input_image_2'];
                                                ?>" alt="">-->
                                            </div>
                                        <?php endif; ?>

                                    <?php } ?>



                                </div>

                                <div class="wrapper_customize_topper ">
                                    <?php
                                    if(isset($_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_type']) && $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_type']!=NULL){
                                        $customizer_topper_1 = 'checked="checked"';

                                    }else{
                                        $customizer_topper_1 = '';

                                    }
                                    // $retVal = (condition) ? a : b ;

                                    // $disabled = 'disabled';
                                    // $disabled = 'disabled';
                                    if( $cat_qty < 12 || $currentDate == $_SESSION['date_sec']) {
                                        $customizer_topper_1 = '';
                                        echo "<style>
										input[type=checkbox]:disabled + label:before {
											transform: scale(1);
											border-color: #e439456b;
											background: #e439456b;
										}
										</style>";

                                    }

                                    $qty_check = 1;

                                    $currentDate = date("Y-m-d");

                                    if($cat_qty<12 || $currentDate == $_SESSION['date_sec']){

                                        if($term->term_id != 35){
                                            $qty_check = 0;
                                        } else {
                                            if($currentDate == $_SESSION['date_sec'])
                                            $qty_check = 0;
                                            else
                                           { $qty_check = 1;
                                            if(isset($_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_type']) && $_SESSION[$group]['cats']["wp_" . $term->term_id]['decor_type']!=NULL)
                                            $customizer_topper_1 = 'checked="checked"';
                                        }

                                        }

                                    }

                                    //echo $qty_check;

                                    ?>

                                    <input <?php echo (!$qty_check) ? 'disabled' : 'enabled'; ?> type="checkbox" data-group_id="<?php echo $group ?>"  data-id="<?php echo $term->term_id ?>" class="customize_topper" id="customize_topper_<?php echo $packaging; ?>" name="customize_topper_<?php echo $packaging; ?>" value="Customize Topper" <?php echo $customizer_topper_1; ?> />
                                    <label style="color: <?php echo (!$qty_check) ? '#E43945' : '#55B7B3'; ?>" for="customize_topper_<?php echo $packaging; ?>">Customize Topper</label>


                                </div>

                            <?php
                            endif;
                            // End second foreach
                            // $packaging++;
                            ?> </div> <?php
                    }

                    echo '</div>';
                }

                $grouping_session = array_unique($grouping_session);
                $_SESSION['grouping_cart']=$grouping_session;

                $group_product_ids = array_unique($group_product_ids);
                $_SESSION['group_product_ids']=$group_product_ids;





                /////////////////////////////////////////////////
    //                echo '<pre>';
    // print_r( $cats_qty);
    // echo '</pre>';





    //             echo '<pre>';
    // print_r( $cats_qty);
    // echo '</pre>';

                    ?>

                    <?php do_action('woocommerce_cart_contents'); ?>

                    <div>
                        <div colspan="6" class="actions">

                            <?php if (wc_coupons_enabled()) { ?>
                                <div class="coupon">
                                    <label for="coupon_code"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_attr_e('Apply coupon', 'woocommerce'); ?></button>
                                    <?php do_action('woocommerce_cart_coupon'); ?>
                                </div>
                            <?php } ?>

                            <button style="visibility: hidden;" type="submit" class="button" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>

                            <?php do_action('woocommerce_cart_actions'); ?>

                            <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                        </div>
                    </div>

                    <?php do_action('woocommerce_after_cart_contents'); ?>
                </div>
            </div>
            <?php do_action('woocommerce_after_cart_table'); ?>
        </div>
    </form>
</div>
<?php
$_SESSION['packaging_price'] = $total_packaging;
?>
<?php do_action('woocommerce_before_cart_collaterals'); ?>

<div class="cart-collaterals">
    <?php
    /**
     * Cart collaterals hook.
     *
     * @hooked woocommerce_cross_sell_display
     * @hooked woocommerce_cart_totals - 10
     */
    do_action('woocommerce_cart_collaterals');
    ?>
</div>

<?php do_action('woocommerce_after_cart'); ?>
