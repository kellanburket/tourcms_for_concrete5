<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 

$cID = Page::getCurrentPage()->getCollectionID();
$tourcms = Loader::helper('tourcms', PKG);
$tour = $tourcms->getTour($cID)->tour;
$images = $tour->images->image;

//get the switchbox	
Loader::model('tour_switchbox', PKG);
$data = TourSwitchboxModel::gatherData();

//$tour->images->image->url_thumbnail

$switcher =
'<div id="sb-tour-header">
	<h4 class="sb-tour-h4">'.$tour->tour_name.'</h4>
</div>
<div id="tablet-booking-elements">
	<div class="image-frame">
		<img class="sb-tour-thumbnail" src="'.$images->url_large.'">
		<div class="image-description">'.$images->image_desc.'</div>
	</div>	
	<div class="image-scroller">';

for($i = 1; $i < count($images); $i++) {
	$switcher .= '<a href="'.$images[$i]->url.'" class="tour-thumbnail"><img src="'.$images[$i]->url_thumbnail.'" alt="'.$images[$i]->image_desc.'"></a>';
}
	
$switcher .=
	'</div>
	<p class="sb-tour-description">'.$tour->shortdesc.'</p>
</div>
<div class="sb-tour-switcher">';


$tabs = '<ul class="sb-tour-tabs">';
$frame = '<div class="sb-tour-tab-frame">';
$i = 0;

foreach($data as $tab) {
	$tabs .='<li class="sb-tour-tab" id="sb-tab-'.$i.'">'.$tab['name'].'</li>';
	$frame .= '<div class="sb-tour-tab-info-wrap" id="tab-frame-'.$i++.'">';
	foreach($tab['fields'] as $field_handle=>$field_name) {
		$frame .= '<p class="sb-tab-info-head"><strong>'.$field_name.':</strong></p>';
		$frame .= '<p class="sb-tour-tab-info">'.$tour->$field_handle.'</p>';					
	}
	$frame .='</div>';
}		
$tabs .='</ul>';
$frame .='</div>';

echo $switcher.$tabs.$frame.'</div>';	
