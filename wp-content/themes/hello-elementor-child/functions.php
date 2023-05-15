<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
// WP Head and other cleanup functions
require_once(get_stylesheet_directory() . '/functions/woo-functions.php');
require_once(get_stylesheet_directory() . '/functions/acf-page-option.php');
require_once(get_stylesheet_directory() . '/functions/bakesheet-functions.php');
require_once(get_stylesheet_directory() . '/backend-functions/main-functions.php');
require_once(get_stylesheet_directory() . '/backend-functions/user-role-cap.php');
require_once(get_stylesheet_directory() . '/backend-functions/backsheet-items.php');

add_action('init', 'my_script_enqueuer');

function my_script_enqueuer()
{
    wp_register_script("wct-custom-script", get_stylesheet_directory() . '/assets/js/custom-script.js', array('jquery'));
    wp_localize_script('wct-custom-script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_enqueue_script('jquery');
    wp_enqueue_script('wct-custom-script');

}


// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if (!function_exists('chld_thm_cfg_locale_css')) :
    function chld_thm_cfg_locale_css($uri)
    {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css')) {
            $uri = get_template_directory_uri() . '/rtl.css';
        }

        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')) :
    function child_theme_configurator_css()
    {
        wp_enqueue_style('chld_thm_cfg_child', trailingslashit(get_stylesheet_directory_uri()) . 'style.css', array(
            'hello-elementor',
            'hello-elementor',
            'hello-elementor-theme-style'
        ));
    }
// wp_enqueue_script( 'script-custom', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/custom-script.js', array('jquery'), '1.0.0', true );
endif;


add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 10);


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('script-custom', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/custom-script.js', array('jquery'), '1.0.3', true);
});
// END ENQUEUE PARENT ACTION

add_action('wp_ajax_add_to_cart_using_json', 'add_to_cart_using_json_callback');
add_action('wp_ajax_nopriv_add_to_cart_using_json', 'add_to_cart_using_json_callback');

function add_to_cart_using_json_callback()
{

	$_SESSION['test_value'] = "abc";

    global $woocommerce;

    $json_data = $_POST['str_json'];

    $total_qty = 0;

    // $uniqid = 'group_' . uniqid();
    $_SESSION['group_cart'] = 'group_' . time();

    $uniqid = $_SESSION['group_cart'];
    // unset($_SESSION['group_carts']);
    $products = json_decode(stripslashes($json_data), true);

    foreach ($products as $prod_id => $qty) {


        // unset($_SESSION['group_carts'][$prod_id]);

        $woocommerce->cart->add_to_cart($prod_id, $qty);
        $_SESSION['group_carts'][$prod_id] = $uniqid;

        // wc_add_order_item_meta($prod_id, 'bundle', $uniqid);
        wc_add_order_item_meta($prod_id, 'group_cart', $uniqid);
        wc_update_order_item_meta($prod_id, 'group_cart', $uniqid);

        $total_qty += $qty;
        // $availability_start_date = get_field('availability_start_date', $prod_id);
        //  $availability_end_date = get_field('availability_end_date', $prod_id);
    }



    echo $total_qty;

    //die();
}


// ajax call for updating packaging
function update_packaging_callback()
{

    //echo "We are here";

    $packaging_status = $_POST['packaging_status'];
    $packaging_term = $_POST['term_id'];
    $uniqid = $_POST['uniqid'];

    //echo $package_term;

    $_SESSION[$uniqid]['cats']['wp_' . $packaging_term]['packaging'] = $packaging_status;

    //echo $_SESSION['cats']['wp_'.$packaging_term]['packaging'];

    //die();

}


add_action('wp_ajax_update_packaging', 'update_packaging_callback');
add_action('wp_ajax_nopriv_update_packaging', 'update_packaging_callback');


// Ajax Call for getting Time Values in The Order Form
function wct_get_time_values_callback()
{

    // We will use a Dummy Data for Comments Explanation starting from here

    // Getting Current selected date from the calendar then the Orde post type Pickup/Deliver and then Order location. Then we will use these things to get values from the database //
    // Sample Selected date = 21/12/2022
    // Sample Order Type = Pickup
    // Sample Location = West University

    $selected_date = $_POST['selected_date'];
    $order_type = $_POST['order_type'];
    $order_location = $_POST['order_location'];

    $day = strtolower(date("l", strtotime($selected_date))); // Selected day by end user.

    // Current Time and Date

    $date_time_now = get_texas_current_time();
    // $date_time_now =  date('Y-m-d H:i:s');


    //echo $date_time_now."<br>";
    $date_now = date("Y-m-d", strtotime($date_time_now));

    $time_now = strtotime($date_time_now);

    //echo $date_now."<br>";
    //echo $selected_date."<br>";

    $occasional_product_id = array();

    $cart_items = WC()->cart->get_cart();
    foreach ($cart_items as $cart_item) {
        $pp_id = $cart_item['product_id'];
        $pp_category = wp_get_post_terms($pp_id, 'product_cat', array('fields' => 'ids'));

        if ($pp_category[0] == 33) {
            $occasional_product_id = $pp_id;
            //break;
        }
        // Do something with the category IDs
    }

    if (!empty($occasional_product_id)) {

        /*echo "<pre>";
        print_r($occasional_product_id);
        echo "</pre>";*/

        $one_post = get_post($occasional_product_id);
        $custom = get_post_custom($occasional_product_id);

        /*echo "<pre>";
        print_r($one_post);
        print_r($custom);
        echo "</pre>";*/

        $visibility_date = $custom['visibility_start_date'][0];
        $availability_start_date = $custom['availability_start_date'][0];
        $availability_end_date = $custom['availability_end_date'][0];

        $avl_date = strtotime($availability_start_date);
        $end_date = strtotime($availability_end_date);

        $sel_date = strtotime($selected_date);

        //echo $avl_date."<br>".$end_date."<br>".$sel_date."<br>";

        if ($sel_date < $avl_date || $sel_date >= $end_date ) {

            $same_day_error = "The Occasional Product is not available in the Selected Date, Please choose a different date";

        } else {
            $same_day_error = "";
        }
    }


    //echo $same_day_error;

    //exit;

    // Sample Check day = wednesday

    $check_day = $day;

    //echo $check_day."<br>";

    if ($order_location == "west_university") {
        $location = "wu";
    } else if ($order_location == "up_town_park") {
        $location = "utp";
    } else if ($order_location == "woodlands") {
        $location = "tw";
    }

    // Sample Field name from below code will be "tuesday_wu_pickup"; This is a custom field from which we will get the open and close time for that day //
    $field_name = $day . "_" . $location . "_" . $order_type;

	//echo $field_name."<br>";

    // Sample timing Response for below code is $timing['opening_time'] = "9:30 am" and $timing['close_time'] = "6:30 pm"
    $timing = get_field($field_name, 'option');

	/*echo "<pre>";
	print_r($timing);
	echo "</pre>";*/

    //echo $timing['opening_time']."<br>";
    //echo $timing['closing_time']."<br>";

    $opening_time = $timing['opening_time'];
    $closing_time = $timing['closing_time'];
    $open_time = strtotime($selected_date . " " . $opening_time); // Convert date and time to timestamp
    $close_time = strtotime($selected_date . " " . $closing_time); // Convert date and time to timestamp

	//echo date("h:i:s", $open_time);

    // Declare empty variable for generating Option Fields for Time Dropdown
    $output = "";


    // Sample field name for below field is "time_slots_wu_table" which will get array of Allowed items each day in every time slot. For example Monday 9:30AM to 11:30AM we have the limit of 10 items then if the number of orders exceeds 10 then that time will not be shown in the dropdown.

    $field_name_1 = "time_slots_" . $location . "_table";
    $production_window = get_field($field_name_1, "option");

    /*echo "<pre>";
    print_r($production_window);
    echo "</pre>";*/

    $header_array = array();
    foreach ($production_window['header'] as $key => $header) {
        if ($key >= 1) {
            $header_array[] = $header['c'];
        }
    }
    $production_array = array();
    foreach ($header_array as $key => $day) {
        $time_key = "";
        foreach ($production_window['body'] as $key2 => $time) {
            $time_key = $time[0]['c'];
            unset($time[0]);
            $day_key = $key + 1;
            $production_array[strtolower($day)][$time_key] = $time[$day_key]['c'];
        }
    }

    // 	Following function will give you array of for a sample day
    //
    //
    //  [09:30 AM - 11:30 AM] => 450
    //	[11:30 AM - 01:30 PM] => 300
    //	[01:30 PM - 03:30 PM] => 230
    //	[03:30 PM - 05:30 PM] => 300
    //	[05:30 PM - 07:30 PM] => 350
    //	[07:30 PM - 09:30 PM] => 300


    $production_window_day = $production_array[$check_day];

    /*echo "<pre>";
    print_r( $production_window_day );
    echo "</pre>";*/


    $total_current_items = get_bakesheet_data($order_location, $selected_date, $selected_date); // Get all items for the date range and Location regarding BakeSheet (Returns Array);

    //echo "<pre>";
    //print_r( $production_window_day );  // [9:30 - 11:30] => 1 // Allowed
    //print_r($total_current_items); 		// [11:00 AM - 11:30 AM] => 54 // Current Items
    //echo "</pre>";

    $new_slots = array();
    $new_slots['11:30 PM - 01:30 AM'] = "";
    $new_slots['01:30 AM - 03:30 AM'] = "";
    $new_slots['03:30 AM - 05:30 AM'] = "";
    $new_slots['05:30 AM - 07:30 AM'] = "";
    $new_slots['07:30 AM - 09:30 AM'] = "";
    $new_slots['09:30 AM - 11:30 AM'] = "";
    $new_slots['11:30 AM - 01:30 PM'] = "";
    $new_slots['01:30 PM - 03:30 PM'] = "";
    $new_slots['03:30 PM - 05:30 PM'] = "";
    $new_slots['05:30 PM - 07:30 PM'] = "";
    $new_slots['07:30 PM - 09:30 PM'] = "";
    $new_slots['09:30 PM - 11:30 PM'] = "";
    foreach ($total_current_items as $key => $tci) {
        $s1 = explode(" - ", $key);
        $current_orders_start_time = $s1[0];
        $current_orders_end_time = $s1[1];
        $current_value = $tci;

        //echo $current_orders_end_time."<br>".$current_orders_start_time."<br>".$current_value."<br><br>";
        if (strtotime($current_orders_start_time) >= strtotime("09:30 AM") && strtotime($current_orders_end_time) <= strtotime("11:30 AM")) {
            $new_slots['09:30 AM - 11:30 AM'] = 0 + (int)$new_slots['09:30 AM - 11:30 AM'] + (int)$current_value;
        }
        if (strtotime($current_orders_start_time) >= strtotime("11:30 AM") && strtotime($current_orders_end_time) <= strtotime("01:30 PM")) {
            $new_slots['11:30 AM - 01:30 PM'] = 0 + (int)$new_slots['11:30 AM - 01:30 PM'] + (int)$current_value;
        }
        if (strtotime($current_orders_start_time) >= strtotime("01:30 PM") && strtotime($current_orders_end_time) <= strtotime("03:30 PM")) {
            $new_slots['01:30 PM - 03:30 PM'] = 0 + (int)$new_slots['01:30 PM - 03:30 PM'] + (int)$current_value;
        }
        if (strtotime($current_orders_start_time) >= strtotime("03:30 PM") && strtotime($current_orders_end_time) <= strtotime("05:30 PM")) {
            $new_slots['03:30 PM - 05:30 PM'] = 0 + (int)$new_slots['03:30 PM - 05:30 PM'] + (int)$current_value;
        }
        if (strtotime($current_orders_start_time) >= strtotime("05:30 PM") && strtotime($current_orders_end_time) <= strtotime("07:30 PM")) {
            $new_slots['05:30 PM - 07:30 PM'] = 0 + (int)$new_slots['05:30 PM - 07:30 PM'] + (int)$current_value;
        }
    }

    /*echo "<pre>";
    print_r($new_slots);
    echo "</pre>";*/

    /*

    $new_slots array(
        [11:30 PM - 01:30 AM] =>
        [01:30 AM - 03:30 AM] =>
        [03:30 AM - 05:30 AM] =>
        [05:30 AM - 07:30 AM] =>
        [07:30 AM - 09:30 AM] =>
        [09:30 AM - 11:30 AM] =>
        [11:30 AM - 01:30 PM] => 13
        [01:30 PM - 03:30 PM] =>
        [03:30 PM - 05:30 PM] =>
        [05:30 PM - 07:30 PM] =>
        [07:30 PM - 09:30 PM] =>
        [09:30 PM - 11:30 PM] =>
    )
    */


    //echo $open_time."<br>".$close_time."<br>";
    //echo date("h:i A",$open_time)."<br>".date("h:i A",$close_time)."<br>";

    /*echo "<pre>";
    print_r($production_window_day);
    print_r($total_current_items);
    echo "</pre>";*/

    $field_name_1 = "block_time_slots_" . $location;
    $block_time_slot = get_field($field_name_1, "option");

    //echo $selected_date."<br>";

    $new_block_data = array();

    foreach ($block_time_slot as $key => $block_slot) {

        $slot_date = date("Y-m-d", strtotime($block_slot['date']));

        //echo $slot_date."<br>";
        //echo $block_slot['date']."<br><br>";

        if ($selected_date == $slot_date) {
            $new_block_data[] = $block_slot;
        }

    }

    /*echo "<pre>";
    print_r($new_block_data);
    echo "</pre>";*/

    /*

        [date] => 2022-11-13
        [start] => 1:00 pm
        [end] => 3:00 pm

    */

    //exit;

    if ($order_type == "pickup") {
        for ($i = $open_time; $i < $close_time ; $i += 1800) {  // start time
            // for ($i = $open_time; $i < ($close_time - 10800); $i += 1800) {  // start time


                $new_check_key = "";

            foreach ($new_slots as $key3 => $one_slot) {
                $s1 = explode(" - ", $key3);

                $current_orders_start_time = $s1[0];
                $current_orders_end_time = $s1[1];

                $current_value = $one_slot;

                if ((strtotime(date("h:i A", $i)) >= strtotime($current_orders_start_time) && (strtotime(date("h:i A", $i)) < strtotime($current_orders_end_time)))) {
                    $new_check_key = $key3;
                    break;
                }

            }  // We have a Check Key from where the time window will start (We have to add the time frames before)

            //echo date("h:i A",$i)." ".$i."<br>";
            //echo date("h:i A", $i+1800)." ".($i+1800)."<br><br>";

            $block_start1 = strtotime($new_block_data[0]['date'] . " " . $new_block_data[0]['start']);
            $block_end1 = strtotime($new_block_data[0]['date'] . " " . $new_block_data[0]['end']);

            $block_start2 = strtotime($new_block_data[1]['date'] . " " . $new_block_data[1]['start']);
            $block_end2 = strtotime($new_block_data[1]['date'] . " " . $new_block_data[1]['end']);

            //echo date("h:i A", $block_start)." ".$block_start."<br>";
            //echo date("h:i A", $block_end)." ".$block_end."<br><br><hr>";

            $s = $i + 1800;

            //echo $s."<br>";
            //echo $block_end."<br>";


            if (($i >= $block_start1 && $s <= $block_end1) || ($i >= $block_start2 && $s <= $block_end2)) {
                //echo "True <br>";
            } else {

                $check_key = date("h:i A", $i) . " - " . date("h:i A", $i + 1800);

                $check = 0;

                if ($production_window_day[$new_check_key] > $new_slots[$new_check_key] && $production_window_day[$new_check_key] >= 1) {

                    // $date_now
                    // $selected_date

                    // check if the order is for today then disable time before now



                    if ($date_now == $selected_date) {

                        // if current time is greater than a time slot then do not show that slot.
                        if ($time_now < ($i - 10800) ) {

                            //echo "We are Here<br>";
                            $output .= "<option data-i='".date("d h:i A", $i)."' data-timenow ='".date("d h:i A", $time_now)."' value='" . date("h:i A", $i) . " - " . date("h:i A", $i + 1800) . "'>" . date("h:i A", $i) . " - " . date("h:i A", $i + 1800) . "</option>";
                            //echo "If 1 <br>";
                            $check = 1;

                        }


                    } else {

                        //echo "We are Here<br>";
                        $output .= "<option data-key='else-d' value='" . date("h:i A", $i) . " - " . date("h:i A", $i + 1800) . "'>" . date("h:i A", $i) . " - " . date("h:i A", $i + 1800) . "</option>";
                        //echo "If 1 <br>";
                        $check = 1;

                    }

                } else {
                    //$output = "<option>Select</option>";
                }

                /*if(!isset($total_current_items[$new_check_key]) && $production_window_day[$new_check_key]>=1 && $check==0){
                    $output .= "<option a value='".date("h:i A",$i)." - ".date("h:i A", $i+1800)."'>".date("h:i A",$i)." - ".date("h:i A", $i+1800)."</option>";
                    //echo "If 2 <br>";
                }*/

            }


        }

    } elseif ($order_type == "delivery") {

        /*echo "<pre>";
        print_r($new_slots);
        echo "</pre>";

		echo "<pre>";
        print_r($production_window_day);
        echo "</pre>";*/

        //echo date("h:i A",$i)." - ".date("h:i A", $i+7200)."<br>";
        for ($i = $open_time; $i < $close_time ; $i += 7200) {

			//echo $open_time."<br>";
			//echo date("h:i A",$i)." - ".date("h:i A", $i+7200)."<br>";

            $block_start1 = strtotime($new_block_data[0]['date'] . " " . $new_block_data[0]['start']);
            $block_end1 = strtotime($new_block_data[0]['date'] . " " . $new_block_data[0]['end']);

            $block_start2 = strtotime($new_block_data[1]['date'] . " " . $new_block_data[1]['start']);
            $block_end2 = strtotime($new_block_data[1]['date'] . " " . $new_block_data[1]['end']);

            //echo date("h:i A", $block_start)." ".$block_start."<br>";
            //echo date("h:i A", $block_end)." ".$block_end."<br><br><hr>";

            $s = $i + 7200;

            if (($i >= $block_start1 && $s <= $block_end1) || ($i >= $block_start2 && $s <= $block_end2)) {
                //echo "True <br>";
            } else {

                $check_key = date("h:i A", $i) . " - " . date("h:i A", $i + 7200);

                //echo $check_key."<br>";
                //echo $production_window_day[$check_key]."<br>";
                //echo $total_current_items[$check_key]."<br>";

                //echo $check_key."<br>";

                //echo $production_window_day[$check_key].": ".$new_slots[$check_key]."<br>";
                //echo date("h:i A",$i)." - ".date("h:i A", $i+7200)."<br>";

                if ($production_window_day[$check_key] > $new_slots[$check_key]) {
                    //if( $i < $now) continue;
                    //echo "We are Here<br>";

                    if ($date_now == $selected_date) {

                        // if current time is greater than a time slot then do not show that slot.
                        if ($time_now < ($i - 10800)) {
                                if($close_time < ($i+7200))
                            		$output .= "<option data-check='True' data-closetime='".$close_time."' data-i='".$i."' value='" . date("h:i A", $i) . " - " . date("h:i A", $close_time ) . "'>" . date("h:i A", $i) . " - " . date("h:i A", $close_time ) . "</option>";
                            	else
                            		$output .= "<option data-check='False' data-closetime='".$close_time."' data-i='".$i."' value='" . date("h:i A", $i) . " - " . date("h:i A", $i + 7200) . "'>" . date("h:i A", $i) . " - " . date("h:i A", $i + 7200) . "</option>";

                        }

                    } else {
                        //$output .= "<option value='" . date("h:i A", $i) . " - " . date("h:i A", $i + 7200) . "'>" . date("h:i A", $i) . " - " . date("h:i A", $i + 7200) . "</option>";
						if($close_time < ($i+7200))
                            		$output .= "<option data-check='True' data-closetime='".$close_time."' data-i='".$i."' value='" . date("h:i A", $i) . " - " . date("h:i A", $close_time ) . "'>" . date("h:i A", $i) . " - " . date("h:i A", $close_time ) . "</option>";
                            	else
                            		$output .= "<option data-check='False' data-closetime='".$close_time."' data-i='".$i."' value='" . date("h:i A", $i) . " - " . date("h:i A", $i + 7200) . "'>" . date("h:i A", $i) . " - " . date("h:i A", $i + 7200) . "</option>";
                    }


                }

                /*if(!isset($production_window_day[$check_key])){
                    $output .= "<option value='".date("h:i A",$i)." - ".date("h:i A", $i+7200)."'>".date("h:i A",$i)." - ".date("h:i A", $i+7200)."</option>";
                }*/

            }

        }
    }

    //$output.="<option id='message_id' data-message='we have an error' disabled>No</option>";

    $data = [];


    if ($selected_date == date('Y-m-d', time())) {
        $data['same_day_error'] = "Customized toppers are not available for same day orders. Cupcakes will come with the standard Crave design";

        //$data['destroy_cats'] = destroy_decorations();

    } else {
        $data['same_day_error'] = "success";
    }

    if ($same_day_error != null) {
        $data['same_day_error'] = $same_day_error;
        $data['options_html'] = "";
    } else {
        $data['options_html'] = $output;
    }


    $data['error_message'] = "We have an error";

    echo json_encode($data);


    die();

}

add_action('wp_ajax_wct_get_time_values', 'wct_get_time_values_callback');
add_action('wp_ajax_nopriv_wct_get_time_values', 'wct_get_time_values_callback');


// Ajax Call for updating Decoration form Cart in SESSION Variables

function update_session_values_cart_decoration_callback()
{
    $uniqid = $_POST['uniqid'];


    $_SESSION[$uniqid]['cats']["wp_" . $_POST['category_id']]['decor_type'] = stripslashes($_POST['decor_type']);
    $_SESSION[$uniqid]['cats']["wp_" . $_POST['category_id']]['decor_color_input_1'] = $_POST['color_1'];
    $_SESSION[$uniqid]['cats']["wp_" . $_POST['category_id']]['decor_color_input_2'] = $_POST['color_2'];

    $_SESSION[$uniqid]['cats']["wp_" . $_POST['category_id']]['decor_color_1'] = $_POST['color_code_1'];
    $_SESSION[$uniqid]['cats']["wp_" . $_POST['category_id']]['decor_color_2'] = $_POST['color_code_2'];


    $_SESSION[$uniqid]['cats']["wp_" . $_POST['category_id']]['decor_input_image_2'] = $_POST['image'];


    echo "<pre>";
    print_r($_SESSION);
    echo "<pre>";

    die();
}


add_action('wp_ajax_update_session_values_cart_decoration', 'update_session_values_cart_decoration_callback');
add_action('wp_ajax_nopriv_update_session_values_cart_decoration', 'update_session_values_cart_decoration_callback');


function customize_topper_callback()
{

    $topper_status = $_POST['topper_status'];
    $term_id = $_POST['term_id'];
    $uniqid = $_POST['uniqid'];



    //echo $cat_id. "|" . $term_id;

    //alog('$_SESSION', $_SESSION, __FILE__, __LINE__);


    if ($topper_status == null || $topper_status == '') {
        // $uniqid = $_SESSION['group_cart'];

        unset($_SESSION['cats'][$uniqid]["wp_" . $term_id]['decor_type']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $term_id]['decor_color_1']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $term_id]['decor_color_input_1']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $term_id]['decor_color_2']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $term_id]['decor_color_input_2']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $term_id]['decor_input_image_2']);

        //alog('if k andar', $_SESSION, __FILE__, __LINE__);
    }


    /*echo "<pre>";
    print_r($_SESSION);
    echo "<pre>";*/

    die();
}

add_action('wp_ajax_update_customize_topper', 'customize_topper_callback');
add_action('wp_ajax_nopriv_update_customize_topper', 'customize_topper_callback');


function session_start_delivery_form()
{
    if (!session_id()) {
        session_start();
    }
    $current_time = time();
    // alog('SESSION', $_SESSION, __LINE__, __FILE__);
    // alog('current_time', $current_time, __LINE__, __FILE__);

    // Check if current 30 minutes passed and still the customer did't checkout then destroy current order date and time and ask him new delivery date and time

    if (isset($_SESSION['session_time']) && ($current_time >= $_SESSION['session_time'] + (60 * 30))) {
        $_SESSION['session_time'] = time();
        $uniqid = $_SESSION['group_cart'];

        unset($_SESSION['order_type']);
        unset($_SESSION['location']);
        unset($_SESSION['date_sec']);
        unset($_SESSION['sel_time']);
        unset($_SESSION[$uniqid]['packaging']);
    }
}

add_action('init', 'session_start_delivery_form');

function store_session_date_callback()
{
    global $woocommerce;

    /*echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    exit; */
    //alog('$_SESSION', $_SESSION, __LINE__, __FILE__);

    if (!session_id()) {
        session_start();
    }
    $base_url = site_url();
    // Popup form (Delivery Form)
    if (isset($_POST['order_type'])) {
        $uniqid = $_SESSION['group_cart'];

        $_SESSION['session_time'] = time();
        $_SESSION['order_type'] = $_POST['order_type'];
        $_SESSION['location'] = $_POST['location'];
        $_SESSION['date_sec'] = $_POST['date_sec'];
        $_SESSION['sel_time'] = $_POST['sel_time'];
        $_SESSION[$uniqid]['packaging'] = $_POST['packaging'];
        $_SESSION[$uniqid]['decor_type_'] = $_POST['decor_type'];
        if ($_POST['date_sec'] == date('Y-m-d', time())) {
            destroy_decorations($_POST['cart_cat_ids']);
        }

		//echo $_POST['current_url'];

		if(strpos($_POST['current_url'], "order-received")){
			//echo "Yes";
			wp_redirect($base_url . "/shop-landing-page");
		} else {
			wp_redirect($_POST['current_url']);
		}

		//exit;

		exit;

    }
    // Decoration and Packaging form
    if (isset($_POST['decor_type'])) {
        $uniqid = $_SESSION['group_cart'];
         $_SESSION[$uniqid]['cats']["wp_" . $_POST['current_cat_id']]['packaging'] = $_POST['packaging_opts'];

        if ($_POST['decor_type'] != null) {
            $uniqid = $_SESSION['group_cart'];
            $_SESSION[$uniqid]['decor_type'] = $_POST['decor_type'];
            $_SESSION[$uniqid]['decor_color_1'] = $_POST['decor_color_1'];
            $_SESSION[$uniqid]['decor_color_2'] = $_POST['decor_color_2'];
            $_SESSION[$uniqid]['decor_input_image_1'] = $_POST['decor_input_image_1'];
            $_SESSION[$uniqid]['decor_input_image_2'] = $_POST['decor_input_image_2'];
            $_SESSION[$uniqid]['decor_type_'] = $_POST['decor_type'];
            // echo "<pre>";
            // print_r($uniqid);
            // echo "</pre>";

            //if(isset($_POST['packaging_opts'])){


            $_SESSION[$uniqid]['cats']["wp_" . $_POST['current_cat_id']]['decor_type'] = $_POST['decor_type'];
            $_SESSION[$uniqid]['cats']["wp_" . $_POST['current_cat_id']]['decor_color_1'] = $_POST['decor_color_code_1'];
            $_SESSION[$uniqid]['cats']["wp_" . $_POST['current_cat_id']]['decor_color_input_1'] = $_POST['decor_color_input_1'];
            $_SESSION[$uniqid]['cats']["wp_" . $_POST['current_cat_id']]['decor_color_2'] = $_POST['decor_color_code_2'];
            $_SESSION[$uniqid]['cats']["wp_" . $_POST['current_cat_id']]['decor_color_input_2'] = $_POST['decor_color_input_2'];
            //$_SESSION['cats']["wp_".$_POST['current_cat_id']]['decor_input_image_1'] = $_POST['decor_input_image_1'];
            $_SESSION[$uniqid]['cats']["wp_" . $_POST['current_cat_id']]['decor_input_image_2'] = $_POST['decor_input_image_2'];
            $_SESSION[$uniqid]['cats']["wp_" . $_POST['current_cat_id']]['customize_topper'] = $_POST['customize_topper'];
        }

        wp_redirect($base_url . "/cart");

        //}

    }


    exit;
}

//add_action('wp_head', 'store_session_date_callback');

add_action('admin_post_add_foobar', 'store_session_date_callback');

//this next action version allows users not logged in to submit requests
//if you want to have both logged in and not logged in users submitting, you have to add both actions!
add_action('admin_post_nopriv_add_foobar', 'store_session_date_callback');


function destroy_decorations($cart_cat_ids)
{

    //return true;

    //global $woocommerce;

    $return = array();

    /*echo "<pre>";
    print_r($woocommerce);
    echo "</pre>";

    exit;*/

    $cart_ids = explode(",", $cart_cat_ids);


    //return true;

    foreach ($cart_ids as $cart_item) {
        //return true;
        $uniqid = $_SESSION['group_cart'];

        unset($_SESSION['cats'][$uniqid]["wp_" . $cart_item]['decor_type']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $cart_item]['decor_color_input_1']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $cart_item]['decor_color_input_2']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $cart_item]['decor_color_1']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $cart_item]['decor_color_2']);
        unset($_SESSION['cats'][$uniqid]["wp_" . $cart_item]['decor_input_image_2']);

        //$return[] = wc_get_product_category_list( $cart_item['product_id'] );
        $return[] = $cart_item;
    }

    return $return;

    //unset($_SESSION['cats']["wp_".$_POST['category_id']]['decor_type']);
    //unset($_SESSION['cats']["wp_".$_POST['category_id']]['decor_color_input_1']);
    //unset($_SESSION['cats']["wp_".$_POST['category_id']]['decor_color_input_2']);
    //unset($_SESSION['cats']["wp_".$_POST['category_id']]['decor_color_1']);
    //unset($_SESSION['cats']["wp_".$_POST['category_id']]['decor_color_2']);
    //unset($_SESSION['cats']["wp_".$_POST['category_id']]['decor_input_image_2']);

}

/*function prefix_admin_add_foobar() {
    status_header(200);
    //request handlers should exit() when they complete their task
    exit("Server received '{$_REQUEST['data']}' from your browser.");
}*/


function add_javascript_in_footer_callback()
{
    $total_qty = 0;
    foreach (WC()->cart->get_cart() as $cart_item) {
        // gets the cart item quantity
        $quantity = $cart_item['quantity'];
        $total_qty += $quantity;
    }

    ?>
    <script>

        jQuery(document).ready(function () {
            jQuery("#total_cupcakes h2").html('<?php echo $total_qty; ?>');
        });

    </script>
    <?php

    if (is_product_category()) {
        ?>

        <input type='hidden' id='current_cat_id' value='<?php echo get_queried_object()->term_id; ?>'/>

        <?php

        $_SESSION['current_cat_id'] = get_queried_object()->term_id; // get current category id from the category page.
    }

    $cart_count = WC()->cart->get_cart_contents_count();

    if ($cart_count >= 1) {
        // column header-start-container
        // header-cart-icon - cart count
        ?>
        <style>
            #start-order {
                display: none;
            }

            #cart-icon {
                display: flex;
            }
        </style>
        <?php
        //echo "<style>#header-cart-icon{display: block !important;} #header-start-container {display:none !important;} </style>";

    } else {
        ?>

        <style>
            #start-order {
                display: block;
            }

            #cart-icon {
                display: none;
            }
        </style>
        <?php
        //echo "<style>#header-cart-icon{display: none !important;} #header-start-container {display:block;} </style>";

    }


}

add_action('wp_footer', 'add_javascript_in_footer_callback');


///////////////////////// Set Texax Time ///////////////////////
function get_texas_current_time()
{

    // Texas Current Time

    $date = new DateTime("now", new DateTimeZone('America/Monterrey')); // Set default time to Texas America
    //return $date->format('Y-m-d H:i:s');

    // Temporary Time Change to Pakistan Time

    //$date = new DateTime("now", new DateTimeZone('Asia/Karachi') ); // Set default time to Pakistan
    return $date->format('Y-m-d H:i:s');

}

// Popup Delivery Shortcode ////


function devlivery_form_1_callback()
{
    ob_start();
    get_template_part('template-parts/delivery', 'form-1');

    return ob_get_clean();
}

add_shortcode('delivery-form-1-popup', 'devlivery_form_1_callback');


add_action('admin_footer', 'my_admin_add_js');
function my_admin_add_js()
{

    $user = wp_get_current_user();
    if (in_array('employee', (array)$user->roles) || in_array('manager', (array)$user->roles)) {
        ?>
        <style>
            li#toplevel_page_topping-settings, li#toplevel_page_time-management {
                display: none;
            }
        </style>
        <?php
    }
}


function cravecupcakes_display_popup_info()
{
    ob_start();

	if(!is_wc_endpoint_url( 'order-received' )){

		?>
		<h3 style="font-family: 'Montserrat', Sans-serif; font-size: 18px; font-weight: 600; line-height: 25px; margin: 0;">
			Order information</h3>
		<div id="display_popup_info">
			<ul class="order_info_checkout">
				<li><span><b>Order Type: </b> </span>
					<?php echo $_SESSION['order_type']; ?></li>
				<li><span><b>Locations:</b> </span><?php
					$str_replace = str_replace('_', ' ', $_SESSION['location']);

					if ($_SESSION['location'] == "up_town_park") {
						echo "Uptown Park";
					} else {
						echo $str_replace;
					}
					?></li>
				<li><span><b>Date:</b> </span><?php echo $_SESSION['date_sec']; ?></li>
				<li><span><b>Time:</b> </span><?php echo $_SESSION['sel_time']; ?></li>
			</ul>
			<a href="#order_info" id="order_info_btn">Edit Order Information</a>
		</div>
		<?php

	}

    return ob_get_clean();

}

add_shortcode('display_popup_info', 'cravecupcakes_display_popup_info');

add_action('admin_enqueue_scripts', 'crave_selectively_enqueue_admin_script');

function crave_selectively_enqueue_admin_script()
{
    wp_register_style('crave_wp_admin_css', get_stylesheet_directory_uri() . '/assets/css/admin-style.css', false, '1.0.0');
    wp_enqueue_style('crave_wp_admin_css');
}

/*Coupon code */
/**
 * Soka - Change 'coupon' text to 'voucher'
 * @source https://gist.github.com/maxrice/8551024
 */
function soka_rename_coupon_field_on_cart($translated_text, $text, $text_domain)
{
    // bail if not modifying frontend woocommerce text
//    if ( is_admin() || 'woocommerce' !== $text_domain ) {
//        return $translated_text;
//    }
if('Click here to enter your promo code' == $text){
    $translated_text = 'Click here to enter your code';
}
    if ('Coupon:' === $text) {
        $translated_text = 'Promo Code:';
    }

    if ('Coupon has been removed.' === $text) {
        $translated_text = 'Promo code has been removed.';
    }

    if ('Apply coupon' === $text) {
        $translated_text = 'Apply Promo';
    }

    if ('Coupon code' === $text) {
        $translated_text = 'Promo code';
    }

    if ('If you have a coupon code, please apply it below.' === $text) {
        $translated_text = 'If you have a Promo code, please apply it below.';
    }

    return $translated_text;
}

add_filter('gettext', 'soka_rename_coupon_field_on_cart', 10, 3);


/**
 * Soka - Change 'coupon' text to 'voucher'
 * @source https://gist.github.com/maxrice/8551024
 */
function soka_rename_coupon_label($err, $err_code = null, $something = null)
{
    $err = str_replace("Coupon", "Promo Code", $err);
    $err = str_replace("coupon", "Promo Code", $err);
    return $err;
}

add_filter('woocommerce_coupon_error', 'soka_rename_coupon_label', 10, 3);
add_filter('woocommerce_coupon_message', 'soka_rename_coupon_label', 10, 3);
add_filter('woocommerce_cart_totals_coupon_label', 'soka_rename_coupon_label', 10, 1);
add_filter('wp_mail_content_type', function ($content_type) {
    return 'text/html';
});

// Add custom column header of Cake/Bakeshop Items
// add_filter( 'manage_edit-product_columns', 'custom_product_column' );
// function custom_product_column( $columns ) {
//     $columns['cake_bakeshop'] = __( 'Cake/Bakeshop Items', 'textdomain' );
//     return $columns;
// }

// Populate custom column of Cake/Bakeshop Items
// add_action( 'manage_product_posts_custom_column', 'populate_custom_product_column', 10, 2 );
// function populate_custom_product_column( $column, $post_id ) {
//     if ( 'cake_bakeshop' === $column ) {
//         $cakebakeshop_items = get_post_meta( $post_id, 'cakebakeshop_items', true );
// 		$taxonomy = get_term( $cakebakeshop_items );
// 		$taxonomy_name = $taxonomy->name;
//         echo $taxonomy_name;
//     }
// }

//deselect checkout billing/shipping state
add_filter( 'default_checkout_billing_state', 'change_default_checkout_state' );
add_filter( 'default_checkout_shipping_state', 'change_default_checkout_state' );
function change_default_checkout_state() {
    return ''; //set state code if you want to set it otherwise leave it blank.
}

//add_filter( 'woocommerce_package_rates', 'remove_shipping_method', 10, 2 );
function remove_shipping_method( $rates, $package ) {
    $shipping_method_to_remove = 'flat_rate:1'; // Replace with the shipping method you want to remove

    if ( isset( $rates[ $shipping_method_to_remove ] ) ) {
        if ($_SESSION['order_type'] == 'delivery' ) {

            unset( $rates[ $shipping_method_to_remove ] );

         }
    }

    return $rates;
}


