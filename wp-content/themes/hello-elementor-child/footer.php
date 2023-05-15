<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	if ( did_action( 'elementor/loaded' ) && hello_header_footer_experiment_active() ) {
		get_template_part( 'template-parts/dynamic-footer' );
	} else {
		get_template_part( 'template-parts/footer' );
	}
}

	if( is_page('packaging-and-decorations') || is_page('checkout') || is_cart() ) {
		//echo $_SESSION['order_type'];
		if (!session_id()) {
			session_start();
		}
		//alog('$_SESSION',$_SESSION,__FILE__,__LINE__); 
		
		if(!isset($_SESSION['order_type'])){
			if(!isset($_GET['key'])){
			?>
				<script>
				//alert(<?php echo $_SESSION['order_type']; ?>);
				jQuery( document ).ready(function() {
					
					
					setTimeout(function(){
						elementorProFrontend.modules.popup.showPopup( { id: 366 } );
					}, 2000);
				});
				
				// Mini Cupcakes Category Plus Minus Buttons //
				
				
				/*jQuery('[name="quantity"]').on("change", function(){
			
					var current_qty = jQuery(this).val();
		
					if(current_qty=='' || current_qty<=0){
						current_qty = 0;
					}
		
					var prod_id = jQuery(this).attr("data-id");
					console.log(prod_id);
					
					var current_cat_id = jQuery("#current_cat_id").val();
					
					//alert(current_qty);
					//alert(prod_id);
					
					order_info[prod_id] = current_qty;
					
					console.log(order_info);
					
					jQuery('#count').html(0);
					
					$.each( order_info, function( prod_id, qty ){	
					
						current_qty = parseInt(jQuery('#count').html());		
						jQuery('#count').html(current_qty + parseInt(qty));
						
						//alert(prod_id + ": " + qty);				
					});
					
					
				});*/
				
				// End Mini Cupcakes Category Plus Minus Buttons //
				
				</script>
			<?php
			}
		}
	}
	/*
	if( is_cart() ) {
		if(!isset($_SESSION['decor_type'])){
			?>
				<script>
				jQuery( document ).ready(function() {
					window.location.href = "/packaging-and-decorations";
				});
				</script>
			<?php
		}
	}
	if( is_page('packaging-and-decorations') ) {
		if(!isset($_SESSION['order_type'])){
			?>
				<script>
				jQuery( document ).ready(function() {
					window.location.href = "/shop-landing-page";
				});
				</script>
			<?php
		}
	}
	*/

wp_footer(); ?>
	
</body>
</html>
