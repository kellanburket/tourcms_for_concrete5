<?php
defined('C5_EXECUTE') or die("Access Denied.");

if (!defined('PKG')) {
	define('PKG', 'tourcms_custom_widgets');
}
 
class DashboardTourTabFormSettingsController extends Controller {
 
    function view() {
		$this->set('processed_tabs', $this->getProcessedTabs());
		$this->set('fields', $this->getFields());
    }
 
    public function on_start() {
        $this->error = Loader::helper('validation/error');
    }
	
    function save() {        
		if (!$this->error->has()) {
        	Loader::model('tour_switchbox', PKG);
		
			foreach($_POST['tour_tab_option'] as $option) {

				$var = explode(':', $option);
				$vitals = explode('&', $var[0]);
				$name = $vitals[0];
				$id = $vitals[1];
				$fields = explode('&', $var[1]);
	
				if (!$id)
					TourSwitchboxModel::insertFields($name, $fields);
				else
					TourSwitchboxModel::updateFields($name, $fields, $id);
			}
			$this->set('message', t('Config Saved'));
		}
        $this->view();
    }

    public function on_before_render() {
		$this->set('error', $this->error);
    }

	public function getFields() {
		Loader::model('tour_switchbox', PKG);
		return array_flip(TourSwitchboxModel::getFields());
	}
	
	public function getTabOptions() {
	
	}
	
	public function getProcessedTabs() {
		Loader::model('tour_switchbox', PKG);
		$tabs = TourSwitchboxModel::gatherData();

		$processed_tabs = '';
		$index = 0;
		
		for ($i = 0; $i < count($tabs); $i++) {
			$selection = $tabs[$i]['name'].'&'.$tabs[$i]['id'].':';

			foreach($tabs[$i]['fields'] as $field) {
				$selection .= $field.'&';		
			}
				
			$selection = substr($selection, 0, -1);

			$processed_tabs .= '<div class="processed_tab">
				<input type="hidden"
				<input type="hidden" name="tour_tab_option['.$index.']" 
				value="'.$selection.'">'.$tabs[$i]['name'].'</div>';
			$index++;
			
		}
		return $processed_tabs;
	}
}
?>