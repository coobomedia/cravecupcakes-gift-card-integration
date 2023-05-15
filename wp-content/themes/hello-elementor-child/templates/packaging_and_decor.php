<?php
// Template Name: Available Toppers
get_header();

// Get current id
$term_id_acf = "product_cat_" . $_SESSION['current_cat_id'];

$check_topping = get_field('toppingdecoration', $term_id_acf);
if($check_topping != 1){
    header('Location: '. site_url() . '/cart');
}
$time_slots_wu = get_field('time_slots_wu_table', 'option');

/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/
 $uniqid = $_SESSION['group_cart'];
if(isset($_SESSION[$uniqid]['cats']["wp_".$_SESSION['current_cat_id']]['customize_topper'])){
	$customize_topper = $_SESSION[$uniqid]['cats']["wp_".$_SESSION['current_cat_id']]['customize_topper'];

	if($customize_topper=="Customize Topper"){
		unset($_SESSION[$uniqid]['cats']["wp_".$_SESSION['current_cat_id']]['customize_topper']);
		unset($_SESSION[$uniqid]['cats']["wp_".$_SESSION['current_cat_id']]['decor_type']);
		unset($_SESSION[$uniqid]['cats']["wp_".$_SESSION['current_cat_id']]['decor_color_1']);
		unset($_SESSION[$uniqid]['cats']["wp_".$_SESSION['current_cat_id']]['decor_color_input_1']);
		unset($_SESSION[$uniqid]['cats']["wp_".$_SESSION['current_cat_id']]['decor_color_2']);
		unset($_SESSION[$uniqid]['cats']["wp_".$_SESSION['current_cat_id']]['decor_color_input_2']);
		unset($_SESSION[$uniqid]['cats']["wp_".$_SESSION['current_cat_id']]['decor_input_image_2']);
	}
}



/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/

	$total_qty = 0;

    global $woocommerce;
    $items = $woocommerce->cart->get_cart();
$term_id = 0;
    foreach($items as $item => $values) { 
        $cat_ids = get_the_terms ( $values['data']->get_id(), 'product_cat' );
        foreach($cat_ids as $cat_id){
            $term_id = $cat_id->term_id;
            $nextgroupID = wc_get_order_item_meta( $values['data']->get_id(), 'group_cart', true );

            if($_SESSION['current_cat_id'] == $cat_id->term_id){
 
                if($nextgroupID == $uniqid)
                $total_qty += $values['quantity']; //print_r($cart_items); 
                
            }
        }
    } 
     $packaging_opt = get_field('packaging_opt',   'product_cat_' . $term_id);
     $toppingdecoration = get_field('toppingdecoration',   'product_cat_' . $term_id);

    //$total_qty = $cats_qty[$_SESSION['current_cat_id']]['qty'];



?>


<div id="p_and_d">
    <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST" style="background: #463A2F;">
    <input type="hidden" name="action" value="add_foobar">
        <div class="p-and-d">
            <div class="p_and_d_box">
                <?php if( $packaging_opt == 1){
?>
            
                <div class="packaging">
                    <h2>Select Packaging</h2>
                    <div class="packaging_opts">
                        <div class="option">
                            
                            <input type="radio" value="standard_box" class="" id="standard_box" name="packaging_opts" 
                                <?php 
                                    if(isset($_SESSION['packaging']) && $_SESSION['packaging'] == 'standard_box'){ 
                                        echo ' checked="checked"'; 
                                    } 
                                    if(!isset($_SESSION['packaging']))
                                    { 
                                        echo 'checked="checked"'; 
                                    } 
                                ?>
                            />
                            <label class="label postField restict-label" id="standard_box" for="standard_box">
                                <span class="input-radio-faux "></span>Standard Box</label>

                            <!-- <input type="checkbox" name="standard_box" id="standard_box" />
                            <label for="standard_box">Standard Box</label> -->
                        </div>
                        <div class="option">
                            <input type="radio" value="individual_box" class="" id="individual_box" name="packaging_opts" 
                            <?php 
                                if(isset($_SESSION['packaging']) && $_SESSION['packaging'] == 'individual_box'){ 
                                    echo 'checked="checked"'; 
                                } ?>
                            />
                            <label class="label postField restict-label" id="individual_box" for="individual_box">
                                <span class="input-radio-faux"></span><span >Individually Boxed</label>

                            <!-- <input type="checkbox" name="individual_box" id="individual_box" />
                            <label for="individual_box">Individually Boxed </label> -->
                            <p class="additional-box">(Additional $1/box)</p>
                        </div>
                    </div>

                </div>
                  <?php  }else if($packaging_opt == 2){ 
                    
                    echo "<style>
                    input[type=radio]:disabled + label:before {
                        transform: scale(1);
                        border-color: #e439456b;
                        background: #e439456b;
                       
                    }
                    </style>";

                    ?>

                    <div class="packaging">
                    <h2><span style=" color:#e439456b;">Select Packaging</span></h2>
                    <div class="packaging_opts">
                        <div class="option">
                            
                            <input type="radio" disabled value="standard_box" class="" id="standard_box" name="packaging_opts" />
                            <label class="label postField" id="standard_box" for="standard_box">
                                <span class="input-radio-faux"></span><span style=" color:#e439456b;">Standard Box</span></label>

                            <!-- <input type="checkbox" name="standard_box" id="standard_box" />
                            <label for="standard_box">Standard Box</label> -->
                        </div>
                        <div class="option">
                            <input type="radio" disabled value="individual_box" class="" id="individual_box" name="packaging_opts"  />
                            <label class="label postField" id="individual_box" for="individual_box">
                                <span class="input-radio-faux"></span><span style=" color:#e439456b;">Individually Boxed</span></label>

                            <!-- <input type="checkbox" name="individual_box" id="individual_box" />
                            <label for="individual_box">Individually Boxed </label> -->
                            <p class="additional-box">(Additional $1/box)</p>
                        </div>
                    </div>

                </div>



              <?php    } ?>
                <?php
					
					
					$qty_check = 1;
                   
                     $total_qty;
					if($total_qty<12){
						
						if(isset($_SESSION['current_cat_id']) && $_SESSION['current_cat_id'] != 35){
							$qty_check = 0;
						} else {
							$qty_check = 1;
						}
						
					}
					
					//echo $qty_check;
				
				?>

                <div class="decorations">
                    <h2>Would you like to Customize Your Topper?</h2>
                    <div class="decorations-details-decor">
                        
                        <p class="subheading" style="color:<?php echo (!$qty_check || $_SESSION['date_sec'] == date('Y-m-d', time())) ? '#E43945' : '#55B7B3'; ?>;">Requires Minimum of 1 dozen. Not Available for Same Day Orders. </p>
                        <p class="description">Without special decorations your cupcakes will come with the standard Crave design. Not available on Sugar-free, gluten-free, or Vegan Flavors</p>
                    </div>

                    <?php
					
                    if( !$qty_check || $_SESSION['date_sec'] == date('Y-m-d', time()) || $toppingdecoration != 1  ) {

                        echo "<style>
                        input[type=checkbox]:disabled + label:before {
                            transform: scale(1);
                            border-color: #e439456b;
                            background: #e439456b;
                        }
                        </style>";
                        
                    }
                    
                    ?>
                    <input <?php echo (!$qty_check || $_SESSION['date_sec'] == date('Y-m-d', time()) || $toppingdecoration != 1 ) ? 'disabled' : 'enabled'; ?> type="checkbox" id='customize_topper' name="customize_topper" value='Customize Topper'/>
                    <label  style="color: <?php echo (!$qty_check) ? '#E43945' : '#55B7B3'; ?>" for="customize_topper">Customize Topper</label>
                    <div class="decorations_opt" style="visibility: hidden; margin-top: 20px;">
                        <div class="decor_options">

                            <div class="decor_option_1 decor_grid">
                                <div class="decor_option">
                                    <?php // $types_unique = array_unique($types); ?>
									<?php $decor_type = get_field("_decor_type", "option") ?> 
                                    <select name="decor_type" id="decor_type">
                                        <option value="" selected> -- Select Type -- </option>
                                        <?php foreach ($decor_type as $type) { ?>
                                            <option data-image="<?php echo $type['decor_image']['url']; ?>" value="<?php echo $type['type_label']; ?>">
												<?php echo $type['type_label']; ?>
											</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="decor_image">
                                </div>
                            </div>
                                            
                            <div class="decor_option_2 decor_grid">
                                <div class="decor_option">
									<?php $decoration_colors = get_field('decoration_colors', 'option'); ?>
                                    <select name="decor_color_1" id="decor_color_1" class="decor_color select_color_1">
                                        <option value="" selected> -- Select Color 1 -- </option>
                                        <?php foreach ($decoration_colors as $key => $color) { ?>
                                            <option data-color="<?php echo $color['color'];?>" value="<?php echo $color['label']; ?>"><?php echo $color['label']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="decor_image">
									<div class="special-deco-bx" style="min-height: auto">
										<div data-kind="image_included" class="clonecake add_color_1" id="cake_deco_base" style="float: left; width: 50px;height: 50px;position: relative;">
										  <img src="/wp-content/uploads/2022/10/base.png" class="cakeimages" style="position: absolute; top: 0; left: 0; max-width: 100%" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
										  <div id="cake_deco_top" class="add_color_1" style="width: 35.0px;height: 35.0px;position: absolute;top: 8.0px;left: 8.0px;border-radius: 45.0px">
											<img src="" class="cakeimages changeimage_cupcake" style="width: 30.0px; height: 30.0px; position: absolute; top: 3.0px; left: 3.0px;" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
										  </div>
										</div>
										
									</div>
                                    <!--<img id="decor_image_1" src="<?php //echo $decoration_array[$types[0]][$colors[0]]; ?>" alt="" />-->
                                    <input type="hidden" id="decor_image_input_1" name="decor_input_image_1" value="" />
                                    <input type='hidden' id='current_cat_id' name="current_cat_id" value='<?php echo $_SESSION['current_cat_id']; ?>' />
                                    <?php //unset($_SESSION['current_cat_id']); ?>
                                </div>
                            </div>

                            <div class="decor_option_3 decor_grid">
                                <div class="decor_option">
                                    <select name="decor_color_2" id="decor_color_2" class="decor_color select_color_2">
										<option selected> -- Select Color 2 -- </option>
                                        <?php foreach ($decoration_colors as $key => $color) { ?>
                                            <option data-color="<?php echo $color['color'];?>" value="<?php echo $color['label']; ?>"><?php echo $color['label']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                          
									<div class="special-deco-bx" style="min-height: auto">
									
										<div data-kind="image_included" class="clonecake add_color_2" id="cake_deco_base_2" style="float: left; width: 50px;height: 50px;position: relative;">
										  <img src="/wp-content/uploads/2022/10/base.png" class="cakeimages" style="position: absolute; top: 0; left: 0; max-width: 100%" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
										  <div id="cake_deco_top_2" class="add_color_2" style="width: 35.0px;height: 35.0px;position: absolute;top: 8.0px;left: 8.0px;border-radius: 45.0px">
											<img src="" class="cakeimages changeimage_cupcake" style="width: 30.0px; height: 30.0px; position: absolute; top: 3.0px; left: 3.0px;" onerror="this.src = '/wp-content/uploads/2022/10/base.png'">
										  </div>
										</div>
										
									</div>
                                    <!--<img id="decor_image_2" src="<?php //echo $decoration_array[$types[0]][$colors[0]]; ?>" alt="" />-->
                                    
                                    <input type="hidden" id="decor_image_input_2" name="decor_input_image_2" value="" />
									<input type="hidden" id="decor_color_input_1" name="decor_color_input_1" value="" />
									<input type="hidden" id="decor_color_input_2" name="decor_color_input_2" value="" />
                                    <input type="hidden" id="decor_color_code_1" name="decor_color_code_1" value="" />
                                    <input type="hidden" id="decor_color_code_2" name="decor_color_code_2" value="" />									
                                
                            </div>

                        </div>
                    </div>
                    <div class="sub-btn">
                        <!-- <a href="/cart"> <button id="btn-pkg-decor">Shop Now</button></a> -->
                        <input id="btn-pkg-decor" name="add_to_cart" type="submit" value="Add To Cart">
                    </div>
                </div>

    </form>
</div>
</div>



<div class="available_toppers_wrapper" style="visibility: hidden;">
    <div class="available_toppers">
        <?php echo do_shortcode('[elementor-template id="686"]') ?>
    </div>
</div>
</div>

<?php get_footer(); ?>