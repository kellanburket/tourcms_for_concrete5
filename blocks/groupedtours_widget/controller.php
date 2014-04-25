<?php defined('C5_EXECUTE') or die(_("Access Denied."));
	
class GroupedtoursWidgetBlockController extends BlockController {
	
	protected $btHandle = 'groupedtours_widget';
	protected $btTable = "btGroupedtoursWidget";
	protected $btInterfaceWidth = "350";
	protected $btInterfaceHeight = "300";

	public function getBlockTypeName() {
		return t('TourCMS Tour Group Widget');
	}

	public function getBlockTypeDescription() {
		return t('Tour Groups');
	}
}
