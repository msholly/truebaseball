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
        .gray-background-Inbox_price select,
        .add_tax_amount input  {
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
        .gray-background-Inbox-space select,
        .add_tax_amount input {
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
        .true-diamond-form-button {
            background: #46A9D4;
            /*border-radius: 0px 0px 8px 8px;*/
            height: 70px;
            border: 1px solid #46A9D4;
            color: #ffffff;
            /*width: 100%;*/
            font-size: 20px;  
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .true-diamond-form-button .ply_with_stripe {
            background-color: #ffffff;
            border: 0px;
            color: #000000;
            padding: 7px 15px;
            font-size: 20px; 
            cursor: pointer;
        }
        .true-diamond-form {
            /*height: 100vh;*/
        }
        .true-diamond-form-scroll {
            height: calc(100vh - 71px);   
            overflow-y: scroll;
        }    
        .true-daimond-flex {
            display: flex;
            align-items: center;
            color: #000;
            font-size: 20px;
            height: 90px;
            margin-bottom: 15px;
            justify-content: space-around;

        }
        .true-daimond-flex .push-top {
            position: relative;
            top: 15px;
        }
        .true-daimond-flex > div {
            margin-right: 10px;
        }
        .add_tax_amt {
            text-align: center;
        }
        .add_tax_amount {
            background-color: #f5f5f5;
            padding: 10px;
        }
        .add_tax_amount input {
            background-color: #ffffff;
            border: 1px solid #979797;
        }
        .add_tax_amount input:focus {
            outline: none;
        }   
        .ellipsis {
          text-overflow: ellipsis;
          white-space: nowrap;
          overflow: hidden;
          width: 150px;
        }
    </style>
</head>
<body style="overflow: hidden; margin: 0px">

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
                <div class="true-diamond-form-button">
                    <button class="ply_with_stripe" id="extensionCustomTagsButtone">
                        Add To Sale
                    </button>
                </div>

                <div id="extensionProductList">

                    

                </div>

                <div class="true-diamond-form-button">
                    <button class="ply_with_stripe" id="extensionTaxAddButtom">
                        Add To Sale
                    </button>
                </div>

            </div>

        </div>
        <div class="true-diamond-form-button">
            <button class="ply_with_stripe" id="extensionFinishedButton">
                Finished
            </button>
        </div>
    </div>
   
    
    

    <script>
        var webRegisterCartData;

        window.addEventListener('load', (event) => {
            // invoke the payment toggle function
            postTogglePaymentButton();
        });

        window.addEventListener('message', function(e) {
            let msgData = JSON.parse(e.data);

            if(typeof msgData !== "undefiend"){
                webRegisterCartData = msgData;
                appendWebRegisterCartData();
            }

            console.log("frame page", msgData)
        }, false);
        
        function bindEvent(element, eventName, eventHandler) {
            element.addEventListener(eventName, eventHandler, false);
        }

        // Send a message to the parent
        var sendMessage = function (msg) {
            console.log("extension msg", msg);
            window.parent.postMessage(msg, '*');
        };

        // var urlParams = new URLSearchParams(decodeURIComponent(window.location.search));

        // var oliverEmail = urlParams.get("user");
        // var salesPersonEmail = document.getElementById("salesPersonEmail");
        // salesPersonEmail.value = oliverEmail;

        var extensionCustomTagsButtone = document.getElementById('extensionCustomTagsButtone');
        bindEvent(extensionCustomTagsButtone, 'click', function (e) {
            var extensionAffiliate = document.getElementById("extensionAffiliate").value;
            var extensionSalesRep = document.getElementById("extensionSalesRep").value;
            var extensionOrderType = document.getElementById("extensionOrderType").value;
            var extensionTicketNumber = document.getElementById("extensionTicketNumber").value;

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
                        "ticketNumber": extensionTicketNumber
                    }
                }
            }

            sendMessage(JSON.stringify(jsonMsg));
        });    


        var extensionFinishedButton = document.getElementById('extensionFinishedButton');
        bindEvent(extensionFinishedButton, 'click', function (e) {
            // invoke the extension finished function
            extensionFinished();
        });

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

        var appendWebRegisterCartData = function() {
            let listItemsData = webRegisterCartData.data.checkoutData.cartProducts;
            if (typeof listItemsData !== "undefiend") {
                document.getElementById("extensionProductList").innerHTML = " ";
                let listItemsDataIndex = 1;
                for (let get_i_data of listItemsData) {
                    document.getElementById("extensionProductList").innerHTML += '<div class="true-daimond-flex"> <div class="push-top">' + get_i_data.quantity + '</div> <div class="push-top ellipsis" title="' + get_i_data.title + '">' + get_i_data.title + '</div> <div class="push-top">' + get_i_data.amount + '</div> <div class="add_tax_amt"> Add Tax Amount <div class="add_tax_amount"> <input type="text" id="listItemsDataTaxInput-'+listItemsDataIndex+'" value="' + get_i_data.tax + '"> </div> </div> </div>';

                    listItemsDataIndex++;
                }
            } else {
                document.getElementById("extensionProductList").innerHTML = "Data not found!";
            }
        }


        var calculateExtensionTax = function() {
            let updateExtensionTaxData = new Array();
            let listItemsData = webRegisterCartData.data.checkoutData.cartProducts;
            let listItemsDataIndex = 1;
           
            for (let set_i_data of listItemsData) {
                set_i_data.tax = document.getElementById("listItemsDataTaxInput-" + listItemsDataIndex).value;
                updateExtensionTaxData.push(set_i_data);
                listItemsDataIndex++;
            }

            return updateExtensionTaxData;
        }

        var extensionTaxAddButtom = document.getElementById('extensionTaxAddButtom');
        bindEvent(extensionTaxAddButtom, 'click', function (e) {
            let extensionTaxCalc = calculateExtensionTax();
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

    </script>

</body>
</html>