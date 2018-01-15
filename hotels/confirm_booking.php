<?php
include_once '../src/Hotels/Hotels.php';

$Hotels = new Hotels();
$Hotels->payload = file_get_contents("buyer.xml");
$Hotels->timeout = 120;

$post = $_POST;
$params = array(    
    "order_id" => $post['order_id'],
    "guest" => $post['guest'],
    "__post__" => $post['__post__']
);

// Now let's make a request!
$request = $Hotels->confirm_booking($params);

try {    
    $result = simplexml_load_string($request['data']);
    echo json_encode($result);
    die();
}
catch(Exception $ex){
    $json['success'] = false;
    $json['msg'] = $ex->getMessage();
}

?>