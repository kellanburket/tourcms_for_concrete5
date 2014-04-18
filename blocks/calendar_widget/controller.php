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
}
