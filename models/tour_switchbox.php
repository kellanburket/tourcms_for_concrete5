<?php

class TourSwitchboxModel {
	
	public static function install() {
		$db = Loader::db();
		
		$query_var = array('tour_name', 'Tour Name', 'start_time', 'Start Time', 'end_time', 'End Time', 'address', 'Postal Address', 'location', 'Primary Location', 'summary', 'Summary', 'shortdesc', 'Short Description', 'longdesc', 'Long Description', 'duration_desc', 'Duration Description', 'duration', 'Duration', 'essential', 'Essentials', 'available', 'Availability', 'itinerary', 'Itinerary', 'exp', 'Experience/Highlights', 'inc', 'Inclusions', 'ex', 'Exclusions', 'extras', 'Extras/Upgrades', 'rest', 'Restrictions');
		
		$name = $db->Execute('INSERT INTO tourSwitchboxFields (tourcms_handle, name) VALUES (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?), (?, ?)', $query_var);	
	}
	
	public static function getFields($id = 0) {
		echo 'get fields<br>';
		$db = Loader::db();
		if (!$id) {
			$fields = $db->GetAssoc('SELECT tourcms_handle, name FROM tourSwitchboxFields');
		} else {
			$fields = $db->GetAssoc('SELECT tsf.tourcms_handle, tsf.name FROM tourSwitchboxFields tsf
			INNER JOIN tourSwitchboxRelationships tsr ON tsf.field_id = tsr.field_id
			INNER JOIN tourSwitchboxTabs tst ON tst.tab_id = tsr.tab_id
			WHERE tsr.tab_id = ?', $id);
		}

		if (!$fields) {
			TourSwitchboxModel::install();	
			$fields = $db->GetAssoc('SELECT tourcms_handle, name FROM tourSwitchboxFields');
		}
		return $fields;
	}
	
	public function getAllFields() {
		$params = 'id=1&show_options=1';
		$fields = $this->tourcms->show_tour('1', $this->channel_id, $params)->tour;	
		return $this->iterate(new SimpleXMLIterator($fields->asXML()));
	}
	
	
	private function iterate($it) {
		$return = array();
		$it->rewind();
		for($it->rewind(); $it->valid(); $it->next()) {
			$return[] =	$it->key();
			if ($it->hasChildren()) { 
				//$this->iterate(new SimpleXMLIterator($it->current()->asXML()));
			}
		}
		return $return;
	}

	public static function insertFields($name, $fields, $id=0) {
		$db = Loader::db();
		
		if (!$id) {
			$db->Execute('INSERT INTO tourSwitchboxTabs (name, isActive) VALUES (?, true)', array($name));
			$id = $db->GetOne('SELECT tab_id FROM tourSwitchboxTabs WHERE name = ?', array($name));
		}
		
		$question_marks = '';
		for($i = 0; $i < count($fields); $i++) {
			$field_ids[] = $id; 
			$field_ids[] = $db->GetOne('SELECT field_id FROM tourSwitchboxFields WHERE tourcms_handle = ?', array($fields[$i]));
			$question_marks .= ' (?, ?),'; 
		}
		$question_marks = substr($question_marks, 0 , -1);
		$query = 'INSERT INTO tourSwitchboxRelationships (tab_id, field_id) VALUES'.$question_marks;
						
		$db->Execute($query, $field_ids);
	}
	
	public static function updateFields($name, $fields, $id) {
		$db = Loader::db();			
		$db->Execute('DELETE FROM tourSwitchboxRelationships WHERE id = ?', array($id));
		TourSwitchboxModel::insertFields($name, $fields, $id);		
	}
	
	public static function getTabs($activeOnly = false) {

		$db = Loader::db();

		if ($activeOnly) {
			return $db->GetAssoc('SELECT tab_id, name FROM tourSwitchboxTabs WHERE isActive = ?', array($activeonly));	
		} else {
		
			$db->GetAssoc('SELECT tab_id, name FROM tourSwitchboxTabs');
			
			
			return 	$array;
		}
	}
	
	public static function gatherData($activeOnly = false) {
		$switchbox = array();
		$tabs = TourSwitchboxModel::getTabs($activeOnly);
	
		$i = 0;
		foreach ($tabs as $id=>$name) {
			$switchbox[$i]['name'] = $name;
			$switchbox[$i]['id'] = $id;
			$switchbox[$i++]['fields'] = TourSwitchboxModel::getFields($id);				
		}
		
		return $switchbox;
	}
}