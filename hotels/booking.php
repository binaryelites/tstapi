<?php
/** booking data render **/
$postparams = $_POST;
$room_id_array = $_POST['room_id'];

$rooms_with_qty = array();
$params = array();

/*$params['user_id'] = 1;
$params['api_key'] = 123;
$params['api_pass'] = 123;*/


$params['hotel_id'] = $_POST['hotel_id'];
$params["check_in_date"] = $_POST['check_in_date'];
$params["check_out_date"] = $_POST['check_out_date'];

/** added for jac travel and tst integration * */
$params['owner'] = $_POST['owner'];
$params['__post__'] = $_POST['__post__'];
/** added for jac travel and tst integration * */

foreach ($room_id_array as $k => $v) {
    if (isset($_POST['quantity'][$v]) && (int) $_POST['quantity'][$v] > 0) {
        $guestInformation = array();

        if(isset($_POST['guest_name']["'title'"]))
        {            
            $guestCount = count($_POST['guest_name']["'title'"][$v]);
            while($guestCount > 0):
                $guestInformation[$v][] = array(
                    "title" => $_POST['guest_name']["'title'"][$v][$guestCount],
                    "first_name" => $_POST['guest_name']["'first_name'"][$v][$guestCount],
                    "last_name" => $_POST['guest_name']["'last_name'"][$v][$guestCount],
                    "age" => $_POST['guest_name']["'age'"][$v][$guestCount]
                );            
                $guestCount--;
            endwhile;
        }
        
        $params['rooms'][] = array(
            "room_id" => $v,
            "quantity" => isset($_POST['quantity'][$v]) ? $_POST['quantity'][$v] : 0,
            "max_children" => isset($_POST['max_children'][$v]) ? $_POST['max_children'][$v] : 0,
            "guest_information" => $guestInformation,
        );

        $rooms_with_qty[$v] = $v;
    }
}

$bookingData = array(    
    "__post__" => $params['__post__'],
    "hotel_id" => $params['hotel_id'],
    "rooms" => $params['rooms'],
    "room" => $_POST['room'],
    "owner" => $_POST['owner']
);

include_once '../src/Hotels/Hotels.php';

$Hotels = new Hotels();
$Hotels->payload = file_get_contents("buyer.xml");
$Hotels->timeout = 120;

// Now let's make a booking request!
$request = $Hotels->book_hotel($bookingData);
if($request['success'] == false):
    echo $request['msg'];
    die();
endif;

try {
    $result = simplexml_load_string($request['data']);

    if ($result->success == 0):
        ?>
        <h3>No hotel found</h3>
        <textarea style="width: 100%;" rows="10"><?= $request['data'] ?></textarea>
        <?php
        die();
    endif;
    $hotelInfo = $result->hotelInfo;
    $hotelRooms = $result->hotelRooms;

    
    $order_id = $result->order->ID;

    $params['order_id'] = $order_id;
    $params['item_id'] = 0;
    
    include_once 'header.php';
    ?>
       
            <style>
                
                #customerDetails{
                    display: table;
                    width: 100%;
                    clear: both;
                }
                
                #customerDetails .form-group{                    
                    width: 33.333333%;
                    margin-bottom: 5px;
                    float:left;
                }
                #customerDetails .form-group label{
                    width: 120px !important;
                    text-align: right;
                    float: left;
                }
                
                #customerDetails .form-group label.error{
                    width: 100% !important;
                    /* float: left; */
                    text-align: left;
                    margin-left: 120px;
                    font-size: 12px;
                    color: red;
                    font-family: Arial;
                }
            </style>
            <div class="container">
    
                <h3>Order Prebooked with : <?= $order_id ?></h3>
                <?php
                if (isset($result->prebookResult->PreBookingToken)):
                    $preBookResult = $result->prebookResult;
                    ?>   
                    <table class="table table-condensed table-bordered" style='width: 100%'>
                        <thead>
                            <tr class="bg-primary">
                                <th colspan="2">PreBook Details</th>
                            </tr>                
                        </thead>
                        <tr>
                            <td>Prebook Token: <?= (string) $preBookResult->PreBookingToken ?></td>
                            <td>Total : <?= (float) $preBookResult->TotalPrice ?></td>
                        </tr>
                    <?php
                    if (isset($preBookResult->Cancellations) && count($preBookResult->Cancellations->item) > 0):
                    ?>
                            <thead>
                                <tr class="bg-info">
                                    <th colspan="2">Cancellation Policy</th>
                                </tr>                        
                            </thead>
                        <?php
                        foreach ($preBookResult->Cancellations->item as $c):
                        ?>     
                                <tr>
                                    <td>
                                        StartDate: <?= (string) $c->StartDate ?><br />
                                        EndDate: <?= (string) $c->EndDate ?>
                                    </td>
                                    <td>
                                        <b>Penalty:</b> <?= (string) $c->Penalty ?>
                                    </td>
                                </tr>
                            <?php
                        endforeach;
                    endif;
                    ?>
                    </table>
                <?php
                endif;
                ?>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <img src="<?= $hotelInfo->Image_Banner ?>" style="float:left; width: 200px; height: 130px; margin-right: 10px" />
                    <h3><?= $hotelInfo->Name ?></a></h3>
                    <p><?= $hotelInfo->City_Name ?>, <?= $hotelInfo->Country_Name ?></p>
                    <p><?= $hotelInfo->Address ?></p>
                    <p><?= $hotelInfo->Description ?></p>            
                </div>

                <div class="col-sm-12 col-md-12 col-xs-12">                
                    <form action="confirm_booking.php" id='bookingForm' method="post" >
                        <h3>Available Rooms</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-primary">
                                        <th colspan="7">Check In/Check Out Date</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td colspan="7">
                                        <b>Hotel ID: </b><?= $params['hotel_id'] ?> <br />
                                        <b>Checkin: </b><?= $params['check_in_date'] ?> <br />
                                        <b>Checkout: </b><?= $params['check_out_date'] ?>
                                    </td>
                                </tr>
                                <thead>                
                                    <tr class="bg-primary">
                                        <th>Room type</th>
                                        <th>Pax</th>
                                        <th>Children for 1 night(s)</th>
                                        <th>No of Child</th>
                                        <th>Price for 1 night(s) (EUR)</th>
                                        <th>Booking Qty</th>
                                        <th>Reservation</th>
                                    </tr>
                                </thead>
                            <?php
                            $availableRoomQuantity = 0;
                            $rooms = array();
                            if (isset($result->hotelRooms->item) && count($result->hotelRooms->item)) {
                                $tcount = 1;
                                foreach ($result->hotelRooms->item as $r) {
                                    $rooms[] = $r;
                                    $availableRoomQuantity += (int) $r->Quantity - (int) $r->Booked_Quantity;
                                    ?>    
                                        <tr>
                                            <td>
                                                <img class="img-responsive room-thumb" style="width: 100px" alt="<?= $r->Name ?>" src="<?=$r->Image ?>" />
                                                <b><?= $r->Name ?></b> <br />
                                                <?= (int) $r->Quantity - (int) $r->Booked_Quantity ?> Rooms Left
                                            </td>
                                            <td>
                                                <?php
                                                $paxToolTipText = "{$r->Max_Adults} adult(s) and {$r->Max_Children} children are allowed";
                                                ?>
                                                <?= ($r->Max_Adults + $r->Max_Children) ?> x <i class="glyphicon glyphicon-user" data-toggle="tooltip" data-placement="top" title="<?= addslashes($paxToolTipText) ?>"></i>
                                            </td>
                                            <td>
                                                Children will cost &euro; <?= $r->Price_Per_Child ?> each
                                            </td>
                                            <td>                                            
                                                <?= (int) $r->Children ?>
                                            </td>
                                            <td>
                                                <b>&euro; <?= ((float) $r->Min_Room_Price <= 0) ? $r->Tariff : (float) $r->Min_Room_Price ?></b><br />
                                                <small>8% vat included</small>
                                            </td>
                                            <td>                                            
                                                <?= (int) $r->Quantity ?>
                                            </td>
                                        <?php
                                        if ($tcount == 1):
                                            $tcount++;
                                            ?>
                                                <td rowspan="<?= count($hotelRooms->item) ?>" style="vertical-align: middle">
                                                    <span id="total_order_price">&euro; <?= $postparams['order_total'] ?></span>                                                        
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                        <tr class="bg-success">
                                            <th colspan="7">Important Data</th>
                                        </tr>
                                        <tr class="bg-info" style="font-size: 11px">    
                                            <td colspan="7">
                                                <?php
                                                echo htmlspecialchars_decode((string)$hotelRooms->Important_Data);
                                                ?>
                                            </td>
                                        </tr>
                                <?php
                            }
                        }
                        ?>
                            </table>
                        </div>
                        <hr style="margin: 10px 5px" />
                        <?php
                        if (isset($result->hotelRooms->item) && count($result->hotelRooms->item)) {
                            $roomno = 1;
                            foreach ($result->hotelRooms->item as $r) {
                        ?>
                            <div class="col-md-12 col-sm-12 col-xs-12" id="guestInformationContainer-<?= (int)$r->ID ?>">
                                <h3 style="margin:0px;font-size: 16px; font-weight: bold">
                                    Room <?= $roomno++ ?>: <?= (string)$r->Name ?>
                                </h3>
                            <?php

                            $guestName = str_replace("<![CDATA[", "", (string)$r->Guest_Name);
                            $guestName = str_replace("]]>", "", $guestName);

                            $guestArray = json_decode($guestName,true);                                
                            if(is_array($guestArray) && count($guestArray)):
                                $RoomGuestCount = 1;
                                foreach($guestArray as $gi):                                        
                                ?>
                                    Guest <?=$RoomGuestCount++?>: <?=$gi['title']." ".$gi['first_name']." ".$gi['last_name']?>, Age: <?=$gi['age']?> <hr />
                                <?php
                                endforeach;                                    
                            endif;                                
                            ?>
                            </div>    
                                <?php
                            }
                        }
                        ?>
                        <div class="col-sm-12 col-xs-12 col-md-12" id="customerDetails">
                            <h3>Please provide these booking details</h3>
                            <div class="row">
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>Title</label>
                                    <input type="text" class="form-control input-sm required" name="guest[LeadGuestTitle]" aria-required="true">
                                </div>
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>First Name</label>
                                    <input type="text" class="form-control input-sm required" name="guest[LeadGuestFirstName]" aria-required="true">
                                </div>
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control input-sm required" name="guest[LeadGuestLastName]" aria-required="true">
                                </div>
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>Address Line 1</label>
                                    <input type="text" class="form-control input-sm required" name="guest[LeadGuestAddress1]" aria-required="true">
                                </div>
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>Address Line 2</label>
                                    <input type="text" class="form-control input-sm" name="guest[LeadGuestAddress2]">
                                </div>
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>City</label>
                                    <input type="text" class="form-control input-sm  required" name="guest[LeadGuestTownCity]" aria-required="true">
                                </div>
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>Post Code</label>
                                    <input type="text" class="form-control input-sm  required" name="guest[LeadGuestPostcode]" aria-required="true">
                                </div>
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>Phone</label>
                                    <input type="text" class="form-control input-sm required" name="guest[LeadGuestPhone]" aria-required="true">
                                </div>
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>Email</label>
                                    <input type="email" class="form-control input-sm required" name="guest[LeadGuestEmail]" aria-required="true">
                                </div>
                                <div class="form-group col-sm-4 col-xs-12">
                                    <label>Any Request</label>
                                    <textarea class="form-control" rows="6" name="guest[Request]"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr />
                        
                        <!-- order id -->
                        <input type="hidden" name="order_id" value="<?=$order_id?>" />
                        <input type="hidden" name="__post__" value="<?=$params['__post__']?>" />
                        <!-- order id -->
                        
                        <button id="submitBtn" class="btn btn-primary btn-lg" type="submit">
                            Confirm Booking
                        </button>
                    </form>

                </div>
            </div>
            <div id="result"></div>
            <script>
                var order_id = '<?= $order_id ?>';
                
                $(document).ready(function () {
                    $("#bookingForm").validate({
                        submitHandler : function(form){
                            //console.log($("#bookingForm").serialize());
                            //alert("test");
                            $("#submitBtn").attr("disabled","disabled");
                            $("#submitBtn").html("Please wait...");
                            $.ajax({
                                url: 'confirm_booking.php',
                                type : 'post',
                                data : $("#bookingForm").serialize(),
                                dataType : 'json',
                                success : function(data){
                                    $("#submitBtn").html("Confirm Booking");
                                    $("#submitBtn").removeAttr("disabled");
                                    alert(data.msg);
                                    if(data.success == true){
                                        window.location = 'view_order.php?order_id=' + order_id;
                                        $("#bookingForm").hide();
                                        var $html = '';
                                        $html += '<p>BookingReference: '+data.BookingReference+'</p>';
                                        $html += '<p>CustomerTotalPrice: '+data.CustomerTotalPrice+'</p>';
                                        $html += '<p>TradeReference: '+data.TradeReference+'</p>';
                                        $("#result").html($html);
                                    }
                                    console.log(data);
                                },
                                error : function(data){
                                    $("#submitBtn").html("Confirm Booking");
                                    $("#submitBtn").removeAttr("disabled");
                                    console.log(data);
                                    alert("There was a problem. Please try again later");
                                }
                            });

                            return false;
                        }
                    });
        
                });
                
            </script>
         
    <?php
    include_once 'footer.php';
} catch (Exception $ex) {
    var_dump($ex->getMessage());
    die();
}