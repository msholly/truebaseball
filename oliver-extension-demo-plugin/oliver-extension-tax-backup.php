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
                <p>Extension Tax Amount:</p>
                <input type="number" id="extensionTaxAmount" name="extensionTaxAmount" class="inp_cont small" min="0.1" />

                <button id="extension_tax_add_button">Push Extension Tax to OliverPOS</button>
                <br>
            </div>

            <br>
            <div style="border: 1px solid #fff"></div>
            <br>

            <button id="extension_finished">Extension Finished</button>

       </div>
    </div>

    <script>
        var registerCartData;

        window.addEventListener('load', (event) => {
            // invoke the payment toggle function
            postTogglePaymentButton();
        });

        window.addEventListener('message', function(e) {
            let msgData = JSON.parse(e.data);

            if(typeof msgData !== "undefiend"){
                registerCartData = msgData;
            }

            console.log("frame page", msgData)
        }, false);

        function bindEvent(element, eventName, eventHandler) {
            element.addEventListener(eventName, eventHandler, false);
        }

        // Send a message to the parent
        var sendMessage = function (msg) {
            console.log("frame sendMessage", msg);
            window.parent.postMessage(msg, '*');
        };

        var urlParams = new URLSearchParams(decodeURIComponent(window.location.search));

        var oliverEmail = urlParams.get("user");

        var extensionTaxAddButtom = document.getElementById('extension_tax_add_button');
        bindEvent(extensionTaxAddButtom, 'click', function (e) {
            let extensionTaxAmount = document.getElementById("extensionTaxAmount").value;
            let extensionTaxCalc = calculateExtensionTax(extensionTaxAmount);
            var jsonMsg = {
                oliverpos:
                {
                    event: "updateProductTaxes"
                },
                data:
                {
                    products: extensionTaxCalc
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

        function calculateExtensionTax(taxAmount = 0) {
            let updateExtensionTaxData = new Array();
            let calcCartTotal = 0;


            if(typeof updateExtensionTaxData !== "undefined"){
                let listItemsData = registerCartData.data.checkoutData.cartProducts;

                for (let get_i_data of listItemsData) {
                    let getItemPrice = parseFloat(get_i_data.amount);
                    // let getItemTax = parseFloat(get_i_data.tax);

                    calcCartTotal += getItemPrice;
                }

                let getTaxInPercent = parseFloat(parseFloat(taxAmount) * (100 / calcCartTotal));
                console.log(`taxAmount = ${taxAmount}, calcCartTotal = ${calcCartTotal}, getTaxInPercent = ${getTaxInPercent}`)
                
                for (let set_i_data of listItemsData) {
                    let itemPrice = parseFloat(set_i_data.amount)
                    let updatedTax = (itemPrice * (getTaxInPercent / 100));

                    set_i_data.tax = updatedTax;
                    updateExtensionTaxData.push(set_i_data);
                }
            }

            return updateExtensionTaxData;
        }

    </script>

</body>
</html>