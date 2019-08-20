"use strict";

(function ($) {

	var acf_orderType = ".acf-field-5d25148656536";
	var acf_salesRep = ".acf-field-5d25156b56537";
	var acf_affiliate = ".acf-field-5d251671a38b3";
	var acf_ticket = ".acf-field-5d4a0a0c75c12";

	jQuery(document).ready(function ($) {

		if ($("body").hasClass("single-product")) {
			// ADDS SPAN TO ADD TO CART BUTTONS TO REMOVE THE SKEW CSS
			$(".single_add_to_cart_button").wrapInner("<span></span>");
		}

		if ($("body").hasClass("term-bats")) {
			// Replaces Prod Cat with TRUE / 2020
			$(".uabb-woo-product-category").text(function () {
				return $(this).text().replace("Bats", "TRUE / 2020");
			});
		}

		if ($("body").hasClass("single-tribe_events")) {
			// ADDS SPAN TO ADD TO CART BUTTONS TO REMOVE THE SKEW CSS
			$(".tribe-button").wrapInner("<span></span>").parent().addClass("cta-btn solid text-center");
		}

		// AFFILIATE JOIN FORM
		if ($("body").hasClass("page-id-671")) {
			$("#affwp-user-login").parent().prepend("<p class='helper'>Please choose a recognizable user name for you or your organization. Do not include any special characters. This CAN NOT be changed later. </p>");
			$("#affwp-register-form legend").after("<p class='helper'>The TRUE Affiliate program is invite only! To apply, you'll need a referral code that is sent to your email. Please enter that below. </p>");
		}
		if ($("body").hasClass("page-template-page-oliver-pos-php")) {

			// URL Params for initial data
			var urlParams = new URLSearchParams(decodeURIComponent(window.location.search));

			var oliverEmail = urlParams.get("userEmail");
			var oliverTotal = urlParams.get("total");
			console.log("EMAIL FROM PARAMS")
			console.log(oliverEmail)

			console.log("Total FROM PARAMS")
			console.log(oliverTotal)

			// ACF OLIVER
			var trueTag = Cookies.getJSON('truecustomtags');

			if (trueTag == undefined) {
				initCookies();
			} else {
				if (trueTag.ordertype) {
					hideOrderType(trueTag.ordertype);
				}

				if (trueTag.salesRep.length > 0) {
					hideSalesRep(trueTag.salesRep);
				}

				if (trueTag.affiliate.length > 0) {
					hideAffiliate(trueTag.affiliate);
				}
			}

			console.log("INITIAL COOKIE")
			console.log(trueTag)

			$('#acf-form').contents().unwrap();
			$('.acf-field-select, .acf-field-user').wrap("<div class='col-4'></div>");
			$('.acf-field-post-object').wrap("<div class='col-6'></div>");

			$('.acf-fields').addClass("row")
			$('.acf-button, .ticket-data').hide();


			// Get Order Info
			var $eventSelect = $(acf_ticket + " select");

			$eventSelect.select2();
			$eventSelect.on("select2:select", function (e) {
				var thisTicket = $(acf_ticket + " select").select2('data');
				if (thisTicket[0].id) {
					var r = /([0-9]+) .*? /;
					var ticketOrderID = thisTicket[0].text.match(r)[1];
				}
				var data = {
					action: 'get_ticket_info',
					ticketOrderID: ticketOrderID,
					ticketID: thisTicket[0].id
				}
				console.log(data)
				$.ajax({
					url: truefunction.ajax_url,
					type: 'get',
					data: data,
					contentType: "application/json; charset=utf-8",
					dataType: "json",
					beforeSend: function () {
						// Show image container
						$("#loader").show();
						$(".ticket-data").hide();
						$("#customtags_button").addClass('disabled');
					},
					success: function (response) {
						setTicketUI(response)
					},
					error: (error) => {
						console.log(JSON.stringify(error));
					},
					complete: function (data) {
						// Hide image container
						$("#loader").hide();
					}
				});

				return false;
			});
			
			$eventSelect.on("select2:unselect", function (e) {
				$(".ticket-data").hide();
				$(".merge").empty();
				$("#customtags_button").removeClass('disabled');
			});
		}

		$("#clearAllTags").on("click", clearAll);

		$("#refreshPage").on("click", refreshPage);

		



	});

    function setTicketUI (response) {
        console.log(response)
        $(".ticket-data").show();
        $("#event-title").text(response.event_meta.post_title);
        $("#event-date").text(response.event_date);

        $("#ticket-orderid").text(response.ticketOrderID);
        $("#ticket-id").text(response.attendee_info[0].attendee_id);

        $("#ticket-num").text(response.attendee_info[0].ticket_id);
        $("#ticket-type").text(response.attendee_info[0].ticket_name);
        $("#ticket-purchaser").text(response.attendee_info[0].holder_name);
        $("#ticket-cost").text(response.attendee_metadata._paid_price[0]);
        $("#ticket-security").text(response.attendee_info[0].security_code);
		
		// IF GOOD ORDER STATUS
		if ( response.ticketOrderStatus === 'completed' || response.ticketOrderStatus === 'processing') {

			if (response.attendee_info[0].check_in === ''){
				$("#ticket-checkin").text('Unused. You can apply this ticket to this order.').parent().addClass("alert-success").removeClass("alert-danger");
				$("#customtags_button").removeClass('disabled');

			} else if ( response.attendee_info[0].check_in === '1' ) {
				$("#ticket-checkin").text('USED. DO NOT APPLY to this order.').parent().addClass("alert-danger").removeClass("alert-success");
			} else {
				$("#ticket-checkin").text(response.attendee_info[0].check_in); // FALLBACK
			}

		} else {
			// ELSE IF BAD ORDER STATUS
			$("#ticket-checkin").text('ORDER ' + response.ticketOrderStatus + '. DO NOT APPLY to this order.').parent().addClass("alert-danger").removeClass("alert-success");

		}
        
        
        $("#player-name").text(response.attendee_info[0].attendee_meta['players-name'].value);
	}

	function clearAll() {
		initCookies();
		$(".savedTag").remove();
		$(".acf-input").show();
		$(acf_orderType + " select, " + acf_salesRep + " select, " + acf_affiliate + " select, " + acf_ticket + " select").show().val("").trigger('change');

		returnCurrentCookie();
	}

	function clearAllTags() {
		initCookies();
		$(".savedTag").remove();
		$(".acf-input").show();
		$(acf_orderType + " select, " + acf_salesRep + " select, " + acf_affiliate + " select").show().val("").trigger('change');

		returnCurrentCookie();
	}

	function refreshPage() {
		location.reload();
	}

	function hideOrderType(data) {
		var tag = $(' .tag-ordertype');

		$(acf_orderType + ' select').hide();
		if (tag) {
			tag.remove();
		}
		$(acf_orderType).append("<p class='savedTag tag-ordertype'>" + data + "</p>");
	}

	function hideSalesRep(data) {
		var tag = $(' .tag-sales');

		$(acf_salesRep + ' .acf-input').hide();
		if (tag) {
			tag.remove();
		}
		$(acf_salesRep).append("<p class='savedTag tag-sales'>" + data[0].text + "</p>");

	}

	function hideAffiliate(data) {
		var tag = $(' .tag-affiliate');

		$(acf_affiliate + ' .acf-input').hide();
		if (tag) {
			tag.remove();
		}

		if (data.length > 0) {
			$(acf_affiliate).append("<p class='savedTag tag-affiliate'>" + data[0].text + "</p>");
		} else {
			$(acf_affiliate).append("<p class='savedTag tag-affiliate'>N/A</p>");

		}

	}

	function initCookies() {
		var trueTag = {
			ordertypeVal: '',
			ordertype: '',
			salesRep: {},
			affiliate: {}
		}
		Cookies.set('truecustomtags', {
			ordertypeVal: trueTag.ordertypeVal,
			ordertype: trueTag.ordertype,
			salesRep: trueTag.salesRep,
			affiliate: trueTag.affiliate
		});
	}

	function returnCurrentCookie() {
		var cookietest = Cookies.getJSON('truecustomtags');
		console.log(cookietest)
	}

	$(function () {
		var options = {
			byRow: true,
			property: 'max-height',
			target: $('.matchThis'),
			remove: false
		}

		$('.matchHeight').matchHeight(options);
    });
    
	// OLIVER POC
	window.addEventListener('message', function(e) {
		if ($("body").hasClass("page-template-page-oliver-pos-php")) {
			console.log(e.or)
			if (e.origin !== 'https://true-diamond-science.local') {
				var msgData = JSON.parse(e.data);
				if (msgData.oliverpos.event == "extensionSendCartData") {
					document.getElementById('parentData').innerHTML = msgData.data.oliverCartData;
				}
			}
		
			console.log("frame page", msgData);
		}
	}, false);

	function bindEvent(element, eventName, eventHandler) {
		if ($("body").hasClass("page-template-page-oliver-pos-php")) {
			element.addEventListener(eventName, eventHandler, false);
		}
		
	}

	// Send a message to the parent
	var sendMessage = function (msg) {
		window.parent.postMessage(msg, '*');
	};

	var customtagsButton = document.getElementById('customtags_button');
	bindEvent(customtagsButton, 'click', function (e) {
        console.log("POC")
        if ( $(this).hasClass("disabled") ) {
            //bail since something is missing or wrong
            return
        }
		var ticketID = $( "#ticket-id" ).text();
		var ticketOrderID = $( "#ticket-orderid" ).text();
		var ticketCost = $( "#ticket-cost" ).text();
		var trueTag = Cookies.getJSON('truecustomtags');
        
		// console.log(thisTicket[0].id)
		// if (thisTicket[0].id) {
        //     var ticketID = thisTicket[0].id;

            // REGEX to get Woo Order ID
			// var r = /([0-9]+) .*? /;
			// var ticketOrderID = thisTicket[0].text.match(r)[1];
		// }

		if (trueTag.ordertypeVal == '') {
			if ($(acf_orderType + ' select').val()) {
				var ordertype = $(acf_orderType + " option:selected").text();
			} else {
				var ordertype = '';
			}
			var trueTag = {
				ordertypeVal: $(acf_orderType + ' select').val(),
				ordertype: ordertype,
				salesRep: $(acf_salesRep + " select").select2('data'),
				affiliate: $(acf_affiliate + " select").select2('data')
			}
		}
		if (trueTag.ordertypeVal == '' || trueTag.salesRep.length == 0) {
			// RESET EVENT TYPE SINCE THATS THE CHECK FOR ALOT
			clearAllTags();

			alert("Please Enter an Event Type and Sales Rep")
			return
		}

		if (trueTag.ordertypeVal === 'league' || trueTag.ordertypeVal === 'facility_event') {
			if (trueTag.affiliate.length == 0) {
				alert("This order type requires an affiliate")
				return
			}
		}

		if (trueTag.ordertypeVal.length > 0) { // if data exist
			hideOrderType(trueTag.ordertype);
		} else {
			// var ordertypeVal = $( acf_orderType + " option:selected").val();
			// var ordertype = $( acf_orderType + " option:selected").text();
		}

		if (trueTag.salesRep.length > 0) {
			hideSalesRep(trueTag.salesRep);
		} else {
			// var salesRep = $( acf_salesRep + " select" ).select2('data');
		}

		// HIDE IF LEFT BLANK, when sending
		hideAffiliate(trueTag.affiliate)
		if (trueTag.affiliate.length === 0) {
			var thisAffiliateID = "N/A";
		} else {
			var thisAffiliateID = trueTag.affiliate[0].id
		}

		Cookies.set('truecustomtags', {
			ordertypeVal: trueTag.ordertypeVal,
			ordertype: trueTag.ordertype,
			salesRep: trueTag.salesRep,
			affiliate: trueTag.affiliate
		});

		returnCurrentCookie();

		// var mySalesPerson = document.getElementById("salesPersonEmail").value;
		// var myaffiliateID = document.getElementById("affiliateID").value;
		// var ticketNumber = document.getElementById("ticketNumber").value;

		var jsonMsg = {
			oliverpos: 
			{
				"event": "addData"
			},
			data: 
			{
				customTags: {
					"affiliateID": thisAffiliateID,
					"salesRep": trueTag.salesRep[0].id,
					"orderType": trueTag.ordertypeVal
				},
				ticket: {
                    "ticketNumber": ticketID,
                    "ticketPrice": ticketCost,
                    "ticketOrderID": ticketOrderID
				}
			}
		}
		console.log("----- DATA TO OLIVER EXTENSION -----")
		console.log(jsonMsg);

		sendMessage(JSON.stringify(jsonMsg));
	});

	var extensionFinishedButton = document.getElementById('extension_finished');
	bindEvent(extensionFinishedButton, 'click', function (e) {
		var jsonMsg = {
			oliverpos: {
				event: "extensionFinished",
				wordpressAction: "tds_neworder"
			}
		}
		console.log("----- FINISH DATA TO OLIVER EXTENSION -----")
		console.log(jsonMsg);

		sendMessage(JSON.stringify(jsonMsg));
	});

})(jQuery);
