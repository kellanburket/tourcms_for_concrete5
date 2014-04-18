<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
define("TOURCMS_SINGLE", "tourcms_single");
define("TOURCMS_SUBGROUP", "tourcms_subgroup");
define("TOURCMS_GROUP", "tourcms_group");
$db = new mysqli;


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
		 		akID int unsigned not null default 0,
		 		avID int unsigned not null default 0,
		 		primary key(tour_id, akID, avID));';

		 	$db->query($query);
		 	
	 		$query = 'CREATE TABLE IF NOT EXISTS TourCMSAttributeKeys ( 
		 		tour_id int unsigned not null default 0,
		 		akID int unsigned not null default 0,
		 		avID int unsigned not null default 0,
		 		primary key(tour_id, akID, avID));';				
				
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
		            
		  BlockType::installBlockTypeFromPackage('calendar_widget', $pkg); 
		  
		  BlockType::installBlockTypeFromPackage('subtours_widget', $pkg); 
		  
		  Loader::model('collection_types');
		  Loader::model('collection_attributes');
		  Loader::model('attribute/categories/collection');
		  
		  $collections = array(TOURCMS_SINGLE=>"Single Tour", TOURCMS_GROUP=>"Tour Grouping", TOURCMS_SUBGROUP=>"Tour Subgrouping");
		  
		  foreach ($collections as $collection_handle=>$collection_name) {		  
			  $collection = CollectionType::getByHandle($collection_handle);
			  if(!$collection || !intval($collection->getCollectionTypeID())) { 
	          	$collection = CollectionType::add(array('ctHandle'=>$collection_handle,'ctName'=>t($collection_name)), $pkg);
			   }
		  }
		  

		  
		  $eaku = AttributeKeyCategory::getByHandle('collection');
		  $eaku->setAllowAttributeSets(AttributeKeyCategory::ASET_ALLOW_SINGLE);			  	
		  $themeSet = $eaku->addSet('built_in', t('Categories Atributes'),$pkg);
		  $ak1=CollectionAttributeKey::add($image_file, array('akHandle'=>'tour_id','akName'=>t('Tour ID'),'akIsSearchable'=>false),$pkg)->setAttributeSet($themeSet);
			  	//$pageType = CollectionType::getByHandle($collection_handle);
			  	//$ak= CollectionAttributeKey::getByHandle('tour_id');
			  	//$pageType->assignCollectionAttribute($ak);
	}
     
}
?>