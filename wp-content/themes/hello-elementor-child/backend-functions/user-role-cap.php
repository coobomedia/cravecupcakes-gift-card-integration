<?php 



//  //add caps to editor role
//  $role = get_role("editor");

//  //for woocommerce
//  $role->add_cap("manage_woocommerce");
//  $role->add_cap("view_woocommerce_reports");
//  $role->add_cap("edit_product");
//  $role->add_cap("read_product");
//  $role->add_cap("delete_product");
//  $role->add_cap("edit_products");
//  $role->add_cap("edit_others_products");
//  $role->add_cap("publish_products");
//  $role->add_cap("read_private_products");
//  $role->add_cap("delete_products");
//  $role->add_cap("delete_private_products");
//  $role->add_cap("delete_published_products");
//  $role->add_cap("delete_others_products");
//  $role->add_cap("edit_private_products");
//  $role->add_cap("edit_published_products");
//  $role->add_cap("manage_product_terms");
//  $role->add_cap("edit_product_terms");
//  $role->add_cap("delete_product_terms");
//  $role->add_cap("assign_product_terms");
//  $role->add_cap("edit_shop_order");
//  $role->add_cap("read_shop_order");
//  $role->add_cap("delete_shop_order");
//  $role->add_cap("edit_shop_orders");
//  $role->add_cap("edit_others_shop_orders");
//  $role->add_cap("publish_shop_orders");
//  $role->add_cap("read_private_shop_orders");
//  $role->add_cap("delete_shop_orders");
//  $role->add_cap("delete_private_shop_orders");
//  $role->add_cap("delete_published_shop_orders");
//  $role->add_cap("delete_others_shop_orders");
//  $role->add_cap("edit_private_shop_orders");
//  $role->add_cap("edit_published_shop_orders");
//  $role->add_cap("manage_shop_order_terms");
//  $role->add_cap("edit_shop_order_terms");
//  $role->add_cap("delete_shop_order_terms");
//  $role->add_cap("assign_shop_order_terms");
//  $role->add_cap("edit_shop_coupon");
//  $role->add_cap("read_shop_coupon");
//  $role->add_cap("delete_shop_coupon");
//  $role->add_cap("edit_shop_coupons");
//  $role->add_cap("edit_others_shop_coupons");
//  $role->add_cap("publish_shop_coupons");
//  $role->add_cap("read_private_shop_coupons");
//  $role->add_cap("delete_shop_coupons");
//  $role->add_cap("delete_private_shop_coupons");
//  $role->add_cap("delete_published_shop_coupons");
//  $role->add_cap("delete_others_shop_coupons");
//  $role->add_cap("edit_private_shop_coupons");
//  $role->add_cap("edit_published_shop_coupons");
//  $role->add_cap("manage_shop_coupon_terms");
//  $role->add_cap("edit_shop_coupon_terms");
//  $role->add_cap("delete_shop_coupon_terms");
//  $role->add_cap("assign_shop_coupon_terms");
//  $role->add_cap("edit_shop_webhook");
//  $role->add_cap("read_shop_webhook");
//  $role->add_cap("delete_shop_webhook");
//  $role->add_cap("edit_shop_webhooks");
//  $role->add_cap("edit_others_shop_webhooks");
//  $role->add_cap("publish_shop_webhooks");
//  $role->add_cap("read_private_shop_webhooks");
//  $role->add_cap("delete_shop_webhooks");
//  $role->add_cap("delete_private_shop_webhooks");
//  $role->add_cap("delete_published_shop_webhooks");
//  $role->add_cap("delete_others_shop_webhooks");
//  $role->add_cap("edit_private_shop_webhooks");
//  $role->add_cap("edit_published_shop_webhooks");
//  $role->add_cap("manage_shop_webhook_terms");
//  $role->add_cap("edit_shop_webhook_terms");
//  $role->add_cap("delete_shop_webhook_terms");
//  $role->add_cap("assign_shop_webhook_terms");

// remove_role( 'employee' );

 
// $role = 'employee';
// $display_name = "Employee";
// $capabilities = [
//     'read' => true, // true allows this capability
//     'edit_posts' => true, // Allows user to edit their own posts
//     'edit_pages' => true, // Allows user to edit pages
//     'edit_others_posts' => true, // Allows user to edit others posts not just their own
//     'create_posts' => true, // Allows user to create new posts
//     'manage_categories' => true, // Allows user to manage post categories
//     'publish_posts' => true, // Allows the user to publish, otherwise posts stays in draft mode
//     'edit_themes' => true, // false denies this capability. User can’t edit your theme
//     'install_plugins' => true, // User cant add new plugins
//     'update_plugin' => true, // User can’t update any plugins
//     'update_core' => true, // user cant perform core updates
//     'upload_files' => true
// ];

// add_role($role, $display_name, $capabilities);


// $author = get_role( 'employee' );

//     $caps = array (
//         // 'edit_posts',
//         // 'edit_published_posts',
//         // 'publish_posts',
//         // 'delete_posts',
//         'delete_published_posts',
//     );

//     foreach ( $caps as $cap ) {

//         $author->remove_cap( $cap );
// }

//Shop Mananger

if (current_user_can('shop_manager')) {
    function remove_admin_menus () { 
        global $menu; 
        $removed = array(
            __('Dashboard'), 
            __('Posts'), 
            __('Media'), 
            __('Links'), 
            __('Pages'), 
            __('Appearance'), 
            __('Tools'), 
            __('Users'), 
            __('Settings'), 
            __('Comments'), 
            __('Plugins'),
            __('Topping Settings'),
            __('WPHEKA'),
            __('Templates'),
            ); 
        end ($menu); 
        while (prev($menu)){ 
            $value = explode(
                    ' ',
                    $menu[key($menu)][0]); 
            if(in_array($value[0] != NULL?$value[0]:"" , $removed)){
                unset($menu[key($menu)]);
            }
        } 
    }
    add_action('admin_menu', 'remove_admin_menus');
    } 
    




