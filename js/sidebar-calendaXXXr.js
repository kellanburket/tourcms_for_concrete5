var $last = null;
var child_rate;
var adult_rate;
var no_adults = 0;
var no_children = 0;
var total_price = 0;

var base_url =  "//" + location.host + "/";


wp-content/plugins/choose-your-adventure/";

$(document).ready(function() {
	
	(function(){	
		//console.log(event.type);
		var tour = $("input[name=tour_id]").val();
	
		$.ajax({
			url: base_url + "calendar-sidebar.php",
			type: "POST",
			data: {
				"tour_id": tour
			}
		}).done(function(html) {
			$('#tourcms-sidebar-table').replaceWith(html);
			$('#sb-tour-month').text(months[month-1] + " " + year);
			$('.tourcms-sidebar-td').click(function(event){
				event.preventDefault();
				event.stopPropagation();
				selected_date = month + "/" + $(this).text() + "/" + year;
				
				if( $('input[name="no_adults"]').val() > 0 || $('input[name="no_children"]').val() > 0 )
					$('#sb-submit').prop('disabled', false);
					
				$('#tourcms-totals').replaceWith(updateTotals());
				
				$('#sb-tour-activity-date-field').val(selected_date);
				if ($last != null) {
					$last.css({
						backgroundColor: "#ffffff",
						color: "#1D9FE6"				
					});	
				}
				$last = $(this);
				$(this).css({
					color: "#ffffff",
					backgroundColor: "#1D9FE6"
				});

			});
		}).fail(function(data) {
			alert('There was an problem accessing tour availabilites. Please try again in a moment.');
		});
	})();
		
	
	
	$("input").keypress(function (evt) {
		//Deterime where our character code is coming from within the event
		var charCode = evt.charCode || evt.keyCode;
		if (charCode  == 13) { //Enter key's keycode
		
		}
	});
	
	var date = new Date();

	var month = date.getMonth() + 1;
	var current_month = month;
	var months = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

	var day = 1;
	var today = day;

	var year = date.getFullYear();
	var current_year = year;
	
	var tour;
	var last_number = 1;
	
	$('#sb-tour-month').text(months[current_month-1] + " " + current_year);
	
	$('.sb-tour-tab').click(function(event) {
		event.preventDefault();
		event.stopPropagation();
		var s = $(this).attr("id").split("-");
		var number = s[2];
		//console.log("Number " + number);
		//console.log("Last Number " + last_number);
		$('.sb-tour-tab-info-wrap').hide();		
		
		$('.sb-tour-tab').css({ color:"#777777"});
		$('#tab-frame-' + number).show();
		$('#sb-tab-' + number).css({ color: "#46B3DD"});
		
		var factor = Math.abs(last_number - number);
		//console.log(Math.abs(last_number - number));

		if (last_number > number && factor == 1) {	
			$('.arrow-left').stop().animate(
				{top: "-=59"},
				200
			);	
		} else if (last_number < number && factor == 1) {
			$('.arrow-left').stop().animate(
				{top: "+=59"},
				200
			);
		} else if (last_number > number && factor == 2) {
			$('.arrow-left').stop().animate(
				{top: "-=118"},
				200
			);
		} else if (last_number < number && factor == 2) {
			$('.arrow-left').stop().animate(
				{top: "+=118"},
				200
			);
		}	
	

		last_number = number;
	});
	
	$('#sb-tour-forward-one').click(function(event) {		
		event.preventDefault();
		event.stopPropagation();
		//console.log(event.type);
		$('#sb-tour-back-one').removeAttr("disabled");
		tour = $("input[name=tour_id]").val();
		day = 1;
		
		if (month == 12) {
			month = 1;
			year = year + 1;
		}
		else {
			month++;
		}	

		document.body.style.cursor = 'progress';

		$.ajax({
			url: base_url + "calendar-sidebar.php",
			type: "POST",
			data: {
				"selected_month": month,
				"selected_year": year,
				"selected_day": day,
				"tour_id": tour
			}
		}).done(function(html) {
			$('#tourcms-sidebar-table').replaceWith(html);
			$('#sb-tour-month').text(months[month-1] + " " + year);
			$('.tourcms-sidebar-td').click(function(event){
				event.preventDefault();
				event.stopPropagation();
				selected_date = month + "/" + $(this).text() + "/" + year;
				
				if( $('input[name="no_adults"]').val() > 0 || $('input[name="no_children"]').val() > 0 )
					$('#sb-submit').prop('disabled', false);

				$('#tourcms-totals').replaceWith(updateTotals());
				
				$('#sb-tour-activity-date-field').val(selected_date);
				if ($last != null) {
					$last.css({
						backgroundColor: "#ffffff",
						color: "#1D9FE6"				
					});	
				}
				$last = $(this);
				$(this).css({
					color: "#ffffff",
					backgroundColor: "#1D9FE6"
				});

			});
		}).fail(function(data) {
			alert('There was an problem accessing tour availabilites. Please try again in a moment.');
		}).always(function(data) {
			document.body.style.cursor = 'default';
		});
	});

	$('.sb-confirm-field').change(function(event){
		event.preventDefault();
		event.stopPropagation();
		
		if( $('#sb-tour-activity-date-field') )
			$('#sb-submit').prop('disabled', false);

		$('#tourcms-totals').replaceWith(updateTotals());
	});

	$('.sb-confirm-field').click(function(event){
		event.preventDefault();
		event.stopPropagation();
		$('#tourcms-totals').replaceWith(updateTotals());
	});

	
	$('.tourcms-sidebar-td').click(function(event){
		event.preventDefault();
		event.stopPropagation();
		selected_date = month + "/" + $(this).text() + "/" + year;
		$('#sb-tour-activity-date-field').val(selected_date);

		$('#tourcms-totals').replaceWith(updateTotals());
		
		if ($last != null) {
			$last.css({
				backgroundColor: "#ffffff",
				color: "#1D9FE6"				
			});	
		}
		$last = $(this);		
		$(this).css({
			color: "#ffffff",
			backgroundColor: "#1D9FE6"
		});
	});
	

	$('#sb-tour-back-one').click(function(event) {	
		event.preventDefault();
		event.stopPropagation();
		tour = $("input[name=tour_id]").val();
		day = 1;
		
		if (month == 1) {
			month = 12;
			year = year - 1;
		}
		else {
			month--;
		}	

		document.body.style.cursor = 'progress';
		
		if (month == current_month) {
				$('#ab-tour-back-one').attr("disabled", "disabled");
		}
		
		$.ajax({
			url: base_url + "calendar-sidebar.php",
			type: "POST",
			data: {
				"selected_month": month,
				"selected_year": year,
				"selected_day": day,
				"tour_id": tour
			}
		}).done(function(html) {
			$('#tourcms-sidebar-table').replaceWith(html);
			$('#sb-tour-month').text(months[month-1] + " " + year);
		}).fail(function(data) {
			alert('There was an problem accessing tour availabilites. Please try again in a moment.');
		}).always(function(data) {
			document.body.style.cursor = 'default';
		});
	});
	
	$('#sb-submit').click(function(event){
		event.preventDefault();
		event.stopPropagation();
		
		var option_names = '';
		var option_quantities = '';
		var i = 0;
		for (i = 0; i <= $('#options-total').val(); i++) {
			if ($('#option-quantity-field-' + i).val() > 0) {
				option_quantities += $('#option-quantity-field-' + i).val() + '&';
				option_names += $('#option-name-field-' + i).val() + '&';
			}
		}
		
		document.body.style.cursor='wait';
		$.post(
			cya.ajaxurl, 
			{
				action: "start_tourcms_booking_engine",
				option_names: option_names,
				option_quantities: option_quantities,
				no_children: no_children, 
				no_adults: no_adults,
				tour_date: $('#sb-tour-activity-date-field').val(),
				tour_id: $("input[name=tour_id]").val(),
				full_refund: $('#full-refund-input').is(":checked") ? true : false,
				promo_code: $('#promo-code-input').val()
				
			},
			function(data){
				document.body.style.cursor='default';
				//console.log(data);
				window.location = data;
			}
		);
	});

});

		


function updateTotals() {

	child_rate = parseFloat($('input[name="child_rate"]').val());
	adult_rate = parseFloat($('input[name="adult_rate"]').val());
	no_adults = parseInt($('input[name="no_adults"]').val());
	no_children = parseInt($('input[name="no_children"]').val());
	total_price = 0;
	
	var adults_total;
	var children_total;
	
	var totalsString = '<div id="tourcms-totals"><ul class="sb-booking-ul">';
	
	if (no_adults > 0) {
		totalsString += sprintf('<li class="sb-booking-li">%d adult%s at $%1.2f = %1.2f</li>',
			no_adults,
			(no_adults > 1) ? "s" : "",
			adult_rate,
			adults_total = adult_rate * no_adults
		);
	
		total_price += adults_total;
	}
	
	if (no_children > 0) {
		totalsString += sprintf('<li class="sb-booking-li">%d child%s at $%1.2f = %1.2f</li>',
			no_children,
			(no_children > 1) ? "ren" : "",
			child_rate,
			children_total = child_rate * no_children
		);
		
		total_price += children_total;
	} 
	
	var total_guests = no_adults + no_children;
	
	for (i = 0; i <= $('#options-total').val(); i++) {
		var o_quantity = parseInt( $('#option-quantity-field-' + i).val() );
		$('#option-quantity-field-' + i).attr('max', total_guests);
		$('#option-quantity-field-' + i).attr('min', 0);

		if (o_quantity > 0) {
			var o_name = $('#option-name-field-' + i).val()
			var o_rate = parseFloat( $('#option-rate-field-' + i).val() );
			var options_total = 0;
			
			totalsString += sprintf('<li class="sb-booking-li">%d %s at $%1.2f = %1.2f</li>',
				o_quantity,
				o_name,
				o_rate,
				options_total = o_quantity * o_rate
			);
			
			total_price += options_total;
		}
	}
	
	//fuel_surcharge = intval($add_ons["fuel_surcharge"]["price"]) * $total_customers;
	//totalsString = sprintf('<li class="booking-li">Fuel Surcharge at $%1.2f </li></ul>', $fuel_surcharge);
	//$total += $fuel_surcharge;
	
	totalsString += sprintf('</ul><p id="sb-total-price">Total price is: $%1.2f</p></div>', total_price);
	return totalsString;
}