var referer;
$(document).ready(function() {

	var other_customer_firstnames = '';
	var other_customer_surnames = '';
	
	referer = $('#referring_url').val()

	$('#checkout-continue').click(function(event){
		var $form = $('#authorize-payment-form'), form_url = $form.attr( 'action' ), form_data = $form.serialize();
		event.preventDefault();
		event.stopPropagation();
		var total_customers = parseInt($('#total-customers').val());

		for (i = 1; i <= total_customers; i++) {
			console.log(i);
			other_customer_firstnames += $('#firstname-' + i).val();
			other_customer_surnames += $('#surname-' + i).val();
			if (i < total_customers) {
				other_customer_firstnames += '&';
				other_customer_surnames += '&';
			}
		}
		
		document.body.style.cursor='wait';

		$.post(
			cya.ajaxurl, 
			{
				action: "authorize_tourcms",
				title: $('#title').val(),
				firstname: $('#firstname').val(),
				surname: $('#surname').val(),
				email: $('#email').val(),
				tel_home: $('#tel_home').val(),
				tel_mobile: $('#tel_mobile').val(),
				firstnames: other_customer_firstnames,
				surnames: other_customer_surnames,
				address: $('#address').val(),
				city: $('#city').val(),
				county: $('#county').val(),
				postcode: $('#postcode').val(),
				country: $('#country').val(),
				cc_number: $('#cc_number').val(),
				cc_month: $('#cc_month').val(),
				cc_year: $('#cc_year').val(),
				user_id: $('#user_id').val(),
				referring_url: $('#referring_url').val(),
				nonce: $('#_wpnonce').val()
			},
			function(data){
				console.log(data);
				if (data.success == true && data.debug == false) {
					
					//console.log("form url: " + form_url);
					//console.log("form data: " + form_data);
					//alert('user id ' + data.user_id);

					$form.submit();
					/*
					$.getJSON(
						form_url, 
						form_data,
						function(response) {
							if (response.success == true) {
								console.log(response);
							}
						}
					);
					*/
											
				} else if (data.success == true && data.debug == true) {
					console.log('Debug Mode');
					console.log(data);
					//alert('booking id 1' + data.booking_id1);
					//alert('booking id 2' + data.booking_id2);
					//alert('user id ' + data.user_id);
					$form.submit();
				} else if (data.success == false && data.error_type == "tourcms_error") {
					alert('Your Session has Expired');
					
					if (referer != null) {
						var random = parseInt(Math.random() * 10000000);
						window.location.replace(referer + '?nocache=' + random);
					}	
									
					if (data.debug == true) {
						console.log(data);
					}
				} else {
					console.log(data);
					alert('booking id 1: ' + data.booking_id1);
					alert('booking id 2: ' + data.booking_id2);
					alert('user id: ' + data.user_id);
					$form.submit();
				}
			}, 
			'json'
			).fail(function(data) {
				console.log("there was an error: " + data);
				//alert(data.error_msg);
			}).always(function(data) {
				document.body.style.cursor='default';
				other_customer_firstnames = '';
				other_customer_surnames = '';
		});
	});
});