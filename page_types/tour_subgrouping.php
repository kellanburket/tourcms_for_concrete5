<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	define("THEME_BODY_CLASS", "tour_details");
	$page = Page::getCurrentPage();
	
	Loader::library('tourcms/config', 'tourcms_custom_widgets'); 
	Loader::model('attribute/categories/collection');
	
	$pkg = $page->getPackageHandle();
	
	$html = Loader::helper('html');
	$this->addHeaderItem($html->css('subgrouping-style.css', $pkg));
	
	$category_search = 'category='.preg_replace('#\s#', '+', $page->getAttribute('tour_category'));		
	$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");
	$channel_id = SiteConfig::get("channel_id");
	$tours = $tourcms->search_tours($category_search, $channel_id);
	
	foreach ($tours->tour as $tour) { ?>



	<div class="subtour-wrap">
		<div class="subtour-div subtour-pic">
			<img class="subtour-thumbnail" src="<?php echo $tour->thumbnail_image; ?>">
		</div>
		
		<div class="subtour-div subtour-desc">
			<a href="<?php echo $tour->tour_url; ?>"><h3 class="subtour-h3"><?php echo $tour->tour_name; ?></h3></a>
			<p class="subtour-description"><?php echo $tour->shortdesc; ?></p>
			
			<table class="subtour-table">
				<tr>
					<td class="subtour-td"><strong>From</strong></td>
					<td class="subtour-td from_price"><?php echo $tour->from_price; ?></td>
				</tr>
				<tr>
					<td class="subtour-td"><strong>Duration</strong></td>
					<td class="subtour-td subtour-duration"><?php echo $tour->duration_desc;?></td>
				</tr>
			</table>
			<a href="<?php echo $tour->tour_url; ?>"><button class="btn select-button">SELECT</button></a>
		</div>
	</div>
<?php }
?>

<?php $this->inc('elements/footer.php'); ?>
<?php $this->inc('elements/end.php'); ?>