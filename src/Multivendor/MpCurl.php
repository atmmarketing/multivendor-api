<?php
namespace Multivendor;

class MpCurl{

    protected $access_token;

    const _MP_ROOT_URL_ = "https://shopify.webkul.com/shopify-marketplace-api/web";

    protected function callCurl($method , $api_path ,  $query = false , $data = false ){
     
        $ch = curl_init();

		if(is_array($query)) $query = http_build_query($query);
		if(is_array($data)) {
			$data = json_encode($data);
			//echo print_r($data,1); 
			//die();
		}
		
        #set url 
        if(!empty($query))
            $api_path.= '?' . $query;

        curl_setopt($ch, CURLOPT_URL, self::_MP_ROOT_URL_.$api_path);

        #set defaults
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        #set header 
        $headers = array();
        $headers[] = "Accept: application/json";
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer ".$this->access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        #set method and data
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        else{
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        if($method == 'POST' || $method == 'PUT'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
        }

        #execute
        $result = curl_exec($ch);
        
        #handle error response 
        if (curl_errno($ch)) {
            return json_encode(array('code'=>403 , 'error'=>'Curl Error' , 'message'=>curl_error($ch))) ;
        }

        #close curl
        curl_close ($ch);

        #return response 
        return $result;

    }

}
