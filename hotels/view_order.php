<?php
include_once '../src/Hotels/Hotels.php';

$Hotels = new Hotels();
$Hotels->payload = file_get_contents("buyer.xml");
$Hotels->timeout = 120;

$params['order_id'] = $_GET['order_id'];

// Now let's make a booking request!
$request = $Hotels->get_order_info($params);
if($request['success'] == false):
    echo $request['msg'];
    die();
endif;

$exception = false;
$response = false;
try
{
    $result = simplexml_load_string($request['data']);
    $response = $result;    
}
catch(Exception $e)
{
    $response = false;
    $exception = $e->getMessage();
}

include_once 'header.php';

?>
<div class="container">
    <?php if(!$response): ?>
    <h3><?=$exception?></h3>
    <?php endif; ?>
    
    <?php 
  //  d($response);
        if($response): 
            $orderInfo = $response->orderInfo;
        
    ?>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <table class="table table-border table-condensed">
                <thead>
                    <tr class="bg-primary">
                        <th colspan="4">Order Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>Order Information</b></td>
                        <td>
                            OrderID : <?=(int)$orderInfo->ID?><br />
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
                <?php foreach($response->rooms->item as $r): ?>
                <tr>
                    <td>
                        <?=(string)$r->Name?><br/>
                        <?=(string)$r->RoomFacilities?>
                    </td>
                    <td>
                        Adult: <?=(int)$r->Adults?><br />
                        Children: <?=(int)$r->Children?><br />
                        Infant: <?=(int)$r->Infants?>
                    </td>
                    <td>
                        <?=(float)$r->Total?>
                    </td>
                    <td>
                        Check In: <?=(string)$r->Check_In?><br />
                        Check Out: <?=(string)$r->Check_Out?><br />
                    </td>                    
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>    
<?php

include_once 'footer.php';