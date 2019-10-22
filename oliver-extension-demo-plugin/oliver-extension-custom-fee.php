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
        .gray-background-Inbox_price input {
            text-align: center;
            border: 0px;
            background-color: transparent;
            box-shadow: none; 
            font-size: 18px;
            color: #4b4b4b; 
            border: 1px solid #979797;
            border-radius: 8px;
            height: 40px;
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

    </style>
</head>
<body>
     <div class="gray-background-box white-background-box">
        <div class="gray-background-Inbox gray-background-Inbox-space">
            <div>
                <p>Affiliate</p>
                <div class="gray-background-Inbox_price" id="affiliateName">Matt Johnson</div>
            </div>
            <div>
                <p>Sales Rep</p>
                <div class="gray-background-Inbox_price" id="salesRepository">Sales Rep</div>
            </div>
            <div>
                <p>Order Type</p>
                <div class="gray-background-Inbox_price" id="orderType">Batting Cage</div>
            </div>
        </div>
    </div>

    <div class="left-right-spcacer">
        <div class="gray-background-box">
            <div class="gray-background-Inbox">
                <p>Custom Fee Key</p>
                <div class="gray-background-Inbox_price">
                    <input type="text" id="customFeeKey" name="customFeeKey" placeholder="Custom Fee" />
                </div>
            </div>
        </div>
         <div class="gray-background-box">
            <div class="gray-background-Inbox">
                <p>Custom Fee Amount</p>
                <div class="gray-background-Inbox_price">
                    <input type="number" id="customFeeAmount" name="customFeeAmount" min="1" placeholder="10" />
                </div>
            </div>
        </div>
    </div>
    <button class="ply_with_stripe" id="custom_fee_add_button">
        Push Custom Fee to OliverPOS
    </button>

    <button class="ply_with_stripe" id="extension_finished">
        Extension Finished
    </button>

   <script>
        window.addEventListener('load', (event) => {
            // invoke the payment toggle function
            postTogglePaymentButton();

            // display query string values
            let getUrlParams = new URLSearchParams(window.location.search);
            
            let affiliateName = (typeof getUrlParams.get('userEmail') !== "undefined") ? getUrlParams.get('userEmail') : "Matt Johnson";
            let salesRepository = (typeof getUrlParams.get('salesRepository') !== "undefined") ? getUrlParams.get('salesRepository') : "Sales Rep";
            let orderType = (typeof getUrlParams.get('orderType') !== "undefined") ? getUrlParams.get('orderType') : "Batting Cage";

            // document.getElementById('affiliateName').innerHTML = affiliateName;
            // document.getElementById('salesRepository').innerHTML = salesRepository;
            // document.getElementById('orderType').innerHTML = orderType;
        });

        window.addEventListener('message', function(e) {
            let msgData = JSON.parse(e.data);

            console.log("frame page", msgData)
        }, false);

        function bindEvent(element, eventName, eventHandler) {
            element.addEventListener(eventName, eventHandler, false);
        }

        // Send a message to the parent
        var sendMessage = function (msg) {
            window.parent.postMessage(msg, '*');
        };

        var urlParams = new URLSearchParams(decodeURIComponent(window.location.search));

        var oliverEmail = urlParams.get("user");

        var customFeeAddButtom = document.getElementById('custom_fee_add_button');
        bindEvent(customFeeAddButtom, 'click', function (e) {
            let customFeeKey = document.getElementById("customFeeKey").value;
            let customFeeAmount = document.getElementById("customFeeAmount").value;

            var jsonMsg = {
                oliverpos:
                {
                    event: "saveCustomFee"
                },
                data:
                {
                    customFee:
                    {
                        "key": customFeeKey,
                        "amount": Math.abs(customFeeAmount)
                    }
                }
            }

          sendMessage(JSON.stringify(jsonMsg));
        });


        var extensionFinishedButton = document.getElementById('extension_finished');
        bindEvent(extensionFinishedButton, 'click', function (e) {
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

    </script>

</body>
</html>