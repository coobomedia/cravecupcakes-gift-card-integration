<?php

final class Ut_Woo_Taxonomies_Coupons {

	/**
	 * Ut_Woo_Taxonomies_Coupons constructor.
	 */
	public function __construct() {

		add_action( 'woocommerce_coupon_options_usage_restriction', [ $this, 'action_woocommerce_coupon_options_usage_restriction' ], 10, 2 );
		add_action( 'woocommerce_coupon_options_save', [ $this, 'action_woocommerce_coupon_options_save' ], 10, 2 );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'filter_tax_coupon_is_valid' ], 10, 3 ); 
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'filter_excluded_coupon_is_valid' ], 10, 3 ); 
	}

	// Add new field - usage restriction tab
	public function action_woocommerce_coupon_options_usage_restriction( $coupon_get_id, $coupon ) {

		$all_terms = [];
		$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );	
		$saved_taxonomies = get_post_meta( $coupon_get_id, 'ut_product_taxonomies', true );
		$saved_exclude_taxonomies = get_post_meta( $coupon_get_id, 'ut_exclude_product_taxonomies', true );	
		foreach ( $taxonomy_objects as $taxonomy_object ) {	

			if ( $taxonomy_object->name == 'product_cat' ) {
				continue;
			}
			$terms = get_terms( [
				'taxonomy' => $taxonomy_object->name,
				'hide_empty' => false,
			] );
			$all_terms[ $taxonomy_object->label ] = $terms;
		}	
	    ?>
	    	<p class="form-field">
				<label for="product_taxonomies"><?php _e( 'Product taxonomies', 'woocommerce' ); ?></label>
				<select id="product_taxonomies" name="ut_product_taxonomies[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any taxonomy', 'woocommerce' ); ?>">
					<?php
					foreach ( (array)$all_terms as $label => $terms ) { 

                        if ( ! empty( $terms ) ) {
                    		echo '<optgroup label="' . esc_attr( $label ) . ':">';
                            foreach ( (array)$terms as $term ) { 
                                echo '<option value="' . esc_attr( $term->term_id ) . '"' . wc_selected( $term->term_id, $saved_taxonomies ) . '>' . esc_html( $term->name ) . '</option>';

						    } 
                            echo '</optgroup>';
                    	}
                	}
					?>
				</select> <?php echo wc_help_tip( __( 'Product taxonomies that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?>
			</p>

			<?php // Exclude Taxonomies. ?>
			<p class="form-field">
				<label for="exclude_product_taxonomies"><?php _e( 'Exclude taxonomies', 'woocommerce' ); ?></label>
				<select id="exclude_product_taxonomies" name="ut_exclude_product_taxonomies[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No taxonomies', 'woocommerce' ); ?>">
					<?php
					foreach ( (array)$all_terms as $label => $terms ) { 

                        if ( ! empty( $terms ) ) {
                    		echo '<optgroup label="' . esc_attr( $label ) . ':">';
                            foreach ( (array)$terms as $term ) { 
                                echo '<option value="' . esc_attr( $term->term_id ) . '"' . wc_selected( $term->term_id, $saved_exclude_taxonomies ) . '>' . esc_html( $term->name ) . '</option>';

						    } 
                            echo '</optgroup>';
                    	}
                	}
					?>
				</select>
				<?php echo wc_help_tip( __( 'Product taxonomies that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?>
			</p>
	    <?php
	}

	public function action_woocommerce_coupon_options_save( $post_id, $coupon ) {

	    update_post_meta( $post_id, 'ut_product_taxonomies', $_POST['ut_product_taxonomies'] );
	    update_post_meta( $post_id, 'ut_exclude_product_taxonomies', $_POST['ut_exclude_product_taxonomies'] );
	}

	public function coupon_get_product_taxonomies( $coupon_id ) {

		$result = get_post_meta( $coupon_id, 'ut_product_taxonomies', true ); 

		return $result;
	}

	public function coupon_get_excluded_product_taxonomies( $coupon_id ) {

		$result = get_post_meta( $coupon_id, 'ut_exclude_product_taxonomies', true ); 

		return $result;
	}

	/**
	 * Ensure coupon is valid for product taxonomies in the list is valid or throw exception.
	 *
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	public function validate_coupon_product_taxonomies( $coupon ) {
		
		$coupon_taxonomies = $this->coupon_get_product_taxonomies( $coupon->get_id() );	

		if ( ! empty( $coupon_taxonomies ) ) {	

			$valid = false;
			foreach ( WC()->cart->get_cart() as $item ) {
				$product = wc_get_product( $item['product_id'] );

				if ( $coupon->get_exclude_sale_items() && $product && $product->is_on_sale() ) {
					continue;
				}

				$product_tax_objects = get_object_taxonomies( 'product', 'objects' );	

				if ( ! is_array( $product_tax_objects ) ) {
					continue;
				}

				$all_product_terms = [];
				foreach ( $product_tax_objects as $product_tax_object ) {
					$product_terms = get_the_terms( $product->get_id(), $product_tax_object->name );

					if ( is_array( $product_terms ) && count( $product_terms ) > 0 ) {	
						foreach ( $product_terms as $product_term ) {
							array_push( $all_product_terms, $product_term->term_id );
						}
					}
				}	

				// If we find an item with a cat in our allowed cat list, the coupon is valid.
				if ( count( array_intersect( $all_product_terms, $coupon_taxonomies ) ) > 0 ) {
					$valid = true;
					break;
				}
			}

			if ( ! $valid ) {
				throw new Exception( __( 'Sorry, this coupon is not applicable to selected products.', 'woocommerce' ), 109 );
			}
		}

		return true;
	}


	/**
	 * Exclude taxonomies from product list.
	 *
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	public function validate_coupon_excluded_product_taxonomies( $coupon ) {

		$coupon_exc_taxonomies = $this->coupon_get_excluded_product_taxonomies( $coupon->get_id() );

		if ( ! empty( $coupon_exc_taxonomies ) ) {

			$taxonomies = array();
			foreach ( WC()->cart->get_cart() as $item ) {
				$product = wc_get_product( $item['product_id'] );

				if ( ! $product ) {
					continue;
				}

				$product_tax_objects = get_object_taxonomies( 'product', 'objects' );	

				if ( ! is_array( $product_tax_objects ) ) {
					continue;
				}

				$all_product_terms = [];
				$all_product_taxonomies = [];
				foreach ( $product_tax_objects as $product_tax_object ) {
					array_push( $all_product_taxonomies, $product_term->taxonomy );
					$product_terms = get_the_terms( $product->get_id(), $product_tax_object->name );

					if ( is_array( $product_terms ) && count( $product_terms ) > 0 ) {	
						foreach ( $product_terms as $product_term ) {
							array_push( $all_product_terms, $product_term->term_id );
						}
					}
				}	

				$all_product_taxonomies = array_filter( $all_product_taxonomies );
				$all_product_taxonomies = array_unique( $all_product_taxonomies );
				$tax_id_list = array_intersect( $all_product_terms, $coupon_exc_taxonomies );

				if ( count( $tax_id_list ) > 0 ) {
					foreach ( $tax_id_list as $term_id ) {
						foreach ( $all_product_taxonomies as $all_product_taxonomy ) {
							$term = get_term( $term_id, $all_product_taxonomy );
							$taxonomies[] = $term->name;
						}
					}
				}
			}

			if ( ! empty( $taxonomies ) ) {
				/* translators: %s: taxonomies list */
				throw new Exception( sprintf( __( 'Sorry, this coupon is not applicable to the taxonomies: %s.', 'woocommerce' ), implode( ', ', array_unique( $taxonomies ) ) ), 114 );
			}
		}

		return true;
	}

	public function filter_tax_coupon_is_valid( $valid, $coupon, $that ) {	

		$valid = $this->validate_coupon_product_taxonomies( $coupon );	
		return $valid;
	}

	public function filter_excluded_coupon_is_valid( $valid, $coupon, $that ) {
		
		$valid = $this->validate_coupon_excluded_product_taxonomies( $coupon );
		return $valid;
	}

}