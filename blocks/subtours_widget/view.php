<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::packageElement('config', 'tourcms_custom_widgets'); 
$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");
$channel_id = SiteConfig::get("channel_id");

$results = $tourcms->search_tours('', $channel_id);

$tours = $results->tour;
foreach ($tours as $tour) { ?>

	<div class="subtour-wrap">
		<div class="subtour-div subtour-pic">
			<img class="subtour-thumbnail" src="<?php echo $tour->thumbnail_image; ?>">
		</div>
		
		<div class="subtour-div subtour-desc">
			<h3 class="subtour-h3"><?php echo $tour->tour_long_name; ?></h3>
			<p class="subtour-description"><?php echo $tour->shortdesc; ?></p>
			
			<table class="subtour-table">
				<tr>
					<td class="subtour-td">From</td>
					<td class="subtour-td from_price"><?php echo $tour->from_price; ?></td>
				</tr>
				<tr>
					<td class="subtour-td">Duration</td>
					<td class="subtour-td subtour-duration"><?php echo $tour->duration_desc;?></td>
				</tr>
			</table>
			<button class="select-button">SELECT</button>
		</div>
	</div>
<?php }
?>