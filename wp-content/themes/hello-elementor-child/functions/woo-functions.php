<?php 


// After setup theme hook adds WC support
function cupcake_add_woocommerce_support()
{
	add_theme_support('woocommerce'); // <<<< here
}
add_action('after_setup_theme', 'cupcake_add_woocommerce_support');

/////// Hide cart page title ////////
add_filter('woocommerce_show_page_title', 'cupcakes_hide_cat_page_title');
 
function cupcakes_hide_cat_page_title($title) {
   $title = false;
   return $title;
}


////// Remove WooCommerce breadcrumbs /////

add_action( 'init', 'my_remove_breadcrumbs' );
 
function my_remove_breadcrumbs() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}
////// Remove Shippiping calc on cart /////
function disable_shipping_calc_on_cart($show_shipping)
{
	if (is_cart()) {
		return false;
	}
	return $show_shipping;
}
add_filter('woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99);

remove_action( 'woocommerce_before_shop_loop' , 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_after_shop_loop' , 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );


// Add custom fields to order table

add_action('woocommerce_checkout_order_processed', 'add_custom_fields_to_order', 10, 1);

function add_custom_fields_to_order($order_id)
{
     $order_id;
	
	
	// echo "<pre>";
	// print_r($_SESSION['cats']);
	// echo "</pre>";

//	exit;
	
$group_product_ids = '';
		foreach($_SESSION['group_carts'] as $key => $product_group_ids){
			$group_product_ids .= $key . " : " .  $product_group_ids." | ";

		}

	update_post_meta( $order_id, '_order_location', $_SESSION['location'] ); 
	update_post_meta( $order_id, 'delivery_type_', $_SESSION['order_type'] ); 
	update_post_meta( $order_id, '_delivery_date', $_SESSION['date_sec'] ); 
	update_post_meta( $order_id, '_delivery_time', $_SESSION['sel_time'] ); 
	
	update_post_meta( $order_id, "group_product_ids", $group_product_ids);
	$decor_type = "";
	$decor_color_1 = "";
	$decor_color_2 = "";
	
	$decor_color_code_1 = "";
	$decor_color_code_2 = "";
	$decor_image = "";
	$packaging = "";
	$i = 1;
	$listGroupIDs = "";
	$group_packaging = "";
	$groupImages = "";
	$groupcolor_1 = "";
	$groupcolor_2 = "";
	foreach(  $_SESSION['grouping_cart'] as $key2 => $group){
                   
                   
                   
                    
	
	foreach($_SESSION[$group]['cats'] as $key=>$decor){
		
		$cat_id = str_replace("wp_", "", $key);
		
		$term_name = get_term( $cat_id )->name;
		
		$decor_type.=$term_name." : ".$decor['decor_type']." | ";
		
		$decor_color_1.=$term_name." : ".$decor['decor_color_input_1']." | ";
		
		$decor_color_2.=$term_name." : ".$decor['decor_color_input_2']." | ";
		
		$decor_image.=$term_name." : ".$decor['decor_input_image_2']." | ";
		
		$decor_color_code_1.=$term_name." : ".$decor['decor_color_1']." | ";
		$decor_color_code_2.=$term_name." : ".$decor['decor_color_2']." | ";
		
		$packaging.=$term_name." : ".$decor['packaging']." | ";

		// Groups items
		$listGroupIDs .= $group." : ". $decor['decor_type']." | ";
		$group_packaging .= $group." : ".$decor['packaging']." | ";
		$groupImages .=  $group." ** ". $decor['decor_input_image_2']." | ";
		$groupcolor_1 .=  $group." : ". $decor['decor_color_input_1']." | ";
		$groupcolor_2 .=  $group." : ". $decor['decor_color_input_2']." | ";
		$groupcolor_Code_1 .=  $group." : ". $decor['decor_color_1']." | ";
		$groupcolor_Code_2 .=  $group." : ". $decor['decor_color_2']." | ";

		

		
	}
	
	if( $_SESSION[$group]['cats']['wp_'.$cat_id]['customize_topper'] != '' || $_SESSION[$group]['cats']['wp_'.$cat_id]['packaging'] != NULL ){
		update_post_meta( $order_id, '_group_id', $listGroupIDs ); 
		update_post_meta( $order_id, '_decor_type', $decor_type ); 
		update_post_meta( $order_id, '_decor_color_1', $decor_color_1 ); 
		update_post_meta( $order_id, '_decor_color_2', $decor_color_2 );
		update_post_meta( $order_id, '_decor_image', $decor_image );
		update_post_meta( $order_id, 'decor_color_code_1', $decor_color_code_1 );
		update_post_meta( $order_id, 'decor_color_code_2', $decor_color_code_2 );
		update_post_meta( $order_id, '_group_packaging', $group_packaging );
		update_post_meta( $order_id, '_groupImages', $groupImages );
		update_post_meta( $order_id, '_groupcolor_1', $groupcolor_1 );
		update_post_meta( $order_id, '_groupcolor_2', $groupcolor_2 );	
		update_post_meta( $order_id, '_groupcolor_Code_1', $groupcolor_Code_1 );
		update_post_meta( $order_id, '_groupcolor_Code_2', $groupcolor_Code_2 );
		update_post_meta( $order_id, 'packaging', $packaging );
	}
	$i++;
}
	
	
	
	//update_post_meta( $order_id, '_decor_type', $_SESSION['decor_type'] ); 
	//update_post_meta( $order_id, '_decor_color_1', $_SESSION['decor_color_1'] ); 
	//update_post_meta( $order_id, '_decor_color_2', $_SESSION['decor_color_2'] ); 
	
	/*echo "<pre>";
	print_r($_SESSION['cats']);
	echo "</pre>";
	
	exit;*/
    unset($_SESSION['group_carts']);

	session_destroy();
	
}

//// Display Quantity to order summury 
add_action('woocommerce_cart_calculate_fees', function() {
	if (is_admin() && !defined('DOING_AJAX')) {
	return;
	}
	
	$total_packaging = 0;
	$cats_qty = array();
	
	//$WC_Cart_Fees = new WC_Cart_Fees();
	//$WC_Cart_Fees->remove_all_fees();
	
	$cat_wisw_pros = array();
	$uniqid = $_SESSION['group_cart'];
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
		$term = get_term($cat_id, 'product_cat');

		foreach ($cart_items as $one_item) {

			if (!isset($cats_qty[$cat_id]['qty'])) {
				$cats_qty[$cat_id]['qty'] = $one_item['quantity'];
			} else {
				$cats_qty[$cat_id]['qty'] += $one_item['quantity']; //print_r($cart_items); 
			}
		}



		foreach ($cart_items as $cart_item_key => $cart_item) {
			if (!array_key_exists($cart_item_key, $grouped_cart_items)) {
				
				
				
			}
			
		}
		
		$cat_qty = $cats_qty[$cat_id]['qty'];
		
		
		if(isset($_SESSION[$uniqid]['cats']['wp_'.$cat_id]['packaging'])){
			$boxing_status = $_SESSION[$uniqid]['cats']['wp_'.$cat_id]['packaging'];
			
			if($boxing_status=="individual_box"){
				if($cat_id==35){
					$boxing_price = (int)$cat_qty*12;
				} else {
					$boxing_price = (int)$cat_qty;
				}
				//$boxing = "checked='checked'";
				//$not_boxing = "";
			} else {
				$boxing_price = 0;
				//$boxing = "";
				//$not_boxing = "checked='checked'";
			}
			
		} else {
			$boxing_status = "";
			$boxing_price = 0;
			$boxing = "";
			$not_boxing = "checked='checked'";
		}
		
		// $total_packaging = $total_packaging + $boxing_price;
		
	}
	
	// $_SESSION['packaging_price'] = $total_packaging;
	
	global $woocommerce;
	$items = $woocommerce->cart->get_cart();
	
	$total_qty = 0;
	
	foreach ($cat_wisw_pros as $cat_id => $cart_items) {
		
		if(isset($_SESSION[$uniqid]['cats']['wp_'.$cat_id]['packaging'])&& $_SESSION[$uniqid]['cats']['wp_'.$cat_id]['packaging']=="individual_box"){
			foreach($items as $item => $values) { 
				//$_product =  wc_get_product( $values['data']->get_id()); 
				$total_qty += $values['quantity']; 
				//$price = get_post_meta($values['product_id'] , '_price', true);
				//echo "  Price: ".$price."<br>";
			}
		}
		
	}

	$total_packaging = 0;
	$group_carts = $_SESSION['group_carts'];

	foreach($group_carts as $prod_id => $group_id){

		foreach ($cat_wisw_pros as $cat_id => $cart_items) {

		if(isset($_SESSION[$group_id]['cats']['wp_'.$cat_id]['packaging'])){


			$boxing_status = $_SESSION[$group_id]['cats']['wp_'.$cat_id]['packaging'];
			
			if($boxing_status=="individual_box"){
				if($cat_id==35){
					$boxing_price = (int)$cat_qty*12;
				} else {
					$boxing_price = (int)$cat_qty;
				}
				$total_packaging = $total_packaging + $boxing_price;

				//$boxing = "checked='checked'";
				//$not_boxing = "";
			}  
			
		} 
	
	}


	}


	$cats_qty = array();

	$total_packaging = 0;
	$group_carts = $_SESSION['group_carts'];
 
	foreach($group_carts as $prod_id => $group_id){

		foreach ($cat_wisw_pros as $cat_id => $cart_items) {

	   
			foreach ($cart_items as $one_item) {

			   $product_id = $one_item['product_id'];
			   if( $prod_id == $product_id) {    
					  $cats_qty[$cat_id][$group_id][$product_id]['qty'] = $one_item['quantity'];
					  $cats_qty[$cat_id][$group_id][$product_id]['packaging'] = $_SESSION[$group_id]['cats']['wp_'.$cat_id]['packaging'];                      
					 }                        

			}
		 }
}


$sumQty = 0;

foreach ($cats_qty as $group) {
foreach ($group as $subgroup) {
foreach ($subgroup as $item) {
if ($item["packaging"] === "individual_box") {
	$sumQty += $item["qty"];
}
}
}
}



	
	WC()->cart->add_fee(__('Packaging Price', 'hello-elementor'), 	$sumQty );
	
	/*if(isset($_SESSION['cats']['wp_'.$cat_id]['packaging'])&& $_SESSION['cats']['wp_'.$cat_id]['packaging']=="individual_box"){
		
		global $woocommerce;
		$items = $woocommerce->cart->get_cart();
		
		$total_qty = 0;
		
		foreach($items as $item => $values) { 
			//$_product =  wc_get_product( $values['data']->get_id()); 
			$total_qty += $values['quantity']; 
			//$price = get_post_meta($values['product_id'] , '_price', true);
			//echo "  Price: ".$price."<br>";
		} 
		
		//WC()->cart->add_fee(__('Standard Boxing Price', 'hello-elementor'), 10);
		if(isset($_SESSION['packaging_price'])){
			WC()->cart->add_fee(__('Individual Boxing Price', 'hello-elementor'), $_SESSION['packaging_price']);
		}
		
	} else {
		//WC()->cart->add_fee(__('Individual Boxing Price', 'hello-elementor'), $total_qty);
		WC()->cart->add_fee(__('Standard Boxing Price', 'hello-elementor'), 0);
	}*/

}, 1);


// Custom function where metakeys / labels pairs are defined
function get_product_attribute_size_terms(){
    $taxonomy = 'store_location';
    $options  = array();
    
    foreach ( get_terms( array('taxonomy' => 'store_location', 'hide_empty' => false ) ) as $term ) {
        $options[$term->slug] = $term->name;
    }

    return $options;
}
    
// Add a dropdown to filter orders by variations size
add_action( 'restrict_manage_posts', 'display_admin_shop_order_by_meta_filter' );
function display_admin_shop_order_by_meta_filter(){
	
	global $pagenow, $typenow;
	
	if( 'shop_order' === $typenow && 'edit.php' === $pagenow ) {
		$domain    = 'woocommerce';
		$filter_id = 'by_location';
		$current   = isset($_GET[$filter_id])? $_GET[$filter_id] : '';
	
		echo '<select name="'.$filter_id.'">
		<option value="">' . __('Filter by Location…', $domain) . '</option>';
	
		$options = get_product_attribute_size_terms();
		foreach($options as $key => $option){
			?>
				<option value="<?php echo $key ?>" <?php if ($key == $current){ echo 'selected="selected"';} ?>><?php echo $option ?></option>
			<?php
		}

		echo '</select>';
	}
}

// Add a dropdown to filter orders by variations size
add_action( 'restrict_manage_posts', 'display_admin_shop_order_by_date_range_filter' );
function display_admin_shop_order_by_date_range_filter(){
	
	global $pagenow, $typenow;
	
	if( 'shop_order' === $typenow && 'edit.php' === $pagenow ) {
		$domain    = 'woocommerce';
		$filter_id_start = 'by_date_range_start';
		$filter_id_end = 'by_date_range_end';
		$current_start   = isset($_GET[$filter_id_start])? $_GET[$filter_id_start] : '';
		$current_end   = isset($_GET[$filter_id_end])? $_GET[$filter_id_end] : '';
		
		echo "From: <input type='date' name='".$filter_id_start."' value='".$current_start."'> To: <input type='date' name='".$filter_id_end."' value='".$current_end."'>";
		
	}
}

// Apply Location and Delivery Date Filters to the Orders Page

add_action( 'pre_get_posts', 'process_admin_shop_order_language_filter' );
function process_admin_shop_order_language_filter( $query ) {
    global $pagenow;

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	
	// Add Orders Query key _order_location
	
    if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['by_location'] ) && $_GET['by_location'] != '' && $_GET['post_type'] == 'shop_order' ) {

      $meta_key_query = array(
        array(
          'meta_key'     => '_order_location',
          'value'   => esc_attr( $_GET['by_location'] ),
        )
      );
      $query->set( 'meta_query', $meta_key_query );

    }
	
	// Add Orders Query key _delivery_date
	
	if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['by_date_range_start'] ) && $_GET['by_date_range_start'] != '' && $_GET['post_type'] == 'shop_order' ) {
	
		  $meta_key_query = array(
		  'relation' => 'AND',
			/*array(
			  'meta_key'     => '_delivery_date',
			  'value'   => esc_attr( $_GET['by_date_range_start'] ),
			)*/
			array(
                'key'       => '_delivery_date',
                'compare'   => '>=',
                'value'     => esc_attr( date("Ymd", strtotime($_GET['by_date_range_start']))), // 20120501 May (Ymd or UnixTime)
                'type'      => 'DATE',
            ),
			array(
                'key'       => '_delivery_date',
                'compare'   => '<=',
                'value'     => esc_attr(  date("Ymd", strtotime($_GET['by_date_range_end']))), // 20120701 July (Ymd or UnixTime)
                'type'      => 'DATE',
            )
		  );
		  
		  if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['by_location'] ) && $_GET['by_location'] != '' && $_GET['post_type'] == 'shop_order' ) {

			  $meta_key_query[] = array(
				array(
				  'meta_key'     => '_order_location',
				  'value'   => esc_attr( $_GET['by_location'] ),
				)
			  );
			  //$query->set( 'meta_query', $meta_key_query );
		
			}
		  
		  /*echo "<pre>";
		  print_r($meta_key_query);
		  echo "</pre>";*/
		  
		  $query->set( 'meta_query', $meta_key_query );
	
		}
}




/////////////////////////// Adding Columns to oder table (Admin Area) /////////////////////////

add_filter( 'manage_edit-shop_order_columns','adding_custom_columns_order_table');
function adding_custom_columns_order_table($columns)
{
    // adding custom cols to orders table
  $columns['customer_address'] = "Address";
	$columns['customer_phone'] = "Phone";
	$columns['order_type'] = "Order Type";
	$columns['completion_date'] = "Completion Date";
	$columns['completion_time'] = "Completion Time";
	$columns['quantity_'] = "Quantity";

    return $columns;    
}

add_action( 'manage_shop_order_posts_custom_column', 'cravecupcake_customer_address' );
function cravecupcake_customer_address($column) {
  global $post;
	
	$order_id = $post->ID;
    $order = wc_get_order( $order_id );
	
	
  $order_type = get_post_meta( $order->id, 'delivery_type_', true );
	$delivery_date = get_post_meta( $order->id, '_delivery_date', true );
	$delivery_time = get_post_meta( $order->id, '_delivery_time', true );
	
	$item_quantity = 0;
	
	$order_items = $order->get_items();
	// Iterating through each item in the order
	foreach ($order_items as $item_id => $item) {
		// Get the product name
		$product_name = $item['name'];
		// Get the item quantity
		$item_quantity += $order->get_item_meta($item_id, '_qty', true);
		
		//alog('$item_quantity', $item_quantity, __FILE__, __LINE__ );
		
	}

  if( $column == 'customer_address' ) {
   echo $order->shipping_address_1 . ', ' . 
        $order->shipping_address_2 . ' ' .
        $order->shipping_city      . ', ' .
        $order->shipping_state     . ' ' .
        $order->shipping_postcode;
	
  }
  if( $column == 'customer_phone' ) {
   echo $order->billing_phone;
  }
  if( $column == 'order_type' ) {
   echo $order_type;
  }
  if( $column == 'completion_date' ) {
   echo $delivery_date;
  }
  if( $column == 'completion_time' ) {
   echo $delivery_time;
  }
 if( $column == 'quantity_' ) {
   echo $item_quantity;
  }
}

// add_filter( 'woocommerce_available_payment_gateways', 'cravecupcakes_payment_disable_for_employee' );
  
// function cravecupcakes_payment_disable_for_employee( $available_gateways ) {
//   if(wc_current_user_has_role( role )('employee')){

//   }
//    if ( isset( $available_gateways['stripe'] ) && ! current_user_can( 'manage_woocommerce' ) ) {
//       unset( $available_gateways['stripe'] );
//    } 
//    return $available_gateways;
// }


// Remove shipping on pickup

add_action('wp_head', 'remove_shipping_on_checkout_cb');

function remove_shipping_on_checkout_cb(){
	
	if(isset($_SESSION['order_type'])){
		if ($_SESSION['order_type'] == 'pickup') {
			?>
			<style>
				tr.woocommerce-shipping-totals.shipping {
					display: none;
				}
	
				h3#ship-to-different-address {
					display: none;
				}
			</style>
			<?php
		}
	}
}




/*add_action('admin_menu','cupcake_admin_menu_bundle_product_callback');

function cupcake_admin_menu_bundle_product_callback(){
	add_menu_page('Bundle Products Function', 'Bundle Products Function', 'administrator', 'bundle-product-function', 'wct_bundle_product_function_callback');
}*/

function wct_bundle_product_function_callback(){
	
	// Delete a Specific Schedule if exist

	/*if(wp_next_scheduled( 'wct_cron_hook_5_minutes' )){
		//wp_clear_scheduled_hook('wct_cron_hook_5_minutes');
	}*/
	
	
	$cat_id = get_term_by('name', 'woosb', 'product_type');
	
	$cat_ids = array($cat_id->term_id);
		
	//$term_taxonomy_ids = wp_set_object_terms( $product_id, $cat_ids, 'product_type', true );
	
	/*echo "<pre>";
	print_r($cat_id);
	echo "</pre>";*/
	
	 $args = array(
		'post_type'             => 'product',
		'post_status'			=> array('draft', 'publish'),
		'ignore_sticky_posts'   => 1,
		'posts_per_page'        => '120',
		'tax_query'             => array(
			array(
				'taxonomy'      => 'product_type',
				'field' => 'term_id', //This is optional, as it defaults to 'term_id'
				'terms'         => $cat_ids,
				'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
			),
		)
	);
	
	//$products = get_posts($args);
	
	$products = new WP_Query($args);
	
	//var_dump($products);
	
	//alog('products', $products,__FILE__,__LINE__);
	
	if($products->have_posts()){
		
		while($products->have_posts()){
			
			$products->the_post();
			
			$product_id = get_the_ID();
			
			update_post_meta($product_id, "_manage_stock", "yes");
			update_post_meta($product_id, "woosb_manage_stock", "on");
			
			//echo get_the_ID()."<br>";
			$one_post = get_post($product_id);
			$custom = get_post_custom($product_id);
			
			/*echo "<pre>";
			print_r($one_post);
			print_r($custom);
			echo "</pre>";*/
			
			$visibility_date = $custom['visibility_start_date'][0];
			$availability_start_date = $custom['availability_start_date'][0];
			$availability_end_date = $custom['availability_end_date'][0];
			
			//echo $visibility_date."<br>";
			//echo $availability_start_date."<br>";
			//echo $availability_end_date."<br>";	
			
			//alog('Visibility: Availability_start: Availablity End',$visibility_date." : ". $availability_start_date." : ".$availability_end_date,__FILE__,__LINE__); 
			
			$cur_date = time();
			
			$vis_date = strtotime($visibility_date);
			
			$avl_date = strtotime($availability_start_date);
			$end_date = strtotime($availability_end_date);
			
			//echo "Current Date: ".$cur_date."<br>"."Visibility Date: ".$vis_date."<br>";
			
			//echo date("d-m-Y H:i A", $cur_date)."<br>".date("d-m-Y H:i A", $vis_date)."<br>";
			
			
			//alog('product_id', $product_id,__FILE__,__LINE__);
			
			if($cur_date>=$vis_date){
				//alog('Current Date: Visibility Date in if', $cur_date." : ". $vis_date,__FILE__,__LINE__); 
				$post_updated = wp_update_post(array(
					'ID'    =>  $product_id,
					'post_status'   =>  'publish',
					'post_type'    =>  'product',
				));
				
				//alog('post_updated', $post_updated,__FILE__,__LINE__); 
				//alog('product_id', $product_id,__FILE__,__LINE__); 
				
				if($cur_date >= $avl_date && $cur_date < $end_date){
					update_post_meta($product_id, "_stock", 50000);
					update_post_meta($product_id, "_stock_status", "instock");
				} elseif($cur_date > $end_date){
					update_post_meta($product_id, "_stock", 0);
					update_post_meta($product_id, "_stock_status", "outofstock");
					$post_updated = wp_update_post(array(
						'ID'    =>  $product_id,
						'post_status'   =>  'draft',
						'post_type'    =>  'product',
					));
				} else {
					update_post_meta($product_id, "_stock", 0);
					update_post_meta($product_id, "_stock_status", "outofstock");
				}
				
				//echo "Change Visibility to Publish from Draft <br />";
				//echo "Passed <br />";
				
			} else {
				
				wp_update_post(array(
					'ID'    =>  $product_id,
					'post_status'   =>  'draft'
				));
				update_post_meta($product_id, "_stock", 0);
				update_post_meta($product_id, "_stock_status", "outofstock");
				//echo "Not Passed<br>";
				
			}
			//echo "<br><br>";
			//echo "<img src='".get_the_post_thumbnail_url(get_the_ID())."' />";
		}
		
		wp_reset_query();
	}
	
}

// Add Custom Schedule Intervals
function wct_custom_cron_schedule( $schedules ) {
    $schedules['every_six_hours'] = array(
        'interval' => 21600, // Every 6 hours
        'display'  => __( 'Every 6 hours' ),
    );
	$schedules['every_five_minutes'] = array(
        'interval' => 300, // Every 5 minutes
        'display'  => __( 'Every 5 minutes' ),
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'wct_custom_cron_schedule' );



//Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'wct_cron_hook' ) ) {
    wp_schedule_event( time(), 'hourly', 'wct_cron_hook' );
}

//Hook into that action that'll fire every six hours
add_action( 'wct_cron_hook', 'wct_bundle_product_function_callback' );

/*if ( ! wp_next_scheduled( 'wct_cron_hook_5_minutes' ) ) {
	wp_schedule_event( time(), 'every_five_minutes', 'wct_cron_hook_5_minutes' );
}*/

//Hook into that action that'll fire 5 Minutes
//add_action( 'wct_cron_hook', 'wct_bundle_product_function_callback' );


/*// Delete a Specific Schedule if exist

delete_action('wct_cron_hook', 'wct_cron_job_delete');
// clean the scheduler
function wct_cron_job_delete()
{
wp_clear_scheduled_hook('wct_cron_hook');
}*/



 
