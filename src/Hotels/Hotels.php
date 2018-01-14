<?php

/**
 * Description of Hotels
 * Search, Show, Book and Cancel Hotel Bookings
 * @author Arif Majid <arif_avi@hotmail.com>
 */

class Hotels 
{
    private $api_url = 'http://travelshoptours.com/';
    private $api_end_point = 'api/xml/hotels/';
    private $request_url = null;
    var $payload = null;
    var $timeout = 120;
    
    
    public function __construct() 
    {
        
        include_once '../src/config.php';
        
        include_once("../src/libraries/Requests.php");
        Requests::register_autoloader();
    }
    
    function get_url($route = '')
    {
        return $this->api_url = hostname . $this->api_end_point . $route;
    }
    
    function make_request($data = array(), $options = array(), $headers = array())
    {
        if($this->payload == NULL):
            throw new Exception("No payload has been set");
        endif;
        
        $data['__payload__'] = $this->payload;
        $options['timeout'] = $this->timeout;
        
        try 
        {
            $request = Requests::post($this->request_url, $headers, $data, $options);
            if($request->success == false):
                return array(
                    "success" => FALSE,
                    "msg" => "you request could not be processed, status_code::".$request->status_code,
                    "body" => $request->body,
                    "requestObject" => $request
                );
            endif;
            
            return array(
                "success" => true,
                "data" => $request->body,
                "requestObject" => $request
            );
        }
        catch(Exception $ex)
        {
            return array(
                "success" => false,
                "msg" => $ex->getMessage()
            );
        }
    }
    
    function search_city($params = array())
    {
        $this->request_url = $this->get_url('get_cities_combined')."?".http_build_query($params);
        return $this->make_request();
    }
    
    function search($params = array())
    {
        $this->request_url = $this->get_url('search_combined');
        return $this->make_request($params);        
    }
    
    function get_hotel_info($params = array())
    {
        $this->request_url = $this->get_url('hotel_info')."?".http_build_query($params);
        return $this->make_request();
    }
    
    function book_hotel($params = array())
    {
        $this->request_url = $this->get_url('save_order');
        return $this->make_request($params);        
    }
    
    function confirm_booking($params = array())
    {
        $this->request_url = $this->get_url('confirm_booking');
        return $this->make_request($params);        
    }
    
    function cancel_booking($params = array())
    {
        $this->request_url = $this->get_url('pre_cancel_order');
        return $this->make_request($params);        
    }
    
    function confirm_cancel($params = array())
    {
        $this->request_url = $this->get_url('cancel_order');
        return $this->make_request($params);        
    }
    
    function get_order_list($params = array())
    {
        $this->request_url = $this->get_url('order_list')."?".http_build_query($params);
        return $this->make_request();        
    }
    
    function get_order_info($params = array())
    {
        $this->request_url = $this->get_url('get_order_info')."?".http_build_query($params);
        return $this->make_request();
    }
}
