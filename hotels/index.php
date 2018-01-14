<?php
include_once("header.php");
?>
<div style="margin: 0 auto; width: 512px">    
    <h3>Search Hotels</h3>
    <form  action="search.php" method="post" id="modHotelSearchForm" >
        <h3 class="hotel-landing-search-title hidden">Hotel Search</h3>        
        <div class="form-group">  
            <label for="search_hotel_name">City</label>
            <input type="text" class="form-control input-sm" name="search_hotel_name" id="search_hotel_name" placeholder="City">
            <input type="hidden" required value="" name="city_id" id="city_id" />
            <input type="hidden" required value="" name="jac_city_id" id="jac_city_id" />
            <small>City (autocomplete) from TST and JacTravel will be displayed</small>            
        </div>

        <div class="row">
            <div class="col-sm-5 col-xs-5">
                <div class="form-group">                
                    <label for="search_hotel_checkin">
                        <i class="glyphicon glyphicon-calendar"></i> Check In
                    </label>
                    <input type="text" class="form-control input-sm required" data-placement="left" name="search_hotel_checkin" id="search_hotel_checkin" />
                </div>
            </div>
            <div class="col-sm-5 col-xs-5">
                <div class="form-group">                
                    <label for="search_hotel_checkout">
                        <i class="glyphicon glyphicon-calendar"></i> Check Out 
                    </label>
                    <input type="text" class="form-control input-sm required" data-popover-position="top" name="search_hotel_checkout" id="search_hotel_checkout" />
                </div>
            </div>
            <div class="col-sm-2 col-xs-2 text-center" id="nights-column-homes">
                <div class="form-group">   
                    <label><span class="widget-query-nights-label">Nights</span></label>
                    <span id="nights" class="widget-query-nights">                                    
                        <span class="label label-primary" id="number-of-nights-home"></span> 
                        <i class="glyphicon glyphicon-lamp"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="room-container-homes">
            <div class="row">
                <div class="col-sm-4 col-xs-12">
                    <div class="form-group">                
                        <label for="search_hotel_room">Rooms</label>
                        <select class="form-control input-sm required" name="search_hotel_room_count" id="search_hotel_room_count" onchange="app.renderRooms();"> 
                            <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-8 col-xs-12 room-container-home">
                    <div class="row">                    
                        <div class="col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12" id="adultdiv">                                                                 
                                </div>           
                            </div>


                            <div class="form-group hidden">                
                                <label for="search_hotel_adult_count">Adults</label>
                                <select class="form-control input-sm required" name="search_hotel_adult_count" id="search_hotel_adult_count" onchange="app.renderRooms();">
                                    <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                                    <option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option>
                                    <option value="9">9</option><option value="10">10</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6 hidden">
                            <div class="form-group">                
                                <label for="search_hotel_child_count">Children</label>
                                <select class="form-control input-sm" name="search_hotel_child_count" id="search_hotel_child_count" onchange="app.toggleChildAge()">    
                                    <?php
                                    $search_hotel_child_count = 0;
                                    while ($search_hotel_child_count < 10) {
                                        ?>
                                        <option value="<?= $search_hotel_child_count ?>"><?= $search_hotel_child_count ?></option>
                                        <?php
                                        $search_hotel_child_count++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>  
                        <div class="col-sm-12 col-xs-12">
                            <div id="search_hotel_child_age_div" style="display:none" class="row"></div>
                        </div> 
                    </div>

                </div>
            </div>    

        </div>
        <input type="hidden" value="" name="search_hotel_type" id="search_hotel_type" />
        <input type="hidden" value="" name="search_hotel_type_id" id="search_hotel_type_id" />

        <button class="btn btn-primary btn-block">
            <i class="glyphicon glyphicon-search"></i> Search
        </button>
    </form>

    <script>
        var app = app || {};
        app.hotelSearchParams = <?= isset($hotelSearchParams) ? json_encode($hotelSearchParams) : "[]" ?>;
        app.hotelSearchDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        app.hotelSearchInit = function () {
            if (app.hotelSearchParams.search_hotel_room_count != undefined) {
                app.populateHotelSearchForm();
            }
            else {
                $("#search_hotel_room_count").val(1);
                app.renderRooms();
            }

            $("#search_hotel_checkin").datepicker({
                dateFormat: 'dd-mm-yy',
                minDate: 1,
                onSelect: function (dateText, inst) {
                    var date = $(this).datepicker('getDate');
                    var dayOfWeek = app.hotelSearchDays[date.getDay()];
                    $(this).next().find('span').html(dayOfWeek);

                    if (date) {
                        date.setDate(date.getDate() + 1);
                    }

                    $("#search_hotel_checkout").datepicker("option", "minDate", date);

                    app.calculateSearchNights();
                    $(this).tooltip('hide').removeClass("error");
                }
            });

            $("#search_hotel_checkout").datepicker({
                dateFormat: 'dd-mm-yy',
                minDate: app.getMinCheckoutDate(),
                beforeShowDay: function (date) {
                    if ($("#search_hotel_checkin").val() == "" || $("#search_hotel_checkin").val() == undefined) {
                        return[false];
                    }
                    return[true];
                },
                onSelect: function (dateText, inst) {
                    var date = $(this).datepicker('getDate');
                    var dayOfWeek = app.hotelSearchDays[date.getDay()];
                    $(this).next().find('span').html(dayOfWeek);
                    app.calculateSearchNights();
                    $(this).tooltip('hide').removeClass("error");
                }
            });

            $("#search_hotel_name").on("keypress", function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                }
            });

            $("#search_hotel_name").autocomplete({
                html: true,
                focusOpen: false,
                source: function (request, response) {
                    $.ajax({
                        url: app.baseUrl + 'city_search.php?name=' + request.term,
                        type: 'get',
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            response(
                                    $.map(data, function (el) {
                                        return {
                                            label: el.Name,
                                            value: el.Name,
                                            obj: el
                                        };
                                    })
                                    );
                        },
                        error: function (data) {
                            console.log(data)
                        }
                    });

                },
                minLength: 2,
                select: function (event, ui) {
                    if (ui.item) {
                        $("#city_id").val(ui.item.obj.ID);
                        $("#jac_city_id").val(ui.item.obj.Jac_City_ID);
                    }
                    else {
                        $("#city_id").val("");
                        $("#jac_city_id").val("");
                    }
                },
                change: function (event, ui) {
                    if (ui.item) {
                        $("#city_id").val(ui.item.obj.ID);
                        $("#jac_city_id").val(ui.item.obj.Jac_City_ID);
                    }
                    else {
                        $("#city_id").val("");
                        $("#jac_city_id").val("");
                    }
                }
            });

            var $searchCheckinDate = $.trim(app.hotelSearchParams["search_hotel_checkin"]);
            var $searchCheckoutDate = $.trim(app.hotelSearchParams["search_hotel_checkout"]);

            if ($searchCheckinDate == undefined || $searchCheckinDate == "") {
                $("#search_hotel_checkin").val(app.getFutureDates(1));
            }

            if ($searchCheckoutDate == undefined || $searchCheckoutDate == "") {
                $("#search_hotel_checkout").val(app.getFutureDates(2));
            }

            app.calculateSearchNights();
        };

        app.getFutureDates = function (daysToAdd) {
            daysToAdd = (daysToAdd == undefined) ? 1 : daysToAdd;
            var myDate = new Date();
            myDate.setDate(myDate.getDate() + daysToAdd);
            // format a date
            var dt = myDate.getDate() + '-' + ("0" + (myDate.getMonth() + 1)).slice(-2) + '-' + myDate.getFullYear();
            return dt;
        };

        app.getMinCheckoutDate = function () {
            var date = $("#search_hotel_checkin").datepicker('getDate');
            if (date) {
                date.setDate(date.getDate() + 1);
            }

            return date;
        };

        app.populateHotelSearchForm = function () {
            for (s in app.hotelSearchParams) {
                $("#" + s).val(app.hotelSearchParams[s]);
                if (s == "search_hotel_child_count") {
                    app.toggleChildAge();
                    var $ChildCount = app.parseInt($("#search_hotel_child_count").val());
                    var $ii = 1;
                    if ($ChildCount > 0) {
                        while ($ii <= $ChildCount) {
                            $("#chil_age_" + $ii).val(app.hotelSearchParams["child_age_" + $ii]);
                            console.log("child age " + $ii + " -- " + app.hotelSearchParams["child_age_" + $ii]);
                            $ii++;
                        }
                    }
                }
            }
        };

        app.renderRooms = function () {
            console.log("rendering room");

            var $RoomCount = app.parseInt($("#search_hotel_room_count").val());
            var $AdultCount = app.parseInt($("#search_hotel_adult_count").val());
            if ($RoomCount > 0 && $AdultCount < $RoomCount) {
                $("#search_hotel_adult_count").val($RoomCount);
            }
            app.toggleAdultOptions();

            var html = "";
            var $i = 1;
            while ($i <= $RoomCount) {
                html += app.getAdultList($i);
                $i++;
            }

            $("#adultdiv").html(html);

            return false;

        };

        app.getAdultList = function ($i)
        {


            var $html = "<div class='row' style='border-bottom:1px solid #000'>";

            $html += "<div class='col-sm-6 col-xs-12'>";
            $html += "<div class='form-group'>";
            $html += '<label>Room ' + ($i) + ' Adult</label>';
            $html += '<input type="number" value="1" name="adult[' + $i + ']" id="adult_' + $i + '" class="form-control input-sm" />';
            $html += "</div>";
            $html += "</div>";

            $html += "<div class='col-sm-6 col-xs-12'>";
            $html += "<div class='form-group'>";
            $html += '<label>Room ' + ($i) + ' Children</label>';
            $html += '<select name="children[' + $i + ']" id="children_' + $i + '" class="form-control input-sm" onchange="app.showChildAge(' + $i + ')" >';
            $html += app.getItemList();
            $html += '</select>';
            $html += "</div>";
            $html += "</div>";

            $html += "<div class='col-sm-12 col-xs-12' id='child_age_" + $i + "_div' style='display:none'>";


            $html += "</div>";

            $html += '</div>';
            return $html;
        };

        app.renderChildAgeList = function ($i, $childCount)
        {
            var $html = "<div class='row'>";
            var $s = 0;
            while ($s < $childCount) {
                $html += "<div class='form-group col-sm-6 col-xs-6'>";
                $html += '<label>Child ' + ($s + 1) + ' Age</label>';
                $html += '<select name="child_age[' + $i + '][' + $s + ']" id="child_age_' + $i + '_' + $s + '" class="form-control input-sm" >';
                $html += app.getItemList(17, "");
                $html += '</select>';
                $html += "</div>";
                $s++;
            }

            $html += '</div>';
            return $html;
        };

        app.getItemList = function ($length, $defaultvalue) {
            $length = $length || 20;
            var $html = "";
            var $i = 0;
            if ($defaultvalue != undefined) {
                $html += '<option value="' + $defaultvalue + '">' + $defaultvalue + '</option>';
            }

            while ($i < $length) {
                $html += '<option value="' + $i + '">' + $i + '</option>';
                $i++;
            }
            return $html;
        };

        app.showChildAge = function ($i) {
            var $childCount = $("select#children_" + $i).val();
            if ($childCount > 0) {
                var $html = app.renderChildAgeList($i, $childCount);
                $("#child_age_" + $i + "_div").html($html).show();
                return false;
            }
            //$("select#children_"+$i).val(0);
            $("#child_age_" + $i + "_div").html("").hide();
            return false;
        };

        app.toggleAdultOptions = function () {
            $("#search_hotel_adult_count option").each(function (e) {
                var $RoomCount = app.parseInt($("#search_hotel_room_count").val());
                if (app.parseInt($(this).val()) < $RoomCount) {
                    $(this).css("display", "none");
                }
                else {
                    $(this).css("display", "block");
                }
            });
        };

        app.toggleChildAge = function (domId, adultId) {
            var $val = app.parseInt($("#search_hotel_child_count").val());
            console.log("rendering child age");
            if ($val > 0) {
                var $chtml = '';
                var $ii = 1;
                while ($val > 0) {
                    $chtml += '<div class="col-xs-6 col-sm-6"><div class="form-group" id="search_hotel_child_age_div_' + $ii + '">';
                    $chtml += '<label for="search_hotel_child_age_' + $ii + '">Child ' + $ii + ' Age:</label>';
                    $chtml += '<select class="form-control input-sm required" name="child_age_' + $ii + '" id="child_age_' + $ii + '">';
                    $chtml += '<option selected="selected" value="">?</option value="0"><option>0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option>';
                    $chtml += '</select>';
                    $chtml += '</div></div>';
                    $ii++;
                    $val--;
                }
                $("div#search_hotel_child_age_div").html($chtml).show();
            }
            else {
                $("div#search_hotel_child_age_div").html("").hide();
            }

        };

        app.calculateSearchNights = function () {
            var end_date = $('#search_hotel_checkout').datepicker('getDate'); // assuming the format is correct
            var start_date = $('#search_hotel_checkin').datepicker('getDate'); // assuming the format is correct

            if (start_date != null && end_date != null) {
                var date_diff = dateDiffInDays(start_date, end_date);
                $("#number-of-nights-home").html(date_diff);
            }
        };

        $(document).ready(function (e) {
            app.hotelSearchInit();
            $("#modHotelSearchForm").validate({
                submitHandler: function (form) {

                    var $city_id = app.parseInt($("#city_id").val());
                    var $jac_city_id = app.parseInt($("#jac_city_id").val());

                    if ($city_id <= 0 && $jac_city_id <= 0) {
                        alert("Please select a city from the autocomplete field");
                        return false;
                    }

                    var $totalPassengers = 0;
                    $("input[id^=adult_]").each(function (i, v) {
                        $totalPassengers += app.parseInt($(this).val())
                    });

                    $("select[id^=children_]").each(function (i, v) {
                        $totalPassengers += app.parseInt($(this).val())
                    });
                    console.log("total passengers : ", $totalPassengers);

                    if ($totalPassengers > 9) {
                        alert("A Maximum of 9 passengers are allowed, Please use a different search criteria")
                        return false;
                    }

                    return true;
                }
            });

            $("#search_hotel_checkin, #search_hotel_checkout").on('focus', function (e) {
                $(this).attr("readonly", "readonly");
            });

            $("#search_hotel_checkin, #search_hotel_checkout").on('blur', function (e) {
                $(this).removeAttr("readonly");
            });
        });

        var _MS_PER_DAY = 1000 * 60 * 60 * 24;

        // a and b are javascript Date objects
        function dateDiffInDays(a, b) {
            // Discard the time and time-zone information.
            var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
            var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

            return Math.floor((utc2 - utc1) / _MS_PER_DAY);
        }
        ;

        $(document).ready(function (e) {
            $("#searchForm").submit(function () {

            });
        });

    </script>
</div>

<?php
include_once 'footer.php';
?>