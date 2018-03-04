<?php
include_once '../src/Hotels/Hotels.php';

$result = false;
if(isset($_GET['search_multi'])){
    $createdFrom = $_GET['createdFrom'];
    $createdTo = $_GET['createdTo'];
    
    $arrivalFrom = $_GET['arrivalFrom'];;
    $arrivalTo = $_GET['arrivalTo'];;
    
    if(trim($createdFrom) !="" && trim($createdTo) != ""){
        $params["BookingCreationStartDate"] = $createdFrom;
        $params["BookingCreationEndDate"] = $createdTo;
    }
    
    if(trim($arrivalFrom) !="" && trim($arrivalTo) != ""){
        $params["ArrivalStartDate"] = $arrivalFrom;
        $params["ArrivalEndDate"] = $arrivalTo;
    }    
    
    $Hotels = new Hotels();
    $Hotels->payload = file_get_contents("buyer.xml");
    $Hotels->timeout = 120;

    // Now let's make a request!
    $request = $Hotels->search_booking($params);
    if($request['success'] == false):
        echo $request['msg'];
        die();
    endif;
    
    
    $result = simplexml_load_string($request['data']);  
    //header('Content-Type: text/xml');
    //echo $request->body;
   // die();
   // d($request->body);
}

$bookings = false;
if($result && $result->success == 1):
    $bookings = $result->bookings;
//d($bookings);
endif;

include_once 'header.php';
?>
<div class='container'>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="" style="width: 500px;margin: 0 auto">
                <form id="modSearchForm" class="form" action="multibookingsearch.php" method="get">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label>Booking Creation Start Date</label>
                        <input type="text" readonly="readonly" name="createdFrom" id="createdFrom" class="form-control input-sm" value="<?=isset($_GET['createdFrom']) ? $_GET['createdFrom'] : ""?>" />
                    </div>
                    <div class="form-group col-sm-6 col-xs-12">
                        <label>Booking Creation End Date</label>
                        <input type="text" readonly="readonly" name="createdTo" id="createdTo" class="form-control input-sm" value="<?=isset($_GET['createdTo']) ? $_GET['createdTo'] : ""?>" />
                    </div>
                    
                    <div class="form-group col-sm-6 col-xs-12">
                        <label>Arrival Start Date</label>
                        <input type="text" readonly="readonly" name="arrivalFrom" id="arrivalFrom" class="form-control input-sm" value="<?=isset($_GET['arrivalFrom']) ? $_GET['arrivalFrom'] : ""?>" />
                    </div>
                    
                    <div class="form-group col-sm-6 col-xs-12">
                        <label>Arrival End Date</label>
                        <input type="text" readonly="readonly" name="arrivalTo" id="arrivalTo" class="form-control input-sm" value="<?=isset($_GET['arrivalTo']) ? $_GET['arrivalTo'] : ""?>" />                        
                    </div>                        
                    <input type="hidden" name="search_multi" value="1" />
                    <input type="hidden" name="s_<?=time()?>" value="<?=time()?>" />
                    <div class="form-group col-sm-12 col-xs-12">
                        <button class="btn btn-primary pull-right">
                            Search
                        </button>
                        <button onclick="return app.resetForm();" class="btn btn-success pull-right">
                            Reset
                        </button>
                    </div>
                </form>                
            </div>
        </div>
    </div>
    <?php if($bookings && $bookings->ReturnStatus->Success == "true" ): ?>
    <div class='row'>
        <div class='col-sm-12 col-xs-12'>
            <table class='table table-condensed table-striped' style="font-size:11px">
                <thead>
                    <tr class="bg-primary">
                        <th>#</th>
                        <th>Booking Reference</th>
                        <th>Booking Status</th>
                        <th>BookingDate</th>
                        <th>TradeReference</th>
                        <th>CurrencyCode</th>
                        <th>TotalPrice</th>
                        <th>HotelName</th>
                        <th>LeadGuestName</th>
                        <th>DestinationResort</th>
                        <th>ArrivalDate</th>
                        <th>CustomerTotalPrice</th>
                        <th>CustomerTotalCommission</th>
                        <th>No. of rooms</th>
                    </tr>                    
                </thead>
            <?php
            $si = 1;
            foreach($bookings->Bookings->Booking as $o): 
            ?>
                <tr>
                    <td><?=$si++?></td>
                    <td><?=(string)$o->BookingReference?></td>
                    <td><?=(string)$o->BookingStatus?></td>
                    <td><?=(string)$o->BookingDate?></td>
                    <td><?=(string)$o->TradeReference?></td>
                    <td><?=(string)$o->CurrencyCode?></td>
                    <td><?=(float)$o->TotalPrice?></td>
                    <td><?=(string)$o->HotelName?></td>
                    <td><?=(string)$o->LeadGuestName?></td>
                    <td><?=(string)$o->DestinationResort?></td>
                    <td><?=(string)$o->ArrivalDate?></td>
                    <td><?=(float)$o->CustomerTotalPrice?></td>
                    <td><?=(float)$o->CustomerTotalCommission?></td>
                    
                    <td>
                    <?php
                        if(isset($o->Rooms->Room)):
                            echo count($o->Rooms->Room);
                        endif;
                    ?>
                    </td>                    
                </tr>
            <?php endforeach; ?>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <?php if($bookings): ?>
            <?=(string)$bookings->ReturnStatus->Exception?>
            <?php endif;?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    var app = app || {};
    
    app.resetForm = function(){
        $("#createdFrom,#createdTo, #arrivalFrom, #arrivalTo").val("");
        return false;
    };
    app.getMinDate = function ($id) {
        var date = $("#"+$id).datepicker('getDate');
        if (date) {
            date.setDate(date.getDate() + 7);
        }

        return date;
    };
    $(document).ready(function(e){
        $("#createdFrom").datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function (dateText, inst) {
                var date = $(this).datepicker('getDate');
                
                if (date) {
                    date.setDate(date.getDate() + 7);
                }

              //  $("#createdTo").datepicker("option", "minDate", date);
            }
        });
        
        $("#createdTo").datepicker({
            dateFormat: 'yy-mm-dd',
            //maxDate: app.getMinDate("createdFrom"),
            beforeShowDay: function (date) {
                if ($("#createdFrom").val() == "" || $("#createdFrom").val() == undefined) {
                    return[false];
                }
                return[true];
            }
        });
        
        $("#arrivalFrom").datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function (dateText, inst) {
                var date = $(this).datepicker('getDate');
                
                if (date) {
                    date.setDate(date.getDate() + 7);
                }

                //$("#arrivalTo").datepicker("option", "minDate", date);
            }
        });
        
        $("#arrivalTo").datepicker({
            dateFormat: 'yy-mm-dd',
            //maxDate: app.getMinDate("arrivalFrom"),
            beforeShowDay: function (date) {
                if ($("#arrivalFrom").val() == "" || $("#arrivalFrom").val() == undefined) {
                    return[false];
                }
                return[true];
            }
        });
        
        $("#modSearchForm").validate({
            submitHandler:function(form){
                if(
                        ($("#arrivalFrom").val() == "" || $("#arrivalTo").val() == "")
                        &&
                        ($("#createdFrom").val() == "" ||  $("#createdTo").val() == "")
                ){
                    alert("Please select either of both the booking dates or arrival dates");
                    return false
                }
               
                return true;
            }
        });
    });
</script>

<?php
    include_once 'footer.php';
?>
