<?php defined('C5_EXECUTE') or die(_("Access Denied."));
	
class CalendarWidgetBlockController extends BlockController {
	
	protected $btHandle = 'calendar_widget';
	protected $btTable = "btCalendarWidget";
	protected $btInterfaceWidth = "350";
	protected $btInterfaceHeight = "300";

	public function getBlockTypeName() {
		return t('TourCMS Calendar Widget');
	}

	public function getBlockTypeDescription() {
		return t('TourCMS Calendar Widget');
	}
	
	private function set_block_tool($tool_name){
		$tool_helper = Loader::helper('concrete/urls');
		$bt = BlockType::getByHandle($this->btHandle);
		$this->set ($tool_name, '/packages/tourcms_custom_widgets/blocks/calendar_widget/tools/'.$tool_name.'.php');
	}

	private function set_package_tool($tool_name){
		$tool_helper = Loader::helper('concrete/urls');
		$this->set ( $tool_name, $tool_helper->getToolsURL($tool_name, PKG));
	}
	
	private function set_tools() {
		$this->set_block_tool('calendar_tools');
		$this->set_package_tool('tourcms_toolbox');
	}
	
	public function add(){
		$this->set_tools();
	}
	
	public function edit(){
		$this->set_tools();
	}
	
	public function view(){
		$cID = Page::getCurrentPage()->getCollectionID();
		$tourcmsHelper = Loader::helper('tourcms', PKG);
		$tour = $tourcmsHelper->getTour($cID)->tour;
		
		$month = date("n");
		$year = date("Y");

		$this->set('today', intval(date('d'))); 
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('days_in_month', cal_days_in_month(CAL_GREGORIAN, intval($month), intval($year))); 
		$this->set('tour_id', $tour->tour_id);
		$this->set('tour_price', $tour->from_price_display);
		$this->set('tour_price', $tour->from_price_display);
		$this->set('rates', $this->getRates($tour->new_booking->people_selection->rate));
		$this->set('options', $this->getOptions($tour->options->option));
		
		$this->set_tools();
	}
	
	public function getRates($rates) {
		$return = '';
		foreach($rates as $rate) {
			$return .= '<div class="sb-tour-rates" id="'.$rate->label_1.'">';
  			$return .= '<p class="sb-tour-guests-label">'.$rate->label_1;
			$return .= ($rate->label_2) ? ' '.$rate->label_2.'</p>' : '</p>';
			$return .= '<input type="number" min="0" name="no_'.strtolower($rate->label_1).'" class="sb-guests-input sb-confirm-field" />';
        	$return .= '</div>';  
		}
		return $return;		
	}
	
	public function getOptions($tour_options) {
	 	$i = 0; 
		$return = '';
		
		foreach ($tour_options as $key=>$option) {
			$return .= '<tr class="sb-tour-upgrades-tr">';
			$return .= '<td class="sb-tour-upgrades-td" colspan="2">'.$option->option_name.'</td></tr>';
			$return .= '<tr class="sb-tour-upgrades-tr">';
			$return .= '<td class="sb-tour-upgrades-td">Price: ';
			$return .= $option->from_price_display.'</td>';	
			$return .= '<td class="sb-tour-upgrades-td">';
			$return .= '<input type="number" name="option_quantity_'.$i.'" id="option-quantity-field-'.$i.'" class="sb-confirm-field" min="0">';
			$return .= '</td>';					
			$return .= '<input type="hidden" name="option_name_'.$i.'" value="'.$option->option_name.'" id="option-name-field-'.$i.'">';
			$return .= '<input type="hidden" name="option_rate_'.$i.'" value="'.$option->from_price.'" id="option-rate-field-'.$i.'" >';
			$return .= '</tr>';		
			$i++;
		}
		$return .= '<input type="hidden" name="total_options" value="'.$i.'" id="options-total">';
		return $return;
	}
}