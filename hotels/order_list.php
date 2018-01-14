<?php
include_once '../src/Hotels/Hotels.php';

$Hotels = new Hotels();
$Hotels->payload = file_get_contents("buyer.xml");
$Hotels->timeout = 120;

// Now let's make a booking request!
$params = array();
//$params['limit'] = 10;
//$params['offset'] = 4;

$request = $Hotels->get_order_list($params);
if($request['success'] == false):
    echo $request['msg'];
    die();
endif;

/*header('Content-Type: text/xml');
echo $request['data'];
die();*/

$order_list = false;
$result = simplexml_load_string($request['data']);

$order_list = $result->orders->item;

include_once 'header.php';
?>
<div class='container'>
    <div class='row'>
        <div class='col-sm-12 col-xs-12'>
            <table class='table table-condensed table-striped'>
                <thead>
                    <tr class="bg-primary">
                        <th>#</th>
                        <th>ID</th>
                        <th>Booking Reference</th>
                        <th>Hotel</th>                        
                        <th>Total Price</th>
                        <th colspan="2">Canceled</th>
                    </tr>                    
                </thead>
            <?php
            $si = 1;
            foreach($order_list as $o):             
            ?>
                <tr>
                    <td><?=$si++?></td>
                    <td>
                        <a href="view_order.php?order_id=<?=(int)$o->Order_ID?>">
                            <?=(int)$o->Order_ID?>                            
                        </a>
                    </td>
                    <td><?=(string)$o->Booking_Reference?></td>
                    <td>
                        <b><?=(string)$o->Hotel_Name?></b> <br />
                        <em><?=(string)$o->Hotel_City.", ".$o->Hotel_Country?></em><br />
                        <small><?=(string)$o->Hotel_Address?></small>
                    </td>
                    <td><?=(float)$o->Order_Total?></td>
                    <td><?=(strtolower((string)$o->Status_ID) == 'canceled') ? "Yes" : "No"?></td>
                    <td>
                        <?php if(strtolower((string)$o->Status_ID) !== 'canceled'): ?>
                        <a class="btn btn-danger btn-xs" href='cancel_booking.php?order_id=<?=$o->Order_ID?>'>
                            Cancel
                        </a>
                        <a class="btn btn-primary btn-xs" href='view_order.php?order_id=<?=$o->Order_ID?>'>
                            View
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
<?php
include_once 'footer.php';