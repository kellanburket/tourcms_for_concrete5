$last = null;

var date = new Date();

var months = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
var month, year, current_month, current_year;
var day = 1;
var today = day;
var tour;
var last_number = 1;
var width, last_width;

var bgColor = '#00529b';
var fColor = '#ffffff';
var selectedColor = '#1D9FE6';

var date_handler = {
	fire: 
	function(event){

		console.log(event.target.textContent);
		
		event.preventDefault();
		event.stopPropagation();
		selected_date = month + "/" + event.target.textContent + "/" + year;
		
		if( $('input[name="no_adult"]').val() > 0 || $('input[name="no_child"]').val() > 0 || $('input[name="no_senior"]').val() > 0 )
			$('#sb-submit').prop('disabled', false);
			
		$('#tourcms-totals').replaceWith(updateTotals());
		
		$('#sb-tour-activity-date-field').val(selected_date);

		if ($last != null) {
			$last.css({
				backgroundColor: bgColor,
				color: fColor			
			});	
		}
		$last = $(event.target);
		
		console.log($last);
		$(event.target).css({
			color: fColor,
			backgroundColor: selectedColor
		});
	}
}
var handleDateClick = $.proxy(date_handler.fire);

var url;
var PLATFORM = 'Concrete5';
	
$(document).ready(function() {

	if (PLATFORM == 'Concrete5') {
		url = $('input[name="get_calendar"]').val();	
	} else if (PLATFORM == 'Wordpress') {
		url = cya.ajaxurl;
	}
	
	month = $('[name=current_month]').val();
	current_month = month;
	year = $('[name=current_year]').val();
	current_year = year;
	tour = $("input[name=tour_id]").val();

	//Load Availabilities Calendar when Page finishes loading
	(function(){	
		$.ajax({
			url: url,
			type: "POST",
			data: {
				"tour_id": tour
			}
		}).done(function(html) {
			$('#tourcms-sidebar-table').empty();
			$('#tourcms-sidebar-table').replaceWith(html);

			//$('#sb-tour-month').text(months[month-1] + " " + year);
			$('.tourcms-sidebar-td').not(".unavailable").click(handleDateClick);
		}).fail(function(data) {
			alert('There was an problem accessing tour availabilites. Please try again in a moment.');
		});
	})();

	//Handler to set whenever we load a new data
	var handleSelectedMonth = function() {
		var selected_date;
		var month; //= <?php echo $selected_month; ?>;
		var year; //= <?php echo $selected_year; ?>;
		$('.tourcms-sidebar-td').click(function(){
			selected_date = month + "/" + $(this).text() + "/" + year;
			$('#activity-date-field').val(selected_date);
			$('#pop-up-calendar').fadeOut();
		});
	}		


	$( window ).resize(function() {
  		width = $( window ).width();
		//console.log(width + " " + last_width);
		if (width >= 753 && last_width < 753) {
			$('.sb-tour-tab-info-wrap').css({display: "none"});
			$('.sb-tour-tab').css({ color: "rgb(119, 119, 119)"});				

			$('#tab-frame-4').css({display: "block"});
			$('#sb-tab-4').css({ color: "#46B3DD"});		
		} else if (width < 753 && last_width >= 753) {
			$('.sb-tour-tab-info-wrap').css({display: "none"});
			$('.sb-tour-tab').css({ color: "rgb(119, 119, 119)"});				

			$('#tab-frame-1').css({display: "block"});
			$('#sb-tab-1').css({ color: "#46B3DD"});	
		}
		last_width = width;
	});
	
	$('.sb-tour-tab').click(function(event) {
		event.preventDefault();
		event.stopPropagation();
		var s = $(this).attr("id").split("-");
		var number = s[2];
		$('.sb-tour-tab-info-wrap').hide();		
		
		$('.sb-tour-tab').css({ color:"#777777"});
		$('#tab-frame-' + number).show();
		$('#sb-tab-' + number).css({ color: "#46B3DD"});
		
		var factor = Math.abs(last_number - number);
		if (number <=3) {
			
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
		}
	});
	
	$('#sb-tour-forward-one').click(function(event) {		
		event.preventDefault();
		event.stopPropagation();
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
			url: url,
			type: "POST",
			data: {
				action: 'update_calendar', 
				selected_month: month,
				selected_year: year,
				selected_day: day,
				tour_id: tour
			}
		}).done(function(html) {
			//console.log(html);
			//console.log($('#tourcms-sidebar-table'));
			
			$('#tourcms-sidebar-table').empty();
			$('#tourcms-sidebar-table').replaceWith(html);

			$('#sb-tour-month').text(months[month-1] + " " + year);
			$('.tourcms-sidebar-td').not(".unavailable").click(handleDateClick);
		}).fail(function(data) {
			alert('There was an problem accessing tour availabilites. Please try again in a moment.');
		}).always(function(data) {
			document.body.style.cursor = 'default';
		});
	});

	$('.tourcms-sidebar-td').not(".unavailable").click(handleDateClick);

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

		document.body.style.cursor = 'wait';
		
		if (month == current_month) {
				$('#sb-tour-back-one').attr("disabled", "disabled");
		}
		
		$.ajax({
			url: url,
			type: "POST",
			data: {
				action: 'update_calendar', 
				selected_month: month,
				selected_year: year,
				selected_day: day,
				tour_id: tour
			}
		}).done(function(html) {
	
			$('#tourcms-sidebar-table').empty();
			$('#tourcms-sidebar-table').replaceWith(html);

			$('#sb-tour-month').text(months[month-1] + " " + year);
			$('.tourcms-sidebar-td').not(".unavailable").click(handleDateClick);
		}).fail(function(data) {
			alert('There was an problem accessing tour availabilites. Please try again in a moment.');
		}).always(function(data) {
			document.body.style.cursor = 'default';
		});
	});
	
});