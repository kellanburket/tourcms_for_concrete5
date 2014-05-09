<?php

echo RateCalculationTools::fetch_rates_data($_POST);

class RateCalculationTools {

	static function fetch_rates_data($args) {
		extract($args);
	
		$tourcms = RateCalculationTools::load_tourcms();
		$channel_id = SiteConfig::get("channel_id");
		$data = $tourcms->search_raw_departures($tour_id, $channel_id);
		
		$rates_data = $data->tour->dates_and_prices->departure->rates->rate;

		$return = '';
		foreach ($rates_data as $rate) {
			$return .= strtolower($rate->rate_name).'_rate='.$rate->customer_price.'&';
		}
		return $return;
	}
	
	private static function load_tourcms() {
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
}
?>