<?php
//Useful Functions to call via Javascript
call_user_func($_POST['action'], $_POST, load_tourcms(), SiteConfig::get("channel_id"));

function check_promo_code($post, $tourcms, $channel_id) {
	$promo_code = $post['promo_code'];
	if ($promo_code) {
		$promo_code = esc_sql(htmlspecialchars($promo_code));
		$code_check = $tourcms->show_promo($promo_code, $channel_id);
		
		if($code_check->error == "OK") {
			echo json_encode(array(
				'value'=>strip_tags($code_check->promo->value->asXML()), 
				'value_type'=>strip_tags($code_check->promo->value_type->asXML())
			));
			exit;
		} else {
			echo json_encode(array('error'=>'Invalid Promo Code'));
			exit;
		}
	}
}

function update_calendar($post, $tourcms, $channel_id) {
	require_once($_SERVER['DOCUMENT_ROOT'].'/packages/tourcms_custom_widgets/blocks/calendar_widget/tools/calendar_tools.php');
	$calendar = new LiveTourCMSCalendar($post);
	echo $calendar->display_calendar();
	exit;
}

function fetch_rates_data($post, $tourcms, $channel_id) {
	extract($post);

	$data = $tourcms->search_raw_departures($tour_id, $channel_id);
	$rates_data = $data->tour->dates_and_prices->departure->rates->rate;

	$return = '';
	foreach ($rates_data as $rate) {
		$return .= strtolower($rate->rate_name).'_rate='.$rate->customer_price.'&';
	}
	echo substr($return, 0, -1);
	exit;
}

function start_booking_engine($post, $tourcms, $channel_id) {
	require_once(dirname(__FILE__).'/tourcms_booking_engine.php');
	$engine = new TourcmsBookingEngine();
	$engine->start($post, $tourcms, $channel_id);	
	exit;
}
	
function load_tourcms() {
	//echo 'in load_tourcms<br>';
	if (!class_exists('TourCMS')) {
		//echo 'class does not exist: TOURCMS<br>';		
		
		if (PLATFORM == 'Concrete5') {
			//echo 'Platform is Concrete5<br>';		
		
			if(class_exists('Loader') && defined('PKG')) {
				//echo 'class exists: Loader<br>';
				Loader::library('tourcms/config', PKG);	
				$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");
			} else {
				//echo 'Class does not exist: Loader. loading tour cms';
				require($_SERVER['DOCUMENT_ROOT'].'/packages/tourcms_custom_widgets/libraries/tourcms/config.php');
			}
		}
	} else {
		//echo 'class exists TOURCMS<br>';		
		$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");
	}
	return $tourcms;
}