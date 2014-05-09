<?php defined('C5_EXECUTE') or die(_("Access Denied."));
$f = Loader::helper('form'); 

$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");
$channel_id = SiteConfig::get("channel_id");

$results = $tourcms->search_tours('', $channel_id);
$tours = array();
foreach ($results->tour as $tour) {
	$tours[$tour->tour_id] = $tour->tour_name; 		
}

?>
 
<fieldset class="ccm-attribute-tour-name-line">
	<?php $f->label($this->field('tour_id'), t('Tour Name')); ?>
	<select>
    	
    </select>
</fieldset>
