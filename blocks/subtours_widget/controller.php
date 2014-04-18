<?php defined('C5_EXECUTE') or die(_("Access Denied."));
	
class SubtoursWidgetBlockController extends BlockController {
	
	protected $btHandle = 'subtours_widget';
	protected $btTable = "btSubtoursWidget";
	protected $btInterfaceWidth = "350";
	protected $btInterfaceHeight = "300";

	public function getBlockTypeName() {
		return t('TourCMS Tour Scroller Widget');
	}

	public function getBlockTypeDescription() {
		return t('Scrolls through tours');
	}
}
