<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	define("THEME_BODY_CLASS", "tour_details");
	Loader::packageElement('config', 'tourcms_custom_widgets'); 
	Loader::model('attribute/categories/collection');
	$ak = CollectionAttributeKey::getByHandle('tour_category');
	//print_r($ak);
	$page = Page::getCurrentPage();
	$reflect = new ReflectionClass($page);
	$methods = $reflect->getMethod('getAttributeValueObject');
	//print_r($methods);
	
	print_r($ak);
	exit;
	$cat = preg_replace('#\s#', '+', $page->getAttribute('tour_category'));
	
	//$category = $c->getAttribute('tour_category');// ;
	//var_dump($c);
	//var_dump($c->getAttribute();
	//exit;
	?>
		

	<style>
	.list-item {
		display: inline-block;
		width: 290px!important;
		position: relative;
		box-sizing: border-box;
	}
	#content {
		background: none;
		padding: 0;
		width: auto;
		height: auto;
	}
	.list-area {
		width: auto;
		float: none;
		margin: 0;
	}	
	img.subtour-thumbnail {
		width: 100%!important;
	}
	figure {
		width: auto!important;
	}

	</style>

	<?php foreach ($tours as $tour) { ?>



	<div class="subtour-wrap">
	<?php echo 'Category: '.$cat; ?>
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

<?php $this->inc('elements/footer.php'); ?>
<?php $this->inc('elements/end.php'); ?>