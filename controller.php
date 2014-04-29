<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
define("TOURCMS_SINGLE", "tour_single");
define("TOURCMS_SUBGROUP", "tour_subgrouping");
define("TOURCMS_GROUP", "tour_grouping");

class TourcmsCustomWidgetsPackage extends Package {

     protected $pkgHandle = 'tourcms_custom_widgets';
     protected $appVersionRequired = '5.5.0';
     protected $pkgVersion = '1.0';


	 public function __construct() {
	 	//parent::__construct();
	 	
	 	@ $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	 	
	 	if($db->connect_errno) {
	 		printf("Connection Failed!: %s\n", $db->connect_error);
	 	} else {
	 		$query = 'CREATE TABLE IF NOT EXISTS TourCMSAttributeValues ( 
		 		tour_id int unsigned not null default 0,
		 		tour_version_id int unsigned not null default 0,
		 		akID int unsigned not null default 0,
		 		avID int unsigned not null default 0,
		 		primary key(tour_id, tour_version_id, akID, avID));';

		 	$db->query($query);
		 	
	 		$query = 'CREATE TABLE IF NOT EXISTS TourCMSAttributeKeys ( 
		 		tour_id int unsigned not null default 0,
		 		tour_version_id int unsigned not null default 0,
		 		akID int unsigned not null default 0,
		 		avID int unsigned not null default 0,
		 		primary key(tour_id, tour_version_id, akID, avID));';				
				
		 	$db->query($query);
		 	
		 	$query = 'CREATE TABLE IF NOT EXISTS TourCMSSearchIndexAttributes (
			  tour_id int unsigned NOT NULL DEFAULT 0,
			  ak_meta_title text,
			  ak_meta_description text,
			  ak_meta_keywords text,
			  ak_icon_dashboard text,
			  ak_exclude_nav tinyint(4) DEFAULT 0,
			  ak_exclude_page_list tinyint(4) DEFAULT 0,
			  ak_header_extra_content text,
			  ak_exclude_search_index tinyint(4) DEFAULT 0,
			  ak_exclude_sitemapxml tinyint(4) DEFAULT 0,
			  PRIMARY KEY (cID)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
			
			$db->query($query);


	 	}
	 }

     public function getPackageDescription() {
          return t("TourCMS Widgets Package");
     }

     public function getPackageName() {
          return t("Tourcms Widgets");
     }
     
     public function install() {
		$pkg = parent::install();

		//Load TourCMS
		Loader::packageElement('config', 'tourcms_custom_widgets'); 
		Loader::model('attribute/categories/collection');

		BlockType::installBlockTypeFromPackage('calendar_widget', $pkg); 
		BlockType::installBlockTypeFromPackage('subtours_widget', $pkg); 

		Loader::model('collection_types');
		Loader::model('collection_attributes');
		Loader::model('attribute/categories/collection');


		$eaku = AttributeKeyCategory::getByHandle('collection');
		
		//adds attribute type to db and returns AttributeType
		$atTourInfo = AttributeType::add('tour_info', t('Tour Info'), $pkg);
		$eaku->associateAttributeKeyType($tour_info_type);

		$tours = $this->get_tours();		
		foreach($tours->tour as $tour) {
			$tour_id_option = TourInfoTypeOption::add($atTourInfo, $tour->tour_name);
		}
		
		//Not sure what these do.
		//$eaku->setAllowAttributeSets(AttributeKeyCategory::ASET_ALLOW_SINGLE);			  	
		//$themeSet = $eaku->addSet('built_in', t('Categories Atributes'), $pkg);
		//$tour_id_attr = CollectionAttributeKey::getByHandle('tour_id_select');

		//if(!$tour_id_attr || !intval($tour_id_attr->getAttributeKeyID())) {
		//$args = array('akHandle'=>'tour_id','akName'=>t('Tour ID'),'akIsSearchable'=>true);
		
		//$tour_id_attr = CollectionAttributeKey::add('tour_info', $args, $pkg, 'SELECT');
			
		
		//$tour_id_attr->setAttributeSet($themeSet);			
		//}
		
		$collections = array(TOURCMS_SINGLE=>"Single Tour", TOURCMS_GROUP=>"Tour Grouping", TOURCMS_SUBGROUP=>"Tour Subgrouping");

		$args = array('akHandle'=>'tour_category','akName'=>t('Tour Category'),'akIsSearchable'=>true);
		CollectionAttributeKey::add('tour_info', $args, $pkg)->setAttributeSet($themeSet);
					
		foreach ($collections as $collection_handle=>$collection_name) {		  
			$collection = CollectionType::getByHandle($collection_handle);
			if(!$collection || !intval($collection->getCollectionTypeID())) { 
				
				$collection = CollectionType::add(array('ctHandle'=>$collection_handle,'ctName'=>t($collection_name)), $pkg);
				$pageType = CollectionType::getByHandle($collection_handle);
				$attribute_key = CollectionAttributeKey::getByHandle('tour_id');
				$pageType->assignCollectionAttribute($attribute_key);
					
				$pageType = CollectionType::getByHandle($collection_handle);
				$attribute_key = CollectionAttributeKey::getByHandle('tour_category');
				$pageType->assignCollectionAttribute($attribute_key);
								
			}
		}
		  


	}

	
	public function get_tours() {
		$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");
		$channel_id = SiteConfig::get("channel_id");
		
		$results = $tourcms->search_tours('', $channel_id);
		return $results;		
	}
	
	public function get_categories() {
		$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");
		$channel_id = SiteConfig::get("channel_id");
		
		$categories = array();		
		$results = $tourcms->search_tours('', $channel_id);
		foreach ($results->tour as $tour) {
			$t = $tourcms->show_tour($tour->tour_id, $channel_id);
			$groups = $t->tour->categories;
			
			foreach ($groups->group as $group) {
				$values = $group->values->value;
				$parser = xml_parser_create();
				xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
				$cats = $group->values->asXML();
				
				xml_parse_into_struct($parser, $cats, $values);
				xml_parser_free($parser);
				foreach ($values as $value) {
					if ($value['type'] == 'complete') {
						$categories[] = $value['value'];   
					}
				}
			}	
		}
		
		return array_unique($categories);		
	}
     
}
?>