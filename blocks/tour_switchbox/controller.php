<?php
class TourSwitchboxBlockController extends BlockController {
 
 	protected $btHandle = 'tour_switchbox';
	protected $btTable = "btTourSwitchbox";
	protected $btInterfaceWidth = "350";
	protected $btInterfaceHeight = "300";
 
 	public function install() {
		//Loader::model('tour_switchbox', PKG);
		//TourSwitchboxModel::install;
	}
	
	public function getBlockTypeName() {
		return t('TourCMS Switchbox');
	}

	public function getBlockTypeDescription() {
		return t('Tour Fields Switchbox');
	}
}
?>