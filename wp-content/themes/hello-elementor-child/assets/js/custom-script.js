jQuery(document).ready(function($){
	
// $('.e-coupon-box').hide();	
$('.e-show-coupon-form').text('Click here to enter your code');
$('p.e-woocommerce-coupon-nudge.e-checkout-secondary-title').html('Have a Promo Code? <a href="#" class="e-show-coupon-form">Click here to enter your code</a>');


	// setTimeout(function(){
	// 	$('.e-coupon-box').show();

	// },1000)

    function incrementValue(e) {
        e.preventDefault();
        var fieldName = $(e.target).data('field');
        var parent = $(e.target).closest('div');
        var currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);
      
        if (!isNaN(currentVal)) {
          parent.find('input[name=' + fieldName + ']').val(currentVal + 1);
        } else {
          parent.find('input[name=' + fieldName + ']').val(0);
        }
      }
      var count = 0;
      // var qty = 0;
      // var qtyPlus = 0;
      function decrementValue(e) {
        e.preventDefault();
        var fieldName = $(e.target).data('field');
        var parent = $(e.target).closest('div');
        var currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);
      
        if (!isNaN(currentVal) && currentVal > 0) {
          parent.find('input[name=' + fieldName + ']').val(currentVal - 1);
        } else {
          parent.find('input[name=' + fieldName + ']').val(0);
        }
      }
      
      $('.quantity-field').on('change', '.quantity-field', function(e) {
        incrementValue(e);

        var qtyPlus = jQuery(this).parent().find(".quantity-field").val();
        var prod_id = jQuery(this).parent().find(".quantity-field").attr("data-id");
       // count = parseInt(qtyPlus) + parseInt(count);
        $('#testID123').remove();
        //var sum = parseInt(qtyPlus) + parseInt(qtyPlus);
        
        $('#count').append("<span id='testID123'>" + qtyPlus + "</span>");
       
        //let qty1 = document.getElementById("testID123").textContent;
        //console.log(qty);
        // count++;
        // $('#count').text(count);
        // console.log('plus', count);
        // var qty = parseInt(qtyPlus) + parseInt(qty);
        // console.log('qty', qty);
		
		
		
      });
      
      
    //  console.log('qtyPlus', qtyPlus);
    //   jQuery('.quantity-field').on('change', '.quantity-field', function(e) {
    //     decrementValue(e);
        
    //     var qty = jQuery(this).parent().find(".quantity-field").val();
    //     console.log(qty);
    //     var prod_id = jQuery(this).parent().find(".quantity-field").attr("data-id");
    //     $('#testID123').remove();
    //     $('#count').append("<span id='testID123'>" + qty + "</span>");
    //     // count--;
    //     // $('#count').text(count);
    //     // console.log('Minus',count);
        
        

    //   });
	  
	  order_info = {};
	  
	  
	  
	  
	  /*jQuery(".cart .minus").on("click", function(){
		 
		   var prod_id = jQuery(this).parent().parent().attr("data-id");
		   //alert(prod_id);
		   
		   var qty = jQuery(this).parent().parent().find('[name="quantity"]').val();
		   
		   if(qty>0){
			 
				qty=parseInt(qty)-1;  
				
				var current_qty = parseInt(jQuery('#count').html());
				
				//alert(current_qty);
				
				jQuery('#count').html(current_qty-1);
				
				order_info[prod_id] = qty;
				//order_info.splice("a" + prod_id, 0, qty);
			   
		   }
		   
		   
		   
		  // alert(qty);
		  
	  });*/
	  
	  /*jQuery(".cart .plus").on("click", function(){
		 
		   var prod_id = jQuery(this).parent().parent().attr("data-id");
		   //alert(prod_id);
		   
		   var qty = jQuery(this).parent().parent().find('[name="quantity"]').val();
			 
			qty=parseInt(qty)+1; 
			
			var current_qty = parseInt(jQuery('#count').html());
		   
		   jQuery('#count').html(current_qty+1);
		   
		   order_info[prod_id] = qty;
		   //order_info.splice("a" + prod_id, 0, qty);
		   
		   //alert(qty);
		   
	  });*/
		
		jQuery('[name="quantity"]').on("change", function(){
			
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
			
			
		});
		
		
		jQuery("#add_to_cart_process").on("click", function(){
			
			var cart = jQuery("#count").html();
	
			// if(cart!=0){
			// 	//alert(cart);
				
				
				
			// 	return true;
			// }
			
			//console.log(order_info);
			
			var json_data = JSON.stringify(order_info);
			
			$.ajax({
				type : "POST",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {
					str_json: json_data,
					action: "add_to_cart_using_json"
					},
				success: function(response) {
					
					if(response!=0){
						window.location.href = "/packaging-and-decoration";
					}


					
					//alert("Your vote could not be added");
					//alert(response);
				}
			});
			
			/*$.each( order_info, function( prod_id, qty ){
				//alert(prod_id + ": " + qty);
				
				
				
			});*/
			
		});

      jQuery('.cc-thumb').hover(function(e){
        jQuery(this).find('.cc-overlay').toggleClass('cc-overlay-active');
      });
      // Adding Quantity to cart button on shop page 


    //   jQuery(".qnt-btn-wrapper .button-minus").on('click', function(){
        

    //   });
    
	
	// jQuery("body").find('[name="quantity"]').val(0);

	
	// Decoration Change Function Start //


	Object.byString = function(o, s) {
		s = s.replace(/\[(\w+)\]/g, '.$1'); // convert indexes to properties
		s = s.replace(/^\./, '');           // strip a leading dot
		var a = s.split('.');
		for (var i = 0, n = a.length; i < n; ++i) {
			var k = a[i];
			if (k in o) {
				o = o[k];
			} else {
				return;
			}
		}
		return o;
	}


	
	
	
	// Decoration Change Function End //
	

	// For Decoration page
	$('#decor_type').on('change', function(){
		// get image from selected option
		var image = $(this).find(':selected').attr('data-image');
		$('.changeimage_cupcake').attr("src", image);


		$("input#decor_image_input_2").val(image);
	});

	// Adding Color 1
	$('.select_color_1').on('change', function(){
		var color_1 = $(this).find(':selected').attr('data-color');
		$('.add_color_1').css('background-color', color_1);


		var dataLabel = $(this).find(':selected').val();
		$('input#decor_color_input_1').val( dataLabel );

		$('input#decor_color_code_1').val(color_1);
	});
	// Adding Color 2
	$('.select_color_2').on('change', function(){
		var color_2 = $(this).find(':selected').attr('data-color');
		$('.add_color_2').css('background-color', color_2);

		var dataLabel = $(this).find(':selected').val();
		$('input#decor_color_input_2').val( dataLabel );

		$('input#decor_color_code_2').val(color_2);
	});

	// $('#decor_color_1').change(function(){
	// 	var color = jQuery(this).find(':selected').val();

	// 	$('#cake_deco_base').css('background-color', color);
	// 	$('#cake_deco_top').css('background-color', color);

	// 	var dataLabel = jQuery(this).find(':selected').attr('data-label');

	// 	$('input#decor_color_input_1').val( dataLabel );



		

		
	// 	// $('#cake_deco_top').html('<img src="'+ image +'" alt"" />');
	// 	// $('#cake_deco_top_1').html('<img src="'+ image +'" alt"" />');
	// });

	// $('#decor_color_2').change(function(){
	// 	var color_1 = jQuery(this).find(':selected').val();

	// 	$('#cake_deco_base_2').css('background-color', color_1);
	// 	$('#cake_deco_top_2').css('background-color', color_1);

	// 	var dataLabel = jQuery(this).find(':selected').attr('data-label');

	// 	$('input#decor_color_input_2').val( dataLabel );
		
	// });

	// Cart Page Decoration JS //



	jQuery('.cart-wrapper').on('change', '.decorations-cart .decor_type_wrapper select', function(){
		// get image from selected option
		var image = jQuery(this).find(':selected').attr('data-image');
        //alert(image);
        //console.log(image);
		jQuery(this).parent().parent().find('.changeImage').attr("src", image);

		//$("input#decor_image_input_2").val(image);
	});


	// jQuery('.decorations-cart .decor_type_wrapper select').change(function(){

    //     //alert("changed");
	// 	// get image from selected option
	// 	var image = jQuery(this).find(':selected').attr('data-image');
    //     //alert(image);
    //     //console.log(image);
	// 	jQuery(this).parent().parent().find('.changeImage').attr("src", image);

	// 	//$("input#decor_image_input_2").val(image);
	// });


	jQuery('.cart-wrapper').on('change', '.decorations-cart .color_1 select',function(e){
		e.preventDefault();
		var color_1 = jQuery(this).find(':selected').attr('data-color');
        jQuery(this).parent().parent().parent().find('.changeColor_1').css("background-color", color_1);
	});

	jQuery('.cart-wrapper').on('change', '.decorations-cart .color_2 select',function(e){
		e.preventDefault();
		var color_1 = jQuery(this).find(':selected').attr('data-color');
        jQuery(this).parent().parent().parent().find('.changeColor_2').css("background-color", color_1);
        
	});


	jQuery('#customize_topper').on('click', function(){
		if (!jQuery("[name='customize_topper']").is(':checked')) {
			jQuery('.decorations_opt').css('visibility', 'hidden');

			jQuery('.available_toppers_wrapper').css('visibility', 'hidden');
			

			
			
			jQuery("option:selected").prop("selected", false);
			jQuery('div#cake_deco_base img').attr('src', ''); // Clear the src
			jQuery('div#cake_deco_top_2 img').attr('src', ''); // Clear the src
			
			
			jQuery("div#cake_deco_top_2").css("background-color", "rgb(65 105 225 / 0%)"); 
			jQuery("div#cake_deco_base_2").css("background-color", "rgb(65 105 225 / 0%)"); 

			jQuery("div#cake_deco_base").css("background-color", "rgb(65 105 225 / 0%)"); 
			jQuery("div#cake_deco_top").css("background-color", "rgb(65 105 225 / 0%)"); 

			
		}
		else {
			jQuery('.decorations_opt').css('visibility', 'visible');
			jQuery('.available_toppers_wrapper').css('visibility', 'visible');
			
		}  
	});

	// jQuery('.cart-wrapper').on('click', '#customize_topper', function(){
	// 	if (!jQuery("[name='customize_topper']").is(':checked')) {
	// 		$(this).parent().find('div#cake_deco_top_2').css("background-color", "rgb(65 105 225 / 0%)");
	// 		$(this).parent().find('div#cake_deco_base_2').css("background-color", "rgb(65 105 225 / 0%)");

	// 		// jQuery("div#cake_deco_top_2").css("background-color", "rgb(65 105 225 / 0%)"); 
	// 		// jQuery("div#cake_deco_base_2").css("background-color", "rgb(65 105 225 / 0%)"); 

	// 		$(this).parent().find("div#cake_deco_base").css("background-color", "rgb(65 105 225 / 0%)");
	// 		$(this).parent().find("div#cake_deco_top").css("background-color", "rgb(65 105 225 / 0%)");

	// 		// jQuery("div#cake_deco_base").css("background-color", "rgb(65 105 225 / 0%)"); 
	// 		// jQuery("div#cake_deco_top").css("background-color", "rgb(65 105 225 / 0%)"); 

	// 		$(this).parent().find("option:selected").prop("selected", false);
	// 		$(this).parent().find('.decor_type_wrapper').css('visibility', 'hidden');
	// 		$(this).parent().find('.decor_color_wrapper').css('visibility', 'hidden');
	// 		$(this).parent().find('.decor-preview').css('visibility', 'hidden');

	// 		// jQuery("option:selected").prop("selected", false);
	// 		// jQuery('.decor_type_wrapper').css('visibility', 'hidden');
	// 		// jQuery('.decor_color_wrapper').css('visibility', 'hidden');
	// 		// jQuery('.decor-preview').css('visibility', 'hidden');
	// 	}else{

	// 		$(this).parent().find('.decor-preview').css('visibility', 'visible');
	// 		$(this).parent().find('.decor_type_wrapper').css('visibility', 'visible');
	// 		$(this).parent().find('.decor_color_wrapper').css('visibility', 'visible');

	// 		// jQuery('.decor-preview').css('visibility', 'visible');
	// 		// jQuery('.decor_type_wrapper').css('visibility', 'visible');
	// 		// jQuery('.decor_color_wrapper').css('visibility', 'visible');
	// 	}
	// })
	
	jQuery(".cart-wrapper").on("click", ".customize_topper", function(){
		
		
		
		var checked = jQuery(this).val();
		var uniqid = jQuery(this).attr('data-group_id');
		var cat_id = jQuery(this).attr('data-id');
		
		//alert(cat_id);
		//alert(jQuery(this).prop("checked"));
		
		if(jQuery(this).prop("checked") == true){
			jQuery("#decor_type_input_" + uniqid).css("visibility", "visible");
			jQuery("#decor_color_input_" + uniqid).css("visibility", "visible");
			jQuery("#decor_images_input_" + uniqid).css("visibility", "visible");

			jQuery.ajax({
				type : "POST",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {
					action: "update_customize_topper",
					topper_status: checked, 
					term_id: cat_id,
					uniqid: uniqid
					},
				success: function(response) {
					console.log(response);
					//location.reload();
				}
			});
			
			//alert("decor_type_input_" + cat_id);
		}
		else if(jQuery(this).prop("checked") == false){
			var text1 = "decor_type_";

			let id = text1.concat("", cat_id);
			var test_id = jQuery(id).val();
			console.log('sadasd', id);
			jQuery("#decor_type_input_" + uniqid).css("visibility", "hidden");
			jQuery("#decor_color_input_" + uniqid).css("visibility", "hidden");
			jQuery("#decor_images_input_" + uniqid).css("visibility", "hidden");




			jQuery.ajax({
				type : "POST",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {
					action: "update_customize_topper",
					topper_status: '', 
					term_id: cat_id
					},
				success: function(response) {
					console.log(response);
					//location.reload();
				}
			});
		}


		



				
	});


	// Quantity Buttons

	$('.minus').click(function () {
		var $input = $(this).parent().find('.qty_input');
		
		/*if($("#current_cat_id").val()==35){
			var count = parseInt($input.val()) - 12;
		} else {
			var count = parseInt($input.val()) - 1;
		}*/
		
		var count = parseInt($input.val()) - 1;
		
		count = count < 0 ? 0 : count;
		$input.val(count);
		$input.change();
		return false;
	});
	$('.plus').click(function () {
		var $input = $(this).parent().find('.qty_input');
		
		/*if($("#current_cat_id").val()==35){
			$input.val(parseInt($input.val()) + 12);
		} else {
			$input.val(parseInt($input.val()) + 1);
		}*/
		
		$input.val(parseInt($input.val()) + 1);
		
		$input.change();
		return false;
	});

	$('.customize_topper').click(function(){
		if($(this).prop("checked") == true){
			console.log("Checkbox is checked.");
		}
		else if($(this).prop("checked") == false){
			console.log("Checkbox is unchecked.");
		}
	});


	$('.cart-wrapper').on('change', '.packaging_status', function() {
		var status = $(this).val();
		var term_id = $(this).attr('data-term_id');
		var uniqid = $(this).attr('data-group-id');
		
		console.log(term_id);

		jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : myAjax.ajaxurl,
			data : {
				action: "update_packaging",
				packaging_status: status, 
				term_id: term_id,
				uniqid:uniqid
				},
			success: function(response) {
				console.log(response);
				location.reload();
			}
		});
	})





	

	
});

// Cart Page Decoration Realtime Update Session Values

jQuery(".cart-wrapper").on('change', '.cart_decoration', function(){ 
	var cat_id = jQuery(this).attr("data-cat_id"); 
	//console.log(cat_id);
	var uniqid = jQuery(this).attr("data-group_id");
	var decor_type_value = jQuery(".decor_type_" + uniqid).val(); 
	var decor_color_1_value = jQuery(".decor_color_1_" + uniqid).val();
	
	var decor_colorCode_1_value = jQuery(".decor_color_1_" + uniqid).find(":selected").attr('data-color');
	var decor_colorCode_2_value = jQuery(".decor_color_2_" + uniqid).find(":selected").attr('data-color');
	console.log('c1: ', decor_colorCode_1_value);
	console.log('c2: ', decor_colorCode_2_value);


	var decor_color_2_value = jQuery(".decor_color_2_" + uniqid).val();
	var image = jQuery(".decor_type_" + uniqid).find(':selected').attr('data-image'); 
	
	console.log('uniqid', uniqid);
	//alert(cat_id + " " + decor_type_value + " " + decor_color_1_value + " " + decor_color_2_value) 
	
	jQuery.ajax({
		type : "POST",
		dataType : "json",
		url : myAjax.ajaxurl,
		data : {
			action: "update_session_values_cart_decoration",
			category_id: cat_id,
			decor_type: decor_type_value,
			color_code_1: decor_colorCode_1_value,
			color_1: decor_color_1_value,
			color_code_2: decor_colorCode_2_value,
			color_2: decor_color_2_value, 
			image: image,
			uniqid: uniqid
			},
		success: function(response) {
			console.log(response);
		}
	});

	
	
});




// jQuery(".cart-wrapper").on('change', '.cart_decoration', function(){ 

// });





// jQuery( function() {
//     jQuery( "#datepicker" ).datepicker({ minDate: +1, maxDate: "+1M +10D" });
//     } );
    
    

//     jQuery(document).ready(function(){
//         jQuery("input.timepicker").timepicker({});
//     });

//     jQuery(".timepicker").timepicker({
//         timeFormat: "h:mm p",
//         interval: 60,
//         minTime: "10",
//         maxTime: "6:00pm",
//         defaultTime: "11",
//         startTime: "10:00",
//         dynamic: false,
//         dropdown: true,
//         scrollbar: true
// });

// jQuery(document).ajaxComplete(function(){
//     jQuery('.quantity').off('click', '.plus').on('click', '.plus', function(e) {        
//         $input = jQuery(this).prev('input.qty');
//         var val = parseInt($input.val());
//         $input.val( val+1 ).change();
//     });



//         jQuery('.quantity').off('click', '.minus').on('click', '.minus',
//         function(e) {       
//         $input = jQuery(this).next('input.qty');
//         var val = parseInt($input.val());
//         if (val > 1) {
//             $input.val( val-1 ).change();
//         }
//     });
// });


function printDiv() 
{

  var divToPrint=document.getElementById('print_table');

  var newWin=window.open('','Print-Window');

  newWin.document.open();

  newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

  newWin.document.close();

  setTimeout(function(){newWin.close();},10);

}