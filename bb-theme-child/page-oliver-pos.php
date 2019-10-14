<?php
/* Template Name: POS Template */
?>

<?php acf_form_head(); ?>
<?php get_header(); ?>

<div id="oliver-pos">
    <div id="content" role="main" style="height: 100vh">

        <?php /* The loop */ ?>
        <?php while (have_posts()) : the_post(); ?>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-3">
                        <button id="clearAllTags" class="btn btn-block btn-lg noradius color--primary-bg color--white">Clear Tags</button>
                    </div>
                    <div class="col-3">
                        <!-- <button id="refreshPage" class="btn btn-dark btn-block btn-lg noradius">Reset</button> -->
                    </div>
                    <div class="col-3">
                        <!-- <button id="custom_fee_add_button" class="btn btn-success btn-block btn-lg noradius button-secondary">Recalc Tax</button> -->
                    </div>
                    <div class="col-3">
                        <button id="custom_fee_remove_button" class="btn btn-danger btn-block btn-lg noradius">Delete Tax</button>
                    </div>
                </div>

                <?php acf_form(); ?>

                <div class="row">
                    <div class="col-12">
                        <?php $true_fitting_nonce = wp_create_nonce('true_fitting_form_nonce'); ?>

                        <input type="hidden" id="true_fitting_nonce" name="true_fitting_nonce" value="<?php echo $true_fitting_nonce ?>" />			
                        <input type="hidden" id="customFeeUniqueId" name="customFeeUniqueId" value="extensionCustomFeeId_<?php echo mt_rand(); ?>" class="inp_cont small" />

                        <!-- Image loader -->
                        <div id='loader' style='display: none;'>
                            <img src="<?php bloginfo('stylesheet_directory'); ?>/assets/img/ball-loading.gif" />
                        </div>
                        <!-- Image loader -->
                    </div>
                </div>

                <div class="row ticket-data">
                    <div class="col-6">
                        <h3 id="event-title" class="merge"></h3>
                        <div id="event-date" class="merge"></div>
                    </div>
                    <div class="col-6">
                        <h3>Player Name</h3>
                        <div id="player-name" class="merge"></div>
                    </div>
                </div>

                <div class="row ticket-data">
                    <div class="col-3">
                        <h3>TICKET #</h3>
                        <div id="ticket-num" class="merge"></div>
                    </div>
                    <div class="col-3">
                        <h3>TICKET TYPE</h3>
                        <div id="ticket-type" class="merge"></div>
                    </div>
                    <div class="col-3">
                        <h3>PURCHASER</h3>
                        <span id="ticket-purchaser" class="merge"></span> for $<span id="ticket-cost" class="merge"></span>
                    </div>
                    <div class="col-3">
                        <h3>SECURITY</h3>
                        <div id="ticket-security" class="merge"></div>
                    </div>
                </div>

                <div class="row ticket-data">
                    <div class="col-12">
                        <div class="alert alert-success" role="alert">
                            <h3 class="nomargin">TICKET <strong>#<span id="ticket-id" class="merge"></span></strong> STATUS from ORDER #<span id="ticket-orderid" class="merge"></span></h3>
                            <div id="ticket-checkin" class="merge"></div>
                        </div>
                    </div>
                </div>

                <div class="fixed-bottom">
                    <table class="table">
                        <tr>
                            <td>
                                <h3 class="nomargin text-center">STEP 1</h3>
                                <button id="customtags_button" class="button button-primary button-large" style="display: block;width: 100%;">Save To POS Order</button>
                            </td>
                            <td>
                                <h3 class="nomargin text-center">STEP 2</h3>
                                <button id="extension_finished" class="button button-secondary button-large" style="display: block;width: 100%;">Complete Tags</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php the_content(); ?>

            </div>

        <?php endwhile; ?>

        <script>
            "use strict";

            var checkoutData, oliverTaxResponse, oliverProductTaxes;

            (function($) {

                var acf_orderType = ".acf-field-5d25148656536";
                var acf_salesRep = ".acf-field-5d25156b56537";
                var acf_affiliate = ".acf-field-5d251671a38b3";
                var acf_ticket = ".acf-field-5d4a0a0c75c12";

                jQuery(document).ready(function($) {


                    if ($("body").hasClass("page-template-page-oliver-pos-php")) {

                        console.log(window.location)

                        $("#extension_finished").addClass("disabled");

                        // URL Params for initial data
                        var urlParams = new URLSearchParams(decodeURIComponent(window.location.search));

                        var oliverEmail = urlParams.get("userEmail");
                        var oliverLocation = urlParams.get("location");
                        var oliverRegister = urlParams.get("register");
                        // console.log("EMAIL FROM PARAMS")
                        // console.log(oliverEmail)

                        // console.log("Location FROM PARAMS")
                        // console.log(oliverLocation)

                        // console.log("Register FROM PARAMS")
                        // console.log(oliverRegister)

                        // window.addEventListener('message', function (e) {

                        // 	if (e.origin === "https://sell.oliverpos.com") {
                        // 		let msgData = JSON.parse(e.data);

                        // 		if (msgData.oliverpos.event == "extensionSendCartData") {
                        // 			document.getElementById('parentData').innerHTML = msgData.data.oliverCartData;
                        // 		}

                        // 		console.log("frame page", msgData);

                        // 	}

                        // }, false);

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

                        // console.log("INITIAL COOKIE")
                        // console.log(trueTag)

                        var taxUImarkup =
                            '<div class="col-6 current-taxes">' +
                            '<h3>Calculated Taxes</h3>' +
                            '<p>' +
                            '<span id="customFeeKey"></span>: $<span id="customFeeAmount"><span>' +
                            '</p>' +
                            '</div>';

                        $('#acf-form').contents().unwrap();
                        $('.acf-field-select, .acf-field-user').wrap("<div class='col-4'></div>");
                        $('.acf-field-post-object').wrap("<div class='event-ticket-search col-6'></div>");
                        $(taxUImarkup).insertAfter('.event-ticket-search');

                        $('.acf-fields').addClass("row")
                        $('.acf-button, .ticket-data').hide();

                        // OLIVER TEST SINCE NO MESSAGE FROM PARENT
                        if (window.location.hostname === "true-diamond-science.local") {
                            oliverEmail = "Alfredo.Sanchez@truediamondscience.com";
                            checkoutData = {
                                "oliverpos": {
                                    "event": "registerExtension"
                                },
                                "data": {
                                    "checkoutData": {
                                        "totalTax": "",
                                        "cartProducts": [{
                                                "amount": 80,
                                                "productId": 2046,
                                                "variationId": 0,
                                                "tax": 0,
                                                "discountAmount": 0,
                                                "quantity": 1,
                                                "title": "TRUE Hitting Report"
                                            },
                                            {
                                                "amount": 45,
                                                "productId": 2414,
                                                "variationId": 0,
                                                "tax": 0,
                                                "discountAmount": 0,
                                                "quantity": 1,
                                                "title": "2 Day Shipping"
                                            },
                                            {
                                                "amount": 50,
                                                "productId": 2053,
                                                "variationId": 0,
                                                "tax": 0,
                                                "discountAmount": 0,
                                                "quantity": 1,
                                                "title": "Fitting"
                                            },
                                            {
                                                "amount": 560,
                                                "productId": 1310,
                                                "variationId": 1486,
                                                "tax": 0,
                                                "discountAmount": 0,
                                                "quantity": 2,
                                                "title": "TRUE 2020 T1 USA Youth Bat -10 30.5/20.5 S"
                                            }
                                        ],
                                        "addressLine1": "2780 McDonough St",
                                        "addressLine2": "",
                                        "city": "Joliet",
                                        "zip": "60436",
                                        "country": "US",
                                        "state": "IL"
                                    }
                                }
                            }
                            // TESTER
                            // oliverTaxResponse = {
                            // 	"taxable_amount": 280,
                            // 	"tax_source": "origin",
                            // 	"shipping": 0,
                            // 	"rate": 0.0925,
                            // 	"order_total_amount": 410,
                            // 	"jurisdictions": {
                            // 		"state": "TN",
                            // 		"county": "SHELBY",
                            // 		"country": "US",
                            // 		"city": "MEMPHIS"
                            // 	},
                            // 	"has_nexus": true,
                            // 	"freight_taxable": true,
                            // 	"breakdown": {
                            // 		"taxable_amount": 280,
                            // 		"tax_collectable": 25.9,
                            // 		"state_taxable_amount": 280,
                            // 		"state_tax_rate": 0.07,
                            // 		"state_tax_collectable": 19.6,
                            // 		"special_tax_rate": 0,
                            // 		"special_district_taxable_amount": 0,
                            // 		"special_district_tax_collectable": 0,
                            // 		"shipping": {
                            // 			"taxable_amount": 0,
                            // 			"tax_collectable": 0,
                            // 			"state_taxable_amount": 0,
                            // 			"state_sales_tax_rate": 0.07,
                            // 			"state_amount": 0,
                            // 			"special_taxable_amount": 0,
                            // 			"special_tax_rate": 0,
                            // 			"special_district_amount": 0,
                            // 			"county_taxable_amount": 0,
                            // 			"county_tax_rate": 0.0225,
                            // 			"county_amount": 0,
                            // 			"combined_tax_rate": 0.0925,
                            // 			"city_taxable_amount": 0,
                            // 			"city_tax_rate": 0,
                            // 			"city_amount": 0
                            // 		},
                            // 		"line_items": [{
                            // 				"taxable_amount": 280,
                            // 				"tax_collectable": 25.9,
                            // 				"state_taxable_amount": 280,
                            // 				"state_sales_tax_rate": 0.07,
                            // 				"state_amount": 19.6,
                            // 				"special_tax_rate": 0,
                            // 				"special_district_taxable_amount": 0,
                            // 				"special_district_amount": 0,
                            // 				"id": "1166",
                            // 				"county_taxable_amount": 280,
                            // 				"county_tax_rate": 0.0225,
                            // 				"county_amount": 6.3,
                            // 				"combined_tax_rate": 0.0925,
                            // 				"city_taxable_amount": 0,
                            // 				"city_tax_rate": 0,
                            // 				"city_amount": 0
                            // 			},
                            // 			{
                            // 				"taxable_amount": 0,
                            // 				"tax_collectable": 0,
                            // 				"state_taxable_amount": 0,
                            // 				"state_sales_tax_rate": 0,
                            // 				"state_amount": 0,
                            // 				"special_tax_rate": 0,
                            // 				"special_district_taxable_amount": 0,
                            // 				"special_district_amount": 0,
                            // 				"id": "2046",
                            // 				"county_taxable_amount": 0,
                            // 				"county_tax_rate": 0,
                            // 				"county_amount": 0,
                            // 				"combined_tax_rate": 0,
                            // 				"city_taxable_amount": 0,
                            // 				"city_tax_rate": 0,
                            // 				"city_amount": 0
                            // 			},
                            // 			{
                            // 				"taxable_amount": 0,
                            // 				"tax_collectable": 0,
                            // 				"state_taxable_amount": 0,
                            // 				"state_sales_tax_rate": 0,
                            // 				"state_amount": 0,
                            // 				"special_tax_rate": 0,
                            // 				"special_district_taxable_amount": 0,
                            // 				"special_district_amount": 0,
                            // 				"id": "2053",
                            // 				"county_taxable_amount": 0,
                            // 				"county_tax_rate": 0,
                            // 				"county_amount": 0,
                            // 				"combined_tax_rate": 0,
                            // 				"city_taxable_amount": 0,
                            // 				"city_tax_rate": 0,
                            // 				"city_amount": 0
                            // 			}
                            // 		],
                            // 		"county_taxable_amount": 280,
                            // 		"county_tax_rate": 0.0225,
                            // 		"county_tax_collectable": 6.3,
                            // 		"combined_tax_rate": 0.0925,
                            // 		"city_taxable_amount": 0,
                            // 		"city_tax_rate": 0,
                            // 		"city_tax_collectable": 0
                            // 	},
                            // 	"amount_to_collect": 25.9
                            // }
                            // appendWebRegisterCartData();
                            calculateOliverTaxes();
                        }

                        // Get Order Info
                        var $eventSelect = $(acf_ticket + " select");

                        $eventSelect.select2();
                        $eventSelect.on("select2:select", function(e) {
                            var thisTicket = $(acf_ticket + " select").select2('data');
                            let true_fitting_nonce = document.getElementById("true_fitting_nonce").value;

                            if (thisTicket[0].id) {
                                var r = /([0-9]+) .*? /;
                                var ticketOrderID = thisTicket[0].text.match(r)[1];
                            }
                            var data = {
                                action: 'get_ticket_info',
                                nonce: true_fitting_nonce,
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
                                beforeSend: function() {
                                    // Show image container
                                    $("#loader").show();
                                    $(".ticket-data").hide();
                                    $("#customtags_button").addClass('disabled');
                                },
                                success: function(response) {
                                    setTicketUI(response)
                                },
                                error: (error) => {
                                    console.log(JSON.stringify(error));
                                },
                                complete: function(data) {
                                    // Hide image container
                                    $("#loader").hide();
                                }
                            });

                            return false;
                        });

                        $eventSelect.on("select2:unselect", function(e) {
                            $(".ticket-data").hide();
                            $(".merge").empty();
                            $("#customtags_button").removeClass('disabled');
                        });
                    }

                    // EXTENSION HELPERS 
                    $("#clearAllTags").on("click", clearAll);
                    $("#refreshPage").on("click", refreshPage);

                    $("#custom_fee_add_button").on("click", calculateOliverTaxes);

                });

                window.addEventListener('load', (event) => {
                    console.log("LOAD EVENT LISTENER")
                    postExtensionReady();
                    // invoke the payment toggle function
                    postTogglePaymentButton();
                });

                function setTicketUI(response) {
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
                    if (response.ticketOrderStatus === 'completed' || response.ticketOrderStatus === 'processing') {

                        if (response.attendee_info[0].check_in === '') {
                            $("#ticket-checkin").text('Unused. You can apply this ticket to this order.').parent().addClass("alert-success").removeClass("alert-danger");
                            $("#customtags_button").removeClass('disabled');

                        } else if (response.attendee_info[0].check_in === '1') {
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

                $(function() {
                    var options = {
                        byRow: true,
                        property: 'max-height',
                        target: $('.matchThis'),
                        remove: false
                    }

                    $('.matchHeight').matchHeight(options);
                });



                function mapOliverTaxes() {
                    var taxarr = new Array();
                    var lineItems = oliverTaxResponse.breakdown.line_items;
                    $.each(lineItems, function(i, obj) {
                        var data = {};
                        var origCartData = checkoutData.data.checkoutData.cartProducts;

                        $.each(origCartData, function(i, v) {

                            // IF MATCHING NORMAL LINE ITEMS
                            if (v.productId == obj.id) {
                                data.amount = v.amount,
                                    data.productId = parseInt(obj.id);
                                data.variationId = v.variationId;
                                data.tax = obj.tax_collectable;
                                data.discountAmount = v.discountAmount;
                                data.quantity = v.quantity;
                                data.title = v.title;

                                taxarr.push(data);

                                return false;

                            }

                        });

                    });
                    oliverProductTaxes = taxarr;
                }

                function calculateOliverTaxes() {

                    var msgData = checkoutData;

                    if (oliverTaxResponse) {
                        // bail if we already have taxes, to limit API usage
                        console.log("Already have Tax Response")
                        console.log(oliverTaxResponse)
                        return
                    }
                    if (msgData.oliverpos.event == "registerExtension") {
                        console.log(msgData.data.checkoutData)
                        let true_fitting_nonce = document.getElementById("true_fitting_nonce").value;

                        var taxdata = {
                            action: 'get_tax_info',
                            nonce: true_fitting_nonce,
                            checkoutData: msgData.data.checkoutData
                        }
                        $.ajax({
                            url: truefunction.ajax_url,
                            type: 'get',
                            data: taxdata,
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            beforeSend: function() {
                                console.log("REQUESTING TAX");
                                $(".current-taxes p").hide();
                                $("#loader").clone().appendTo(".current-taxes").show();
                                $("#customtags_button").addClass('disabled');
                            },
                            success: function(response) {
                                console.log(response);
                                oliverTaxResponse = response;
                                setTaxUI(response);
                            },
                            error: (error) => {
                                console.log(JSON.stringify(error));
                            },
                            complete: function(data) {
                                $(".current-taxes #loader").remove()
                            }
                        });

                    }


                }

                function setTaxUI(response) {
                    $("#customtags_button").removeClass('disabled');

                    $(".current-taxes p").show();
                    $("#customFeeKey").text(response.jurisdictions.state + " Tax");
                    $("#customFeeAmount").text(response.amount_to_collect);
                    mapOliverTaxes();
                }


                // OLIVER POC
                window.addEventListener('message', function(e) {
                    console.log(e)
                    if (e.origin === "https://sell.oliverpos.com") {
                        var msgData = JSON.parse(e.data);
                        console.log(msgData)
                        if (msgData.oliverpos.event == "registerExtension") {
                            checkoutData = msgData;
                            // appendWebRegisterCartData();
                            calculateOliverTaxes();
                            // document.getElementById('parentData').innerHTML = msgData.data.oliverCartData;
                        }
                    }

                }, false);


                function bindEvent(element, eventName, eventHandler) {
                    element.addEventListener(eventName, eventHandler, false);
                }

                // Send a message to the parent
                var sendMessage = function(msg) {
                    window.parent.postMessage(msg, 'https://truediamondscience.com');
                };

                var customFeeDeleteButtom = document.getElementById('custom_fee_remove_button');
                bindEvent(customFeeDeleteButtom, 'click', function(e) {
                    console.log("DELETE CUSTOM FEE")
                    let customFeeUniqueId = document.getElementById("customFeeUniqueId").value;

                    var jsonMsg = {
                        oliverpos: {
                            event: "deleteCustomFee"
                        },
                        data: {
                            customFee: {
                                "id": customFeeUniqueId,
                            }
                        }
                    }

                    sendMessage(JSON.stringify(jsonMsg));
                });

                var customtagsButton = document.getElementById('customtags_button');
                bindEvent(customtagsButton, 'click', function(e) {
                    console.log("POC")
                    if ($(this).hasClass("disabled")) {
                        //bail since something is missing or wrong
                        return
                    }
                    var ticketID = $("#ticket-id").text();
                    var ticketOrderID = $("#ticket-orderid").text();
                    var ticketCost = $("#ticket-cost").text();
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
                        oliverpos: {
                            "event": "addData"
                        },
                        data: {
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

                    if (ticketCost) {
                        // Custom Fee Add
                        var customFeeKey = "Attendee ID: #" + $("#ticket-id").text();
                        var customFeeAmount = -parseInt($("#ticket-cost").text());
                        var customFeeUniqueId = document.getElementById("customFeeUniqueId").value;

                        var feejsonMsg = {
                            oliverpos: {
                                event: "saveCustomFee"
                            },
                            data: {
                                customFee: {
                                    "id": customFeeUniqueId,
                                    "key": customFeeKey,
                                    "amount": customFeeAmount
                                }
                            }
                        }
                        console.log("----- FEE DATA TO OLIVER EXTENSION DISABLED-----")
                        console.log(feejsonMsg);


                        sendMessage(JSON.stringify(feejsonMsg));
                    }
                    // end ticket check


                    // Custom Taxes Add
                    var taxjsonMsg = {
                        oliverpos: {
                            event: "updateProductTaxes"
                        },
                        data: {
                            "products": oliverProductTaxes
                        }
                    }
                    console.log("----- TAX DATA TO OLIVER EXTENSION -----")
                    console.log(taxjsonMsg);

                    sendMessage(JSON.stringify(taxjsonMsg));

                    // MESSAGES SENT TO OLIVER, ALLOW FINISH EXTENSION BUTTON
                    $(this).text("TAGS SAVED");
                    $("#extension_finished").removeClass("disabled");
                });

                var extensionFinishedButton = document.getElementById('extension_finished');
                bindEvent(extensionFinishedButton, 'click', function(e) {
                    if ($(this).hasClass("disabled")) {
                        //bail since something is missing or wrong
                        return
                    }
                    var jsonMsg = {
                        oliverpos: {
                            event: "extensionFinished",
                            wordpressAction: "tds_neworder"
                        }
                    }
                    console.log("----- FINISH DATA TO OLIVER EXTENSION -----")
                    console.log(jsonMsg);

                    sendMessage(JSON.stringify(jsonMsg));

                    $(this).text("CHARGE CREDIT CARD NOW");

                    // invoke the payment toggle function
                    postTogglePaymentButton(true);
                });

                var postTogglePaymentButton = function(flag = false) {
                    var jsonMsg = {
                        oliverpos: {
                            "event": "togglePaymentButtons"
                        },
                        data: {
                            togglePayment: {
                                "flag": flag
                            }
                        }
                    }

                    sendMessage(JSON.stringify(jsonMsg));
                }

                var postExtensionReady = function() {
                    console.log("TRUE Extension is Ready")
                    var jsonMsg = {
                        oliverpos: {
                            "event": "extensionReady"
                        }
                    }

                    sendMessage(JSON.stringify(jsonMsg));
                }

                // CHECK IF Extension iFrame
                function iniFrame() { 
                    if ( window.location !== window.parent.location ) { 
                    
                        // The page is in an iFrames 
                        return true
                    }  
                    else { 
                        
                        // The page is not in an iFrame 
                        return false
                    } 
                } 

                // var appendWebRegisterCartData = function() {
                // 	let listItemsData = checkoutData.data.checkoutData.cartProducts;
                // 	if (typeof listItemsData !== "undefiend") {
                // 		document.getElementById("extensionProductList").innerHTML = " ";
                // 		let listItemsDataIndex = 1;
                // 		for (let get_i_data of listItemsData) {
                // 			document.getElementById("extensionProductList").innerHTML += '<div class="true-daimond-flex"> <div class="push-top">' + get_i_data.quantity + '</div> <div class="push-top ellipsis" title="' + get_i_data.title + '">' + get_i_data.title + '</div> <div class="push-top">' + get_i_data.amount + '</div> <div class="add_tax_amt"> Add Tax Amount <div class="add_tax_amount"> <input type="text" id="listItemsDataTaxInput-'+listItemsDataIndex+'" value="' + get_i_data.tax + '"> </div> </div> </div>';

                // 			listItemsDataIndex++;
                // 		}
                // 	} else {
                // 		document.getElementById("extensionProductList").innerHTML = "Data not found!";
                // 	}
                // }



            })(jQuery);
        </script>
    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>