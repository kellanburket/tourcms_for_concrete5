<?php
	if (class_exists('Loader')) {
		Loader::library('tourcms/site_config', 'tourcms_custom_widgets');
		Loader::library('tourcms/functions', 'tourcms_custom_widgets');
		Loader::library('tourcms/video_embed/video_embed', 'tourcms_custom_widgets');
		Loader::library('tourcms/tourcms', 'tourcms_custom_widgets');
	} else {
		require_once(dirname(__FILE__).'/site_config.php');
		require_once(dirname(__FILE__).'/functions.php');		
		require_once(dirname(__FILE__).'/tourcms.php');
	}

	// Your TourCMS API credentials	
	// These can be found by logging in to TourCMS and heading to "Configuration & Setup" > "API"\
	SiteConfig::set("channel_id", 5966);
	SiteConfig::set("api_private_key", "03e4fea8d321");

	// Page title
	SiteConfig::set("page_title", "");

	// Cache duration (seconds)
	// Set to zero to disable caching
	SiteConfig::set("cache_duration", 30*60);

	// The directory to save cached data to
	SiteConfig::set("cache_dir", "cache");

	// Create an initial TourCMS API object
	$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");