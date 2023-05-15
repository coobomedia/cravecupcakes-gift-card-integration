<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */


$order__id        = wc_get_order( $order->get_id() );
$shipping_items   = $order__id->get_items( 'shipping' );
$shipping_methods = WC()->shipping->get_shipping_methods();
$method_id        = 'roadie_';
$data             = array();
foreach ( $shipping_items as $shipping_item ) {
	$shipping_method_id = $shipping_item->get_method_id(); // Get the shipping method ID
	if ( $shipping_method_id === $method_id ) {
		$shipping_instance = $shipping_methods[ $method_id ];
		$data['token']     = $shipping_instance->token;
		$data['end_point'] = $shipping_instance->end_point;

	}

}


?>
    <div class="woocommerce-order">
		<?php
		if ( $order ) :
			do_action( 'woocommerce_before_thankyou', $order->get_id() );
			?>
			<?php if ( $order->has_status( 'failed' ) ) : ?>
            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>
            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"
                   class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
                       class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
            </p>
		<?php else : ?>
			<?php
			global $wp;
			if ( isset( $wp->query_vars['order-received'] ) ) {
				$order_id        = absint( $wp->query_vars['order-received'] ); // The order ID
				$order           = wc_get_order( $order_id );
				$currency_symbol = get_woocommerce_currency_symbol( $order->get_currency() );
			}
			$ordergroupID           = get_post_meta( $order_id, '_group_id', true );
			$group_product_ids      = get_post_meta( $order_id, 'group_product_ids', true );
			$check_result_proudct   = array();
			$partsgroup_product_ids = explode( "|", $group_product_ids );

			// Loop through the parts array
			foreach ( $partsgroup_product_ids as $partgroup_product_ids ) {
				// Split each part by ":" to get key-value pairs
				$keyValuegroup_product_ids = explode( ":", $partgroup_product_ids );

				// Extract the key and value, and trim any whitespaces
				$keygroup_product_ids   = trim( $keyValuegroup_product_ids[0] );
				$valuegroup_product_ids = trim( $keyValuegroup_product_ids[1] );

				// Store the key-value pair in the result array
				// $result[$keygroup_product_ids] = $valuegroup_product_ids;
				$check_result_proudct[ $order_id ][ $keygroup_product_ids ] = $valuegroup_product_ids;
			}

			// Split the string by " " (space) to get an array of substrings
			$parts = explode( "|", $ordergroupID );

			// Initialize an empty array to store the extracted values
			$result = array();
			$result = return_groups_parts( $ordergroupID, ":" );


			// grouping
			$group_packaging   = get_post_meta( $order_id, '_group_packaging', true );
			$groupImages       = get_post_meta( $order_id, '_groupImages', true );
			$groupcolor_1      = get_post_meta( $order_id, '_groupcolor_1', true );
			$groupcolor_2      = get_post_meta( $order_id, '_groupcolor_2', true );
			$groupcolor_Code_1 = get_post_meta( $order_id, '_groupcolor_Code_1', true );
			$groupcolor_Code_2 = get_post_meta( $order_id, '_groupcolor_Code_2', true );


//  group for packaging;
			$group_packaging_Array = array();
			$group_packaging_Array = return_groups_parts( $group_packaging, ":" );

// group for GroupImage

// Initialize an empty array to store the extracted values
			$groupImages_Array = array();
			$groupImages_Array = return_groups_parts( $groupImages, "**" );

// group for Color 2

			// Initialize an empty array to store the extracted values
			$groupcolor_2_Array = array();
			$groupcolor_2_Array = return_groups_parts( $groupcolor_2, ":" );

// group for Color 2

			// Initialize an empty array to store the extracted values
			$groupcolor_1_Array = array();
			$groupcolor_1_Array = return_groups_parts( $groupcolor_1, ":" );


// group for Color code 1

			// Initialize an empty array to store the extracted values
			$groupcolor_Code_1_Array = array();
			$groupcolor_Code_1_Array = return_groups_parts( $groupcolor_Code_1, ":" );


// group for Color code 2

			// Initialize an empty array to store the extracted values
			$groupcolor_Code_2_Array = array();
			$groupcolor_Code_2_Array = return_groups_parts( $groupcolor_Code_2, ":" );


			$location      = get_post_meta( $order_id, '_order_location', true );
			$location      = str_replace( '_', ' ', $location );
			$decor_type    = get_post_meta( $order_id, '_decor_type', true );
			$Packaging     = get_post_meta( $order_id, 'packaging', true );
			$decor_type    = explode( '|', $decor_type );
			$decor_color   = get_post_meta( $order_id, '_decor_color_1', true );
			$decor_color2  = get_post_meta( $order_id, '_decor_color_2', true );
			$decor_color   = explode( '|', $decor_color );
			$decor_color2  = explode( '|', $decor_color2 );
			$order_detail  = array(
				'Order Type:'   => get_post_meta( $order_id, 'delivery_type_', true ),
				'Location:'     => $location,
				'Date:'         => get_post_meta( $order_id, '_delivery_date', true ),
				'Time:'         => get_post_meta( $order_id, '_delivery_time', true ),
				'Order Number:' => $order_id,
				'Name:'         => $order->get_billing_first_name() . " " . $order->get_billing_last_name(),
				'Email:'        => $order->get_billing_email(),
				"Number:"       => $order->get_billing_phone(),
			);
			$order_payment = array(
				'Sub Total:'      => $order->get_subtotal_to_display(),
				'Packaging Fee:'  => wc_price( $order->get_total_fees() ),
				'Payment Method:' => $order->get_payment_method_title(),
				'Shipping Cost:'  => wc_price( $order->get_shipping_total() ),
				'Total:'          => $currency_symbol . $order->get_total()
			);
			$all_items     = array();

			$order_items = $order->get_items();

			foreach ( $order_items as $order_item ) {

				$prod_id = $order_item->get_product_id();

				$groupId      = $check_result_proudct[ $order_id ][ $prod_id ];
				$decore_types = $result[ $groupId ];

				$terms           = get_the_terms( $prod_id, 'product_cat' );
				$cat_id          = $terms[0]->term_id;
				$categorie_title = $terms[0]->name;
				$product         = $order_item->get_product();

				$name  = $order_item->get_name();
				$qty   = $order_item->get_quantity();
				$total = $order_item->get_total();

				$all_items[ $groupId ][ $categorie_title ][ $prod_id ] = array(
					'categoryName'     => $categorie_title,
					'Product_ID'       => $prod_id,
					'Image_ID'         => $product->get_image_id(),
					"Product_Feature"  => wp_get_attachment_url( $product->get_image_id() ),
					'Product_Name'     => $order_item->get_name(),
					'Product_Quantity' => $order_item->get_quantity(),
					"Sub_Total"        => $order_item->get_subtotal(),
					"All_Total"        => $order_item->get_total(),
					"Product_Type"     => $product_type,
					"Category"         => $categorieID,
					"decoration_type"  => $decore_types,
					"decoration_image" => $images[ $categorie_title ],
					"color_1"          => $colors1[ $categorie_title ],
					"color_2"          => $colors2[ $categorie_title ],
					"packaging"        => $packaging2[ $categorie_title ],
					'color_title_2'    => $color1Title2[ $categorie_title ],
					'color_title_1'    => $color1Title1[ $categorie_title ],
				);

			}

			$billing  = $order->get_formatted_billing_address();
			$shipping = $order->get_formatted_shipping_address();
			?>
            <section class="thankyou-template">
                <div class="thankyou-detail">
                    <h2>Thank you. Your order has been received</h2>
                    <div class="divider"></div>
                    <div class="order-information">
                        <h3>Order Information</h3>
                        <div class="divider row-divider"></div>
                        <div class="order-information-detail">
							<?php foreach ( $order_detail as $key => $value ): ?>
                                <p><span><b><?php echo $key; ?> </b></span> <span><?php echo $value; ?></span></p>
							<?php endforeach; ?>
                        </div>
                    </div>
                    <div class="divider"></div>
					<?php foreach ( $all_items as $key0 => $items ):
						foreach ( $items as $key => $item ) {


							?>


                            <div class="mini-cupcake <?php echo ( $key == 'Mini Cupcakes' ) ? '' : 'original-cupcake' ?>">
								<?php if ( $key == 'Mini Cupcakes' ): ?>
                                    <h3><?php echo $key; ?></h3>
								<?php endif; ?>
                                <div class="mini-cup-cakes-detail">
                                    <table>
                                        <thead>
                                        <tr>
                                            <th>
												<?php if ( $key == 'Mini Cupcakes' ): ?>
                                                    For Those cupcakes 1 Qty = 12 (1 Dzn)
												<?php else: ?>
                                                    <h3><?php echo $key ?></h3>
												<?php endif; ?>

                                            </th>
                                            <th>
                                                Quantity
                                            </th>
                                            <th>
                                                Amount
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
										<?php foreach ( $item as $sub_item ): ?>
											<?php $totalCount += $sub_item['Product_Quantity']; ?>
                                            <tr>
                                                <td>
                                                    <div class="item-detail">
                                                        <img src="<?php echo $sub_item['Product_Feature'] ?>"
                                                             alt="">
                                                        <span>  <?php echo $sub_item['Product_Name']; ?></span>
                                                    </div>
                                                </td>
                                                <td>
													<?php echo $sub_item['Product_Quantity']; ?>
                                                </td>
                                                <td>
													<?php echo wc_price( $sub_item['All_Total'] ); ?>
                                                </td>
                                            </tr>
										<?php

										endforeach; ?>
                                        </tbody>
                                    </table>
                                    <div class="packaging-detail">
										<?php if ( isset( $group_packaging_Array[ $key0 ] ) && $group_packaging_Array[ $key0 ] != '' ) {


											?>
                                            <p><span><b>Packaging:</b></span>
                                                <span><?php echo str_replace( '_', ' ', $group_packaging_Array[ $key0 ] ); ?></span>
                                            </p>

											<?php
										}

										if ( $result[ $key0 ] != ' ' ): ?>
                                            <p><span><b>Decoration:</b></span>
                                                <span><?php echo $result[ $key0 ] ?> - <?php echo $groupcolor_1_Array[ $key0 ] ?> / <?php echo $groupcolor_2_Array[ $key0 ] ?></span>
                                            </p>
										<?php else: ?>
                                            <p><span><b>Decoration:</b></span>
                                                <span>Standard Topper</span>
                                            </p>
										<?php endif; ?>
                                    </div>
									<?php if ( $result[ $key0 ] != ' ' ): ?>
										<?php if ( $result[ $key0 ] ): ?>
                                            <div class="packaging-items">
                                                <div class="packaging-item">
                                                    <div class="item"
                                                         style="background-color:<?php echo $groupcolor_Code_1_Array[ $key0 ] ?>;">
                                                        <img src="/wp-content/uploads/2022/10/base.png" alt=""
                                                             style="position: absolute;left: 0;right: 0;">
                                                        <div class="topper-image"
                                                             style="width: 35px; height: 35px; position: absolute;top: 8px; left: 8px; border-radius: 45px;">
                                                            <img src="<?php echo $groupImages_Array[ $key0 ] ?>"
                                                                 alt=""
                                                                 style="width: 30px;height: 30px;position: absolute;top: 3px;left: 3px;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="packaging-item">
                                                    <div class="item"
                                                         style="background-color:<?php echo $groupcolor_Code_2_Array[ $key0 ] ?>;">
                                                        <img src="/wp-content/uploads/2022/10/base.png"
                                                             alt=""
                                                             style="position: absolute;left: 0;right: 0;">
                                                        <div class="topper-image"
                                                             style="width: 35px; height: 35px; position: absolute;top: 8px; left: 8px; border-radius: 45px;">
                                                            <img src="<?php echo $groupImages_Array[ $key0 ] ?>"
                                                                 alt=""
                                                                 style="width: 30px;height: 30px;position: absolute;top: 3px;left: 3px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

										<?php endif; ?>
									<?php endif; ?>

                                    <div class="divider"></div>
                                </div>
                            </div>
							<?php
						}
					endforeach; ?>

                    <div class="total-amount">
						<?php foreach ( $order_payment as $key => $value ): ?>
							<?php if ( $value != '' ): ?>
                                <p><span><b><?php echo $key ?></b></span> <span><?php echo $value ?></span></p>
							<?php endif; ?>
						<?php endforeach; ?>
                    </div>

                    <div class="billing-info">
                        <h3>Billing Information</h3>
                        <p>
							<?php echo $billing; ?>
                        </p>
                        <p><?php echo $order->get_billing_email() ?></p>
                        <p><?php echo $order->get_billing_phone() ?></p>
                    </div>
                    <div class="ship-info">
                        <h3>Shipping Information</h3>
                        <p>
							<?php echo $shipping; ?>
                        </p>
                    </div>
                </div>
            </section>
		<?php endif; ?>
		<?php endif; ?>
    </div>


<?php

function return_groups_parts( $string, $opt ) {


	// Split the string by " " (space) to get an array of substrings
	$string_parts = explode( "|", $string );

	// Initialize an empty array to store the extracted values
	$string_parts_Array = array();

	// Loop through the parts array
	foreach ( $string_parts as $part ) {
		// Split each part by ":" to get key-value pairs
		$keyValue = explode( $opt, $part );

		// Extract the key and value, and trim any whitespaces
		$key   = trim( $keyValue[0] );
		$value = trim( $keyValue[1] );

		// Store the key-value pair in the result array
		$string_parts_Array[ $key ] = $value;

	}

	return $string_parts_Array;


}