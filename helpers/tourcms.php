<?php 
/**
 * @package TourCMS Custom Widgets
 */

/**
 * Functions to help with using TourCMS. Does not include form elements - those have their own helper. 
 */

defined('C5_EXECUTE') or die("Access Denied.");

class TourcmsCustomWidgetsTourcmsHelper {
	
	public $channel_id;
	public $tour_name;
	public $tour_id;
	public $cID;
	private $tourcms;
	
	public function __construct() {
		Loader::library('tourcms/config', PKG); 
		$this->tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");		
		$this->channel_id = SiteConfig::get("channel_id");
	}

	public function getTour($cID) {
		$this->cID = $cID;	
		$db = Loader::db();
		$name = $db->Execute('SELECT * FROM atTourInfoOptions atio INNER JOIN atTourInfoOptionsSelected atios ON atios.atTourInfoOptionID = atio.tour_id INNER JOIN AttributeKeys ak ON ak.akID = atio.akID WHERE atios.ctID = ? AND ak.akHandle = ?', array($this->cID, 'tour_name'));	
		$name = $name->getRows();
		$name = $name[0];

		$this->tour_name = $name['tour_name'];
		$this->tour_id = $name['tour_id'];
	
		$params = 'id='.$this->tour_id.'&show_options=1';
		return $this->tourcms->show_tour($this->tour_id, $this->channel_id, $params);
	}
}
?>
