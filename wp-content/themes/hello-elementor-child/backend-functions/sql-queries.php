<?php

function bpr_get_total_sold_by_product_id( $date_from, $date_to, $product_id ) {
	
	global $wpdb;
	
	$sql = "
		SELECT order_item_meta__product_id.meta_value AS product_id,
			SUM(order_item_meta__qty.meta_value) AS order_item_count,
			SUM(order_item_meta__line_total.meta_value) AS order_item_total
			
			FROM  {$wpdb->prefix}posts AS posts
			
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items
				ON posts.id = order_items.order_id
				
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__qty
				ON ( order_items.order_item_id =
				order_item_meta__qty.order_item_id )
				AND ( order_item_meta__qty.meta_key = '_qty' )
				
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__line_total
				ON ( order_items.order_item_id =
				order_item_meta__line_total.order_item_id )
				AND ( order_item_meta__line_total.meta_key = '_line_total' )
				
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__product_id
				ON ( order_items.order_item_id = order_item_meta__product_id.order_item_id )
				AND ( order_item_meta__product_id.meta_key = '_product_id' )
				
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS
				order_item_meta__product_id_array
				ON order_items.order_item_id = order_item_meta__product_id_array.order_item_id
			WHERE  posts.post_type IN ( 'shop_order', 'shop_order_refund' )
			
			AND posts.post_status IN ( 'wc-completed', 'wc-processing', 'wc-on-hold' )
			
			AND DATE(posts.post_date) BETWEEN '$date_from' AND '$date_to'
			
			AND (( order_item_meta__product_id_array.meta_key IN ( '_product_id', '_variation_id' )
			
			AND order_item_meta__product_id_array.meta_value IN ( '$product_id' ) ))
			
		GROUP  BY product_id
	";
	
	$query = $wpdb->prepare( $sql, $date_from, $date_to, $product_id  );
	
	$results = $wpdb->get_results( $query );
	
	return $results;
}

function get_orders_by_date_range($date_from, $date_to, $location){
	
	global $wpdb;
	
	$location_query = "";
	
	if($location!=NULL){
		
		$location_query = "AND 
	  
	  (wpostmetaLocation.meta_key = '_order_location'
	  AND wpostmetaLocation.meta_value = '".$location."')";
	  
	}
	
	$sql = "SELECT DISTINCT {$wpdb->prefix}posts.* FROM {$wpdb->prefix}posts
	
	LEFT JOIN {$wpdb->prefix}postmeta wpostmetaOrder ON ( {$wpdb->prefix}posts.ID = wpostmetaOrder.post_id AND wpostmetaOrder.meta_key = '_delivery_date' )
	
	LEFT JOIN {$wpdb->prefix}postmeta wpostmetaLocation ON ( {$wpdb->prefix}posts.ID = wpostmetaLocation.post_id AND wpostmetaLocation.meta_key = '_order_location' )
	
	LEFT JOIN {$wpdb->prefix}postmeta ON wp_posts.ID = {$wpdb->prefix}postmeta.post_id 
	
	WHERE 
	
	  (wpostmetaOrder.meta_key = '_delivery_date'
	  AND wpostmetaOrder.meta_value  BETWEEN '".$date_from."' AND '".$date_to."') AND post_status = 'wc-processing'
	  
	  $location_query
	  
	";
	
	//echo $sql."<br>";
	
	/*$sql = "SELECT * FROM wp_posts 
LEFT JOIN wp_postmeta wpostmetaOrder ON ( wp_posts.ID = wpostmetaOrder.post_id AND wpostmetaOrder.meta_key = '_delivery_date' )
LEFT JOIN wp_postmeta ON wp_posts.ID = wp_postmeta.post_id 
LEFT JOIN wp_term_relationships wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id)
LEFT JOIN wp_term_taxonomy wp_term_taxonomy ON (wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id)
WHERE wp_postmeta.meta_key = '_delivery_date'
  AND wp_postmeta.meta_value = '".$date_from."'
  AND wp_posts.post_status = 'publish' 
  AND wp_posts.post_type = 'shop_order' 
  AND wp_term_taxonomy.taxonomy = 'product_cat'";*/

	$query = $wpdb->prepare( $sql );
	
	$results = $wpdb->get_results( $query );
	
	return $results;

	
}