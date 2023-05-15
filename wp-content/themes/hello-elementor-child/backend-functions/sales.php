<?php

add_menu_page('Sales Report', 'Sales Report', 'administrator', 'sales-report', 'wct_sales_report_page');

//$shop_order_link = site_url().'/wp-admin/edit.php?post_type=shop_order';

//add_submenu_page( 'sales-report', 'Order Fulfillment', 'Order Fulfillment', 'manage_options', $shop_order_link, 'oa_submenu_page' );

function oa_submenu_page()
{
}

function wct_sales_report_page()
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

?>
	<h1>Sales Report</h1>

	From
	<input type="date" id="from_date" />
	To
	<input type="date" id="to_date" />

	Store:
	<select name="location" id="location">
		<option value="">All Locations</option>
		<?php
		foreach ($locations as $location) { ?>
			<option value="<?php echo $location->slug; ?>"><?php echo $location->name; ?></option>
		<?php } ?>
	</select>

	Product Type:
	<select name="product_type" id="product_type">
		<option value="">All Types</option>
		<?php
		foreach ($product_cats as $product_cat) { ?>
			<option value="<?php echo $product_cat->term_id; ?>"><?php echo $product_cat->name; ?></option>
		<?php } ?>
	</select>

	<button id="generate_report" class="crave-table-btn">Generate</button>

	<div id="result"></div>

<?php

}

add_action('wp_ajax_wct_get_sales_report', 'wct_get_sales_report_callback');
// If you want not logged in users to be allowed to use this function as well, register it again with this function:
add_action('wp_ajax_nopriv_wct_get_sales_report', 'wct_get_sales_report_callback');

function wct_get_sales_report_callback()
{

	$from_date = $_POST['from_date'];
	$to_date = $_POST['to_date'];
	$location = $_POST['location'];
	$category = $_POST['category'];

	//echo $from_date."<br>";
	//echo $to_date."<br>";
	//echo $location."<br>";
	//echo $category."<br>";

	$orders = wc_get_orders(
		array(
			'limit' => -1,
			//'type'=> 'shop_order',
			'date_created' => $from_date . '...' . $to_date,

			/*array(
                'key' => '_order_location',
                'value' => $location,
                'compare' => '='
            ),
			array(
                'key' => '_decor_type',
                'value' => "%".$category."%",
                'compare' => 'LIKE'
            ),*/


			'meta_key'      => '_order_location', // Postmeta key field
			'meta_value'    => $location

		)

	);

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


	/*global $wpdb;
	
	$orders = $wpdb->get_results( "SELECT * FROM $wpdb->posts 
				WHERE post_type = 'shop_order'
				AND post_date BETWEEN '{$from_date}  00:00:00' AND '{$to_date} 23:59:59'
			");*/

	/*$orders = new WP_Query(
		array(
			'post_type'			=> 'shop_order', // post type to query
			'posts_per_page'	=> -1, // get all the posts not limited
			'date_created'=> $from_date .'...'. $to_date,
			'meta_query'		=> array(
				'relation'	=> 'AND',
				'location' => array(
					'key'		=> '_order_location',
					'value' 	=> $location,
					'compare'	=> '='
				),
				'start_time' => array(
					'key'		=> '_decor_type',
					'value' 	=> "%".$category."%",
					'compare'	=> 'LIKE',
					
				)
			),
			// order by using the meta array keys to reference them
			'orderby'			=> array(
				'day'			=> 'ASC',
				'start_time'	=> 'ASC'
			),
			
		)
	);
	
	echo count($orders);*/

	$new_products = array();

	foreach ($orders as $key => $order) {

		$order_id = $order->id;
		$order_items = $order->data['line_items'];

		//echo "OrderNumber: ".$key."<br>";
		//echo "<pre>";
		//print_r($order);
		//echo "Order_ID()".$order_id."<br>";
		//print_r($order_items);
		//echo "<pre>";



		foreach ($order_items as $order_item) {
			$prod_id = $order_item->get_product_id();
			$name = $order_item->get_name();
			//$item = $order_item->data();
			$qty = $order_item->get_quantity();
			$total = $order_item->get_total();

			$old_qty = $new_products[$name]['quantity'];

			//echo $prod_id."<br>";
			//echo $name."<br>";
			//echo $qty."<br>";
			//echo $total."<br>";
			//echo $old_qty."<br><br><br>";
			//if(!empty($new_products[$name])){


			$new_products[$name]['product_id'] = $prod_id;
			$new_products[$name]['product_name'] = $name;
			$new_products[$name]['quantity'] = $old_qty + $qty;
			$new_products[$name]['total'] = $new_products[$name]['total'] + $total;

			/*} else {
				
				$new_products[$name] = array(
											"product_id"=>$prod_id,
											"product_name"=>$name,
											"quantity"=>$qty,
											"total"=>$total,
									);
				
			}*/
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

	/*echo "<pre>";
	print_r($cat_array);
	echo "</pre>";*/

	$total_quantity = 0;
	$total_amount = 0;

?>

	<br class="clear" />

	<h2><?php esc_attr_e('By Source', 'WpAdminStyle'); ?></h2>

	<table class="widefat crave-table" id="print_table">
		<tr>
			<th class="row-title"><b><?php esc_attr_e('Item Type', 'WpAdminStyle'); ?></b></th>
			<th><b><?php esc_attr_e('Item Count', 'WpAdminStyle'); ?></b></th>
			<th><b><?php esc_attr_e('Item Total', 'WpAdminStyle'); ?></b></th>
		</tr>
		<?php foreach ($cat_array as $key => $one_cat) { ?>
			<tr>
				<td class="row-title"><label for="tablecell"><?php echo $key; ?></label></td>
				<td><?php echo $one_cat['quantity']; ?></td>
				<td><?php echo $one_cat['total']; ?></td>
			</tr>
			<?php
			$total_quantity += $one_cat['quantity'];
			$total_amount += $one_cat['total'];
			?>
		<?php } ?>
		<tr>
			<td class="row-title"><b>Total</b></td>
			<td><b><?php echo $total_quantity; ?></b></td>
			<td><b><?php echo $total_amount; ?></b></td>
		</tr>
	</table>
	<br /><br />
	<input type='button' id='btn' class="crave-table-btn" value='Print' onclick='printDiv();'>


	<!--<table>
    	<thead>
            <th>Item Type</th>
            <th>Item Count</th>
            <th>Item Total</th>
		</thead>        
        <tbody>
        <?php foreach ($cat_array as $key => $one_cat) { ?>
            <tr>
                <td><?php echo $key; ?></td>
                <td><?php echo $one_cat['quantity']; ?></td>
                <td><?php echo $one_cat['total']; ?></td>
                <?php
				$total_quantity += $one_cat['quantity'];
				$total_amount += $one_cat['total'];
				?>
            </tr>
        <?php } ?>
        	<tr>
                <td>Total</td>
                <td><?php echo $total_quantity; ?></td>
                <td><?php echo $total_amount; ?></td>
            </tr>
        </tbody>
    </table>-->

<?php

	die();
}

function get_sales_report_backend_js()
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

		jQuery("#generate_report").on("click", function() {

			var date_from = jQuery("#from_date").val();
			var date_to = jQuery("#to_date").val();
			var location1 = jQuery("#location").children("option").filter(":selected").val();
			var category1 = jQuery("#product_type").children("option").filter(":selected").val();

			jQuery.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					'from_date': date_from,
					'to_date': date_to,
					'location': location1,
					'category': category1,
					'action': 'wct_get_sales_report' //this is the name of the AJAX method called in WordPress
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
add_action('admin_footer', 'get_sales_report_backend_js');
