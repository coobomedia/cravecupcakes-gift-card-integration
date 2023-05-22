<?php

add_submenu_page('sales-report', 'Bake Sheet', 'Bake Sheet', 'manage_options', 'bake-sheet', 'wct_bakesheet_page');

function wct_bakesheet_page()
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

	// Bakeshop items Array
	$args = array(
		'type'                     => 'product',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => false,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'cake_bakeshop_item',
		'pad_counts'               => false

	);
	$cake_bakeshop_item = get_categories($args);



	$today = date("Y-m-d", time());
	$tomorrow = date("Y-m-d", strtotime(date("Y-m-d", time()) . " +1 days"));

	$from_date = $today;
	$to_date = $tomorrow;


?>
	<h1>Bake Sheet</h1>

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

	<button id="generate_bakesheet" class="crave-table-btn">Generate</button>

	<div id="result">

		<?php

		$location = "";
		$category = "";
		$category_name = "All Locations";
		//echo $from_date.": ".$to_date;

		global $wpdb;

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
				$cakebakeshop_items = get_post_meta( $prod_id, 'cakebakeshop_items', true );
 				$name = $order_item->get_name();
				//$item = $order_item->data();
				$qty = $order_item->get_quantity();
				$total = $order_item->get_total();




				$old_qty = $new_products[$name][$order_time]['quantity'];

				$new_products[$name][$order_time]['product_id'] = $prod_id;
				$new_products[$name][$order_time]['product_name'] = $name;
				$new_products[$name][$order_time]['quantity'] = $old_qty + $qty;
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

		<div id="print_table">
		<style>
	@media print {
.crave-table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #55B7B3;
    color: #FFF;
	}
 
		div#adminmenuwrap {
    display: none;
 
	}
	div#wpadminbar {
    display: none;
}
.crave-table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 100px;
}
table {
    display: table;
    border-collapse: separate;
    box-sizing: border-box;
    text-indent: initial;
    border-spacing: 2px;
    border-color: gray;
}
body {
    
    color: #3c434a;
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
    font-size: 13px;
    line-height: 1.4em;
   
}
tr {
    display: table-row;
    vertical-align: inherit;
    border-color: inherit;
}
.crave-table td, .crave-table th {
    border: 1px solid #ddd;
 }

}
	</style>
			<h2><?php esc_attr_e('Bake Sheet for ' . $category_name . ' Pre-orders: ' . date("D M d, Y", strtotime($from_date)) . ' to ' . date("D M d, Y", strtotime($to_date)), 'WpAdminStyle'); ?></h2>

			<table class="crave-table">
				<tr>
					<th class="row-title"><b><?php esc_attr_e('Cupcake / Bakeshop Items', 'WpAdminStyle'); ?></b></th>

					<?php foreach ($time_array as $key2 => $new_time) { ?>
						<th><b><?php echo $key2; ?></b></th>
					<?php } ?>
					<th><b>Total</b></th>
				</tr>


				<?php foreach ($new_products as $key => $new_product) { ?>
					<tr>
						<td><b><?php echo $key; ?></b></td>
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
						<td><b><?php echo $total_array_product[$key]['quantity']; ?></b></td>
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
						<td><b><?php echo $total_array[$key2]['quantity']; ?><b></td>
						<?php

						$grand_total += $total_array[$key2]['quantity'];

						?>
					<?php } ?>
					<td><b><?php echo $grand_total; ?></b></td>
				</tr>

			</table>





<!-- bakeshop table  -->
<table class="crave-table" >
				<tr>
					<th class="row-title"><b><?php esc_attr_e('Core Cupcake / Bakeshop Items', 'WpAdminStyle'); ?></b></th>

					<?php foreach ($time_array as $key2 => $new_time) { ?>
						<th><b><?php echo $key2; ?></b></th>
					<?php } ?>
					<th><b>Total</b></th>
				</tr>

				<?php 
				$bakeShopQty = array();

				
				foreach ($cake_bakeshop_item as $bakeshop_item) {	

				
				$bakeShop_term_id = $bakeshop_item->term_id;
				
$totalQty = 0;
 				foreach ($new_products as $key => $new_product) {
					
			foreach ($time_array as $key2 => $new_time) { 
				
 				 $new_product[$key2]['quantity'];
				// echo "<br>";
				 $product_id =  $new_product[$key2]['product_id'];
				
				 
				 

				$terms = get_the_terms ( $product_id, 'product_cat' );
					$catgory_id;
					foreach($terms as $term){
						$catgory_id = $term->term_id;
					
					}
					$term_id_acf = "product_cat_" . $catgory_id;

						$metaID = get_field(  'cakebakeshop_items', $product_id );
						//  print_r($metaID);
						$bakeShopQty[$bakeshop_item->name][$key2]['product_id'] = $product_id;
					if($bakeShop_term_id == $metaID)
				{ 
				 
					$bakeShopQty[$bakeshop_item->name]['category'] = $catgory_id;
					
					$totalQty = $new_product[$key2]['quantity']+$bakeShopQty[$bakeshop_item->name][$key2]['qty'];
					$bakeShopQty[$bakeshop_item->name][$key2]['qty'] = $totalQty;
				}

				}

			}
	
}
?>
				<?php 

// print_r($bakeShopQty);

$grand_total = 0;
$sums = array();

 				foreach ($cake_bakeshop_item as $bakeshop_item) { 
					$rowTotal = 0;
					?>
					<tr>
						<td><b><?php echo $bakeshop_item->name; ?></b></td>
						<?php //ksort($new_product); 
						?>
						<?php foreach ($time_array as $key2 => $new_time) { ?>
							<td>
								<?php  echo $bakeShopQty[$bakeshop_item->name][$key2]['qty']; 
								$sums[$key2]['qty'] += $bakeShopQty[$bakeshop_item->name][$key2]['qty'];
								$rowTotal += $bakeShopQty[$bakeshop_item->name][$key2]['qty'];
								
								?>
							</td>
							 
						<?php } ?>
						<td><b><?php 
						if(!empty($rowTotal)){

						
					 echo $rowTotal;
					 $grand_total += $rowTotal;
					 $category_id =  $bakeShopQty[$bakeshop_item->name]['category'];

					 if($category_id == 39)
					{
						 echo " &nbsp;&nbsp;(". ceil(($rowTotal / 35) )." tray)";
						
					}else  if($category_id == 35){
						echo " &nbsp;&nbsp;(". ceil(($rowTotal / 24) )." tray)";
					}else{
						// echo " &nbsp;&nbsp;(". $rowTotal ." )";
					}
					 
						}
						?></b></td>
					</tr>
					<?php
					//$total_quantity += $new_product[$key2]['quantity'];
					//$total_amount += $new_product[$key2]['total'];
					?>
				<?php } ?>

				<?php
				
				?>

				<tr>
					<td><b>Total</b></td>
					<?php foreach ($time_array as $key2 => $new_time) { ?>
						<td><b>
						<?php	
							echo $sums[$key2]['qty'];
						?>
						<b></td>
						 
					<?php } ?>
					<td><b><?php echo $grand_total; ?></b></td>
				</tr>

			</table>

			<!-- End bakeshop table  -->


		</div>
		<br /><br />
		<input type='button' id='btn' class="crave-table-btn" value='Print' onclick='printDiv();'>

		<style>
			.widefat td {
				border: 1px solid black;
			}
		</style>


	</div>

<?php

}

add_action('wp_ajax_wct_get_bakesheet_report', 'wct_get_get_bakesheet_callback');
// If you want not logged in users to be allowed to use this function as well, register it again with this function:
add_action('wp_ajax_nopriv_wct_get_bakesheet_report', 'wct_get_get_bakesheet_callback');

function wct_get_get_bakesheet_callback()
{

	
	// Bakeshop items Array
	$args = array(
		'type'                     => 'product',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => false,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'cake_bakeshop_item',
		'pad_counts'               => false

	);
	$cake_bakeshop_item = get_categories($args);



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
			
			//echo $prod_id."<br>";
			
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
		
		$multiplier = 1;
		
		if($term_id==35){
			
			$multiplier = 12;
			
		}
		
		if ($term_id == $category) {
			$cat_array[$term_slug]['quantity'] = $old_qty + $new_product['quantity'];
			$cat_array[$term_slug]['total'] = $old_total + $new_product['total'];
		} elseif ($category == NULL) {
			$cat_array[$term_slug]['quantity'] = $old_qty + $new_product['quantity'];
			$cat_array[$term_slug]['total'] = $old_total + $new_product['total'];
		}
	}

	$time_array = array();
	
	/*echo "<pre>";
	print_r($new_products);
	echo "</pre>";*/
	
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

	<div id="print_table">
	<style>
	@media print {
.crave-table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #55B7B3;
    color: #FFF;
	}
 
		div#adminmenuwrap {
    display: none;
 
	}
	div#wpadminbar {
    display: none;
}
.crave-table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 100px;
}
table {
    display: table;
    border-collapse: separate;
    box-sizing: border-box;
    text-indent: initial;
    border-spacing: 2px;
    border-color: gray;
}
body {
    
    color: #3c434a;
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
    font-size: 13px;
    line-height: 1.4em;
   
}
tr {
    display: table-row;
    vertical-align: inherit;
    border-color: inherit;
}
.crave-table td, .crave-table th {
    border: 1px solid #ddd;
 }

}
	</style>
		<h2><?php esc_attr_e('Bake Sheet for ' . $category_name . ' Pre-orders: ' . date("D M d, Y", strtotime($from_date)) . ' to ' . date("D M d, Y", strtotime($to_date)), 'WpAdminStyle'); ?></h2>

		<table class="crave-table">
			<tr>
				<th class="row-title"><b><?php esc_attr_e('Cupcake / Bakeshop Items', 'WpAdminStyle'); ?></b></th>

				<?php foreach ($time_array as $key2 => $new_time) { ?>
					<th><b><?php echo $key2; ?></b></th>
				<?php } ?>
				<th><b>Total</b></th>
			</tr>


			<?php foreach ($new_products as $key => $new_product) { ?>
				<tr>
					<td><b><?php echo $key; ?></b></td>
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
					<td><b><?php echo $total_array_product[$key]['quantity']; ?></b></td>
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
					<td><b><?php echo $total_array[$key2]['quantity']; ?><b></td>
					<?php

					$grand_total += $total_array[$key2]['quantity'];

					?>
				<?php } ?>
				<td><b><?php echo $grand_total; ?></b></td>
			</tr>

		</table>


<!-- bakeshop table  -->
<table class="crave-table" >
				<tr>
					<th class="row-title"><b><?php esc_attr_e('Core Cupcake / Bakeshop Items', 'WpAdminStyle'); ?></b></th>

					<?php foreach ($time_array as $key2 => $new_time) { ?>
						<th><b><?php echo $key2; ?></b></th>
					<?php } ?>
					<th><b>Total</b></th>
				</tr>

				<?php 
				$bakeShopQty = array();

				
				foreach ($cake_bakeshop_item as $bakeshop_item) {	

				
				$bakeShop_term_id = $bakeshop_item->term_id;
				
$totalQty = 0;
 				foreach ($new_products as $key => $new_product) {
					
			foreach ($time_array as $key2 => $new_time) { 
				
 				 $new_product[$key2]['quantity'];
				// echo "<br>";
				 $product_id =  $new_product[$key2]['product_id'];
				
				 
				 

				$terms = get_the_terms ( $product_id, 'product_cat' );
					$catgory_id;
					foreach($terms as $term){
						$catgory_id = $term->term_id;
					
					}
					$term_id_acf = "product_cat_" . $catgory_id;

					$metaID = get_field(  'cakebakeshop_items', $product_id );
					//  print_r($metaID);
						$bakeShopQty[$bakeshop_item->name][$key2]['product_id'] = $product_id;
					if($bakeShop_term_id == $metaID)
				{ 
				 
					$bakeShopQty[$bakeshop_item->name]['category'] = $catgory_id;
					
					$totalQty = $new_product[$key2]['quantity']+$bakeShopQty[$bakeshop_item->name][$key2]['qty'];
					$bakeShopQty[$bakeshop_item->name][$key2]['qty'] = $totalQty;
				}

				}

			}
	
}
?>
				<?php 

// print_r($bakeShopQty);

$grand_total = 0;
$sums = array();

 				foreach ($cake_bakeshop_item as $bakeshop_item) { 
					$rowTotal = 0;
					?>
					<tr>
						<td><b><?php echo $bakeshop_item->name; ?></b></td>
						<?php //ksort($new_product); 
						?>
						<?php foreach ($time_array as $key2 => $new_time) { ?>
							<td>
								<?php  echo $bakeShopQty[$bakeshop_item->name][$key2]['qty']; 
								$sums[$key2]['qty'] += $bakeShopQty[$bakeshop_item->name][$key2]['qty'];
								$rowTotal += $bakeShopQty[$bakeshop_item->name][$key2]['qty'];
								
								?>
							</td>
							 
						<?php } ?>
						<td><b><?php 
						if(!empty($rowTotal)){

						
					 echo $rowTotal;
					 $grand_total += $rowTotal;
					 $category_id =  $bakeShopQty[$bakeshop_item->name]['category'];

					 if($category_id == 39)
					{
						 echo " &nbsp;&nbsp;(". ceil(($rowTotal / 35) )." tray)";
						
					}else  if($category_id == 35){
						echo " &nbsp;&nbsp;(". ceil(($rowTotal / 24) )." tray)";
					}else{
						// echo " &nbsp;&nbsp;(". $rowTotal ." )";
					}
					 
						}
						?></b></td>
					</tr>
					<?php
					//$total_quantity += $new_product[$key2]['quantity'];
					//$total_amount += $new_product[$key2]['total'];
					?>
				<?php } ?>

				<?php
				
				?>

				<tr>
					<td><b>Total</b></td>
					<?php foreach ($time_array as $key2 => $new_time) { ?>
						<td><b>
						<?php	
							echo $sums[$key2]['qty'];
						?>
						<b></td>
						 
					<?php } ?>
					<td><b><?php echo $grand_total; ?></b></td>
				</tr>

			</table>

			<!-- End bakeshop table  -->

	</div>
	<br /><br />
	<input type='button' id='btn' class="crave-table-btn" value='Print' onclick='printDiv();'>

	<style>
		.widefat td {
			border: 1px solid black;
		}
	</style>
<?php

	die();
}

function get_bakesheet_backend_js()
{
?>
	<script>
		function printDiv() {

			var divToPrint = document.getElementById('print_table');

			var newWin = window.open('', 'Print-Window');

			newWin.document.open();

			newWin.document.write('<html><style>@media print {.crave-table th {padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #55B7B3;color: #FFF;}}</style><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

			newWin.document.close();

			setTimeout(function() {
				newWin.close();
			}, 100);

		}
		 
		// function printData()
		// {
		//   /* var divToPrint=document.getElementById("print_table");
		//    newWin= window.open("");
		//    newWin.document.write(divToPrint.outerHTML);
		//    newWin.print();
		//    newWin.close();*/

		// 	var printContents = document.getElementById("print_table").innerHTML;
		// 	var originalContents = document.body.innerHTML;

		// 	document.body.innerHTML = printContents + "<style>td {border:1px solid black; }</style>";

		// 	window.print();

		// 	document.body.innerHTML = originalContents;
		// }

		// jQuery('#wpbody-content').on('click', "#print_me", function(){
		// 	//alert("Print");
		// 	printData();
		// })

		jQuery("#generate_bakesheet").on("click", function() {

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
					'action': 'wct_get_bakesheet_report' //this is the name of the AJAX method called in WordPress
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
add_action('admin_footer', 'get_bakesheet_backend_js');
