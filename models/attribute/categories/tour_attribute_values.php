<?php

public class TourCMSAttributeKey extends AttributeKey {
	
	protected $searchIndexFieldDefinition = 'tour_id I(11) UNSIGNED NOTNULL DEFAULT 0 PRIMARY';

	function getIndexedSearchTable() {
		return 'TourCMSSearchIndexAttributes';
	}
	
	function getAttributes($tour_id, $tour_version_id, $method = 'getValue') {
		
		$db = Loader::db();
		$values = $db->GetAll("select akID, avID from TourCMSAttributeValues where tour_id = ? and tour_version_id = ?", array($tour_version, $tour_version_id));
		$avl = new AttributeValueList();
		
		foreach($values as $val) {
			$ak = FileAttributeKey::getByID($val['akID']);
			if (is_object($ak)) {
				$value = $ak->getAttributeValue($val['avID'], $method);
				$avl->addAttributeValue($ak, $value);
			}
		}		
		return $avl;		
	}
	
	function getAttributeValue($avID, $method = 'getValue') {
		$av = TourCMSAttributeValue::getByID($avID);
		if (is_object($av)) {
			$av->setAttributeKey($this);
			return $av->{$method}();
		} else {
			return 0;
		}
	}


	public static function getByHandle($akHandle) {
		//This method simply needs to query the database for the ID of the matching handle and return getByID($akID)
		$ak = CacheLocal::getEntry('tourcms_attribute_key_by_handle', $akHandle);
		if (is_object($ak)) {
			return $ak;
		} else if ($ak == -1) {
			return false;
		}
		
		$ak = -1;
		$db = Loader::db();
		$q = "SELECT ak.akID FROM AttributeKeys ak INNER JOIN AttributeKeyCategories akc ON ak.akCategoryID = akc.akCategoryID  WHERE ak.akHandle = ? AND akc.akCategoryHandle = 'tour'";
		$akID = $db->GetOne($q, array($akHandle));
		if ($akID > 0) {
			$ak = self::getByID($akID);
		} else {
			 // else we check to see if it's listed in the initial registry
			 $ia = FileTypeList::getImporterAttribute($akHandle);
			 if (is_object($ia)) {
			 	// we create this attribute and return it.
			 	$at = AttributeType::getByHandle($ia->akType);
				$args = array(
					'akHandle' => $akHandle,
					'akName' => $ia->akName,
					'akIsSearchable' => 1,
					'akIsAutoCreated' => 1,
					'akIsEditable' => $ia->akIsEditable
				);
			 	$ak = TourCMSAttributeKey::add($at, $args);
			 }
		}
		CacheLocal::set('tourcms_attribute_key_by_handle', $akHandle, $ak);
		if ($ak === -1) {
			return false;
		}
		return $ak;
	}

	function getByID($akID) {
		$ak = new TourCMSAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}	
	}

	function getList() {
		return parent::getList('tour');
	}

	function getColumnHeaderList() {
		return parent::getList('tour', array('akIsColumnHeader' => 1));
	}

	function getSearchableIndexedList() {
		return parent::getList('tour', array('akIsSearchableIndexed' => 1));
	}
	
	function getSearchableList() {
		return parent::getList('tour', array('akIsSearchable' => 1));
	}

	function saveAttribute($tour, $value = false) {
		//This method should implement 
		$av = $tour->getAttributeValueObject($this, true);
		
		parent::saveAttribute($av, 	$value);
		
		$db = Loader::db();
		
		$v = array($tour->getTourID(), $tour->getTourVersionID(), $this->getAttributeKeyID(), $av->getAttributeValueID());
		$db->Replace('TourCMSAttributeValues', array(
			'tour_id' => $tour->getTourID(), 
			'tour_version_id' => $tour->getTourVersionID(), 
			'akID' => $this->getAttributeKeyID(), 
			'avID' => $av->getAttributeValueID()
		), array('tour_id', 'tour_version_id', 'akID'));
		$tour->logVersionUpdate(TourVersion::UT_EXTENDED_ATTRIBUTE, $this->getAttributeKeyID());
		$file = $tour->getFile();
		$file->reindex();
		unset($av);
		unset($file);
		unset($tour);
	}

	function add($type, $args, $pkg = false) {
		//Here you add any special information about an attribute category into your own WidgetAttributeKeys table (if it exists), as well as run parent::add($type, $args, $pkg).
		CacheLocal::delete('tourcms_attribute_key_by_handle', $args['akHandle']);
		$ak = parent::add('tour', $type, $args, $pkg);
		return $ak;
	}

	function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from TourCMSAttributeKeys where akID = ?', array($this->getAttributeKeyID()));
		
		$r = $db->Execute('select avID from TourCMSAttributeValues where akID = ?', array($this->getAttributeKeyID()));
		
		while ($row = $r->FetchRow()) {
			$db->Execute('delete from AttributeValues where avID = ?', array($row['avID']));
		}
		
		$db->Execute('delete from TourCMSAttributeValues where akID = ?', array($this->getAttributeKeyID()));
	}

}

public class TourCMSAttributeValue() {
	
	public function setTour($tour) {
		//Whatever object you're binding these attribute to, you'll want to create a setter for this object. e.g. The CollectionAttributeValue category has a setter named setCollection. 
		$this->tour = $tour;
	}
	
	function getByID($avID) {
		$cav = new TourCMSAttributeValue();
		$cav->load($avID);
		if ($cav->getAttributeValueID() == $avID) {
			return $cav;
		}
	}
	
	function delete() {
		$db = Loader::db();
		$db->Execute('delete from TourCMSAttributeValues where tour_id = ? and akID = ? and avID = ?', array(
			$this->tour->getTourID(), 
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));
				
			// Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
			$num = $db->GetOne('select count(avID) from TourCMSAttributeValues where avID = ?', array($this->getAttributeValueID()));
			if ($num < 1) {
			parent::delete();
		}

}

?>