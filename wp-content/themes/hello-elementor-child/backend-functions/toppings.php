<?php

add_submenu_page('sales-report', 'Toppings by Type', 'Toppings by Type', 'manage_options', 'toppings-sheet', 'wct_toppings_page');

function wct_toppings_page()
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
	<h1>Toppings Sheet</h1>

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

	<button id="generate_toppings" class="crave-table-btn">Generate</button>

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



		?>

	</div>

<?php

}

add_action('wp_ajax_wct_get_toppings_report', 'wct_get_get_toppings_callback');
// If you want not logged in users to be allowed to use this function as well, register it again with this function:
add_action('wp_ajax_nopriv_wct_get_toppings_report', 'wct_get_get_toppings_callback');

function wct_get_get_toppings_callback()
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
		$groupingArray = array();
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
 
		


// Group data by "group_id" and calculate total quantity for each group
$groupedData = array();
foreach ($final_products as $main_index => $value) {
    foreach ($value as $time => $data) {
		foreach ($data as $order_id => $order) {

        foreach ($order as $id => $details) {
            $group_id = $details['group_id'];
            if (!isset($groupedData[$group_id])) {
                $groupedData[$group_id] = array(
                    'main_index' => $main_index,
                    'time' => $time,
					'delivery_date' => $details['delivery_date'],
                    'total_quantity' => 0
                );
            }
            $groupedData[$group_id]['total_quantity'] += $details['quantity'];
        }
	}
    }
}


// Create an associative array to store unique main_index values as keys and sum of total_quantity values as values
$result = array();
foreach ($groupedData as $key => $group) {
    $main_index = $group["main_index"];
    $total_quantity = $group["total_quantity"];
    if (isset($result[$main_index])) {
        $result[$main_index]["total_quantity"] += $total_quantity;
    } else {
        $result[$main_index] = array(
            "main_index" => $main_index,
            "time" => $group["time"],
            "delivery_date" => $group["delivery_date"],
            "total_quantity" => $total_quantity
        );
    }
}

  	

// echo '<pre>';
// print_r($result);
// echo '</pre>';
 	

 
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
	<th>Start Date</th>
				<th>Completion Date</th>
				<th><?php esc_attr_e('Topping Name', 'WpAdminStyle'); ?></th>
				<th>Color 1</th>
				<th>Color 2</th>
				<th>Total</th>
				<th>#Completed</th>
				<th>#Remaining</th>
				<th>Initials</th>
				<th>Action</th>

      
    </tr>
    <?php foreach ($result as $group_id => $data) {
		
		$start_date = date("Y-m-d", strtotime($data['delivery_date']) - (3600 * 24));
				$cols = explode(" | ", $data['main_index']);
				
				 

					global $wpdb;
					$topping_name_sql = str_replace('\'', '\\\\\\\'', $cols[0]);

					$results = $wpdb->get_results(
						"SELECT * FROM  toppings_sheet WHERE 
						delivery_date = '" . $data['delivery_date'] . "' AND
						topping_name = '" . $topping_name_sql . "' AND
						color_1 = '" . $cols[1] . "' AND
						color_2 = '" . $cols[2] . "'"
					);

					if (!empty($results)) {
						$done_toppings = $results[0]->done_toppings;
						$initials = $results[0]->initials;
					} else {
						$done_toppings = '';
						$initials = '';
					}

					$difference = (int)$data['total_quantity'] - (int)$done_toppings;
 
				
		?>
        <tr>
		<td class="order-date"><?php echo $start_date; ?></td>
		<td class="delivery-date"><?php echo $data['delivery_date']; ?></td>

		<td class="topping-name"><?php echo $cols[0]; ?></td>
		<td class="color-1"><?php echo $cols[1]; ?></td>
		<td class="color-2"><?php echo $cols[2]; ?></td>
 		<td><span class="total-toppings"><?php echo $data['total_quantity']; ?></span></td>

		 <td>
						 
						<input type="number" class="done-toppings" min="0" max="<?php echo $data['total_quantity']; ?>" value="<?php echo $done_toppings; ?>" stle="width:30px;" />
					</td>
					<td>
						<p class="remaining-toppings"><?php echo $difference; ?></p>
					</td>
					<td><input type="text" class="initials" value="<?php echo $initials; ?>" stle="width:50px;" /></td>
					<td><button class="save-toppings crave-table-btn">Save</button></td>

          
           
        </tr>
    <?php } ?>
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


function get_group_id($prod_id,$order_id,$check_result_proudct){
	
	return $check_result_proudct[$order_id][$prod_id];
}

add_action('wp_ajax_wct_save_toppers_done', 'wct_save_toppers_done_callback');
// If you want not logged in users to be allowed to use this function as well, register it again with this function:
add_action('wp_ajax_nopriv_wct_save_toppers_done', 'wct_save_toppers_done_callback');

function wct_save_toppers_done_callback()
{

	global $wpdb;

	$delivery_date = $_POST['delivery_date'];
	$topping_name = $_POST['topping_name'];
	$color_1 = $_POST['color_1'];
	$color_2 = $_POST['color_2'];
	$done_toppings = $_POST['done_toppings'];
	$initials = $_POST['initials'];


	$results = $wpdb->get_results("SELECT * FROM  toppings_sheet WHERE 
			delivery_date = '" . $delivery_date . "' AND
			topping_name = '" . addslashes($topping_name) . "' AND
			color_1 = '" . $color_1 . "' AND
			color_2 = '" . $color_2 . "'");

	echo "SELECT * FROM  toppings_sheet WHERE 
			delivery_date = '" . $delivery_date . "' AND
			topping_name = '" . addslashes($topping_name) . "' AND
			color_1 = '" . $color_1 . "' AND
			color_2 = '" . $color_2 . "'";

	echo "<pre>";
	// print_r($results);
	echo "</pre>";

	if (!empty($results)) {

		$id = $results[0]->id;

		echo $id;

		$wpdb->query("UPDATE toppings_sheet SET done_toppings = '" . $done_toppings . "', initials='" . $initials . "' WHERE id = '" . $id . "'");
	} else {

		$wpdb->insert("toppings_sheet", array(
			"delivery_date" => $delivery_date,
			"topping_name" => $topping_name,
			"color_1" => $color_1,
			"color_2" => $color_2,
			"done_toppings" => $done_toppings,
			"initials" => $initials,
		));
	}
}


function get_toppings_backend_js()
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

		// function printData() {
		// 	/* var divToPrint=document.getElementById("print_table");
		// 	 newWin= window.open("");
		// 	 newWin.document.write(divToPrint.outerHTML);
		// 	 newWin.print();
		// 	 newWin.close();*/

		// 	var printContents = document.getElementById("print_table").innerHTML;
		// 	var originalContents = document.body.innerHTML;

		// 	document.body.innerHTML = printContents + "<style>td {border:1px solid black; }</style>";

		// 	window.print();

		// 	document.body.innerHTML = originalContents;
		// }

		// jQuery('#wpbody-content').on('click', "#print_me", function() {
		// 	//alert("Print");
		// 	printData();
		// })

		jQuery("#generate_toppings").on("click", function() {

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
					'action': 'wct_get_toppings_report' //this is the name of the AJAX method called in WordPress
				},
				success: function(msg) {
					document.getElementById('result').innerHTML = msg;
				},
				error: function() {
					alert("error: Please Re-generate the Report");
				}
			});
		});

		jQuery("#result").on('click', ".save-toppings", function() {

			var delivery_date = jQuery(this).parent().parent().find('.delivery-date').html();
			var topping_name = jQuery(this).parent().parent().find('.topping-name').html();
			var color_1 = jQuery(this).parent().parent().find('.color-1').html();
			var color_2 = jQuery(this).parent().parent().find('.color-2').html();
			var done_toppings = jQuery(this).parent().parent().find('.done-toppings').val();
			var initials = jQuery(this).parent().parent().find('.initials').val();

			//alert(color_1);

			jQuery.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					'delivery_date': delivery_date,
					'topping_name': topping_name,
					'color_1': color_1,
					'color_2': color_2,
					'done_toppings': done_toppings,
					'initials': initials,
					'action': 'wct_save_toppers_done' //this is the name of the AJAX method called in WordPress
				},
				success: function(msg) {

				},
				error: function() {
					alert("error: Please Re-generate the Report");
				}
			});

		});

		jQuery("#result").on("change", ".done-toppings", function() {

			//alert(jQuery(this).val());

			var new_value = jQuery(this).val();
			var old_value = jQuery(this).parent().parent().find(".total-toppings").html();
if(old_value == ''){
	old_value = 0;
}
			jQuery(this).parent().parent().find(".remaining-toppings").html(parseInt(old_value) - parseInt(new_value));

		});
	</script>
<?php
}
add_action('admin_footer', 'get_toppings_backend_js');
