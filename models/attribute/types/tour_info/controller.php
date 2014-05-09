<?php

class TourInfoAttributeTypeController extends AttributeTypeController {

	protected $searchIndexFieldDefinition = 'X NULL';
	
	public function type_form() {
		$path1 = $this->getView()->getAttributeTypeURL('type_form.js');
		$path2 = $this->getView()->getAttributeTypeURL('type_form.css');
		$this->addHeaderItem(Loader::helper('html')->javascript($path1));
		$this->addHeaderItem(Loader::helper('html')->css($path2));
		$this->set('form', Loader::helper('form'));		

		if ($this->isPost()) {
			$fields = $this->getFieldsFromPost();
			$this->set('akTourName', $fields->tour_name);
			$this->set('akTourID', $fields->tour_id);

		} else if (isset($this->attributeKey)) {
			$options = $this->getOptions();
			$this->set('akTourName', $options->tour_name);
			$this->set('akTourID', $options->tour_id);

		} else {
			$this->set('akTourName', array());
			$this->set('akTourID', array());
		}
	}

	public function duplicateKey($newAK) {
		$db = Loader::db();
		$r = $db->Execute('select tour_id, tour_name, displayOrder, isEndUserAdded from atTourInfoOptions where akID = ?', $this->getAttributeKey()->getAttributeKeyID());
		while ($row = $r->FetchRow()) {
			$db->Execute('insert into atTourInfoOptions (akID, tour_id, tour_name, displayOrder, isEndUserAdded) values (?, ?, ?, ?, ?)', array(
				$newAK->getAttributeKeyID(),
				$row['tour_id'],
				$row['tour_name'],
				$row['displayOrder'],
				$row['isEndUserAdded']
			));
		}
	}
	
	public function exportKey($akey) {
		$db = Loader::db();
		$type = $akey->addChild('type');
		$r = $db->Execute('select tour_id, tour_name, displayOrder, isEndUserAdded from atTourInfoOptions where akID = ? order by displayOrder asc', $this->getAttributeKey()->getAttributeKeyID());
		$options = $type->addChild('options');
	
		while ($row = $r->FetchRow()) {
			$opt = $options->addChild('option');
			$opt->addAttribute('tour_id', $row['tour_id']);
			$opt->addAttribute('tour_name', $row['tour_name']);
			$opt->addAttribute('is-end-user-added', $row['isEndUserAdded']);
		}
		return $akey;
	}
	
	//A bit fuzzy on how this works--will have to double check.
	public function exportValue($akn) {
		$tour_name = $this->getSelectedOptions();
		if (count($tour_name) > 0) {
			$aName = $akn->addChild('tour_name');
			$aName->addChild('option', $tour_name);
		}
		return $aName;
	}
	
	//Again--a bit fuzzy on this, 
	public function importValue(SimpleXMLElement $akv) {
		if (isset($akv->tour_id)) {
			$vals = array();
			foreach($akv->tour_id->children() as $ch) {
				$vals[] = (string) $ch;
			}
			return $vals;
		}
	}
	
	//Need to look into what is loaded in with akey variable
	public function importKey($akey) {
		if (isset($akey->type)) {
			$db = Loader::db();
			if (isset($akey->type->options)) {
				foreach($akey->type->options->children() as $option) {
					TourInfoAttributeTypeOption::add($this->attributeKey, $option['tour_id'], $option['tour_name'], $option['is-end-user-added']);
				}
			}
		}
	}
	
	//Updated
	private function getFieldsFromPost() {
		$key = $this->getAttributeKey()->getAttributeKeyID();
		$name = $this->getAttributeKey()->getAttributeKeyHandle();
		$options = new TourInfoAttributeTypeOptionList($key, $name);
		
		$displayOrder = 0;		
		
		$idField = "akTourID_";
		$nameField = "akTourName_";
		
		$new_options = array(array());
		
		//print_r($options);
		
		if ($_POST) {
			//echo "POST: ";
			//print_r($_POST);
			
			
			foreach($_POST as $key => $value) {
				$opt = false;
				
				if ($value == 'TEMPLATE') {
					continue;			
					//echo "<br>ID(".$id."), VALUE IS TEMPLATE. CONTINUE... KEY(".$key."), VALUE(".$value.")";
				} elseif(strstr($key, $idField)) {
					$id = substr($key, intval(count($idField) - 1));				
					$new_options[$id]['tour_id'] = $idField;
					//echo "<br>ID(".$id."), OPTION(".$new_options[$id]['tour_id']."), KEY(".$key."), VALUE(".$value.")";
				} elseif (strstr($key, $nameField)) {
					$id = substr($key, intval(count($nameField) - 1));			
					$new_options[$id]['tour_name'] = $nameField;
					//echo "<br>ID(".$id."), OPTION(".$new_options[$id]['tour_name']."), KEY(".$key."), VALUE(".$value.")";
				} else {
					continue;
					//echo "<br>ID(".$id."), CONTINUE... KEY(".$key."), VALUE(".$value.")";
				} 
			}
	
			if ($_POST['TourInfoNewOption_' . $id] == $id) {
				$opt = new TourInfoAttributeTypeOption(0, $new_options[$id]['tour_id'], $new_options[$id]['tour_name'], $displayOrder);
				$opt->tempID = $id;
				//echo "<br>TourInfo NEW Option: ";
				//print_r($opt);
			} else if ($_POST['TourInfoExistingOption_' . $id] == $id) {
				$opt = new TourInfoAttributeTypeOption($id, $new_options[$id]['tour_id'], $new_options[$id]['tour_name'], $displayOrder);
				//echo "<br>TourInfo Existing Option: ";
				//print_r($opt);
			}
			
			if (is_object($opt)) {
				$options->add($opt);
				$displayOrder++;
				return $options;
			}
		}
	}
	
	//Not sure where the footer and header items are coming from
	public function form() {

		$options = $this->getSelectedOptions();
		$options = $this->getOptions();
		$selectedOptions = array();

		echo $options;

		$this->addFooterItem(Loader::helper('html')->javascript('jquery.ui.js'));
		$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
	}
	
	public function search() {
		$selectedOptions = $this->request('atTourInfoOptionID');
		if (!is_array($selectedOptions)) {
			$selectedOptions = array();
		}
		$this->set('selectedOptions', $selectedOptions);
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atTourInfoOptionsSelected where avID = ?', array($this->getAttributeValueID()));
	}

	public function deleteKey() {
		$db = Loader::db();
		$r = $db->Execute('select ID from atTourInfoOptions where akID = ?', array($this->attributeKey->getAttributeKeyID()));
		while ($row = $r->FetchRow()) {
			$db->Execute('delete from atTourInfoOptionsSelected where atTourInfoOptionID = ?', array($row['ID']));
		}
		$db->Execute('delete from atTourInfoOptions where akID = ?', array($this->attributeKey->getAttributeKeyID()));
	}

	//I created this function
	protected function updateSelectedValues($avID, $atTourInfoOptionID, $ctID) {
		$db = Loader::db();
		
		if ($avID && $atTourInfoOptionID) { 
			$selected = $db->Execute('SELECT * FROM atTourInfoOptionsSelected WHERE avID = ? AND ctID = ?', array($avID, $ctID));
			
			if (!$selected->getRows()) {
				$db->Execute(
					'INSERT INTO atTourInfoOptionsSelected (avID, atTourInfoOptionID, ctID) VALUES (?, ?, ?)', 
					array($avID, $atTourInfoOptionID, $ctID)
				);
			} else {
				$db->Execute(
					'UPDATE atTourInfoOptionsSelected SET atTourInfoOptionID = ? WHERE avID = ? AND ctID = ?', 
					array($atTourInfoOptionID, $avID, $ctID)
				);		
			}
		}
	}

	//Totally Updated and customized
	public function saveForm($data) {
		extract($_POST);

		if (is_array($tour_category)) {
			reset($tour_category);
			$this->updateSelectedValues(key($tour_category), current($tour_category), $entryID); 	
		} elseif (is_array($tour_name)) {
			reset($tour_name);
			$this->updateSelectedValues(key($tour_name), current($tour_name), $entryID); 	
		}
	}

	
	public function getDisplayValue() {
		$list = $this->getSelectedOptions();
		$html = '';
		foreach($list as $l) {
			$html .= $l . '<br/>';
		}
		return $html;
	}

	public function getDisplaySanitizedValue() {
		$list = $this->getSelectedOptions();
		$html = '';
		foreach($list as $l) {
			$html .= $l->getTourID() . '<br/>';
		}
		return $html;
	}
	
	public function validateForm($p) {
		$this->load();
		$options = $this->request('atTourInfoOptionID');
	
		$options = array_filter((Array) $this->request('atSelectNewOption'));
		if (is_array($options) && count($options) > 0) {
			return true;
		} else if (array_shift($this->request('atTourInfoOptionID')) != null) {
			return true;
		}

		return count($options) > 0;
	}
	
	public function searchForm($list) {
		$options = $this->request('atTourInfoOptionID');
		$optionText = array();
		$db = Loader::db();
		$tbl = $this->attributeKey->getIndexedSearchTable();
		if (!is_array($options)) {
			return $list;
		}
		foreach($options as $id) {
			if ($id > 0) {
				$opt = TourInfoAttributeTypeOption::getByID($id);
				if (is_object($opt)) {
					$optionText[] = $opt->getTourID(true);
					$optionQuery[] = $opt->getTourID(false);
				}
			}
		}
		if (count($optionText) == 0) {
			return false;
		}
		
		$i = 0;
		foreach($optionQuery as $val) {
			$val = $db->quote('%||' . $val . '||%');
			$multiString .= 'REPLACE(' . $tbl . '.ak_' . $this->attributeKey->getAttributeKeyHandle() . ', "\n", "||") like ' . $val . ' ';
			if (($i + 1) < count($optionQuery)) {
				$multiString .= 'OR ';
			}
			$i++;
		}
		$list->filter(false, '(' . $multiString . ')');
		return $list;
	}
	
	public function getValue() {
		$list = $this->getSelectedOptions();
		return $list;	
	}
	
    public function getSearchIndexValue() {
        $str = "\n";
        $list = $this->getSelectedOptions();
        foreach($list as $l) {
            $l = (is_object($l) && method_exists($l,'__toString')) ? $l->__toString() : $l;
            $str .= $l . "\n";
        }
        // remove line break for empty list
        if ($str == "\n") {
            return '';
        }
        return $str;
    }
	
	//This is the function called by Page::getAttribute('attribute_handle');
	public function getSelectedOptions() {
		$page = Page::getCurrentPage();
		$pageID = $page->getCollectionID();
		$ak = $this->getAttributeKey()->getAttributeKeyID();
		
		$db = Loader::db();
		$rows = $db->GetAll(
			"SELECT atio.tour_id, atio.tour_name from atTourInfoOptionsSelected atios
			INNER JOIN atTourInfoOptions atio ON atios.atTourInfoOptionID = atio.tour_id
			INNER JOIN AttributeKeys ak ON atio.akID = ak.akID
			WHERE ctID = ? AND ak.akID = ?", 
			array($pageID, $ak)
		);
		
		return $rows[0]['tour_name'];		
	}
	

	/**
	 * returns a list of available options optionally filtered by an sql $like statement ex: startswith%
	 * @param string $like
	 * @return TourInfoAttributeTypeOptionList
	 */
	public function getOptions($like = NULL) {
		$aK = $this->getAttributeKey()->getAttributeKeyID();
		$db = Loader::db();
		if(isset($like) && strlen($like)) {
			$r = $db->Execute('select ID, tour_id, tour_name, displayOrder from atTourInfoOptions where akID = ? AND atTourInfoOptions.value LIKE ? order by displayOrder asc', array($aK, $like));
		} else {
			$r = $db->Execute('select ID, tour_id, tour_name, displayOrder from atTourInfoOptions where akID = ? order by displayOrder asc', array($aK));
		}

		$key = $this->getAttributeKey()->getAttributeKeyID();
		$name = $this->getAttributeKey()->getAttributeKeyHandle();
		$options = new TourInfoAttributeTypeOptionList($key, $name);

		while ($row = $r->FetchRow()) {
			$opt = new TourInfoAttributeTypeOption($row['ID'], $row['tour_id'], $row['tour_name'], $row['displayOrder']);
			$options->add($opt);
		}
		return $options;
	}
		
	public function saveKey($data) {
		$ak = $this->getAttributeKey();
		$akID = $this->getAttributeKey()->getAttributeKeyID();
		$db = Loader::db();
		$initialOptionSet = $this->getOptions();
		$postValues = $this->getFieldsFromPost();
		
		
						
		// Now we add the options
		$key = $this->getAttributeKey()->getAttributeKeyID();
		$name = $this->getAttributeKey()->getAttributeKeyHandle();
		$newOptionSet = new TourInfoAttributeTypeOptionList($key, $name);

		$displayOrder = 0;
		if (is_array($postValues)) {
			foreach($postValues as $option) {
				$opt = $option->saveOrCreate($ak);
				$newOptionSet->add($opt);
				$displayOrder++;
			}
		}

		foreach($initialOptionSet as $iopt) {
			if (!$newOptionSet->contains($iopt)) {
				$iopt->delete();
			}
		}
	}
}

class TourInfoAttributeTypeOption extends Object {
	
	public function __construct($ID, $tour_id, $tour_name, $displayOrder, $ctID = 0, $usageCount = false) {
		$this->ID = $ID;
		$this->tour_id = $tour_id;
		$this->tour_name = $tour_name;
		$this->th = Loader::helper('text');
		$this->displayOrder = $displayOrder;	
		$this->ctID = $ctID;
	}
	
	public function getID() {
		return $this->ID;
	}
	
	public function getPageID() {
		return $this->ctID;
	}
	
	public function getTourName($sanitize = true) {
		if (!$sanitize) {
			return $this->tour_name;
		} else {
			return $this->th->specialchars($this->tour_name);
		}
	}

	public function getTourID($sanitize = true) {
		if (!$sanitize) {
			return $this->tour_id;
		} else {
			return $this->th->specialchars($this->tour_id);
		}
	}

	public static function add($ak, $tour_id, $tour_name, $isEndUserAdded = 0) {
		$db = Loader::db();
		$th = Loader::helper('text');
		// this works because displayorder starts at zero. So if there are three items, for example, the display order of the NEXT item will be 3.
		$displayOrder = $db->GetOne('select count(ID) from atTourInfoOptions where akID = ?', array($ak->getAttributeKeyID()));			

		$v = array($ak->getAttributeKeyID(), $displayOrder, $th->sanitize($tour_id), $th->sanitize($tour_name), $isEndUserAdded);
		$db->Execute('insert into atTourInfoOptions (akID, displayOrder, tour_id, tour_name, isEndUserAdded) values (?, ?, ?, ?, ?)', $v);
		
		return TourInfoAttributeTypeOption::getByID($db->Insert_ID());
	}
		
	public static function getByPageID($id) {
		$db = Loader::db();
		$row = $db->GetRow("select akID, tour_id, tour_name, displayOrder from atTourInfoOptions atio INNER JOIN atTourInfoOptionsSelected atios ON atio.tour_id = atios.atTourInfoOptionID WHERE ctID = ?", array($id));
		if (isset($row['ctID'])) {
			$obj = new TourInfoAttributeTypeOption($row['akID'], $row['tour_id'], $row['tour_name'], $row['displayOrder']);
			return $obj;
		}	
	}
		
	public static function getByID($id) {
		$db = Loader::db();
		$row = $db->GetRow("select akID, tour_id, tour_name, displayOrder from atTourInfoOptions where ID = ?", array($id));
		if (isset($row['ID'])) {
			$obj = new TourInfoAttributeTypeOption($row['akID'], $row['tour_id'], $row['tour_name'], $row['displayOrder']);
			return $obj;
		}
	}
	
	public static function getByTourName($value, $ak = false) {
		$db = Loader::db();
		if (is_object($ak)) {
			$row = $db->GetRow("select ID, tour_id, tour_name, displayOrder from atTourInfoOptions where value = ? and akID = ?", array($value, $ak->getAttributeKeyID()));
		} else {
			$row = $db->GetRow("select ID, tour_id, tour_name, displayOrder from atTourInfoOptions where value = ?", array($value));
		}
		if (isset($row['ID'])) {
			$obj = new TourInfoAttributeTypeOption($row['ID'], $row['tour_id'], $row['tour_name'], $row['displayOrder']);
			return $obj;
		}
	}

	public function saveOrCreate($ak) {
		if ($this->tempID != false || $this->ID==0) {
			return TourInfoAttributeTypeOption::add($ak, $this->tour_id, $this_tour_name);
		} else {
			$db = Loader::db();
			$th = Loader::helper('text');
			$db->Execute('update atTourInfoOptions set tour_id = ? where ID = ?', array($th->sanitize($this->tour_id), $this->ID));
			$db->Execute('update atTourInfoOptions set tour_name = ? where ID = ?', array($th->sanitize($this->tour_name), $this->ID));
			return TourInfoAttributeTypeOption::getByID($this->ID);
		}
	}
		
	public function __toString() {
		$return = ''.$this->tour_name;
		return $return;		
	}
}

class TourInfoAttributeTypeOptionList extends Object implements Iterator {

	private $options = array();
	public $key;
	public $name;	
	public $selection;
	public $pageID;
		
	public function __construct($key, $name) {
		$this->key = $key;
		$this->name = $name;		

		$page = Page::getCurrentPage();
		$this->pageID = $page->getCollectionID();
		 
		$db = Loader::db();
		$options = $db->GetAll(
			"SELECT atio.tour_name FROM atTourInfoOptions atio
			INNER JOIN atTourInfoOptionsSelected atios ON atio.ID = atios.atTourInfoOptionID
			WHERE atio.akID = ?
			AND atios.ctID = ?", 
			array($this->key, $this->pageID)
		);
		//echo 'Key: '.$key.'<br>';
		//echo 'Name: '.$name.'<br>';		
		//echo 'Page ID: '.$this->pageID.'<br>';		
		//echo 'options: ';
		//print_r($options);
		
		//print_r($options);
		//exit;
		$this->selection = $options['tour_name'];
	}
	
	public function add(TourInfoAttributeTypeOption $opt) {
		$this->options[] = $opt;
	}	
	
	public function contains(TourInfoAttributeTypeOption $opt) {
		foreach($this->options as $o) {
			if ($o->getID() == $opt->getID()) {
				return true;
			}
		}
		return false;
	}
	
	public function __toString() {
		$str = '<select name="'.$this->name.'['.$this->key.']" value="'.$this->selection.'">';
		$i = 0;
		
		foreach($this->options as $opt) {
			$str .= '<option value="'.$opt->getTourID().'">'.$opt->getTourName().'</option>';
			$i++;
		}
		$str .= '</select>';
		return $str;

	}
	
	public function rewind() {
		reset($this->options);
	}
	
	public function current() {
		return current($this->options);
	}
	
	public function key() {
		return key($this->options);
	}
	
	public function next() {
		next($this->options);
	}
	
	public function valid() {
		return $this->current() !== false;
	}
	
	public function count() {return count($this->options);}
		
	public function get($index) {
		return $this->options[$index];
	}
	
	public function getOptions() {
		return $this->options;
	}


}
?>