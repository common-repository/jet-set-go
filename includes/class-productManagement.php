<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'class-cedJetFileUploadHelper.php';
require_once 'class-cedJetFileCreationHelper.php';
require_once 'class-cedJetDBHelper.php';

class cedJetProductManagement{

	private static $_instance;

	public static function getInstance() {

		if( !self::$_instance instanceof self )
			self::$_instance = new self;

		return self::$_instance;

	}
	
	public function __construct() {
		
		$this->apiUrl				=	get_option( 'jet_api_url',true);
		$this->jetUser				=	get_option( 'jet_user',true);
		$this->jetSecret			=	get_option( 'jet_password',true);
		$this->jetNodeId			=	json_decode(get_option( 'jet_node_id',true));
		$this->fileCreationHelper	=	cedJetFileCreationHelper::getInstance();
		$this->fileUploadHelper		=	cedJetFileUploadHelper::getInstance();
		$this->modelAction			=	cedJetDBHelper::getInstance();

	}
	
	public function uploadProducts($fileType=null,$selectedProductIDs=array(),$error_file_id=null){
		$this->validateUserData();
		if(isset($_POST['upload_type']) || trim($fileType)	== 'upload' || trim($fileType)	==	'Archive' || trim($fileType)	==	'Unarchive'){
			
			if(isset($_POST['upload_type'])){
				$fileType				=	$_POST['upload_type'];
				$selectedProductIDs		=	$_POST['all_upload_product_id'];
			}
			
			if(count($selectedProductIDs)	==	0)
			{
				$_SESSION['upload_common_msg'][] = 'Please Select product\'s to perform Action.';
				return;
			}
			
			$productIdsChunks		=	(array_chunk($selectedProductIDs,30));
			
			if($fileType	== 'upload'){
				
				$this->uploadAction($productIdsChunks);
			}
		// print_r($productIdsChunks);die;
			elseif($fileType	==	'Archive'){
				
				$this->archiveAction($productIdsChunks);
			}
			elseif($fileType	==	'Unarchive'){
				
				$this->unarchiveAction($productIdsChunks);
			}
			
		}else{
			// print_r("kasashjhas");die;
			//setup intial Api settings 
			$selectedProductIDs = array($selectedProductIDs);
			if(count($selectedProductIDs)	==	0)
			{
				$_SESSION['upload_common_msg'][] = 'Please Select product\'s to perform Action.';
				return;
				
			}
			$this->uploadAction($selectedProductIDs);
		}

	}
	
	public function archiveAction($productIdsChunks){
		
		$ids = $productIdsChunks[0];
		
		if(!is_array($ids) && $ids!='')
		{
			$ids=array($ids);
		}
		
		if(!is_array($ids))
		{
			$_SESSION['archieve_message'][] = 'Please select product id(es)';
			return;
		}
		else
		{
			try
			{
				$cArchived=0;$cClosed=0;
				foreach ($ids as $id)
				{
					$productbyid 		= 	get_product($id);
					
					if($productbyid->is_type('variable'))
					{
						$variationsbyid			=	$productbyid->get_available_variations();
						$archieve_var_settings 	=   get_option('archieve_variable_settings');
						
						if($archieve_var_settings == 'yes'){
							foreach($variationsbyid as $key1 => $variationdata)
							{
								$sku					=	$variationdata['variation_id'];
								$data['is_archived']	=	true;
								$result   				= 	$this->fileUploadHelper->CGetRequest('/merchant-skus/'.$sku.'');
								$response 				= 	json_decode($result);

								if($response->status == 'Archived')
								{
									$cArchived++;
								}
								else
								{
									$cClosed++;
									$data1=$this->fileUploadHelper->CPutRequest('/merchant-skus/'.$sku.'/status/archive',json_encode($data));
									update_post_meta($id,'jet_product_status','Archived');
								}
							}
						}else{
							$sku					=	$id;
							$data['is_archived']	=	true;
							$result   				= 	$this->fileUploadHelper->CGetRequest('/merchant-skus/'.$sku.'');
							$response 				= 	json_decode($result);

							if($response->status == 'Archived')
							{
								$cArchived++;
							}
							else
							{
								$cClosed++;
								$data1=$this->fileUploadHelper->CPutRequest('/merchant-skus/'.$sku.'/status/archive',json_encode($data));
								update_post_meta($id,'jet_product_status','Archived');
							}
							
						}//else
					}
					else
					{
						$sku = $id;
						$data['is_archived']	=	true;
						$result   				= 	$this->fileUploadHelper->CGetRequest('/merchant-skus/'.$sku.'');
						$response 				= 	json_decode($result);

						if($response->status == 'Archived')
						{
							$cArchived++;
						}
						else
						{
							$cClosed++;
							$data1		=	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.$sku.'/status/archive',json_encode($data));
							update_post_meta($id,'jet_product_status','Archived');
						}
					}

				}
				if($cClosed>0 || $cArchived>0)
				{
					if($cClosed>0)
					{
						$_SESSION['archieve_message'][] = $cClosed.' product(s) is archived successfully';
						return;	
					}
					if($cArchived>0)
					{
						$_SESSION['archieve_message'][] = $cArchived.' product(s) is already archived';
						return;
					}
				}
				else
				{
					$_SESSION['archieve_message'][] = 'product(s) can not be archived';
					return;
					
				}

			}
			catch (Exception $e)
			{
				$_SESSION['archive_error'] = $e->getMessage();
			}
		}
		
	}
	
	public function unarchiveAction($productIdsChunks){

		$ids = $productIdsChunks[0];
		
		if(!is_array($ids) && $ids!='')
		{
			$ids=array($ids);
		}
		
		if(!is_array($ids))
		{
			$_SESSION['unarchieve_message'][] = 'Please select product id(es)';
			return;
		}
		else
		{
			try
			{
				$cunArchived=0;
				$jet_node_id	= get_option( 'jet_node_id');
				
				$jet_node_id    = json_decode($jet_node_id);
				
				foreach ($ids as $id)
				{
					$productDetails	=	$this->fetchProductDetail($id);
					
					if($productDetails['type']	==	'variable'){

						$tmp_gallery_images= array();
						if(isset($productDetails['gallery_images'])){
							$tmp_gallery_images = $productDetails['gallery_images'];
							unset($productDetails['gallery_images']);
						}
						unset($productDetails['type']);
						$productFiles	=	array();
						$skus			=	array();
						foreach($productDetails as $productVariation){
							
							$sku=$productVariation['id'];
							
							$data['is_archived']=false;
							
							foreach($jet_node_id as $key => $fullfillment_id)
							{
								$qty 								= 	 $productVariation[stock];
								$node1['fulfillment_node_id']		=	"$fullfillment_id";
								$node1['quantity']					=	(int)$qty;
								$inventory['fulfillment_nodes'][]	=	$node1;
								$node1								=	array();
							}
							
							$data1		=	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.trim($sku).'/status/archive',json_encode($data));
							$inventry	=	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.trim($sku).'/inventory',json_encode($inventory));
							$result		=	$this->fileUploadHelper->CGetRequest('/merchant-skus/'.$sku.'');
							$response	=	json_decode($result);
							
							if($response->status == 'Processing' || $response->status	==	'Under Jet Review')
							{
								$cunArchived++;
								update_post_meta($sku,'jet_product_status','Under Jet Review');
							}else{
								update_post_meta($sku,'jet_product_status','Not Uploaded');
							}
						}
					}
					else
					{
						$sku					=	$productDetails['id'];
						$qty					= 	$productDetails['stock'];
						$data['is_archived']	=	false;

						foreach($jet_node_id as $key => $fullfillment_id)
						{
							$node1['fulfillment_node_id']		=	 "$fullfillment_id";
							$node1['quantity']					= 	 (int)$qty;
							$inventory['fulfillment_nodes'][]	=	 $node1;
							$node1								=	array();
						}

						$data1		=	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.trim($sku).'/status/archive',json_encode($data));
						$inventry	=	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.trim($sku).'/inventory',json_encode($inventory));
						$result		=	$this->fileUploadHelper->CGetRequest('/merchant-skus/'.$sku.'');
						$response	=	json_decode($result);

						if($response->status == 'Processing' || $response->status	==	'Under Jet Review')
						{
							$cunArchived++;
							update_post_meta($sku,'jet_product_status','Under Jet Review');
						}else{
							update_post_meta($sku,'jet_product_status','Not Uploaded');
						}
					}
				}
				if($cunArchived>0)
				{
					$_SESSION['unarchieve_message'][] = $cunArchived.' product(s) is unarchived successfully';
					return;
				}
				else
				{
					$_SESSION['unarchieve_message'][] = 'product(s) can not unarchive';
					return;
				}

			}
			catch (Exception $e)
			{
				$_SESSION['unarchive_error'] = $e->getMessage();
			}
		}
		
	}
	
	public function validateUserData(){
		
		if(empty($this->apiUrl) || empty($this->jetUser) || empty($this->jetSecret) || empty($this->jetNodeId))
		{
			$_SESSION['upload_common_msg'][] = 'Please Insert all the Jet Configuration details';
			return;
		}else{
			
			return true;
		}
	}
	/*
	 * for uploading
	 *   */
	public function uploadAction($productIdsChunks){
		$counter = 0;
		// print_r($productIdsChunks);die;
		$wpuploadDir	=	wp_upload_dir();
		$baseDir		=	$wpuploadDir['basedir'];
		$uploadDir		=	$baseDir . '/var/jet-upload';
		
		if (! is_dir($uploadDir))
		{
			mkdir( $uploadDir, 0777 ,true);
		}
		
		$skuArray		=	array();
		$priceArray		=	array();
		$inventArray	=	array();
		$shipExptn		=	array();
		$retrnExptn		=	array();
		$reltnArray		=	array();
		
		foreach($productIdsChunks as  $productIDs)
		{
			$commaseperatedids = implode(",", $productIDs);
			$this->commaseperatedids = $commaseperatedids;
			foreach($productIDs as $pid){
				
				$productDetails	=	$this->fetchProductDetail($pid);
				if($productDetails['type']	==	'variable'){
					
					$tmp_gallery_images= array();
					if(isset($productDetails['gallery_images'])){
						$tmp_gallery_images = $productDetails['gallery_images'];
						unset($productDetails['gallery_images']);
					}
					
					unset($productDetails['type']);
					$productFiles	=	array();
					$skus			=	array();
					foreach($productDetails as $productVariation){
						
						$productVariation['gallery_images'] = $tmp_gallery_images;
				// print_r($productVariation);
						$skus[]			=	$productVariation[id];
						$attrIds		=	$productVariation['all_attributes'];
						//print_r($skus);
						
						$productFiles	=	$this->fileCreationHelper->create_file_formatted_array($productVariation);
						
						if(isset($productFiles['sku']) && count($productFiles['sku'])){
							
							$skuArray[]			=	$productFiles['sku'];
						}

						if(isset($productFiles['price']) && count($productFiles['price'])){
							$priceArray[]		=	$productFiles['price'];
						}
						
						if(isset($productFiles['inventory']) && count($productFiles['inventory'])){
							
							$inventArray[]		=	$productFiles['inventory'];
						}
						
						if(isset($productFiles['shipping_exceptions']) && count($productFiles['shipping_exceptions'])){
							
							$shipExptn[]		=	$productFiles['shipping_exceptions'];
						}
						
						if(isset($productFiles['return_exceptions']) && count($productFiles['return_exceptions'])){
							
							$retrnExptn[]		=	$productFiles['return_exceptions'];
						}
					}
					
					$relationship_return	=	$this->fileCreationHelper->create_relationship_file($skus,$attrIds);
					
					if(!empty($relationship_return)){
// 						print_r($relationship_return);die('fgdsafsaf');
						$relationship[]	=	$relationship_return;
					}
					
				}else{

					$productFiles	=	array();
					$productFiles	=	$this->fileCreationHelper->create_file_formatted_array($productDetails);
					
					if(isset($productFiles['sku'])){
						
						$skuArray[]			=	$productFiles['sku'];
					}

					if(isset($productFiles['price'])){
						$priceArray[]		=	$productFiles['price'];
					}
					
					if(isset($productFiles['inventory'])){
						
						$inventArray[]		=	$productFiles['inventory'];
					}
					
					if(isset($productFiles['shipping_exceptions'])){
						
						$shipExptn[]		=	$productFiles['shipping_exceptions'];
					}
					
					if(isset($productFiles['return_exceptions'])){
						
						$retrnExptn[]		=	$productFiles['return_exceptions'];
					}
				}
			}
			
			
			//dinesh change 
			$finalskuarray	=	array();
			foreach($skuArray as $tmpindx => $skuval)
			{
				foreach($skuval as $tmpskuid	=>	$all_data_value)
				{
					$finalskuarray[$tmpskuid]	=	$all_data_value;
				}
			}
			
			$finalpricearray	=	array();
			foreach($priceArray as $tmppriceindx => $priceval)
			{
				foreach($priceval as $tmppriceid	=>	$all_price_data_value)
				{ 
					$finalpricearray[$tmppriceid]	=	$all_price_data_value;
				}
			}
			
			$finalinventarray	=	array();
			foreach($inventArray as $tmpinvntindx => $invntval)
			{
				foreach($invntval as $tmpinvntid	=>	$all_invent_data_value)
				{
					$finalinventarray[$tmpinvntid]	=	$all_invent_data_value;
				}
			}

			$finalrelatnshparray	=	array();
			foreach($relationship as $tmprelindx	=>	$reltnval)
			{
				foreach($reltnval as $tmprelatnid	=>	$all_reltn_data_value)
				{
					$finalrelatnshparray[$tmprelatnid]	=	$all_reltn_data_value;
				}
			}
			
			$shipngexcptnarray	=	array();
			foreach($shipExptn as $tmpshpexcptn	=>	$excptnval)
			{
				foreach($excptnval as $excptnid	=>	$all_excptn_data)
				{
					$shipngexcptnarray[$excptnid]	=	$all_excptn_data;
				}
			}
			
			$returnexceptnarray		=	array();
			foreach($retrnExptn as $tmprtrnexcptn	=>	$rtrnexcptnval)
			{
				foreach($rtrnexcptnval as $rtrnexcptnid	=>	$all_retrn_excptn_data)
				{
					$returnexceptnarray[$rtrnexcptnid]	=	$all_retrn_excptn_data;
				}
			}
			
			$upload_file = false;
			$t=time();

			if(!empty($finalskuarray) && count($finalskuarray)>0)
			{
				$finalskujson		= 	json_encode($finalskuarray);
				$file_path 			= 	$uploadDir . '/skus'.$t.".json";
				$file_type 			= 	"MerchantSKUs";
				$file_name			=	"skus".$t.".json";
				$myfile 			= 	fopen($file_path, "w") ;

				fwrite($myfile, $finalskujson);
				fclose($myfile);
				if(fopen($file_path.".gz","r") == false)
				{
					$this->fileUploadHelper->gzCompressFile($file_path,9);
					$upload_file = true;
				}
			}
			
			if(!empty($finalpricearray) && count($finalpricearray)>0)
			{
				
				$finalpricejson		= 	json_encode($finalpricearray);
				$file_path1			= 	$uploadDir . '/prices'.$t.".json";
				$file_type1			=	"Price";
				$file_name1			=	"prices".$t.".json";
				$myfile1 			= 	fopen($file_path1, "w") ;

				fwrite($myfile1, $finalpricejson);
				fclose($myfile1);
				if(fopen($file_path1.".gz","r") == false)
				{
					$this->fileUploadHelper->gzCompressFile($file_path1,9);
				}
			}
			
			if(!empty($finalinventarray) && count($finalinventarray)>0)
			{
				$finalinventoryjson		=	json_encode($finalinventarray);
				$file_path2 			= 	$uploadDir . '/inventrys'.$t.".json";
				$file_type2				=	"Inventory";
				$file_name2				=	"inventrys".$t.".json";
				$myfile2 				= 	fopen($file_path2, "w") ;

				fwrite($myfile2, $finalinventoryjson);
				fclose($myfile2);

				if(fopen($file_path2.".gz","r") == false)
				{
					$this->fileUploadHelper->gzCompressFile($file_path2,9);
				}
			}
			
			if(!empty($shipngexcptnarray) && count($shipngexcptnarray)>0)
			{
				$finalshipexcptnjson	=	json_encode($shipngexcptnarray);
				$file_path3 			= 	$uploadDir . '/shippingexception'.$t.".json";
				$file_type3				=	"ShippingException";
				$file_name3				=	"shippingexception".$t.".json";
				$myfile3 				= 	fopen($file_path3, "w") ;

				fwrite($myfile3, $finalshipexcptnjson);
				fclose($myfile3);

				if(fopen($file_path3.".gz","r") == false)
				{
					$this->fileUploadHelper->gzCompressFile($file_path3,9);
				}
			}
			
			if(!empty($returnexceptnarray) && count($returnexceptnarray)>0)
			{
				$finalrtrnexcptnjson	=	json_encode($returnexceptnarray);
				$file_path4 			= 	$uploadDir . '/returnexception'.$t.".json";
				$file_type4				=	"ReturnsException";
				$file_name4				=	"returnexception".$t.".json";
				$myfile4				= 	fopen($file_path4, "w") ;

				fwrite($myfile4, $finalrtrnexcptnjson);
				fclose($myfile4);

				if(fopen($file_path4.".gz","r") == false)
				{
					$this->fileUploadHelper->gzCompressFile($file_path4,9);
				}
			}
			
			if(!empty($finalrelatnshparray) && count($finalrelatnshparray))
			{
				$finalreltnshpjson	=	json_encode($finalrelatnshparray);
				$file_path5 			= 	$uploadDir . '/relationship'.$t.".json";
				$file_type5				=	"Variation";
				$file_name5				=	"relationship".$t.".json";
				$myfile5				= 	fopen($file_path5, "w") ;

				fwrite($myfile5, $finalreltnshpjson);
				fclose($myfile5);

				if(fopen($file_path5.".gz","r") == false)
				{
					$this->fileUploadHelper->gzCompressFile($file_path5,9);
				}
			}
			
			
			
			if($upload_file==false)
			{
				$_SESSION['upload_product_error'][] = __('Product name : '.$this->product[name].' informtion was incomplete so they are not prepared for upload.','woocommerce-jet-integration');  
				continue;
			}
			
			$compressed_file_path 	=	$file_path.".gz";
			$compressed_file_path1 	=	$file_path1.".gz";
			$compressed_file_path2 	=	$file_path2.".gz";
			$compressed_file_path3 	=	$file_path3.".gz";
			$compressed_file_path4	=	$file_path4.".gz";
			
			if(fopen($compressed_file_path,"r")!=false){
				$sku_status = $this->uploadSkuFile($compressed_file_path,$file_name,$file_type);
				//sku checkin start and all in exist within this sku if
				// print_r($sku_status);die;
				if($sku_status == 'Acknowledged'){
					$this->after_sku_status_acknowledge();
					if(fopen($compressed_file_path1,"r")!=false)
					{
						$price_status = $this->UploadPriceFile($compressed_file_path1,$file_name1,$file_type1);
						if($price_status == 'Acknowledged'){
							$this->after_price_status_acknowledge();
							//upload inventory file
							if(fopen($compressed_file_path2,"r")!=false)
							{
								$inventory_status = $this->UploadInventoryFile($compressed_file_path2,$file_name2,$file_type2);
								if($inventory_status == 'Acknowledged'){
									$status 		= 	'Acknowledged';
									global $wpdb;
									$currentid   	= 	$this->currentid;
									$table_name 	= 	$wpdb->prefix.'jet_file_info';
									$qry 			= 	"UPDATE `$table_name` SET `status`= '$status' WHERE id = '$currentid';";
									$wpdb->query($qry);
								}
								$this->after_inventory_status_acknowlegde();
								if(!empty($finalrelatnshparray)){
									$this->uploadRelationshipFile($finalrelatnshparray); 
								}
								$_SESSION['file_sucess'] = 'File Uploaded Success';
							}//end uploading inventory file

							//upload shipping exception
							if(fopen($compressed_file_path3,"r")!=false)
							{
								$shipping_exception_status = $this->UploadShippingException($compressed_file_path3,$file_name3,$file_type3);
								
								if($shipping_exception_status == 'Acknowledged'){
									$currentid   	= 	$this->currentid;
									$status 		= 	'Acknowledged';
									
									global $wpdb;
									$table_name 	= 	$wpdb->prefix.'jet_file_info';
									$qry 			= 	"UPDATE `$table_name` SET `status`= '$status' WHERE id = '$currentid';";
									$wpdb->query($qry);
								}
								$this->after_shipping_exception_acknowledge_check();
							}
							//end shipping exception
							
							//upload return exception
							if(fopen($compressed_file_path4,"r")!=false)
							{
								$return_exception_status = $this->UploadReturnException($compressed_file_path4,$file_name4,$file_type4);
								if($return_exception_status == 'Acknowledged' ){
									$status 		= 	'Acknowledged';
									$currentid   	= 	$this->currentid;
									
									global $wpdb;
									$table_name 	= 	$wpdb->prefix.'jet_file_info';
									$qry 			= 	"UPDATE `$table_name` SET `status`= '$status' WHERE id = '$currentid';";
									$wpdb->query($qry);
								}
								$this->after_return_exception_check();
							}
							//end return exception
						}
					}
					/* if(!empty($finalrelatnshparray)){
						$this->uploadRelationshipFile($finalrelatnshparray);
					} */
				}//all check should be within sku status Acknowledge
			}
		}
				// print_r($productFiles);die;

	}
	
	/**
	 * Actual validating data before upload
	 * 
	 */
	public function validate_jet_product_data($all_product_id){
		foreach($all_product_id as $key => $pid){
		// print_r($pid);

			$productDetails	=	$this->fetchProductDetail($pid);
			// print_r($productDetails);
			if($productDetails['type']	==	'variable'){
				$tmp_gallery_images= array();
				if(isset($productDetails['gallery_images'])){
					$tmp_gallery_images = $productDetails['gallery_images'];
					unset($productDetails['gallery_images']);
				}
				
				unset($productDetails['type']);
				$productFiles	=	array();
				$skus			=	array();
				foreach($productDetails as $productVariation){
					// print_r("FDFSD");
					$productFiles	=	array();
					$skus[]			=	$productVariation[id];
					$attrIds		=	$productVariation['all_attributes'];
					$productFiles	=	$this->fileCreationHelper->create_file_formatted_array($productVariation);

					if(isset($productFiles['sku']) && count($productFiles['sku'])){
						$skuArray[]			=	$productFiles['sku'];
					}

					if(isset($productFiles['price']) && count($productFiles['price'])){
						$priceArray[]		=	$productFiles['price'];
					}

					if(isset($productFiles['inventory']) && count($productFiles['inventory'])){
						$inventArray[]		=	$productFiles['inventory'];
					}

				}

			}else{

				$productFiles	=	array();
				$productFiles	=	$this->fileCreationHelper->create_file_formatted_array($productDetails);

				if(isset($productFiles['sku'])){
					$skuArray[]			=	$productFiles['sku'];
				}

				if(isset($productFiles['price'])){
					$priceArray[]		=	$productFiles['price'];
				}

				if(isset($productFiles['inventory'])){
					$inventArray[]		=	$productFiles['inventory'];
				}

			}
		}// End Foreach loop
		
		// print_r($skuArray);
		$all_data['all_data']['sku'] 			= $skuArray;
		$all_data['all_data']['price'] 			= $priceArray;
		$all_data['all_data']['inventory'] 		= $inventArray;
		// print_r($all_data);die;
		return $all_data;
		
	}
	public function uploadRelationshipFile($finalrelatnshparray){
		/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/relationship.txt","a") or die("Can't open the requested file");
		fwrite($fp, ("Relationship file Upload")); */
		// print_r($finalrelatnshparray);die;
		if(!empty($finalrelatnshparray) && count($finalrelatnshparray)>0){
			foreach($finalrelatnshparray as $relid	=>	$reldata){
				/* fwrite($fp,("\n"));
				fwrite($fp,("Parent id".$relid));
				fwrite($fp,("\n"));
				fwrite($fp,("Data".json_encode($reldata))); */
				
				// print_r($relid);die("function");
				$parentsku		=	$relid;
				$reltndata		=	$reldata;
				
				$relresponse	=	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.$parentsku.'/variation',json_encode($reltndata));
				// print_r($relresponse);die;
				if($relresponse){
					$res1  = json_decode($relresponse);
					$str = '';
					if($res1 && count($res1->errors)>0){
						$str=$str."Error(s) in Relationship for sku : ".$relid;
						foreach($res1->errors as $er){
							$str=$str."<br/>".$er;
						}
						$str=$str."<br/>";
						$_SESSION['upload_product_error'][] = $str;
					}
				}
			}
		}
		//fclose($fp);
	}
	
	/**
	 * perform action after sku status acknowledge
	 */
	public function after_sku_status_acknowledge(){
		
		$this->errorflag = 'true';
		$status = 'Acknowledged';
		$currentid = $this->currentid;
		global $wpdb;
		$table_name = $wpdb->prefix.'jet_file_info';
		$qry = "UPDATE `$table_name` SET `status`= '$status' WHERE id = '$currentid';";
		$wpdb->query($qry);
		
		$fileid 				=	$this->uploadfileid ;
		$jdata['jet_file_id'] = $fileid;
		$responsesku = $this->fileUploadHelper->CGetRequest('/files/'.$jdata['jet_file_id']);
		$resvaluesku = json_decode($responsesku);
		// print_r($resvaluesku);die;
		
		if(isset($resvaluesku->error_excerpt) || $resvaluesku->status == 'Processed with errors' )
		{
			$jetfileid = $resvaluesku->jet_file_id;
			$jetfilename = $resvaluesku->file_name;
			$jetfiletype = $resvaluesku->file_type;
			$jetfilestatus = $resvaluesku->status;
			$errorexcerpt = $resvaluesku->error_excerpt;

			if(is_array($errorexcerpt))
			{
				foreach($errorexcerpt as $array_index=>$error_reason)
				{
					$error_reasons[] = $error_reason;
				}
			}
			//$jetfileerror = json_encode($error_reasons);
			$jetfileerror = addslashes(json_encode($error_reasons));

			global $wpdb;
			$table_name = $wpdb->prefix.'jet_errorfile_info';
			$qry = "INSERT INTO `$table_name` ( `jet_file_id`, `file_name`, `file_type`, `status`, `error`) VALUES ('".$jetfileid."', '".$jetfilename."', '".$jetfiletype."', '".$jetfilestatus."', '".$jetfileerror."');";
			$wpdb->query($qry);
			$_SESSION['upload_product_error'][] 	= $resvalueprice;
			$_SESSION['upload_product_error'][] =  __('Error in Sku file ,please check Sku files','woocommerce-jet-integration');
			unset($resvaluesku);
		}
	}
	
	/**
	 * perform action after price status acknowledge
	 */
	
	public function after_price_status_acknowledge(){
		
		$status 	= 	'Acknowledged';
		global $wpdb;
		$currentid		= 	$this->currentid;
		$table_name 	= 	$wpdb->prefix.'jet_file_info';
		$qry 			= 	"UPDATE `$table_name` SET `status`= '$status' WHERE id = '$currentid';";
		$wpdb->query($qry);

		$fileid 				=	$this->uploadfileid ;
		$jdata['jet_file_id'] 	= $fileid;
		$responseprice 			= $this->fileUploadHelper->CGetRequest('/files/'.$jdata['jet_file_id']);
		// print_r($responseprice);die;
		$resvalueprice 			= json_decode($responseprice);
		
		
		if(isset($resvalueprice->error_excerpt) || $resvalueprice->status == 'Processed with errors' )
		{
			
			
			$jetfileid 		= 	$resvalueprice->jet_file_id;
			$jetfilename 	= 	$resvalueprice->file_name;
			$jetfiletype 	= 	$resvalueprice->file_type;
			$jetfilestatus 	= 	$resvalueprice->status;
			$errorexcerpt 	= 	$resvalueprice->error_excerpt;

			if(is_array($errorexcerpt))
			{
				foreach($errorexcerpt as $array_index=>$error_reason)
				{
					$error_reasons[] = $error_reason;
				}
			}

			$jetfileerror = addslashes(json_encode($error_reasons));

			global $wpdb;
			$table_name 	= 	$wpdb->prefix.'jet_errorfile_info';
			$qry 			= 	"INSERT INTO `$table_name` ( `jet_file_id`, `file_name`, `file_type`, `status`, `error`) VALUES ('".$jetfileid."', '".$jetfilename."', '".$jetfiletype."', '".$jetfilestatus."', '".$jetfileerror."');";

			$wpdb->query($qry);
			$_SESSION['upload_product_error'][] 	=  __('Error in price file,please Check error file','woocommerce-jet-integration');
			$_SESSION['upload_product_error'][] 	= $resvalueprice;
			$this->errorflag = 'true';
			unset($resvalueprice);
		}	
		
	}
	
	/**
	 * Perform action after acknowledge status of inventory
	 */
	public function after_inventory_status_acknowlegde(){

		$fileid 				=	$this->uploadfileid ;
		$jdata['jet_file_id'] 	= 	$fileid;
		$responseinvent 		= 	$this->fileUploadHelper->CGetRequest('/files/'.$jdata['jet_file_id']);
		// print_r($responseinvent);die;
		$resvalueinvent 		= 	json_decode($responseinvent);
		if(isset($resvalueinvent->error_excerpt) || $resvalueinvent->status == 'Processed with errors' )
		{
			$jetfileid 		= 	$resvalueinvent->jet_file_id;
			$jetfilename 	= 	$resvalueinvent->file_name;
			$jetfiletype 	= 	$resvalueinvent->file_type;
			$jetfilestatus 	= 	$resvalueinvent->status;
			$errorexcerpt 	= $resvalueinvent->error_excerpt;
			if(is_array($errorexcerpt))
			{
				foreach($errorexcerpt as $array_index=>$error_reason)
				{
					$error_reasons[] = $error_reason;
				}
			}

			$jetfileerror = addslashes(json_encode($error_reasons));

			global $wpdb;
			$table_name 	= 	$wpdb->prefix.'jet_errorfile_info';
			$qry 			= 	"INSERT INTO `$table_name` ( `jet_file_id`, `file_name`, `file_type`, `status`, `error`) VALUES ('".$jetfileid."', '".$jetfilename."', '".$jetfiletype."', '".$jetfilestatus."', '".$jetfileerror."');";

			$wpdb->query($qry);
			$_SESSION['upload_product_error'][] 	= $resvalueprice;
			$_SESSION['upload_product_error'][] =  __('error in inventory file please go to error files and resubmit file','woocommerce-jet-integration');
			$this->errorflag = 'true';
			exit;
		}	
	}
	
	/**
	 * Perfom after shipping action check
	 */
	public function after_shipping_exception_acknowledge_check(){
		
		$fileid 				= 	$this->uploadfileid;
		$jdata['jet_file_id'] 	= 	$fileid;
		$responseexcptn 		= 	$this->fileUploadHelper->CGetRequest('/files/'.$jdata['jet_file_id']);
		$resvalueexcptn 		= 	json_decode($responseexcptn);
		
		if(isset($resvalueexcptn->error_excerpt) || $resvalueexcptn->status == 'Processed with errors' )
		{
			$jetfileid 		= 	$resvalueexcptn->jet_file_id;
			$jetfilename 	= 	$resvalueexcptn->file_name;
			$jetfiletype 	= 	$resvalueexcptn->file_type;
			$jetfilestatus 	= 	$resvalueexcptn->status;
			$errorexcerpt 	= 	$resvalueexcptn->error_excerpt;
			if(is_array($errorexcerpt))
			{
				foreach($errorexcerpt as $array_index=>$error_reason)
				{
					$error_reasons[] = $error_reason;
				}
			}

			$jetfileerror = addslashes(json_encode($error_reasons));

			global $wpdb;
			$table_name 	= 	$wpdb->prefix.'jet_errorfile_info';
			$qry 			= 	"INSERT INTO `$table_name` ( `jet_file_id`, `file_name`, `file_type`, `status`, `error`) VALUES ('".$jetfileid."', '".$jetfilename."', '".$jetfiletype."', '".$jetfilestatus."', '".$jetfileerror."');";

			$wpdb->query($qry);
			$_SESSION['upload_product_error'][] 	= $resvalueprice;
			$_SESSION['upload_product_error'][] =  __('Error in shipping exception file please go to error files and resubmit file','woocommerce-jet-integration');
			$this->errorflag = 'true';
			exit;
		}
	}
	
	/**
	 * perforn after check acknowledge status for return exception 
	 */
	public function after_return_exception_check(){
		
		$fileid 				= 	$this->uploadfileid;
		$jdata['jet_file_id'] 	= 	$fileid;
		$responseexcptn 		= 	$this->fileUploadHelper->CGetRequest('/files/'.$jdata['jet_file_id']);
		$resvalueexcptn 		= 	json_decode($responseexcptn);
		
		if(isset($resvalueexcptn->error_excerpt) || $resvalueexcptn->status == 'Processed with errors' )
		{
			$jetfileid 		= 	$resvalueexcptn->jet_file_id;
			$jetfilename 	= 	$resvalueexcptn->file_name;
			$jetfiletype 	= 	$resvalueexcptn->file_type;
			$jetfilestatus 	= 	$resvalueexcptn->status;
			$errorexcerpt 	= 	$resvalueexcptn->error_excerpt;
			if(is_array($errorexcerpt))
			{
				foreach($errorexcerpt as $array_index=>$error_reason)
				{
					$error_reasons[] = $error_reason;
				}
			}

			$jetfileerror = addslashes(json_encode($error_reasons));

			global $wpdb;
			$table_name 	= 	$wpdb->prefix.'jet_errorfile_info';
			$qry 			= 	"INSERT INTO `$table_name` ( `jet_file_id`, `file_name`, `file_type`, `status`, `error`) VALUES ('".$jetfileid."', '".$jetfilename."', '".$jetfiletype."', '".$jetfilestatus."', '".$jetfileerror."');";

			$wpdb->query($qry);
			$_SESSION['upload_product_error'][] 	= $resvalueprice;
			$_SESSION['upload_product_error'][] =  __('error in return exception file please go to error files and resubmit file','woocommerce-jet-integration');
			$this->errorflag = 'true';
			exit;
		}
	}
	
	/**
	 * upload sku file
	 * @param unknown $compressed_file_path
	 * @param unknown $file_name
	 * @param unknown $file_type
	 */
	public function uploadSkuFile($compressed_file_path,$file_name,$file_type){
		
		$commaseperatedids = $this->commaseperatedids ;
		$response = $this->fileUploadHelper->CGetRequest('/files/uploadToken');
		$data 	  = json_decode($response);
		$fileid	  = $data->jet_file_id;
		$this->uploadfileid = $fileid; 
		$tokenurl =	$data->url;
		
		$text = array('woocommerce_batch_info'=>$commaseperatedids,'jet_file_id'=>$fileid,'token_url'=>$tokenurl,'file_name'=>$file_name,'file_type'=>$file_type,'status'=>'unprocessed');
		
		$status = 'unprocessed';
		
		global $wpdb;
		$table_name = $wpdb->prefix.'jet_file_info';
		$qry = "INSERT INTO `$table_name` (`woocommerce_batch_info`, `jet_file_id`, `token_url`, `file_name`,`file_type`,`status`) VALUES
		('".$commaseperatedids."', '".$fileid."', '".$tokenurl."', '".$file_name."', '".$file_type."','".$status."');";
		$wpdb->query($qry);
		
		$currentid = $wpdb->insert_id;
		$this->currentid = $currentid;
		$reponse = $this->fileUploadHelper->uploadFile($compressed_file_path,$data->url);
		$postFields='{"url":"'.$data->url.'","file_type":"'.$file_type.'","file_name":"'.$file_name.'"}';
		
		$response = $this->fileUploadHelper->CPostRequest('/files/uploaded',$postFields);
		$data2  = json_decode($response);
		// print_r($data2);die;

		return $data2->status;		
		
	}
	
	/**
	 * Upload price
	 * @param unknown $compressed_file_path1
	 * @param unknown $file_name1
	 * @param unknown $file_type1
	 */
	public function UploadPriceFile($compressed_file_path1,$file_name1,$file_type1){
		
		$commaseperatedids = $this->commaseperatedids ;
		$response 		= 	$this->fileUploadHelper->CGetRequest('/files/uploadToken');
		$data 			= 	json_decode($response);
		$fileid			=	$data->jet_file_id;
		$this->uploadfileid = $fileid;
		$tokenurl		=	$data->url;
		
		$this->errorflag = 'false';
		
		$status = 'unprocessed';

		global $wpdb;
		$table_name 	= 	$wpdb->prefix.'jet_file_info';
		$qry 			= 	"INSERT INTO `$table_name` (`woocommerce_batch_info`, `jet_file_id`, `token_url`, `file_name`,`file_type`,`status`) VALUES
		('".$commaseperatedids."', '".$fileid."', '".$tokenurl."', '".$file_name1."', '".$file_type1."','".$status."');";
		$wpdb->query($qry);
		
		$currentid			= 	$wpdb->insert_id;
		$this->currentid	= 	$currentid;
		
		$reponse 			= 	$this->fileUploadHelper->uploadFile($compressed_file_path1,$data->url);
		$postFields			=	'{"url":"'.$data->url.'","file_type":"'.$file_type1.'","file_name":"'.$file_name1.'"}';
		$responseprice 		= 	$this->fileUploadHelper->CPostRequest('/files/uploaded',$postFields);
		$pricedata  		= 	json_decode($responseprice);
		// print_r($pricedata);die;
		return $pricedata->status;
	}
	
	/**
	 * Upload Inventory File
	 * @param unknown $compressed_file_path2
	 * @param unknown $file_name2
	 * @param unknown $file_type2
	 */
	public function UploadInventoryFile($compressed_file_path2,$file_name2,$file_type2){
		
		$commaseperatedids = $this->commaseperatedids ;
		$response 	= 	$this->fileUploadHelper->CGetRequest('/files/uploadToken');
		$data 		= 	json_decode($response);
		$fileid		=	$data->jet_file_id;
		$this->uploadfileid = $fileid;
		$tokenurl	=	$data->url;
		
		global $wpdb;
		$status 		= 	'unprocessed';
		$table_name 	= 	$wpdb->prefix.'jet_file_info';
		$qry 			= 	"INSERT INTO `$table_name` (`woocommerce_batch_info`, `jet_file_id`, `token_url`, `file_name`,`file_type`,`status`) VALUES
		('".$commaseperatedids."', '".$fileid."', '".$tokenurl."', '".$file_name2."', '".$file_type2."','".$status."');";
		$wpdb->query($qry);
		
		$currentid			= 	$wpdb->insert_id;
		$this->currentid	= 	$currentid;
		$reponse			= 	$this->fileUploadHelper->uploadFile($compressed_file_path2,$data->url);
		$postFields			=	'{"url":"'.$data->url.'","file_type":"'.$file_type2.'","file_name":"'.$file_name2.'"}';
		$responseinventry 	= 	$this->fileUploadHelper->CPostRequest('/files/uploaded',$postFields);
		$invetrydata		=	json_decode($responseinventry);
		// print_r($invetrydata);die;
		return $invetrydata->status;
	}
	
	/**
	 * Upload shipping exception file
	 * @param unknown $compressed_file_path3
	 * @param unknown $file_name3
	 * @param unknown $file_type3
	 */
	public function UploadShippingException($compressed_file_path3,$file_name3,$file_type3){
		
		$commaseperatedids = $this->commaseperatedids ;
		$response 	= 	$this->fileUploadHelper->CGetRequest('/files/uploadToken');
		$data 		= 	json_decode($response);
		$fileid		=	$data->jet_file_id;
		$this->uploadfileid = $fileid;
		$tokenurl	=	$data->url;
		
		global $wpdb;
		$status 		= 	'unprocessed';
		$table_name 	= 	$wpdb->prefix.'jet_file_info';
		$qry 			= 	"INSERT INTO `$table_name` (`woocommerce_batch_info`, `jet_file_id`, `token_url`, `file_name`,`file_type`,`status`) VALUES
		('".$commaseperatedids."', '".$fileid."', '".$tokenurl."', '".$file_name3."', '".$file_type3."','".$status."');";
		$wpdb->query($qry);

		$currentid				= 	$wpdb->insert_id;
		$this->currentid		= 	$currentid;
		$reponse				= 	$this->fileUploadHelper->uploadFile($compressed_file_path3,$data->url);
		$postFields				=	'{"url":"'.$data->url.'","file_type":"'.$file_type3.'","file_name":"'.$file_name3.'"}';
		$responseshipexcptn 	= 	$this->fileUploadHelper->CPostRequest('/files/uploaded',$postFields);
		$excptn_data			=	json_decode($responseshipexcptn);
		
		return $excptn_data->status;
	}
	
	/**
	 * Upload return exception 
	 * @param unknown $compressed_file_path4
	 * @param unknown $file_name4
	 * @param unknown $file_type4
	 */
	public function UploadReturnException($compressed_file_path4,$file_name4,$file_type4){
		
		$commaseperatedids = $this->commaseperatedids ;
		$response 	= 	$this->fileUploadHelper->CGetRequest('/files/uploadToken');
		$data 		= 	json_decode($response);
		$fileid		=	$data->jet_file_id;
		$this->uploadfileid = $fileid;
		$tokenurl	=	$data->url;
		
		global $wpdb;
		$status 		= 	'unprocessed';
		$table_name 	= 	$wpdb->prefix.'jet_file_info';
		$qry 			= 	"INSERT INTO `$table_name` (`woocommerce_batch_info`, `jet_file_id`, `token_url`, `file_name`,`file_type`,`status`) VALUES
		('".$commaseperatedids."', '".$fileid."', '".$tokenurl."', '".$file_name4."', '".$file_type4."','".$status."');";

		$wpdb->query($qry);
		
		$currentid				= 	$wpdb->insert_id;
		$this->currentid		= 	$currentid;
		$reponse				= 	$this->fileUploadHelper->uploadFile($compressed_file_path4,$data->url);
		$postFields				=	'{"url":"'.$data->url.'","file_type":"'.$file_type4.'","file_name":"'.$file_name4.'"}';
		$responsertrnexcptn 	= 	$this->fileUploadHelper->CPostRequest('/files/uploaded',$postFields);
		$rtrn_excptn_data		=	json_decode($responsertrnexcptn);
		
		return $rtrn_excptn_data->status;
	}
	/**
	 * 
	 * @param unknown $pid
	 * @return Ambigous <unknown, unknown, mixed>|boolean
	 */
	public function fetchProductDetail($pid){
		
		if(WC()->version < "3.0.0")
		{
			$_product 		= 	get_product($pid);
			if(isset($_product)){

				$productData	=	array();

				$commonDetails	=	$this->fetchCommonProductDetails($_product,$productData);

				if(!empty($commonDetails))
					$productData = $commonDetails;

				if($_product->is_type('variable')){
				// print_r("hsdvhjsd");die;
					$productData			=	$this->fetchVariationDetails($_product,$productData);
					$productData['type']	=	'variable';
				}
				elseif ($_product->is_type('simple')){

				//get necessary details of product 
					$simpleData	=	$this->fetchSimpleProductDetails($_product,$productData);
					if(!empty($simpleData)){

						$productData 			= 	$simpleData;
						$productData['type']	=	'simple';
					}

				//fetch return settings for product
					$returnSettings  =	$this->fetchReturnSettings($_product,$productData);
					if(!empty($returnSettings))
						$productData = $returnSettings;

				//fetch shipping settings 
					$shippingSettings	=	$this->fetchShippingSettings($_product,$productData);
					if(!empty($shippingSettings))
						$productData = $shippingSettings;

				}
			// print_r($productData);die;
				return $productData;
			}else{

				return false;
			}
		}
		else
		{
			$_product= wc_get_product($pid);
			$ced_product_type=$_product->get_type();
			if(isset($_product)){
				
				$productData	=	array();
				
				$commonDetails	=	$this->fetchCommonProductDetails($_product,$productData);
			
				if(!empty($commonDetails))
					$productData = $commonDetails;

				if($ced_product_type=='variable'){
				// print_r("hsdvhjsd");die;
					$productData			=	$this->fetchVariationDetails($_product,$productData,$pid);
					$productData['type']	=	'variable';
				}
				elseif ($_product->is_type('simple')){
					// print_r("expression");
				//get necessary details of product 
					$simpleData	=	$this->fetchSimpleProductDetails($_product,$productData);
					if(!empty($simpleData)){

						$productData 			= 	$simpleData;
						$productData['type']	=	'simple';
					}

				//fetch return settings for product
					$returnSettings  =	$this->fetchReturnSettings($_product,$productData);
					if(!empty($returnSettings))
						$productData = $returnSettings;

				//fetch shipping settings 
					$shippingSettings	=	$this->fetchShippingSettings($_product,$productData);
					if(!empty($shippingSettings))
						$productData = $shippingSettings;

				}
			// print_r($productData);die;
				return $productData;
			}else{

				return false;
			}
		}
		
	}
	
	public function fetchVariationDetails($_product,$productData,$pid=null){
		
		if(isset($pid))
		{
			$product_parent_id=$pid;
		}
		$variations			=	$_product->get_available_variations();
		if(!empty($variations) && count($variations)){
			
			foreach($variations as $variation){
				// print_r($variation);
				if(WC()->version < "3.0.0"){
					$productData[$variation[variation_id]]		=	$this->fetchProfileRelatedData($variation[variation_id],array(),$_product->id,$variation);
				}
				else
				{
					// print_r($variation[variation_id]);
					$productData[$variation[variation_id]]		=	$this->fetchProfileRelatedData($variation[variation_id],array(),$product_parent_id,$variation);
				}
				//$productData[$variation[variation_id]]	=	$this->fetchProfileRelatedData($variation[variation_id] ,array('gallery_images' => $productData['gallery_images']) , $_product->id);
				
				$shippingSettings = '';
				//product id
				$productData[$variation[variation_id]]['id']					=	$variation[variation_id];
			// print_r($productData);
				//product name
				$productData[$variation[variation_id]]['name']					=	get_post_meta( $variation['variation_id'], '_jet_title', true );
				
				//start product price processing according to condition
				$productData[$variation[variation_id]]['price']					=	$this->fetchConditionalPrice((object)array(),$variation['variation_id']);
				//end product price processing
				
				//variable product conditional description.
				$productData[$variation[variation_id]]['desc']					=	!empty($variation['variation_description']) ? substr( strip_tags($variation['variation_description']), 0,1998) : substr($_product->post->post_content, 0,1998);
				
				//get jet standard code
				$productData[$variation[variation_id]]['asin']			  		=	get_post_meta($variation['variation_id'],'_jet_asin', true);
				//end asin
				
				//standard product code type
				$productData[$variation[variation_id]]['standardCode_type']		=	get_post_meta($variation['variation_id'],'standardCodetype',true);
				//end 
				
				$productData[$variation[variation_id]]['standardCode_value']	=	get_post_meta($variation['variation_id'],'standardCodeVal',true);
				
				//start product stock according to condition
				$productData[$variation[variation_id]]['stock']					=	 $this->fetchConditionalStock((object)array(),$variation['variation_id']);
				//end stock
				
				//extra product settings
				$productData[$variation[variation_id]]['jet_mfr_part_number']		 			= 	 get_post_meta($variation['variation_id'], 'jet_mfr_part_number', true);
				$productData[$variation[variation_id]]['number_units_for_price_per_unit']		= 	 get_post_meta($variation['variation_id'], 'number_units_for_price_per_unit', true);
				$productData[$variation[variation_id]]['type_of_unit_for_price_per_unit']		=	 get_post_meta($variation['variation_id'], 'type_of_unit_for_price_per_unit', true);
				$productData[$variation[variation_id]]['shipping_weight_pounds'] 			 	= 	 get_post_meta($variation['variation_id'], '_weight', true);
				//$productData[$variation[variation_id]]['package_length'] 					 	= 	 get_post_meta($variation['variation_id'], 'package_length', true);
				//$productData[$variation[variation_id]]['package_width']	 					= 	 get_post_meta($variation['variation_id'], 'package_width', true);
				//$productData[$variation[variation_id]]['package_height']						= 	 get_post_meta($variation['variation_id'], 'package_height', true);
				//$productData[$variation[variation_id]]['prop_65']								= 	 get_post_meta($variation['variation_id'], 'prop_65', true);
				$productData[$variation[variation_id]]['bullets_1']					 			= 	 get_post_meta($variation['variation_id'], '_bullet_1', true);
				$productData[$variation[variation_id]]['bullets_2']					 			= 	 get_post_meta($variation['variation_id'], '_bullet_2', true);
				$productData[$variation[variation_id]]['bullets_3']					 			= 	 get_post_meta($variation['variation_id'], '_bullet_3', true);
				$productData[$variation[variation_id]]['bullets_4']					 			= 	 get_post_meta($variation['variation_id'], '_bullet_4', true);
				$productData[$variation[variation_id]]['bullets_5']					 			= 	 get_post_meta($variation['variation_id'], '_bullet_5', true);
				/*
				 * amazon
				 *   */
				
				$productData[$variation[variation_id]]['amazon_item_type_keyword']				=	get_post_meta($variation['variation_id'], 'amazon_item_type_keyword', true);
				/*
				 * jetbackorder  */
				$productData[$variation[variation_id]]['jetbackorder']							=	get_post_meta($variation['variation_id'], 'jetbackorder', true);
				//$productData[$variation[variation_id]]['cpsia_cautionary_statements'] 			= 	 get_post_meta($variation['variation_id'], 'cpsia_cautionary_statements', true);
				$productData[$variation[variation_id]]['exclude_from_fee_adjustments']			= 	 get_post_meta($variation['variation_id'], 'exclude_from_fee_adjustments', true);
				
				$temp_weight			=	$variation['weight'];
				$product_weight			=	wc_get_weight($temp_weight, 'lbs');
				$dimensions				=	$variation['dimensions'];
				$brokn_array			=	explode('x',$dimensions);
				if(count($brokn_array)==3){
					$temp_width				=	$brokn_array[1];
					$product_width			=	wc_get_dimension($temp_width, 'in');
					$temp_height			=	$brokn_array[2];
					$product_height			=	wc_get_dimension($temp_height, 'in');
					$exp_lngth				=	explode(' ', $brokn_array[0]);
					$temp_length			=	$exp_lngth[0];
					$product_length			=	wc_get_dimension($temp_length, 'in');
				}
				
				/* $productData[$variation[variation_id]]['weight_lbs']		= 	$product_weight;
				if(!empty($product_width)){
					$productData[$variation[variation_id]]['height_in']	= 	$product_height;
					$productData[$variation[variation_id]]['widht_in']	= 	$product_width;
					$productData[$variation[variation_id]]['length_in']	= 	$product_length;
				}
				 */
				//variation image
				if(WC()->version <"3.0.0"){
					$productData[$variation[variation_id]]['jet_product_image_link']	=	$variation['image_src'];
				}
				else
				{
					$productData[$variation[variation_id]]['jet_product_image_link']	=	$variation['image']['src'];
				}
				
				//fetch return settings for product
				$returnSettings  =	$this->fetchReturnSettings($_product,array(),$variation[variation_id]);
				// print_r($returnSettings);die;
				if(!empty($returnSettings))
					$productData[$variation[variation_id]]['return_settings'] = $returnSettings;
				
				
				//fetch shipping settings
				$shippingSettings	=	$this->fetchShippingSettings($_product,array(),$variation[variation_id]);
				if(!empty($shippingSettings))
					$productData[$variation[variation_id]]['shipping_settings'] = $shippingSettings;
				
				//$productData[$variation[variation_id]]['ships_alone']							= 	 get_post_meta($variation['variation_id'], 'ships_alone', true);
			}
			//unset($productData['gallery_images']);
			return $productData;
		}
	}
	
	/**
	 * 
	 * @param unknown $_product
	 * @param unknown $productData
	 */
	public function fetchShippingSettings($_product,$productData,$variable_id=null){
		//set price,stock and quantity for multiplefullfillmet
		
		if($variable_id != null){
			if(WC()->version < "3.0.0"){
				$_product->id = $variable_id;
			}
			else{
				$_product->set_id($variable_id);
			}
		}
		
		$jet_node_id = get_option('jet_node_id');
		$jet_node_id = json_decode($jet_node_id);

		foreach($jet_node_id as $key => $value){
			$price 	  = get_post_meta($_product->id, 'p_'.$value, true);
			$stock    = get_post_meta($_product->id, 's_'.$value, true);

			$all_upload_product_on_jet['p_'.$value] 	  = $price;
			$all_upload_product_on_jet['s_'.$value] 	  = $stock;

			//shipping exception
			//shipping settings for jet
			$jet_service_level             		=	'';
			$jet_shipping_methods				=	'';
			$jet_override_type					=	'';
			$jet_shipping_charge_amount			=	'';
			$jet_shipping_type					=	'';
			$jet_service_level           		=	get_post_meta($_product->id, 'jet_service_level_'.$value, true );
			$jet_shipping_methods				=	get_post_meta($_product->id, 'jet_shipping_methods_'.$value, true);
			$jet_override_type					=	get_post_meta($_product->id, 'jet_override_type_'.$value, true );
			$jet_shipping_charge_amount			=	get_post_meta($_product->id, 'jet_shipping_charge_amount_'.$value,true );
			$jet_shipping_type					=	get_post_meta($_product->id, 'jet_shipping_exception_type_'.$value,true );

			$check_enable = '';
			$check_enable = get_post_meta($_product->id,'sipping_exception_settings_'.$value, true );

			//echo $check_enable;
			if($check_enable == 'yes'){
				if(!empty($jet_service_level) && $jet_service_level != 'choose'){
					$shipng_exception_arr['fulfillment_node_id'] 								= 	$value;
					$shipng_exception_arr['shipping_exceptions'][0]['service_level'] 			= 	$jet_service_level;
					$shipng_exception_arr['shipping_exceptions'][0]['override_type'] 			= 	$jet_override_type;
					$shipng_exception_arr['shipping_exceptions'][0]['shipping_charge_amount'] 	= 	(float)$jet_shipping_charge_amount;
					$shipng_exception_arr['shipping_exceptions'][0]['shipping_exception_type'] 	= 	$jet_shipping_type;

					$shipng_exception_arr['shipping_exceptions'][1]['shipping_method'] 			= 	$jet_shipping_methods;
					$shipng_exception_arr['shipping_exceptions'][1]['override_type'] 			= 	$jet_override_type;
					$shipng_exception_arr['shipping_exceptions'][1]['shipping_charge_amount'] 	= 	(float)$jet_shipping_charge_amount;
					$shipng_exception_arr['shipping_exceptions'][1]['shipping_exception_type'] 	= 	$jet_shipping_type;

				}else{
					$shipng_exception_arr['fulfillment_node_id'] 									= 	$value;
					$shipng_exception_arr['shipping_exceptions'][0]['shipping_method'] 				= 	$jet_shipping_methods;
					$shipng_exception_arr['shipping_exceptions'][0]['override_type'] 				= 	$jet_override_type;
					$shipng_exception_arr['shipping_exceptions'][0]['shipping_charge_amount'] 		= 	(float)$jet_shipping_charge_amount;
					$shipng_exception_arr['shipping_exceptions'][0]['shipping_exception_type']	 	= 	$jet_shipping_type;
				}
				$ship_array[] = $shipng_exception_arr;
				unset($shipng_exception_arr);
			}//check enable end

		}
		if(!empty($ship_array)){
			$productData['ship_excp_detail'] = $ship_array;
		}	
		
		return $productData;
	} 
	/**
	 * 
	 * @param  $_product 
	 * @param unknown $productData
	 * @return multitype:number mixed
	 */
	
	public function fetchReturnSettings($_product,$productData,$variable_id=null){
		// echo $variable_id;
		if($variable_id != null){
			if(WC()->version < "3.0.0")
			{
				$_product->id=$variable_id; 
			}
			else{
				$_product->set_id($variable_id); 
			}
		}
		$return_excptn_check		=	get_post_meta($_product->id,'return_exception_setting', true );
		// print_r($return_excptn_check);
		$return_excptn_array		=	array();
		if($return_excptn_check == 'yes')
		{
			
			$return_excptn_array['time_to_return']				=	(int)get_post_meta($_product->id, 'jet_time_to_return', true );
			$return_excptn_array['return_location_ids']			=	json_decode(get_post_meta($_product->id,'return_id',true));
			$return_excptn_array['return_shipping_methods']		=	json_decode(get_post_meta($_product->id, '_return_shipping_methods', true ));
			if(!empty($return_excptn_array)){echo "40-->";
			$productData['return_excptn_data']				=	$return_excptn_array;

		}
		unset($return_excptn_array);
		return $productData;
	}
	return;


}
public function fetchCommonProductDetails($_product,$productData){

		// start fetching the gallery images if any
	$attachment_ids 			= 	$_product->get_gallery_attachment_ids();
	$alternate_image_urls 		= 	array();
	$image_counter				=	1;

	if(count($attachment_ids))
	{
		foreach( $attachment_ids as $attachment_id )
		{
			if($image_counter<9)
			{
					//Get URL of Gallery Images - default wordpress image sizes
				$alternate_image_urls[] = wp_get_attachment_url( $attachment_id );
			}
			$image_counter++;
		}
	}

	if(count($alternate_image_urls))
		$productData['gallery_images']	=	$alternate_image_urls;

		/* $productCats	= 	get_the_terms($_product->id, "product_cat");
		$mappedIDs		=	get_option('cedWooJetMapping',true);
		
		if(is_array($mappedIDs)){
			
			$catArray	=	array();
			foreach($mappedIDs as $woocatid => $jetcatId){
					
				$catArray[]	=	$woocatid;
			}
			
			if(!empty($catArray) && is_array($catArray)){
				
				foreach ($productCats as $index	=>	$catObject){
					
					if(in_array($catObject->term_id, $catArray)){
						
						$jetSelectedNodeID	=	$mappedIDs[$catObject->term_id];
					}
				}
			}
		} */
		
		//Start product Brand information.
		
		//End product Brand Information fetching.
		
		return $productData;
	}
	
	/**
	 * copied from stackoverflow (airdrumz)
	 * @param unknown $pid
	 */
	public function getProductAttrAllData($pid){
		
		$allAttrData	=	array();
		
		$product	=	get_product($pid);
		
		$attributes	=	$product->get_attributes();
		
		foreach($attributes as $attr=>$attr_deets){

			$attribute_label = wc_attribute_label($attr);
			
			if ( isset( $attributes[ $attr ] ) || isset( $attributes[ 'pa_' . $attr ] ) ) {

				$attribute = isset( $attributes[ $attr ] ) ? $attributes[ $attr ] : $attributes[ 'pa_' . $attr ];
				
				$exploded =	explode('pa_',$attr);
				if(count($exploded)==2)
				{
					$tempName	=	$exploded[1];
				}else{
					$tempName   =   $attr;
				}
				if ( $attribute['is_taxonomy'] ) {

					$allAttrData[$tempName] = implode( ', ', wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) ) );

				} else {

					$allAttrData['name'] = $attribute['value'];
				}

			}
		}
		return $allAttrData;
	}
	
	public function fetchProfileRelatedData($pid,$productData,$variable=null,$variation=array()){
		// print_r($variation);
		if($variable){
			$selectedNodeID		=	get_post_meta($pid,$pid.'_selectedCatAttr',true);
			$profileId			=	get_post_meta($variable,'productProfileID',true);
		}else{
			
			$selectedNodeID		=	get_post_meta($pid,'selectedCatAttr',true);
			$profileId			=	get_post_meta($pid,'productProfileID',true);
		}
		
		if(!empty($selectedNodeID)){

			$mappedAttributes		=	get_option($selectedNodeID.'_linkedAttributes',false);
			// print_r($mappedAttributes);
			if($mappedAttributes){

				$mappedAttributes	=	json_decode($mappedAttributes);
				if(is_array($mappedAttributes)){

					$jetAttrInfo[$selectedNodeID]	=	$this->modelAction->fetchAttrDetails($mappedAttributes);
				}
			}
		}
		
		$productData['jet_cat_id']		=	$selectedNodeID;
		// print_r($productData);die("dfd");
		
		//change by me 
		$all_prodile_ids = $this->modelAction->get_all_product_Ids();
		// print_r($all_prodile_ids);
		//if not profile set
		if(!empty($profileId) && in_array($profileId,$all_prodile_ids)){
			
			$profileCondtns	=	$this->modelAction->getProfileDetail($profileId);

			$profiledata		= 	$profileCondtns[0];

			$name 				= 	$profiledata->profile_name;
			$categoryAttrdata	= 	json_decode($profiledata->profile_category);
			// print_r($categoryAttrdata);die("ssafa");
			$categoryAttrdata	=	(array)$categoryAttrdata;
			
			$all_item_specific  = 	json_decode($profiledata->item_specific);

			$item_specific 		= 	$all_item_specific->item_specific;

			$country_manuf		= 	$item_specific->country_manufacturer;
			
			$attributesData	=	array();
			
			if(!empty($jetAttrInfo) && count($jetAttrInfo)):
				foreach($jetAttrInfo as $jetNode => $mappedCAT):
					
					foreach($mappedCAT as $attrARRAY):

						$attrObject = $attrARRAY[0];
					$tempName	=	$jetNode."_".$attrObject->jet_attr_id;
					$tempID		=	$attrObject->jet_attr_id;
							// print_r($tempID);

					if($variable){
						// print_r($variation);
						$productattrData	=	$this->formatVariationAttrDetail($variation[attributes]);

						if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit)) ){
								//	$attributesData[$tempID]			=	$categoryAttrdata[$tempName] ? $categoryAttrdata[$tempName] : $categoryAttrdata[$tempName.'_attributeMeta'] ? $productattrData[$categoryAttrdata[$tempName.'_attributeMeta']] : get_post_meta($pid,$tempName,true);
							if(!empty($categoryAttrdata[$tempName]) && $categoryAttrdata[$tempName] != ''){

								$attributesData[$tempID]	=	$categoryAttrdata[$tempName];
							}elseif (!empty($categoryAttrdata[$tempName.'_attributeMeta']) && $categoryAttrdata[$tempName.'_attributeMeta'] != ''){

								$attributesData[$tempID]	=	$productattrData[$categoryAttrdata[$tempName.'_attributeMeta']];
							}else{

								$attributesData[$tempID]	=	get_post_meta($pid,$tempName,true);
							}
							$attributesData[$tempID.'_unit']	=	$categoryAttrdata[$tempName.'_unit'];
						}
						else{
							if(!empty($categoryAttrdata[$tempName]) && $categoryAttrdata[$tempName] != 'none'){

								$attributesData[$tempID]	=	$categoryAttrdata[$tempName];

							}else{

								if(!empty($categoryAttrdata[$tempName.'_attributeMeta']) && $categoryAttrdata[$tempName.'_attributeMeta'] != 'none'){

									$attributesData[$tempID]	=	$productattrData[$categoryAttrdata[$tempName.'_attributeMeta']];

								}else{

									$attributesData[$tempID]	=	get_post_meta($pid,$tempName,true);
								// print_r($tempID);die("gj");
								}
							}
						}
					}else{

						$productattrData	=	$this->getProductAttrAllData($pid);
						
						if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit)) ){
							//	$attributesData[$tempID]			=	$categoryAttrdata[$tempName] ? $categoryAttrdata[$tempName] : $categoryAttrdata[$tempName.'_attributeMeta'] ? $productattrData[$categoryAttrdata[$tempName.'_attributeMeta']] : get_post_meta($pid,$tempName,true);

							if(!empty($categoryAttrdata[$tempName]) && $categoryAttrdata[$tempName] != ''){

								$attributesData[$tempID]	=	$categoryAttrdata[$tempName];
							}elseif (!empty($categoryAttrdata[$tempName.'_attributeMeta']) && $categoryAttrdata[$tempName.'_attributeMeta'] != ''){

								$attributesData[$tempID]	=	$productattrData[$categoryAttrdata[$tempName.'_attributeMeta']];
							}else{

								$attributesData[$tempID]	=	get_post_meta($pid,$tempName,true);
							}
							$attributesData[$tempID.'_unit']	=	$categoryAttrdata[$tempName.'_unit'];	
						}
						else{

							if(!empty($categoryAttrdata[$tempName]) && $categoryAttrdata[$tempName] != 'none'){

								$attributesData[$tempID]	=	$categoryAttrdata[$tempName];

							}else{

								if(!empty($categoryAttrdata[$tempName.'_attributeMeta']) && $categoryAttrdata[$tempName.'_attributeMeta'] != 'none'){

									$attributesData[$tempID]	=	$productattrData[$categoryAttrdata[$tempName.'_attributeMeta']];

								}else{

									$attributesData[$tempID]	=	get_post_meta($pid,$tempName,true);
								}
							}
						}
					}

					endforeach;

					endforeach;

					endif;

					$mappedCode	=	$item_specific->skuMappedWith;
					
					if(!empty($mappedCode) && $mappedCode != 'choose'){
						$productData['mappedSkuWith']	=	$mappedCode;
					}
					// print_r($pid);

			//brand details
					if(isset($item_specific->brand) && !empty($item_specific->brand)){

						$productData['jet_brand'] 			=	$item_specific->brand;
						// print_r($productData);die("Das");
					}elseif (isset($item_specific->jetbrand_attributeMeta) && !empty($item_specific->jetbrand_attributeMeta)) {

						if(in_array($item_specific->jetbrand_attributeMeta, $productattrData)){

							$productData['jet_brand'] 			=	$productattrData["$item_specific->jetbrand_attributeMeta"];
						}
						else{

							$productData['jet_brand'] 			=	get_post_meta($pid,$item_specific->jetbrand_attributeMeta,true);
						}

					}else{

						$productData['jet_brand']			=	get_post_meta($pid,'jetBrand',true);
					}

			//safety warning details
					if(isset($item_specific->safety_warning) && !empty($item_specific->safety_warning)){

						$productData['safety_warning'] 			=	$item_specific->safety_warning;

					}elseif (isset($item_specific->sw_attributeMeta) && !empty($item_specific->sw_attributeMeta)) {

				//$productData['safety_warning'] 			=	$productattrData["$item_specific->sw_attributeMeta"];
						if(in_array($item_specific->sw_attributeMeta, $productattrData)){

							$productData['safety_warning'] 			=	$productattrData["$item_specific->sw_attributeMeta"];
						}
						else{

							$productData['safety_warning'] 			=	get_post_meta($pid,$item_specific->sw_attributeMeta,true);
						}
					}else{

						$productData['safety_warning']			=	get_post_meta($pid,'safety_warning',true);
					}

			//country manufacturer details
					if(isset($item_specific->country_manufac) && !empty($item_specific->country_manufac)){

						$productData['country_manufacturer'] 			=	$item_specific->country_manufac;
					}elseif (isset($item_specific->cm_attributeMeta) && !empty($item_specific->cm_attributeMeta)) {

			//	$productData['country_manufacturer'] 			=	$productattrData["$item_specific->cm_attributeMeta"];
						if(in_array($item_specific->cm_attributeMeta, $productattrData)){

							$productData['country_manufacturer'] 			=	$productattrData["$item_specific->cm_attributeMeta"];
						}
						else{

							$productData['country_manufacturer'] 			=	get_post_meta($pid,$item_specific->cm_attributeMeta,true);
						}
					}else{

						$productData['country_manufacturer']			=	get_post_meta($pid,'jet_country',true);
					}

			//fullfillment_time details
					if(isset($item_specific->fullfillment_time) && !empty($item_specific->fullfillment_time)){

						$productData['fulfillment_time'] 			=	$item_specific->fullfillment_time;
					}elseif (isset($item_specific->ft_attributeMeta) && !empty($item_specific->ft_attributeMeta)) {

			//	$productData['fulfillment_time'] 			=	$productattrData["$item_specific->ft_attributeMeta"];
						if(in_array($item_specific->ft_attributeMeta, $productattrData)){

							$productData['fulfillment_time'] 			=	$productattrData["$item_specific->ft_attributeMeta"];
						}
						else{

							$productData['fulfillment_time'] 			=	get_post_meta($pid,$item_specific->ft_attributeMeta,true);
						}
					}else{

						$productData['fulfillment_time']			=	get_post_meta($pid,'fulfillment_time',true);
					}

			//fmap_price details
					if(isset($item_specific->map_price) && !empty($item_specific->map_price)){

						$productData['map_price'] 			=	$item_specific->map_price;
					}elseif (isset($item_specific->mp_attributeMeta) && !empty($item_specific->mp_attributeMeta)) {

			//	$productData['map_price'] 			=	$productattrData["$item_specific->mp_attributeMeta"];
						if(in_array($item_specific->mp_attributeMeta, $productattrData)){

							$productData['map_price'] 			=	$productattrData["$item_specific->mp_attributeMeta"];
						}
						else{

							$productData['map_price'] 			=	get_post_meta($pid,$item_specific->mp_attributeMeta,true);
						}
					}else{

						$productData['map_price']			=	get_post_meta($pid,'map_price',true);
					}

			//msrp details
					if(isset($item_specific->manufacturer_retail_price) && !empty($item_specific->manufacturer_retail_price)){

						$productData['msrp'] 			=	$item_specific->manufacturer_retail_price;
					}elseif (isset($item_specific->mr_attributeMeta) && !empty($item_specific->mr_attributeMeta)) {

				//$productData['msrp'] 			=	$productattrData["$item_specific->mr_attributeMeta"];
						if(in_array($item_specific->mr_attributeMeta, $productattrData)){

							$productData['msrp'] 			=	$productattrData["$item_specific->mr_attributeMeta"];
						}
						else{

							$productData['msrp'] 			=	get_post_meta($pid,$item_specific->mr_attributeMeta,true);
						}
					}else{

						$productData['msrp']			=	get_post_meta($pid,'msrp',true);
					}

			//package_length details
					if(isset($item_specific->package_length) && !empty($item_specific->package_length)){

						$productData['package_length'] 			=	$item_specific->package_length;
					}elseif (isset($item_specific->pl_attributeMeta) && !empty($item_specific->pl_attributeMeta)) {

			//	$productData['package_length'] 			=	$productattrData["$item_specific->pl_attributeMeta"];
						if(in_array($item_specific->pl_attributeMeta, $productattrData)){

							$productData['package_length'] 			=	$productattrData["$item_specific->pl_attributeMeta"];
						}
						else{

							$productData['package_length'] 			=	get_post_meta($pid,$item_specific->pl_attributeMeta,true);
						}
					}else{

						$productData['package_length']			=	get_post_meta($pid,'package_length',true);
					}

			//package_width details
					if(isset($item_specific->package_width) && !empty($item_specific->package_width)){

						$productData['package_width'] 			=	$item_specific->package_width;
					}elseif (isset($item_specific->pw_attributeMeta) && !empty($item_specific->pw_attributeMeta)) {

			//	$productData['package_width'] 			=	$productattrData["$item_specific->pw_attributeMeta"];
						if(in_array($item_specific->pw_attributeMeta, $productattrData)){

							$productData['package_width'] 			=	$productattrData["$item_specific->pw_attributeMeta"];
						}
						else{

							$productData['package_width'] 			=	get_post_meta($pid,$item_specific->pw_attributeMeta,true);
						}
					}else{

						$productData['package_width']			=	get_post_meta($pid,'package_width',true);
					}

			//package_height details
					if(isset($item_specific->package_height) && !empty($item_specific->package_height)){

						$productData['package_height'] 			=	$item_specific->package_height;
					}elseif (isset($item_specific->ph_attributeMeta) && !empty($item_specific->ph_attributeMeta)) {

			//	$productData['package_height'] 			=	$productattrData["$item_specific->ph_attributeMeta"];
						if(in_array($item_specific->ph_attributeMeta, $productattrData)){

							$productData['package_height'] 			=	$productattrData["$item_specific->ph_attributeMeta"];
						}
						else{

							$productData['package_height'] 			=	get_post_meta($pid,$item_specific->ph_attributeMeta,true);
						}
					}else{

						$productData['package_height']			=	get_post_meta($pid,'package_height',true);
					}

					if(isset($item_specific->otherSelectedCode) && !empty($item_specific->otherSelectedCode)){

						$productData['otherSelectedCode']		= 	$item_specific->otherSelectedCode;
						$productData['otherSelectedCodeValue'] 	=  	$productattrData[$item_specific->otherSelectedCodeValue];
					}

		//end
					$productData['all_attributes']					=	$attributesData;
		//	$productData['jet_brand']						=	!empty($item_specific->brand)				?	$item_specific->brand				 :	get_post_meta($pid,'jetBrand',true);
		//	$productData['country_manufacturer']			= 	!empty($item_specific->country_manufacturer)?	$item_specific->country_manufacturer :	get_post_meta($pid, 'jet_country', true);
		//	$productData['safety_warning']					= 	!empty($item_specific->safety_warning)		?	$item_specific->safety_warning		 :	get_post_meta($pid, 'safety_warning', true);
		//	$productData['fulfillment_time']				= 	!empty($item_specific->fullfillment)		? 	$item_specific->fullfillment		 :	get_post_meta($pid, 'fulfillment_time', true);
		//	$productData['map_price']						= 	!empty($item_specific->map_price)			? 	$item_specific->map_price			 :	get_post_meta($pid, 'map_price', true);
					$productData['legal_disclaimer_description']	= 	!empty($item_specific->legal_desc)			?	$item_specific->legal_desc			 :	get_post_meta($pid, 'legal_disclaimer_description', true);
					$productData['product_tax_code']				= 	!empty($item_specific->product_tax_code)	?	$item_specific->product_tax_code	 :	get_post_meta($pid, 'product_tax_code', true);
		//	$productData['msrp']							= 	!empty($item_specific->manufac_retail_price)?	$item_specific->manufac_retail_price :	get_post_meta($pid, 'msrp', true);
					$productData['map_implementation']				= 	!empty($item_specific->map_implementation)	?	$item_specific->map_implementation	 :	get_post_meta($pid, 'map_implementation', true);

					$productData['ship_alone']						= 	!empty($item_specific->ship_alone)			?	$item_specific->ship_alone			 :	get_post_meta($pid, 'ships_alone', true);
					$productData['prop65']							= 	!empty($item_specific->prop65)				?	$item_specific->prop65				 :	get_post_meta($pid, 'prop_65', true);
		//	$productData['package_length']					= 	!empty($item_specific->package_length)		?	$item_specific->package_length		 :	get_post_meta($pid, 'package_length', true);
		//	$productData['package_width']					= 	!empty($item_specific->package_width)		?	$item_specific->package_width		 :	get_post_meta($pid, 'package_width', true);
		//	$productData['package_height']					= 	!empty($item_specific->package_height)		?	$item_specific->package_height		 :	get_post_meta($pid, 'package_height', true);

					$productData['cpsia_statement']					= 	!empty($item_specific->cpsia_statement)		?	$item_specific->cpsia_statement		 :	get_post_meta($pid, 'cpsia_cautionary_statements', true);

				}else{
					$attributesData	=	array();
					foreach($jetAttrInfo as $jetNode => $mappedCAT):
						foreach($mappedCAT as $attrARRAY):

							$attrObject = $attrARRAY[0];
						if($variable){
							$tempName	= $pid."_".$jetNode."_".$attrObject->jet_attr_id;
						}else{
							$tempName	= $jetNode."_".$attrObject->jet_attr_id;
						}
						$tempID		= $attrObject->jet_attr_id;

						if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit))){

							$attributesData[$tempID]			=	get_post_meta($pid,$tempName,true);
							$attributesData[$tempID.'_unit']	=	get_post_meta($pid,$tempName.'_unit',true);
						}
						$attributesData[$tempID]	=	get_post_meta($pid,$tempName,true);
							// print_r($attributesData);

						endforeach;
						endforeach;

						$productData['all_attributes']						=	 $attributesData;
						$productData['jet_brand']	 						=	 get_post_meta($pid,'jetBrand',true);
						$productData['product_manufacturer']			 	= 	 get_post_meta($pid, 'product_manufacturer', true);
						$productData['safety_warning']					 	= 	 get_post_meta($pid, 'safety_warning', true);
						$productData['fulfillment_time']					= 	 get_post_meta($pid, 'fulfillment_time', true);
						$productData['map_price']					 		= 	 get_post_meta($pid, 'map_price', true);
						$productData['legal_disclaimer_description'] 		= 	 get_post_meta($pid, 'legal_disclaimer_description', true);
						$productData['product_tax_code']					= 	 get_post_meta($pid, 'product_tax_code', true);
						$productData['msrp']					 			= 	 get_post_meta($pid, 'msrp', true);
						$productData['map_implementation']					= 	 get_post_meta($pid, 'map_implementation', true);
						$productData['country_manufacturer']				= 	 get_post_meta($pid, 'jet_country', true);
						$productData['ship_alone']							= 	 get_post_meta($pid, 'ships_alone', true);
						$productData['prop65']								= 	 get_post_meta($pid, 'prop_65', true);
						$productData['package_length']						= 	 get_post_meta($pid, 'package_length', true);
						$productData['package_width']						= 	 get_post_meta($pid, 'package_width', true);
						$productData['package_height']						= 	 get_post_meta($pid, 'package_height', true);

						$productData['cpsia_statement']						= 	 get_post_meta($pid, 'cpsia_cautionary_statements', true);
					}
					// print_r($productData);
					return $productData;
				}

				public function formatVariationAttrDetail($variationAttr){

					$formattedAttrArray	=	array();
					if(is_array($variationAttr)){

						foreach ($variationAttr as $index => $value){

							$explodedIndex	=	explode('attribute_', $index);
							if(!empty($explodedIndex[1])){

								$formattedAttrArray[$explodedIndex[1]]	=	$value;
							// print_r($formattedAttrArray);die;
							}
						}
					}
					// print_r($formattedAttrArray);die("fsd");
					return $formattedAttrArray;
				}

				public function fetchSimpleProductDetails($_product,$productData){
					if(WC()->version < "3.0.0"){
						$productData		=	$this->fetchProfileRelatedData($_product->id, $productData);
					}
					else
					{
						$productData		=	$this->fetchProfileRelatedData($_product->get_id(), $productData);					
					}

		//product id
					$productData['id']						=	$_product->id;
		//product name
					$productData['name']					=	$_product->get_title();

		//start product price processing according to condition
					$productData['price']					=	$this->fetchConditionalPrice($_product);
		//end product price processing
					if(isset($productData['mappedSkuWith']) && !empty($productData['mappedSkuWith'])){

						if($productData['mappedSkuWith']	==	'ASIN'){

							if(isset($productData['otherSelectedCode']) && $productData['otherSelectedCode'] == 'ASIN'){
								$productData['asin']	=	$productData['otherSelectedCodeValue'];

							}else{
								$productData['asin']			  		=	$_product->get_sku();
							}

							$productData['standardCode_type']		=	get_post_meta($_product->id,'jetSelectedCode',true);
							$productData['standardCode_value']		=	get_post_meta($_product->id,'standardCode',true);
						}else{
							$productData['standardCode_type']		=	$productData['mappedSkuWith'];
							$productData['standardCode_value']		=	$_product->get_sku();
							$productData['asin']			  		=	get_post_meta($_product->id,'jet_asin', true);
						}
					}else{
			//get jet standard code
						$prdctAsin	=	get_post_meta($_product->id,'jet_asin', true);
						if(!empty($prdctAsin) && strlen($prdctAsin)>9){

							$productData['asin']			  		= $prdctAsin;
						}

			//end asin
			//standard product code type
						$productData['standardCode_type']		=	get_post_meta($_product->id,'jetSelectedCode',true);
			//end
						$productData['standardCode_value']		=	get_post_meta($_product->id,'standardCode',true);
					}
					if(isset($productData['otherSelectedCode']) && ($productData['otherSelectedCode'] != 'choose') && !empty($productData['otherSelectedCodeValue'])){
						$productData['standardCode_type'] = 	$productData['otherSelectedCode'];
						$productData['standardCode_value']	=	$productData['otherSelectedCodeValue'];
					}

		//product image
					$image_link_url							=	wp_get_attachment_image_src( get_post_thumbnail_id( $_product->id ), 'single-post-thumbnail' );
					$productData['jet_product_image_link']	=	$image_link_url[0];

		//get product description
					$productData['desc']		=	substr($_product->post->post_content, 0,1998);

		//start product stock according to condition
					$productData['stock']					=	 $this->fetchConditionalStock($_product);
		//end stock

		//extra product settings
					$productData['jet_mfr_part_number']		 			= 	 get_post_meta($_product->id, 'jet_mfr_part_number', true);
					$productData['number_units_for_price_per_unit']		= 	 get_post_meta($_product->id, 'number_units_for_price_per_unit', true);
					$productData['type_of_unit_for_price_per_unit']		=	 get_post_meta($_product->id, 'type_of_unit_for_price_per_unit', true);
					$productData['shipping_weight_pounds'] 			 	= 	 get_post_meta($_product->id, '_weight', true);
		//$productData['package_length'] 					= 	 get_post_meta($_product->id, 'package_length', true);
		//$productData['package_width']	 					= 	 get_post_meta($_product->id, 'package_width', true);
		//$productData['package_height']					= 	 get_post_meta($_product->id, 'package_height', true);
		//$productData['prop_65']							= 	 get_post_meta($_product->id, 'prop_65', true);
					$productData['bullets']					 			= 	 get_post_meta($_product->id, 'bullets', true);
		//$productData['cpsia_cautionary_statements'] 		= 	 get_post_meta($_product->id, 'cpsia_cautionary_statements', true);
					$productData['exclude_from_fee_adjustments']		= 	 get_post_meta($_product->id, 'exclude_from_fee_adjustments', true);
		//$productData['ships_alone']							= 	 get_post_meta($_product->id, 'ships_alone', true);

					$productData['weight_lbs']	= 	wc_get_weight($_product->get_weight(), 'lbs');
					$productData['height_in']	= 	wc_get_weight($_product->height, 'lbs');
					$productData['widht_in']	= 	wc_get_weight($_product->width, 'lbs');
					$productData['length_in']	= 	wc_get_weight($_product->length, 'lbs');

					$amazon_item = get_post_meta($_product->id, 'amazon_item_type_keyword', true);
					if(!empty($amazon_item)){
						$productData['amazon_item_type_keyword']=$amazon_item;
					}
		//end extra product settings
	//	echo '<pre>';print_r($productData);die;
					return $productData;
				}

				public function fetchConditionalPrice($_product,$variation_id = null){

					if($variation_id != null){
						$_product->id = $variation_id;
					}
					$priceType		=	get_post_meta($_product->id,'jetPriceSelect',true);

					if($priceType == 'sale_price')
						$appliedPrice	=	get_post_meta($_product->id, '_sale_price', true) ? get_post_meta($_product->id, '_sale_price', true): get_post_meta($_product->id, '_regular_price', true);;

					if($priceType	==	'main_price')
						$appliedPrice	=	get_post_meta($_product->id, '_regular_price', true);

					if($priceType	==	'otherPrice')
						$appliedPrice	=	get_post_meta($_product->id, 'jetPrice',true);

					if($priceType	==	'fullfillment_wise')
						$appliedPrice	=	$priceType;

					return $appliedPrice;
				}

	/**
	 * 
	 * @param store all information about product $_product
	 */
	public function fetchConditionalStock($_product,$variation_id = null){
		
		if($variation_id != null){
			$_product->id = $variation_id;
		}
		
		$stocktype  = get_post_meta($_product->id,'jetStockSelect',true);
		
		if(empty($stocktype))
			$stocktype = 'central';

		$stocktype	= trim($stocktype);
		
		if('central' == $stocktype)
			$appliedStock		= 	(int)get_post_meta($_product->id,'_stock',true);
		
		if('default' == $stocktype)
			$appliedStock 		= 	99;
		
		if('other' == $stocktype)
			$appliedStock		= 	get_post_meta($_product->id,'jetStock',true);
		
		if('fullfillment_wise' == $stocktype)
			$appliedStock 		= 	$stocktype;
		
		return $appliedStock;
	}

	/* public function fetchJetAttributeDataSimple($_product,$attributesData){
		
		$formatted_attributes = array();
		
		$attributes = $_product->get_attributes();
		
		foreach($attributes as $attr=>$attr_deets){
		
			$attribute_label = wc_attribute_label($attr);
		
			if ( isset( $attributes[ $attr ] ) || isset( $attributes[ 'pa_jet' . $attr ] ) ) {
		
				$attribute = isset( $attributes[ $attr ] ) ? $attributes[ $attr ] : $attributes[ 'pa_jet' . $attr ];
		
				if ( $attribute['is_taxonomy'] ) {
		
					$formatted_attributes[$attribute_label] = implode( ', ', wc_get_product_terms( $_product->id, $attribute['name'], array( 'fields' => 'names' ) ) );
		
				} else {
		
					$formatted_attributes[$attribute_label] = $attribute['value'];
				}
		
			}
		}
		$attr_mappng_array	=	get_option('woo_jet_attr_map');
		
		foreach($formatted_attributes as $att_name => $value)
		{
			$attrb_name = split('_',$att_name);
			if($attrb_name[count($attrb_name)-1]	==	'unit')
			{
				continue;
			}
			$find_name =	'jet_'.$attrb_name[0];
			
			global $wpdb;
			$table_name 			=	$wpdb->prefix.'woocommerce_attribute_taxonomies';
			$qry 					=	"SELECT `attribute_id` from `$table_name` WHERE `attribute_name` = '$find_name'";
			$retrieve_woo_attr_id 	=	$wpdb->get_results($qry);
			$woo_attr_id			=	$retrieve_woo_attr_id[0]->attribute_id;
			
			foreach($attr_mappng_array as $jet_id	=>	$woo_id)
			{
				if($woo_attr_id == $woo_id)
				{
					if(!empty($formatted_attributes[$att_name.'_unit']))
					{
						$attributesData[$jet_id]			=	$value;
						$attributesData[$jet_id.'_unit']	=	$formatted_attributes[$att_name.'_unit'];
					}else{
						$attributesData[$jet_id]	=	$value;
					}
				}
			}
		}
		return $attributesData;
	} */
	
	public function updateSkuStatus($product_ids){
		foreach($product_ids as $index=>$sku){

			$result	=	$this->fileUploadHelper->CGetRequest('/merchant-skus/'.$sku);
			$response=json_decode($result);
			if(!empty($response->status)){

				update_post_meta($sku,'jet_product_status',$response->status);
			}
			else{
				update_post_meta($sku,'jet_product_status','Not Uploaded');
			}

		}
	}
	
	public function updateProductStatus(){
		// $result	=	$this->fileUploadHelper->CGetRequest('/merchant-skus/1324');
		// print_r($result); die("test");
		
		// all product ids selected for uploading to jet.
		$product_ids = is_array(ced_jet_get_mapped_products()) ? ced_jet_get_mapped_products() : array() ;
		
		if(count($product_ids) > 100){
			
			$product_ids_array = array_chunk($product_ids,100);
			
			foreach($product_ids_array as $productIDs){
				$this->updateSkuStatus($productIDs);
			}
		}else{
			
			$this->updateSkuStatus($product_ids);
		}
	}
	
	public function archiveChunkSkus($missed_ids=array(),$status){
		
		/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/archive_missing_product-new.txt","a") or die("Can't open the requested file");
		fwrite($fp, ("Archive missing products in status: ".$status)); */
		
		$i = 0;
		foreach ($missed_ids as $id) {
			$sku=$id;
			
			/* fwrite($fp,("Serial Num : ".$i.'->'.$sku));
			fwrite($fp,("\n"));
 */			
			$i++;
			$data['is_archived']=true;
			$updateCount++;
			$data1=$this->fileUploadHelper->CPutRequest('/merchant-skus/'.$sku.'/status/archive',json_encode($data));
		}
		//fclose($fp);
		return true;
	}
	
	public function archive_by_status(){

		$status =  rawurlencode('Excluded,Unauthorized');
		$this->archiveMissedProducts($status);

		$status = rawurlencode("Under Jet Review");
		$this->archiveMissedProducts($status);

		$status = rawurlencode("Missing Listing Data");
		$this->archiveMissedProducts($status);
	}
	
	public function archiveMissedProducts($status){
		// all product ids selected for uploading to jet.
		$product_ids = is_array(ced_jet_get_mapped_products()) ? ced_jet_get_mapped_products() : array() ;
		

		$range =  count($product_ids);
		
		// while($start < $end){
		//$result = $this->fileUploadHelper->CGetRequest('/merchant-skus/?offset='.$start.'&limit='.$end);
		
		$raw_encode = $status;
		$response = $this->fileUploadHelper->CGetRequest('/portal/merchantskus?from=0&size='.$range.'&statuses='.$raw_encode);
		$response = json_decode($response,true);
		
		//$status = rawurlencode("Under Jet Review");
		//$result		= 	$this->fileUploadHelper->CGetRequest('merchant-skus/bystatus/'.$status.'/0/'.'1000');
		//$response=json_decode($result);

		$uploaded_skus = $response['merchant_skus'];
		$all_uploaded_skus_array = array();

		if(count($uploaded_skus))
		{
			foreach($uploaded_skus as $key => $product)
			{
				$all_uploaded_skus_array[] = $product['merchant_sku'];
			}
			
			$missed_ids = array_diff($all_uploaded_skus_array, $product_ids);

			if(!empty($missed_ids)){
				$this->archiveChunkSkus($missed_ids,$raw_encode);
			}else{
				$_SESSION['archieve_message'][] = $updateCount.' product(s) is archived successfully';
				return;
				wp_die();
			}
		}
			//$start++;
	//	}
		$_SESSION['archieve_message'][] = $updateCount.' product(s) is archived successfully';
		return;
		wp_die();
	}
}