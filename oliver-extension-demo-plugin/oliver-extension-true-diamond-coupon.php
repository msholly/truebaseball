<?php 
    $coupons = get_posts(array(
        'post_type' => 'shop_coupon',
        'post_status' => array('publish'),
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key'     => 'discount_type',
                'value'   => 'fixed_cart',
                'compare' => '=',
            ),
            array(
                'key'     => 'date_expires',
                'value'   => strtotime(date('Y-m-d')),
                'compare' => '>=',
            ),
        ),
    ));
// echo "<pre>";
// print_r($coupons);
// exit();
?>

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

        .main-wrap-content-list {
            height: calc(100vh - 70px);
            overflow-y: scroll;
            padding-left: 20px;
            padding-right: 20px;
        }
        .main-wrap-content-list table {
            width: 100%;
            border-spacing: 0px;
        }
        .main-wrap-content-list table th {
            border-top: 1px solid #c5c5c5;
        }
        .main-wrap-content-list table th,
        .main-wrap-content-list table td {
            text-align: left;
            height: 50px;
            border-bottom: 1px solid #c5c5c5;
            font-size: 18px;
            padding: 5px;
        }
        .main-wrap-content-list caption {
            font-size: 24px;
            font-weight: bold;
            text-align: left;
            padding-bottom:  15px;
            padding-top:  15px;
            border-bottom: 1px solid #c5c5c5;
        }
    </style>
</head>
<body style="overflow: hidden; margin: 0px">

    <div class="true-diamond-form">
        <div class="main-wrap-content-list">
            <table>
                <caption>Coupon's</caption>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Code</th>
                        <th>Amount</th>
                        <th>Push to Oliver</th>
                    </tr>
                </thead>
                <tbody>

                    <?php 
                        foreach ($coupons as $key => $coupon) { 
                                $coupon_id = (int) $coupon->ID;
                                $coupon_code = get_the_title($coupon_id);
                                $coupon_amt = get_post_meta($coupon_id, 'coupon_amount', true);
                            ?>
                                <tr>
                                    <td><?php echo $key+1; ?></td>
                                    <td><?php echo $coupon_code; ?></td>
                                    <td><?php echo $coupon_amt; ?></td>
                                    <td style="cursor: pointer;" onclick="pushToOliver(`<?php echo $coupon_code ?>`, `<?php echo $coupon_amt ?>`)">
                                        <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDc3LjE3NSA0NzcuMTc1IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA0NzcuMTc1IDQ3Ny4xNzU7IiB4bWw6c3BhY2U9InByZXNlcnZlIiBmaWxsPSIjNzM3MzczIj4NCjxnPg0KCTxwYXRoIGQ9Ik0zNjAuNzMxLDIyOS4wNzVsLTIyNS4xLTIyNS4xYy01LjMtNS4zLTEzLjgtNS4zLTE5LjEsMHMtNS4zLDEzLjgsMCwxOS4xbDIxNS41LDIxNS41bC0yMTUuNSwyMTUuNQ0KCQljLTUuMyw1LjMtNS4zLDEzLjgsMCwxOS4xYzIuNiwyLjYsNi4xLDQsOS41LDRjMy40LDAsNi45LTEuMyw5LjUtNGwyMjUuMS0yMjUuMUMzNjUuOTMxLDI0Mi44NzUsMzY1LjkzMSwyMzQuMjc1LDM2MC43MzEsMjI5LjA3NXoNCgkJIi8+DQo8L2c+DQo8L3N2Zz4NCg==" width="20">
                                    </td>
                                </tr>
                            
                    <?php } ?>

                </tbody>
            </table>
        </div>
        <div class="true-diamond-form-button">
            <button class="ply_with_stripe" id="extensionFinishedButton">
                Finished
            </button>
        </div>
    </div>
   
    
    

    <script>
        var webRegisterCartData;
        var oliverExtensionTargetOrigin = '<?php echo OLIVER_EXTENSION_TARGET_ORIGIN; ?>';

        window.addEventListener('load', (event) => {
            // invoke the payment toggle function
            postTogglePaymentButton();
            toggleExtensionReady();
        });

        window.addEventListener('message', function(e) {
            if (e.origin !== oliverExtensionTargetOrigin) {
                console.log("Invalid origin " + e.origin);
            } else {
                let msgData = JSON.parse(e.data);

                if(typeof msgData !== "undefiend"){
                    webRegisterCartData = msgData;
                    appendWebRegisterCartData();
                }

                console.log("frame page", msgData)
            }
        }, false);
        
        function bindEvent(element, eventName, eventHandler) {
            element.addEventListener(eventName, eventHandler, false);
        }

        // Send a message to the parent
        var sendMessage = function (msg) {
            console.log("extension msg", msg);
            window.parent.postMessage(msg, oliverExtensionTargetOrigin);
        };

        // var urlParams = new URLSearchParams(decodeURIComponent(window.location.search));

        // var oliverEmail = urlParams.get("user");
        // var salesPersonEmail = document.getElementById("salesPersonEmail");
        // salesPersonEmail.value = oliverEmail;

        function pushToOliver(code, amt) {
            var jsonMsg = {
                oliverpos:
                {
                    "event": "addCoupon"
                },
                data:
                {
                    coupon:
                    {
                        "code": code,
                        "amount": amt,
                    }
                }
            }

            sendMessage(JSON.stringify(jsonMsg));
        }    


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


        var toggleExtensionReady = function() {
            let jsonMsg = {
                oliverpos: {
                    "event": "extensionReady"
                },
            }

            sendMessage(JSON.stringify(jsonMsg));
        }

    </script>

</body>
</html>