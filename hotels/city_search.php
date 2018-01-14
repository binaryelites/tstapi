<?php
include_once '../src/Hotels/Hotels.php';

$Hotels = new Hotels();
$Hotels->payload = file_get_contents("buyer.xml");
$Hotels->timeout = 90;

$request = $Hotels->search_city($_GET);

$cities = array();
if($request['success'] == false):
    echo json_encode($cities);
    die();
endif;

$cityResult = simplexml_load_string($request['data']);

//echo "<pre>";
if(isset($cityResult->cities->item)){
    
    $cityList = $cityResult->cities;    
    foreach($cityList->item as $r){        
        $cities[] = array(
            "ID" => (int)$r->ID,
            "City_Name" => (string)$r->City_Name,
            "Jac_Country_Name" => (string)$r->Jac_Country_Name,
            "Jac_City_Name" => (string)$r->Jac_City_Name,
            "Jac_City_ID" => (int)$r->Jac_City_ID,
            "Name" => (string)$r->Name
        );
    }
}

echo json_encode($cities);
die();