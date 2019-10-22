<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Client Extension</title>
<!-- <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet"> -->
    <style type="text/css">
        body {
            margin: 0px;
            background: #46A9D4;
            font-family: 'Montserrat', sans-serif;
        }
        .oliver_from {
                position: absolute;
                left: 0px;
                right: 0px;
                bottom: 0px;
                top: 0px;
                margin: auto;
                color: #fff;
                display: flex;
                justify-content: center;
                align-items: center;
        }  
        .oliver_from p {
            font-size: 20px;
            margin-bottom: 10px;
            margin-top: 0px;
        }
        .oliver_from .inp_cont {
            width: 320px;
            border-radius: 4px;
            height: 40px;
            min-height: 40px;
            padding: 8px;
            padding-top: 0px;
            padding-bottom: 0px;
            border: 0px;
            box-shadow: none;
            display: block;
            font-size: 15px;
            margin-bottom: 20px;
        }
        .oliver_from .inp_cont.small {
            width: 305px;
        }
        .oliver_from button {
            width: 320px;
            border-radius: 4px;
            height: 42px;
            padding: 8px;
            padding-top: 0px;
            padding-bottom: 0px;
            border: 1px solid rgba(169,212,125,0.95);
            background-color: rgba(169,212,125,0.95);
            color: white;
            font-size: 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="oliver_from">
       <div>
            <h1>Client Extension</h1>
            <div id="parentData"></div>

            <div>
                <p>Custom Fee Key:</p>
                <input type="text" id="customFeeKey" name="customFeeKey" class="inp_cont small"/>

                <p>Custom Fee Amount:</p>
                <input type="number" id="customFeeAmount" name="customFeeAmount" class="inp_cont small" />

                <button id="custom_fee_add_button">Push Custom Fee to OliverPOS</button>

                <br>

            </div>

            <br>
            <div style="border: 1px solid #fff"></div>
            <br>

            <button id="extension_finished">Extension Finished</button>

       </div>
    </div>

    <script>
        window.addEventListener('load', (event) => {
            // invoke the payment toggle function
            postTogglePaymentButton();
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
                        "amount": -parseInt(customFeeAmount)
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