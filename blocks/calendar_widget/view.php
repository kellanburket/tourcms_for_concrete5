<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<!--Begin Tour Form -->
<form id="sb-tour-form" action="" method="post">
    <input name="get_calendar" type="hidden" value="<?php echo $calendar_tools; ?>">
    <input name="tourcms_toolbox" type="hidden" value="<?php echo $tourcms_toolbox; ?>">
    <input name="tour_id" type="hidden" value="<?php echo $tour_id; ?>">
    <input name="user_id" type="hidden" value="<?php echo $user; ?>">
    <div id="sb-tour-pick-a-date-wrapper">
        <div id="sb-tour-calendar">
          
            <div id="sb-tour-head">
                <button id="sb-tour-back-one" class="sb-tour-button" disabled>&larr;</button>
                <?php if ($days_in_month != intval($today)) { ?>
                    <input type="hidden" name="current_month" value="<?php echo date("n", strtotime("now")); ?>">
                    <input type="hidden" name="current_year" value="<?php echo date("Y", strtotime("now")); ?>">
                    <span id="sb-tour-month"><?php echo date("F")." ".date("Y"); ?></span>
                <?php } else { ?>
                    <input type="hidden" name="current_month" value="<?php echo date("n", strtotime("+1 day")); ?>">
                    <input type="hidden" name="current_year" value="<?php echo date("Y", strtotime("+1 day")); ?>">
                    <span id="sb-tour-month">
                    <?php echo $month = date("F", strtotime("+1 day")); ?>
                    <?php echo $year = date("Y", strtotime("+1 day")); ?>
                    </span>
                <?php } ?>
                <button id="sb-tour-forward-one" class="sb-tour-button">&rarr;</button>
            </div>
           
            <div id="tourcms-sidebar-table"></div>
        </div>
        <ul class="availability-key">
            <li class="a-key-li">Selected<div id="selected-key"></div></li>
            <li class="a-key-li">Available<div id="available-key"></div></li>
            <li class="a-key-li">Unavailable<div id="unavailable-key"></div></li>                   	
        </ul>
        <div class="sb-tour-activity-date-wrap">
            <p class="sb-tour-p" id="activity-date-lb">Activity Date</p>
            <input type="text" id="sb-tour-activity-date-field" name="activity_date" class="sb-confirm-field" />
        </div>
    </div>
    
    <div class="sb-divider"></div>
    
    <div class="sb-rates">
        <?php echo $rates; ?>
    </div>            
    
    <div class="sb-divider"></div>
    
    <h5 class="confirm-booking-h5">Available Upgrades</h5>

    <table id="sb-available-upgrades">
        <tbody>
           <?php echo $options; ?>
        </tbody> 
    </table>
  
    
    <div class="sb-divider"></div>
    <div id="sb-tour-promo-code">
        <p id="sb-promo-label">Promotional Code</p>
        <input type="text" name="promo_code" id="promo-code-input" class="sb-confirm-field" />
    </div>
    <div id="sb-tour-savings-box">
        <p id="sb-tour-you-saved-text">                    	
        </p>
    </div>
    <div class="sb-divider"></div>
    
    <div id="tourcms-totals">
    
    </div>
        
    <div id="sb-tour-submit-div">
        <button id="sb-submit" disabled>BOOK NOW</button>
    </div>
                
</form>
<!-- End Tour Form -->