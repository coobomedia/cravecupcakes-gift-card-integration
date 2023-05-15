<!-- top header -->
<div class="theme-header">
	<div class="inner-header">
		<div class="logo">
			<a href="<?php echo get_home_url(); ?>"><img src="/wp-content/uploads/2022/06/cravelogo.jpg" alt="" /></a>

			<!-- <div class="minimum-infor">
				<p><strong>Delivery: </strong>Houston APR 25 5:00pm</p>
			</div> -->
		</div>
		<div class="order-type">
			<div class="pickup" ><i>Order&nbsp; </i> Pickup</div>
			<div class="delivery" ><i>Order&nbsp; </i> Delivery <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-up" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/>
</svg></div>

			<div class="minimum-infor" style="display: none;">
				<p><strong>Delivery: </strong>Houston APR 25 5:00pm</p>
			</div>
		
		</div>
	</div>
</div>


<!-- Delivery Modal  -->

<div id="DeliveryModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <!--<span class="close">&times;</span>-->
    <h1 class="delivery-form-title">Required to add items to cart</h1>
    <div class="delivery-content-wrapper">
        <h3>Order Type</h3><br />
        
        <input type="radio" id="pickup" name="order_type" value="pickup" checked="checked">
        <span class="checkmark"></span>
        <label for="pickup">Pickup</label>
        <input type="radio" id="delivery" name="order_type" value="delivery">
        <span class="checkmark"></span>
        <label for="delivery">Delivery</label><br /><br />
        
        <h3>Select Location</h3><br />
        
        <input type="radio" id="west_uni" name="select_location" value="1" checked="checked">
        <span class="checkmark"></span>
        <label for="west_uni">West University</label>
        <input type="radio" id="uptown_park" name="select_location" value="2">
        <span class="checkmark"></span>
        <label for="uptown_park">Uptown Park</label><br />
        <input type="radio" id="the_woodlands" name="select_location" value="3">
       	<span class="checkmark"></span>
        <label for="the_woodlands">The Woodlands</label><br /><br />
        
        <h3>Choose Day / Time</h3>
        <div class="datetime_select">
        	<div class="date_select">
        		<label for="datepicker">Date</label>
        		<input type="text" id="datepicker" name="select_date" required="required">
        	</div>

        	<div class="time_select">
        		<label for="timepicker">Time</label>
        		<input id="timepicker" class="timepicker timepicker-with-dropdown text-center" name="select_time" required="required">
        	</div>
        </div>
        
        <div class="shop-button-wrapper">
        	<button id="delivery-shop-now">Shop Now</button>
        </div>
        
    </div>
  </div>
</div>