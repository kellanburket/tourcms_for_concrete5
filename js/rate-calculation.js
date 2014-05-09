var rates_data = new Array(new Array());

var total_guests = 0;
var total_price = 0;
var tour_id;
var totals_string;
var total_savings;
var savings_string;

var promo_value;
var promo_type;
var promo_savings = 0;

var url;
var PLATFORM = 'Concrete5';

//declare rate object
var tour_rates = new Array();
function Rate(kind, rate) {
	this.kind = kind;
	this.rate = rate;
	this.number = 0;
	this.total = 0;
}


$(document).ready(function() {
	tour_id = $("input[name=tour_id]").val();

	if (PLATFORM == 'Concrete5') {
		url = $('input[name="tourcms_toolbox"]').val();	
		
	} else if (PLATFORM == 'Wordpress') {
		url = cya.ajaxurl;
	}
	
	
	//console.log(url);
	$.post(url, {tour_id: tour_id, action: 'fetch_rates_data'}, function(data){
		var rates = data.split('&');
	
		for (var i = 0; i < rates.length; i++) {
			var rates_data = rates[i].split('=');
			var kind = rates_data[0].split('_');			
			rates_data[0] = kind[0];
			
			tour_rates[i] = new Rate(rates_data[0], rates_data[1]);			
			
			$('.sb-confirm-field').change(function(event) {
				changeHandler(event);
			});
		}
		console.log(tour_rates);

	});	
	

	$('.sb-confirm-field').focus( function (e) {
	  	$(this).on('mousewheel.disableScroll', function (e) {
			e.preventDefault()
	  });
	});
	
	$('.sb-confirm-field').blur( function (e) {
	  	$(this).off('mousewheel.disableScroll');
	});

	$('#promo-code-input').change( function(event) {
		event.preventDefault();
		event.stopPropagation();
		$.post(url, {action: 'check_promo_code', promo_code: $(this).val()}, function(data) {
			//console.log(data);
			promo_value = data.value;
			promo_type = data.value_type;
			//console.log(promo_value);
			//console.log(promo_type);
			$('#tourcms-totals').replaceWith(totals_string);
			$('#sb-tour-savings-box').show();
			$('#sb-tour-you-saved-text').replaceWith(updateSavings());		
		}, "json");	
	});
	
	$('.sb-confirm-field').click(function(event){
		event.preventDefault();
		event.stopPropagation();

		if( tour_rates ) {
			$('#sb-tour-you-saved-text').replaceWith(updateSavings());
		}		

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
			url, 
			{
				action: "start_booking_engine",
				option_names: option_names,
				option_quantities: option_quantities,
				rates_data: getRatesString(),
				tour_date: $('#sb-tour-activity-date-field').val(),
				tour_id: $("input[name=tour_id]").val(),
				promo_code: $('#promo-code-input').val(),
				totals_string: totals_string,
				user_id: $("input[name=user_id]").val()
			},
			function(data){
				if (data.success == true && data.debug == false) {
					window.location.replace(data.site_url + '&id=' + data.user_id);
				} else if (data.success == true && data.debug == true) {
					console.log(data);
					alert('Debug Mode');
					window.location.replace(data.site_url + '&id=' + data.user_id);
				} else if (data.success == false) {
					alert(data.error_msg);
				} else {
					console.log(data);
				}
			}, 
			'json'
			).fail(function(data) {
				console.log(data);
				alert('Error!');
				//alert(data.error_msg);
			}).always(function(data) {
				document.body.style.cursor='default';
			});
		});

	function getRatesString() {
		var string = 'rates[';
		for(var i = 0; i < tour_rates.length; i++) {
			string += '[kind[' + tour_rates[i].kind + '], rate[' + tour_rates[i].rate + '], number[' + tour_rates[i].number + ']],'
		}
		string.slice(0, -1) 
		string += ']';
		return string;
	}

	function changeHandler(event) {
		event.preventDefault();
		event.stopPropagation();
		
		if( $('#sb-tour-activity-date-field') )
			$('#sb-submit').prop('disabled', false);
				
		if( tour_rates ) {
			$('#sb-tour-you-saved-text').replaceWith(updateSavings());
		}
	}
});

function getPlural(name) {
	//TODO get this right; 
	//for all words that end in two of the same consanant add es;
	//for words that end in vowel + y, only add s
	if (name == "child")
		return "children";
	else if(name.substr(name.length -1) == 'y' )
		return name.splice(0, -1) + "ies";
	else if(name.substr(name.length -1) == 'h' )
		return name + "es";
	else
		return name + "s";
}

function updateSavings() {

	for (var i = 0; i < tour_rates.length; i++) {
		var num = parseInt($('input[name="no_'+ tour_rates[i].kind +'"]').val());
		if (num) {
			tour_rates[i].number = num;	
			total_guests += tour_rates[i].number;
			//console.log(tour_rates[i].number);			
		}
	}

	totals_string = updateTotals();
	$('#tourcms-totals').replaceWith(totals_string);
		
	if (promo_savings) {
		total_savings += promo_savings;
		$('#sb-tour-savings-box').show();
		return sprintf('<p id="sb-tour-you-saved-text">You Saved $%1.2f on Your Booking!</p>', total_savings);
	}
}

function updateTotals() {
	
	total_price = 0;
	var adults_total = 0;
	var children_total = 0;
	
	var totalsString = '<div id="tourcms-totals"><ul class="sb-booking-ul">';



	for(var i = 0; i < tour_rates.length; i++) {
		if (parseInt(tour_rates[i].number) > 0) {
			console.log(tour_rates[i].kind);			
			console.log(parseFloat(tour_rates[i].rate));
			console.log(parseFloat(tour_rates[i].number));
			tour_rates[i].total = parseFloat(tour_rates[i].rate) * parseFloat(tour_rates[i].number)

			console.log(tour_rates[i].total);

			
			totalsString += sprintf('<li class="sb-booking-li">%d %s at $%1.2f = $%1.2f</li>',
				parseInt(tour_rates[i].number),
				(tour_rates[i].number > 1) ? getPlural(tour_rates[i].kind) : tour_rates[i].kind,
				parseFloat(tour_rates[i].rate),
				tour_rates[i].total
			);
		
			total_price += tour_rates[i].total;
		}
	}
	
	for (i = 0; i <= $('#options-total').val(); i++) {
		var o_quantity = parseInt( $('#option-quantity-field-' + i).val() );
		$('#option-quantity-field-' + i).attr('max', total_guests);
		$('#option-quantity-field-' + i).attr('min', 0);

		if (o_quantity > 0) {
			var o_name = $('#option-name-field-' + i).val()
			var o_rate = parseFloat( $('#option-rate-field-' + i).val() );
			var options_total = 0;
			
			totalsString += sprintf('<li class="sb-booking-li">%d %s at $%1.2f = $%1.2f</li>',
				o_quantity,
				o_name,
				o_rate,
				options_total = o_quantity * o_rate
			);
			
			total_price += options_total;
		}
	}
	
	if (promo_type && promo_value && promo_type == "PERCENT") {
		var old_total = total_price;
		total_price *= ((100 - parseInt(promo_value)) / 100);
		promo_savings = old_total - total_price;
	} else if(promo_type && promo_value) {
		var old_total = total_price;
		total_price -= parseFloat(promo_value);	
		promo_savings = old_total - total_price;
	}
	
	totalsString += sprintf('</ul><p id="sb-total-price">Subtotal: $%1.2f</p></div>', total_price);
	return totalsString;
}