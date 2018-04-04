<?php
//define("hostname", "http://localhost/tstnew/");
define("hostname", "http://toursreservation.com/");

$_currency_rates = array();
function convert_currency($amount, $baseCurrency, $convertTo){
    $baseCurrency = isset($_currency_rates['currencies'][$baseCurrency]) ? 
                        $_currency_rates['currencies'][$baseCurrency]->Code : '';
    
    $convertTo = isset($_currency_rates['currencies'][$convertTo]) ? 
                        $_currency_rates['currencies'][$convertTo]->Code : '';
    
    $symbol = strtoupper($baseCurrency)."_".strtoupper($convertTo);
        
    $rate = isset($_currency_rates['currency_rates'][$symbol])
                ? (float)$_currency_rates['currency_rates'][$symbol] : 1;


    return number_format((float)$amount * $rate, 2 , ".", "");
}

function get_currency_rates($currency_rates_xml_array){
    $rates = array();
    foreach($currency_rates_xml_array->item as $r){
        $rates[(string)$r->Title] = (float)$r->Rate;
    }
    return $rates;
}

function set_currency_symbols($currencies_rates)
{
    global $_currency_rates;
    $_currency_rates = json_decode(rtrim(ltrim($currencies_rates, "<![CDATA["), "]]>"),true);    
    //d($_currency_rates);
}

function d($var, $die = true, $header = null){    
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    if($die):
        die();
    endif;
}