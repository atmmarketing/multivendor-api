<?php

namespace Multivendor;

use Requests;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\GrantType\RefreshToken;
use kamermans\OAuth2\OAuth2Subscriber;
use kamermans\OAuth2\OAuth2Middleware;
/**
 * Class MultivendorAPI
 * @package AtmMarketing\MultivendorAPI
 */
class MultivendorAPI {
//https://shopify.webkul.com/shopify-marketplace-api/web/
	private $api_endpoint = 'https://shopify.webkul.com/shopify-marketplace-api/web';
//	private $products_endpoint = 'https://www.sendowl.com/api/v1/products';
//	const PRODUCT_TYPE_DIGITAL = 'digital';
	
	/**
	 * @var string
	 */
	private $access_token;

	/**
	 * @var string
	 */
	private $refresh_token;

	/**
	 * @var string
	 */
	private $client_id;
	/**
	 * @var string
	 */
	private $client_secret;
	/**
	 * @var array|null
	 */
	private $options;
	// guzzle
	private $Client;
	
	/**
	 * MultivendorAPI constructor.
	 *
	 * @param string $key
	 * @param string $secret
	 * @param array|null $options
	 */
	public function __construct( $access_token='', $refresh_token='', $client_id='', $client_secret='',$options = [] ) {
		$this->access_token		= $access_token;
		$this->refresh_token	= $refresh_token;
		$this->client_id		= $client_id;
		$this->client_secret	= $client_secret;
 		$this->options         = $options;
// 		$this->options['auth'] = array( $this->get_key(), $this->get_secret() );
		
		echo 'access_token = ',$this->access_token, ' refresh_token = ', $this->refresh_token, ' client_id = ', $this->client_id, 'client_secret = ', $this->client_secret;
		
		
		$reauth_client = new Client([
		    // URL for access_token request
		    'base_uri' => $this->api_endpoint . '/new/token',
		]);
		$reauth_config = [
		    "client_id" => $this->client_id,
		    "client_secret" => $this->client_secret,
		    "refresh_token" => $this->refresh_token
		    
		];
		$grant_type = new RefreshToken($reauth_client, $reauth_config);
		$oauth = new OAuth2Middleware($grant_type);

		$stack = HandlerStack::create();
		$stack->push($oauth);
		
		
		
		$this->Client = new Client([
			'auth' => 'oauth' ,
			'handler' => $stack
		]);
	}

	/**
	 * Retrieve API Access Token
	 *
	 * @return string
	 */
	public function get_access_token() {
		return $this->access_token;
	}
	/**
	 * Retrieve API Access Token
	 *
	 * @return string
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
	}
	/**
	 * Retrieve API Key
	 *
	 * @return string
	 */
	public function get_client_id() {
		return $this->client_id;
	}

	/**
	 * Retrieve API Secret
	 *
	 * @return string
	 */
	public function get_client_secret() {
		return $this->client_secret;
	}
	/*
	
	$params[''] () : 
	*/
	function refresh_access_token ($params = array()){
		extract($params);



		
		$RefreshClient = new Client([
			'auth' => ['Authorization', 'Bearer ' . $this->refresh_token] 
		]);
		
		$response = $RefreshClient->request('GET', $this->api_endpoint .'/multivendor/product/count.json');
		print_r($response);
	}
	/**
	 * Create a new product
	 *
	 * @param int $product_id
	 *
	 * @return array
	 * @throws MultivendorAPIException
	 */
	public function create_product( $fields = [] ) {
		$headers  = [
			'Accept' => 'application/json',
			'Content-Type' => 'multipart/form-data'
		];
		
		echo '$fields=';print_r($fields);
		//$response = Requests::post( $this->products_endpoint , $headers, $fields, $this->options );
		$response = $this->Client->request('POST',$this->products_endpoint , 
			[ 
				'multipart' => $fields  
			]
		);

		return json_decode( $response->getBody(), true );
		throw new MultivendorAPIException( $response->body, $response->status_code );
	}

	/**
	 * Retrieves products
	 *
	 * @param int $per_page Default is 10
	 * @param int $page Default is 1
	 *
	 * @return array
	 * @throws MultivendorAPIException
	 */
	public function get_products( $per_page = 10, $page = 1 ) {
		$headers     = [ 'Accept' => 'application/json' ];
		$per_page = $per_page > 0 ? $per_page: 10;
		$page = $page >= 1 ? $page : 1;
		$query_array = [ 'per_page' => $per_page, 'page' => $page ];
		$query       = http_build_query( $query_array );
		$response    = Requests::get( $this->products_endpoint .'/?' . $query, $headers, $this->options );
		if ( $response->success ) {
			return json_decode( $response->body, true );
		}
		throw new MultivendorAPIException( $response->body, $response->status_code );
		
	}

	/**
	 * Retrieve a product
	 *
	 * @param int $product_id
	 *
	 * @return array
	 * @throws MultivendorAPIException
	 */
	public function get_product( $product_id = 0 ) {
		$headers  = [ 'Accept' => 'application/json' ];
		//$response = Requests::get( $this->products_endpoint .'/' . $product_id, $headers, $this->options );
		$response = $this->Client->request('GET', $this->api_endpoint .'/multivendor/product/id/' . $product_id . '.json');
		
		//print_r($response);
		
		
		return json_decode( $response->getBody(), true );
		
		throw new MultivendorAPIException( $response->body, $response->getStatusCode() );
	}

	/**
	 * Retrieve a product
	 *
	 * @param int $product_id
	 *
	 * @return array
	 * @throws MultivendorAPIException
	 */
	public function get_product_count( $product_id = 0 ) {
		$headers  = [ 'Accept' => 'application/json' ];
		//$response = Requests::get( $this->products_endpoint .'/' . $product_id, $headers, $this->options );
		$response = $this->Client->request('GET', $this->api_endpoint .'/multivendor/product/count.json');
		
		//print_r($response);
		
		
		return json_decode( $response->getBody(), true );
		
		throw new MultivendorAPIException( $response->body, $response->status_code );
	}


	/**
	 * Deletes a product
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public function delete_product( $product_id = 0 ) {
		$headers  = [ 'Accept' => 'application/json' ];
		$response = Requests::delete( $this->products_endpoint .'/' . $product_id, $headers, $this->options );
		if ( $response->success ) {
			return true;
		}

		return false;
	}

	/**
	 * @param int $product_id
	 * @param array $fields
	 *
	 * @return bool
	 */
    public function update_product($product_id = 0, $fields = [])
    {
	    $headers  = [ 'Accept' => 'application/json' ];
	    $response = Requests::put( $this->products_endpoint .'/' . $product_id, $headers, $fields, $this->options );
	    if ( $response->success ) {
		    return true;
	    }

	    return false;
    }

	/**
	 * @param int $product_id
	 * @param string $license_key
	 *
	 * @return array
	 * @throws MultivendorAPIException
	 */
    public function get_license_meta_data( $product_id = 0, $license_key = '')
    {
	    $headers  = [ 'Accept' => 'application/json' ];
	    $response = Requests::get( $this->products_endpoint .'/' . $product_id . '/licenses/check_valid?key='.$license_key, $headers, $this->options );
	    if ( $response->success ) {
		    return json_decode( $response->body, true );
	    }
	    throw new MultivendorAPIException( $response->body, $response->status_code );
    }

	/**
	 * @param int $product_id
	 * @param string $license_key
	 *
	 * @return bool
	 * @throws MultivendorAPIException
	 */
    public function license_key_is_valid($product_id = 0, $license_key = '')
    {
    	$license_meta_data = $this->get_license_meta_data($product_id, $license_key);
    	if (empty($license_meta_data)) {
    		return false;
	    }
	    if ( !isset($license_meta_data[0]['license']['order_refunded']) || true === $license_meta_data[0]['license']['order_refunded']) {
    		return false;
	    }
	    return true;
    }

	/**
	 * @param int $product_id
	 *
	 * @return array
	 * @throws MultivendorAPIException
	 */
    public function get_licenses_by_product($product_id = 0)
    {
	    $headers  = [ 'Accept' => 'application/json' ];
	    $response = Requests::get( $this->products_endpoint .'/' . $product_id . '/licenses/', $headers, $this->options );
	    if ( $response->success ) {
		    return json_decode( $response->body, true );
	    }
	    throw new MultivendorAPIException( $response->body, $response->status_code );
    }

	/**
	 * @param int $product_id
	 *
	 * @return mixed
	 * @throws MultivendorAPIException
	 */
    public function get_licenses_by_order($product_id = 0)
    {
	    $headers  = [ 'Accept' => 'application/json' ];
	    $response = Requests::get( $this->products_endpoint .'/' . $product_id . '/licenses/', $headers, $this->options );
	    if ( $response->success ) {
		    return json_decode( $response->body, true );
	    }
	    throw new MultivendorAPIException( $response->body, $response->status_code );
    }

	/**
	 * @param int $order_id
	 * @return array
	 * @throws MultivendorAPIException
	 */
	public function get_order( $order_id = 0 ) {
		$headers  = [ 'Accept' => 'application/json' ];
		$response = Requests::get( $this->orders_endpoint . '/' . $order_id , $headers, $this->options );
		if ( $response->success ) {
			return json_decode( $response->body, true );
		}
		throw new MultivendorAPIException( $response->body, $response->status_code );
    }
}
