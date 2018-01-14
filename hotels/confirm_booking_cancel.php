<?php
include_once '../src/Hotels/Hotels.php';

$Hotels = new Hotels();
$Hotels->payload = file_get_contents("buyer.xml");
$Hotels->timeout = 120;


$params['order_id'] = $_POST['order_id'];
$params['CancellationCost'] = $_POST['CancellationCost'];
$params['CancellationToken'] = $_POST['CancellationToken'];

// Now let's make a request!
$request = $Hotels->confirm_cancel($params);

if($request['success'] == false):
    echo $request['msg'];
    die();
endif;

$result = simplexml_load_string($request['data']);

if((int)$result->success == 0):
    $json = array(
        "success" => false,
        "msg" => (string)$result->msg,
        "r" => $result
    );
    echo json_encode($json);
    die();
endif;

$json = array(
    "success" => true,
    "msg" => "The booking was successfully canceled"
);
echo json_encode($json);
die();