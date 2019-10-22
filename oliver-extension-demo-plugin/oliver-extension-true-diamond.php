<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Client Extension</title>
<!-- <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet"> -->
    <style type="text/css">
        .gray-background-box {
            background-color: #f5f5f5;
            border-radius: 8px;
            margin-bottom: .5rem!important;
        }
        .gray-background-Inbox {
            min-height: 90px;
            align-items: center;
            position: relative;
            justify-content: center;
            text-align: center;
            display: flex;
            flex-direction: column;
        }
        .gray-background-Inbox p {
            margin: 0px;
            font-size: 16px;
        }
        .gray-background-Inbox_price  {
            margin-top: 5px;
            color: #4b4b4b;
            font-size: 18px;
        }
        .gray-background-Inbox_price input,
        .gray-background-Inbox_price select {
            text-align: center;
            border: 0px;
            background-color: transparent;
            box-shadow: none; 
            font-size: 18px;
            color: #4b4b4b; 
            border: 1px solid rgba(151,151,151,0.5);
            border-radius: 8px;
            height: 40px;
        }

        .gray-background-Inbox-space input,
        .gray-background-Inbox-space select {
            border-color: #f5f5f5;
        }
        .gray-background-Inbox_price input:focus {
            background-color: transparent; 
            outline: none;
        }
        .white-background-box {
            background-color: #ffffff; 
        }
        .gray-background-Inbox-space {
            flex-direction: unset;
            justify-content: space-around;
            min-height: auto;
            padding: 15px 0px;
        }
        .gray-background-Inbox-space p{
            color: #c3c3c3; 
        }
        .ply_with_stripe {
            background: #46A9D4;
            border-radius: 0px 0px 8px 8px;
            height: 70px;
            border: 1px solid #46A9D4;
            color: #ffffff;
            width: 100%;
            font-size: 20px;  
            margin-bottom: 15px;
        }
        .true-diamond-form {
            /*height: 100vh;*/
        }
        .true-diamond-form-scroll {
            height: calc(100vh - 71px);   
            overflow-y: scroll;
        }    
    </style>
</head>
<body style="overflow: hidden;">

    <div class="true-diamond-form">
        <div class="true-diamond-form-scroll overflowscroll">
           <div class="gray-background-box white-background-box">
                <div class="gray-background-Inbox gray-background-Inbox-space">
                    <div>
                        <p>Affiliate</p>
                        <div class="gray-background-Inbox_price">
                            <input id="extensionAffiliate" name="extensionAffiliate" placeholder="Matt Johnson" />
                        </div>
                    </div>
                    <div>
                        <p>Sales Rep</p>
                        <div class="gray-background-Inbox_price">
                            <select id="extensionSalesRep" name="extensionSalesRep">
                                <option value="user1@test.com">user1@test.com</option>
                                <option value="salesrep2@test.com">salesrep2@test.com</option>
                                <option value="user3@test.com">user3@test.com</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <p>Order Type</p>
                        <div class="gray-background-Inbox_price">
                            <select id="extensionOrderType" name="extensionOrderType">
                                <option value="Batting Cage">Batting Cage</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="left-right-spcacer">
                <div class="gray-background-box">
                    <div class="gray-background-Inbox">
                        <p>Ticket Number</p>
                        <div class="gray-background-Inbox_price">
                            <input type="text" name="extensionTicketNumber" id="extensionTicketNumber" placeholder="18792398123798" />
                        </div>
                    </div>
                </div>
                 <div class="gray-background-box">
                    <div class="gray-background-Inbox">
                        <p>Card Number</p>
                        <div class="gray-background-Inbox_price">
                            <input type="text" name="extensionCardNumber" id="extensionCardNumber" placeholder="4324-2342-5477-4564" onkeyup="formatCardNumber(this)" />
                        </div>
                    </div>
                </div>
                 <div class="gray-background-box">
                    <div class="gray-background-Inbox">
                        <p>Date</p>
                        <div class="gray-background-Inbox_price">
                            <input type="text" name="extensionDate" id="extensionDate" placeholder="09-20" onkeyup="formatCardDate(this)" />
                        </div>
                    </div>
                </div>
                 <div class="gray-background-box">
                    <div class="gray-background-Inbox">
                        <p>CVV</p>
                        <div class="gray-background-Inbox_price">
                            <input type="text" maxlength="5" name="extensionCvv" id="extensionCvv" placeholder="781" />
                        </div>
                    </div>
                </div>

                <!-- <button class="ply_with_stripe" id="extensionPayWithStripe">
                    Play With Stripe
                </button> -->
            </div>

        </div>
        <div class="true-diamond-form-button">
            <button class="ply_with_stripe" id="extensionPayWithStripe">
                Play With Stripe
            </button>
        </div>
    </div>
   
    
    

    <script>
        window.addEventListener('load', (event) => {
            // invoke the payment toggle function
            postTogglePaymentButton();
        });
        
        function bindEvent(element, eventName, eventHandler) {
            element.addEventListener(eventName, eventHandler, false);
        }

        // Send a message to the parent
        var sendMessage = function (msg) {
            window.parent.postMessage(msg, '*');
        };

        // var urlParams = new URLSearchParams(decodeURIComponent(window.location.search));

        // var oliverEmail = urlParams.get("user");
        // var salesPersonEmail = document.getElementById("salesPersonEmail");
        // salesPersonEmail.value = oliverEmail;

        var extensionPayWithStripe = document.getElementById('extensionPayWithStripe');
        bindEvent(extensionPayWithStripe, 'click', function (e) {
            var extensionAffiliate = document.getElementById("extensionAffiliate").value;
            var extensionSalesRep = document.getElementById("extensionSalesRep").value;
            var extensionOrderType = document.getElementById("extensionOrderType").value;
            var extensionTicketNumber = document.getElementById("extensionTicketNumber").value;
            var extensionCardNumber = document.getElementById("extensionCardNumber").value;
            var extensionDate = document.getElementById("extensionDate").value;
            var extensionCvv = document.getElementById("extensionCvv").value;

            var jsonMsg = {
                oliverpos:
                {
                    "event": "addData"
                },
                data:
                {
                    customTags:
                    {
                        "affiliateID": extensionAffiliate,
                        "salesRep": extensionSalesRep,
                        "orderType": extensionOrderType,
                        "ticketNumber": extensionTicketNumber,
                        "cardNumber": extensionCardNumber,
                        "date": extensionDate,
                        "cvv": extensionCvv,
                    }
                }
            }

            sendMessage(JSON.stringify(jsonMsg));

            // invoke the extension finished function
            extensionFinished();
        });    


        // var extensionFinishedButton = document.getElementById('extension_finished');
        // bindEvent(extensionFinishedButton, 'click', function (e) {
        //     // invoke the extension finished function
        //     extensionFinished();
        // });

        var extensionFinished = function() {
            var jsonMsg = {
                oliverpos:
                {
                    event: "extensionFinished",
                    wordpressAction: "tds_neworder"
                }
            }

            sendMessage(JSON.stringify(jsonMsg));

            // invoke the payment toggle function
            postTogglePaymentButton(true);
        }

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

        var formatCardNumber = function(element) {
            let ele = document.getElementById(element.id);
            ele = ele.value.split('-').join('');    // Remove dash (-) if mistakenly entered.

            let finalVal = ele.match(/.{1,4}/g).join('-');
            document.getElementById(element.id).value = finalVal;
        }

        var formatCardDate = function(element) {
            let ele = document.getElementById(element.id);

            ele = ele.value.split('-').join('');    // Remove dash (-) if mistakenly entered.

            let finalVal = ele.match(/.{1,2}/g).join('-');
            document.getElementById(element.id).value = finalVal;

        }

    </script>

</body>
</html>