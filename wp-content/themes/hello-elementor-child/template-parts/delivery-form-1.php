<?php
    //$curent_date = get_texas_current_time(); 
    $current_timestamp = strtotime(get_texas_current_time());
    //$current_timestamp = time();

    //echo "Current: ".$current_timestamp."<br>";
    //echo "Current: ".date("H:i:s", $current_timestamp)."<br>";

    $today_5pm = strtotime("today 17:00");

    //echo "5PM: ".$today_5pm."<br>";
    //echo "5PM: ".date("H:i:s", $today_5pm)."<br>";

    //$date = new DateTime("today 17:00", new DateTimeZone('America/Monterrey') );
    //echo "5PM Texas: ".strtotime($date->format('Y-m-d H:i:s'));

    //echo "Current+3: ".($current_timestamp+3600+3600+3600)."<br>";
    //echo "Current+3: ".date("H:i:s", ($current_timestamp+3600+3600+3600))."<br>";

    $difference = $today_5pm - ($current_timestamp + 3600 + 3600 + 3600);

    //echo $difference/60/60;

    $datetime = new DateTime('today');
    $datetime->modify('+1 day');

    //$datetime->modify('+1 day');
    //echo $datetime->format('Y-m-d H:i:s');

    if ($difference >= 0) {

      $min_date = date("Y-m-d");
    } else {

      $min_date = $datetime->format('Y-m-d');
    }
	
	$cart_cat_ids = "";
	$cart_cats = array();
	
	foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ){
		//return true;
		$terms = get_the_terms( $cart_item['product_id'], 'product_cat' );
		foreach ($terms as $term) {
		   $product_cat = $term->term_id;
		}
		
		//unset($_SESSION['cats']["wp_".$product_cat]['decor_type']);
		//unset($_SESSION['cats']["wp_".$product_cat]['decor_color_input_1']);
		//unset($_SESSION['cats']["wp_".$product_cat]['decor_color_input_2']);
		//unset($_SESSION['cats']["wp_".$product_cat]['decor_color_1']);
		//unset($_SESSION['cats']["wp_".$product_cat]['decor_color_2']);
		//unset($_SESSION['cats']["wp_".$product_cat]['decor_input_image_2']);
		
		//$return[] = wc_get_product_category_list( $cart_item['product_id'] );
		$cart_cats[] = $product_cat;
	}
	
	$cart_cat_ids = implode(",", $cart_cats);
	
    //echo $min_date."<br>";
    //echo $datetime->format('Y-m-d');
    //$current_pak_time = time();
?>
<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST" >
<input type="hidden" name="action" value="add_foobar">
  <div class="delivery-form">
      <div class="delivery-form-header">
      </div>
      <?php 

            if(isset($_SESSION['order_type'])) {
                $check_order_type = $_SESSION['order_type'];
                $check_location = $_SESSION['location'];
                $check_date_sec = $_SESSION['date_sec'];
                $check_sel_time = $_SESSION['sel_time'];
            } else {
                $check_order_type = "";
                $check_location = "";
                $check_date_sec = "";
                $check_sel_time = "";
            }
    
      ?>  
      <div class="order-type-info">
          <h3>Order Type</h3>
          <div class="order-type-radio">
              <div class="pickup-check">
                  <span class="oneChoice">
                      <input type="radio" value="pickup" id="pickup" name="order_type" required <?php echo ($check_order_type=="pickup") ? 'checked="checked"' : '' ; ?>/>
                      <label class="label postField" id="pickup" for="pickup">
                          <span class="input-radio-faux"></span>Pickup</label>
                  </span>
              </div>
              <div class="delivery-check">
                  <span class="oneChoice">
                      <input type="radio" value="delivery" id="delivery" name="order_type" required <?php echo ($check_order_type=="delivery") ? 'checked="checked"' : '' ; ?>/>
                      <label class="label postField" id="delivery" for="delivery">
                          <span class="input-radio-faux"></span>Delivery</label>
                  </span>
              </div>
          </div>
      </div>

      <div class="select_location">
          <h3>
              Select Location
          </h3>
          <div class="location-radio">
              <div class="west_university">
                  <span class="oneChoice">
                      <input type="radio" value="west_university" id="west_university" name="location" required <?php echo ($check_location=="west_university") ? 'checked="checked"' : '' ; ?> />
                      <label class="label postField" id="west_university" for="west_university">
                          <span class="input-radio-faux"></span>West University</label>
                  </span>
              </div>
              <div class="up_town_park">
                  <span class="oneChoice">
                      <input type="radio" value="up_town_park" id="up_town_park" name="location" required <?php echo ($check_location=="up_town_park") ? 'checked="checked"' : '' ; ?> />
                      <label class="label postField" id="up_town_park" for="up_town_park">
                          <span class="input-radio-faux"></span>Uptown Park</label>
                  </span>
              </div>

              <div class="woodlands">
                  <span class="oneChoice">
                      <input type="radio" value="woodlands" id="woodlands" name="location" required <?php echo ($check_location=="woodlands") ? 'checked="checked"' : '' ; ?> />
                      <label class="label postField" id="woodlands" for="woodlands">
                          <span class="input-radio-faux"></span>The Woodlands</label>
                  </span>
              </div>
          </div>
      </div>


      <div class="sel_timeanddate">
          <h3>
              Date / Time
          </h3>
          <div class="timeanddate">
              <div class="date_sec">
                  <label for="date_sec">Date</label>
                  <input type="date" id="date_sec" name="date_sec" min="<?php echo $min_date; ?>" required value="<?php echo $check_date_sec; ?>" />
              </div>
              <div class="time_sec">
                  <label for="sel_time">Time</label><br>
                  <!--<input type="time" id="sel_time" name="sel_time" required />-->
				  <select class="InputText" id="sel_time" name="sel_time" required>

                    <?php if($check_sel_time!=NULL){
                        ?>
                            <option value="<?php echo $check_sel_time ?>"><?php echo $check_sel_time ?></option>
                        <?php
                    } else {
                        ?>
                            <option value="">Select</option>
                        <?php
                    }
                    ?>
				  </select>
              </div>
          </div>

      </div>
  </div>
  <div class="sub-btn">
    <!-- <a href="https://cravecupcakstg.wpengine.com/cupcakes/"> <button id="delivery-form-btn">Shop Now</button></a> -->
    <?php 

        if(is_shop() || is_product() || is_product_category() || is_checkout() || is_cart() || is_page('packaging-and-decorations') ){
            global $wp;
            // wp_redirect( $_POST['current_url'] );
            $redirect_url = home_url( $wp->request );
        }else{
            $redirect_url = '/shop-landing-page/';
        }
    
    ?>
    <input type="hidden" name="current_url" value="<?php echo $redirect_url; ?>" />

    
    <input type="hidden" name="cart_cat_ids" value="<?php echo $cart_cat_ids; ?>" />
    <input id="btn-shop-now" name="shop_now" type="submit" value="Shop Now">
  </div>
</form>

<script>
	
	/*var pickup_dropdown = '<option value"">Select</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="13:00">01:00 PM</option><option value="13:30">01:30 PM</option><option value="14:00">02:00 PM</option><option value="14:30">02:30 PM</option><option value="15:00">03:00 PM</option><option value="15:30">03:30 PM</option><option value="16:00">04:00 PM</option><option value="16:30">04:30 PM</option><option value="17:00">05:00 PM</option><option value="17:30">05:30 PM</option>';
	
	var delivery_dropdown = '<option value"">Select</option><option value="09:30 - 11:30">09:30 AM - 11:30 AM</option><option value="11:30 - 13:30">11:30 AM - 01:30 PM</option><option value="13:30 - 15:30">01:30 PM - 03:30 PM</option><option value="15:30 - 17:30">03:30 PM - 05:30 PM</option>';
	
	jQuery("#pickup").on("click", function(){
		
		jQuery("#sel_time").html(pickup_dropdown);
		
	});*/
	
	delivery_dropdown = "<option value=''>Select</option>";
	
	jQuery('input[name="order_type"]').on("click", function(){
		
		jQuery("#sel_time").html(delivery_dropdown);
		jQuery("#date_sec").val("");
		
	});
	
	jQuery('input[name="location"]').on("click", function(){
		
		jQuery("#sel_time").html(delivery_dropdown);
		jQuery("#date_sec").val("");
		
	});
	
	
	jQuery("#date_sec").on("change", function($){
		//alert(myAjax.ajaxurl);
		var selectedDate = jQuery(this).val();
		var orderType = jQuery('input[name="order_type"]:checked').val();
		var orderLocation = jQuery('input[name="location"]:checked').val();
		//$('input[name="name_of_your_radiobutton"]:checked').val();
		
		if( orderType!=null && orderLocation!=null ){
		
			jQuery.ajax({
				type : "POST",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {
					action: "wct_get_time_values",
					selected_date: selectedDate,
					order_type: orderType,
					order_location: orderLocation,
					},
				success: function(response) {
					console.log(response.same_day_error);

                    if(response.same_day_error != 'success'){
                        alert(response.same_day_error);
                    }
					jQuery("#sel_time").html(response.options_html);
					//alert(jQuery("#sel_time #message_id").attr('data-message'));
					//alert(response.error_message);
				}
			});
		
		} else {
			
			alert("Please Select Type and/or Location");
			jQuery("#date_sec").val("");
		}
	});

</script>

