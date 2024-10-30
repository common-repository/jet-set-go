<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'class-cedJetFileUploadHelper.php';

class cedJetDBHelper{

	private static $_instance;

	public static function getInstance() {

		if( !self::$_instance instanceof self )
			self::$_instance = new self;

		return self::$_instance;
	}
	
	public function __construct() {
		$this->apiHelper		=		cedJetFileUploadHelper::getInstance();
	}
	
	/**
	 * public function for creating jet related tables.
	 * @param none
	 * @author cedcommerce
	 */
	public function createTables(){
		
		global $wpdb;
		
		$attribute_table_name 			=	 $wpdb->prefix.'jet_attributes_table';
		$category_table_name 			=	 $wpdb->prefix.'jet_catgory_attribute';
		$errorfile_table_name 			= 	 $wpdb->prefix.'jet_errorfile_info';
		$file_table_name 				=	 $wpdb->prefix.'jet_file_info';
		$order_detail_table_name 		= 	 $wpdb->prefix.'jet_order_detail';
		$return_detail_table_name 		= 	 $wpdb->prefix.'jet_return_detail';
		$refund_detail_table_name 		= 	 $wpdb->prefix.'jet_refund_detail';
		$shipping_exception_table_name 	= 	 $wpdb->prefix.'jet_shipping_exception';
		$order_import_table_name 		= 	 $wpdb->prefix.'jet_order_import_error';
		$archive_table_name 			= 	 $wpdb->prefix.'jet_archive_table';
		$settlement_table_name			=	 $wpdb->prefix.'jet_settlement_report';
		$profile_settings				=	 $wpdb->prefix.'jet_profile_settings';
		
		update_option('table_created','table_create');
		
		update_option('auto_order_acknowledge','yes');
		update_option('sync_product_update','No');
		update_option('archieve_variable_settings','No');
		update_option('jet_delivery_day','5');
		/*
		 * We'll set the default character set and collation for this table.
		* If we don't do this, some characters could end up being converted
		* to just ?'s when saved in our table.
		*/
		$charset_collate = '';
		
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
			
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}
		
		$tbl1 = "CREATE TABLE IF NOT EXISTS `$attribute_table_name` ( 
			`id` int(11) NOT NULL auto_increment,
		    `jet_node_id` int(11) default NULL,
			`jet_attr_id` bigint(20) NOT NULL, 
			`woocommerce_attr_id` int(11) NOT NULL,
			`freetext` int(4) NOT NULL, 
			`name` text NOT NULL, 
			`values` text NULL, 
			`pre_value` int(11) NOT NULL, 
			`variant` int(1) NOT NULL default 0, 
			`variant_pair` int(1) NOT NULL default 0, 
			`unit` text NULL,
				 PRIMARY KEY (`id`),
				 UNIQUE KEY `attr_id` (`jet_attr_id`) 
			);";
		
		$tbl2 = "
		CREATE TABLE IF NOT EXISTS `$category_table_name` (
		`id` int(11) NOT NULL  auto_increment,
		`jet_cate_id` bigint(20) unsigned NOT NULL default 0,
		`woocommerce_cat_id` int(11) NOT NULL,
		`jet_attributes` text,
		PRIMARY KEY (`id`),
		UNIQUE KEY `cat_id` (`jet_cate_id`)
		);";
		$tbl3 = "
		CREATE TABLE IF NOT EXISTS `$errorfile_table_name` (
		`id` int(11) NOT NULL  auto_increment,
		`jet_file_id` varchar(70) NOT NULL,
		`file_name` varchar(70) NOT NULL,
		`file_type` varchar(70) NOT NULL,
		`status` varchar(60) NOT NULL,
		`error` text NOT NULL,
		PRIMARY KEY (`id`)
		);";
		$tbl4 = "
		CREATE TABLE IF NOT EXISTS `$file_table_name` (
		`id` int(10) NOT NULL  auto_increment,
		`woocommerce_batch_info` varchar(900) NOT NULL,
		`jet_file_id` varchar(400) NOT NULL,
		`token_url` varchar(200) NOT NULL,
		`file_name` varchar(100) NOT NULL,
		`file_type` varchar(100) NOT NULL,
		`status` varchar(50) NOT NULL default 'unprocessed',
		PRIMARY KEY (`id`)
		) ;";
		$tbl5 = "
		CREATE TABLE IF NOT EXISTS `$order_detail_table_name` (
		`id` int(10) NOT NULL  auto_increment,
		`order_item_id` varchar(100) NOT NULL,
		`merchant_order_id` varchar(100) NOT NULL,
		`merchant_sku` varchar(100) NOT NULL,
		`deliver_by` varchar(100) NOT NULL,
		`woocommerce_order_id` varchar(100) NOT NULL,
		`order_all_item` text NOT NULL,
		`shipment_data` text NOT NULL,
		`status` varchar(100) NOT NULL,
		PRIMARY KEY (`id`)
		);";
		
		$tbl6 =
		"CREATE TABLE IF NOT EXISTS `$return_detail_table_name` (
		`id` int(10) NOT NULL  auto_increment,
		`return_id` varchar(100) NOT NULL,
		`merchant_order_id` varchar(100) NOT NULL,
		`status` varchar(100) NOT NULL,
		`reason_to_disagree` varchar(100) NOT NULL,
		`all_return_item_details` text NOT NULL,
		`refund_without_return` tinyint(1) NOT NULL DEFAULT 0,
        `reference_order_id` VARCHAR(255),
        `alt_order_id` VARCHAR(255),
        `return_date` datetime,
        `shipping_carrier` VARCHAR(255),
        `tracking_number` VARCHAR(255),
        `merchant_return_charge` float(11) DEFAULT NULL,
        `return_charge_feedback` VARCHAR(255),
        `return_status` VARCHAR(255),
		PRIMARY KEY (`id`)
		);
		";
		
		$tbl7 =
		"CREATE TABLE IF NOT EXISTS `$refund_detail_table_name` (
		`id` int(10) NOT NULL  auto_increment,
		`order_item_id` varchar(100) NOT NULL,
		`qty_returned` varchar(100) NOT NULL,
		`qty_refund` varchar(100) NOT NULL,
		`refund_reason` varchar(100) NOT NULL,
		`refund_feedback` varchar(100) NOT NULL,
		`refund_tax` varchar(100) NOT NULL,
		`refund_shippingcost` varchar(100) NOT NULL,
		`refund_shipping_tax` varchar(100) NOT NULL,
		`refund_orderid` varchar(100) NOT NULL,
		`refund_merchantid` varchar(100) NOT NULL,
		`refund_amount` varchar(100) NOT NULL,
		`refund_id` varchar(100) NOT NULL,
		`refund_status` varchar(100) NOT NULL,
		`woo_order_id` varchar(100) NOT NULL,
		PRIMARY KEY (`id`)
		);
		";
		
		$tbl8 =
		"CREATE TABLE IF NOT EXISTS `$shipping_exception_table_name` (
		`id` int(10) NOT NULL  auto_increment,
		`sku` varchar(100) NOT NULL,
		`shipping_carrier` varchar(100) NOT NULL,
		`shipping_method` varchar(100) NOT NULL,
		`shipping_override` varchar(100) NOT NULL,
		`shipping_charge` varchar(100) NOT NULL,
		`shipping_excep` varchar(100) NOT NULL,
		PRIMARY KEY (`id`)
		);
		";
		$tbl9 = "CREATE TABLE IF NOT EXISTS `$order_import_table_name` (
		`id` int(10) NOT NULL  auto_increment,
		`merchant_order_id` varchar(100) NOT NULL,
		`reason` varchar(100) NOT NULL,
		`order_item_id` text NOT NULL,
		PRIMARY KEY (`id`)
		);
				";
				$tbl10 =
				"CREATE TABLE IF NOT EXISTS `$archive_table_name` (
				`id` int(10) NOT NULL  auto_increment,
				`sku` varchar(100) NOT NULL,
				`status` varchar(100) NOT NULL,
				`current_time` varchar(100) NOT NULL,
				PRIMARY KEY (`id`)
		);
		";
		$tbl11	=
		"CREATE TABLE IF NOT EXISTS `$settlement_table_name` (
		`date` int(10) NOT NULL,
		`report` text NOT NULL,
		PRIMARY KEY (`date`)
		);
		";
		
		$tbl12 =
		"CREATE TABLE IF NOT EXISTS `$profile_settings` (
		`profile_id` int(10) NOT NULL  auto_increment,
		`profile_name` varchar(100) NOT NULL,
		`profile_category` text NOT NULL,
		`item_specific` text,
		PRIMARY KEY (`profile_id`)
		);
		";
		
		//ENGINE=InnoDB $charset_collate;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		dbDelta( $tbl1 );
		dbDelta( $tbl2 );
		dbDelta( $tbl3 );
		dbDelta( $tbl4 );
		dbDelta( $tbl5 );
		dbDelta( $tbl6 );
		dbDelta( $tbl7 );
		dbDelta( $tbl8 );
		dbDelta( $tbl9 );
		dbDelta( $tbl10 );
		dbDelta($tbl11);
		dbDelta($tbl12);
		
		update_option('attr_table_updated','attr_table_done_updated');
		
	}
	
	public function deleteTables(){

		global $wpdb;
		$attribute_table_name 			= $wpdb->prefix.'jet_attributes_table';
		$category_table_name 			= $wpdb->prefix.'jet_catgory_attribute';
		$errorfile_table_name 			= $wpdb->prefix.'jet_errorfile_info';
		$file_table_name 				= $wpdb->prefix.'jet_file_info';
		$order_detail_table_name 		= $wpdb->prefix.'jet_order_detail';
		$return_detail_table_name 		= $wpdb->prefix.'jet_return_detail';
		$refund_detail_table_name 		= $wpdb->prefix.'jet_refund_detail';
		$shipping_exception_table_name 	= $wpdb->prefix.'jet_shipping_exception';
		$order_import_table_name 		= $wpdb->prefix.'jet_order_import_error';
		$archive_table_name 			= $wpdb->prefix.'jet_archive_table';
		$settlement_table_name			= $wpdb->prefix.'jet_settlement_report';
		
		$woo_attr_table = $wpdb->prefix.'woocommerce_attribute_taxonomies';
		
		$profile_settings				= $wpdb->prefix.'jet_profile_settings';
		
		$sql1 		= "DROP TABLE ". $attribute_table_name;
		$sql2 		= "DROP TABLE ". $category_table_name;
		$sql3 		= "DROP TABLE ". $errorfile_table_name;
		$sql4 		= "DROP TABLE ". $file_table_name;
		$sql5 		= "DROP TABLE ". $order_detail_table_name;
		$sql6 		= "DROP TABLE ". $return_detail_table_name;
		$sql7 		= "DROP TABLE ". $refund_detail_table_name;
		$sql8 		= "DROP TABLE ". $shipping_exception_table_name;
		$sql9 		= "DROP TABLE ". $order_import_table_name;
		$sql10 		= "DROP TABLE ". $archive_table_name;
		$sql11		= "DROP TABLE ". $settlement_table_name;
		$sql12		= "DROP TABLE ". $profile_settings;
		//$sql11 = "TRUNCATE TABLE" .$woo_attr_table;
		$wpdb->query($sql1);
		$wpdb->query($sql2);
		$wpdb->query($sql3);
		$wpdb->query($sql4);
		$wpdb->query($sql5);
		$wpdb->query($sql6);
		$wpdb->query($sql7);
		$wpdb->query($sql8);
		$wpdb->query($sql9);
		$wpdb->query($sql10);
		$wpdb->query($sql11);
		$wpdb->query($sql12);
	}
	
	public function removeCategories(){

		global $wpdb;
		$table_name = $wpdb->prefix.'jet_catgory_attribute';
		$qry = "SELECT  `woocommerce_cat_id` FROM `$table_name` WHERE 1 ;";
		$result = $wpdb->get_results($qry);
		$jet_cat_id = array();
		$jet_cat_id[] = get_option('wp_jet_super_parent_category','');
		foreach($result as $key => $value)
		{
			$jet_cat_id[] = $value->woocommerce_cat_id;
		}
		foreach($jet_cat_id as $index => $cat_id)
		{
			wp_delete_term( $cat_id, 'product_cat' );
		}
	}
	
	public function deleteOptions(){
		
		//drop all configuration details
		delete_option('jetcom_token');
		delete_option( 'jet_api_url');
		delete_option( 'jet_user');
		delete_option( 'jet_password');
		delete_option( 'jet_node_id');
		delete_option( 'jet_email_id');
		delete_option( 'jet_store_name');
		
		// return location settings
		delete_option( 'jet_first_address');
		delete_option( 'jet_second_address');
		delete_option( 'jet_city');
		delete_option( 'jet_state');
		delete_option( 'jet_zip_code');
		
		//attr_mapping data
		delete_option('woo_jet_attr_map');
		
		//
		delete_option('cedWooJetMapping');
	}
	
	public function insertAttributeData($jetCatID){
		
		$linkedAttrArray	=	array();
		
		$attr_Detail = $this->apiHelper->get_category_attributes($jetCatID);
		//print_r($attr_Detail);die('no');
		if(empty($attr_Detail))
			return;
		
		$all_attr_Detail = $attr_Detail['attributes'];
		
		//$mappedAttrIDS = $attr_Detail->attribute_ids;
		//$linkedAttrArray = explode(',', $mappedAttrIDS);
		$mappedAttrIDS = array();
		
		if(! empty($all_attr_Detail)) {
			//update_option($jetCatID.'_linkedAttributes',json_encode($linkedAttrArray));
			foreach($all_attr_Detail as $index=> $attr){
				//$mappdattrDetail = $this->apiHelper->get_attribute_detail($att_id);
				//$attr_info = json_decode($mappdattrDetail);
				if( !empty($attr) ){
					
					$mappedAttrIDS[] 		= 	$attr['attribute_id'];
					
					$att_id					=	$attr['attribute_id'];
					$attr_info->id 			=	$att_id;
					$attr_info->attr_value	=   $attr['values'];
					$attr_info->free_text	=	$attr['free_text'];
					$attr_info->name		=	$attr['attribute_description'];	
					$attr_info->units		=	$attr['units'];
					$attr_info->variant		=	$attr['variant'];
					
					$att_id = isset($attr_info->id) ? absint($attr_info->id) : 0;
					$values = isset($attr_info->attr_value) ? json_encode($attr_info->attr_value) : '';
					if(isset($values) && !empty($values) && count($attr_info->attr_value)>1){
						$pre_value = 1;
					}else{
						$pre_value = 0;
					}
					$free_text = isset($attr_info->free_text) ? absint($attr_info->free_text) : 0;	
					if($free_text == 0 && $pre_value== 0)
						$free_text = 1;
								
					$name = isset($attr_info->name) ? $attr_info->name : '';
					$units = isset($attr_info->units) ? json_encode($attr_info->units) : '';
					$variant	=	isset($attr_info->variant) ? $attr_info->variant : '';
					
					global $wpdb;
					$table_name = $wpdb->prefix.'jet_attributes_table';
					
					$qry1 = "select * from `$table_name` where `jet_attr_id` = $att_id";
					$resultdata = $wpdb->get_results($qry1);
					
					if(empty($resultdata) || $resultdata == null){
						$name = addslashes($name);
						$qry = "INSERT INTO `$table_name` (`jet_attr_id`, `freetext`,`name`, `values`,`pre_value`,`unit`,`variant`) VALUES
						('".$att_id."', '".$free_text."','".$name."', '".$values."', '".$pre_value."', '".$units."','".$variant."');";
						//die($qry);
						$wpdb->query($qry);
					}else{
						$name = addslashes($name);
						$qry_update = "UPDATE $table_name SET `freetext` = $free_text, `name` = $name, `values` = $values, `pre_value` = $pre_value, `unit` = $units , `variant` = $variant WHERE `jet_attr_id`= $att_id;";
						$resultdata = $wpdb->get_results($qry);
					}
				 }
			}
			//update attributes values on category index
			update_option($jetCatID.'_linkedAttributes',json_encode($mappedAttrIDS));
		}else{
			return ;
		}
	}
	
	public function fetchAttrDetails($mappedAttributes = array()){
		
		if(isset($mappedAttributes)){
			
			$allAttrInfo	=	array();
			foreach($mappedAttributes as $jetAttrID){
				
				global $wpdb;
				$table_name 	= 	$wpdb->prefix.'jet_attributes_table';
				$qry			= 	"SELECT * FROM `$table_name` WHERE `jet_attr_id`=$jetAttrID;";
				$attrInfo		= 	$wpdb->get_results($qry);
				if(!empty($attrInfo)){
					$allAttrInfo[]	=	$attrInfo;
				}
			}
			return $allAttrInfo;
		}
	}
	
	public function getAllSavedProfileName(){
		
		global $wpdb;
		$table_name 	= 	$wpdb->prefix.'jet_profile_settings';
		$qry			= 	"SELECT `profile_id`,`profile_name` FROM `$table_name` WHERE 1;";
		$attrInfo		= 	$wpdb->get_results($qry);
		
		$profileInfo = array();
		foreach($attrInfo as $retrnObjct){
			
			$profileInfo[$retrnObjct->profile_id]	=	$retrnObjct->profile_name;
		}
		return $profileInfo;
	}
	
	public function getProfileDetail($profileID){
		
		if(!empty($profileID) && $profileID != ''){
			
			global $wpdb;
			$table_name 	= 	$wpdb->prefix.'jet_profile_settings';
			$qry			= 	"SELECT * FROM `$table_name` WHERE `profile_id`=$profileID;";
			$profileInfo	= 	$wpdb->get_results($qry);
			
			return $profileInfo;
		}
	}
	
	/**
	 * get all profile Ids
	 */
	public function get_all_product_Ids(){
		global $wpdb;
		$table_name 	= $wpdb->prefix.'jet_profile_settings';
		$qry 			= "SELECT `profile_id` FROM `$table_name`;";
		$profile_data 	= $wpdb->get_results($qry);
		
		$all_ids = array();
		if(!empty($profile_data)){
			foreach($profile_data as $key=>$id){
				$all_ids[] = $id->profile_id;
			}
		}	
		return $all_ids;
	}
	
	/**
	 * delete error file.
	 */
	public function deleteErrorFile($fileID){
		
		if(!empty($fileID) && $fileID != ''){
			global $wpdb;
			$table_name 		=	$wpdb->prefix.'jet_errorfile_info';
			$resultdata 		= 	$wpdb->delete($table_name,array('jet_file_id' => $fileID));
			print_r($resultdata); print_r($qry); die('adf');
			_e('error file deleted','woocommerce-jet-integration');
			exit;
		}else{
			_e('problem while deleting file','woocommerce-jet-integration');
			exit;
		}
	}
}