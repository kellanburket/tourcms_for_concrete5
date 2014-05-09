<?php

class TourcmsBookingEngine {

	function start($post, $channel_id, $tourcms) {
		$user_id = uniqid();
		extract($post);
		
		$real_tour = false;
		$tours = $tourcms->list_tours($channel_id)->tour;
		
		foreach ($tours as $index=>$tour) {
			if ($tour->tour_id == $tour_id) {
				$real_tour = true;
				$tour_name = strip_tags($tour->tour_name->asXML());
				break;
			}
		}
		
		if (!$real_tour) {
			$message = 'invalid tour';//get_option('invalid_tour');
			echo json_encode(array('success'=>false, 'error_msg'=>$message));
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
				$message = get_option('invalid_promo');
				echo json_encode(array('success'=>false, 'error_msg'=>$message));
				exit;
			}
		}
		
		//check to make sure a valid date has been given;
		$tour_date = explode("/", $tour_date);
		$month = ($tour_date[0] < 10) ? "0".$tour_date[0] : $tour_date[0];
		$day = ($tour_date[1] < 10) ? "0".$tour_date[1] : $tour_date[1];
		$year = $tour_date[2];
		$tour_date = $year.'-'.$month.'-'.$day;
		
		if (!checkdate($month, $day, $year)) {
			$message = get_option('invalid_date');
			echo json_encode(array('success'=>false, 'error_msg'=>$message));
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
			$message = get_option('no_availabilities');
			echo json_encode(array('success'=>false, 'error_msg'=>$message));
			exit;
		}
		
		
		//Set total customers
		$total_customers = intval($no_adults) + intval($no_children);
			
		//echo htmlentities($availability->available_components->component->asXML());
		
		$full_refund = ($full_refund == "true") ? true : false; 
		
		$selected_options = array_combine(
			explode('&', $option_names), 
			explode('&', $option_quantities)
		);
	
		
		//Get Booking Key
			//if all variables clear, start new booking
		$booking = new SimpleXMLElement('<booking />');
		$booking->addChild('total_customers', $total_customers);
		
		$url = plugins_url()."/choose-your-adventure/fetch_key.php";
		$url_data = new SimpleXMLElement('<url />'); 
		$url_data->addChild('response_url',	$url);	
		$result = $tourcms->get_booking_redirect_url($url_data, $channel_id);	
		$redirect_url = $result->url->redirect_url;
		
		$booking_key = file_get_contents($redirect_url);
		
		if ($booking_key == null) {
			$message = get_option('tourcms_technical_problem');
			echo json_encode(array('success'=>false, 'error_msg'=>$message));
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
			
			if ($option->option_name == 'Full Refund Charge' && $full_refund == true) {
				$booking_option->addChild('component_key', $option->quantities_and_prices->selection[$total_customers - 1]->component_key);
			} else {
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
		}
	
		$no_children = intval($no_children);
		$no_adults = intval($no_adults);
	
		$customers = $booking->addChild('customers');
	
		for ( $i = 0; $i < $no_adults; $i++) {
			$customer = $customers->addChild('customer');
			if ($i == 0) {
				$customer->addChild('title', '');
			}
			$customer->addChild('firstname', '');
			$customer->addChild('surname', '');
			
			if ($i == 0) {
				$customer->addChild('email', '');
				$customer->addChild('tel_home', '');		
				$customer->addChild('address', '');
				$customer->addChild('city', '');		
				$customer->addChild('county', '');		
				$customer->addChild('postcode', '');		
				$customer->addChild('country', '');		
			}
			
			$customer->addChild('agecat', 'a');
			
			if ($i == 0) {
				$customer->addChild('tel_mobile', '');		
			}
		}
	
		for ( $i = 0; $i < $no_children; $i++) {
			$customer = $customers->addChild('customer');
			if ($i == 0 && $no_adults == 0) {
				$customer->addChild('title', '');
			}
			$customer->addChild('firstname', '');
			$customer->addChild('surname', '');
	
			if ($i == 0 && $no_adults == 0) {
				$customer->addChild('email', '');
				$customer->addChild('tel_home', '');	
				$customer->addChild('address', '');
				$customer->addChild('city', '');		
				$customer->addChild('county', '');		
				$customer->addChild('postcode', '');		
				$customer->addChild('country', '');		
			}
	
			$customer->addChild('agecat', 'c');
	
			if ($i == 0 && $no_adults == 0) {
				$customer->addChild('tel_mobile', '');		
			}		
		}
	
		if ($user_id == 0) {
			$user = get_user_by('login', $cookie);
			$user_id = $user->ID;
			if ($user_id == 0) {		
				$userdata = array(
					role=>'customer',
					user_login=>$cookie,
					user_pass=>'NeverLogIn100!!@#'
				);
				$user_id = wp_insert_user($userdata);
			}
		}
	
		if (!$user_id) {
			echo json_encode(array(
				'success'=> false,
				'message'=>'No User ID Added'			
			));
			exit;
		}
	
		$result = $tourcms->start_new_booking($booking, $channel_id);
		$result_display = $result->asXML();
		
		delete_user_meta($user_id, 'tourcms_response');
		update_user_meta($user_id, 'tourcms_response', htmlentities($result_display), true); 
		$response = get_user_meta($user_id, 'tourcms_response', true);
	
		delete_user_meta($user_id, 'new_booking');
		update_user_meta($user_id, 'new_booking', htmlentities($booking->asXML()), true);
		$get_user_meta = get_user_meta($user_id, 'new_booking');
			
		delete_user_meta($user_id, 'totals_string');
		update_user_meta($user_id, 'totals_string', htmlentities($totals_string), true);
	
		delete_user_meta($user_id, 'tour_date');
		update_user_meta($user_id, 'tour_date', $tour_date, true);
	
		delete_user_meta($user_id, 'tour_name');
		update_user_meta($user_id, 'tour_name', htmlentities($tour_name), true);
	
		delete_user_meta($user_id, 'booking_id');
		update_user_meta($user_id, 'booking_id', strip_tags($result->booking->booking_id->asXML()), true); 
	
		if (!get_user_meta($user_id, 'pom_cookie')) {
			add_user_meta($user_id, 'pom_cookie', $cookie, true); 
		}
	
		$DEBUG_MODE = get_option('tourcms_debug_mode');
		
		//$args = 'between_date_start='.$tour_date.'&between_date_end='.$tour_date;
		//$dates_and_deals = $tourcms->show_tour_datesanddeals($tour_id, $channel_id, $args)->asXML();
	
		//$args = 'start_date_start='.$tour_date.'&start_date_end='.$tour_date;
		//$show_tours = $tourcms->show_tour_departures($tour_id, $channel_id, $args)->asXML();
		
		$site_url = wp_nonce_url(get_site_url(get_current_blog_id(), '?p='.get_option('tourcms_checkout_page'), 'https'), 'id='.$user_id);
	
		try {
			
			if ($result->unavailable_component_count == 0 && $DEBUG_MODE == false) {
				echo json_encode(array(
					'success'=> true,
					'debug'=> false,
					'user_id'=>$user_id,
					'site_url'=>$site_url			
				));
			} elseif ($result->unavailable_component_count > 0) {
				throw new Exception('Availabilities Exception');
			} elseif ($DEBUG_MODE == true) {
				//header("Location: ".$site_url);
				//header("Cache-Control: no-cache, must-revalidate");
				//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
				//exit;
				echo json_encode(array(
					'success'=>true,
					'debug'=> true,
					'user_id'=> $user_id,
					'new_booking' => $get_user_meta,				
					'site_url'=>$site_url			
				));
			} else {
				echo json_encode(array(
					'success'=> true,
					'debug'=> true,
					'user_id'=>$user_id,
					'site_url'=>$site_url,
					'booking_id'=>get_user_meta($user_id, 'booking_id', true)		
				));
			}
		} catch(Exception $e) {
			$message = get_option('no_availabilities');
			echo json_encode(array('success'=>false, 'error_msg'=>$message));
		}
		
		//$booking_engine_url = $result->booking->booking_engine_url;
		//echo plugins_url().'/choose-your-adventure/checkout-page.php?booking_url='.$booking_engine_url;
		
		//$booking_engine_url = htmlspecialchars($booking_engine_url);
			exit;	
	}
}
