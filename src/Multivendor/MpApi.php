<?php
namespace Multivendor;


class MpApi extends MpCurl{
	var $access_token;
	var $refresh_token;
	var $client_id;
	var $client_secret;
	
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	/**
	* Rate limits API calls so we don't hit Shopify's rate limiter.
	* @var bool
	*/
	public $rate_limit = TRUE;
	
	public function __construct($access_token,$refresh_token,$client_id,$client_secret){
		$this->access_token		= $access_token;
		$this->refresh_token	= $refresh_token;
		$this->client_id		= $client_id;
		$this->client_secret	= $client_secret;
		// check if valid and get new one if not
		//$this->checkToken();
	}
	public function sleep($seconds=2){
		sleep($seconds);//jebus cripes
	}
	/*
	returns a single product details
	$params['id'] (str) : the product ID
	*/
	function getProduct($params = array()){
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::GET , '/multivendor/product/id/'.(int)$params['id'].'.json'),true);
	}
	/*
	returns products details . if filters are not set then by default returns last 50 product details in descending order
	$params['limit'] (int) : 
	$params['offset'] (int) : 
	*/
	function getProducts($params = array()){
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::GET , '/multivendor/product.json' , $params ),true);
	}
	/*
	returns count of all products
	$params['type'] (str) : if type is set to 'active' returns count of all active product , if it is 'inactive' then return count of all inactive prodcts by default it returns count of all products

	*/
	function getProductCount($params = array()){
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::GET , '/multivendor/product/count.json' , $params ),true);
	}
	/*
	creates a new product
	$params['data'] (arr or str) : either an array or a JSON string
	*/
	function createProduct($params = array()){
		
		//echo json_encode($params['data']); die();
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::POST , '/multivendor/product/create/product.json' , false, $params['data'] ),true);
	}
	
	/*
	update inventory of product by id , use this api to update normal product only
	$params['data'] (arr or str) : either an array or a JSON string
	
	{
	    "product_id": (str/int),
	    ...
	    [ more product properties ]
	    "variants": [
	        {
	            "variant_id": (str/int),
	            ...
	            [ more variant properties ]
	        },
    }
	
	*/
	function updateProduct($params = array()){
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::PUT , '/multivendor/inventory/inventory.json', false, $params['data'] ),true);
	}
	
	
	
	/*
	check token is expired
	call this outside the object to obtain the new token
	returns true if not expired
	returns access token (str) if new
	returns false if new attempt was unsuccessful
	$params[''] () : 
	*/
	function checkToken($params = array()){
		extract($params);
		$r = false;
		//generally does not need sleep as it's the first API call 
		$response = $this->getSellerCount();
		//print_r($response);die();
		
		if( (isset($response['error']) AND $response['error'] != '') OR (isset($response['errors']) AND $response['errors'] != '')){
			// !new token
			$r = $this->newToken(); 

// 		print_r($response); 
// 		die('p');

			
			//if($r == '' OR $r === true OR $r === false) $r = $this->newToken();
			
			//if($r !== '' AND $r !== true AND $r !== false){
				// new access token
				//echo 'Notice: NEW access token was generated ' . $r;
				//die();
			//}
			
		}elseif(isset($response['total_sellers'])){
			// !existing access token
			$r = true;
		}
		
		
		return $r;
	}

	/*
	obtains new access token
	*/
	function newToken($params = array()){
		$data =	[
			"client_id"=>$this->client_id, 
			"client_secret"=> $this->client_secret,
			"refresh_token"=> $this->refresh_token
			];
		
		//echo json_encode($data), PHP_EOL; //die();
		$r = false;
		MpApi::sleep(); 
		$response = json_decode($this->callCurl('POST' , '/new/token' , false, $data ),true);
		
		//print_r($response); die('pp');
		
		if(isset($response['access_token']) AND $response['access_token'] != ''){
			$this->access_token = $response['access_token'];
			$this->refresh_token = $response['refresh_token'];
			//echo '<!-- New tokens generated: ' . print_r($response,1) . '-->';
			$r = $response['access_token'];
		}
		
		
		
		
		return $r;
	}
	
	/*
	returns a single seller details
	$params['id'] (str) : the ID
	*/
	function getSeller($params = array()){
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::GET , '/multivendor/seller/id/'.(int)$params['id'].'.json'),true);
	}
		
	/*
	returns count of all sellers
	$params['type'] (str) : if type is set to 'active' returns count of all active sellers , if it is 'inactive' then return count of all inactive sellers; by default it returns count of all sellers

	*/
	function getSellerCount($params = array()){
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::GET , '/multivendor/seller/count.json' , $params ),true);
	}
	/*
	create a new seller
	$params['data'] (arr or str) : either an array or a JSON string
	*/
	function createSeller($params = array()){
		
		//echo json_encode($params['data']); die();
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::POST , '/multivendor/seller/seller.json' , false, $params['data'] ),true);
	}
		
	/*
	returns a single seller's products
	$params['seller_id'] (str) : the ID
	$params['limit'] (str) : total limit
	$params['offset'] (str) : rows to skip
	*/
	function getSellerProducts($params = array()){
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::GET , '/multivendor/seller/'.(int)$params['seller_id'].'/product.json',$params),true);
	}
		
	/*
	returns a single seller's orders
	$params['seller_id'] (str) : the ID
	*/
	function getSellerOrders($params = array()){
		MpApi::sleep(); 
		return json_decode($this->callCurl(MpApi::GET , '/multivendor/seller/'.(int)$params['seller_id'].'/orders.json',$params),true);
	}

  	/**
	 * [getResponse returns response for api ]
	 * @param  [string]  $method [http request method e.g. GET , POST , PUT ]
     * @param  [string]  $api_path [api path according doc , e.g.   '/multivendor/product/create/product.json']
     * @param  [string]  $query [extra query strting with ?  e.g. '?limit=5&offset=30']
     * @param  [string]  $data [json encoded data with parameters specified in api reference page of app 
	 * @return [string]       [returns response]
	 */
	 public function getResponse($method , $api_path , $query = false , $data = false){
		MpApi::sleep(); 
		return json_decode($this->callCurl($method , $api_path , $query ,  $data),true);
	}
}
