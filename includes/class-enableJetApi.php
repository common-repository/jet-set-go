<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

//require_once 'includes/class-cedJetFileUploadHelper.php';


class ced_Jetenable_api{
	
	private static $_instance;
	
	public static function getInstance() {
		self::$_instance = new self;
		if( !self::$_instance instanceof self )
			self::$_instance = new self;
	     
		return self::$_instance;
	
	}
	
	public function __construct() {
		$this->fileUploadHelper		=	cedJetFileUploadHelper::getInstance();
	}
	
	/**
	 * Enable Product section api
	 */
	public function enable_product_api(){
		
		$token_status = $this->fileUploadHelper->JrequestTokenCurl();
		
		if($token_status == false){
			echo "Api User or Secret key is wrong.Please enter valid sandbox api key";exit;
		}
		
		$jet_node_id 	=	 get_option('jet_node_id');
		$jet_node_id 	= 	 json_decode($jet_node_id);
		$fullfillmentnodeid = '';
		foreach($jet_node_id as $key => $node){
			$fullfillmentnodeid = $node;
			break;
		}
		
		//enable product api
		$error			=	array();
		$productsku		=	array();
		$sku			=	'test_product';
		$id				=	"12345678";
		
		//print_r($result);die;
		$productsku["product_title"]			=	"Test Product";
		$productsku["jet_browse_node_id"]		=	4000188;
		$productsku["brand"]					=	"Test";
		$upcinfo["standard_product_code"]		=	"719236005030";
		$upcinfo["standard_product_code_type"]	=	"UPC";
		$productsku["standard_product_codes"][]	=	$upcinfo;
		$productsku["multipack_quantity"]		=	1;
		$description							=	"";
		$description							=	"Test Product is used to enable product api woo";
		$productsku['product_description']		=	$description;
		
		$productsku['main_image_url']			=	CEDJETINTEGRATION.'jet_dashicon.png';
		$responseArray							=	"";
		
		
		$response 		= 	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.rawurlencode($sku),json_encode($productsku));
		$responseArray	=	json_decode($response,true);
		
		if($responseArray=="")
		{
			
			$price				=	array();
			$price['price']		=	38.00;
			
			$priceinfo['fulfillment_node_id']		=	$fullfillmentnodeid;
			$priceinfo['fulfillment_node_price']	=	38.00;
			$price['fulfillment_nodes'][]			=	$priceinfo;
			
			$responsePrice							=	"";
			$responsePrice 	= 	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.rawurlencode($sku).'/price',json_encode($price));
			$responsePrice	=	json_decode($responsePrice,true);
			
			if($responsePrice=="")
			{
				$inv								=	array();
				$inventory							=	array();
				$inv['fulfillment_node_id']			=	$fullfillmentnodeid;
				$qty								=	20;
				$inv['quantity']					=	$qty;
				$inventory['fulfillment_nodes'][]	=	$inv;
												
				$responseInventory		=	"";
				$response = $this->fileUploadHelper->CPutRequest('/merchant-skus/'.rawurlencode($sku).'/inventory',json_encode($inventory));
				$responseInventory 		= 	json_decode($response,true);
				
				if(isset($responseInventory['errors'])){
					echo "Error: ".json_encode($responseInventory['errors']);exit;
				}
			}
			elseif(isset($responsePrice['errors']))
			{
				echo "Error: ".json_encode($responsePrice['errors']);exit;
			}
		}
		elseif(isset($responseArray['errors']))
		{
			echo "Error: ".json_encode($responseArray['errors']);exit;
		}
		
	
			$responseSkuData		=	"";
			$responseSkuData 		=	 $this->fileUploadHelper->CGetRequest('/merchant-skus/'.rawurlencode($sku));
			$responseSkuDataArr 	= 	 json_decode($responseSkuData,true);
		
			if($responseSkuDataArr && !(isset($responseSkuDataArr['errors'])))
			{
				//Enable cancel api
				$order						=	array();
				$order['fulfillment_node']	=	$fullfillmentnodeid;
				$order['items']				=	array(array('sku'=>$sku,'order_quantity'=>0,'order_cancel_quantity'=>1));
				
				$response	=	"";
				$response	=	$this->fileUploadHelper->CPostRequest('/orders/generate/1',json_encode($order));
				$response	=	json_decode($response,true);
				
				if(isset($response['order_urls']) && count($response['order_urls']) > 0)
				{
					$responseData	=	"";
					$qty			=	0;
					$cancel			=	0;
					$carrier		=	"";
					$deliver		=	"";
					
					$responsearr	=	array();
					$responseData	=	$this->fileUploadHelper->CGetRequest($response['order_urls'][0]);
					$responsearr 	= 	json_decode($responseData,true);
					$item_sku		=	$responsearr['order_items'][0]['merchant_sku'];
					$carrier		=	$responsearr['order_detail']['request_shipping_carrier'];
					$qty			=	$responsearr['order_items'][0]['request_order_quantity'];
					$cancel			=	$responsearr['order_items'][0]['request_order_cancel_qty'];
					$deliver 		=	$responsearr['order_detail']['request_delivery_by'];
					
					if($deliver){
						$time		=	explode('.', $deliver);
						$deliver	=	$time[0].'.0000000-07:00';
					}
						$t			=	time();
						$data_ship	=	array();
						$data_ship['shipments'][]	=	array (
															"alt_shipment_id"=> time()."96",
															'shipment_tracking_number'=>time().'5454',
															'response_shipment_date'=>$deliver,
															'response_shipment_method'=>'',
															'expected_delivery_date'=>$deliver,
															'ship_from_zip_code'=>'84047',
															'carrier_pick_up_date'=>$deliver,
															'carrier'=>$carrier,
															'shipment_items'=>array(
																	array(
																		'shipment_item_id'=>time().'-123',
																		'merchant_sku'=>$item_sku,
																		'response_shipment_sku_quantity'=>$qty,
																		'response_shipment_cancel_qty'=>$cancel,
																		'RMA_number'=>'abcedef',
																		'days_to_return'=>30,
																		'return_location'=>array('address1'=>'6909 South State Street','address2'=>'Suite C',
																		'city'=>'Midvale','state'=>'UT',
																		'zip_code'=>'84047'
																							)
																					)
																			)
																	);
							
							$data		=	$this->fileUploadHelper->CPutRequest('/orders/'.$responsearr['merchant_order_id'].'/shipped',json_encode($data_ship));
							
						}
		
						
						//create order by api and acknowledge and shipped
						$order						=	array();
						$order['fulfillment_node']	=	$fullfillmentnodeid;
						$order['items']				=	array(array('sku'=>$sku,'order_quantity'=>1,'order_cancel_quantity'=>0));
						$response					=	"";
						$response 					=	$this->fileUploadHelper->CPostRequest('/orders/generate/1',json_encode($order));
						$response					=	json_decode($response,true);
						
						
						if(isset($response['order_urls']) && count($response['order_urls']) > 0)
						{
							$responseData		=	"";
							$responsearr		=	array();
							$responseData		=	$this->fileUploadHelper->CGetRequest($response['order_urls'][0]);
							$responsearr 		=	json_decode($responseData,true);
							
							if(count($responsearr) > 0 && isset($responsearr['merchant_order_id'])){
								
								$order_ack		=	array();
								
								$order_ack['acknowledgement_status'] 	=	"accepted";
								$order_ack['order_items'][] 			= 	array(
																			 'order_item_acknowledgement_status'=>'fulfillable',
																			 'order_item_id' =>$responsearr['order_items'][0]['order_item_id']
																			);
								
								$ackResponse	=	"";
								$ackResponse	=	$this->fileUploadHelper->CPutRequest('/orders/'.$responsearr['merchant_order_id'].'/acknowledge',json_encode($order_ack));
								$ackData		=	"";
								$ackData		=	json_decode($ackResponse,true);
								
								if($ackData==""){
									//ship order data
									$deliver					=	"";
									$request_shipping_carrier	=	"";
									$deliver 					=	$responsearr['order_detail']['request_delivery_by'];
									$request_shipping_carrier	=	$responsearr['order_detail']['request_shipping_carrier'];
									
									if($deliver)
									{
										$time=explode('.', $deliver);
										$deliver=$time[0].'.0000000-07:00';
									}
									$shipment_arr=array();
									$shipment_arr[]= array(
											'shipment_item_id'=>time().'-123',
											'merchant_sku'=>'test_product',
											'response_shipment_sku_quantity'=>1,
											'response_shipment_cancel_qty'=>0,
											'RMA_number'=>"abcdef",
											'days_to_return'=>30,
											'return_location'=>array('address1'=>'6909 South State Street',
													'address2'=>'Suite C',
													'city'=>'Midvale',
													'state'=>'UT','zip_code'=>'84047'
											)
									);
									$data_ship=array();
									$data_ship['shipments'][]=array	(
											"alt_shipment_id"=> time()."96",
											'shipment_tracking_number'=>time().'5454',
											'response_shipment_date'=>$deliver,
											'response_shipment_method'=>'',
											'expected_delivery_date'=>$deliver,
											'ship_from_zip_code'=>"12345",
											'carrier_pick_up_date'=>$deliver,
											'carrier'=>$request_shipping_carrier,
											'shipment_items'=>$shipment_arr
									);
									
									$data	=	$this->fileUploadHelper->CPutRequest('/orders/'.$responsearr['merchant_order_id'].'/shipped',json_encode($data_ship));
									$data	= 	json_decode($data,true);
									
								}
							}
						}
		
						//create return
						$order_id	=	$responsearr['merchant_order_id'];
						
						$return		=	"";
						$returnarr	=	array();
						$return		=	$this->fileUploadHelper->CGetRequest('/returns/generate/'.$order_id);
						$returnarr 	= 	json_decode($return,true);
						
						if(isset($returnarr['url']) && count($returnarr)>0)
						{
							$testurlarray	=	explode("/",$returnarr['url']);
							$returnid		=	$testurlarray[3];
							
							$resturnData	=	"";
							$refund			=	"";
							$item_id		=	"";
							$shipping		=	"";
							$principal		=	0;
							
							$resturnData 	= 	json_decode($this->fileUploadHelper->CGetRequest($returnarr['url']),true);
							$item_id		=	$resturnData['return_merchant_SKUs'][0]['order_item_id'];
							$refund			=	$resturnData['return_merchant_SKUs'][0]['return_quantity'];
							$refund			=	$resturnData['return_merchant_SKUs'][0]['return_quantity'];
							$feedback		=	"item damaged";
							
							$status			=	true;
							$shipping		=	(int)$resturnData['return_merchant_SKUs'][0]['requested_refund_amount']['shipping_cost'];
							$s_tax			=	0;
							$tax=0;
							$principal		=	(int)$resturnData['return_merchant_SKUs'][0]['requested_refund_amount']['principal'];
							$data_ship		=	array();
							$data_ship['merchant_order_id']	=	$order_id;
							$data_ship['items'][]	=	array(
									'order_item_id'=>$item_id,
									'total_quantity_returned'=>$refund,
									'order_return_refund_qty'=>$refund,
									'return_refund_feedback'=>$feedback,
									'refund_amount'=>array(
											'principal'=>$principal,
											'tax'=>$tax,
											'shipping_cost'=>$shipping,
											'shipping_tax'=>$s_tax
									)
							);
							$data_ship['agree_to_return_charge']	=	$status;
							$data	=	$this->fileUploadHelper->CPutRequest('/returns/'.$returnid.'/complete',json_encode($data_ship));
						}
						echo "enabled";
						update_option('enable_api','yes');exit;
					}
					else
					{
						if(count($error)>0){
							echo implode('\n',$error);exit;
						}
					}
		
	}
	
	
	public function live_check_api(){
		//get info about available for purchase
		if(isset($_GET['status_csv']) && $_GET['status_csv']==1){
			$value='';
			$value=implode('+',explode(' ',$_GET['status_csv']));
			//echo '/portal/merchantskus/export?statuses='.$_GET['status_csv'];die;
			$response = $jetHelper->CGetRequest('/portal/merchantskus/export?statuses=Available+for+Purchase');
			var_Dump($response);die('hell');
		}
		
		//get info about under jet review
		if(isset($_GET['status_csv']) && $_GET['status_csv']==2){
			$response = $jetHelper->CGetRequest('/portal/merchantskus/export?statuses=Under+Jet+Review');
			var_Dump($response);die('hell');
		}
		
		//get info about missing listing data
		if(isset($_GET['status_csv']) && $_GET['status_csv']==3){
			$response = $jetHelper->CGetRequest('/portal/merchantskus/export?statuses=Missing+Listing+Data');
			var_Dump($response);die('hell');
		}
		
		//get info about all product status(s) listed on jet panel
		if(isset($_GET['status_csv']) && $_GET['status_csv']==4){
			$response = $jetHelper->CGetRequest('/portal/merchantskus/export');
			var_Dump($response);die('hell');
		}
		
		//get all product status(s) listed on jet panel
		if(isset($_GET['status'])){
			$value='';
			$value=implode('+',explode(' ',$_GET['status']));
			$response = $jetHelper->CGetRequest('/portal/merchantskus?from=0&size='.$_GET['limit'].'&statuses='.$value);
			var_Dump($response);die('hell');
		}
		
		//get info either all api enable or not
		if(isset($_GET['setup'])){
			$response = $jetHelper->CGetRequest('/portal/merchantsetupstatus/');
			var_Dump($response);die('hell');
		}
		
		//get info about return
		if(isset($_GET['return'])){
			$response = $jetHelper->CGetRequest('/portal/returnssetup/');
			var_Dump($response);die('hell');
		}
		
		//get info about all fulfilment node on jet
		if(isset($_GET['fulfillment'])){
			$response = $jetHelper->CGetRequest('/fulfillmentnodesbymerchantid/');
			var_Dump($response);die('hell');
		}
	}
}	