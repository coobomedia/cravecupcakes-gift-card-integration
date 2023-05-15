<?php 

if (function_exists('acf_add_options_page')) {

	acf_add_options_page(array(
		'page_title' 	=> 'Toppings Settings',
		'menu_title'	=> 'Topping Settings',
		'menu_slug' 	=> 'topping-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

    
	// acf_add_options_page(array(
	// 	'page_title'    => 'Time Management',
	// 	'menu_title'    => 'Time Management',
	// 	'menu_slug'     => 'time-manangement',
	// 	'capability'    => 'edit_posts',
	// 	'redirect'      => false
	// ));
	
	// acf_add_options_sub_page(array(
	// 	'page_title'    => 'Block Preorder Time Slots',
	// 	'menu_title'    => 'Block Preorder Time Slots',
	// 	'parent_slug'   => 'time-manangement',
	// ));
	
	// acf_add_options_sub_page(array(
	// 	'page_title'    => 'Theme Footer Settings',
	// 	'menu_title'    => 'Footer',
	// 	'parent_slug'   => 'theme-general-settings',
	// ));

	acf_add_options_page(array(
        'page_title'    => 'Time Management',
        'menu_title'    => 'Time Management',
        'menu_slug'     => 'time-management',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
    
    acf_add_options_sub_page(array(
        'page_title'    => 'Block Preorder Time Slots',
        'menu_title'    => 'Block Preorder Time Slots',
        'parent_slug'   => 'time-management',
    ));

	acf_add_options_sub_page(array(
        'page_title'    => 'Production Windows',
        'menu_title'    => 'Production Windows',
        'parent_slug'   => 'time-management',
    ));

	// acf_add_options_sub_page(array(
    //     'page_title'    => 'The Woodlands',
    //     'menu_title'    => 'The Woodlands',
    //     'parent_slug'   => 'time-management',
    // ));
    
    // acf_add_options_sub_page(array(
    //     'page_title'    => 'Theme Footer Settings',
    //     'menu_title'    => 'Footer',
    //     'parent_slug'   => 'time-management',
    // ));
		
}







