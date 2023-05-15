<?php

function get_bakesheet_data( $location, $from_date, $to_date ){
	
	//exit;
	
	$location = "";
			$category = "";
			$category_name = "All Locations";
			//echo $from_date.": ".$to_date;
			
			global $wpdb;
	
			$orders = get_orders_by_date_range($from_date, $to_date, $location);
			
			$new_products = array();
			
			foreach($orders as $key=>$order){
				
				$order_id = $order->ID;
				
				
				
				$order_details = wc_get_order( $order_id );
				$order_items = $order_details->get_items();
				
				$order_customs = get_post_custom($order_id);
				
				$order_time = $order_customs['_delivery_time'][0];
						
				foreach($order_items as $order_item){
					$prod_id = $order_item->get_product_id();
					$name = $order_item->get_name();
					//$item = $order_item->data();
					$qty = $order_item->get_quantity();
					$total = $order_item->get_total();
					
					$old_qty = $new_products[$name][$order_time]['quantity'];
					
					$new_products[$name][$order_time]['product_id'] = $prod_id;
					$new_products[$name][$order_time]['product_name'] = $name;
					$new_products[$name][$order_time]['quantity'] = $old_qty+$qty;
					$new_products[$name][$order_time]['total'] = $new_products[$name][$order_time]['total']+$total;
					
					
				}
				
				
			}
				
			$cat_array = array();
			
			foreach($new_products as $new_product){
				
				$prod_id = $new_product['product_id'];
				
				$terms = get_the_terms ( $prod_id, 'product_cat' );
				
				$term_id = $terms[0]->term_id;
				$term_slug = $terms[0]->name;
				
				$old_qty = $cat_array[$term_slug]['quantity'];
				$old_total = $cat_array[$term_slug]['total'];
				if($term_id==$category){
					$cat_array[$term_slug]['quantity'] = $old_qty + $new_product['quantity'];
					$cat_array[$term_slug]['total'] = $old_total + $new_product['total'];
				} elseif($category==NULL){
					$cat_array[$term_slug]['quantity'] = $old_qty + $new_product['quantity'];
					$cat_array[$term_slug]['total'] = $old_total + $new_product['total'];
				}
				
			}
				
				$time_array = array();
				
				foreach($new_products as $key=>$new_product){
					
					foreach($new_product as $key2=>$one_time){
						
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
				


				
				
				 foreach($new_products as $key=>$new_product){ 
					
						 //echo $key;
						 //ksort($new_product);
						 foreach($time_array as $key2=>$new_time){ 
						 //echo $new_product[$key2]['quantity'];
						 
							
							$old_qty = $total_array[$key2]['quantity'];
							$old_total = $total_array[$key2]['total'];
							$total_array[$key2]['quantity'] = $old_qty + $new_product[$key2]['quantity'];
							$total_array[$key2]['total'] = $old_total + $new_product[$key2]['total'];
							
							
							$old_qty_product = $total_array_product[$key]['quantity'];
							$old_total_product = $total_array_product[$key]['total'];
							$total_array_product[$key]['quantity'] = $old_qty_product + $new_product[$key2]['quantity'];
							$total_array_product[$key]['total'] = $old_total_product + $new_product[$key2]['total'];
							
						
						 } 
						 //echo $total_array_product[$key]['quantity']; 
					
					
						//$total_quantity += $new_product[$key2]['quantity'];
						//$total_amount += $new_product[$key2]['total'];
					
				 } 
				
				 
					$grand_total = 0;
				 foreach($time_array as $key2=>$new_time){ 
						 //echo $total_array[$key2]['quantity']; 
						
							
							 $grand_total+=$total_array[$key2]['quantity'];
						
						
					 } 
					//echo $grand_total; 
    
  
	
	/*echo "<pre>";
	print_r($total_array);
	echo "</pre>";*/
	
	$new_totals = array();
	
	foreach($total_array as $key=>$totals){
		$new_totals[$key] = $totals['quantity'];
	}
	
	return $new_totals;
	
}