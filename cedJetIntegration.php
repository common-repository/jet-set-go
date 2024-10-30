<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

require_once 'pages/class-cedJetPageCreation.php';
require_once 'includes/class-cedJetDBHelper.php';
require_once 'includes/class-cedJetStylesScripts.php';
require_once 'includes/class-cedJetCustomTabs.php';
require_once 'includes/class-productManagement.php';
require_once 'includes/class-cedJetSetupDummyApi.php';
require_once 'includes/class-cedJetAjaxHandler.php';
require_once 'includes/class-cedJetFileUploadHelper.php';
require_once 'includes/class-enableJetApi.php';

class cedJetIntegration{
	
	private static $_instance;
	
	public static function getInstance() {
		self::$_instance = new self;
		if( !self::$_instance instanceof self )
			self::$_instance = new self;
	     
		return self::$_instance;
	
	}
	
	public function __construct() {
		
		//ini_set('max_execution_time', 300);
		
		$this->activationAction 	= 	cedJetActivation::getInstance();
		$this->dbActions			=	cedJetDBHelper::getInstance();
		$this->enqueActions			=	cedJetStylesScripts::getInstance();
		$this->customTabs			=	cedJetCustomTabs::getInstance();
		$this->productManagement	=	cedJetProductManagement::getInstance();
		$this->setupapi				=	cedJetSetupDummyApi::getInstance();
		$this->ajaxHandler			=	cedJetAjaxHandler::getInstance();
		$this->fileUploadHelper		=	cedJetFileUploadHelper::getInstance();
		$this->enablejetapi			=	ced_Jetenable_api::getInstance();
		
	}
	
	public function jetActivate(){
		
		$this->dbActions->createTables();
	}
	
	public function jetDeactivate(){
		
		//$this->dbActions->deleteTables();
		//$this->dbActions->removeCategories();
		//$this->dbActions->deleteOptions();
	}
	
	public function cedJetPages(){
		
		$this->activationAction->createPages();
	}
	
	public function cedJetEnqueScript(){
		
		$this->enqueActions->enqueJetAdminScripts();
	}
	
	public function cedJetCustomTab(){
		
		$this->customTabs->addCutomProductTAb();
	}
	
	public function cedJetcustomTabFields(){
		
		$this->customTabs->JetcustomTabFields();
	}
	
	public function cedJetProcessProductMeta($post_id){
		
		$this->customTabs->jetProcessProductMeta($post_id);
	}
	
	public function cedJetDynamicAttributes(){
	
		$this->customTabs->jetDynamicAttributes();
	}
	
	public function cedJetUploadProduct(){
		
		if(check_admin_referer('product_upload_nonce_check','pupload_nonce')){
			$this->productManagement->uploadProducts();
		}
	}
	
	public function cedUpdateProductStatus(){
		if(check_admin_referer('jet_pstatus_nonce_check','ups_nonce')){
			$this->productManagement->updateProductStatus();
		}
	}
	
	public function cedArchiveProductStatus(){
		if(check_admin_referer('jet_archnonce_check','arc_nonce')){
			$product_ids = array();
			$this->productManagement->archive_by_status();
		}
	}
	/**
	 * Create new fields for variations
	 */
	public function variation_settings( $loop, $variation_data, $variation ) {
		
		$this->customTabs->variation_settings_fields($loop, $variation_data, $variation);
	}
	
	public function cedJetProfileMetaBox($post_type){

		$this->customTabs->addProfileRelatedMetaBox($post_type);
	}

	/**
	 * Save variable settings 
	 */
	public function save_variation_settings($post_id){
		
		$this->customTabs->save_variation_settings_fields($post_id);
	}
	
	/**
	 * Mapping woo categories with jet categories.
	 */
	public function woojetCategoryMapping(){
		
		$this->ajaxHandler->cedCategoryMapping();
	}
	
	/**
	 * Setup Api call for product
	 */
	public function cedJetActivateAndResubmitProduct(){
		
		$this->ajaxHandler->cedActivateAndResubmitProduct();
	}
	
	public function cedDeleteMapCatEntry(){
		
		$this->ajaxHandler->deleteMappedOptionCatEntry();
	}
	
	public function cedUpdateMappedCatId(){
	
		$this->ajaxHandler->UpdateMappedCatId();
	}
	
	/**
	 * profile assigning by ajax.
	 */
	public function cedAssignProfile(){
		
		$this->ajaxHandler->createProfileHtml();
	}
	
	public function cedRemoveProfile(){
		
		$this->ajaxHandler->removeProfile();
	}
	
	public function cedSetProductProfile(){
		
		$this->ajaxHandler->assignProfileToProduct();
	}
	
	public function cedjetaddDynamicCatAttr(){
		
		$this->ajaxHandler->appendMappedCategoryHtml();
	}
	
	public function cedJetDeleteErrorFile(){
		
		$this->ajaxHandler->errorFileDeletion();
	}
	/**
	 * start session
	 */
	public function jetStartSession(){
		
		if(!session_id()){
			
			session_start();
		}
	}
	
	/**
	 * end session
	 */
	public function jetEndSession(){
		
		session_destroy ();
	}
	
	//order related operations
	public function cedJetOrderMetaBox(){
		
		add_meta_box('custom_order_option', 'Jet Order Management', array($this->customTabs,'addOrderMetaBox'),'shop_order');
	}
	
	//saving the meta box data
	public function saveJetOrderMetaBox($post_id){
		
		$this->customTabs->saveOrderMetaBox($post_id);
	}
	
	
	//mass product upload on jet
	public function get_all_product_of_jet(){
		$this->ajaxHandler->all_product_of_jet();
	}
	
	//start mass product upload
	public function start_mass_product_upload(){
			$this->ajaxHandler->uploading_mass_product();
	}
	
	//start mass product inventory upload on jet 
	
	public function start_mass_inventory_upload(){
		if(check_ajax_referer('jet_inventory_nonce_check','inp_nonce')){
			$this->ajaxHandler->uploading_mass_product_inventory();
		}
	}
	//mass archieve product from jet
	public function start_mass_archive_product(){
		if(check_ajax_referer('check_mass_pro_upload_nonce','bupbm_nonce')){
			$this->ajaxHandler->mass_archive_product();
		}
	}
	
	//mass unarchieve product from jet
	public function start_mass_unarchive_product(){
		if(check_ajax_referer('check_mass_pro_upload_nonce','bupbm_nonce')){
			$this->ajaxHandler->mass_unarchive_product();
		}
	}
	
	
	/**
	 * Delete error file 
	 */

	public function delete_jet_error_file(){
		if(check_ajax_referer('error_file_delete_action_jet','def_nonce')){
			$this->ajaxHandler->delete_error_file_jet();
		}
	}
	
	
	/**
	 * Remove admin sub menu
	 */
	public function remove_admin_submenus(){
		$parentslug = 'options-general.php';
		//remove return settings page
		$menuslug1 = 'jet_specific_order_return_page';
		$remove_link1 = remove_submenu_page($parentslug,$menuslug1);
		
		//remove error settings menu
		$menuslug2 = 'jet_upload_error_return_page';
		$remove_link2 = remove_submenu_page($parentslug,$menuslug2);
		
		//remove order return reject page settings menu
		$menuslug3 = 'jet_order_reject_return_page';
		$remove_link3 = remove_submenu_page($parentslug,$menuslug3);
		
		//remove order refund page settings menu
		$menuslug4 = 'jet_order_refund_page';
		$remove_link4 = remove_submenu_page($parentslug,$menuslug4);
		
	}
	
	/**
	 * Archieve and unarchive on thrash product
	 */
	
	public function archive_on_trash_product($new_status, $old_status, $post){
		
		//echo $new_status;die;
		if($new_status == 'trash' && $old_status == 'publish'){
			
			$product_id = $post->ID;
			$file_type  = 'Archive';
			$send_sku  	= array($product_id);
			
			require_once CEDJET_DIRPATH.'/includes/class-productManagement.php';
			$archieve = cedJetProductManagement::getInstance();
			$archieve->uploadProducts($file_type,$send_sku);
		}
		
		//unarchive product on publish
		if($new_status == 'publish' && $old_status ==  'trash'){
			
				$product_id 	= $post->ID;
				$file_type = 'Unarchive';
				$send_sku  = array($product_id);

				require_once CEDJET_DIRPATH.'/includes/class-productManagement.php';
				$unarchieve = cedJetProductManagement::getInstance();
				$unarchieve->uploadProducts($file_type,$send_sku);
		}	
}
	
	/**
	 * All mapped cat
	 * @return multitype:unknown
	 */
	public function all_mapped_jet_cat_and_product(){
		
		$mappedCategories	=	get_option('cedWooJetMapping',false);
			
		if(!empty($mappedCategories)){
			$all_woo_cat	=	array();
			foreach($mappedCategories as $mappedwoocat	=> $mappedjetcat){
				$all_woo_cat[]	=	$mappedwoocat;
			}
		}
		

		$args = array(
				'post_type' => array('product'),
				'tax_query'             => array(
						array(
								'taxonomy'      => 'product_cat',
								'field' 		=> 'term_id', //This is optional, as it defaults to 'term_id'
								'terms'         => $all_woo_cat,
								'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
						)
				)
		);
		$all_product_id = array();
			
		$loop = new WP_Query($args);
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$_product = get_product($loop->post->ID );
			$all_product_id[] = $_product->id;
		}

		$all_details['woo_cat_id'] = $all_woo_cat;
		$all_details['all_jet_product'] = $all_product_id;
		return $all_details;
		
	}
	
	public function update_inventory_on_jet($order_id){
		
		// order object (optional but handy)
		$order_type  = get_post_meta($order_id,'order_type_jet',true);
		if(trim($order_type) != 'jet_order'){
			$all_order_item_id = array();
			$all_product_quantity = array();
			
			$order		 = 	new WC_Order( $order_id );
			$items 		 = 	$order->get_items();
			
			foreach ( $items as $item ) {
				$all_order_item_id[] 	= $item['product_id'];
				$all_product_quantity[]	= $item['qty'];
			}
			
			$all_data 			= 	$this->all_mapped_jet_cat_and_product();
			$mapped_jet_product =   $all_data['all_jet_product']; 
			
			if(!empty($mapped_jet_product)){
				foreach($all_order_item_id as $index => $product_id){
					if(in_array($product_id,$mapped_jet_product)){
						$order_quantity 			= 	 $all_product_quantity[$index];
						$this->get_updated_inventory($product_id,$order_quantity);
					}
				}
			}
		}
	}
	
/**
 * Update Inventory for product on jet
 */	
	public function get_updated_inventory($product_id,$order_quantity){
		
		$jet_node_id	= get_option( 'jet_node_id');
		$jet_node_id    = json_decode($jet_node_id);
		
		foreach($jet_node_id as $key => $fullfillment_id)
		{
			$stocktype  = get_post_meta($product_id,'jetStockSelect',true);
			
			if(empty($stocktype))
				$stocktype = 'central';
						
			$stocktype	= trim($stocktype);
			
			if('central' == $stocktype){
				$appliedStock		= 	(int)get_post_meta($product_id,'_stock',true);
				$new_stock 			=   (int)$appliedStock - $quantity;
				
				if($new_stock > 0){
					$this->actual_update_inventory_on_jet($product_id,$new_stock,$fullfillment_id);
				}
				else{
					$new_stock = get_option('jet_default_stock');
					$this->actual_update_inventory_on_jet($product_id,$new_stock,$fullfillment_id);
				}
			}
			if('other' == $stocktype){
				$appliedStock		= 	get_post_meta($product_id,'jetStock',true);
				$new_stock 			=   (int)$appliedStock - $quantity;
				$this->actual_update_inventory_on_jet($product_id,$new_stock,$fullfillment_id);
			}
			if('fullfillment_wise' == $stocktype){
			
				if($fullfillment_id == $value){
					$stock    	  		=  get_post_meta($product_id, 's_'.$value, true);
					$new_stock    		=  $stock - $quantity;
					$this->actual_update_inventory_on_jet($product_id,$new_stock,$fullfillment_id);
			}
		}
	}	
}
  
  /**
   * Actual update inventory
   */
  public function actual_update_inventory_on_jet($product_id,$new_stock,$fullfillment_id){
  	
  	$node1								=	array();
  	$qty 								= 	 $new_stock;
  	$node1['fulfillment_node_id']		=	"$fullfillment_id";
  	$node1['quantity']					=	(int)$qty;
  	$inventory['fulfillment_nodes'][]	=	$node1;
  	/* $node1								=	array(); */

  	if(!empty($inventory)){
  		$inventry	=	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.trim($product_id).'/inventory',json_encode($inventory));
  	}
  	
  	/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/inventory_test.txt","a") or die("Can't open the requested file");
  	fwrite($fp, ("New Stock".$qty));
  	fwrite($fp,("\n"));
  	fclose($fp); */
  	return;	
  }
  
  /**
   * Order acknowledge
   */
  // public function cedJetPerformOrderAction(){
  
  // 	$this->orderManagement->performOrderAction();
  // }
  
  public function cedcommerce_jet_admin_notice_success_call(){
  		$this->licence->cedcommerce_jet_admin_notice_success();
  }
  
/**
  * deleting useless files of uploaded products.
  */
 public function cedDeleteUselessFiles(){
  
  $wpuploadDir = wp_upload_dir();
  $baseDir  = $wpuploadDir['basedir'];
  $path  = $baseDir . '/var/jet-upload';
  
  $this->ajaxHandler->delete_useless_files($path);
 }
 
 /**
  * get all meta field to show on profile settings
  */
 public function get_all_meta_fields(){
 	$id = $_POST['id'];
 	$getPostCustom = get_post_custom($id);
 	echo json_encode($getPostCustom);
 	exit;
 }
 
 /**
  * call for enable jet api
  */
 public function call_for_enable_jet_api(){
 	$this->enablejetapi->enable_product_api();
 }
 
 public function get_all_jet_categories(){
 	$this->ajaxHandler->get_jet_all_categories();
 }
 
 public function validate_jet_products(){
 	$this->ajaxHandler->validate_all_jet_product();
 }
 
 public function get_all_jet_categories_update(){
 	$this->ajaxHandler->update_edit_cat();
 }
 
 //map bulk products with profile 
 public function map_bulk_products_with_profile(){
 	
 	if(check_admin_referer('jet_mass_profile_check','mpa_nonce')){
 	if(isset($_POST['profile_id']) && isset($_POST['all_map_products_id'])){
 		$all_product_ids 	= 	$_POST['all_map_products_id'];
 		$profile_id 		= 	$_POST['profile_id'];
 		
 		foreach($all_product_ids as $key => $pro_id){
 			update_post_meta($pro_id, 'productProfileID',$profile_id);
 		}
 		echo 'Your Profile Mapping Updated Successfully !!!';exit;
 	}
   }
 }
 
 /**
  * Category assign to products in bulk
  */
 public function mass_cat_assign_to_products(){
 	
 	if(isset($_POST['upload_type']) && $_POST['upload_type'] == 'mass_category_mapping_with_products')
 	{
 		$selected_product_ids 	=	$_POST['selected_product_ids']; 
 		$selected_cat_ids  		=	$_POST['selected_cat_ids'];
 		
 		$mappedCategories		=	get_option('cedWooJetMapping',false);
 		
 		if(!empty($selected_product_ids) && !empty($selected_cat_ids)){
 			foreach($selected_product_ids as $index => $pro_id){
 				$cat_id 		= 	$selected_cat_ids[$index];
 				$jet_cat_id		=	$mappedCategories[$cat_id];
 				
 				$_product 		= 	get_product($pro_id);
 				
 				if(isset($_product)){
 					if($_product->is_type('variable')){
 						
 						$term_list 			= 	wp_get_post_terms($pro_id, 'product_cat',array("fields" => "ids"));
 						
 						if(!in_array($cat_id,$term_list)){
 							$term_list[]	 	=	intval($cat_id);
 						}
 						
 						$term_taxonomy_ids 		= 	wp_set_object_terms( $pro_id, $term_list, 'product_cat',true );
 						if ( is_wp_error( $term_taxonomy_ids ) ) {
 							// There was an error somewhere and the terms couldn't be set.
 							$_SESSION['Mapped_mass_cat_to_products'][]	= __('Error While Category Assign for variable product id'.$pro_id,'woocommerce-jet-integration'); 
 						}  
 						
 						if(!empty($variations) && count($variations)){
 							foreach($variations as $variation){
 								$variation_id	= $variation['variation_id'];
 								update_post_meta($variation_id,$variation_id.'_selectedCatAttr',$jet_cat_id);
 							}
 						}
 					}
 					elseif ($_product->is_type('simple')){
 						$term_list 			= 	wp_get_post_terms($pro_id, 'product_cat',array("fields" => "ids"));
 							
 						if(!in_array($cat_id,$term_list)){
 							$term_list[]	 	=	intval($cat_id);
 						}
 						$term_taxonomy_ids 		= 	wp_set_object_terms( $pro_id, $term_list,'product_cat',true );
 						 	
 						if ( is_wp_error( $term_taxonomy_ids ) ) {
 						// There was an error somewhere and the terms couldn't be set.
 						$_SESSION['Mapped_mass_cat_to_products'][]	= __('Error While Category Assign for variable product id'.$pro_id,'woocommerce-jet-integration');
 						} 
 						update_post_meta($pro_id,'selectedCatAttr',$jet_cat_id);
 					}else{
 						continue;
 					}
 				}
 			}
 		}
     }
  }
  
  /**
   * Mass profile assign by check to products
   */
  
  public function assign_profile_to_product_by_check(){
  	if(check_admin_referer('Mapped_bulk_profile_check_products','paoca_nonce')){
  		if(isset($_POST['profile_id']) && isset($_POST['selected_product_ids'])){
  			$all_product_ids 	= 	$_POST['selected_product_ids'];
  			$profile_id 		= 	$_POST['profile_id'];
  				
  			foreach($all_product_ids as $key => $pro_id){
  				update_post_meta($pro_id, 'productProfileID',$profile_id);
  			}
  			echo 'Your Profile Mapping Updated Successfully !!!';exit;
  		}
  	}
  }
  
  /**
   * Mass category assign  to product
   */
  public function mass_cat_manage_product(){
  	if (isset($_POST['action']) && isset($_POST['cat_id'])){
  		$cat_id  =  $_POST['cat_id'];
  		$all_product_mang        =  $_POST['all_upload_product_id'];
  		$mappedCategories		=	get_option('cedWooJetMapping',false);
  		$jet_cat_id		=	$mappedCategories[$cat_id];
  		foreach ($all_product_mang as $k=>$single_product){
  			$pro_id=$single_product;
  			$_product 		= 	get_product($pro_id);
  			if(isset($_product)){
  				if($_product->is_type('variable')){
  						
  					$term_list 			= 	wp_get_post_terms($pro_id, 'product_cat',array("fields" => "ids"));
  						
  					if(!in_array($cat_id,$term_list)){
  						$term_list[]	 	=	intval($cat_id);
  					}
  						
  					$term_taxonomy_ids 		= 	wp_set_object_terms( $pro_id, $term_list, 'product_cat',true );
  					if ( is_wp_error( $term_taxonomy_ids ) ) {
  						// There was an error somewhere and the terms couldn't be set.
  						$_SESSION['Mapped_mass_cat_to_products_manage'][]	= __('Error While Category Assign for variable product id'.$pro_id,'woocommerce-jet-integration');
  					}
  						
  					if(!empty($variations) && count($variations)){
  						foreach($variations as $variation){
  							$variation_id	= $variation['variation_id'];
  							update_post_meta($variation_id,$variation_id.'_selectedCatAttr',$jet_cat_id);
  						}
  					}
  				}
  				elseif ($_product->is_type('simple')){
  					
  					$term_list 			= 	wp_get_post_terms($pro_id, 'product_cat',array("fields" => "ids"));
  			
  					if(!in_array($cat_id,$term_list)){
  						$term_list[]	 	=	intval($cat_id);
  					}
  					$term_taxonomy_ids 		= 	wp_set_object_terms( $pro_id, $term_list,'product_cat',true );
  						
  					if ( is_wp_error( $term_taxonomy_ids ) ) {
  						// There was an error somewhere and the terms couldn't be set.
  						$_SESSION['Mapped_mass_cat_to_products'][]	= __('Error While Category Assign for variable product id'.$pro_id,'woocommerce-jet-integration');
  					}
  					update_post_meta($pro_id,'selectedCatAttr',$jet_cat_id);
  				}else{
  					continue;
  				}
  			}
  		}
  	}
  }
  
  /**
   * Notification mail
   *   */
  public function notification_mail($to,$product_id,$new_stock){
  	$subject = 'Low Inventory';
  	$body = '<!DOCTYPE html>
			<html>
			<head>
				<title></title>
			</head>
			<body style="margin: 0; padding: 0;">
			<table  align="center" border="1" cellpadding="0" cellspacing="0" width="600" style="border: none;">
				<tr style="text-align: center;">
					<td style="padding-bottom: 10px; padding-bottom: 20px; padding-top: 20px; padding-right: 20px; padding-left: 20px; color: #ffffff; background-color: #4073b5; border: none; font-size: 28px;">
						Low Inventory 
					</td>
				</tr>
					<tr style="text-align: center;">
					<td style="color: #ffffff; padding-bottom: 20px; padding-right: 20px; padding-left: 20px; background-color: #4073b5; border: none; font-size: 20px;">
						Poduct having inventory less than threshold inventory. Left only :'.$new_stock.'
					</td>
				</tr>
				<tr style="text-align: center;">
					<td style="padding-top: 40px; padding-bottom: 25px; border: none; font-size: 20px; padding-right: 20px; padding-left: 20px;">
						Notification for product having low inventory product id:<span><a href="'. site_url() .'/wp-admin/post.php?post='. $product_id .'&action=edit">View Details</a></span>
					</td>
				</tr>
				</tr>
					<tr style="text-align: center;">
					<td style=" color: #ffffff; padding-bottom: 30px; padding-right: 20px; padding-top: 30px; padding-left: 20px; background-color: #4073b5; border: none; font-size: 20px;">
						Woocommerce JetIntegration(cedcommerce)
					</td>
				</tr>
			</table>
			</body>
			</html>';
  	$headers = array('Content-Type: text/html; charset=UTF-8');
  	wp_mail( $to, $subject, $body, $headers );
	} 
}