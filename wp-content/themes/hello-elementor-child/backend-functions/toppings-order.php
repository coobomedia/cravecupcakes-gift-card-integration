<?php

add_submenu_page('sales-report', 'Toppings by Order', 'Toppings by Order', 'manage_options', 'toppings-order-sheet', 'wct_toppings_orders_page');

function wct_toppings_orders_page()
{

	$args = array(
		'type'                     => 'product',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => 0,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'store_location',
		'pad_counts'               => false

	);

	$locations = get_categories($args);

	$args = array(
		'type'                     => 'product',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => true,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'product_cat',
		'pad_counts'               => false

	);

	$product_cats = get_categories($args);

	$today = date("Y-m-d", time());
	$tomorrow = date("Y-m-d", strtotime(date("Y-m-d", time()) . " +1 days"));

	$from_date = $today;
	$to_date = $tomorrow;


?>
	<h1>Topping By Order Sheet</h1>

	From
	<input type="date" id="from_date" value="<?php echo $today; ?>" />
	To
	<input type="date" id="to_date" value="<?php echo $tomorrow; ?>" />

	Store:
	<select name="location" id="location">
		<option value="">All Locations</option>
		<?php
		foreach ($locations as $location) { ?>
			<option value="<?php echo $location->slug; ?>"><?php echo $location->name; ?></option>
		<?php } ?>
	</select>

	<!--Product Type:
    <select name="product_type" id="product_type" >
        <option value="">All</option>
        <?php
		foreach ($product_cats as $product_cat) { ?>
        <option value="<?php echo $product_cat->term_id; ?>" ><?php echo $product_cat->name; ?></option>
        <?php } ?>
    </select>-->

	<button id="generate_toppings_orders" class="crave-table-btn">Generate</button>

	<div id="result">

		<?php

		$location = "";
		$category = "";
		$category_name = "All Locations";
		//echo $from_date.": ".$to_date;

		global $wpdb;

		//echo $from_date."<br>";

		$from_date = date("Y-m-d", strtotime($from_date) + (3600 * 24));

		//echo $from_date."<br>";

		//echo $to_date."<br>";

		$to_date = date("Y-m-d", strtotime($to_date) + (3600 * 24));

		//echo $to_date."<br>";



		$orders = get_orders_by_date_range($from_date, $to_date, $location);

		$new_products = array();

		foreach ($orders as $key => $order) {

			$order_id = $order->ID;



			$order_details = wc_get_order($order_id);
			$order_items = $order_details->get_items();

			$order_customs = get_post_custom($order_id);

			$order_time = $order_customs['_delivery_time'][0];

			foreach ($order_items as $order_item) {
				$prod_id = $order_item->get_product_id();
				$name = $order_item->get_name();
				//$item = $order_item->data();
				$qty = $order_item->get_quantity();
				$total = $order_item->get_total();
				
				$terms = get_the_terms ( $prod_id, 'product_cat' );
				
				$cat_id = $terms[0]->term_id;
				$cat_name = $terms[0]->name;

				$old_qty = $new_products[$name][$order_time]['quantity'];

				$new_products[$name][$order_time]['product_id'] = $prod_id;
				$new_products[$name][$order_time]['product_name'] = $name;
				
				$new_products[$name][$order_time]['cat_id'] = $cat_id;
				$new_products[$name][$order_time]['cat_name'] = $cat_name;
				
				if($cat_id==35){
					$new_products[$name][$order_time]['quantity'] = $old_qty + $qty*12;
				} else {
					$new_products[$name][$order_time]['quantity'] = $old_qty + $qty;
				}
				
				$new_products[$name][$order_time]['total'] = $new_products[$name][$order_time]['total'] + $total;
			}
		}

		$cat_array = array();

		foreach ($new_products as $new_product) {

			$prod_id = $new_product['product_id'];

			$terms = get_the_terms($prod_id, 'product_cat');

			$term_id = $terms[0]->term_id;
			$term_slug = $terms[0]->name;

			$old_qty = $cat_array[$term_slug]['quantity'];
			$old_total = $cat_array[$term_slug]['total'];
			if ($term_id == $category) {
				$cat_array[$term_slug]['quantity'] = $old_qty + $new_product['quantity'];
				$cat_array[$term_slug]['total'] = $old_total + $new_product['total'];
			} elseif ($category == NULL) {
				$cat_array[$term_slug]['quantity'] = $old_qty + $new_product['quantity'];
				$cat_array[$term_slug]['total'] = $old_total + $new_product['total'];
			}
		}

		$time_array = array();

		foreach ($new_products as $key => $new_product) {

			foreach ($new_product as $key2 => $one_time) {

				$time_array[$key2] = $one_time['quantity'];
			}
		}

		ksort($time_array);

		// Totals Separated by Time
		$total_quantity = 0;
		$total_amount = 0;

		$total_array = array();

		// Totals Separated by Product
		$total_quantity_product = 0;
		$total_amount_product = 0;

		$total_array_product = array();

		?>

		<br class="clear" />

		<!-- <div id="print_table">

			<h2><?php esc_attr_e('Toppings Orders for ' . $category_name . ' Pre-orders: ' . date("D M d, Y", strtotime($from_date)) . ' to ' . date("D M d, Y", strtotime($to_date)), 'WpAdminStyle'); ?></h2>

			<table class="crave-table">
				<tr>
					<th><?php esc_attr_e('Topping Name | Color 1 | Color 2', 'WpAdminStyle'); ?></th>

					<?php foreach ($time_array as $key2 => $new_time) { ?>
						<th><?php echo $key2; ?></th>
					<?php } ?>
					<th>Total</th>
				</tr>


				<?php foreach ($new_products as $key => $new_product) { ?>
					<tr>
						<td><?php echo $key; ?></td>
						<?php //ksort($new_product); 
						?>
						<?php foreach ($time_array as $key2 => $new_time) { ?>
							<td><?php echo $new_product[$key2]['quantity']; ?></td>
							<?php

							$old_qty = $total_array[$key2]['quantity'];
							$old_total = $total_array[$key2]['total'];
							$total_array[$key2]['quantity'] = $old_qty + $new_product[$key2]['quantity'];
							$total_array[$key2]['total'] = $old_total + $new_product[$key2]['total'];

							$old_qty_product = $total_array_product[$key]['quantity'];
							$old_total_product = $total_array_product[$key]['total'];
							$total_array_product[$key]['quantity'] = $old_qty_product + $new_product[$key2]['quantity'];
							$total_array_product[$key]['total'] = $old_total_product + $new_product[$key2]['total'];

							?>
						<?php } ?>
						<td><?php echo $total_array_product[$key]['quantity']; ?></td>
					</tr>
					<?php
					//$total_quantity += $new_product[$key2]['quantity'];
					//$total_amount += $new_product[$key2]['total'];
					?>
				<?php } ?>

				<?php
				$grand_total = 0;
				?>

				<tr>
					<td><b>Total</b></td>
					<?php foreach ($time_array as $key2 => $new_time) { ?>
						<td><?php echo $total_array[$key2]['quantity']; ?></td>
						<?php

						$grand_total += $total_array[$key2]['quantity'];

						?>
					<?php } ?>
					<td><?php echo $grand_total; ?></td>
				</tr>

			</table>

		</div>
		<br /><br />
		<button id="print_me">Print</button> -->




	</div>

<?php

}

add_action('wp_ajax_wct_get_toppings_orders_report', 'wct_get_get_toppings_orders_callback');
// If you want not logged in users to be allowed to use this function as well, register it again with this function:
add_action('wp_ajax_nopriv_wct_get_toppings_orders_report', 'wct_get_get_toppings_orders_callback');

function wct_get_get_toppings_orders_callback()
{

	$from_date = $_POST['from_date'];
	$to_date = $_POST['to_date'];
	$location = $_POST['location'];
	$category = $_POST['category'];
	$category_name = $_POST['category_name'];

	//echo $from_date."<br>";
	//echo $to_date."<br>";

	//echo $location."<br>";
	//echo $category."<br>";

	/*$orders = wc_get_orders(array(
	
		'limit'=>-1,
		//'type'=> 'shop_order',
		'date_created'=> $from_date .'...'. $to_date,
		
		'meta_key'      => '_order_location', // Postmeta key field
    	'meta_value'    => $location
		
		)
		
	);*/

	/*$args = array(
			'limit' => -1,
			'type'=> 'shop_order',
			'meta_query' => array(
				array(
					'key' => '_order_location',
					'value' => $location,
					'compare' => '='
				),
			),
		); 
	
	$orders = wc_get_orders( $args );*/


	global $wpdb;

	//echo $from_date."<br>";

	$from_date = date("Y-m-d", strtotime($from_date) + (3600 * 24));

	//echo $from_date."<br>";

	//echo $to_date."<br>";

	$to_date = date("Y-m-d", strtotime($to_date) + (3600 * 24));

	//echo $to_date."<br>";

	$orders = get_orders_by_date_range($from_date, $to_date, $location);

	$new_products = array();
	
	$final_products = array();
	
	$check_result = array();
	$check_result_proudct = array();
		
		
	foreach ($orders as $key => $order) {
		$order_id = $order->ID;
		$order_details = wc_get_order($order_id);
		$order_items = $order_details->get_items();
		$order_customs = get_post_custom($order_id);
	
		$ordergroupID =  get_post_meta( $order_id, '_group_id', true ); 

		 $group_product_ids =  get_post_meta( $order_id, 'group_product_ids', true ); 

//  print_r($group_product_ids);
		// Split the string by " " (space) to get an array of substrings
		$parts = explode("|", $ordergroupID);
		
		// Initialize an empty array to store the extracted values
		$result = array();
		
		// Loop through the parts array
		foreach ($parts as $part) {
			// Split each part by ":" to get key-value pairs
			$keyValue = explode(":", $part);
		
			// Extract the key and value, and trim any whitespaces
			$key = trim($keyValue[0]);
			$value = trim($keyValue[1]);
		
			// Store the key-value pair in the result array
			$result[$key] = $value;
			$check_result[$key] = $value;
		}
		$partsgroup_product_ids = explode("|", $group_product_ids);
		
// Loop through the parts array
		foreach ($partsgroup_product_ids as $partgroup_product_ids) {
			// Split each part by ":" to get key-value pairs
			$keyValuegroup_product_ids = explode(":", $partgroup_product_ids);
		
			// Extract the key and value, and trim any whitespaces
			$keygroup_product_ids = trim($keyValuegroup_product_ids[0]);
			$valuegroup_product_ids = trim($keyValuegroup_product_ids[1]);
		
			// Store the key-value pair in the result array
			// $result[$keygroup_product_ids] = $valuegroup_product_ids;
			$check_result_proudct[$order_id][$keygroup_product_ids] = $valuegroup_product_ids;
		}
		



		// $result = array_pop($result);
// echo '<pre>';
// 		print_r($result);
// 	echo	'</pre>';
// exit;		

	
		$order_time = $order_customs['_delivery_time'][0];
	
		$delivery_date = $order_customs['_delivery_date'][0];
	
		$decor_type1 = $order_customs['_decor_type'][0];
	
		$color1_1 = $order_customs['_decor_color_1'][0];
		$color2_1 = $order_customs['_decor_color_2'][0];
	
		$colors1 = explode("|", $color1_1);
		$colors2 = explode("|", $color2_1);
	
		$decor_types = explode("|", $decor_type1);

//  print_r($decor_types);


		//  echo '<pre>';
// print_r($decor_types);
// echo '</pre>';
	


		foreach ($decor_types as $decor_key => $decor_type) {
			if ($decor_type == " ") {
				continue;
			}
		
			$color1_2 = explode(":", $colors1[$decor_key]);
			$color2_2 = explode(":", $colors2[$decor_key]);
		
			$color1 = $color1_2[1];
			$color2 = $color2_2[1];
		
			$decor_type2 = explode(":", $decor_type);
		 
			$decor_type = $decor_type2[1] . " | " . $color1 . " | " . $color2;
		
			foreach ($order_items as $order_item) {
				$prod_id = $order_item->get_product_id();
				 
				 
				 
				 $group_IDs = get_group_id($prod_id,$order_id,$check_result_proudct);
			 
				
 				if( $result[$group_IDs] == trim($decor_type2[1])){
					
					$name = $order_item->get_name();
					$qty = $order_item->get_quantity();
					$total = $order_item->get_total();
					$terms = get_the_terms($prod_id, 'product_cat');
					$cat_id = $terms[0]->term_id;
					$cat_name = $terms[0]->name;
					
				 
		
					$old_qty = isset($final_products[$decor_type][$order_id][$prod_id]['quantity']) ? $final_products[$decor_type][$order_id][$prod_id]['quantity'] : 0;
					$final_products[$decor_type][$order_time][$order_id][$prod_id]['order'] = $order_id;
					$final_products[$decor_type][$order_time][$order_id][$prod_id]['group_id'] = $group_IDs;
					$final_products[$decor_type][$order_time][$order_id][$prod_id]['product_id'] = $prod_id;
					$final_products[$decor_type][$order_time][$order_id][$prod_id]['product_name'] = $name;
					$final_products[$decor_type][$order_time][$order_id][$prod_id]['cat_id'] = $cat_id;
					$final_products[$decor_type][$order_time][$order_id][$prod_id]['cat_name'] = $cat_name;
					$final_products[$decor_type][$order_time][$order_id][$prod_id]['delivery_date'] = $delivery_date;

					if($cat_id==35){
						$final_products[$decor_type][$order_time][$order_id][$prod_id]['quantity'] = $old_qty + $qty*12;					
					} else {
							$final_products[$decor_type][$order_time][$order_id][$prod_id]['quantity'] = $old_qty + $qty;					}
			
			}
			


			}
		}
 
		
		
	
	}

	// Loop through the array and remove empty indices
foreach ($check_result as $key => $value) {
    if ($value === '') {
        unset($check_result[$key]);
    }
}
 
$products_final = array();
foreach ($final_products as $main_index => $value) {
    foreach ($value as $time => $data) {
		foreach ($data as $order_id => $order) {
			foreach ($order as $prod_id => $details) {
			 
			$products_final[$main_index][$order_id]['qty'] += $details['quantity'];
			}
		}
	}
}
		

$finalProducts = array();
foreach($products_final as $key => $product){
	foreach($product as $key0 => $data){
	 
		$finalProducts[$key][$key0] = array(
			'main_index' => $key,
			'total_quantity' => $data['qty'],
			'order_id' => $key0

		);
	 
	}

}
// 	echo '<pre>';
// print_r($finalProducts);
// echo '</pre>';
// Group data by "group_id" and calculate total quantity for each group
// $groupedData = array();
// foreach ($final_products as $main_index => $value) {
//     foreach ($value as $time => $data) {
// 		foreach ($data as $order_id => $order) {

//         foreach ($order as $id => $details) {
//             $group_id = $details['group_id'];
//             if (!isset($groupedData[$group_id])) {
//                 $groupedData[$group_id] = array(
//                     'main_index' => $main_index,
//                     'time' => $time,
// 					'delivery_date' => $details['delivery_date'],
//                     'total_quantity' => 0,
// 					'order_id' => $details['order']
//                 );
//             }
//             $groupedData[$group_id]['total_quantity'] += $details['quantity'];
//         }
// 	}
//     }
// }


// Create an associative array to store unique main_index values as keys and sum of total_quantity values as values
// $result = array();
// foreach ($groupedData as $key => $group) {
//     $main_index = $group["main_index"];
//     $total_quantity = $group["total_quantity"];
//     if (isset($result[$main_index])) {
//         $result[$main_index]["total_quantity"] += $total_quantity;
//     } else {
//         $result[$main_index] = array(
//             "main_index" => $main_index,
//             "time" => $group["time"],
//             "delivery_date" => $group["delivery_date"],
//             "total_quantity" => $total_quantity,
// 			'order_id' => $group['order_id']

//         );
//     }
// }

  	



 
//  exit;
 		?>
		<!-- HTML table to display the grouped data -->
		<div id="print_table">

		<?php

		$from_date = date("Y-m-d", strtotime($from_date) - (3600 * 24));
		$to_date = date("Y-m-d", strtotime($to_date) - (3600 * 24));

		?>

		<h2><?php esc_attr_e('Toppings for ' . $category_name . ' Pre-orders: ' . date("D M d, Y", strtotime($from_date)) . ' to ' . date("D M d, Y", strtotime($to_date)), 'WpAdminStyle'); ?></h2>

		 

		

	
<table class="crave-table"> 
    <tr>
	<th>Order ID</th>
 				<th><?php esc_attr_e('Topping Name', 'WpAdminStyle'); ?></th>
				<th>Color 1</th>
				<th>Color 2</th>
				<th>Total</th>
				 
				<th>Action</th>

      
    </tr>
    <?php 
	



	foreach ($finalProducts as $main_index => $finalProduct) {
		
 				$cols = explode(" | ",$main_index);

				foreach($finalProduct as $order_id => $data){


				?>
        <tr>
		<td class="order-date"><?php echo $order_id; ?></td>
 		<td class="topping-name"><?php echo $cols[0]; ?></td>
		<td class="color-1"><?php echo $cols[1]; ?></td>
		<td class="color-2"><?php echo $cols[2]; ?></td>
 		<td><span class="total-toppings"><?php echo $data['total_quantity']; ?></span></td>
		 <td><a href="<?php echo site_url() . "/wp-admin/post.php?post=" . $order_id . "&action=edit"; ?>" class="view-order" target="_blank"><button class="crave-table-btn">View Order</button></a></td>

         </tr>
    <?php 
				}
} ?>
</table>
</div>
<?php
		

?>

	<br class="clear" />

	
	<br /><br />
	<!-- <button id="print_me">Print</button> -->
	<input type='button' id='btn' class="crave-table-btn" value='Print' onclick='printDiv();'>
	<style>
		.widefat td {
			border: 1px solid black;
		}
	</style>

<?php

	die();
}

function get_toppings_orders_backend_js()
{
?>
	<script>
		function printDiv() {

			var divToPrint = document.getElementById('print_table');

			var newWin = window.open('', 'Print-Window');

			newWin.document.open();

			newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

			newWin.document.close();

			setTimeout(function() {
				newWin.close();
			}, 10);

		}

		jQuery("#generate_toppings_orders").on("click", function() {

			var date_from = jQuery("#from_date").val();
			var date_to = jQuery("#to_date").val();
			var location1 = jQuery("#location").children("option").filter(":selected").val();
			//var category1 = jQuery("#product_type").children("option").filter(":selected").val();
			var category_name1 = jQuery("#location").children("option").filter(":selected").text();

			jQuery.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					'from_date': date_from,
					'to_date': date_to,
					'location': location1,
					'category_name': category_name1,
					'action': 'wct_get_toppings_orders_report' // this is the name of the AJAX method called in WordPress
				},
				success: function(msg) {
					document.getElementById('result').innerHTML = msg;
				},
				error: function() {
					alert("error: Please Re-generate the Report");
				}
			});
		});
	</script>
<?php
}
add_action('admin_footer', 'get_toppings_orders_backend_js');
