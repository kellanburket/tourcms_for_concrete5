
$(document).ready(function() {
	var index = 0;
	
	$('button[name="js_update"]').click(function(event) {
		event.preventDefault();
		event.stopPropagation();
	});
	
	$('button[name="js_callback"]').click(function(event) {
		event.preventDefault();
		event.stopPropagation();
		
		var tab_name = $('input[name="tab_name"]').val();		
		var selection = tab_name + ":";

		if (!tab_name) {
			alert('Please Enter a Name For Your New Tab!');
			return;
		}
		
		$('#controls').find('.control-group').each( function() {
			$checkbox = $(this).find('.controls .ccm-input-checkbox');
			var name = $checkbox.val();
			var checked = $checkbox[0].checked;

			if (checked) {
				selection += name + '&';	
			}
		});
		
		selection = selection.slice(0, -1);
		
		$('#active-tabs').append('<div class="processed_tab"><input type="hidden" name="tour_tab_option[' + (index++) + ']" value="' + selection + '">' + tab_name +'</div>');		
		$('.processed_tab').click(function(event) { handleTabClick(event, $(this)) });
	});


	$('.processed_tab').click(function(event) { handleTabClick(event, $(this)) });
	
	
	function handleTabClick(event, $target) {
		event.preventDefault();
		event.stopPropagation();
		
		var value = $target.find('input[type="hidden"]').val();
		value = value.split(':');

		var vitals = value[0].split('&');
		var tab_name = vitals[0];
		$('#tab_name').val(tab_name);
		$('button[name="js_callback"]').text('Update');
		$('button[name="js_callback"]').attr('name', 'js_update');
		var fields = new Array();
		if (value[1]) {
			fields = value[1].split('&');	
		}

			
		$('#controls').find('.control-group').each( function() {
			
			$checkbox = $(this).find('.controls .ccm-input-checkbox');
			$checkbox.prop('checked', false);

			//console.log($checkbox);
			var check_box_name = $checkbox.attr('id');
			for(var i = 0; i < fields.length; i++) {
				
				if (fields[i] == check_box_name) {
					console.log(fields[i]);
					$checkbox.prop('checked', true);
					
					$checkbox.parent().animate(
						{background: '#00ff11'}, 
						{duration: 400, complete: function() {
							$checkbox.parent().animate(
								{background: '#ffffff'}, 
								{duration: 300}
							); 
						}}
					);
					
					fields.splice(i, 1);
					break;
				}
			}
		});	
	};		

});