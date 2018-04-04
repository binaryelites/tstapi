<?php
include_once '../src/Hotels/Hotels.php';

$Hotels = new Hotels();
$Hotels->payload = file_get_contents("buyer.xml");
$Hotels->timeout = 120;

// Now let's make a request!
$params = $_GET;
$request = $Hotels->get_hotel_info($params);

if(!$request['success']):
    echo $request['msg'];
    die();
endif;

try {
    $result = simplexml_load_string($request['data']);

    if ($result->success == 0):
        ?>
        <h3>No hotel found</h3>
        <textarea style="width: 100%;" rows="10"><?=$request['data']?></textarea>
        <?php
        die();
    endif;
    $hotelInfo = $result->hotelInfo;
    $hotelRooms = $result->hotelRooms;    
    $Invoice_Currency = (string)$result->invoice_currency;
    //d($hotelRooms,false);
    include_once 'header.php';
    ?>
    
        
            <style>                
                .form-group{                    
                    width: 100%;
                    margin-bottom: 5px;
                    float:left;
                }
                .form-group label{
                    width: 100%;
                    font-family: 'Arial';
                    font-size: 14px;
                    font-weight: 700;
                    display: block;
                }
                .form-group small{
                    display: block;
                    width: auto;
                }
                
                #guestRow{
                    width: 100%;
                    display: table;
                    margin-bottom: 15px;
                    border-bottom: 1px dotted #ccc;
                }
                
            </style>

            <div class="container">            
                <small>
                    <pre>
                        Profiler: <?=(string)$result->profiler->hotel_info?>
                    </pre>
                </small>
                <div class="col-sm-12 col-xs-12">
                    <img src="<?= $hotelInfo->Image_Banner ?>" style="float:left; width: 200px; height: 130px; margin-right: 10px" />
                    <h3><?= $hotelInfo->Name ?></a></h3>
                    <p><?= $hotelInfo->City_Name ?>, <?= $hotelInfo->Country_Name ?></p>
                    <p><?= $hotelInfo->Address ?></p>
                    <p><?= nl2br($hotelInfo->Description) ?></p>            
                </div>
                <div class="col-sm-12 col-xs-12">
                    <h3>Terms</h3>
                    <?= $hotelInfo->Terms_Conditions ?>
                </div>
                <div class="row">
                    <form action="booking.php" method="post" id="bookingForm">
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
                                        <b>Hotel ID: </b><?=$_GET['hotel_id']?> <br />
                                        <b>Checkin: </b><?=$_GET['search_hotel_checkin']?> <br />
                                        <b>Checkout: </b><?=$_GET['search_hotel_checkout']?>
                                        
                                        <input type="hidden" name="hotel_id" value="<?=$_GET['hotel_id']?>" />
                                        <input type="hidden" name="check_in_date" value="<?=$_GET['search_hotel_checkin']?>" />
                                        <input type="hidden" name="check_out_date" value="<?=$_GET['search_hotel_checkout']?>" />
                                        
                                        <!-- MANDATORY FIELD -->
                                        <!-- use the below field for jac travel and tst --> 
                                        <input type="hidden" name="__post__" value="<?=  base64_encode(json_encode($_GET))?>" />
                                        <input type="hidden" name="owner" value="<?=(isset($_GET['owner']) ? $_GET['owner'] : 'tst')?>" />
                                        <!-- ending implementation for jac travel and tst --> 
                                    </td>
                                </tr>
                                <thead>                
                                    <tr class="bg-info">
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
                                        
                                        /*** MANDATORY FIELD ***/
                                        /* for jac travel and tst **/
                                        
                                        //used to unique identifier
                                        $roomIdentifier = (int)$r->Seq;
                                        $roomData = array(
                                            "ID" => (int)$r->ID,
                                            "Seq" => (int)$r->Seq,
                                            "BookingToken" => (string)$r->BookingToken,
                                            "MealBasisID" => (int)$r->MealBasisID,
                                            "Adults" => (int)$r->Max_Adults,
                                            "Children" => (int)$r->Max_Children,
                                            "Infants" => (int)$r->Max_Infants,
                                            "SubTotal" => (float)$r->SubTotal,
                                            "Discount" => (float)$r->Discount,
                                            "Total" => (float)$r->Total,
                                            "Name" => (string)$r->Name,
                                            "MealBasis" => (string)$r->MealBasis,
                                            "Quantity" => 1,
                                            "Min_Room_Price" => (isset($r->Min_Room_Price) && (float)$r->Min_Room_Price > (float)$r->Tariff) ? (float)$r->Min_Room_Price : (float)$r->Tariff,
                                            "Tariff" => (float)$r->Tariff,
                                            "Invoice_Currency" => (string)$Invoice_Currency
                                        );
                                        
                                        //we will put this room_data in hidden field
                                        $roomBookingData = base64_encode(json_encode($roomData));
                                        /** end room data section for jac travel and tst **/
                                        /*** MANDATORY FIELD ***/
                                        
                                        ?>    
                                        <tr>
                                            <td>
                                                <img class="img-responsive room-thumb" style="width: 100px" alt="<?= $r->Name ?>" src="<?= $r->Image ?>" />
                                                <b><?= $r->Name ?></b> <br />
                                                <?= (int) $r->Quantity - (int) $r->Booked_Quantity ?> Rooms Left
                                            </td>
                                            <td>
                                                <?php
                                                $paxToolTipText = "{$r->Max_Adults} adult(s) and {$r->Max_Children} children are allowed";
                                                ?>
                                                <?= ((int)$r->Max_Adults + (int)$r->Max_Children) ?> 
                                                <?php if((int)$r->Max_Infants > 0): ?>
                                                    AND <?=(int)$r->Max_Infants?> infants Allowed
                                                <?php endif; ?>
                                                x <i class="glyphicon glyphicon-user" data-toggle="tooltip" data-placement="top" title="<?= addslashes($paxToolTipText) ?>"></i>
                                            </td>
                                            <td>
                                                Children will cost <?=$Invoice_Currency?> <?= $r->Price_Per_Child ?> each
                                            </td>
                                            <td>
                                                <select class="form-control input-sm" data-rid="<?=$roomIdentifier?>" id="max_children_<?=$roomIdentifier?>" name="max_children[<?=$roomIdentifier?>]" onchange="app.calculateTotalPrice(<?=$roomIdentifier?>);">
                                                <?php 
                                                $cCount = 0;
                                                $cChild = (int)$r->Max_Children;
                                                while ($cCount <= $cChild) {
                                                    ?>
                                                    <option class="<?=$cCount?>"><?=$cCount?></option>
                                                    <?php
                                                    $cCount++;
                                                }
                                                ?>
                                                </select>
                                            </td>
                                            <td>
                                                <b><?=$Invoice_Currency?> <?= ((float) $r->Min_Room_Price <= 0) ? $r->Tariff : (float) $r->Min_Room_Price ?></b><br />
                                                <small>8% vat included</small>
                                            </td>
                                            <td>
                                                <select class="form-control input-sm" data-rid="<?= $roomIdentifier ?>" name="quantity[<?= $roomIdentifier ?>]" id="quantity_<?= $roomIdentifier ?>" onchange="app.calculateTotalPrice(<?= $roomIdentifier ?>, true);">
                                                    <?php
                                                    $rCount = (int) $r->Quantity - (int) $r->Booked_Quantity;
                                                    $ic = 0;
                                                    while ($ic <= $rCount) {
                                                        ?>
                                                        <option class="<?= $ic ?>"><?= $ic ?></option>
                                                        <?php
                                                        $ic++;
                                                    }
                                                    ?>
                                                </select>
                                                <input type="hidden" value="<?=$roomIdentifier?>" id="room_id" name="room_id[]" />
                                                
                                                <!-- MANDATORY FIELD FOR JAC TRAVEL AND TST -->
                                                <input type="hidden" name="room_identifier" id="room_identifier" value="<?=$roomIdentifier?>" />
                                                <input type="hidden" value="<?=$roomBookingData?>" id="room_data" name="room[<?=$roomIdentifier?>]" />
                                                <!-- MANDATORY FIELD FOR JAC TRAVEL AND TST -->
                                                
                                            </td>
                                            <?php
                                            if ($tcount == 1):
                                                $tcount++;
                                                ?>
                                                <td rowspan="<?= count($hotelRooms->item) + (count($hotelRooms->item)*2) ?>" style="vertical-align: middle">
                                                    <span id="total_order_price"></span>  
                                                    <input type="hidden" value="" name="order_total" id="order_total" />
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                        <tr class="bg-success">
                                            <th colspan="6"><b>Important Data</b></th>                                            
                                        </tr>
                                        <tr class="bg-info">
                                            <td colspan="6" style="font-size:11px">                                                
                                                <?php
                                                echo htmlspecialchars_decode((string)$r->Important_Data);
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
                         if(count($rooms)) {    
                             $roomno = 1;
                            foreach($rooms as $r){                                                           
                         ?>
                        <div class="col-sm-12 col-xs-12 col-md-12" id="guestInformationContainer-<?=((int)$r->Seq)?>">
                            <h3 class="page-title-underlined">Room <?=$roomno++?>: <?=$r->Name?></h3>
                            <small>
                                Please tell us the name of the guest staying at the hotel as it appears on the ID that they’ll present at check out. If the guest has more than one last name, please enter them all.
                            </small>
                            <div class="row">                                
                                <div class="col-sm-12 col-md-12 col-xs-12" id="guestInformationDiv-<?=((int)$r->Seq)?>">
                                </div>
                            </div>
                        </div>    
                        <?php
                            }
                         }
                        ?>
                        <button class="btn btn-primary btn-lg hidden" id='hotelReserveButton' onclick="return app.saveBooking(this);">
                            Reserve
                        </button>
                    </form>
                </div>
                
                <textarea style="width: 100%;margin-top: 25px;" rows="10"><?= $request['data'] ?></textarea>
            </div>
            
            <script type="htm-template" id="guestInfoTemplate">
            <div class='row' id='guestRow'>                
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group">
                        <label>Guest Title {{counter}}</label>
                        <input type="text" class="form-control input-sm required" name="guest_name[title][{{rid}}][{{counter}}]" id="guest_name_{{rid}}_{{counter}}">
                        <small>Please give us the title of one of the people staying in this room.</small>
                    </div>    
                </div>
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group">
                        <label>Guest First Name {{counter}}</label>
                        <input type="text" class="form-control input-sm required" name="guest_name[first_name][{{rid}}][{{counter}}]" id="guest_name_{{rid}}_{{counter}}">
                        <small>Please give us the fitst name of one of the people staying in this room.</small>
                    </div>    
                </div>
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group">
                        <label>Guest Last Name {{counter}}</label>
                        <input type="text" class="form-control input-sm required" name="guest_name[last_name][{{rid}}][{{counter}}]" id="guest_name_{{rid}}_{{counter}}">
                        <small>Please give us the last name of one of the people staying in this room.</small>
                    </div>    
                </div>
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group">
                        <label>Guest Age {{counter}}</label>
                        <input type="number" class="form-control input-sm required" name="guest_name[age][{{rid}}][{{counter}}]" id="guest_name_{{rid}}_{{counter}}">
                        <small>Please give us the age of one of the people staying in this room.</small>
                    </div>    
                </div>
            </div>
            </script>
            
            <script>
                var app = app || {};
                app.hotelInfo = <?= json_encode($hotelInfo) ?>;
                app.rooms = <?= json_encode($rooms) ?>;
                app.roomlist = [];
                app.currency = '<?=$Invoice_Currency?>';
                app.availableRoomQuantity = '<?= $availableRoomQuantity ?>';
                app.hotelInfoSearchCriteria = <?=json_encode($params)?>;
                
                $(document).ready(function(e){
                    app.initHotelInfo();
                });
                
                String.prototype.replaceAll = function(search, replacement) {
                    var target = this;
                    return target.split(search).join(replacement);
                };

                app.renderGuestInformation = function($roomid, $quantity){        
                    var $counter = ($quantity > 0) ? 1 : 0;

                    var $guestHtml = "";
                    var $guestEL = [];
                    if($quantity > 0){
                        while($counter <= $quantity){
                            var $html = $("#guestInfoTemplate").html();
                            $html = $html.replaceAll("{{rid}}", $roomid);
                            $html = $html.replaceAll("{{counter}}", $counter);
                            
                            $guestHtml += $html;
                            $counter++;
                        }
                    }
                    
                    $("#guestInformationDiv-"+$roomid).html($guestHtml);
                    console.log("room -",$roomid, " :: " ,$guestEL);
                    
                    if($quantity <= 0){
                        $("#guestInformationContainer-"+$roomid).hide();                        
                    }
                    else {
                        $("#guestInformationContainer-"+$roomid).show();                        
                    }
                    /*$.each(app.guestInformation, function(i,v){
                        console.log("i : "+i + "    ---     v: "+v);
                        $("input#"+i).val(v);
                    });*/
                };
                
                app.initHotelInfo = function(){
                    if(app.parseInt(app.availableRoomQuantity) <= 0 ){
                        $("#hotelReserveButton").remove();
                    }

                    $.each(app.rooms,function(i,r){           
                        app.roomlist[r.Seq] = r;
                        console.log("rid -- ",r.ID, " :: Seq -- ",r.Seq);
                    });

                    $("#check_in_date").val(app.hotelInfoSearchCriteria.search_hotel_checkin);
                    $("#check_out_date").val(app.hotelInfoSearchCriteria.search_hotel_checkout);

                    app.calculateTotalPrice();
                };
                
                app.renderRoomChildren = function ($room, $qty) {                   
                    var $maxchild = app.parseInt($room.Max_Children);
                    var $cCount = 0;

                    var $html = '<div class="row"><div class="col-sm-12 col-xs-12"><div class="form-group"><label>Room ' + ($qty) + '</label>';
                    $html += '<select class="input-sm form-control" data-rid="' + $room.Seq + '" id="max_children_' + $room.ID + '_' + ($cCount + 1) + '" name="max_children[' + $room.ID + '][' + ($cCount + 1) + ']" onchange="app.calculateTotalPrice(' + $room.ID + ');" >';
                    while ($cCount <= $maxchild) {
                        $html += '<option value="' + $cCount + '">' + $cCount + '</option>';
                        $cCount++;
                    }
                    $html += '</select>';
                    $html += '</div></div></div>';

                    return $html;
                };

                app.getChildQuantity = function ($roomid) {
                    var $cQty = 0;
                    $("select[id^=max_children_" + $roomid + "_]").each(function (e) {
                        $cQty += app.parseInt($(this).val());
                    });

                    return $cQty;
                };

                app.calculateTotalPrice = function($roomid){
                    /*$roomid = app.parseInt($roomid);
                    if($roomid <= 0){
                        return false;
                    }*/

                    var $subtotal = 0.00;
                    var $total = 0.00;
                    var $roomQty = 0;

                    $("select[id^=quantity]").each(function(){
                        var $rid = $(this).attr("data-rid");
                        var $qty = app.parseInt($("select#quantity_"+$rid).val());
                        var $childQty = app.parseInt($("select#max_children_"+$rid).val());
                        var $room = app.roomlist[$rid];
                        var $roomPrice = (app.parseFloat($room.Min_Room_Price) <= 0) ? $room.Tariff : $room.Min_Room_Price;
                        if($qty > 0){
                            $subtotal += ($qty * app.parseFloat($roomPrice)) + ($childQty * app.parseFloat($room.Price_Per_Child));
                        }
                        
                        console.log($rid," seq");
                        app.renderGuestInformation($rid, $qty);
                        $roomQty += $qty;
                    });

                    console.log(app.currency+$subtotal);
                    $("#total_order_price").html(app.currency+$subtotal);
                    $("#order_total").val($subtotal);

                    if($roomQty > 0){
                        $("#hotelReserveButton").removeClass("hidden");
                    }
                    else {
                        $("#hotelReserveButton").addClass("hidden");            
                    }
                    return $subtotal;
                };
                
                app.saveBooking = function($this){
                    var $qty = 0;
                    var $guestNameError = [];
                    $("select[id^=quantity]").each(function(){
                        var $rid = app.parseInt($(this).attr('data-rid'));
                        $qty += app.parseInt($(this).val());
                        
                        if($rid > 0){
                            $("input[id^=guest_name_"+$rid+"_]").each(function(e){
                                if($.trim($(this).val()) == ""){
                                    $guestNameError.push("Please provide all the guest names");                                    
                                }
                            });
                        }
                    });
                    
                    if($qty <= 0){
                        alert("Please select a room")
                        return false;
                    }
                    
                    if($guestNameError.length > 0){
                        alert("Please provide all the guest names");
                        return false;
                    }
                    return true;                   
                };
            </script>
 
            
<?php
    include_once 'footer.php';
} catch (Exception $ex) {
    var_dump($ex->getMessage());
    die();
}