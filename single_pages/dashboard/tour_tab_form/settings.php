<?php
Loader::helper('concrete/interface');

$html = Loader::helper('html');
$this->addHeaderItem($html->css('dashboard.css', PKG));
$this->addHeaderItem($html->javascript('tour_tab_callback.js', PKG));

$ih = new ConcreteInterfaceHelper();
$fh = new FormHelper();
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Tour Tab Helper'), t('Please configure your tabs'), 'span8', false); ?>




<div class="cc-pane-headnote"> 

	<?php echo $fh->label('name_label', 'Tab Name'); ?>
    <?php echo $fh->text('tab_name', ''); ?>
    <button name="js_callback">Add New Tab</button>
</div>

<form method="post" class="form-horizontal" id="config-settings"
      action="<?php echo $this->action('save');?>">
   	<div class="ccm-pane-body">
		<input type="hidden" value="<?php echo $this->action('getTabOptions');?>" name="getTabOptions">
		<div id="controls">		
			<?php foreach($fields as $field=>$label) { ?>
                <div class="control-group">
                    <?php echo $fh->label($field, $label); ?>
                    <div class="controls">
                        <?php echo $fh->checkbox($label, $field, false); ?>
                    </div>
                </div>
            <?php } ?>
		</div>        
    </div>
    <div id="active-tabs">
    	<?php echo $processed_tabs; ?>
    </div>
 
    <div class='ccm-pane-footer'>
    	
        <?php echo $ih->submit('Save', null, 'right', 'primary'); ?>
    </div>
 
</form>
 
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>