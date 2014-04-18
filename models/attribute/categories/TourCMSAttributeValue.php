<?php

public class TourCMSAttributeValue extends AttributeValue {
	
}

public class TourCMSAttributeKey extends AttributeKey {
	
	protected $searchIndexFieldDefinition = 'tour_id I(11) UNSIGNED NOTNULL DEFAULT 0 PRIMARY';

	function getIndexedSearchTable() {
		return 'TourCMSSearchIndexAttributes';
	}
	
	function getAttributes($pageID, $pageVersionID, $method = 'getValue') {
		
		$list = new AttributeValueList(this);
		while($list->next()) {
			$list->current();
		}
		
		Basically, whatever object the attribute key category is going to bind to, this will need to get all attributes for that object.
		This object instantiates an AttributeValueList, stores all keys within that list, and runs the method against each one.
	
	}
	
	function getColumnHeaderList() {
		return parent::getList('widget', array('akIsColumnHeader' => 1));
	}

	function getSearchableIndexedList() {
		return parent::getList('widget', array('akIsSearchableIndexed' => 1));
	}
	
	function getSearchableList() {
		return parent::getList('widget', array('akIsSearchable' => 1));
	}

	function getAttributeValue($avID, $method = 'getValue') {
		$av = WidgetAttributeValue::getByID($avID);
		$av->setAttributeKey($this);
		return call_user_func_array(array($av, $method), array());	
	}

	function getByID($akID) {
		$ak = new WidgetAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}	
	}

	function getByHandle($akHandle) {
		//This method simply needs to query the database for the ID of the matching handle and return getByID($akID)
		@ $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	 	
	 	if($db->connect_errno) {
	 		printf("Connection Failed!: %s\n", $db->connect_error);
	 	} else {
	 		$query = 'SELECT';
	 	
	 		if ($db->query($query) === TRUE) {
				//echo "Table Created";	 		
	 		} else {
	 			//echo 'Last Query: '.$this->db->info.'<br>';
	 			//echo 'Last Error: '.$this->db->error.'<br>';
	 		}
	 	}

	}


	function getList() {
		return parent::getList('widget');
	}

	function saveAttribute($object, $value = false) {
		//This method should implement $av = $object->getAttributeValueObject($this, true), and then run parent::saveAttribute($av, 	$value); Then, insert all information into your WidgetAttributeValues table.
	
	}

	function add($type, $args, $pkg = false) {
		//Here you add any special information about an attribute category into your own WidgetAttributeKeys table (if it exists), as well as run parent::add($type, $args, $pkg).
	}


	function update($args) {
		//Here you add any special information about an attribute category into your own WidgetAttributeKeys table (if it exists), as well as run parent::update($args).
	}
	

	function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from WidgetAttributeKeys where akID = ?', array($this->getAttributeKeyID()));
		$r = $db->Execute('select avID from WidgetAttributeValues where akID = ?', array($this->getAttributeKeyID()));
		while ($row = $r->FetchRow()) {
			$db->Execute('delete from AttributeValues where avID = ?', array($row['avID']));
		}
		$db->Execute('delete from WidgetAttributeValues where akID = ?', array($this->getAttributeKeyID()));
	}

}

public class TourCMSAttributeValue() {
	
	public function setWidget($widget) {
		$this->widget = $widget;
		//Whatever object you're binding these attribute to, you'll want to create a setter for this object. e.g. The CollectionAttributeValue category has a setter named setCollection. If you were binding to the Widget object, you'd create

	}
	
	function getByID($avID) {
	
		//Place this within the method
	
		$cav = new TourCMSAttributeValue();
		$cav->load($avID);
		if ($cav->getAttributeValueID() == $avID) {
			return $cav;
		}
	}
	
	function delete() {
	
		$db = Loader::db();
			$db->Execute('delete from WidgetAttributeValues where yourKeyID = ? and akID = ? and avID = ?', array(
				$this->widget->getWidgetID(), 
				$this->attributeKey->getAttributeKeyID(),
				$this->getAttributeValueID()
			));
				
			// Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
			$num = $db->GetOne('select count(avID) from WidgetAttributeValues where avID = ?', array($this->getAttributeValueID()));
			if ($num < 1) {
			parent::delete();
		}

}

?>