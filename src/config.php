<?php
define("hostname", "http://localhost/tstnew/");
//define("hostname", "http://travelshoptours.com/apiv2/");
function convert_currency($amount, $baseCurrency, $convertTo, $rates){
    $symbol = strtoupper($baseCurrency)."_".strtoupper($convertTo);
        
    $rate = isset($rates[$symbol])
                ? (float)$rates[$symbol] : 1;


    return number_format((float)$amount * $rate, 2 , ".", "");
}

function get_currency_rates($currency_rates_xml_array){
    $rates = array();
    foreach($currency_rates_xml_array->item as $r){
        $rates[(string)$r->Title] = (float)$r->Rate;
    }
    return $rates;
}

function d($var, $die = true, $header = null){    
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    if($die):
        die();
    endif;
}