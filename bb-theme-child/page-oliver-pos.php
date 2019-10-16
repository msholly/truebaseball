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
                    <div class="col-9">
                        <div id="oliver-msg" class="alert alert-dismissible fade show nomargin" role="alert">
                            <strong class="status">Holy guacamole!</strong>
                            <span class="msg">You should check in on some of those fields below.</span>
                            <button type="button" class="close" data-hide="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-3">
                        <!-- <button id="refreshPage" class="btn btn-dark btn-block btn-lg noradius">Ready</button> -->
                        <button id="custom_tax_add_button" class="btn btn-success btn-block btn-lg noradius button-secondary">Recalc Tax</button>
                        <!-- <button id="custom_fee_remove_button" class="btn btn-danger btn-block btn-lg noradius">Delete Tax</button> -->
                        <!-- <button id="clearAllTags" class="btn btn-block btn-lg noradius color--primary-bg color--white">Clear Tags</button> -->
                    </div>
                </div>

                <?php acf_form(); ?>

                <div class="row">
                    <div class="col-12">
                        <?php $true_pos_nonce = wp_create_nonce('true_pos_form_nonce'); ?>

                        <input type="hidden" id="true_pos_nonce" name="true_pos_nonce" value="<?php echo $true_pos_nonce ?>" />

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
                        <h3>Attendee ID</h3>
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

                        $("#extension_finished").addClass("disabled");
                        $("#oliver-msg").hide();

                        // URL Params for initial data
                        var urlParams = new URLSearchParams(decodeURIComponent(window.location.search));

                        var oliverEmail = urlParams.get("userEmail");
                        var oliverLocation = urlParams.get("location");
                        var oliverRegister = urlParams.get("register");
                        console.log("EMAIL FROM PARAMS")
                        console.log(oliverEmail)

                        console.log("Location FROM PARAMS")
                        console.log(oliverLocation)

                        console.log("Register FROM PARAMS")
                        console.log(oliverRegister)

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

                        var taxUImarkup =
                            '<div class="col-6 current-taxes">' +
                            '<h3>Calculated Taxes</h3>' +
                            '<p>' +
                            '<span id="customTaxKey"></span>: $<span id="customTaxAmount"><span>' +
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
                                    "event": "shareCheckoutData"
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
                            calculateOliverTaxes();
                        }

                        // Get Order Info
                        var $eventSelect = $(acf_ticket + " select");

                        $eventSelect.select2();
                        $eventSelect.on("select2:select", function(e) {
                            var thisTicket = $(acf_ticket + " select").select2('data');
                            let true_pos_nonce = document.getElementById("true_pos_nonce").value;

                            if (thisTicket[0].id) {
                                var r = /([0-9]+) .*? /;
                                var ticketOrderID = thisTicket[0].text.match(r)[1];
                            }
                            var data = {
                                action: 'get_ticket_info',
                                true_pos_nonce: true_pos_nonce,
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
                                    showAlert(error.responseText, "danger", error.statusText)
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
                    $("#refreshPage").on("click", refreshPage);

                    $("#custom_tax_add_button").on("click", resetOliverTaxes);

                });

                window.addEventListener('load', (event) => {
                    console.log("LOAD EVENT LISTENER")
                    postExtensionReady();
                    // invoke the payment toggle function
                    postTogglePaymentButton();
                });

                function setTicketUI(response) {
                    console.log(response)
                    if (response.event_meta === null) {
                        // EVENT WAS DELETED
                        showAlert("Error getting event information.", "danger", "Error")
                        return
                    }
                    $(".ticket-data").show();
                    $("#event-title").text(response.event_meta.post_title);
                    $("#event-date").text(response.event_date);

                    $("#ticket-orderid").text(response.ticketOrderID);
                    $("#ticket-id").text(response.attendee_info[0].attendee_id);

                    $("#ticket-num").text(response.attendee_info[0].attendee_id);
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

                $('body').on('click', '.clearAllTags', function() {
                    clearAllTags();
                });

                function clearAllTags() {
                    initCookies();
                    $(".savedTag").remove();
                    $(".acf-input").show();
                    $(acf_orderType + " select, " + acf_salesRep + " select, " + acf_affiliate + " select, " + acf_ticket + " select").show().val("").trigger('change');

                    returnCurrentCookie();
                }

                function refreshPage() {
                    // location.reload();
                    postExtensionReady();
                    postTogglePaymentButton();
                }

                function hideOrderType(data) {
                    var tag = $(' .tag-ordertype');

                    $(acf_orderType + ' select').hide();
                    if (tag) {
                        tag.remove();
                    }
                    $(acf_orderType).append("<p class='savedTag tag-ordertype'>" + data + " <span class='clearAllTags'>Clear</span></p>");
                }

                function hideSalesRep(data) {
                    var tag = $(' .tag-sales');

                    $(acf_salesRep + ' .acf-input').hide();
                    if (tag) {
                        tag.remove();
                    }
                    $(acf_salesRep).append("<p class='savedTag tag-sales'>" + data[0].text + " <span class='clearAllTags'>Clear</span></p>");

                }

                function hideAffiliate(data) {
                    var tag = $(' .tag-affiliate');

                    $(acf_affiliate + ' .acf-input').hide();
                    if (tag) {
                        tag.remove();
                    }

                    if (data.length > 0) {
                        $(acf_affiliate).append("<p class='savedTag tag-affiliate'>" + data[0].text + " <span class='clearAllTags'>Clear</span></p>");
                    } else {
                        $(acf_affiliate).append("<p class='savedTag tag-affiliate'>N/A <span class='clearAllTags'>Clear</span></p>");

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

                function resetOliverTaxes() {
                    if ($("#custom_tax_add_button").hasClass("disabled")) {
                        return
                    }
                    oliverTaxResponse = null;
                    postExtensionReady();
                    calculateOliverTaxes();
                }

                function calculateOliverTaxes() {

                    var msgData = checkoutData;
                    var checkout = msgData.data.checkoutData;
                    console.log(msgData);

                    checkCheckoutData(checkout);

                    if (oliverTaxResponse) {
                        console.log("Already have Tax Response")
                        console.log(oliverTaxResponse)
                        // bail if we already have taxes, to limit API usage
                        return
                    }
                    if (msgData.oliverpos.event == "shareCheckoutData") {
                        console.log(checkout)
                        let true_pos_nonce = document.getElementById("true_pos_nonce").value;

                        var taxdata = {
                            action: 'get_tax_info',
                            true_pos_nonce: true_pos_nonce,
                            checkoutData: checkout
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
                                $("#customtags_button, #custom_tax_add_button").addClass('disabled');
                            },
                            success: function(response) {
                                console.log(response);
                                oliverTaxResponse = response;
                                setTaxUI(response);
                            },
                            error: (error) => {
                                showAlert(error.responseText, "danger", error.statusText)
                                console.log(JSON.stringify(error));
                            },
                            complete: function(data) {
                                $(".current-taxes #loader").remove()
                            }
                        });

                    }


                }

                function setTaxUI(response) {
                    $("#customtags_button, #custom_tax_add_button").removeClass('disabled');

                    $(".current-taxes p").show();
                    $("#customTaxKey").text(response.jurisdictions.state + " Tax");
                    $("#customTaxAmount").text(response.amount_to_collect);
                    mapOliverTaxes();
                }


                // OLIVER POC
                window.addEventListener('message', function(e) {
                    // console.log(e)
                    if (e.origin === "https://sell.oliverpos.com") {
                        var msgData = JSON.parse(e.data);
                        console.log(msgData)
                        if (msgData.oliverpos.event === 'shareCheckoutData') {
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
                    // window.parent.postMessage(msg, 'https://truediamondscience.com');
                    window.parent.postMessage(msg, '*');
                };

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

                        showAlert("Please Enter an Event Type and Sales Rep", "warning", "Warning")
                        return
                    }

                    if (trueTag.ordertypeVal === 'league' || trueTag.ordertypeVal === 'facility_event') {
                        if (trueTag.affiliate.length == 0) {
                            showAlert("This order type requires an affiliate", "warning", "Warning")
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
                    console.log(jsonMsg);
                    console.log("^^ DATA TO OLIVER EXTENSION ^^")

                    sendMessage(JSON.stringify(jsonMsg));

                    if (ticketCost) {
                        // Custom Discount Add
                        var customDiscountKey = "Attendee ID: #" + $("#ticket-id").text();
                        var customDiscountAmount = $("#ticket-cost").text();

                        var discountjsonMsg = {
                            oliverpos: {
                                event: "saveDiscount"
                            },
                            data: {
                                discount: {
                                    "key": customDiscountKey,
                                    "amount": customDiscountAmount
                                }
                            }
                        }
                        console.log(discountjsonMsg);
                        console.log("^^ DISCOUNT DATA TO OLIVER EXTENSION DISABLED ^^")


                        sendMessage(JSON.stringify(discountjsonMsg));
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
                    console.log(taxjsonMsg);
                    console.log("^^ TAX DATA TO OLIVER EXTENSION ^^")

                    sendMessage(JSON.stringify(taxjsonMsg));

                    // MESSAGES SENT TO OLIVER, ALLOW FINISH EXTENSION BUTTON
                    $(this).text("TAGS SAVED");
                    $("#oliver-msg").hide();
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
                    if (window.location !== window.parent.location) {

                        // The page is in an iFrames 
                        return true
                    } else {

                        // The page is not in an iFrame 
                        return false
                    }
                }

                function showAlert(msg, type, status) {
                    $('#oliver-msg').removeClass("alert-warning","alert-info","alert-danger")
                    $('#oliver-msg .status').text(status + ": ");
                    $('#oliver-msg .msg').text(msg);
                    $('#oliver-msg').addClass("alert-" + type).show();
                }

                function checkCheckoutData(checkout) {

                    var msg, type;
                    switch (true) {
                        case checkout.country === "":
                            msg = "Customer Missing Country.";
                            status = "Error";
                            type = "danger";
                            break;
                        case checkout.addressLine1 === "":
                            msg = "Customer Missing Address Line 1.";
                            status = "Warning";
                            type = "warning";
                            break;
                        case checkout.city === "":
                            msg = "Customer Missing City.";
                            status = "Warning";
                            type = "warning";
                            break;
                        case checkout.state === "":
                            msg = "Customer Missing State.";
                            status = "Error";
                            type = "danger";
                            break;
                        case checkout.zip === "":
                            msg = "Customer Missing Zip Code.";
                            status = "Warning";
                            type = "warning";
                            break;
                        default:
                            msg = "Customer Address is set.";
                            status = "Note";
                            type = "info";
                    }
                    showAlert(msg, type, status)

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

                $("[data-hide]").on("click", function() {
                    $("." + $(this).attr("data-hide")).hide();
                });



            })(jQuery);
        </script>
    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>