<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	define("THEME_BODY_CLASS", "tour_details");
	$this->inc('elements/header.php');
	$this->inc('elements/nav.php');
?>

<?php
	$a = new Area('Breadcrumbs');
	$a->setBlockLimit(1);  
	$a->display($c);
?>

<div id="main" class="container-fluid">
	<div class="row-fluid">
		<div class="span8">
            <!--Body content-->
            <figure id="main-image">
				<?php
					$a = new Area('Main Image');
					$a->setBlockLimit(1);  
					$a->display($c);
                ?>
            </figure>
			<?php
				$a = new Area('Main Content');
				$a->setBlockLimit(1);  
				$a->display($c);
            ?>
        </div>
        <div class="span4">
			<!--Sidebar content-->
            <div class="side-box purchase-box">
                <h3>Purchase Tickets Online</h3>
                
                <?php
					$a = new Area('Main');
					$a->setBlockLimit(1);  
					$a->display($c);
				?>
                
                <?php
					$stack = Stack::getByName('Tour Booking Tools and Info');				
					if( $stack ) $stack->display();
				?>
                
            	<?php /*?><form name="tickets">
                	<div class="add-to-purchase">
                        <h3>Customize your adventure!</h3>
                        <p>Add one or more of the following options to your San Francisco Sightseeing Tour and maximize your view of the city:</p>
                        
                        
                        <?php
						// get list of tours in the group including their bdID? 
						
						
						// use jquery / ajax to load tour info into the block
						
						// use jquery ajax to check the availability of tours using existing block as a model
						
						// use jquery to submit select tour for purchase
						
						?>
                        
                        
                        
                        <label class="radio"><input type="radio" name="add-to-adventure" value="Golden Gate Bridge" /> Golden Gate Bridge</label>
                        <label class="radio"><input type="radio" name="add-to-adventure" value="San Francisco Bay Cruise" /> San Francisco Bay Cruise</label>
                        <label class="radio"><input type="radio" name="add-to-adventure" value="Alcatraz" /> Alcatraz</label>
                        <label class="radio"><input type="radio" name="add-to-adventure" value="Muir Woods" /> Muir Woods</label>
                        <label class="radio"><input type="radio" name="add-to-adventure" value="Sausalito" /> Sausalito</label>
                        <label class="radio"><input type="radio" name="add-to-adventure" value="nochange" checked="checked" /> Just the standard tour, thank you.</label>
                    </div>
                    <div style="padding:10px;">
                        <label>Date / Time</label>
                        <input type="text" placeholder="datepicker">
                        <label class="radio"><input type="radio" name="time" value="10:00 AM" /> 10:00 AM</label>
                        <label class="radio"><input type="radio" name="time" value="2:00 PM" /> 2:00 PM</label>
                        
                        <label>Adults (x $55)</label>
                        <input type="text" name="adult">
                        <label>Seniors (x $53)</label>
                        <input type="text" name="senior">
                        <label>Children (x $30)</label>
                        <input type="text" name="child">
                    </div>
                    
                    
                    <div class="subtotal">
                        Subtotal: $0.00
                    </div>
                
                	<input type="submit" class="btn btn-large btn-primary" value="Buy Tickets">
             
          		</form><?php */?>


                <div class="general-info">
                    <?php /*?><h4>General Information</h4>
                    <p><strong>Round Trip Travel Time:</strong><br />
                    3.5 hours (approximately)</p>
                    <p><strong>Departure Times:<br />
                    </strong>10:00 AM, 2:00 PM</p>
                    <p><strong>Pricing:</strong><br />
                    $55 Adult, $53 Senior, $30 Child</p>
                    <p><strong>Take the Virtual Tour:</strong><br />
                    </p>
                    <p><strong>Download PDF Brochure:</strong><br />
                    Ultimate City Tour (211 KB)<br />
                    </p><?php */?>
                    <p>Share this page:</p>
                    <span class='st_fblike'></span>
                    <span class='st_fbsend'></span>
                    <span class='st_facebook'></span>
                    <span class='st_twitter'></span>
                    <span class='st_email'></span>
                </div>
            
          </div>
            <?php
				$a = new Area('Sidebar');
				$a->display($c);
            ?>
    	</div>
	</div>
</div>

<?php $this->inc('elements/footer.php'); ?>
<?php $this->inc('elements/end.php'); ?>