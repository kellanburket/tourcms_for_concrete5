<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
define("TOURCMS_SINGLE", "tour_single");
define("TOURCMS_SUBGROUP", "tour_subgrouping");
define("TOURCMS_GROUP", "tour_grouping");
define('PLATFORM', 'Concrete5');
define('PKG', 'tourcms_custom_widgets');

class TourcmsCustomWidgetsPackage extends Package {

     protected $pkgHandle = PKG;
     protected $pkgVersion = '1.04';

	public function upgrade() {
		$pkg = $this;
		parent::upgrade();
	}

     public function getPackageDescription() {
          return t("TourCMS Widgets Package");
     }

     public function getPackageName() {
          return t("Tourcms Widgets");
     }
     
	 private function installBlocks($pkg) {
		BlockType::installBlockTypeFromPackage('tour_switchbox', $pkg); 
		//echo 'tour switchbox installed';
		BlockType::installBlockTypeFromPackage('calendar_widget', $pkg); 
		//echo 'calendar widget installed';

		//$switchbox = BlockType::getByHandle('tour_switchbox', $pkg)->getController(); 
		//$switchbox->install();
	 }
	 
	 private function installModels($pkg) {
		Loader::library('tourcms/config', PKG); 
		
		//Load custom pages and types
		Loader::model('attribute/categories/collection');
		Loader::model('collection_types');
		Loader::model('collection_attributes');
		Loader::model('attribute/categories/collection');		
		$akCat = AttributeKeyCategory::getByHandle('collection');
		//adds attribute type to db and returns AttributeType
		$atTourInfo = AttributeType::add('tour_info', t('Tour Info'), $pkg);
		$akCat->associateAttributeKeyType($atTourInfo);

		$args = array('akHandle'=>'tour_name','akName'=>t('Tour Name'),'akIsSearchable'=>true);
		$akTourName = CollectionAttributeKey::add('tour_info', $args, $pkg);

		$tours = $this->get_tours();		
		foreach($tours->tour as $tour) {
			$tour_id_option = TourInfoAttributeTypeOption::add($akTourName, $tour->tour_id, $tour->tour_name);
		}
		
		$args = array('akHandle'=>'tour_category','akName'=>t('Tour Category'),'akIsSearchable'=>true);
		$akTourCategory = CollectionAttributeKey::add('tour_info', $args, $pkg);

		$categories = $this->get_categories();
		$i = 0;
		foreach($categories as $category) {
			$tour_id_option = TourInfoAttributeTypeOption::add($akTourCategory, $i++, $category);
		}
		
		$collections = array(TOURCMS_SINGLE=>"Single Tour", TOURCMS_GROUP=>"Tour Grouping", TOURCMS_SUBGROUP=>"Tour Subgrouping");					
		foreach ($collections as $collection_handle=>$collection_name) {		  
			$collection = CollectionType::getByHandle($collection_handle);
			if(!$collection || !intval($collection->getCollectionTypeID())) { 				
				$collection = CollectionType::add(array('ctHandle'=>$collection_handle,'ctName'=>t($collection_name)), $pkg);
				$pageType = CollectionType::getByHandle($collection_handle);
				$pageType->assignCollectionAttribute($akTourCategory);
				$pageType->assignCollectionAttribute($akTourName);								
				$pageType->saveComposerPublishTargetAll();
			}
		}
	 
	 }
	 
	 private function installSingles($pkg) {
	 	Loader::model('single_page');
		$cak = CollectionAttributeKey::getByHandle('icon_dashboard');
        /*
		$p = SinglePage::add('/dashboard/tour_tab_form/', $pkg);
        if (is_object($p) && $p->isError() !== false) {
            $p->update(array('cName' => t('Tour Tab Settings'), 
                'cDescription' => 'Manage configuration of tabbed items on tour pages.'));
        }
		*/
        $p = SinglePage::add('/dashboard/tour_tab_form/settings', $pkg);
        if (is_object($p) && $p->isError() !== false) {
            $p->update(array('cName' => t('Tour Tab Settings'))); 
            if (is_object($cak)) {
            	$p->setAttribute('icon_dashboard', 'icon-wrench');
            }
        }
	 }
	 
     public function install() {
		$pkg = parent::install();
		//echo 'parent installed<br>';
		$this->installSingles($pkg);		
		//echo 'singles installed<br>';
		$this->installModels($pkg);
		//echo 'models installed<br>';
		$this->installBlocks($pkg);
		//echo 'blocks installed<br>';		
	}

	function add_db_xml($xmlFile) {
    	if(file_exists($xmlFile)) {
			$db = Loader::db();
			Package::installDB($xmlFile);
		} else {
			echo 'File Does Not Exist';
			exit;
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
	
	private function set_package_tool($tool_name){
		$tool_helper = Loader::helper('concrete/urls');
		$this->set ( $tool_name, $tool_helper->getToolsURL($tool_name, 'tourcms_custom_widgets'));
	}
	
	private function set_block_tool($tool_name){
		$tool_helper = Loader::helper('concrete/urls');
		$bt = BlockType::getByHandle($this->btHandle);
		$this->set ($tool_name, $tool_helper->getBlockTypeToolsURL($bt).'/'.$tool_name);
	}

	function uninstall() {
        $db = Loader::db();
        parent::uninstall();
    }
     
}
?>