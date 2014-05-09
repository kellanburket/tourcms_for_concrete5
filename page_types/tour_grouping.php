<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('tourcms/config', 'tourcms_custom_widgets'); 

$tourcms = new TourCMS(0, SiteConfig::get("api_private_key"), "simplexml");
$channel_id = SiteConfig::get("channel_id");
$site_url = View::url('/');
$tours_url = View::url('/tours/');
$categories = array(
	array(
		'name'=>'Alcatraz Tours',
		'image'=>$site_url.'files/5513/6191/6922/list-alcatraz.jpg',
		'url'=> $tours_url.'alcatraz-island'
		),
	array(
		'name'=>'Golden Gate Bridge',
		'image'=>$site_url.'files/3913/6479/8938/list-attractions-golden-gate.jpg',
		'url'=> $tours_url.'golden-gate-bridge'
		),
	array(
		'name'=>'Monterey Bay Aquarium, Carmel & Cannery Row Tours',
		'image'=>$site_url.'files/1513/6479/8938/list-tours-aquarium.jpg',
		'url'=> $tours_url.'monterey-carmel'
		),
	array(
		'name'=>'Muir Woods California Redwoods Tours',
		'image'=>$site_url.'files/1213/6191/6924/list-muirwoods.jpg',
		'url'=>$tours_url.'muir-woods-california-redwoods-tour'
		),
	array(
		'name'=>'Napa and Sonoma Winery Tours',
		'image'=>$site_url.'files/5613/6191/6925/list-napa.jpg',
		'url'=>$tours_url.'napa-sonoma-wineries'		
		),
	array(
		'name'=>'Tours Departing from Santa Clara',
		'image'=>$site_url.'files/9613/6609/8536/barbary-coast.jpg',
		'url'=>$tours_url.'santa-clara'
		),
	array(
		'name'=>'Tours of San Francisco',
		'image'=>$site_url.'files/2313/6191/6926/list-sftour.jpg',
		'url'=>$tours_url.'san-francisco-tour'
		),
	array(
		'name'=>'Yosemite National Park',
		'image'=>$site_url.'files/3313/6191/6928/list-yosemite.jpg',
		'url'=>$tours_url.'yosemite'
		)
	);

$results = $tourcms->search_tours('', $channel_id);
$tours = $results->tour; ?>

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
	<div id="main">
		<ul class="span12 list-area">
		
		<?php foreach ($categories as $category) { ?>
		<?php extract($category); ?>

			<li class="list-item">
				<figure class="list">
					<a href="<?php echo $url; ?>">
						<img class="subtour-thumbnail" src="<?php echo $image; ?>">
					</a>
				</figure>
				<p>
					<a title-"<?php echo $name; ?>" href="<?php echo $url; ?>">
						<?php echo $name; ?>
					</a>
				</p>
			</li>
	<?php } ?>
		
		</ul>
	</div>