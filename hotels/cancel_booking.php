<?php
include_once '../src/Hotels/Hotels.php';

$Hotels = new Hotels();
$Hotels->payload = file_get_contents("buyer.xml");
$Hotels->timeout = 120;

// Now let's make a booking request!
$params['order_id'] = $_GET['order_id'];

$request = $Hotels->cancel_booking($params);
if($request['success'] == false):
    echo $request['msg'];
    die();
endif;

$result = simplexml_load_string($request['data']);

$response = $result;
if($response->success == 0):
    echo (string)$response->msg;
    die();
endif;

$orderInfo = $response->orderInfo;

include_once 'header.php';
?>
<div class='container'>
    <div class='row'>
        <div class='col-sm-12 col-xs-12'>
            <table class='table table-condensed table-bordered'>
                <thead>
                    <tr class="bg-primary">
                        <th colspan="4">Order Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>Order Information</b></td>
                        <td>
                            OrderID : <?=(int)$orderInfo->Order_ID?><br />
                            Booking Ref : <?=(int)$orderInfo->Booking_Reference?><br />                            
                            
                        </td>
                        <td><b>Customer Detail</b></td>
                        <td>
                            <?=(string)$orderInfo->Title." ".(string)$orderInfo->First_Name." ".(string)$orderInfo->Last_Name?><br />
                            <?=(string)$orderInfo->Address.", ".(string)$orderInfo->City.", ".(string)$orderInfo->Postal_Code?><br />
                            <?=(string)$orderInfo->Phone.", ".(string)$orderInfo->Email?><br />
                            Request:: <?=(string)$orderInfo->Request?>
                        </td>
                    </tr>                    
                </tbody>
                <thead>
                    <tr class="bg-primary">
                        <th colspan="4">Hotel Information</th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="4">
                        <?=$orderInfo->Hotel_Name?><Br />
                        <?=$orderInfo->Hotel_Address?><Br />
                        <?=$orderInfo->Hotel_City.", ".$orderInfo->Hotel_Country?>
                    </td>
                </tr>
                <thead>
                    <tr class="bg-info">
                        <th colspan="4">Room Information</th>
                    </tr>
                </thead>
                <tr>
                    <td>Room</td>
                    <td>Pax</td>
                    <td>Price</td>
                    <td>Booking Date</td>
                </tr>
                
                <tr class="bg-primary">
                    <th colspan="4">
                        Cancellation Conditions
                    </th>
                </tr>
                <tr>
                    <td>Order id: <?=$orderInfo->Order_ID?></td>
                    <td>Booking Reference: <?=$orderInfo->Booking_Reference?></td>
                    <td>Cancellation Cost: <?=$response->CancellationCost?></td>
                    <td>Cancellation Token: <?=$response->CancellationToken?></td>
                </tr>
                <tr class="bg-success">
                    <td colspan="4">
                        <form id="cancelForm" class="form" action="cancel_booking_jac.php" method="post">
                            <input type="hidden" name="order_id" value="<?=$orderInfo->Order_ID?>" />
                            <input type="hidden" name="CancellationCost" value="<?=$response->CancellationCost?>" />
                            <input type="hidden" name="CancellationToken" value="<?=$response->CancellationToken?>" />
                            <button type="button" onclick="return app.cancelBooking(this);" class="btn btn-danger btn-lg pull-right">
                                Cancel Booking
                            </button>
                        </form>
                    </td>
                </tr>
            </table>
            
            <script>
                var app = app || {};
                app.cancelBooking = function($this){
                    if(!confirm("Are you sure you want to cancel this booking?")){
                        return false;
                    }
                    $($this).html("Please wait...");
                    $($this).attr("disabled","disabled");
                    $.ajax({
                        url : app.baseUrl+"confirm_booking_cancel.php",
                        type : "post",
                        data : $("#cancelForm").serialize(),
                        dataType : "json",
                        success : function(data){
                            console.log(data);
                            $($this).html("Cancel Booking");
                            $($this).removeAttr("disabled");
                            if(data.success){
                                alert("Booking has been cancelled");
                                window.location = app.baseUrl + "order_list.php";
                                return false;
                            }
                            
                            alert(data.msg);
                        },
                        error : function(data){
                            $($this).html("Cancel Booking");
                            $($this).removeAttr("disabled");
                            alert("There was a problem, please try again later");
                            console.log(data);
                        }
                    });
                    
                    return false;
                };
            </script>
        </div>
    </div>
</div>

<?php
    include_once 'footer.php';
?>