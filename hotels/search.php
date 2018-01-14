<?php
include_once '../src/Hotels/Hotels.php';

$Hotels = new Hotels();
$Hotels->payload = file_get_contents("buyer.xml");
$Hotels->timeout = 120;

$params = $_POST;
// Now let's make a request!
$request = $Hotels->search($params);


if($request['success'] == false):
    echo $request['msg'];
    die();
endif;

include_once 'header.php';
?>
<div class="container">
    <div class="row">            
<?php
// Check what we received
try {
    $result = simplexml_load_string($request['data']);
    $count = count($result->item);
    if($count > 0)
    {
?>
        <div class="col-sm-12 col-xs-12"><h1 class="well text-center"><?=$count?> Result found</h1></div>
        
    <?php
        foreach($result->item as $h)
        {   
    ?>
        <div class="col-sm-12 col-md-12 col-xs-12">
            <div class="row">
                
                <div class="col-sm-3 col-md-3 col-xs-12">
                    <img src="<?=$h->Image_Banner?>" class="img-responsive img-thumbnail" style="max-height: 200px;width: 100%" />                
                </div>
                <div class="col-sm-9 col-md-9 col-xs-12">
                    <h3>
                        <a href="#"><?=$h->Name?> <small class="label label-primary"><?=$h->Owner?></small></a>
                        <small class="pull-right"><?=$h->Customer_Rating?>/5 out of <?=$h->Total_Reviews?></small>
                    </h3>
                    <b><?=$h->City_Name?>, <?=$h->Country_Name?></b><Br />
                    <b>Address :</b> <?=$h->Address?><Br /><Br />
                    <small><?=$h->Description?></small>       <br />

                    <div class="full-width clearfix">
                        <h4 style="background-color: #337ab7; width: 100%;margin-top:5px; margin-bottom: 5px;padding: 10px 5px;color : white">
                            Min Room Price
                        </h4>
                        <table class="table table-condensed table-bordered table-striped" style="font-size: 12px">
                            <tr class="bg-success">
                                <th style="width: 120px;">Room</th>
                                <th>Price Starts With</th>
                            </tr>
                            <tr>
                                <td>
                                    <b><?=$h->Room_Name?></b>
                                </td>
                                <td>
                                    <?=$h->Room_Price?>
                                    
                                    <?php      
                                    /* very important to set this data here */
                                    $params['owner'] = (string)$h->Owner;
                                    $params['prid'] = (string)$h->PRID;
                                    $params['hotel_id'] = (string)$h->ID;
                                    /* end setting data                     */
                                    ?>
                                    <form method="post" action="hotel_info.php?<?=http_build_query($params)?>" target="_blank">                                                                                
                                        <button class="btn btn-success btn-xs pull-right">
                                            <i class="glyphicon glyphicon-check"></i> Book Now
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </table>                    
                    </div>                
                </div>
            </div>
        </div>
    
    <?php
        }
    }
    
    else {
    ?>
        <div class="col-sm-12 col-xs-12">No Results found</div>
    <?php
    }
}
catch (Exception $ex) {
     var_dump($ex->getMessage());
}
?>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <textarea class="form-control" rows="15"><?=$request['data']?></textarea>            
        </div>
    </div>
</div>
<?php
include_once 'footer.php';
?>