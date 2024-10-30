<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if ( !session_id() )
	session_start();

/**
 * Helper for file creation during product uploading.
 */
class cedJetFileCreationHelper{
	
	private static $_instance;
	
	public static function getInstance() {

		if( !self::$_instance instanceof self )
			self::$_instance = new self;

		return self::$_instance;

	}
	
	public function create_file_formatted_array($product){
		
		//echo '<pre>';print_r($product);die("product");
		$this->SKU_Array		= array();
		$this->Attribute_array 	= array();
		$this->result			= array();
		$this->product			= $product;
		
		$unique_check			=	$this->check_unique_code();
		//print_r($unique_check);die;
		if($unique_check	==	'unique_missing'){
			return; 
		}
		else{
			
			$this->sku										=	$this->product[id];
			$name											=	$this->product[name];
			$this->SKU_Array['product_title']				=	$name;
			
			
			
			$this->product_information_array();
			$product_id = $this->sku; 
			
			//get product price on different condition
			$this->get_product_price_information($product_id);
			
			//get product stock on differnet stock option
			$this->get_product_stock_information($product_id);
			
			
			$attr_info							=	$this->create_attribute_array();
			//print_r($attr_info);die('test');
			if($attr_info){
				$_SESSION['upload_product_error'][] =  __('One of  attributes for product: '.$this->product[name].' is missing So this is skipped from upload','woocommerce-jet-integration');
			}
			

			$shipng_exception	=	$this->create_shipping_exception();
			if(!empty($shipng_exception))
				$this->result['shipping_exceptions']	=	$shipng_exception;

			$return_exception	=	$this->create_return_exception();
			if(!empty($return_exception))
				$this->result['return_exceptions']		=	$return_exception;

				$this->SKU_Array['attributes_node_specific'] = $this->Attribute_array; // add attributes details
				$this->result['sku'][$this->sku]= $this->SKU_Array;
				return $this->result;

			}
		}

		public function check_unique_code(){

			$codetype  						= 	trim($this->product['standardCode_type']);
			$codevalue						= 	trim($this->product['standardCode_value']);

		//echo '<pre>';print_r($this->product);die;
			$standard_value_count			=  	strlen($codevalue);

			$asin							=	trim($this->product['asin']);
			$asin_count						=	strlen($asin);

		/* if(empty($asin)){
			$_SESSION['upload_product_error'][] =  __('Asin Value is missing for product: '.$this->product[name].'','woocommerce-jet-integration'); 
			return;
		}else */if(!empty($asin) && $asin_count != 10){
			$_SESSION['upload_product_error'][] = __('Asin value is missing or it must be of 10 charcater for product: '.$this->product[name].'','woocommerce-jet-integration');
			//return;
		}
		if($codetype != 'select'){	
			if(empty($codevalue)){
				$_SESSION['upload_product_error'][] = __('Standard code value is missing for product: '.$this->product[name].'','woocommerce-jet-integration');
				//return;
			}		
			elseif($codetype == 'upc' && $standard_value_count != 12){
				
				$_SESSION['upload_product_error'][] = __('UPC value is must be of 12 charcater for product: '.$this->product[name].'','woocommerce-jet-integration');
				//return 'unique_missing';

			}elseif($codetype == 'gtin14' && $standard_value_count != 14){
				
				$_SESSION['upload_product_error'][] = __('GTIN-14 value is must be of 14 charcater for product: '.$this->product[name].'','woocommerce-jet-integration');
				//return 'unique_missing';
				
			}elseif($codetype == 'isbn13' && $standard_value_count != 13){
				
				$_SESSION['upload_product_error'][] = __('ISBN-13 value is must be of 13 charcater for product: '.$this->product[name].'','woocommerce-jet-integration');
				//return 'unique_missing';
				
			}elseif($codetype == 'isbn10' && $standard_value_count != 10){
				
				$_SESSION['upload_product_error'][] = __('ISBN-10 value is must be of 10 charcater for product: '.$this->product[name].'','woocommerce-jet-integration');
				//return 'unique_missing';
				
			}elseif($codetype == 'ean' && $standard_value_count != 13){
				
				$_SESSION['upload_product_error'][] = __('EAN value is must be of 13 charcater for product: '.$this->product[name].'','woocommerce-jet-integration');
				//return 'unique_missing';
			}
		}	
		if($codetype == 'upc')
			$codetype = 'UPC';
		elseif($codetype == 'gtin14')
			$codetype = 'GTIN-14';
		elseif($codetype == 'ean')
			$codetype = 'EAN';
		elseif($codetype == 'isbn10')
			$codetype = 'ISBN-10';
		elseif($codetype == 'isbn13')
			$codetype = 'ISBN-13';
		elseif($codetype == 'upc-e')
			$codetype = 'UPC-E';

		if(!empty($asin))
			$this->SKU_Array['ASIN']=$asin;

		if(!empty($codetype) && !empty($codevalue) && $codetype != 'select' && $codetype != 'mfr_part_number'){

			$txt['standard_product_code']= $codevalue;
			$txt['standard_product_code_type']=$codetype;
			$this->SKU_Array['standard_product_codes'][]=$txt;
		}

		$mfr_part_number 										=  '';
		if($codetype == 'mfr_part_number'){
			$mfr_part_number  = $codevalue;
		}else{
			$mfr_part_number 										= 	$this->product['jet_mfr_part_number'];
		}

		if(!empty($mfr_part_number))
		{
			$this->SKU_Array['mfr_part_number'] 				= 	$mfr_part_number;
		}

		$brand_name 											= 	$this->product[jet_brand];
		if(!empty($brand_name))
		{
			$this->SKU_Array['brand'] 							= 	$brand_name;
		}


				//check for mfr and brand
		if(empty($this->SKU_Array['standard_product_codes']) && empty($asin)){
			if(empty($mfr_part_number)){
				$_SESSION['upload_product_error'][] = __('mfr part number is required'.$this->product[name].'','woocommerce-jet-integration');
			}
			if(empty($brand_name)){
				$_SESSION['upload_product_error'][] = __('Brand name is required'.$this->product[name].'','woocommerce-jet-integration');
			}
		}
		return $this->SKU_Array;
	}
	
	public function product_information_array(){
		
		
		$nodeid													=	(int)$this->product['jet_cat_id'];
		if(empty($nodeid)){
			$_SESSION['upload_product_error'][] = __('Category Missing for product: '.$this->product[name].' please set product category and reupload','woocommerce-jet-integration');
			return;
		}
		$this->SKU_Array['jet_browse_node_id']					=	$nodeid;
		
		if(!empty($this->product['weight_lbs'])){
			$this->SKU_Array['shipping_weight_pounds'] 			= (float) number_format( (float) $this->product['weight_lbs'], 2, '.', '');

		}
		if(!empty($this->product['length_in']) && !empty($this->product['widht_in']) && !empty($this->product['height_in'])){

			$this->SKU_Array['package_length_inches'] 			= (float) number_format( (float) $this->product['length_in'], 2, '.', '');
			$this->SKU_Array['package_width_inches'] 			= (float) number_format( (float) $this->product['widht_in'], 2, '.', '');
			$this->SKU_Array['package_height_inches'] 			= (float) number_format( (float) $this->product['height_in'], 2, '.', '');
		}

		if(!empty($this->product['display_length_inches']) && !empty($this->product['display_width_inches']) && !empty($this->product['display_height_inches'])){

			$this->SKU_Array['display_length_inches'] 			= (float) number_format( (float) $this->product['display_length_inches'], 2, '.', '');
			$this->SKU_Array['display_width_inches'] 			= (float) number_format( (float) $this->product['display_width_inches'], 2, '.', '');
			$this->SKU_Array['display_height_inches'] 			= (float) number_format( (float) $this->product['display_height_inches'], 2, '.', '');
		}

		$manufacturer 											= 	$this->product['manufacturer'];
		if(!empty($manufacturer))
		{
			$this->SKU_Array['manufacturer'] 					=	$manufacturer;
		}
		
		
		
		
		$number_units_for_price_per_unit 						= 	(float)$this->product['number_units_for_price_per_unit'];
		if(!empty($number_units_for_price_per_unit))
		{
			$this->SKU_Array['number_units_for_price_per_unit'] = 	$number_units_for_price_per_unit;
		}
		
		$type_of_unit_for_price_per_unit 						= 	$this->product[type_of_unit_for_price_per_unit];
		if(!empty($type_of_unit_for_price_per_unit))
		{
			$this->SKU_Array['type_of_unit_for_price_per_unit'] = 	$type_of_unit_for_price_per_unit;
		}
		
		$prop_65 												= 	$this->product[prop_65];
		if($prop_65 == 'true')
		{
			$prop_65 = true;
		}
		else {
			$prop_65 = false;
		}
		$this->SKU_Array['prop_65'] 							= 	$prop_65;
		if(isset($this->product['amazon_item_type_keyword'])&& !empty($this->product['amazon_item_type_keyword']))
		{
			$this->SKU_Array['amazon_item_type_keyword']	=	$this->product['amazon_item_type_keyword'];
		}
		$legal_disclaimer_description 							= 	$this->product['legal_disclaimer_description'];
		if(!empty($legal_disclaimer_description))
		{
			$this->SKU_Array['legal_disclaimer_description'] 	= 	$legal_disclaimer_description;
		}
		
		$cpsia_cautionary_statements 							= 	$this->product['cpsia_cautionary_statements'];
		//
		if(!empty($cpsia_cautionary_statements))
		{
			$cpsia_cautionary_statements  						= json_decode($cpsia_cautionary_statements);
			$this->SKU_Array['cpsia_cautionary_statements'] 	= $cpsia_cautionary_statements;	//stripslashes($cpsia_cautionary_statements);
		}
		
		$safety_warning 										= 	$this->product['safety_warning'];
		if(!empty($safety_warning))
		{
			$this->SKU_Array['safety_warning']					=	$safety_warning;
		}
		
		$msrp													= 	(float)$this->product['msrp'];
		if(!empty($msrp))
		{
			$this->SKU_Array['msrp']							=	$msrp;
		}
		
		$fulfillment_time 										= 	intval($this->product['fulfillment_time']);
		if(!empty($fulfillment_time))
		{
			$this->SKU_Array['fulfillment_time'] 				= 	$fulfillment_time;
		}
		
		$map_price 												= 	(float)$this->product['map_price'];
		if(!empty($map_price))
		{
			$this->SKU_Array['map_price'] 						= 	$map_price;
		}
		
		$map_implementation 									= 	$this->product['map_implementation'];
		if($map_implementation == 'Jet member savings never applied to product')
		{
			$map_implementation = '103';
		}
		elseif($map_implementation == 'Jet member savings on product only visible to logged in Jet members')
		{
			$map_implementation = '102';
			
			if(empty($map_price))
			{
				$_SESSION['upload_product_error'][] = 'Map price should not be empty,if you select "Map implemetation" as Jet member savings on product only visible to logged in Jet members';
			}
		}
		elseif($map_implementation == 'no restrictions on product based pricing') {
			$map_implementation = '101';
		}
		
		if(!empty($map_implementation) && $map_implementation != 'select')
			$this->SKU_Array['map_implementation'] 					= 	$map_implementation;
		
		$ships_alone 											= 	$this->product['ships_alone'];
		
		if($ships_alone == 'true')
		{
			$ships_alone = true;
		}
		else {
			$ships_alone = false;
		}
		$this->SKU_Array['ships_alone'] 						= 	$ships_alone;
		$exclude_from_fee_adjustments 							= 	$this->product['exclude_from_fee_adjustments'];
		
		if($exclude_from_fee_adjustments == 'true')
		{
			$exclude_from_fee_adjustments = true;
		}
		else {
			$exclude_from_fee_adjustments = false;
		}
		$this->SKU_Array['exclude_from_fee_adjustments'] 		=	 $exclude_from_fee_adjustments;
		
		$bullets 												= 	 $this->product['bullets'];
		if(!empty($bullets))
		{
			$bullets 											=	json_decode($bullets);
			$this->SKU_Array['bullets'] 						= 	 $bullets;
		}
		
		$bullets_var1 = $this->product['bullets_1'];
		$bullets_var2 = $this->product['bullets_2'];
		$bullets_var3 = $this->product['bullets_3'];
		$bullets_var4 = $this->product['bullets_4'];
		$bullets_var5 = $this->product['bullets_5'];
		if(!empty($bullets_var1) || !empty($bullets_var2) || !empty($bullets_var3) || !empty($bullets_var4) || !empty($bullets_var5))
		{
			$bullets_var = array();
			$bullets_var['0'] = $bullets_var1;
			$bullets_var['1'] = $bullets_var2;
			$bullets_var['2'] = $bullets_var3;
			$bullets_var['3'] = $bullets_var4;
			$bullets_var['4'] = $bullets_var5;
			if(!empty($bullets_var)){
				$this->SKU_Array['bullets']      			= $bullets_var;
			}
		}
		$product_tax_code 										= 	 $this->product['product_tax_code'];
		if(!empty($product_tax_code) && $product_tax_code != 'select')
		{
			$this->SKU_Array['product_tax_code'] 				= 	 $product_tax_code;
		}
		
		$this->SKU_Array['multipack_quantity']					= 	 1;
		
		$product_desc											= 	$this->product['desc'];
		
		if(!empty($product_desc))
			$this->SKU_Array['product_description']				=	$product_desc;	 
		
		$country_name 											= 	 $this->product['jet_country_manufacture'];
		
		if($country_name){
			$this->SKU_Array['country_of_origin']				= 	 $country_name;
		}
		
		$image= $this->product['jet_product_image_link'];
		
		if(isset($image)){
			$this->SKU_Array['main_image_url']=  $image;
		}
		
		$alternate_image 										=	$this->product['gallery_images'];
		if(count($alternate_image))
		{
			//$alternate_image    								=    array_diff($alternate_image, $image);
			foreach($alternate_images as $tmp_image){
				if($tmp_image !== $image){
					$alternate_image[] = $tmp_image;
				}
			}
		} 
		
		$Alternate_images	=	array();
		$alt_img_urls		=	array();
		$start_count		=	1;
		if(count($alternate_image))
		{
			foreach($alternate_image as $slot_id => $alt_img_url)
			{
				$Alternate_images[]	=	array('image_slot_id'=>$start_count,'image_url'=>$alt_img_url);
				$start_count++;
			}
			$this->SKU_Array['alternate_images']	=	$Alternate_images;
		}
	}

		/**
		 * 
		 * @param unknown $product_id
		 * @return string
		 */
		public function get_product_price_information($product_id){
			
			$select_price				=  get_post_meta($product_id,'jetPriceSelect',true);
			if(empty($select_price))
				$select_price = 'main_price';
			
			$select_price = trim($select_price);
			
			$price_value				=  get_post_meta($product_id,'jetPrice',true);
			$main_price					= 	get_post_meta($product_id,'_regular_price',true);
			$jet_node_id				= get_option( 'jet_node_id');
			$jet_node_id    			= json_decode($jet_node_id);
			
			if(!empty($jet_node_id)){
				
				//if price is set to main price
				if('main_price' == $select_price){
					$prices 						= 	get_post_meta($product_id,'_regular_price',true);
					$prices							= 	$prices ? $prices : get_post_meta($product_id,'_sale_price',true);
					
					if(empty($prices)){
						$_SESSION['upload_product_error'][] = __('please set either regular price and sale price for product: '.$this->product[name].'','woocommerce-jet-integration');
					}
					else{
						foreach($jet_node_id as $key => $fullfillment_id){
						// Add price
							$price[$this->sku]['price']		=	(float)$main_price;
							$node['fulfillment_node_id']	=	"$fullfillment_id";
							$node['fulfillment_node_price']	=	(float)$prices;
							$price[$this->sku]['fulfillment_nodes'][]= $node; //price
						}
						$this->result['price']		=	$price;
					}	
				}



			 	//if price is set to sale price
				if('sale_price' == $select_price){
					$prices 						= 	get_post_meta($product_id,'_sale_price',true);
					if(empty($prices)){
						$prices							= 	$prices ? $prices : get_post_meta($product_id,'_regular_price',true);
					}
					if(empty($prices)){
						$_SESSION['upload_product_error'][] = __('please set sale price for product: '.$this->product[name].'','woocommerce-jet-integration');

					}else{						
						foreach($jet_node_id as $key => $fullfillment_id){
						// Add price
							$price[$this->sku]['price']		=	(float)$main_price;
							$node['fulfillment_node_id']	=	"$fullfillment_id";
							$node['fulfillment_node_price']	=	(float)$prices;
							$price[$this->sku]['fulfillment_nodes'][]= $node; //price
						}
						$this->result['price']		=	$price;
					}	
				}

			 	//if select other price for store
				if('otherPrice' == $select_price){
					$prices 						= 	get_post_meta($product_id,'jetPrice',true);
					if(empty($prices)){
						$_SESSION['upload_product_error'][] = __('please set Other price for product: '.$this->product[name].'','woocommerce-jet-integration');
					}else{

						foreach($jet_node_id as $key => $fullfillment_id){
			 				// Add price
							$price[$this->sku]['price']		=	(float)$main_price;
							$node['fulfillment_node_id']	=	"$fullfillment_id";
							$node['fulfillment_node_price']	=	(float)$prices;
			 				$price[$this->sku]['fulfillment_nodes'][]= $node; //price
			 			}
			 			$this->result['price']		=	$price;
			 		}
			 	}
			 	
			 	//if select fullmillment type to set price
			 	if('fullfillment_wise' == $select_price){
			 		
			 		//echo '<pre>';print_r($this->product);die;
			 		foreach($jet_node_id as $key => $fullfillment_id){

			 			$prices 	=	 get_post_meta($product_id, 'p_'.$fullfillment_id, true);//$this->product['p_'.$fullfillment_id];
			 			if(empty($prices)){
			 				$_SESSION['upload_product_error'][] = __('please set price for fullfillment node id : '.$fullfillment_id  .' for product: '.$this->product[name].'','woocommerce-jet-integration');
			 				//return;
			 			}
			 				// Add price
			 			$price[$this->sku]['price']=(float)$main_price;
			 			$node['fulfillment_node_id']="$fullfillment_id";
			 			$node['fulfillment_node_price']=(float)$prices;
			 			$price[$this->sku]['fulfillment_nodes'][]= $node; //price

			 		}
			 		$this->result['price']		=	$price;
			 	}
			 	
			 	
			 }else{
			 	$_SESSION['upload_product_error'][] = __('Fullfillment is not set please go to jet configuration settings and save fullfillment settings','woocommerce-jet-integration');
			 	return 'fullfillment_missing' ;
			 }	
			}


		/**
		 * 
		 * @param unknown $product_id
		 */
		public function get_product_stock_information($product_id){


			$select_stock				=  get_post_meta($product_id,'jetStockSelect',true);
			
			if(empty($select_stock))
				$select_stock = 'central';

			$select_stock = trim($select_stock);
			
			$selected_value				=  get_post_meta($product_id,'jetStock',true);

			$jet_node_id	= get_option( 'jet_node_id');
			$jet_node_id    = json_decode($jet_node_id);

			if(!empty($jet_node_id)){

				//if stock is set to centarl stock
				if('central' == $select_stock){
					$stock	 						= 	get_post_meta($product_id,'_stock',true);
					if(empty($stock)){
						$_SESSION['upload_product_error'][] = __('please set stock of the product for product: '.$this->product[name].'','woocommerce-jet-integration');
					}
					else{
						foreach($jet_node_id as $key => $fullfillment_id){
							// Add price
							$qty	= $stock;
							$node1['fulfillment_node_id']="$fullfillment_id";
							$node1['quantity']=(int)$qty;
							$inventory[$this->sku]['fulfillment_nodes'][]=$node1; // inventory
						}
						$this->result['inventory']	=	$inventory;
					}
				}
				
				//if stock is set to default stock 99
				if('default' == $select_stock){
					foreach($jet_node_id as $key => $fullfillment_id){
							// Add price
						$qty	= 99;
						$node1['fulfillment_node_id']="$fullfillment_id";
						$node1['quantity']=(int)$qty;
							$inventory[$this->sku]['fulfillment_nodes'][]=$node1; // inventory
						}
						$this->result['inventory']	=	$inventory;
					}

					//if select other price for store
					if('other' == $select_stock){
						$stock 						= 	get_post_meta($product_id,'jetStock',true);

						if(empty($stock)){
							$_SESSION['upload_product_error'][] = __('Please set Other Stock option for product: '.$this->product[name].'','woocommerce-jet-integration');
						}else{

							foreach($jet_node_id as $key => $fullfillment_id){
							// Add price
								$qty	= $stock;
								$node1['fulfillment_node_id']="$fullfillment_id";
								$node1['quantity']=(int)$qty;
							$inventory[$this->sku]['fulfillment_nodes'][]=$node1; // inventory
						}
						$this->result['inventory']	=	$inventory;
					}
				}

				//if select fullmillment type to set stock
				
				if('fullfillment_wise' == $select_stock){

					foreach($jet_node_id as $key => $fullfillment_id){

						$stock 	=	 get_post_meta($product_id, 's_'.$fullfillment_id, true);//$this->product['p_'.$fullfillment_id];
						if(empty($stock)){
							$_SESSION['upload_product_error'][] = __('please set stock for fullfillment node id : '.$fullfillment_id  .' for product: '.$this->product[name].'','woocommerce-jet-integration');
							//return;
						}
						
						$qty	= $stock;
						$node1['fulfillment_node_id']="$fullfillment_id";
						$node1['quantity']=(int)$qty;
						$inventory[$this->sku]['fulfillment_nodes'][]=$node1; // inventory

					}
					$this->result['inventory']	=	$inventory;
				}

			}else{
				$_SESSION['upload_product_error'][] = __('Fullfillment is not set please go to jet configuration settings and save fullfillment settings','woocommerce-jet-integration');
				return 'fullfillment_missing' ;
			}
		}
		
		
		public function create_attribute_array(){

			$attribute	=	$this->product['all_attributes'];
			if(count($attribute))
			{
				$counter 	= 	0;
				$empty		=	false;
				foreach ($attribute as $att_id => $att_val)
				{
					if(!empty($att_val) && $att_val != 'none'){
						
						$explode_check	=	explode('_', $att_id);
						if($explode_check[count($explode_check)-1] == 'unit')
						{
							continue;
						}
						if(empty($att_val))
						{
							$empty	=	true;
						}
						$attr_unit	=	$attribute[$att_id.'_unit'];
						if(!empty($attr_unit))
						{
							$this->Attribute_array[$counter] = array(
								'attribute_id'=>$att_id,
								'attribute_value'=>$att_val,
								'attribute_value_unit' =>$attr_unit,
								);
						}else{
							$this->Attribute_array[$counter] = array(
								'attribute_id'=>$att_id,
								'attribute_value'=>$att_val,
								);
						}
						$counter++;
					}
				}
			}
			if($empty){
				return $empty;
			}
			else{
				return false;
			}
		}
		
		public function create_relationship_file($skus,$attr_ids){
			
			$variation_refinements	=	array();
			$attr_data_to_send = $this->Attribute_array;
//		print_r($attr_data_to_send);die('die');
			if(!empty($attr_data_to_send) && is_array($attr_data_to_send)){
				
				foreach($attr_data_to_send as $slctd_attr){
					
					if(isset($slctd_attr['attribute_id']) && !empty($slctd_attr['attribute_id']) && check_if_variant($slctd_attr['attribute_id']))
						$variation_refinements[] = $slctd_attr['attribute_id'];
					
				}
			}

			
			$relationship		=	array();
			$parent_sku			=	$skus[0];
			$children_skus		=	array();
			for($counter=1;$counter<count($skus);$counter++){
				$children_skus[]	=	"$skus[$counter]";
			}
				
				$relationship["$parent_sku"]["relationship"]			=	"Variation";
				$relationship["$parent_sku"]["variation_refinements"]	=	$variation_refinements;
				$relationship["$parent_sku"]["children_skus"]			=	$children_skus;

			//print_r($relationship); die("fine");
// 			print_R(json_encode($relationship));die("relationship");
				return $relationship;
			}

			public function create_shipping_exception(){

				$id = $this->product['id'];

				$productbyid 		= 	get_product($id);
				
				if($productbyid->is_type('simple')){
					$ship_excptn_detail	=	$this->product['ship_excp_detail'];
				}else{
					$ship_excptn_detail	=	$this->product['shipping_settings']['ship_excp_detail'];
				}


			//echo '<pre>';print_r($ship_excptn_detail);die;
				if(!empty($ship_excptn_detail) && count($ship_excptn_detail)):

					$shipping_exception_array	=	array();
				$fullfillment_data			=	array();
				foreach($ship_excptn_detail as $excptn_counter	=>	$excptn_data)
				{
					$fullfillment_data['fulfillment_nodes'][]	=	$excptn_data;
				}
				if(!empty($fullfillment_data))
				{
					$shipping_exception_array[$this->sku]	=	$fullfillment_data;
				}

				return $shipping_exception_array;
				
				endif;
			}


			public function create_return_exception(){

				$id = $this->product['id'];
				
				$productbyid 		= 	get_product($id);

				if($productbyid->is_type('simple')){
					$return_excptn_detail	=	$this->product['return_excptn_data'];
				}else{
					$return_excptn_detail	=	$this->product['return_settings']['return_excptn_data'];
				}
				


				if(!empty($return_excptn_detail))
				{
					$return_exception_array[$this->sku]		=	$return_excptn_detail;
				}

				return $return_exception_array;
			}
		}