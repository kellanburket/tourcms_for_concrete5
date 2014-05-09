
var base_url =  "//" + location.host + "/wp-content/plugins/choose-your-adventure/";

var modal = (function(){
	var method = {},
		$overlay,
		$modal,
		$content,
		$close,
		$loader;
		
	$overlay = $('<div id="overlay"></div>');
	$modal = $('<div id="modal"></div>');
	$loader= $('');
	//$loader = $('<img src="' + base_url  + 'img/ajax-loader.gif" id="ajax-loader-gif">');
	$content = $('<div id="modal-content"></div>');
	$close = $('<a id="modal-close" href="#">close</a>');
	
	
	$modal.hide();
	$overlay.hide();
	$loader.hide();
	$modal.append($content, $close);

	$(document).ready(function(){
		$('body').append($overlay, $modal);
		$modal.append($loader);
	});
	
	method.center = function () {
		var top, left;
		
		top = Math.max($(window).height() - $modal.outerHeight(), 0) / 2;
		
		$modal.css({
			top: top + $(window).scrollTop(),
			left: 0,
			right: 0,
			position: "absolute",
			width: "auto",
			height: "auto"
		});	
		
	};
	
	method.open = function (settings) {
		$content.empty().append(settings.content);
		$loader.show();
		
		$modal.css({
			width: settings.width || 'auto', 
			height: settings.height || 'auto'
		})
	
		method.center();
	
		$(window).bind('resize.modal', method.center);
	
		$modal.show();
		$overlay.show();
	};
	
	method.spinnerHide = function () {
		$('#ajax-loader-gif').hide();
	};
	
	method.close = function () {
		$modal.hide();
		$overlay.hide();
		$content.empty();
		$(window).unbind('resize.modal');
	};	
	
	return method;
}());
	
$(document).ready(function() {

	$("#datepicker-select").val("1");

	var secure_base_url = "https://" + location.host + "/wp-content/plugins/choose-your-adventure/";
	
	$('#calendar-button').click(function(event){
		event.preventDefault();
		if ($('#pop-up-calendar').is(":visible")) {
			//do nothing
		}
		else {		
			document.body.style.cursor='wait';
			$.post(base_url + "calendar.php", {
				tour_id: $('#datepicker-select').val()
			})
			.done(function(calendar_data){
				document.body.style.cursor='default';				
				$.getScript(base_url + "js/calendar.js");		
				$('#pop-up-calendar').show();
				$('#datepicker-table').replaceWith(calendar_data);
			});
		}
		event.preventDefault();
	});
	
	$('#datepicker-select').change(function(event){
		$("select option:selected").each(function() {
			var this_tour = $(this).text();
			matchDate(this_tour);
			$('#the_tour').val(this_tour);
		});
	});
		
	$('#datepicker').submit(function(event) {
		event.preventDefault();
		
		if ($('#activity-date-field').val().length > 0 
			&& ( $('#no-adults-input').val().length > 0 
				|| $('#no-children-input').val().length > 0 
			)
		) {
			modal.open({content: ''});
			$('#pop-up-calendar').hide();
			$.get(
				cya.ajaxurl, 
				{
					action: "adventure_ajax_submit",
					bookingNonce: cya.bookingNonce,
					tour_id: $('#datepicker-select').val(),
					activity_date: $("#activity-date-field").val(),
					no_adults: $('#no-adults-input').val(),
					no_children: $('#no-children-input').val(),
					promo_code: $('#promo-code-input').val(),
					full_refund: $('#full-refund-input').val(),
					tour_name: $('#tour_name').val()
				},
				function(data){
					$.getScript(base_url + "js/confirm-booking.js");		
					modal.open({content: data});
					$('#modal')
						.on("click", '#modal-content', function(event) {
							event.stopPropagation();
						})
						.on("click", function() {
							modal.close();
						});
					$('#overlay').click( function() {
						modal.close();
					});
				}
			);
		}
		event.preventDefault();
	});

});

function matchDate(this_tour) {
	
	var date = new Date();
	var day = parseInt(date.getDate());
	var month = parseInt(date.getMonth()) + 1;
	var year = parseInt(date.getFullYear());	
	
	if (this_tour.match(/Valentine/gi)) {
		if(month > 2 || (month == 2  && day > 14)) {
			$("#activity-date-field").val("2" + "/" + "14" + "/"  + (year + 1) ); 
		}
		else {
			$("#activity-date-field").val("2" + "/" + "14" + "/"  + year ); 
		}
	}
	if (this_tour.match(/New Year/gi)) {
		$("#activity-date-field").val("12" + "/" + "31" + "/"  + year ); 
	}
	if (this_tour.match(/Halloween/gi)) {
		if(month > 10) {
			$("#activity-date-field").val("10" + "/" + "31" + "/"  + (year + 1) ); 
		}
		else {
			$("#activity-date-field").val("10" + "/" + "31" + "/"  + year ); 
		}
	}
	if (this_tour.match(/of July/gi)) {
		if(month > 7 || (month == 7  && day > 4)) {
			$("#activity-date-field").val("7" + "/" + "4" + "/"  + (year + 1) ); 
		}
		else {
			$("#activity-date-field").val("7" + "/" + "4" + "/"  + year ); 
		}
	}
}


function matchUrl(url){
/*	
	var values = $.map($('#datepicker-select option'), function(option) {
    	
		if (url.match())
		
		return option.text;
	});
	
	if (url.match(/valentine/gi)) {
		$('#datepicker-select').val('');
	}
	
*/	
}