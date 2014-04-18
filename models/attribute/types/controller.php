<?php

class TourAttributeType extends AttributeTypeController {

	protected $searchIndexFieldDefinition = array(
		'tour_id' => 'I 10 NULL',
		'tour_name' => 'C 255 NULL',
	);

	public $helpers = array('form');
	
	public function searchKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		// todo make this less hardcoded (with ak_ in front of it)
		$str = '(ak_' . $this->attributeKey->getAttributeKeyHandle() . '_tour_id like '.$qkeywords.' or ';
		$str .= 'ak_' . $this->attributeKey->getAttributeKeyHandle() . '_tour_name like '.$qkeywords.' or ';
		return $str;
	}
	
	public function searchForm($list) {
		$tour_id = $this->request('tour_id');
		$tour_name = $this->request('tour_name');
		if ($tour_id) {
			$list->filterByAttribute(array('tour_id' => $this->attributeKey->getAttributeKeyHandle()), '%' . $tour_id. '%', 'like');
		}
		if ($tour_name) {
			$list->filterByAttribute(array('tour_name' => $this->attributeKey->getAttributeKeyHandle()), '%' . $tour_name. '%', 'like');
		}
		return $list;
	}
	
	public function search() {
		$this->load();
		print $this->form();
		$v = $this->getView();
		$this->set('search', true);
		$v->render('form');
	}

	public function saveForm($data) {
		$this->saveValue($data);
	}

	public function validateForm($data) {
		return ($data['tour_id'] != '' && $data['tour_name'] != '');	
	}	
	
	public function getSearchIndexValue() {
		$v = $this->getValue();
		$args = array();
		$args['tour_id'] = $v->get_tour_id();
		$args['tour_name'] = $v->get_tour_name();
		return $args;
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atTour where avID = ?', array($id));
		}
	}
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atTour where avID = ?', array($this->getAttributeValueID()));
	}
	
	public function saveValue($data) {
		$db = Loader::db();
		if ($data instanceof TourAttributeTypeValue) {
			$data = (array) $data;
		}
		extract($data);
		$db->Replace('atTour', array('avID' => $this->getAttributeValueID(),
			'tour_id' => $tour_id,
			'tour_name' => $tour_name
			),
			'avID', true
		);
	}

	public function getValue() {
		$val = TourAttributeTypeValue::getByID($this->getAttributeValueID());		
		return $val;
	}
	
	public function getDisplayValue() {
		$v = Loader::helper('text')->entities($this->getValue());
		$ret = nl2br($v);
		return $ret;
	}
		
	public function validateKey($data) {
		$e = parent::validateKey($data);		
		return $e;
	}

	public function duplicateKey($newAK) {
		$this->load();
		$db = Loader::db();
		$db->Execute('insert into atTourSettings (akID) values (?)', array($newAK->getAttributeKeyID()));	
	}

	public function exportKey($akey) {
		$this->load();
		$type = $akey->addChild('type');
		return $akey;
	}

	public function exportValue($akn) {
		$avn = $akn->addChild('value');
		$address = $this->getValue();
		$avn->addAttribute('tour_id', $address->gettour_id());
		$avn->addAttribute('tour_name', $address->gettour_name());
	}

	public function importValue(SimpleXMLElement $akv) {
		if (isset($akv->value)) {
			$data['tour_id'] = $akv->value['tour_id'];
			$data['tour_name'] = $akv->value['tour_name'];
			return $data;
		}
	}
	
	public function importKey($akey) {
		if (isset($akey->type)) {
			$this->saveKey($data);
		}
	}

	public function saveKey($data) {
		$e = Loader::helper('validation/error');
		
		$ak = $this->getAttributeKey();
		$db = Loader::db();

		if (!$e->has()) {
			$db->Replace('atTourSettings', array(
				'akID' => $ak->getAttributeKeyID(), 
			), array('akID'), true);
		} else {
			return $e;
		}
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
	}

	public function type_form() {
		$this->load();
	}
	
	public function form() {
		$this->load();
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
			$this->set('tour_id', $value->gettour_id());
			$this->set('tour_name', $value->gettour_name());
		}
		$this->set('key', $this->attributeKey);
	}
	
}

class TourAttributeTypeValue extends Object {
	
	public static function getByID($avID) {
		$db = Loader::db();
		$value = $db->GetRow("select avID, tour_id, tour_name from atTour where avID = ?", array($avID));
		$aa = new TourAttributeTypeValue();
		$aa->setPropertiesFromArray($value);
		if ($value['avID']) {
			return $aa;
		}
	}	
	
	public function get_tour_id() {return $this->tour_id;}
	public function get_tour_name() {return $this->tour_name;}
	
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

?>