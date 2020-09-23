<?php

require_once 'MpApi.php'; 

$access_token = "Your Access token";
$obj_api = new MpApi($access_token);


#check if token expired 
#then update token using refresh token ;  (for more details  see /new/token in api reference page of app)
#else proceed 


$api_response = $obj_api->getResponse('GET', '/multivendor/orders.json' , '?limit=5&offset=10');

echo 'Get Orders <br>';
echo '<pre>';
echo $api_response;
echo '</pre>';

sleep(3);

$data = '{
                                    "product_id": 9888869633,
                                    "variants": [
                                        {
                                            "variant_id": 36494673921,
                                            "quantity":15
                                        },
                                        {
                                            "variant_id": 36494673857,
                                            "quantity":34
                                        },
                                        {
                                            "variant_id": 36494673793,
                                            "quantity": 3
                                        }
                                    ]
                                }';
$api_response = $obj_api->getResponse('PUT', '/multivendor/inventory/inventory.json' , false , $data);

sleep(6);
echo 'Update Inventory<br>';
echo '<pre>';
echo $api_response;
echo '</pre>';


$data = '{
                                            "seller_email": "test@webkul.com" ,
                                            "product_name": "Api Test Product",
                                            "product_type": "api-test-type",
                                            "tags":"x,y,z,a,b,c,d,e,f",
                                            "collection":[{
                                            	"id_category":1
                                            }],
                                            "variants":[
                                                {
                                                    "price":"12",
                                                    "quantity": "3",
                                                    "track_inventory": true,
                                                    "requires_shipping": true,
                                                    "charge_taxes":true,
                                                    "options_value":{
                                                        "option1":"red",
                                                        "option2":"S",
                                                        "option3":"XL"
                                                    }


                                                } ,
                                                {
                                                    "price":"14",
                                                    "quantity": "5",
                                                    "track_inventory": true,
                                                    "requires_shipping": true,
                                                    "charge_taxes":true,
                                                    "options_value":{
                                                        "option1":"red",
                                                        "option3":"MD",
                                                        "option2":"B"
                                                    }


                                                } ,
                                                    {
                                                    "price":"12",
                                                    "quantity": "3.5",
                                                    "track_inventory": true,
                                                    "requires_shipping": true,
                                                    "charge_taxes":true,
                                                    "options_value":{
                                                        "option1":"red",
                                                        "option3":"SM",
                                                        "option2":"T"
                                                    }


                                                } 
                                            ],
                                            "images":[
                                                    
                                                    {
                                                    
                                                    "img_url": "http:\/\/shopify.webkul.com\/multivendor\/img\/product_img\/shop.jpg"
                                                    },
                                                    {
                                                    
                                                    "img_url": "http:\/\/shopify.webkul.com\/multivendor\/img\/product_img\/shop.jpg"
                                                    }
                                                ],
                                            "options_name":{
                                                    "option1":{
                                                        "name":"color",
                                                        "value":["red" ,"green" ,"blue"]
                                                    },
                                                    "option2":{
                                                        "name":"title",
                                                        "value":["B" ,"S" ,"T"]
                                                    },
                                                    "option3":{
                                                        "name":"size",
                                                        "value":["XL" ,"MD" ,"SM"]
                                                    }
                                            }

                                        }';
$api_response = $obj_api->getResponse('POST', '/multivendor/product/create/product.json' , false , $data);

echo 'Create Product <br>';
echo '<pre>';
echo $api_response;
echo '</pre>';





?>