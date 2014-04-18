<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

/*
add_action('widgets_init', 'create_tourcms_sidebar_widget');
add_action('wp_enqueue_scripts', 'enqueue_tourcms_sb_js');

add_action('wp_ajax_nopriv_start_tourcms_booking_engine', 'start_tourcms_booking_engine');
add_action('wp_ajax_start_tourcms_booking_engine', 'start_tourcms_booking_engine');


function create_tourcms_sidebar_widget() {
	register_widget('tourcms_sidebar_widget');	
}

function enqueue_tourcms_sb_js() {
	wp_enqueue_script('tourcms_sidebar_calendar', plugins_url()."/choose-your-adventure/js/sidebar-calendar.js", array('jquery', 'sprintf') );
	wp_enqueue_script("sprintf", plugins_url()."/choose-your-adventure/js/sprintf.js", array(), false, false);

}
*/

function start_tourcms_booking_engine() {

	Loader::packageElement('config', 'tourcms_custom_widgets'); 
	$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");


	$channel_id = SiteConfig::get("channel_id");
	extract($_POST);
	
	$real_tour = false;
	$tours = $tourcms->list_tours($channel_id)->tour;
	
	foreach ($tours as $index=>$tour) {
		if ($tour->tour_id == $tour_id) {
			$real_tour = true;
			$tour_name = $tour->tour_name;
			break;
		}
	}
	
	if (!$real_tour) {
		echo '<span class="warning-msg">Not a Valid Tour!</span>';
		echo '</form>';
		exit;
	}
	
	//Set all numerical
	$tour_id = intval($tour_id);
	$no_adults = intval($no_adults);
	$no_children = intval($no_children);
	$adult_rate = floatval($adult_rate);
	$child_rate = floatval($child_rate);
	
	
	//check to make sure promo code is valid;
	if ($promo_code != null) {
		$promo_code = mysqli_real_escape_string(htmlspecialchars($promo_code));
		
		$code_check = $tourcms->show_promo($promo_code, $channel_id);
		
		if($code_check->error == "OK") {
		} else {
		  echo '<span class="warning-msg">Not a Valid Promo Code!</span>';
		  $promo_code = null; 
		}
	}
	
	//check to make sure a valid date has been given;
	$tour_date = explode("/", $tour_date);
	$month = ($tour_date[0] < 10) ? "0".$tour_date[0] : $tour_date[0];
	$day = ($tour_date[1] < 10) ? "0".$tour_date[1] : $tour_date[1];
	$year = $tour_date[2];
	$tour_date = $year.'-'.$month.'-'.$day;
	
	if (!checkdate($month, $day, $year)) {
		echo '<span class="warning-msg">Not a Valid Date!</span>';
		echo '</form>';
		exit;
	}
	
	//Check tour availability;
	$params =
		"date=".$tour_date
		."&r1=".$no_adults
		."&r2=".$no_children;
	
	$availability = $tourcms->check_tour_availability($params, $tour_id, $channel_id);
	
	if (isset($availability->available_components->component))
		$num_components = count($availability->available_components->component);
	else {
		echo '<span id="not-available-message">Sorry, no availability!</span>';
		echo '</form>';
		exit;
	}
	
	
	//Set total customers
	$total_customers = intval($no_adults) + intval($no_children);
		
	//echo htmlentities($availability->available_components->component->asXML());
	
	$full_refund = ($full_refund == 1) ? true : false; 
	
	$selected_options = array_combine(
		explode('&', $option_names), 
		explode('&', $option_quantities)
	);

	
	//Get Booking Key
		//if all variables clear, start new booking
	$booking = new SimpleXMLElement('<booking />');
	$booking->addChild('total_customers', $total_customers);
	
	$url = SiteConfig::get("code_root")."/fetch_key.php";
	$url_data = new SimpleXMLElement('<url />'); 
	$url_data->addChild('response_url',	$url);	
	$result = $tourcms->get_booking_redirect_url($url_data, $channel_id);	
	$redirect_url = $result->url->redirect_url;
	
	$booking_key = file_get_contents($redirect_url);
	
	if ($booking_key == null) {
		echo '<span id="not-available-message">There was a technical problem!</span>';
		echo '</form>';
		exit;
	}

	$booking->addChild('booking_key', $booking_key);
	
	if ($promo_code != null) {
		$booking->addChild('promo_code',  $promo_code);
	}
	
	$components = $booking->addChild('components');
	$component = $components->addChild('component');
	
	$component->addChild('component_key', $availability->available_components->component->component_key);
	
	$booking_options = $component->addChild('options');
	$tour_options = $availability->available_components->component->options->option;
	
	foreach ($tour_options as $key => $option) {
		$booking_option = $booking_options->addChild('option');
		
		foreach ($selected_options as $op_name => $op_no) {
			
			if ($option->option_name == $op_name && $op_no >= 0) {		
				$booking_option->addChild(
					'component_key', 
					$option->quantities_and_prices->
						selection[($op_no > $total_customers - 1) ? $total_customers - 1 : intval($op_no) - 1]->component_key
				);
				break 1;
			}
		}
	}

	$customers = $booking->addChild('customers');
	
	for ( $i = 0; $i <= intval($no_adults - 1); $i++) {
		//$customer = $customers->addChild('customer');
		//$customer->addChild('title', '');
		//$customer->addChild('firstname', '');
		//$customer->addChild('surname', '');
		//$customer->addChild('email', '');
		//$customer->addChild('telhome', '');		
		$customer->addChild('agecat', 'a');

	}
	for ( $i = 0; $i <= intval($no_children - 1); $i++) {
		$customer = $customers->addChild('customer');
		//$customer->addChild('title', '');
		//$customer->addChild('firstname', '');
		//$customer->addChild('surname', '');
		//if ($no_adults == 0) {
			//$customer->addChild('email', '');
			//$customer->addChild('telhome', '');
		//}		
		$customer->addChild('agecat', 'c');
	}

	$result = $tourcms->start_new_booking($booking, $channel_id);
	
	//echo $booking->asXML();
	$booking_engine_url = $result->booking->booking_engine_url;
	//$booking_engine_url = htmlspecialchars($booking_engine_url);
	
	echo SiteConfig::get("code_root").'checkout-page.php?booking_url='.$booking_engine_url;
	exit;	
}

Loader::packageElement('config', 'tourcms_custom_widgets'); 
$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");

extract($args);
$channel_id = SiteConfig::get("channel_id"); 
$tour_id = '1';
$params = 'id=1&show_options=1';
$tour = $tourcms->show_tour($tour_id, $channel_id, $params);

if ($tour) {
	$tour = $tour->tour;
	
	$tour_name = $tour->tour_name_long;
	$tour_desc = $tour->shortdesc;
	$tour_thumbnail = $tour->images->image->url_thumbnail;
	
	//$tour_details_destination =
	$tour_details_address = $tour->address;
	$tour_details_itinerary = $tour->itinerary;
	$tour_details_start_time = $tour->start_time;
	$tour_details_end_time = $tour->end_time;
	//$tour_menu
	
	$tour_essentials = $tour->essential;
	$tour_included = $tour->inc;
	
	//$tour_seasonal_start
	//$tour_seasonal_end

	$tour_price = $tour->from_price_display;
	//$tour_adult_price_discounted
	
	//$tour_child_price		
	//$tour_child_price_discounted
	
	$tour_suitable_for_children = $tour->suitable_for_children;
	
	$tour_options = $tour->options->option;
	
	$rates_data = $tourcms->search_raw_departures($tour_id, $channel_id);

	foreach ($rates_data->tour->dates_and_prices->departure->rates->rate as $key=>$rate) {
		if (stristr($rate->rate_name, "child")) {
			$child_rate = $rate->customer_price;
			echo '<input type="hidden" name="child_rate" value="'.$child_rate.'">';			  				
		}
		if (stristr($rate->rate_name, "adult")) {
			$adult_rate = $rate->customer_price;
			echo '<input type="hidden" name="adult_rate" value="'.$adult_rate.'">';
		}
	}
}

?>
<div id="tour-info">
	<h1 class="tour-h4"><?php echo $tour_name; ?></h1>
    <img class="tour-thumbnail" src="<?php echo $tour_thumbnail; ?>">
    <div id="tour-switcher">
    	<div class="tour-ul-wrap">
            <ul class="tour-tabs">
                <li class="tour-tab" id="tab-1">Overview</li>
                <li class="tour-tab" id="tab-2">Included</li>
                <li class="tour-tab" id="tab-3">What to Bring</li>                    
            </ul>
  		</div>
  		<div id="tour-content-wrap">
    		<div class="tour-content-area" id="tour-overview">
	    		<h3 class="tour-h3"
	    		<p class="tour-description"><?php echo $tour_desc; ?></p>
				<p class="tab-info-head"><strong>Where to Board:</strong></p>
                <p class="tour-tab-info"><?php echo $tour_details_address; ?></p>
                <p class="tab-info-head"><strong>Itinerary:</strong></p>
				<p class="tour-tab-info"><?php echo str_replace('AM', 'AM<BR>', $tour_details_itinerary); ?></p>
                <p class="tab-info-head"><strong>Trip:</strong></p>
                <p class="tour-tab-info"><?php echo $tour_details_start_time.' - '.$tour_details_end_time; ?></p>
    		</div>
			<div class="tour-content-area" id="tour-included">
            	<p class="tour-tab-info"><?php echo $tour_included; ?></p>
            </div>
            
			<div class="tour-content-area" id="tour-essentials">
            	<p class="tour-tab-info"><?php echo $tour_essentials; ?></p>
            </div>
  		</div>
	</div>
</div>

</div>
<div id="tour-widget-wrap">
	<div class="mini-divider"></div>
    <form id="tour-form" action="" method="post">
    	<input name="tour_id" type="hidden" value="<?php echo $tour_id; ?>">
        
        <div id="tour-pick-a-date-wrapper">
            <div id="tour-calendar">
                <div id="tour-head">
                    <button id="tour-back-one" class="tour-button" disabled>&larr;</button>
                    <span id="tour-month"><?php echo date("F")." ".date("Y"); ?></span>
                    <button id="tour-forward-one" class="tour-button">&rarr;</button>
                </div>
				<div class="mini-divider"></div>
				<div id="tourcms-sidebar-table"></div>
            </div>
            <ul class="availability-key">
            	<li class="a-key-li">Selected<div id="selected-key"></div></li>
            	<li class="a-key-li">Available<div id="available-key"></div></li>
            	<li class="a-key-li">Unavailable<div id="unavailable-key"></div></li>                   	
            </ul>
            <div class="tour-activity-date-wrap">
        		<p class="tour-p" id="activity-date-lb">Activity Date</p>
        		<input type="text" id="tour-activity-date-field" name="activity_date" class="confirm-field" />
            </div>
        </div>
        <div class="divider"></div>
        
        <div id="tour-adults">
            <p class="tour-guests-label">Adults</p>
            <input type="number" name="no_adults" id="no-adults-input" class="guests-input confirm-field" />
        </div>
        
        <?php if ($tour_suitable_for_children) : ?>
            <div id="tour-children">
            	<p class="tour-guests-label">Children (ages 3-12)</p>
                <input type="number" name="no_children" id="no-children-input" class="guests-input confirm-field" />
            </div>
        <?php endif; ?>
        
        <div class="divider"></div>
        <h5 class="confirm-booking-h5">Available Upgrades</h5>

        <table id="available-upgrades">
            <thead>
            </thead>
            <tbody>
				<?php $i = 0; 
                foreach ($tour_options as $key=>$option) {
                    if ($option->group_title == "Tour extra (general)") {
                        echo '<tr class="tour-upgrades-tr">';
                        echo '<td class="tour-upgrades-td">'.$option->option_name.'</td>';
                        echo '<td class="tour-upgrades-td">Price: ';
                        echo $option->from_price_display.'</td>';	
                        echo '<td class="tour-upgrades-td"><input 
                            type="number" 
                            name="option_quantity_'.$i.'" 
                            id="option-quantity-field-'.$i.'" 
                            class="confirm-field"
                        ></td>';					
                        echo '<input 
                            type="hidden" 
                            name="option_name_'.$i.'" 
                            value="'.$option->option_name.'" 
                            id="option-name-field-'.$i.'" 
                        >';
                        echo '<input 
                            type="hidden" 
                            name="option_rate_'.$i.'" 
                            value="'.$option->from_price.'" 
                            id="option-rate-field-'.$i.'" 
                        >';
                        echo '</tr>';		
                        $i++;
                    }
                      
                }
                echo '<input 
                            type="hidden" 
                            name="total_options" 
                            value="'.$i.'" 
                            id="options-total" 
                        >'; ?>
        	</tbody> 
        </table>
        
        
        <div class="divider"></div>
		<div id="tour-promo-code">
            <p id="promo-label">Promotional Code</p>
            <input type="text" name="promo_code" id="promo-code-input" class="confirm-field" />
        </div>
        
        <div id="tour-refund">
            <input type="checkbox" name="full_refund" id="full-refund-input">
            <span>Full refund with 48 hour advance cancellation.</span>
		</div>
        

        <div class="divider"></div>
		
		<div id="tourcms-totals">
		
		</div>

			
        <div id="tour-submit-div">
            <button id="submit" disabled>BOOK NOW</button>
		</div>
                    
    </form>
           
    </div>
	</div> <?php

echo $content ?>
