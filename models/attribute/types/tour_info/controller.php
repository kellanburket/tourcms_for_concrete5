<?php

class TourInfoAttributeTypeController extends AttributeTypeController {

	private $akTourInfoAllowMultipleValues;
	private $akTourInfoAllowOtherValues;
	private $akTourInfoOptionDisplayOrder;

	protected $searchIndexFieldDefinition = 'X NULL';
	
	public function type_form() {
		$path1 = $this->getView()->getAttributeTypeURL('type_form.js');
		$path2 = $this->getView()->getAttributeTypeURL('type_form.css');
		$this->addHeaderItem(Loader::helper('html')->javascript($path1));
		$this->addHeaderItem(Loader::helper('html')->css($path2));
		$this->set('form', Loader::helper('form'));		
		$this->load();

		
		if ($this->isPost()) {
			$fields = $this->getFieldsFromPost();

			$this->set('akTourInfoTourName', $fields->tour_name);
			$this->set('akTourInfoTourId', $fields->tour_id);

		} else if (isset($this->attributeKey)) {
			$options = $this->getOptions();

			$this->set('akTourInfoTourName', $options->tour_name);
			$this->set('akTourInfoTourId', $options->tour_id);

		} else {
			$this->set('akTourInfoTourName', array());
			$this->set('akTourInfoTourId', array());
		}
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		
		//Load the settings for this attribute from table atTourInfoSettings;
		
		$row = $db->GetRow('select akTourInfoAllowMultipleValues, akTourInfoOptionDisplayOrder, akTourInfoAllowOtherValues from atTourInfoSettings where akID = ?', $ak->getAttributeKeyID());
		$this->akTourInfoAllowMultipleValues = $row['akTourInfoAllowMultipleValues'];
		$this->akTourInfoAllowOtherValues = $row['akTourInfoAllowOtherValues'];
		$this->akTourInfoOptionDisplayOrder = $row['akTourInfoOptionDisplayOrder'];

		$this->set('akTourInfoAllowMultipleValues', $this->akTourInfoAllowMultipleValues);
		$this->set('akTourInfoAllowOtherValues', $this->akTourInfoAllowOtherValues);			
		$this->set('akTourInfoOptionDisplayOrder', $this->akTourInfoOptionDisplayOrder);			
	}

	public function duplicateKey($newAK) {
		$this->load();
		$db = Loader::db();
		$db->Execute('insert into atTourInfoSettings (akID, akTourInfoAllowMultipleValues, akTourInfoOptionDisplayOrder, akTourInfoAllowOtherValues) values (?, ?, ?, ?)', array($newAK->getAttributeKeyID(), $this->akTourInfoAllowMultipleValues, $this->akTourInfoOptionDisplayOrder, $this->akTourInfoAllowOtherValues));	
		$r = $db->Execute('select value, displayOrder, isEndUserAdded from atTourInfoOptions where akID = ?', $this->getAttributeKey()->getAttributeKeyID());
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
		$this->load();
		$db = Loader::db();
		$type = $akey->addChild('type');
		$type->addAttribute('allow-multiple-values', $this->akTourInfoAllowMultipleValues);
		$type->addAttribute('display-order', $this->akTourInfoOptionDisplayOrder);
		$type->addAttribute('allow-other-values', $this->akTourInfoAllowOtherValues);
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
		$list = $this->getSelectedOptions();
		if ($list->count() > 0) {
			
			$aID = $akn->addChild('tour_id');
			$aName = $akn->addChild('tour_name');
			
			foreach($list as $l) {
				$aID->addChild('option', (string) $l);
				$aName->addChild('option', (string) $l);
			}
		}
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
			$akTourInfoAllowMultipleValues = $akey->type['allow-multiple-values'];
			$akTourInfoOptionDisplayOrder = $akey->type['display-order'];
			$akTourInfoAllowOtherValues = $akey->type['allow-other-values'];
			$db = Loader::db();
			$db->Replace('atTourInfoSettings', array(
				'akID' => $this->attributeKey->getAttributeKeyID(), 
				'akTourInfoAllowMultipleValues' => $akTourInfoAllowMultipleValues, 
				'akTourInfoAllowOtherValues' => $akTourInfoAllowOtherValues,
				'akTourInfoOptionDisplayOrder' => $akTourInfoOptionDisplayOrder
			), array('akID'), true);

			if (isset($akey->type->options)) {
				foreach($akey->type->options->children() as $option) {
					TourInfoTypeOption::add($this->attributeKey, $option['tour_id'], $option['tour_name'], $option['is-end-user-added']);
				}
			}
		}
	}
	
	//Updated
	private function getFieldsFromPost() {
		$options = new TourInfoTypeOptionList();
		$displayOrder = 0;		
		
		$idField = "akTourInfo_";
		$nameField = "akTourName_";
		
		$new_options = array(array());
		
		foreach($_POST as $key => $value) {
			$opt = false;

			// strip off the prefix to get the ID
			// now we determine from the post whether this is a new option
			// or an existing. New ones have this value from in the akSelectValueNewOption_ post field
			
			if ($value == 'TEMPLATE') {
				continue;			
			} elseif(strstr($key, $idField)) {
				$id = substr($key, count($idField));				
				$new_options[$id]['tour_id'] = $idField;
			} elseif (strstr($key, $nameField)) {
				$id = substr($key, count($nameField));			
				$new_options[$id]['tour_name'] = $idField;
			} else {
				continue;
			} 
		}

		if ($_POST['TourInfoNewOption_' . $id] == $id) {
			$opt = new TourInfoTypeOption(0, $new_options[$id]['tour_id'], $new_options[$id]['tour_name'], $displayOrder);
			$opt->tempID = $id;
		} else if ($_POST['TourInfoExistingOption_' . $id] == $id) {
			$opt = new TourInfoTypeOption($id, $new_options[$id]['tour_id'], $new_options[$id]['tour_name'], $displayOrder);
		}
		
		if (is_object($opt)) {
			$options->add($opt);
			$displayOrder++;
		}

		return $options;
	}
	
	//Not sure where the footer and header items are coming from
	public function form() {
		$this->load();
		$options = $this->getSelectedOptions();
		$selectedOptions = array();

		foreach($options as $opt) {
			$selectedOptions[] = $opt->getID();
			$selectedOptionTours[$opt->getID()][tour_name] = $opt->getTourName();
			$selectedOptionTours[$opt->getID()][tour_id] = $opt->getTourInfo();
		}
			
		$this->set('selectedOptionTours',$selectedOptionTours);
		$this->set('selectedOptions', $selectedOptions);
		
		$this->addFooterItem(Loader::helper('html')->javascript('jquery.ui.js'));
		$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
	}
	
	public function search() {
		$this->load();	
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
		$db->Execute('delete from atTourInfoSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
		$r = $db->Execute('select ID from atTourInfoOptions where akID = ?', array($this->attributeKey->getAttributeKeyID()));
		while ($row = $r->FetchRow()) {
			$db->Execute('delete from atTourInfoOptionsSelected where atTourInfoOptionID = ?', array($row['ID']));
		}
		$db->Execute('delete from atTourInfoOptions where akID = ?', array($this->attributeKey->getAttributeKeyID()));
	}

	public function saveForm($data) {
		$this->load();
		
		if ($this->akTourInfoAllowOtherValues && is_array($data['atSelectNewOption'])) {
			$options = $this->getOptions();
						
			foreach($data['atSelectNewOption'] as $newoption) {
				// check for duplicates
				$existing = false;
				foreach($options as $opt) {
					if(strtolower(trim($newoption)) == strtolower(trim($opt->getTourInfo(false)))) {
						$existing = $opt;
						break;
					}
				}
				if($existing instanceof TourInfoTypeOption) {
					$data['atTourInfoOptionID'][] = $existing->getID();
				} else {
					$optobj = TourInfoTypeOption::add($this->attributeKey, $newoption, 1);
					$data['atTourInfoOptionID'][] = $optobj->getID();
				}
			}
		}

		if(is_array($data['atTourInfoOptionID'])) {
			$data['atTourInfoOptionID'] = array_unique($data['atTourInfoOptionID']);
		}		
		$db = Loader::db();
		$db->Execute('delete from atTourInfoOptionsSelected where avID = ?', array($this->getAttributeValueID()));
		if (is_array($data['atTourInfoOptionID'])) {
			foreach($data['atTourInfoOptionID'] as $optID) {
				if ($optID > 0) {
					$db->Execute('insert into atTourInfoOptionsSelected (avID, atTourInfoOptionID) values (?, ?)', array($this->getAttributeValueID(), $optID));
					if ($this->akTourInfoAllowMultipleValues == false) {
						break;
					}
				}
			}
		}
	}
	
	// Sets select options for a particular attribute
	// If the $value == string, then 1 item is selected
	// if array, then multiple, but only if the attribute in question is a select multiple
	// Note, items CANNOT be added to the pool (even if the attribute allows it) through this process.
	public function saveValue($name) {
		$db = Loader::db();
		$this->load();
		$options = array();		
		
		if (is_array($name) && $this->akTourInfoAllowMultipleValues) {
			foreach($name as $n) {
				$opt = TourInfoTypeOption::getByTourName($n, $this->attributeKey);
				if (is_object($opt)) {
					$options[] = $opt;	
				}
			}
		} else {
			if (is_array($name)) {
				$name = $name[0];
			}
			
			$opt = TourInfoTypeOption::getByTourName($name, $this->attributeKey);
			if (is_object($opt)) {
				$options[] = $opt;	
			}
		}
		
		$db->Execute('delete from atTourInfoOptionsSelected where avID = ?', array($this->getAttributeValueID()));
		if (count($options) > 0) {
			foreach($options as $opt) {
				$db->Execute('insert into atTourInfoOptionsSelected (avID, atTourInfoOptionID) values (?, ?)', array($this->getAttributeValueID(), $opt->getID()));
				if ($this->akTourInfoAllowMultipleValues == false) {
					break;
				}
			}
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
			$html .= $l->getTourInfo() . '<br/>';
		}
		return $html;
	}
	
	public function validateForm($p) {
		$this->load();
		$options = $this->request('atTourInfoOptionID');
		if ($this->akTourInfoAllowOtherValues) {
			$options = array_filter((Array) $this->request('atSelectNewOption'));
			if (is_array($options) && count($options) > 0) {
				return true;
			} else if (array_shift($this->request('atTourInfoOptionID')) != null) {
				return true;
			}
		}
		if ($this->akTourInfoAllowMultipleValues) {
			return count($options) > 0;
		} else {
			if ($options[0] != false) {
				return $options[0] > 0;
			}
		}
		return false;
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
				$opt = TourInfoTypeOption::getByID($id);
				if (is_object($opt)) {
					$optionText[] = $opt->getTourInfo(true);
					$optionQuery[] = $opt->getTourInfo(false);
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
	
	//Checked
	public function getSelectedOptions() {
		if (!isset($this->akTourInfoOptionDisplayOrder)) {
			$this->load();
		}
		$db = Loader::db();
		switch($this->akTourInfoOptionDisplayOrder) {
			case 'popularity_desc':
				$options = $db->GetAll("select ID, tour_id, tour_name, displayOrder, (select count(s2.atTourInfoOptionID) from atTourInfoOptionsSelected s2 where s2.atTourInfoOptionID = ID) as total from atTourInfoOptionsSelected inner join atTourInfoOptions on atTourInfoOptionsSelected.atTourInfoOptionID = atTourInfoOptions.ID where avID = ? order by total desc, value asc", array($this->getAttributeValueID()));
				break;
			case 'alpha_asc':
				$options = $db->GetAll("select ID, tour_id, tour_name, displayOrder from atTourInfoOptionsSelected inner join atTourInfoOptions on atTourInfoOptionsSelected.atTourInfoOptionID = atTourInfoOptions.ID where avID = ? order by value asc", array($this->getAttributeValueID()));
				break;
			default:
				$options = $db->GetAll("select ID, tour_id, tour_name, displayOrder from atTourInfoOptionsSelected inner join atTourInfoOptions on atTourInfoOptionsSelected.atTourInfoOptionID = atTourInfoOptions.ID where avID = ? order by displayOrder asc", array($this->getAttributeValueID()));
				break;
		}
		$db = Loader::db();
		$list = new TourInfoTypeOptionList();
		foreach($options as $row) {
			$opt = new TourInfoTypeOption($row['ID'], $row['tour_id'], $row['tour_name'], $row['displayOrder']);
			$list->add($opt);
		}
		return $list;
	}
	
	public function action_load_autocomplete_values() {
		$this->load();
		$values = array();
			// now, if the current instance of the attribute key allows us to do autocomplete, we return all the values
		if ($this->akTourInfoAllowMultipleValues && $this->akTourInfoAllowOtherValues) {
			$options = $this->getOptions($_GET['term'] . '%');
			foreach($options as $opt) {
				$values[] = $opt->getTourInfo(false);
			}
		}
		print Loader::helper('json')->encode($values);
	}
	
	public function getOptionUsageArray($parentPage = false, $limit = 9999) {
		$db = Loader::db();
		$q = "select atTourInfoOptions.value, atTourInfoOptionID, count(atTourInfoOptionID) as total from Pages inner join CollectionVersions on (Pages.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) inner join CollectionAttributeValues on (CollectionVersions.cID = CollectionAttributeValues.cID and CollectionVersions.cvID = CollectionAttributeValues.cvID) inner join atTourInfoOptionsSelected on (atTourInfoOptionsSelected.avID = CollectionAttributeValues.avID) inner join atTourInfoOptions on atTourInfoOptionsSelected.atTourInfoOptionID = atTourInfoOptions.ID where Pages.cIsActive = 1 and CollectionAttributeValues.akID = ? ";
		$v = array($this->attributeKey->getAttributeKeyID());
		if (is_object($parentPage)) {
			$v[] = $parentPage->getCollectionID();
			$q .= "and cParentID = ?";
		}
		$q .= " group by atTourInfoOptionID order by total desc limit " . $limit;
		$r = $db->Execute($q, $v);
		$list = new TourInfoTypeOptionList();
		$i = 0;
		while ($row = $r->FetchRow()) {
			$opt = new TourInfoTypeOption($row['atTourInfoOptionID'], $row['value'], $i, $row['total']);
			$list->add($opt);
			$i++;
		}		
		return $list;
	}
	
	/**
	 * returns a list of available options optionally filtered by an sql $like statement ex: startswith%
	 * @param string $like
	 * @return TourInfoTypeOptionList
	 */
	public function getOptions($like = NULL) {
		if (!isset($this->akTourInfoOptionDisplayOrder)) {
			$this->load();
		}
		
		$db = Loader::db();
		switch($this->akTourInfoOptionDisplayOrder) {
			case 'popularity_desc':
				if(isset($like) && strlen($like)) {
					$r = $db->Execute('select ID, tour_id, tour_name, displayOrder, count(atTourInfoOptionsSelected.atTourInfoOptionID) as total 
						from atTourInfoOptions left join atTourInfoOptionsSelected on (atTourInfoOptions.ID = atTourInfoOptionsSelected.atTourInfoOptionID) 
						where akID = ? AND atTourInfoOptions.value LIKE ? group by ID order by total desc, value asc', array($this->attributeKey->getAttributeKeyID(),$like));
				} else {
					$r = $db->Execute('select ID, tour_id, tour_name, displayOrder, count(atTourInfoOptionsSelected.atTourInfoOptionID) as total 
						from atTourInfoOptions left join atTourInfoOptionsSelected on (atTourInfoOptions.ID = atTourInfoOptionsSelected.atTourInfoOptionID) 
						where akID = ? group by ID order by total desc, value asc', array($this->attributeKey->getAttributeKeyID()));
				}
				break;
			case 'alpha_asc':
				if(isset($like) && strlen($like)) {
					$r = $db->Execute('select ID, tour_id, tour_name, displayOrder from atTourInfoOptions where akID = ? AND atTourInfoOptions.value LIKE ? order by value asc', array($this->attributeKey->getAttributeKeyID(),$like));
				} else {
					$r = $db->Execute('select ID, tour_id, tour_name, displayOrder from atTourInfoOptions where akID = ? order by value asc', array($this->attributeKey->getAttributeKeyID()));
				}
				break;
			default:
				if(isset($like) && strlen($like)) {
					$r = $db->Execute('select ID, tour_id, tour_name, displayOrder from atTourInfoOptions where akID = ? AND atTourInfoOptions.value LIKE ? order by displayOrder asc', array($this->attributeKey->getAttributeKeyID(),$like));
				} else {
					$r = $db->Execute('select ID, tour_id, tour_name, displayOrder from atTourInfoOptions where akID = ? order by displayOrder asc', array($this->attributeKey->getAttributeKeyID()));
				}
				break;
		}
		$options = new TourInfoTypeOptionList();
		while ($row = $r->FetchRow()) {
			$opt = new TourInfoTypeOption($row['ID'], $row['tour_id'], $row['tour_name'], $row['displayOrder']);
			$options->add($opt);
		}
		return $options;
	}
		
	public function saveKey($data) {
		$ak = $this->getAttributeKey();
		
		$db = Loader::db();

		$initialOptionSet = $this->getOptions();
		$selectedPostValues = $this->getSelectValuesFromPost();
		
		$akTourInfoAllowMultipleValues = $data['akTourInfoAllowMultipleValues'];
		$akTourInfoAllowOtherValues = $data['akTourInfoAllowOtherValues'];
		$akTourInfoOptionDisplayOrder = $data['akTourInfoOptionDisplayOrder'];
		
		if ($data['akTourInfoAllowMultipleValues'] != 1) {
			$akTourInfoAllowMultipleValues = 0;
		}
		if ($data['akTourInfoAllowOtherValues'] != 1) {
			$akTourInfoAllowOtherValues = 0;
		}
		if (!in_array($data['akTourInfoOptionDisplayOrder'], array('display_asc', 'alpha_asc', 'popularity_desc'))) {
			$akTourInfoOptionDisplayOrder = 'display_asc';
		}
				
		// now we have a collection attribute key object above.
		$db->Replace('atTourInfoSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akTourInfoAllowMultipleValues' => $akTourInfoAllowMultipleValues, 
			'akTourInfoAllowOtherValues' => $akTourInfoAllowOtherValues,
			'akTourInfoOptionDisplayOrder' => $akTourInfoOptionDisplayOrder
		), array('akID'), true);
		
		// Now we add the options
		$newOptionSet = new TourInfoTypeOptionList();
		$displayOrder = 0;
		foreach($selectedPostValues as $option) {
			$opt = $option->saveOrCreate($ak);
			if ($akTourInfoOptionDisplayOrder == 'display_asc') {
				$opt->setDisplayOrder($displayOrder);
			}
			$newOptionSet->add($opt);
			$displayOrder++;
		}
		
		// Now we remove all options that appear in the 
		// old values list but not in the new
		foreach($initialOptionSet as $iopt) {
			if (!$newOptionSet->contains($iopt)) {
				$iopt->delete();
			}
		}
	}

	/**
	 * Convenience methods to retrieve a select attribute key's settings
	 */
	public function getAllowMultipleValues() {
		if (is_null($this->akTourInfoAllowMultipleValues)) {
			$this->load();
		}
		return $this->akTourInfoAllowMultipleValues;
	}
	
	public function getAllowOtherValues() {
		if (is_null($this->akTourInfoAllowOtherValues)) {
			$this->load();
		}
		return $this->akTourInfoAllowOtherValues;
	}
	
	public function getOptionDisplayOrder() {
		if (is_null($this->akTourInfoOptionDisplayOrder)) {
			$this->load();
		}
		return $this->akTourInfoOptionDisplayOrder;
	}

}

class TourInfoTypeOption extends Object {
	
	public function __construct($ID, $tour_id, $tour_name, $displayOrder, $usageCount = false) {
		$this->ID = $ID;
		$this->tour_id = $tour_id;
		$this->tour_name = $tour_name;
		$this->th = Loader::helper('text');
		$this->displayOrder = $displayOrder;	
		$this->usageCount = $usageCount;	
	}
	
	public function getID() {return $this->ID;}
	
	public function getTourName($sanitize = true) {
		if (!$sanitize) {
			return $this->tour_name;
		} else {
			return $this->th->specialchars($this->tour_name);
		}
	}

	public function getTourInfo($sanitize = true) {
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
		$db->Execute('insert into atSelectTourInfoOptions (akID, displayOrder, tour_id, tour_name, isEndUserAdded) values (?, ?, ?, ?, ?)', $v);
		
		return TourInfoTypeOption::getByID($db->Insert_ID());
	}
		
	public static function getByID($id) {
		$db = Loader::db();
		$row = $db->GetRow("select ID, tour_id, tour_name, displayOrder from atSelectTourInfoOptions where ID = ?", array($id));
		if (isset($row['ID'])) {
			$obj = new TourInfoTypeOption($row['ID'], $row['tour_id'], $row['tour_name'], $row['displayOrder']);
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
			$obj = new TourInfoTypeOption($row['ID'], $row['tour_id'], $row['tour_name'], $row['displayOrder']);
			return $obj;
		}
	}

	public function saveOrCreate($ak) {
		if ($this->tempID != false || $this->ID==0) {
			return TourInfoTypeOption::add($ak, $this->tour_id, $this_tour_name);
		} else {
			$db = Loader::db();
			$th = Loader::helper('text');
			$db->Execute('update atTourInfoOptions set tour_id = ? where ID = ?', array($th->sanitize($this->tour_id), $this->ID));
			$db->Execute('update atTourInfoOptions set tour_name = ? where ID = ?', array($th->sanitize($this->tour_name), $this->ID));
			return TourInfoTypeOption::getByID($this->ID);
		}
	}
		
	public function __toString() {
		$ret = '';
		if ($this->tour_id) {
			$ret .= $this->tour_id . "\n";
		}
		if ($this->tour_name) {
			$ret .= $this->tour_name . "\n";
		}
		return $ret;		
	}
}

class TourInfoOptionList extends Object implements Iterator {

	private $options = array();
	
	public function add(TourInfoTypeOption $opt) {
		$this->options[] = $opt;
	}	
	
	public function contains(TourInfoTypeOption $opt) {
		foreach($this->options as $o) {
			if ($o->getID() == $opt->getID()) {
				return true;
			}
		}
		return false;
	}
	
	public function __toString() {
		$str = '';
		$i = 0;
		foreach($this->options as $opt) {
			$str .= $opt->getTourInfo();
			$i++;
			if ($i < count($this->options)) {
				$str .= "\n";
			}
		}
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