<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	define("THEME_BODY_CLASS", "tour_details");
	$cID= Page::getCurrentPage()->getCollectionID();
	$pkg = Page::getCurrentPage()->getPackageHandle();

	$switchbox = BlockType::getByHandle('tour_switchbox', $pkg); 
	$calendar = BlockType::getByHandle('calendar_widget', $pkg);
	
	$html = Loader::helper('html');
	$this->addHeaderItem($html->css('tour-style.css', $pkg));
	$this->addHeaderItem($html->javascript('sprintf.js', $pkg));
	$this->addHeaderItem($html->javascript('sidebar-calendar.js', $pkg));
	$this->addHeaderItem($html->javascript('rate-calculation.js', $pkg)); 
?>
	
	<div id="sb-tour-widget-wrap">
        <?php $switchbox->render('view'); ?>       
    </div>
    
	<?php $calendar->render('view'); ?>