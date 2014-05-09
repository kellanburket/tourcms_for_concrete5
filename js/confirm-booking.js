$(document).ready(function(){

	$('#booking-box').submit(function(event){
		event.preventDefault();
		
		var option_names = '';
		var option_quantities = '';
		var i = 0;
		while ( $('#option-quantity-field-' + i).val() != null ) {
			option_quantities += $('#option-quantity-field-' + i).val() + '&';
			option_names += $('#option-name-field-' + i).val() + '&';
			i++;				 
		}
		
		document.body.style.cursor='wait';
		$.post(
			cya.ajaxurl, 
			{
				action: "confirm_tour_booking",
				hotel: $('#hotel-field').val(),
				room: $('#room-field').val(),
				option_names: option_names,
				option_quantities: option_quantities,
				no_children: $('#no-children-hidden').val(), 
				no_adults: $('#no-adults-hidden').val(),
				tour_date: $('#tour-date-hidden').val(),
				tour_id: $('#tour-id-hidden').val(),
				full_refund: $('#full-refund-hidden').val()
				
			},
			function(response){
				var data = $.parseJSON(response);
				document.body.style.cursor='default';
				if (data.success == true && data.debug == false) {
					window.location = data.site_url;
				} else if (data.success == true && data.debug == true) {
					console.log(data.debug_msg);
				} else if (data.success == false) {
					alert(data.error_msg);
				}
			}, 
			'json'
			).fail(function(data) {
				alert(data);
			});
		return false;
	});

});