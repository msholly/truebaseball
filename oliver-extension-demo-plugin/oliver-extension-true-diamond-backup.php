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

            <!-- for display data -->
            <div id="parentData"></div>

            <h1>Client Extension</h1>

            <div>
                <p>Sales Rep:</p>
                <select id="salesPersonEmail" name="salesPersonEmail" class="inp_cont">
                    <option value="user1@test.com">user1@test.com</option>
                    <option value="salesrep2@test.com">salesrep2@test.com</option>
                    <option value="user3@test.com">user3@test.com</option>
                </select>
                <p>AffiliateID:</p>
                <input type="text" id="affiliateID" class="inp_cont small"/>
                <button id="customtags_button">Push custom tags to OliverPOS</button>
            </div>

            <br>
            <div style="border: 1px solid #fff"></div>
            <br>

            <div>
                <p>Ticket:</p>
                <input type="text" id="ticketNumber" class="inp_cont small"/>
                <button id="ticketnumber_button">Push ticket to OliverPOS</button>
            </div>

            <br>
            <div style="border: 1px solid #fff"></div>
            <br>

            <div>
                <p>City:</p>
                <input type="text" id="cityName" class="inp_cont small"/>
                <button id="cityname_button">Push city to OliverPOS</button>
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
        
        function bindEvent(element, eventName, eventHandler) {
            element.addEventListener(eventName, eventHandler, false);
        }

        // Send a message to the parent
        var sendMessage = function (msg) {
            window.parent.postMessage(msg, '*');
        };

        var urlParams = new URLSearchParams(decodeURIComponent(window.location.search));

        var oliverEmail = urlParams.get("user");
        var salesPersonEmail = document.getElementById("salesPersonEmail");
        salesPersonEmail.value = oliverEmail;

        var customtagsButton = document.getElementById('customtags_button');
        bindEvent(customtagsButton, 'click', function (e) {
            var mySalesPerson = document.getElementById("salesPersonEmail").value;
            var myaffiliateID = document.getElementById("affiliateID").value;

            var jsonMsg = {
                oliverpos:
                {
                    "event": "addData"
                },
                data:
                {
                    customTags:
                    {
                        "affiliateID": myaffiliateID,
                        "salesRep": mySalesPerson
                    }
                }
            }

            sendMessage(JSON.stringify(jsonMsg));
        });
        
        var ticketnumberButton = document.getElementById('ticketnumber_button');
        bindEvent(ticketnumberButton, 'click', function (e) {
            var ticketNumber = document.getElementById("ticketNumber").value;

            var jsonMsg = {
                oliverpos:
                {
                    event: "addData"
                },
                data:
                {
                    ticket:
                    {
                        "ticketNumber": ticketNumber
                    }
                }
            }

            sendMessage(JSON.stringify(jsonMsg));
        });


        var cityNameButton = document.getElementById('cityname_button');
        bindEvent(cityNameButton, 'click', function (e) {
            var cityName = document.getElementById("cityName").value;

            var jsonMsg = {
                oliverpos:
                {
                    event: "addData"
                },
                data:
                {
                    city:
                    {
                        "cityName": cityName
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