<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'class-cedJetLibraryFunctions.php';
require_once 'class-cedJetDBHelper.php';
require_once 'class-cedJetFileUploadHelper.php';
require_once 'class-productManagement.php';

class cedJetCustomTabs{

	private static $_instance;

	public static function getInstance() {

		if( !self::$_instance instanceof self )
			self::$_instance = new self;

		return self::$_instance;

	}
	
	public function __construct() {
	
		$this->libraryAction 		= 	cedJetLibraryFunctions::getInstance();
		$this->modelAction			=	cedJetDBHelper::getInstance();
		$this->fileUploadHelper		=	cedJetFileUploadHelper::getInstance();
		$this->productManager		=	cedJetProductManagement::getInstance();
	}
	
	public function addCutomProductTAb(){
		
		?><li class="custom_tab"><a href="#jet_attribute_settings"><?php _e('Jet Attributes', 'woocommerce-jet-integration'); ?></a></li>
		<li class="custom_tab"><a href="#jet_extra_attribute_settings"><?php _e('Jet Extra Attributes', 'woocommerce-jet-integration'); ?></a></li>
		<li class="custom_tab"><a href="#jet_shipping_settings"><?php _e('Shipping Exception', 'woocommerce-jet-integration'); ?></a></li>
		<li class="custom_tab"><a href="#jet_return_settings"><?php _e('Return Exception', 'woocommerce-jet-integration'); ?></a></li><?php 
	}
	
	public function JetcustomTabFields(){
		
		global $post;
		$_product 		= 	get_product($post->ID);
		
		if(!$_product->is_type('simple'))
			return;
		
		$custom_tab_options = array(
			'jet_asin_value' 			 	=> get_post_meta($post->ID,'jet_asin', true),
			'selectedCode'					=> get_post_meta($post->ID,'jetSelectedCode',true),
			'standardCodeValue'				=> get_post_meta($post->ID,'standardCode',true),
			'jetBrand'						=> get_post_meta($post->ID,'jetBrand',true),
			'jetPriceSelect'				=> get_post_meta($post->ID,'jetPriceSelect',true),
			'jetPrice'						=> get_post_meta($post->ID, 'jetPrice',true),
			'jetStockSelect'				=> get_post_meta($post->ID, 'jetStockSelect',true),
			'jetStock'						=> get_post_meta($post->ID, 'jetStock',true)
		);
		$extra_tab_options = array(

		'jet_country' 			 				=>	 get_post_meta($post->ID, 'jet_country', true),
		'product_manufacturer' 			 		=> 	 get_post_meta($post->ID, 'product_manufacturer', true),
		'jet_mfr_part_number' 		 			=> 	 get_post_meta($post->ID, 'jet_mfr_part_number', true),
		'number_units_for_price_per_unit' 		=> 	 get_post_meta($post->ID, 'number_units_for_price_per_unit', true),
		'type_of_unit_for_price_per_unit' 		=>	 get_post_meta($post->ID, 'type_of_unit_for_price_per_unit', true),
		'shipping_weight_pounds' 			 	=> 	 get_post_meta($post->ID, 'shipping_weight_pounds', true),
		'package_length' 					 	=> 	 get_post_meta($post->ID, 'package_length', true),
		'package_width' 	 					=> 	 get_post_meta($post->ID, 'package_width', true),
		'package_height' 						=> 	 get_post_meta($post->ID, 'package_height', true),
		'prop_65' 								=> 	 get_post_meta($post->ID, 'prop_65', true),
		'legal_disclaimer_description' 			=> 	 get_post_meta($post->ID, 'legal_disclaimer_description', true),
		'bullets'					 			=> 	 get_post_meta($post->ID, 'bullets', true),
		'safety_warning'					 	=> 	 get_post_meta($post->ID, 'safety_warning', true),
		'msrp'					 				=> 	 get_post_meta($post->ID, 'msrp', true),
		'map_price'					 			=> 	 get_post_meta($post->ID, 'map_price', true),
		'map_implementation'					=> 	 get_post_meta($post->ID, 'map_implementation', true),
		'fulfillment_time'					 	=> 	 get_post_meta($post->ID, 'fulfillment_time', true),
		'product_tax_code'					 	=> 	 get_post_meta($post->ID, 'product_tax_code', true),
		'cpsia_cautionary_statements' 			=> 	 get_post_meta($post->ID, 'cpsia_cautionary_statements', true),
		'exclude_from_fee_adjustments'			=> 	 get_post_meta($post->ID, 'exclude_from_fee_adjustments', true),
		'ships_alone'							=> 	 get_post_meta($post->ID, 'ships_alone', true),
		'amazon_item_type_keyword'		        =>   get_post_meta($post->ID, 'amazon_item_type_keyword',true),
		//'jetbackorders'		        			=>   get_post_meta($post->ID, 'jetbackorders',true)
);?>
		<!-- standard product data tab -->
    <div id="jet_attribute_settings" class="panel woocommerce_options_panel">
        <div class="options_group custom_tab_options"> 
        	<p class="form-field">
        		<strong><?php _e('product standard codes','woocommerce-jet-integration');?></strong>
        		<img class="help_tip" data-tip="<?php _e("At least one of the following must be provided for each merchant SKU: Standard Product Code (UPC, GTIN-14 etc.), ASIN, or Brand and Manufacturer Part Number", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
        	</p>
             <p class="form-field">
                <label><?php _e('ASIN', 'woocommerce-jet-integration'); ?></label>
	            <input type="text" size="5" name="jet_asin" value="<?php echo @$custom_tab_options['jet_asin_value']; ?>" placeholder="<?php _e('Product ASIN code', 'woocommerce-jet-integration'); ?>" />
	            <img class="help_tip" data-tip="<?php _e("ASIN value of your product", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
             </p>
           	 <p class="form-field">
           	 	<label>
           	 		<select name="jetSelectedCode">
           	 			<option value="select" <?php if(@$custom_tab_options['selectedCode'] == 'select'){ echo "selected"; }?>><?php _e('select','woocommerce-jet-integration');?></option>
           	 			<option value="upc" <?php if(@$custom_tab_options['selectedCode'] == 'upc'){ echo "selected"; }?>><?php _e('UPC','woocommerce-jet-integration');?></option>
           	 			<option value="upce" <?php if(@$custom_tab_options['selectedCode'] == 'upce'){ echo "selected"; }?>><?php _e('UPC-E','woocommerce-jet-integration');?></option>
           	 			<option value="gtin14" <?php if(@$custom_tab_options['selectedCode'] == 'gtin14'){ echo "selected"; }?>><?php _e('GTIN-14','woocommerce-jet-integration');?></option>
           	 			<option value="isbn13" <?php if(@$custom_tab_options['selectedCode'] == 'isbn13'){ echo "selected"; }?>><?php _e('ISBN-13','woocommerce-jet-integration');?></option>
           	 			<option value="isbn10" <?php if(@$custom_tab_options['selectedCode'] == 'isbn10'){ echo "selected"; }?>><?php _e('ISBN-10','woocommerce-jet-integration');?></option>
           	 			<option value="ean" <?php if(@$custom_tab_options['selectedCode'] == 'ean'){ echo "selected"; }?>><?php _e('EAN','woocommerce-jet-integration');?></option>
           	 		</select>
           	 		<img class="help_tip" data-tip="<?php _e("Another product code(optional, if you provide ASIN)", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
           	 	</label>
           	 	<input type="text" size="5" name="standardCodeValue" value="<?php echo @$custom_tab_options['standardCodeValue']; ?>" placeholder="<?php _e('value', 'woocommerce-jet-integration'); ?>" />
           	 	<img class="help_tip" data-tip="<?php _e("value of the selected standard product code", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
           	 </p>
           	 <p class="form-field">
                <label><?php _e('Brand', 'woocommerce-jet-integration'); ?></label>
	            <input type="text" size="5" name="jetBrand" value="<?php echo @$custom_tab_options['jetBrand']; ?>" placeholder="<?php _e('Product Brand', 'woocommerce-jet-integration'); ?>" />
	            <img class="help_tip" data-tip="<?php _e("Brand Name Of Your Product.", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
             </p>
           	 <p class="form-field">
	           <label><?php _e('MFR Part Number:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="jet_mfr_part_number" value="<?php echo @$extra_tab_options['jet_mfr_part_number']; ?>" placeholder="<?php _e('Value', 'woocommerce-jet-integration'); ?>" />
		            <img class="help_tip" data-tip="<?php _e("Manufacturer part number of you product(provided by original manufacturer of the product).", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
	        </p>
    	</div>
    	<!-- jet price field -->
    	<div class="options_group">
    		<p class="form-field">
    			<?php $type = 'sale';?>
    			<label><?php _e('Jet Price')?></label>
    			<select name="jetPriceSelect" id="priceTypeSelect" class="select short">
           	 			<option value="main_price" <?php if(@$custom_tab_options['jetPriceSelect'] == 'main_price'){ echo "selected"; }?>><?php _e('MAIN PRICE','woocommerce-jet-integration');?></option>
           	 			<option value="sale_price" <?php if(@$custom_tab_options['jetPriceSelect'] == 'sale_price'){ echo "selected"; }?>><?php _e('SALE PRICE','woocommerce-jet-integration');?></option>
           	 			<option value="otherPrice" <?php if(@$custom_tab_options['jetPriceSelect'] == 'otherPrice'){ echo "selected"; $type= 'other'; }?>><?php _e('OTHER','woocommerce-jet-integration');?></option>
           	 			<option value="fullfillment_wise" <?php if(@$custom_tab_options['jetPriceSelect'] == 'fullfillment_wise'){ echo "selected"; $type='fulfilment'; }?>><?php _e('FULLFILLMENT WISE','woocommerce-jet-integration');?></option>
           	 	</select>
           	 	<img class="help_tip" data-tip="<?php _e("Product price you want to send on jet.", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
           	 </p>
           	 <p class="form-field" id="jetPriceField" <?php if($type!='other'){ echo 'style="display: none"'; }?>>
                <label><?php _e('price', 'woocommerce-jet-integration'); ?></label>
	            <input type="text" size="5" name="jetPrice" value="<?php echo @$custom_tab_options['jetPrice']; ?>" placeholder="<?php _e('Product price', 'woocommerce-jet-integration'); ?>" />
             </p>
             <?php 
	        $jet_node_id = get_option('jet_node_id');
	        $jet_node_id = json_decode($jet_node_id);
	        global $post;
	        ?>
	        <div  id="fullfillmetWisePrice" <?php if($type!='fulfilment'){ echo 'style="display: none"'; }?>>
	        <?php if(!empty($jet_node_id)){  ?>
	        	<?php foreach($jet_node_id as $key => $value){ 
	        			$price 	  = get_post_meta($post->ID, 'p_'.$value, true);
	        	?>
		        <p class="form-field dimensions_field">
					<label for="product_length"><?php _e('Price for '.$value,'woocommerce-jet-integration')?></label>
						<span class="wrap">
							<input type="text" value="<?php echo $price;?>" name="p_<?php echo $value;?>" size="6" class="input-text wc_input_decimal" placeholder="Price" id="">
						</span>
				</p>
			<?php } ?>
			<?php } else{
				?>
				 <p class="form-field">
				<h4 style="text-align:center;"><?php  _e('Please first Fill Fullfillment details under Jet Configuration tab', 'woocommerce-jet-integration'); ?></h4>
	         </p>
				<?php 
				
			}?>
			</div>
    	</div>
    	<!-- price field end. -->
    	
    	
    	<!-- stock field -->
    	<div class="options_group">
    		<p class="form-field">
    			<?php $stype = 'central';?>
    			<label><?php _e('Jet Stock', "woocommerce-jet-integration")?></label>
    			<select name="jetStockSelect" id="stockTypeSelect" class="short">
           	 			<option value="central" <?php if(@$custom_tab_options['jetStockSelect'] == 'central'){ echo "selected"; }?>><?php _e('central (product Stock)','woocommerce-jet-integration');?></option>
           	 			<option value="default" <?php if(@$custom_tab_options['jetStockSelect'] == 'default'){ echo "selected"; }?>><?php _e('default (99)','woocommerce-jet-integration');?></option>
           	 			<option value="other" <?php if(@$custom_tab_options['jetStockSelect'] == 'other'){ echo "selected"; $stype= 'other'; }?>><?php _e('OTHER','woocommerce-jet-integration');?></option>
           	 			<option value="fullfillment_wise" <?php if(@$custom_tab_options['jetStockSelect'] == 'fullfillment_wise'){ echo "selected"; $stype='fulfilment'; }?>><?php _e('FULLFILLMENT WISE','woocommerce-jet-integration');?></option>
           	 	</select>
           	 	<img class="help_tip" data-tip="<?php _e("Product Stock you want to send on jet", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
           	 </p>
           	 <p class="form-field" id="jetStockField" <?php 
           	 if($stype!='other'){ echo 'style="display: none"'; }?>>
                <label><?php _e('Stock', 'woocommerce-jet-integration'); ?></label>
	            <input type="text" size="5" name="jetStock" value="<?php echo @$custom_tab_options['jetStock']; ?>" placeholder="<?php _e('Product stock', 'woocommerce-jet-integration'); ?>" />
             </p>
             <?php 
	        $jet_node_id = get_option('jet_node_id');
	        $jet_node_id = json_decode($jet_node_id);
	        global $post;
	       // echo '<pre>';print_r($post);die;
	        ?>
	        <div  id="fullfillmetWiseStock" <?php if($type!='fulfilment'){ echo 'style="display: none"'; }?>>
	        <?php if(!empty($jet_node_id)){  ?>
	        	<?php foreach($jet_node_id as $key => $value){ 
	        			$stock    = get_post_meta($post->ID, 's_'.$value, true);
	        	?>
		        <p class="form-field dimensions_field">
					<label for="product_length"><?php _e('Stock for '.$value,'woocommerce-jet-integration')?></label>
						<span class="wrap">
							<input type="text" value="<?php echo $stock;?>" name="s_<?php echo $value;?>" size="6" class="input-text wc_input_decimal" placeholder="Stock" id="">
						</span>
				</p>
			<?php } ?>
			<?php }else{ ?>
			 <p class="form-field">
				<h4 style="text-align:center;"><?php  _e('Please first Fill Fullfillment details under Jet Configuration tab', 'woocommerce-jet-integration'); ?></h4>
	         </p>
	 		 <?php } ?>
			</div>
    	</div>
    	<!-- stock field end -->
    	
    	
    	
    	<!-- jet provided attributes for selected category. -->
    	<?php 
    	$productCats 			= 	get_the_terms($post->ID, "product_cat");
    	$mappedIDs				=	get_option('cedWooJetMapping',true);
    	
    	if(!empty($productCats)):
	    	if(is_array($mappedIDs)){
	    			
	    		$catArray	=	array();
	    		foreach($mappedIDs as $woocatid => $jetcatId){
	    				
	    			$catArray[]	=	$woocatid;
	    		}
	    			
	    		if(!empty($catArray) && is_array($catArray)){
	    			
					$jetSelectedNodeID = array();
	    			foreach ($productCats as $index	=>	$catObject){
	    				if(in_array($catObject->term_id, $catArray)){
	    					
	    					$jetSelectedNodeID[$catObject->term_id]	=	$mappedIDs[$catObject->term_id];
	    				}
	    			}
	    		}
	    	}
    	
	    	$jetAttrInfo	=	array();
	    	if(!empty($jetSelectedNodeID)):
	    	foreach($jetSelectedNodeID as $wooNodeID => $jetNodeID){
	    		
				$mappedAttributes		=	get_option($jetNodeID.'_linkedAttributes',false);
				//print_r($jetNodeID.'_linkedAttributes'); die("test");
				if($mappedAttributes){
					
					$mappedAttributes	=	json_decode($mappedAttributes);
					if(is_array($mappedAttributes)){
							
						$jetAttrInfo[$jetNodeID]	=	$this->modelAction->fetchAttrDetails($mappedAttributes);//$mappedAttributes
						
					}
				}
	    	}
	    	
	    	$enable 				= 	get_post_meta($post->ID,'selectedCatAttr', true);
	    	
	    if(!empty($jetAttrInfo) && count($jetAttrInfo)):
	    
	    	foreach($jetAttrInfo as $jetNode => $mappedCAT):
	      		$wooCatID 	=	array_search($jetNode, $jetSelectedNodeID);
	      		$term	=	get_term_by('id', $wooCatID, 'product_cat');
	      
	    		$mappedWooCatName	=	'';
	    		
	    		if(isset($term)){
	
	    			$mappedWooCatName	=	$term->name;
	    		}
	    		if($enable == $jetNode){
	    			$check = "checked='checked'";
	    		}else{
	    			$check	=	'';
	    		}?>
	    	<div class="options_group" >
	    		<p><?php _e($mappedWooCatName." JET Attributes",'wocommerce-jet-integration');?>
	    			<input type="radio" class="jet-category-select" name="selectedCatAttr" value="<?php echo $jetNode;?>" <?php echo $check;?>>
	    			<img class="expand-image" value="<?php echo $jetNode; ?>" style="float: right;" src="<?php echo CEDJETINTEGRATION; ?>expand.png" height="16" width="16" />
	    			<img class="help_tip" data-tip="<?php _e("Select this if you want to send the attributes mapped with this jet category(only single category attributes can be sent to jet.)", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
	    		</p>
	    	</div>
	    	<div class="options_group" id="<?php echo $jetNode;?>" style="display: none">
	      <?php foreach($mappedCAT as $attrARRAY):
	      
	    		$attrObject = $attrARRAY[0];
	      
	      		if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit)) ):
	      		
	      		$values	=	json_decode($attrObject->unit);
	      		
	      		$assocValues			=	array();
	      		$assocValues['none']	=	'Select A Value';
	      		 
	      		if(!empty($values)){
	      		foreach($values as $VALUE):
	      			$assocValues[$VALUE]	=	$VALUE;
	      		endforeach;
	      		}
	      			$this->libraryAction->cedcommerce_text_with_unit_select(
						array(
							'id' => $jetNode."_".$attrObject->jet_attr_id,
							'name' => $jetNode."_".$attrObject->jet_attr_id,
							'label' => __($attrObject->name , 'woocommerce-jet-integration'),
							'value1'=>  get_post_meta($post->ID, $jetNode."_".$attrObject->jet_attr_id, true),
							'value2'=> get_post_meta($post->ID, $jetNode."_".$attrObject->jet_attr_id."_unit", true),
							'options' => $assocValues,
							'description' => 'please provide value and select unit',
					)
	      		);
	      		endif;
	    		if($attrObject->freetext == 1):?>
		    		<p class="form-field dimensions_field">
						<label for="jetAttributes"><?php echo $attrObject->name;?></label>
						<?php $tempName	=	$jetNode."_".$attrObject->jet_attr_id;?>
						<?php $tempValue	=	get_post_meta($post->ID , $tempName , true);?>
						<input type="text" value="<?php echo $tempValue;?>" name="<?php echo $tempName;?>" size="5" >
						<?php if($attrObject->variant==1){?><img class="help_tip" data-tip="<?php _e("Variant must be filled", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /><?php } ?>
					</p>
	    	<?php endif;
	    	
	    		if($attrObject->freetext == 0 && !empty($attrObject->values) && empty($attrObject->unit)):
	    		
	    			$values	=	json_decode($attrObject->values);
	    		
	    			$assocValues	=	array();
	    			$assocValues['none']	=	'Select A Value';
	    			
	    			if(!empty($values)){
	    			foreach($values as $VALUE):
	    				$assocValues[$VALUE]	=	$VALUE;
	    			endforeach;
	    			}
					if($attrObject->variant==0){
		            	woocommerce_wp_select(
		              						array(
		              			        		'id'      => $variation->ID."_".$jetNode.'_'.$attrObject->jet_attr_id,
		              			        		'label'   => __( $attrObject->name, 'woocommerce-jet-integration' ),
		              			        		'description' => __( 'Select a value.', 'woocommerce-jet-integration' ),
		              			        		'value'       => get_post_meta( $variation->ID, $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id , true ),
		              			        		'options' => $assocValues,
		              			        		)
		              			        	);
	              			  		}
              			            			if($attrObject->variant==1){
              			            				woocommerce_wp_select(
													array(
													'id'      => $variation->ID."_".$jetNode.'_'.$attrObject->jet_attr_id,
													'label'   => __( $attrObject->name, 'woocommerce-jet-integration' ),
													'description' => __( ' Used as Variant', 'woocommerce-jet-integration' ),
													'value'       => get_post_meta( $variation->ID, $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id , true ),
													'options' => $assocValues,
													)
													);
              			            			}
	    		endif;?>
	    	<?php endforeach;?>
	    	</div>
	    	<?php endforeach;?>
	    	<?php endif;?>
   <!--  </div> -->
    <?php endif;?>
    <?php endif;?>
    </div>
    
   <!-- standard product data tab end. --> 
   <!-- extra attribute data tab start -->
   <?php $bullets = json_decode($extra_tab_options['bullets']);?>

	<div id="jet_extra_attribute_settings" class="panel woocommerce_options_panel">
		
		 <div class="options_group custom_tab_options">                                                

		    <!-- field first -->
		    <p class="form-field">
	           <label><?php _e('Country Manufacturer:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="jet_country" value="<?php echo @$extra_tab_options['jet_country']; ?>" placeholder="<?php _e('Manufacturer Country', 'woocommerce-jet-integration'); ?>" />
	        </p>
	       	<!-- First Field End  -->
	       
	       <!-- field second -->
		    <p class="form-field">
	           <label><?php _e('Product Manufacturer:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="product_manufacturer" value="<?php echo @$extra_tab_options['product_manufacturer']; ?>" placeholder="<?php _e('Value', 'woocommerce-jet-integration'); ?>" />
	        </p>
	       	<!--  second field End  -->
	       	
	       	<p class="form-field">
	           <label><?php _e('Safety Warning:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="safety_warning" value="<?php echo @$extra_tab_options['safety_warning']; ?>" placeholder="<?php _e('Safety Warning', 'woocommerce-jet-integration'); ?>" />
	        </p>
	        
	        <p class="form-field">
	           <label><?php _e('Fullfillment Time:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="fulfillment_time" value="<?php echo @$extra_tab_options['fulfillment_time']; ?>" placeholder="<?php _e('Days', 'woocommerce-jet-integration'); ?>" />
	        </p>
	        
	        <p class="form-field">
	           <label><?php _e('Amazon Item Type Keyword:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="amazon_item_type_keyword" value="<?php echo @$extra_tab_options['amazon_item_type_keyword']; ?>" placeholder="<?php _e('Amazon Keyword', 'woocommerce-jet-integration'); ?>" />
	        </p>
	        
	        <p class="form-field">
	           <label><?php _e('Manufacturer suggested retail price:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="msrp" value="<?php echo @$extra_tab_options['msrp']; ?>" placeholder="<?php _e('Value', 'woocommerce-jet-integration'); ?>" />
	        </p>
	       	
	       	<p class="form-field">
	           <label><?php _e('Map Price:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="map_price" value="<?php echo @$extra_tab_options['map_price']; ?>" placeholder="<?php _e('Value', 'woocommerce-jet-integration'); ?>" />
	        </p>
	       	
	       	 <p class="form-field">
	           <label><?php _e('Map implementation:', 'woocommerce-jet-integration'); ?></label>
		           <?php $map = array('select','no restrictions on product based pricing',
		           		'Jet member savings on product only visible to logged in Jet members',
		           'Jet member savings never applied to product',);?>
		           <select name="map_implementation" class="select sort variable_dropdown_size">
		           <?php foreach($map as $key => $val){?>
		           			<?php if($extra_tab_options['map_implementation'] === $val ){?>
		           				<option value="<?php echo $val;?>" selected="selected"><?php echo $val; ?></option>
		           			<?php }else{?>
		           				<option value="<?php echo $val;?>" ><?php echo $val; ?></option>
		           			<?php }?>
		           <?php }?>
		       </select>
		           
	        </p>
	       	
	       	<p class="form-field">
	           <label><?php _e('Product Tax Code:', 'woocommerce-jet-integration'); ?></label>
		           <?php $taxcode = array('select','Toilet Paper', 'Thermometers','Sweatbands','SPF Suncare Products',
		           		'Sparkling Water','Smoking Cessation','Shoe Insoles','Safety Clothing','Pet Foods','Paper Products',
		           'OTC Pet Meds','OTC Medication','Oral Care Products','Non-Motorized Boats','Non Taxable Product'
		           ,'Mobility Equipment','Medicated Personal Care Items','Infant Clothing','Helmets','Handkerchiefs'
		           ,'Generic Taxable Product','General Grocery Items','General Clothing','Fluoride Toothpaste','Feminine Hygiene Products'
		           ,'Durable Medical Equipment','Drinks under 50 Percent Juice','Disposable Wipes','Disposable Infant Diapers'
		           ,'Dietary Supplements','Diabetic Supplies','Costumes','Contraceptives','Contact Lens Solution',
		           'Carbonated Soft Drinks','Car Seats','Candy with Flour','Candy','Breast Pumps','Braces and Supports'
		           ,'Bottled Water Plain','Beverages with 51 to 99 Percent Juice','Bathing Suits','Bandages and First Aid Kits'
		           ,'Baby Supplies','Athletic Clothing','Adult Diapers');?>
		           <select name="product_tax_code" class="select sort variable_dropdown_size">
		           <?php foreach($taxcode as $key => $val){?>
		           			<?php if($extra_tab_options['product_tax_code'] === $val ){?>
		           				<option value="<?php echo $val;?>" selected="selected"><?php echo $val; ?></option>
		           			<?php }else{?>
		           				<option value="<?php echo $val;?>" ><?php echo $val; ?></option>
		           			<?php }?>
		           <?php }?>
		           
		       </select>
		           
	        </p>
	       	
	       	<p class="form-field">
	           <label><?php _e('Ships Alone:', 'woocommerce-jet-integration'); ?></label>
		           <?php $ship = array("false","true");?>
		           <select name="ships_alone" class="select sort">
		           <?php foreach($ship as $key => $val){?>
		           			<?php if($extra_tab_options['ships_alone'] === $val ){?>
		           				<option value="<?php echo $val;?>" selected="selected"><?php echo $val; ?></option>
		           			<?php }else{?>
		           				<option value="<?php echo $val;?>" ><?php echo $val; ?></option>
		           			<?php }?>
		           <?php }?>
		           
		       </select>
		           
	        </p>
	        
	        <p class="form-field">
	           <label><?php _e('Exclude From Fee Adjustment:', 'woocommerce-jet-integration'); ?></label>
		           <?php $eclude = array("false","true");?>
		           <select name="exclude_from_fee_adjustments" class="select sort">
		           <?php foreach($eclude as $key => $val){?>
		           			<?php if($extra_tab_options['exclude_from_fee_adjustments'] === $val ){?>
		           				<option value="<?php echo $val;?>" selected="selected"><?php echo $val; ?></option>
		           			<?php }else{?>
		           				<option value="<?php echo $val;?>" ><?php echo $val; ?></option>
		           			<?php }?>
		           <?php }?>
		           
		       </select>
		           
	        </p>
	       	
	       	<!-- field fourth -->
		    <p class="form-field">
	           <label><?php _e('Number Units For Price Per Units:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="number_units_for_price_per_unit" value="<?php echo @$extra_tab_options['number_units_for_price_per_unit']; ?>" placeholder="<?php _e('Value', 'woocommerce-jet-integration'); ?>" />
	        </p>
	       	<!-- Fourth Field End  -->
	       	
	       	<!-- field five -->
		    <p class="form-field">
	           <label><?php _e('Type Of Unit For Price Per Unit:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="type_of_unit_for_price_per_unit" value="<?php echo @$extra_tab_options['type_of_unit_for_price_per_unit']; ?>" placeholder="<?php _e('Value', 'woocommerce-jet-integration'); ?>" />
	        </p>
	       	<!-- Five Field End  -->
	       	
	       	<!-- field seven -->
		  <p class="form-field dimensions_field">
							<label for="product_length"><?php _e('Package (inches):', 'woocommerce-jet-integration'); ?></label>
							<span class="wrap">
								<input type="text" value="<?php echo @$extra_tab_options['package_length']; ?>" name="package_length" size="6" class="input-text wc_input_decimal" placeholder="Length" id="product_length">
								<input type="text" value="<?php echo @$extra_tab_options['package_width']; ?>" name="package_width" size="6" class="input-text wc_input_decimal" placeholder="Width">
								<input type="text" value="<?php echo @$extra_tab_options['package_height']; ?>" name="package_height" size="6" class="input-text wc_input_decimal last" placeholder="Height">
							</span>
						</p>
	       	<!-- seven Field End  -->
	       	
	       	<!-- field eight -->
		    <p class="form-field">
	           <label><?php _e('PROP 65:', 'woocommerce-jet-integration'); ?></label>
		           <?php $prop = array("false","true");?>
		           <select name="prop_65" class="select sort">
		           <?php foreach($prop as $key => $val){?>
		           			<?php if($extra_tab_options['prop_65'] === $val ){?>
		           				<option value="<?php echo $val;?>" selected="selected"><?php echo $val; ?></option>
		           			<?php }else{?>
		           				<option value="<?php echo $val;?>" ><?php echo $val; ?></option>
		           			<?php }?>
		           <?php }?>
		           
		       </select>
		           
	        </p>
	       	<!-- eight Field End  -->
	       	
	       	
	       	<!-- nine five -->
		    <p class="form-field">
	           <label><?php _e('Legal Disclaimer Description:', 'woocommerce-jet-integration'); ?></label>
		            <input type="text" size="5" name="legal_disclaimer_description" value="<?php echo @$extra_tab_options['legal_disclaimer_description']; ?>" placeholder="<?php _e('Value', 'woocommerce-jet-integration'); ?>" />
	        </p>
	       	<!-- nine Field End  -->
	       	
	       	
	       	<!-- field ten -->
		    <p class="form-field">
	           <label><?php _e('Bullets:', 'woocommerce-jet-integration'); ?></label>
	           		<input type="text" size="5" name="bullet_1" value="<?php echo @$bullets['0']; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	           		<input type="text" size="5" name="bullet_2" value="<?php echo @$bullets['1']; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	           		<input type="text" size="5" name="bullet_3" value="<?php echo @$bullets['2']; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	           		<input type="text" size="5" name="bullet_4" value="<?php echo @$bullets['3']; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	           		<input type="text" size="5" name="bullet_5" value="<?php echo @$bullets['4']; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	        </p>
	       	<!-- ten Field End  -->	
		    <?php $statement = array(	
		    					'no warning applicable' => 'no warning applicable',
		    					'choking hazard small parts' => 'choking hazard small parts',
		    					'choking hazard is a small ball' =>'choking hazard is a small ball',
		    					'choking hazard is a marble' =>'choking hazard is a marble',
		    					'choking hazard contains a small ball' => 'choking hazard contains a small ball',
		    					'choking hazard contains a marble' => 'choking hazard contains a marble',
		    					'choking hazard balloon' => 'choking hazard balloon');
		    
		  
	        $this->libraryAction->woocommerce_wp_select_multiple( array(
		        'id' => 'cpsia_cautionary_statements',
		        'name' => 'cpsia_cautionary_statements[]',
		        'label' => __('CPSIA causionary statements', 'woocommerce-jet-integration'),
		        'value'	=>  json_decode(get_post_meta($post->ID, 'cpsia_cautionary_statements', true)),
		        'options' => $statement
		        )
		        );
	        ?>
	    </div>
	 </div>
   <!-- extra attribute data tab end -->
   
   <!-- shipping exception tab start -->
   <div id="jet_shipping_settings" class="panel woocommerce_options_panel">
	<?php 
	// Select service level
	$jet_node_id = get_option('jet_node_id');
	$jet_node_id = json_decode($jet_node_id);
	//$shipping_exception = array();
	$ship_array = array();
	if(!empty($jet_node_id)){
	foreach($jet_node_id as $key => $value){
?> <div class="options_group"  style="margin: 0px 10px 0px 10px;">
		<p class="form-field">
			<h4><?php  _e('Shipping Exception for fullfillment : '.$value, 'woocommerce-jet-integration'); ?>
				<img class="expand-image" value="<?php echo $value; ?>" style="float: right;" src="<?php echo CEDJETINTEGRATION; ?>expand.png" height="16" width="16" />
			</h4>
	     </p>
	     </div>
	     <div class="options_group" id="<?php echo $value;?>" style="display: none;">
<?php 
// Checkbox
woocommerce_wp_checkbox(
array(
'id'            => 'sipping_exception_settings['.$value.']',
'label'         => __('<b>Enable Shipping Exception</b>', 'woocommerce' ),
'description'   => __( 'Check For activate shipping exception settings on this fullfillment', 'woocommerce' ),
'value'			=> get_post_meta( $post->ID, 'sipping_exception_settings_'.$value, true ),
)
);
	woocommerce_wp_select(
	array(
	'id'      => '_service_level['.$value.']',
	'label'   => __( 'Service Level', 'woocommerce-jet-integration' ),
	'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	'value'       => get_post_meta( $post->ID, 'jet_service_level_'.$value, true ),
	'options' => array(
	'choose'   => __( 'Choose any value', 'woocommerce-jet-integration' ),
	'SecondDay'   => __( 'SecondDay', 'woocommerce-jet-integration' ),
	'NextDay'   => __( 'NextDay', 'woocommerce-jet-integration' ),
	'Scheduled' => __( 'Scheduled', 'woocommerce-jet-integration' ),
	'Expedited' => __( 'Expedited', 'woocommerce-jet-integration' ),
	'Standard' => __( 'Standard', 'woocommerce-jet-integration' ),
	)
	)
	);
 
//shipping method
woocommerce_wp_select(
array(
'id'      => '_shipping_methods['.$value.']',
'label'   => __( 'Shipping Methods', 'woocommerce-jet-integration' ),
'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
'value'       => get_post_meta( $post->ID, 'jet_shipping_methods_'.$value, true ),
'options' => array(
'choose'   => __( 'Choose any value', 'woocommerce-jet-integration' ),
'UPS Ground'   => __( 'UPS Ground', 'woocommerce-jet-integration' ),
'UPS Next Day Air'   => __( 'UPS Next Day Air', 'woocommerce-jet-integration' ),
'FedEx Home' => __( 'FedEx Home', 'woocommerce-jet-integration' ),
'Freight' => __( 'Freight', 'woocommerce-jet-integration' ),
)
)
);


//override type
woocommerce_wp_select(
array(
'id'      => '_override_type['.$value.']',
'label'   => __( 'Override Type', 'woocommerce-jet-integration' ),
'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
'value'       => get_post_meta( $post->ID, 'jet_override_type_'.$value, true ),
'options' => array(
'choose'   => __( 'Choose any value', 'woocommerce-jet-integration' ),
'Override charge'   => __( 'Override charge', 'woocommerce-jet-integration' ),
'Additional charge'   => __( 'Additional charge', 'woocommerce-jet-integration' ),
)
)
);


// shipping_charge_amount
woocommerce_wp_text_input(
array(
'id'          => 'jet_shipping_charge_amount['.$value.']',
'label'       => __( 'Shipping Charge Amount', 'woocommerce-jet-integration' ),
'placeholder' => 'Amount',
'desc_tip'    => 'true',
'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
'value'       => get_post_meta($post->ID, 'jet_shipping_charge_amount_'.$value, true ),
)
);
	
//shipping method
woocommerce_wp_select(
array(
'id'      => 'shipping_exception_type_id['.$value.']',
'label'   => __( 'Shipping exception type', 'woocommerce-jet-integration' ),
'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
'value'       => get_post_meta($post->ID,'jet_shipping_exception_type_'.$value, true ),
'options' => array(
'choose'   => __( 'Choose any value', 'woocommerce-jet-integration' ),
'exclusive'   => __( 'exclusive', 'woocommerce-jet-integration' ),
'restricted'   => __( 'restricted', 'woocommerce-jet-integration' ),
)
)
);
	?>
	</div> 
	<?php 
	}
	}else{ ?>
 <h3 style="text-align:center;"><?php  _e('Please Set atleast one fullfillment,otherwise you can\'t set shipping exception ', 'woocommerce-jet-integration'); ?></h3>
	<?php 
		
	}?>
	</div> 
   <!-- shipping exception tab end -->
   
   <!-- return exception tab start -->
	
	<div id="jet_return_settings" class="panel woocommerce_options_panel">
		<div class="options_group">
	<?php 
	// Select return id
	$jet_return_id = get_option('jet_return_id');
	$jet_return_id = json_decode($jet_return_id);

// Checkbox
if(!empty($jet_return_id)){
woocommerce_wp_checkbox(
array(
'id'            => 'return_exception_setting',
'label'         => __('<b>Enable Return Exception</b>', 'woocommerce-jet-integration' ),
'description'   => __( 'Check For activate return exception setting for this product', 'woocommerce-jet-integration' ),
'value'			=> get_post_meta( $post->ID, 'return_exception_setting', true ),
)
);
// shipping_charge_amount
woocommerce_wp_text_input(
array(
'id'          => 'jet_time_to_return',
'label'       => __( 'Time To Return', 'woocommerce-jet-integration' ),
'placeholder' => 'Days',
'desc_tip'    => 'true',
'description' => __( 'Enter the number of days after purchase a customer can return the item.', 'woocommerce-jet-integration' ),
'value'       => get_post_meta($post->ID, 'jet_time_to_return', true ),
)
);

$jet_id_assoc_array	=	array();
foreach ($jet_return_id as $id_index	=>	$id_data){
	$jet_id_assoc_array[$id_data]	=	$id_data;	
	
}
$this->libraryAction->woocommerce_wp_select_multiple(array(
		'id'            => 'return_id',
		'name'			=>	'return_id[]',
		'label'         => __('<b>Return ids', 'woocommerce-jet-integration' ),
		'description'   => __( 'Select return ids for sending Return Exception to this Return id', 'woocommerce-jet-integration' ),
		'value'			=> json_decode(get_post_meta( $post->ID, 'return_id', true )),
		'options'		=>	$jet_id_assoc_array,
));

$this->libraryAction->woocommerce_wp_select_multiple( array(
    'id' => '_return_shipping_methods',
    'name' => '_return_shipping_methods[]',
    'label' => __('Return Shipping Method', 'woocommerce-jet-integration'),
   	'value'	=>  json_decode(get_post_meta( $post->ID, '_return_shipping_methods' ,true)),
    'options' => array(
	'UPS 3 Day Select'   => __( 'UPS 3 Day Select', 'woocommerce-jet-integration' ),
	'UPS Ground' => __( 'UPS Ground', 'woocommerce-jet-integration' ),
	'UPS 2nd Day Air'   => __( 'UPS 2nd Day Air', 'woocommerce-jet-integration' ),
	'UPS Next Day Air'   => __( 'UPS Next Day Air', 'woocommerce-jet-integration' ),
	'UPS 2nd Day Air AM' => __( 'UPS 2nd Day Air AM', 'woocommerce-jet-integration' ),
	'UPS Next Day Air Saver'   => __( 'UPS Next Day Air Saver', 'woocommerce-jet-integration' ),
	'UPS SurePost'   => __( 'UPS SurePost', 'woocommerce-jet-integration' ),
	'UPS Mail Innovations' => __( 'UPS Mail Innovations', 'woocommerce-jet-integration' ),
	'FedEx Ground' => __( 'FedEx Ground', 'woocommerce-jet-integration' ),
	'FedEx Home Delivery'   => __( 'FedEx Home Delivery', 'woocommerce-jet-integration' ),
	'FedEx Express Saver'   => __( 'FedEx Express Saver', 'woocommerce-jet-integration' ),
	'FedEx 2 Day' => __( 'FedEx 2 Day', 'woocommerce-jet-integration' ),
	'FedEx Standard Overnight'   => __( 'FedEx Standard Overnight', 'woocommerce-jet-integration' ),
	'FedEx Priority Overnight'   => __( 'FedEx Priority Overnight', 'woocommerce-jet-integration' ),
	'FedEx First Overnight' => __( 'FedEx First Overnight', 'woocommerce-jet-integration' ),
	'FedEx Smart Post'   => __( 'FedEx Smart Post', 'woocommerce-jet-integration' ),
    ))
);
}else{?>
	<h3 style="text-align:center;"><?php  _e('Please Set atleast one return node id', 'woocommerce-jet-integration'); ?></h3>
	<?php 
}?>
	</div> 
	</div>
   <!-- return exception tab end -->
<?php }

	public function jetProcessProductMeta($post_id){
		// standard code data processing start..
		update_post_meta($post_id, 	'jet_asin', 		$_POST['jet_asin']);
		update_post_meta($post_id, 	'jetSelectedCode', 	$_POST['jetSelectedCode']);
		update_post_meta($post_id,  'standardCode', 	$_POST['standardCodeValue']);
		update_post_meta($post_id,  'jetBrand', 		$_POST['jetBrand']);
		update_post_meta($post_id,  'jetPriceSelect',   $_POST['jetPriceSelect']);
		update_post_meta($post_id,  'jetPrice',   		$_POST['jetPrice']);
		update_post_meta($post_id,  'jetStockSelect',   $_POST['jetStockSelect']);
		update_post_meta($post_id,  'jetStock',   		$_POST['jetStock']);
		update_post_meta($post_id,   'jet_selected_category', $_POST['selectedCategory']);
		
		$jet_node_id = get_option('jet_node_id');
		$jet_node_id = json_decode($jet_node_id);
		if(!empty($jet_node_id))
		{
			foreach($jet_node_id as $key => $value)
			{
				update_post_meta( $post_id, 'p_'.$value,$_POST['p_'.$value]);
				update_post_meta( $post_id, 's_'.$value, $_POST['s_'.$value]);
			}
		}
		
		$productCats 			= 	get_the_terms($post_id, "product_cat");
		$mappedIDs				=	get_option('cedWooJetMapping',true);
		 
		if(is_array($mappedIDs)){
			 
			$catArray	=	array();
			foreach($mappedIDs as $woocatid => $jetcatId){
		
				$catArray[]	=	$woocatid;
			}
			 
			if(!empty($catArray) && is_array($catArray)){
				 
				$$jetSelectedNodeID = array();
				foreach ($productCats as $index	=>	$catObject){
						
					if(in_array($catObject->term_id, $catArray)){
						 
						$jetSelectedNodeID[$catObject->term_id]	=	$mappedIDs[$catObject->term_id];
					}
				}
			}
		}
		 
		$jetAttrInfo	=	array();
		foreach($jetSelectedNodeID as $wooNodeID => $jetNodeID){
		
			$mappedAttributes		=	get_option($jetNodeID.'_linkedAttributes',false);
				
			if($mappedAttributes){
		
				$mappedAttributes	=	json_decode($mappedAttributes);
				if(is_array($mappedAttributes)){
		
					$jetAttrInfo[$jetNodeID]	=	$this->modelAction->fetchAttrDetails($mappedAttributes);
				}
			}
		}
		foreach($jetAttrInfo as $jetNode => $mappedCAT):
			foreach($mappedCAT as $attrARRAY):
				$attrObject = $attrARRAY[0];
				$tempName	=	$jetNode."_".$attrObject->jet_attr_id;
				if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit)) ){
					update_post_meta($post_id, 	$tempName, 	$_POST[$tempName]);
					update_post_meta($post_id, 	$tempName.'_unit', 	$_POST[$tempName.'_unit']);
				}
				elseif($attrObject->freetext == 1){
					
					update_post_meta($post_id, 	$tempName, $_POST[$tempName]);
				}else{
					update_post_meta($post_id, 	$tempName, $_POST[$tempName]);
				}
			endforeach;
		endforeach;
		
		update_post_meta($post_id,'selectedCatAttr',$_POST['selectedCatAttr']);
		
		//standard code data processing end.
		
		//extra attribute tab data processing start.
		$jet_country						= 	$_POST['jet_country'];
		$product_manufacturer 				= 	$_POST['product_manufacturer'];
		$jet_mfr_part_number 				= 	$_POST['jet_mfr_part_number'];
		$number_units_for_price_per_unit	= 	$_POST['number_units_for_price_per_unit'];
		$type_of_unit_for_price_per_unit	= 	$_POST['type_of_unit_for_price_per_unit'];
		$shipping_weight_pounds 			= 	$_POST['shipping_weight_pounds'];
		$package_length 					= 	$_POST['package_length'];
		$package_width						= 	$_POST['package_width'];
		$package_height 					=	$_POST['package_height'];
		$safety_warning						=	$_POST['safety_warning'];
		$fulfillment_time					=	$_POST['fulfillment_time'];
		$msrp								=	$_POST['msrp'];
		$map_price							=	$_POST['map_price'];
		$map_implementation					=	$_POST['map_implementation'];
		$prop_65 							= 	$_POST['prop_65'];
		$legal_disclaimer_description 		= 	$_POST['legal_disclaimer_description'];
		$product_tax_code  					= 	$_POST['product_tax_code'];
		$exclude_from_fee_adjustments 		= 	$_POST['exclude_from_fee_adjustments'];
		$ships_alone   						= 	$_POST['ships_alone'];
		//$jetbackorders						=	$_POST['jetbackorder'];
		$amazon_item_keyword                =   $_POST['amazon_item_type_keyword'];
		$cpsia_cautionary_statement 		= 	$_POST['cpsia_cautionary_statements'];
		$cpsia_cautionary_statements 		=	json_encode($cpsia_cautionary_statement);
		
		for ($i=1;$i<6;$i++)
		{
			if(isset($_POST['bullet_'.$i]) && !empty($_POST['bullet_'.$i]))
			{
				$bullets[] = $_POST['bullet_'.$i];
			}
		}
		$bullets							= json_encode($bullets);
		//update_post_meta($post_id, 'jetbackorders', $jetbackorders);
		update_post_meta($post_id, 'amazon_item_type_keyword', $amazon_item_keyword);
		update_post_meta( $post_id, 'jet_country',$jet_country);
		update_post_meta( $post_id, 'product_manufacturer', $product_manufacturer);
		update_post_meta( $post_id, 'jet_mfr_part_number', $jet_mfr_part_number);
		update_post_meta( $post_id, 'number_units_for_price_per_unit', $number_units_for_price_per_unit);
		update_post_meta( $post_id, 'type_of_unit_for_price_per_unit', $type_of_unit_for_price_per_unit);
		update_post_meta( $post_id, 'shipping_weight_pounds', $shipping_weight_pounds);
		update_post_meta( $post_id, 'package_length', $package_length);
		update_post_meta( $post_id, 'package_width', $package_width);
		update_post_meta( $post_id, 'package_height', $package_height);
		update_post_meta( $post_id, 'prop_65', $prop_65);
		update_post_meta( $post_id, 'legal_disclaimer_description', $legal_disclaimer_description);
		update_post_meta( $post_id, 'cpsia_cautionary_statements', $cpsia_cautionary_statements);
		update_post_meta( $post_id, 'safety_warning', $safety_warning);
		update_post_meta( $post_id, 'fulfillment_time', $fulfillment_time);
		update_post_meta( $post_id, 'msrp', $msrp);
		update_post_meta( $post_id, 'map_price', $map_price);
		update_post_meta( $post_id, 'map_implementation', $map_implementation);
		update_post_meta( $post_id, 'bullets', $bullets);
		update_post_meta( $post_id, 'product_tax_code', $product_tax_code);
		update_post_meta( $post_id, 'ships_alone', $ships_alone);
		update_post_meta( $post_id, 'exclude_from_fee_adjustments', $exclude_from_fee_adjustments);
		//extra attribute tab data processing end.
		
		//shipping exception tab data processing start.
		$jet_node_id = get_option('jet_node_id');
		$jet_node_id = json_decode($jet_node_id);
		
		$ship_array = array();
		foreach($jet_node_id as $key => $value){
		
			//shipping exception enable settings
			// Checkbox
			$enable_exception = isset( $_POST['sipping_exception_settings'][$value] ) ? 'yes' : 'no';
			update_post_meta( $post_id, 'sipping_exception_settings_'.$value, $enable_exception );
		
			//service level
			$_service_level = $_POST['_service_level'][$value];
			if( !empty( $_service_level ) )
				update_post_meta( $post_id, 'jet_service_level_'.$value, esc_attr( $_service_level ) );
		
			// Select
			$_shipping_methods = $_POST['_shipping_methods'][$value];
			if( !empty( $_shipping_methods )  )
				update_post_meta( $post_id, 'jet_shipping_methods_'.$value, esc_attr( $_shipping_methods ) );
		
			// Select
			$_override_type = $_POST['_override_type'][$value];
			if( !empty( $_override_type ) )
				update_post_meta( $post_id, 'jet_override_type_'.$value, esc_attr( $_override_type ) );
		
			// Text Field
			$shipping_charge_amount = $_POST['jet_shipping_charge_amount'][$value];
			if( !empty( $shipping_charge_amount ) )
				update_post_meta( $post_id, 'jet_shipping_charge_amount_'.$value, esc_attr( $shipping_charge_amount ) );
		
		
			// Select
			$shipping_exception_type = $_POST['shipping_exception_type_id'][$value];
			if( !empty( $shipping_exception_type ) ){ //echo '<pre>';print_r($_POST);die;
				update_post_meta( $post_id,'jet_shipping_exception_type_'.$value, esc_attr( $shipping_exception_type ) );
			}
		}
		//shipping exception tab data processing end.
		
		//return exception tab data processing start

		$jet_return_id = get_option('jet_return_id');
		$jet_return_id = json_decode($jet_return_id);
		// Checkbox
		$return_exception_setting = isset( $_POST['return_exception_setting'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, 'return_exception_setting', $return_exception_setting );
		
		// Text Field
		$jet_time_to_return = $_POST['jet_time_to_return'];
		if( !empty( $jet_time_to_return ) )
			update_post_meta( $post_id, 'jet_time_to_return', esc_attr( $jet_time_to_return ) );
		
		$_return_ids = $_POST['return_id'];
		if( !empty( $_return_ids )  )
			update_post_meta( $post_id, 'return_id', json_encode($_return_ids));
		
		// Select
		$_return_shipping_methods = $_POST['_return_shipping_methods'];
		
		if( !empty( $_return_shipping_methods )  )
			update_post_meta( $post_id, '_return_shipping_methods', json_encode($_return_shipping_methods));
		
		//return exception tab data processing end.
		
		$sync_auto_update = get_option('sync_product_update');
		
		if($sync_auto_update == 'yes'){

			$product = get_product($post_id);
		
			if($product->is_type('variable')){
		
				$var_id 		= 	$variations[0]['variation_id'];
				$result 		= 	$this->fileUploadHelper->CGetRequest('/merchant-skus/'.$var_id);
				$response 		= 	json_decode($result);
		
			}else{
				$result 		= 	$this->fileUploadHelper->CGetRequest('/merchant-skus/'.$post_id);
				$response 		= 	json_decode($result);
			}
			
			if(isset($response) && $response->status != 'Archive'){
				
				$productIDChunk			=	array();
				$productIDChunk[]		=	array($post_id);	
				$this->productManager->uploadAction($productIDChunk);
			}
		}//End Auto sync check
	}


	
	public function variation_settings_fields( $loop, $variation_data, $variation ) {
		?>
		<div class="variation_extra_data">
		 <div class="options_group">
		        <p class="form-field">
	               <h3 style="color:red; text-align:center;"><?php  _e('Fill All compulsory Attributes Value which required During jet Store Integration', 'woocommerce-jet-integration'); ?></h3>
	            </p>
	        </div>
	        <h4><b><?php _e('JET Category Settings','wocommerce-jet-integration');?></b></h4>
	         <div id="jet_attribute_settings">
	        <?php 
	        global $post;
	        
	        $productCats 			= 	get_the_terms($post->ID, "product_cat");
	        $mappedIDs				=	get_option('cedWooJetMapping',true);
	         
	        if(is_array($mappedIDs) && !empty($productCats)){
	        	 
	        	$catArray	=	array();
	        	foreach($mappedIDs as $woocatid => $jetcatId){
	        
	        		$catArray[]	=	$woocatid;
	        	}
	        	 
	        	if(!empty($catArray) && is_array($catArray)){
	        		 
	        		$jetSelectedNodeID = array();
	        		foreach ($productCats as $index	=>	$catObject){
	        			if(in_array($catObject->term_id, $catArray)){
	        					
	        				$jetSelectedNodeID[$catObject->term_id]	=	$mappedIDs[$catObject->term_id];
	        			}
	        		}
	        	}
	        }
	        $jetAttrInfo	=	array();
	        if(!empty($jetSelectedNodeID)):
	        foreach($jetSelectedNodeID as $wooNodeID => $jetNodeID){
	        
	        	$mappedAttributes		=	get_option($jetNodeID.'_linkedAttributes',false);
	        		
	        	if($mappedAttributes){
	        
	        		$mappedAttributes	=	json_decode($mappedAttributes);
	        		if(is_array($mappedAttributes)){
	        
	        			$jetAttrInfo[$jetNodeID]	=	$this->modelAction->fetchAttrDetails($mappedAttributes);
	        		}
	        	}
	        }
	        // print_R($jetAttrInfo);
	        
	        $enable 				= 	get_post_meta($variation->ID, $variation->ID.'_selectedCatAttr', true);
	         $i	=	1;
 	        // print_r($jetAttrInfo);die('vp');
	        foreach($jetAttrInfo as $jetNode => $mappedCAT):
	        $wooCatID 	=	array_search($jetNode, $jetSelectedNodeID);
	        $term	=	get_term_by('id',$wooCatID,'product_cat');
	        
	        $mappedWooCatName	=	'';
	        
	        if(isset($term)){
	        
	        	$mappedWooCatName	=	$term->name;
	        }
	        if($enable == $jetNode){
	        	$check = "checked='checked'";
	        }else{
	        	$check	=	'';
	            		}?>
	           <hr>
	          
	            	<div class="options_group" id="attr_<?php echo $jetNode;?>" data-wid="<?php echo $wooCatID;?>" >
	            		<h4 class="variable_cat_heading"><span><?php echo $i;$i++; ?>) </span><?php _e($mappedWooCatName." JET Attributes",'wocommerce-jet-integration');?></h4>
	            		<div class="variable_cat_body" data-wid="<?php echo $wooCatID;?>">
	            		<input type="radio" name="<?php echo $variation->ID;?>_selectedCatAttr" value="<?php echo $jetNode;?>" <?php echo $check;?>>
	              <?php foreach($mappedCAT as $attrARRAY):
	              
	            		$attrObject = $attrARRAY[0];
	              		if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit))):
	              		
	              		$values	=	json_decode($attrObject->unit);
	              		
	              		$assocValues			=	array();
	              		$assocValues['none']	=	'Select A Value';
	              		//echo get_post_meta($variation->ID, $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id."_unit",true);
	              		//echo get_post_meta($variation->ID, $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id, true);
	              		//print_r($assocValues);die;
	              		if(!empty($values)){
	              		foreach($values as $VALUE):
	              		$assocValues[$VALUE]	=	$VALUE;
	              		endforeach;
	              		}
	              		//print_r($assocValues);die;
	              		$this->libraryAction->cedcommerce_text_with_unit_select(
	              				array(
	              						'id' => $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id,
	              						'name' => $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id,
	              						'label' => __($attrObject->name , 'woocommerce-jet-integration'),
	              						'value1'=> get_post_meta($variation->ID, $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id, true),
	              						'value2'=>	get_post_meta($variation->ID, $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id."_unit",true),
	              						'options' => $assocValues,
	              						'description' => 'please provide value and select unit',
	              				)
	              		);
	              		endif;
	              			            		if($attrObject->freetext == 1):?>
	              			        	    		<p class="form-field dimensions_field">
	              			        					<label for="jetAttributes"><?php echo $attrObject->name; if($attrObject->variant==1){echo ' (variant)';}?></label>
	              			        					<?php $tempName	=	$variation->ID."_".$jetNode."_".$attrObject->jet_attr_id;?>
	              			        					<?php $tempValue	=	get_post_meta($variation->ID , $tempName , true);?>
	              			        					<input type="text" value="<?php echo $tempValue;?>" name="<?php echo $tempName;?>" size="5" >
	              			        					<?php if($attrObject->variant==1){  ?><span><img class="help_tip" data-tip="<?php _e("Used as Variant ", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></span> <?php    } ?>
	              			        				</p>
	              			            	<?php endif;
	              			            		if($attrObject->freetext == 0 && !empty($attrObject->values) && empty($attrObject->unit)):
	              			            		
	              			            			$values	=	json_decode($attrObject->values);
	              			            		
	              			            			$assocValues	=	array();
	              			            			$assocValues['none']	=	'Select A Value';
	              			            			
	              			            			if(!empty($values)){
	              			            			foreach($values as $VALUE):
	              			            				$assocValues[$VALUE]	=	$VALUE;
	              			            			endforeach;
	              			            			}
	              			            			if($attrObject->variant==0){
		              			            			woocommerce_wp_select(
		              			        						array(
		              			        						'id'      => $variation->ID."_".$jetNode.'_'.$attrObject->jet_attr_id,
		              			        						'label'   => __( $attrObject->name, 'woocommerce-jet-integration' ),
		              			        						'description' => __( 'Select a value.', 'woocommerce-jet-integration' ),
		              			        						'value'       => get_post_meta( $variation->ID, $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id , true ),
		              			        						'options' => $assocValues,
		              			        						)
		              			        					);
	              			            			}
	              			            			if($attrObject->variant==1){
	              			            				woocommerce_wp_select(
														array(
														'id'      => $variation->ID."_".$jetNode.'_'.$attrObject->jet_attr_id,
														'label'   => __( $attrObject->name, 'woocommerce-jet-integration' ),
														'description' => __( 'Used as  Variant', 'woocommerce-jet-integration' ),
														'value'       => get_post_meta( $variation->ID, $variation->ID."_".$jetNode."_".$attrObject->jet_attr_id , true ),
														'options' => $assocValues,
														)
														);
	              			            			}
	              			            		endif;?>
	              			            	<?php endforeach;?>
	              			            	</div>
	              			            	
	              			            	<?php endforeach;?>
	              			            </div>
	              			            </div>
	              			            <?php endif;?>
	              			    </div>        
	              			  <!-- End cat settings -->          
	              			        <br>        <hr>
	              			        <h4><u><b><?php _e('Basic Settings','wocommerce-jet-integration');?></b></u></h4>
	              			        <?php 
	              			        woocommerce_wp_text_input(
	              			        array(
	              			        'id'          => '_jet_title[' . $variation->ID . ']',
	              			        'label'       => __('Enter Product Title', 'woocommerce-jet-integration' ),
	              			        'placeholder' => 'Value',
	              			        'desc_tip'    => 'true',
	              			        'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	              			        'value'       => get_post_meta( $variation->ID, '_jet_title', true ),
	              			        )
	              			        );
	              			        woocommerce_wp_text_input(
	              			        array(
	              			        'id'          => '_jet_country_manufacture[' . $variation->ID . ']',
	              			        'label'       => __('Country Manufacturer', 'woocommerce-jet-integration' ),
	              			        'placeholder' => 'Value',
	              			        'desc_tip'    => 'true',
	              			        'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	              			        'value'       => get_post_meta( $variation->ID, 'jet_country', true ),
	              			        )
	              			        );
	              			        
	              			        woocommerce_wp_text_input(
	              			        array(
	              			        'id'          => '_brand[' . $variation->ID . ']',
	              			        'label'       => __('Enter Brand', 'woocommerce-jet-integration' ),
	              			        'placeholder' => 'Value',
	              			        'desc_tip'    => 'true',
	              			        'description' => __( 'All Variation Brand should be same.', 'woocommerce-jet-integration' ),
	              			        'value'       => get_post_meta( $variation->ID, 'jetBrand', true ),
	              			        )
	              			        );
	              			        
	              			        
	              			        woocommerce_wp_text_input(
	              			        array(
	              			        'id'          => '_jet_asin[' . $variation->ID . ']',
	              			        'label'       => __('ASIN Value', 'woocommerce-jet-integration' ),
	              			        'placeholder' => 'Value',
	              			        'desc_tip'    => 'true',
	              			        'description' => __( 'ASIN Value should be 10 character ', 'woocommerce-jet-integration' ),
	              			        'value'       => get_post_meta( $variation->ID, '_jet_asin', true ),
	              			        )
	              			        );
	              			        //upc value
	              			        woocommerce_wp_select(
	              			        array(
	              			        'id'          => 'standardCodetype[' . $variation->ID . ']',
	              			        'label'       => __( 'Standard Code :', 'woocommerce-jet-integration' ),
	              			        'value'       => get_post_meta( $variation->ID, 'standardCodetype', true ),
	              			        'options' => array(
	              			        'select' => __( 'select', 'woocommerce-jet-integration' ),
	              			        'upc'   => __( 'UPC', 'woocommerce-jet-integration' ),
	              			        'upce' => __( 'UPC-E', 'woocommerce-jet-integration' ),
	              			        'gtin14' => __( 'GTIN-14', 'woocommerce-jet-integration' ),
	              			        'isbn13' => __( 'ISBN-13', 'woocommerce-jet-integration' ),
	              			        'isbn10' => __( 'ISBN-10', 'woocommerce-jet-integration' ),
	              			        'ean' => __( 'EAN', 'woocommerce-jet-integration' ),
	              			        )
	              			        )
	              			        );
	              			         
	              			        woocommerce_wp_text_input(
	              			        array(
	              			        'id'          => 'standardCodeVal[' . $variation->ID . ']',
	              			        'label'       => __('Standard Code Value', 'woocommerce-jet-integration' ),
	              			        'placeholder' => 'Value',
	              			        'desc_tip'    => 'true',
	              			        'description' => __( 'Standard Code Should be unique', 'woocommerce-jet-integration' ),
	              			        'value'       => get_post_meta( $variation->ID, 'standardCodeVal', true ),
	              			        )
	              			        );
	              			         
	              			        
	              			        ?>
	              			        
	              			      <!-- star -->
					<hr>
	              	 	<h4>
	              	 		<u><b><?php _e('Inventory And Stock settings','wocommerce-jet-integration');?></b></u>
	              	 	</h4>
	              		<div class="variation_price_and_stock">
	              		   <div class="price_dropdown">
              			       <?php 
              			        	woocommerce_wp_select(
              			        	 array(
              			        	 	'id'          => 'pricetype[' . $variation->ID . ']',
              			        	 	'class' 	  => 'variation_price',
              			        	 	'label'       => __( 'Price Type :', 'woocommerce-jet-integration' ),
              			        	 	'value'       => get_post_meta( $variation->ID, 'jetPriceSelect', true ),
              			        	 	'options' => array(
              			        	 	'main_price' => __( 'MAIN PRICE', 'woocommerce-jet-integration' ),
              			        	 	'sale_price'   => __( 'SALE PRICE', 'woocommerce-jet-integration' ),
              			        	 	'otherPrice' => __( 'OTHERS', 'woocommerce-jet-integration' ),
              			        	 	'fullfillment_wise' => __( 'FULLFILLMENT-WISE', 'woocommerce-jet-integration' ),
              			        	 		)
              			        	 	)
              			        	 );
              			        	?>
              			        	<?php $pricetype =  get_post_meta( $variation->ID, 'jetPriceSelect', true );
										  if($pricetype == 'otherPrice'){
										  	$otherprice = 'display:block';
										  }else{
										  	$otherprice = 'display:none';
										  }
										  //fullfillment			
										  if($pricetype == 'fullfillment_wise'){
										  	$fullfillment_price = 'display:block';
										  }else{
										  	$fullfillment_price = 'display:none';
										  }
              			        	?>
	              			        	<div class ="other_variation_price" style="<?php echo $otherprice;?>">
	              			        	<?php 
	              			        	woocommerce_wp_text_input(
	              			        	array(
	              			        	 'id'          => 'variationotherprice[' . $variation->ID . ']',
	              			        	 'label'       => __('Other Price', 'woocommerce-jet-integration' ),
	              			        	 'placeholder' => 'Value',
	              			        	 'desc_tip'    => 'true',
	              			        	 'description' => __( 'Other Price ', 'woocommerce-jet-integration' ),
	              			        	 'value'       => get_post_meta( $variation->ID, 'jetPrice', true ),
	              			        	 )
	              			        	 );
	              			        	?>
	              			        	</div>
	              			        	<div class="variation_fullfillment_price" style = "<?php echo $fullfillment_price;?>";>
	              			        	<?php 
	              			        	        $jet_node_id = get_option('jet_node_id');
	              			        	        $jet_node_id = json_decode($jet_node_id);
	              			        	       // global $post;
	              			        	       // echo '<pre>';print_r($post);die;
	              			        	        ?>
	              			        	        <?php if(!empty($jet_node_id)){ //echo '<pre>';print_r($jet_node_id); ?>
	              			        	        	<?php foreach($jet_node_id as $key => $value){ 
	              			        	        			$price 	  = get_post_meta($variation->ID, 'p_'.$value, true);
	              			        	        			?>
	              			        	        		 <p class="form-field dimensions_field">
	              			        						<label for="product_length"><?php _e('price For Fullfillment '.$value,'woocommerce-jet-integration')?></label>
	              			        					<span class="wrap">
	              			        	        			
	              			        	        			<?php 
	              			        	        			woocommerce_wp_text_input(
	              			        	        			array(
	              			        	        			'id'          => 'p_'.$value.'[' . $variation->ID . ']',
	              			        	        			'placeholder' => 'Price',
	              			        	        			'desc_tip'    => 'true',
	              			        	        			'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	              			        	        			'value'       => get_post_meta( $variation->ID,'p_'.$value, true ),
	              			        	        			)
	              			        	        			);
	              			        	        		?></span>
	              			        	        		</p>
	              			        					<?php }?>
	              			        					<?php }
	              			        					else{?>
	              			        						 <h3 style="text-align:center;"><?php  _e('Please Set atleast one fullfillment,otherwise you product will be not uploaded on jet ', 'woocommerce-jet-integration'); ?></h3>
	              			        					<?php }?>
	              			        	       		
	              			        	</div>
	              			        	</div><!-- Stock dropdown -->
	       							<div class="stock_dropdown">
	       						<?php 
							       		woocommerce_wp_select(
							       		array(
							       		'id'          => 'stocktype[' . $variation->ID . ']',
							       		'class' 	  => 'variation_stock',
							       		'label'       => __( 'Stock Type :', 'woocommerce-jet-integration' ),
							       		'value'       => get_post_meta( $variation->ID, 'jetStockSelect', true ),
							       		'options' => array(
							       		'central'   => __( 'CENTRAL STOCK', 'woocommerce-jet-integration' ),
							       		'default' => __( 'DEFAULT 99', 'woocommerce-jet-integration' ),
							       		'other' => __( 'OTHERS', 'woocommerce-jet-integration' ),
							       		'fullfillment_wise' => __( 'FULLFILLMENT-WISE', 'woocommerce-jet-integration' ),
							       		)
							       		)
							       		);
	       							?>
	       							<?php $stocktype =  get_post_meta( $variation->ID,'jetStockSelect', true );
										  if($stocktype == 'other'){
										  	$otherstock = 'display:block';
										  }else{
										  	$otherstock = 'display:none';
										  }
										  //fullfillment			
										  if($stocktype == 'fullfillment_wise'){
										  	$fullfillment_stock = 'display:block';
										  }else{
										  	$fullfillment_stock = 'display:none';
										  }
              			        	?>
	       								<div class ="other_variation_stock" style="<?php echo $otherstock;?>">
	       							<?php 
								       		woocommerce_wp_text_input(
								       		array(
								       		'id'          => 'variationotherstock[' . $variation->ID . ']',
								       		'label'       => __('Other Stock', 'woocommerce-jet-integration' ),
								       		'placeholder' => 'Value',
								       		'desc_tip'    => 'true',
								       		'description' => __( 'Other Stock ','woocommerce-jet-integration' ),
								       		'value'       => get_post_meta( $variation->ID, 'jetStock', true ),
								       		)
								       		);
	       							?>
	       								</div>
	       							<div class="variation_fullfillment_stock" style="<?php echo $fullfillment_stock;?>">
	       							
	       		
			<?php 
	        $jet_node_id = get_option('jet_node_id');
	        $jet_node_id = json_decode($jet_node_id);
	       // global $post;
	       // echo '<pre>';print_r($post);die;
	        ?>
	        <?php if(!empty($jet_node_id)){ //echo '<pre>';print_r($jet_node_id); ?>
	        	<?php foreach($jet_node_id as $key => $value){ 
	        			
	        			$stock    = get_post_meta($variation->ID, 's_'.$value, true);
	        			?>
	        		 <p class="form-field dimensions_field">
						<label for="product_length"><?php _e('Stock For Fullfillment '.$value,'woocommerce-jet-integration')?></label>
					<span class="wrap">
	        			
	        			<?php 
	        			woocommerce_wp_text_input(
	        			array(
	        			'id'          => 's_'.$value.'[' . $variation->ID . ']',
	        			'placeholder' => 'Stock',
	        			'desc_tip'    => 'true',
	        			'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	        			'value'       => get_post_meta( $variation->ID,'s_'.$value, true ),
	        			)
	        			);
	        		?></span>
	        		</p>
					<?php }?>
					<?php }else{?>
		 <h3 style="text-align:center;"><?php  _e('Please Set atleast one fullfillment,otherwise you product will be not uploaded on jet ', 'woocommerce-jet-integration'); ?></h3>
		<?php }?>
	       	
	       	</div>
	       	</div>
	      </div> 	
	    <!-- stop -->
	       	
	       <hr>
	   <div class="options_group">
				<p class="form-field">
					<h3 style="text-align:center;"><?php  _e('Fill All the Extra Attributes Value which required During jet Store Integration', 'woocommerce-jet-integration'); ?></h3>
		         </p>
	    </div>
	    <?php 
	    woocommerce_wp_text_input(
	    array(
	    'id'          => '_manufacturer[' . $variation->ID . ']',
	    'label'       => __('Enter Product Manufacturer', 'woocommerce-jet-integration' ),
	    'placeholder' => 'Value',
	    'desc_tip'    => 'true',
	    'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'product_manufacturer', true ),
	     )
	    );
	    
	    woocommerce_wp_text_input(
	    array(
	    'id'          => '_safety_warning[' . $variation->ID . ']',
	    'label'       => __('Safety Warning', 'woocommerce-jet-integration' ),
	    'placeholder' => 'Value',
	    'desc_tip'    => 'true',
	    'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'safety_warning', true ),
	    )
	    );
	    
	    
	    woocommerce_wp_text_input(
	    array(
	    'id'          => '_fullfillment_time[' . $variation->ID . ']',
	    'label'       => __('Fullfillment Time', 'woocommerce-jet-integration' ),
	    'placeholder' => 'Value',
	    'desc_tip'    => 'true',
	    'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'fulfillment_time', true ),
	    )
	    );
	    
	    woocommerce_wp_text_input(
	    array(
	    'id'          => 'amazon_item_type_keyword[' . $variation->ID . ']',
	    'label'       => __('Amazon Item Type Keyword', 'woocommerce-jet-integration' ),
	    'placeholder' => 'Value',
	    'desc_tip'    => 'true',
	    'description' => __( 'amazon_item_type_keyword.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'amazon_item_type_keyword', true ),
	    )
	    );
	   
	    woocommerce_wp_text_input(
	    array(
	    'id'          => '_retail_price[' . $variation->ID . ']',
	    'label'       => __('Manufacturer suggested retail price', 'woocommerce-jet-integration' ),
	    'placeholder' => 'Value',
	    'desc_tip'    => 'true',
	    'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'msrp', true ),
	    )
	    );
	    
	    woocommerce_wp_text_input(
	    array(
	    'id'          => '_map_price[' . $variation->ID . ']',
	    'label'       => __('Map Price', 'woocommerce-jet-integration' ),
	    'placeholder' => 'Value',
	    'desc_tip'    => 'true',
	    'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'map_price', true ),
	    )
	    );
	    
	    woocommerce_wp_select(
	    array(
	    'id'          => '_map_implementation[' . $variation->ID . ']',
	    'class'		 => 'variable_dropdown_size',	
	    'label'       => __( 'Map implementation', 'woocommerce-jet-integration' ),
	    'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'map_implementation', true ),
	    'options' => array(
	    'select'   => __( 'select', 'woocommerce-jet-integration' ),
	    'no restrictions on product based pricing'   => __( 'no restrictions on product based pricing', 'woocommerce-jet-integration' ),
	    'Jet member savings on product only visible to logged in Jet members' => __( 'Jet member savings on product only visible to logged in Jet members', 'woocommerce-jet-integration' ),
	    'Jet member savings never applied to product' => __( 'Jet member savings never applied to product', 'woocommerce-jet-integration' )
	   	  )
	     )
	    );
	    woocommerce_wp_select(
	    array(
	    'id'          => '_product_tax_code[' . $variation->ID . ']',
	    'class'		  => 'variable_dropdown_size',
	    'label'       => __( 'Product Tax Code', 'woocommerce-jet-integration' ),
	    'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'product_tax_code', true ),
	    'options' => array(
	    'select'   => __( 'select', 'woocommerce-jet-integration' ),
	    'Toilet Paper'   => __( 'Toilet Paper', 'woocommerce-jet-integration' ),
	    'Thermometers' => __( 'Thermometers', 'woocommerce-jet-integration' ),
	    'Sweatbands' => __( 'Sweatbands', 'woocommerce-jet-integration' ),
	    'SPF Suncare Products' => __( 'SPF Suncare Products', 'woocommerce-jet-integration' ),
	    'Sparkling Water' => __( 'Sparkling Water', 'woocommerce-jet-integration' ),
	    'Smoking Cessation' => __( 'Smoking Cessation', 'woocommerce-jet-integration' ),
	    'Shoe Insoles' => __( 'Shoe Insoles', 'woocommerce-jet-integration' ),
	    'Safety Clothing' => __( 'Safety Clothing', 'woocommerce-jet-integration' ),
	    'Pet Foods' => __( 'Pet Foods', 'woocommerce-jet-integration' ),
	    'Paper Products' => __( 'Paper Products', 'woocommerce-jet-integration' ),
	    'OTC Pet Meds' => __( 'OTC Pet Meds', 'woocommerce-jet-integration' ),
	    'OTC Medication' => __( 'OTC Medication', 'woocommerce-jet-integration' ),
	    'Oral Care Products' => __( 'Oral Care Products', 'woocommerce-jet-integration' ),
	    'Non-Motorized Boats' => __( 'Non-Motorized Boats', 'woocommerce-jet-integration' ),
	    'Non Taxable Product' => __( 'Non Taxable Product', 'woocommerce-jet-integration' ),
	    'Mobility Equipment' => __( 'Mobility Equipment', 'woocommerce-jet-integration' ),
	    'Medicated Personal Care Items' => __( 'Medicated Personal Care Items', 'woocommerce-jet-integration' ),
	    'Infant Clothing' => __( 'Infant Clothing', 'woocommerce-jet-integration' ),
	    'Helmets' => __( 'Helmets', 'woocommerce-jet-integration' ),
	    'Handkerchiefs' => __( 'Handkerchiefs', 'woocommerce-jet-integration' ),
	    'Generic Taxable Product' => __( 'Generic Taxable Product', 'woocommerce-jet-integration' ),
	    'General Grocery Items' => __( 'General Grocery Items', 'woocommerce-jet-integration' ),
	    'General Clothing' => __( 'General Clothing', 'woocommerce-jet-integration' ),
	    'Fluoride Toothpaste' => __( 'Fluoride Toothpaste', 'woocommerce-jet-integration' ),
	    'Feminine Hygiene Products' => __( 'Feminine Hygiene Products', 'woocommerce-jet-integration' ),
	    'Durable Medical Equipment' => __( 'Durable Medical Equipment', 'woocommerce-jet-integration' ),
	    'Drinks under 50 Percent Juice' => __( 'Drinks under 50 Percent Juice', 'woocommerce-jet-integration' ),
	    'Disposable Wipes' => __( 'Disposable Wipes', 'woocommerce-jet-integration' ),
	    'Disposable Infant Diapers' => __( 'Disposable Infant Diapers', 'woocommerce-jet-integration' ),
	    'Dietary Supplements' => __( 'Dietary Supplements', 'woocommerce-jet-integration' ),
	    'Diabetic Supplies' => __( 'Diabetic Supplies', 'woocommerce-jet-integration' ),
	    'Costumes' => __( 'Costumes', 'woocommerce-jet-integration' ),
	    'Contraceptives' => __( 'Contraceptives', 'woocommerce-jet-integration' ),
	    'Contact Lens Solution' => __( 'Contact Lens Solution', 'woocommerce-jet-integration' ),
	    'Carbonated Soft Drinks' => __( 'Carbonated Soft Drinks', 'woocommerce-jet-integration' ),
	    'Car Seats' => __( 'Car Seats', 'woocommerce-jet-integration' ),
	    'Candy with Flour' => __( 'Candy with Flour', 'woocommerce-jet-integration' ),
	    'Candy' => __( 'Candy', 'woocommerce-jet-integration' ),
	    'Breast Pumps' => __( 'Breast Pumps', 'woocommerce-jet-integration' ),
	    'Braces and Supports' => __( 'Braces and Supports', 'woocommerce-jet-integration' ),
	    'Bottled Water Plain' => __( 'Bottled Water Plain', 'woocommerce-jet-integration' ),
	    'Beverages with 51 to 99 Percent Juice' => __( 'Beverages with 51 to 99 Percent Juice', 'woocommerce-jet-integration' ),
	    'Bathing Suits' => __( 'Bathing Suits', 'woocommerce-jet-integration' ),
	    'Bandages and First Aid Kits' => __( 'Bandages and First Aid Kits', 'woocommerce-jet-integration' ),
	    'Baby Supplies' => __( 'Sweatbands', 'woocommerce-jet-integration' ),
	    'Athletic Clothing' => __( 'Sweatbands', 'woocommerce-jet-integration' ),
	    'Adult Diapers' => __( 'Sweatbands', 'woocommerce-jet-integration' ),
	    )
	    )
	    );
	    
	    woocommerce_wp_select(
	    array(
	    'id'          => '_ships_alone[' . $variation->ID . ']',
	    'label'       => __( 'Ships Alone', 'woocommerce-jet-integration' ),
	    'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'ships_alone', true ),
	    'options' => array(
	    'false'   => __( 'false', 'woocommerce-jet-integration' ),
	    'true'   => __( 'true', 'woocommerce-jet-integration' ),
	    )
	    )
	    );
	    
	    woocommerce_wp_select(
	    array(
	    'id'          => '_exclude_from_fee_adjustments[' . $variation->ID . ']',
	    'label'       => __( 'Exclude From Fee Adjustment', 'woocommerce-jet-integration' ),
	    'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'exclude_from_fee_adjustments', true ),
	    'options' => array(
	    'false'   => __( 'false', 'woocommerce-jet-integration' ),
	    'true'   => __( 'true', 'woocommerce-jet-integration' ),
	    )
	    )
	    );
	    
	    woocommerce_wp_text_input(
	    array(
	    'id'          => '_jet_mfr_part_number[' . $variation->ID . ']',
	    'label'       => __('MFR Part Number', 'woocommerce-jet-integration' ),
	    'placeholder' => 'Value',
	    'desc_tip'    => 'true',
	    'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'jet_mfr_part_number', true ),
	    )
	    );
	     
	     
	    woocommerce_wp_text_input(
	    array(
	    'id'          => '_number_units_for_price_per_unit[' . $variation->ID . ']',
	    'label'       => __('Number Units For Price Per Units', 'woocommerce-jet-integration' ),
	    'placeholder' => 'Value',
	    'desc_tip'    => 'true',
	    'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'number_units_for_price_per_unit', true ),
	    )
	    );
	    woocommerce_wp_text_input(
	    array(
	    'id'          => '_type_of_unit_for_price_per_unit[' . $variation->ID . ']',
	    'label'       => __('Type Of Unit For Price Per Unit', 'woocommerce-jet-integration' ),
	    'placeholder' => 'Value',
	    'desc_tip'    => 'true',
	    'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    'value'       => get_post_meta( $variation->ID, 'type_of_unit_for_price_per_unit', true ),
	    )
	    );
	     
	    
	    ?>
	    	    	       	
	    	    	       	<!-- field seven -->
	    	    		  <p class="form-field dimensions_field">
    	    				<label for="product_length"><?php _e('Package (inches):', 'woocommerce-jet-integration'); ?></label>
    	    					<span class="wrap package_size">
    	    						<input type="text" value="<?php echo get_post_meta( $variation->ID, 'package_length', true ); ?>" name="_package_length[<?php echo $variation->ID ?>]" id="_package_length[<?php echo $variation->ID ?>]" size="6" class="input-text wc_input_decimal" placeholder="Length" id="product_length">
    	    						<input type="text" value="<?php echo get_post_meta( $variation->ID, 'package_width', true ); ?>" name="_package_width[<?php echo $variation->ID ?>]"  id="_package_width[<?php echo $variation->ID ?>]" size="6" class="input-text wc_input_decimal" placeholder="Width">
    	    						<input type="text" value="<?php echo get_post_meta( $variation->ID, 'package_height', true ); ?>" name="_package_height[<?php echo $variation->ID ?>]" id="_package_height[<?php echo $variation->ID ?>]" size="6" class="input-text wc_input_decimal last" placeholder="Height">
    	    					</span>
	    	    			</p>
	    	    	       	<!-- seven Field End  -->
	    	
	    	  <?php 
	    	  woocommerce_wp_select(
	    	  array(
	    	  'id'          => '_prop_65[' . $variation->ID . ']',
	    	  'label'       => __( 'Select PROP 65', 'woocommerce-jet-integration' ),
	    	  'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	    	  'value'       => get_post_meta( $variation->ID, 'prop_65', true ),
	    	  'options' => array(
	    	  'false'   => __( 'false', 'woocommerce-jet-integration' ),
	    	  'true'   => __( 'true', 'woocommerce-jet-integration' ),
	    	  )
	    	  )
	    	  );
	    	  
	    	  woocommerce_wp_text_input(
	    	  array(
	    	  'id'          => '_legal_disclaimer_description[' . $variation->ID . ']',
	    	  'label'       => __('Legal Disclaimer Description', 'woocommerce-jet-integration' ),
	    	  'placeholder' => 'Value',
	    	  'desc_tip'    => 'true',
	    	  'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    	  'value'       => get_post_meta( $variation->ID, 'legal_disclaimer_description', true ),
	    	  )
	    	  );
	    	  
	    	  ?><div class="bullet_div_wrap"><?php 
	    	  woocommerce_wp_text_input(
	    	  array(
	    	  'id'          => '_bullet_one[' . $variation->ID . ']',
	    	  'class'		=>	'bullet_settings',
	    	  'label'       => __('Bullets Value', 'woocommerce-jet-integration' ),
	    	  'placeholder' => 'Value',
	    	  'desc_tip'    => 'true',
	    	  'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    	  'value'       => get_post_meta( $variation->ID, '_bullet_1', true ),
	    	  )
	    	  );
	    	  
	    	
	    	  woocommerce_wp_text_input(
	    	  array(
	    	  'id'          => '_bullet_two[' . $variation->ID . ']',
	    	  'label'       => '&nbsp;',
	    	  'class'		=>	'bullet_settings',
	    	  'placeholder' => 'Value',
	    	  'desc_tip'    => 'true',
	    	  'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    	  'value'       => get_post_meta( $variation->ID, '_bullet_2', true ),
	    	  )
	    	  );
	    	  woocommerce_wp_text_input(
	    	  array(
	    	  'id'          => '_bullet_three[' . $variation->ID . ']',
	    	  'label'       => '&nbsp;',
	    	  'class'		=>	'bullet_settings',
	    	  'placeholder' => 'Value',
	    	  'desc_tip'    => 'true',
	    	  'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    	  'value'       => get_post_meta( $variation->ID, '_bullet_3', true ),
	    	  )
	    	  );
	    	   
	    	  woocommerce_wp_text_input(
	    	  array(
	    	  'id'          => '_bullet_four[' . $variation->ID . ']',
	    	  'label'       => '&nbsp;',
	    	  'class'		=>	'bullet_settings',
	    	  'placeholder' => 'Value',
	    	  'desc_tip'    => 'true',
	    	  'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    	  'value'       => get_post_meta( $variation->ID, '_bullet_4', true ),
	    	  )
	    	  );
	    	   
	    	  woocommerce_wp_text_input(
	    	  array(
	    	  'id'          => '_bullet_five[' . $variation->ID . ']',
	    	  'label'       => '&nbsp;',
	    	  'class'		=>	'bullet_settings',
	    	  'placeholder' => 'Value',
	    	  'desc_tip'    => 'true',
	    	  'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    	  'value'       => get_post_meta( $variation->ID, '_bullet_5', true ),
	    	  )
	    	  );
	    	  ?></div><?php 
	    	  $statement = array(
	    	  		'no warning applicable' => 'no warning applicable',
	    	  		'choking hazard small parts' => 'choking hazard small parts',
	    	  		'choking hazard is a small ball' =>'choking hazard is a small ball',
	    	  		'choking hazard is a marble' =>'choking hazard is a marble',
	    	  		'choking hazard contains a small ball' => 'choking hazard contains a small ball',
	    	  		'choking hazard contains a marble' => 'choking hazard contains a marble',
	    	  		'choking hazard balloon' => 'choking hazard balloon');
	    	   
	    	  $this->libraryAction->woocommerce_wp_select_multiple( array(
	    	  		'id' => 'cpsia_cautionary_statements[' . $variation->ID . ']',
	    	  		'name' => 'cpsia_cautionary_statements[' . $variation->ID . '][]',
	    	  		'label' => __('CPSIA causionary statements', 'woocommerce-jet-integration'),
	    	  		'value'	=>  get_post_meta( $variation->ID, 'cpsia_cautionary_statements', true ),
	    	  		'options' => $statement
	    	  )
	    	  );
	    	  ?>
	    	  	<hr>	        
	    	  	<div class="options_group">
	    	  				
	    	  	    </div>
	    	  	<?php 
	    	  	        $jet_node_id = get_option('jet_node_id');
	    	  	        $jet_node_id = json_decode($jet_node_id);
	    	  	       // global $post;
	    	  	       // echo '<pre>';print_r($post);die;
	    	  	        ?>
	    	  	        <?php if(!empty($jet_node_id)){ //echo '<pre>';print_r($jet_node_id); ?>
	    	  	        	<?php foreach($jet_node_id as $key => $value){	?> 
	    	  	        		<p class="form-field dimensions_field shipping_exception_label">
	    	  					<label for="product_length"><?php  _e('Shipping Exception for fullfillment : '.$value, 'woocommerce-jet-integration'); ?></label>
	    	  					<span class="wrap">
	    	  		         
	    	  	<?php 
	    	  	// Checkbox
	    	  	woocommerce_wp_checkbox(
	    	  	array(
	    	  	'id'            => 'sipping_exception_settings['.$value.'][' . $variation->ID . ']',
	    	  	'label'         => __('<b>Enable Shipping Exception</b>', 'woocommerce' ),
	    	  	'description'   => __( 'Check For activate shipping exception settings on this fullfillment', 'woocommerce' ),
	    	  	'value'			=> get_post_meta( $variation->ID, 'sipping_exception_settings_'.$value, true ),
	    	  	)
	    	  	);
	    	  		woocommerce_wp_select(
	    	  		array(
	    	  		'id'      => '_service_level['.$value.'][' . $variation->ID . ']',
	    	  		'label'   => __( 'Service Level', 'woocommerce-jet-integration' ),
	    	  		'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	    	  		'value'       => get_post_meta( $variation->ID, 'jet_service_level_'.$value, true ),
	    	  		'options' => array(
	    	  		'choose'   => __( 'Choose any value', 'woocommerce-jet-integration' ),
	    	  		'SecondDay'   => __( 'SecondDay', 'woocommerce-jet-integration' ),
	    	  		'NextDay'   => __( 'NextDay', 'woocommerce-jet-integration' ),
	    	  		'Scheduled' => __( 'Scheduled', 'woocommerce-jet-integration' ),
	    	  		'Expedited' => __( 'Expedited', 'woocommerce-jet-integration' ),
	    	  		'Standard' => __( 'Standard', 'woocommerce-jet-integration' ),
	    	  		)
	    	  		)
	    	  		);
	    	  	 
	    	  	//shipping method
	    	  	woocommerce_wp_select(
	    	  	array(
	    	  	'id'      => '_shipping_methods['.$value.'][' . $variation->ID . ']',
	    	  	'label'   => __( 'Shipping Methods', 'woocommerce-jet-integration' ),
	    	  	'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	    	  	'value'       => get_post_meta( $variation->ID, 'jet_shipping_methods_'.$value, true ),
	    	  	'options' => array(
	    	  	'choose'   => __( 'Choose any value', 'woocommerce-jet-integration' ),
	    	  	'UPS Ground'   => __( 'UPS Ground', 'woocommerce-jet-integration' ),
	    	  	'UPS Next Day Air'   => __( 'UPS Next Day Air', 'woocommerce-jet-integration' ),
	    	  	'FedEx Home' => __( 'FedEx Home', 'woocommerce-jet-integration' ),
	    	  	'Freight' => __( 'Freight', 'woocommerce-jet-integration' ),
	    	  	)
	    	  	)
	    	  	);
	    	  	//override type
	    	  	woocommerce_wp_select(
	    	  	array(
	    	  	'id'      => '_override_type['.$value.'][' . $variation->ID . ']',
	    	  	'label'   => __( 'Override Type', 'woocommerce-jet-integration' ),
	    	  	'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	    	  	'value'       => get_post_meta( $variation->ID, 'jet_override_type_'.$value, true ),
	    	  	'options' => array(
	    	  	'choose'   => __( 'Choose any value', 'woocommerce-jet-integration' ),
	    	  	'Override charge'   => __( 'Override charge', 'woocommerce-jet-integration' ),
	    	  	'Additional charge'   => __( 'Additional charge', 'woocommerce-jet-integration' ),
	    	  	)
	    	  	)
	    	  	);
	    	  	
	    	  	
	    	  	// shipping_charge_amount
	    	  	woocommerce_wp_text_input(
	    	  	array(
	    	  	'id'          => 'jet_shipping_charge_amount['.$value.'][' . $variation->ID . ']',
	    	  	'label'       => __( 'Shipping Charge Amount', 'woocommerce-jet-integration' ),
	    	  	'placeholder' => 'Amount',
	    	  	'desc_tip'    => 'true',
	    	  	'description' => __( 'Enter the custom value here.', 'woocommerce-jet-integration' ),
	    	  	'value'       => get_post_meta($variation->ID, 'jet_shipping_charge_amount_'.$value, true ),
	    	  	)
	    	  	);
	    	  	
	    	  	//shipping method
	    	  	woocommerce_wp_select(
	    	  	array(
	    	  	'id'      => 'shipping_exception_type_id['.$value.'][' . $variation->ID . ']',
	    	  	'label'   => __( 'Shipping exception type', 'woocommerce-jet-integration' ),
	    	  	'description' => __( 'Choose a value.', 'woocommerce-jet-integration' ),
	    	  	'value'       => get_post_meta($variation->ID,'jet_shipping_exception_type_'.$value, true ),
	    	  	'options' => array(
	    	  	'choose'   => __( 'Choose any value', 'woocommerce-jet-integration' ),
	    	  	'exclusive'   => __( 'exclusive', 'woocommerce-jet-integration' ),
	    	  	'restricted'   => __( 'restricted', 'woocommerce-jet-integration' ),
	    	  	)
	    	  	)
	    	  	);
	    	  	?></span>
	    	  		       </p>
	    	  		<?php }?>
	    	  		 <?php }else{?>
	    	  			 <h3 style="text-align:center;"><?php  _e('Please Set atleast one fullfillment,otherwise you product will be not uploaded on jet ', 'woocommerce-jet-integration'); ?></h3>
	    	  			<?php }?>
	    	  			<hr>
	    	  			<div class="options_group">
	    	  					<p class="form-field">
	    	  						<h3 style="text-align:center;"><?php  _e('Settings For Return Exception for Return IDs', 'woocommerce-jet-integration'); ?></h3>
	    	  			         </p>
	    	  		    </div>
	    	  			 <p class="form-field dimensions_field">
	    	  			 <?php $jet_return_id = get_option('jet_return_id');
	    	  						$jet_return_id = json_decode($jet_return_id);
	    	  	
	    	  						if(!empty($jet_return_id)){?>
	    	  							<label for="product_length"><?php _e('Return Exception ','woocommerce-jet-integration')?></label>
	    	  						<span class="wrap">
	    	  						<?php 
	    	  						
	    	  						// Checkbox
	    	  						woocommerce_wp_checkbox(
	    	  						array(
	    	  						'id'            => 'return_exception_setting[' . $variation->ID . ']',
	    	  						'label'         => __('<b>Enable Return Exception</b>', 'woocommerce-jet-integration' ),
	    	  						'description'   => __( 'Check For activate return exception setting for this product', 'woocommerce-jet-integration' ),
	    	  						'value'			=> get_post_meta( $variation->ID, 'return_exception_setting', true ),
	    	  						)
	    	  						);
	    	  						// shipping_charge_amount
	    	  						woocommerce_wp_text_input(
	    	  						array(
	    	  						'id'          => 'jet_time_to_return[' . $variation->ID . ']',
	    	  						'label'       => __( 'Time To Return', 'woocommerce-jet-integration' ),
	    	  						'placeholder' => 'Days',
	    	  						'desc_tip'    => 'true',
	    	  						'description' => __( 'Enter the number of days after purchase a customer can return the item.', 'woocommerce-jet-integration' ),
	    	  						'value'       => get_post_meta($variation->ID, 'jet_time_to_return', true ),
	    	  						)
	    	  						);
	    	  						
	    	  						$jet_id_assoc_array	=	array();
	    	  		foreach ($jet_return_id as $id_index	=>	$id_data){
	    	  			$jet_id_assoc_array[$id_data]	=	$id_data;	
	    	  			
	    	  		}
	    	  		$this->libraryAction->woocommerce_wp_select_multiple(array(
	    	  				'id'            => 'return_id[' . $variation->ID . ']',
	    	  				'name'			=>	'return_id[' . $variation->ID . '][]',
	    	  				'label'         => __('<b>Return ids', 'woocommerce-jet-integration' ),
	    	  				'description'   => __( 'Select return ids for sending Return Exception to this Return id', 'woocommerce-jet-integration' ),
	    	  				'value'			=> json_decode(get_post_meta( $variation->ID, 'return_id', true )),
	    	  				'options'		=>	$jet_id_assoc_array,
	    	  		));
	    	  						
	    	  						$this->libraryAction->woocommerce_wp_select_multiple( array(
	    	  		    'id' => '_return_shipping_methods[' . $variation->ID . ']',
	    	  		    'name' => '_return_shipping_methods[' . $variation->ID . '][]',
	    	  		    'label' => __('Return Shipping Method', 'woocommerce-jet-integration'),
	    	  		   	'value'	=>  json_decode(get_post_meta( $variation->ID, '_return_shipping_methods' ,true)),
	    	  		    'options' => array(
	    	  			'UPS 3 Day Select'   => __( 'UPS 3 Day Select', 'woocommerce-jet-integration' ),
	    	  			'UPS Ground' => __( 'UPS Ground', 'woocommerce-jet-integration' ),
	    	  			'UPS 2nd Day Air'   => __( 'UPS 2nd Day Air', 'woocommerce-jet-integration' ),
	    	  			'UPS Next Day Air'   => __( 'UPS Next Day Air', 'woocommerce-jet-integration' ),
	    	  			'UPS 2nd Day Air AM' => __( 'UPS 2nd Day Air AM', 'woocommerce-jet-integration' ),
	    	  			'UPS Next Day Air Saver'   => __( 'UPS Next Day Air Saver', 'woocommerce-jet-integration' ),
	    	  			'UPS SurePost'   => __( 'UPS SurePost', 'woocommerce-jet-integration' ),
	    	  			'UPS Mail Innovations' => __( 'UPS Mail Innovations', 'woocommerce-jet-integration' ),
	    	  			'FedEx Ground' => __( 'FedEx Ground', 'woocommerce-jet-integration' ),
	    	  			'FedEx Home Delivery'   => __( 'FedEx Home Delivery', 'woocommerce-jet-integration' ),
	    	  			'FedEx Express Saver'   => __( 'FedEx Express Saver', 'woocommerce-jet-integration' ),
	    	  			'FedEx 2 Day' => __( 'FedEx 2 Day', 'woocommerce-jet-integration' ),
	    	  			'FedEx Standard Overnight'   => __( 'FedEx Standard Overnight', 'woocommerce-jet-integration' ),
	    	  			'FedEx Priority Overnight'   => __( 'FedEx Priority Overnight', 'woocommerce-jet-integration' ),
	    	  			'FedEx First Overnight' => __( 'FedEx First Overnight', 'woocommerce-jet-integration' ),
	    	  			'FedEx Smart Post'   => __( 'FedEx Smart Post', 'woocommerce-jet-integration' ),
	    	  		    )));
	    	  						
	    	  						?></span>
	    	  						</p>
	    	  						</div>
	    	  		<?php 
	    	  		     }else{
	    	  		     	?>
	    	  		     	<h3 style="text-align:center;"><?php  _e('Please Set atleast one Return Id', 'woocommerce-jet-integration'); ?></h3>
	    	  		     	<?php 
	    	  		     }
	}
	
	/**
	 * Save new fields for variations
	 *
	 */
	public function save_variation_settings_fields( $post_id ) {

		//print_R($_POST);die;
		$postID					=	$_POST['product_id'];
		$productCats 			= 	get_the_terms($postID, "product_cat");
		$mappedIDs				=	get_option('cedWooJetMapping',true);
		
		if(is_array($mappedIDs)){
		
			$catArray	=	array();
			foreach($mappedIDs as $woocatid => $jetcatId){
		
				$catArray[]	=	$woocatid;
			}
		
			if(!empty($catArray) && is_array($catArray)){
					
				$jetSelectedNodeID = array();
				
				if(!empty($productCats)){
				foreach ($productCats as $index	=>	$catObject){
		
					if(in_array($catObject->term_id, $catArray)){
							
						$jetSelectedNodeID[$catObject->term_id]	=	$mappedIDs[$catObject->term_id];
					}
				}
				}
			}
		}
			
		
		$jetAttrInfo	=	array();
		
		if(!empty($jetSelectedNodeID)){
			foreach($jetSelectedNodeID as $wooNodeID => $jetNodeID){
			
				$mappedAttributes		=	get_option($jetNodeID.'_linkedAttributes',false);
			
				if($mappedAttributes){
			
					$mappedAttributes	=	json_decode($mappedAttributes);
					if(is_array($mappedAttributes)){
			
						$jetAttrInfo[$jetNodeID]	=	$this->modelAction->fetchAttrDetails($mappedAttributes);
					}
				}
			}
		}
		
		if(!empty($jetAttrInfo)){
			foreach($jetAttrInfo as $jetNode => $mappedCAT):
				foreach($mappedCAT as $attrARRAY):
					$attrObject = $attrARRAY[0];
			
					//echo $attrObject->freetext;
					//echo $_POST[$tempName.'_unit'];
					//224_15000028_114_unit
					$tempName	=	$post_id."_".$jetNode."_".$attrObject->jet_attr_id;
					if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit))){
						
						update_post_meta($post_id, 	$tempName, 	$_POST[$tempName]);
						update_post_meta($post_id, 	$tempName.'_unit', 	$_POST[$tempName.'_unit']);
					}
					else{
						update_post_meta($post_id, 	$tempName, 	$_POST[$tempName]);
					}
				endforeach;
			endforeach;
		}
		
		update_post_meta($post_id, $post_id.'_selectedCatAttr',$_POST[$post_id.'_selectedCatAttr']);
		
		// save upc value
		$standard_code = $_POST['standardCodetype'][ $post_id ];
		if( ! empty( $standard_code ) ) {
			update_post_meta( $post_id, 'standardCodetype',esc_attr( $standard_code ) );
		}
		// save upc_e value
		$standard_code_val = $_POST['standardCodeVal'][ $post_id ];
		
// 		if( ! empty( $standard_code_val ) ) {
			update_post_meta( $post_id, 'standardCodeVal',esc_attr( $standard_code_val ) );
// 		}
		
		
		// save asin value
		$text_field_asin = $_POST['_jet_asin'][ $post_id ];
			update_post_meta( $post_id, '_jet_asin',esc_attr( $text_field_asin ) );
		
		
		//price type
		$pricetype = $_POST['pricetype'][ $post_id ];
		if( ! empty( $pricetype ) ) {
			update_post_meta( $post_id, 'jetPriceSelect', esc_attr( $pricetype ) );
		}
		
		if(trim($pricetype) == trim('otherPrice')){
			$otherprice = $_POST['variationotherprice'][ $post_id ];
			if( ! empty( $otherprice ) ) {
				update_post_meta( $post_id, 'jetPrice', esc_attr( $otherprice ) );
			}
		}
		//stock type
			
		$stocktype = $_POST['stocktype'][ $post_id ];
		if( ! empty( $stocktype ) ) {
			update_post_meta( $post_id, 'jetStockSelect', esc_attr( $stocktype ) );
		}
		
		if(empty($stocktype))
			$stocktype = 'central';
			
		if(trim($stocktype) == trim('other')){
			$otherstock = $_POST['variationotherstock'][ $post_id ];
			if( ! empty( $otherstock ) ) {
				update_post_meta( $post_id, 'jetStock', esc_attr( $otherstock ) );
			}
		}
		//save country manufacturer
		$text_field_manf = $_POST['_jet_country_manufacture'][ $post_id ];
		if( ! empty( $text_field_manf ) ) {
			update_post_meta( $post_id, 'jet_country', esc_attr( $text_field_manf ) );
		}
		
		//save brand _brand
		$text_field_brand = $_POST['_brand'][ $post_id ];
		if( ! empty( $text_field_brand ) ) {
			update_post_meta( $post_id, 'jetBrand', esc_attr( $text_field_brand ) );
		}
		
		//save manufacturing
		$text_field_manufacturer = $_POST['_manufacturer'][ $post_id ];
		if( ! empty( $text_field_manufacturer ) ) {
			update_post_meta( $post_id, 'product_manufacturer', esc_attr( $text_field_manufacturer ) );
		}
		//safety warning
		$text_field_safety_warning = $_POST['_safety_warning'][ $post_id ];
		if( ! empty( $text_field_safety_warning ) ) {
			update_post_meta( $post_id, 'safety_warning', esc_attr( $text_field_safety_warning ) );
		}
		
		//amazon
		$amazon_key = $_POST['amazon_item_type_keyword'][ $post_id ];
		//print_r($amazon_key);die;
		if( ! empty( $amazon_key ) ) {
			update_post_meta( $post_id, 'amazon_item_type_keyword', esc_attr( $amazon_key ) );
		}
		
		// Select
		$map_implementation = $_POST['_map_implementation'][ $post_id ];
		if( ! empty( $map_implementation ) ) {
			update_post_meta( $post_id, 'map_implementation', esc_attr( $map_implementation ) );
		}
		
		//fullfillment time
		$text_field_fullfillment_time = $_POST['_fullfillment_time'][ $post_id ];
		if( ! empty( $text_field_fullfillment_time ) ) {
			update_post_meta( $post_id, 'fulfillment_time', esc_attr( $text_field_fullfillment_time ) );
		}
		
		//retail price
		$text_field_retail_price = $_POST['_retail_price'][ $post_id ];
		if( ! empty( $text_field_retail_price ) ) {
			update_post_meta( $post_id, 'msrp', esc_attr( $text_field_retail_price ) );
		}
		
		//map price
		$text_field_map_price = $_POST['_map_price'][ $post_id ];
		if( ! empty( $text_field_map_price ) ) {
			update_post_meta( $post_id, 'map_price', esc_attr( $text_field_map_price ) );
		}
		
		//product tax code
		$text_field_product_tax_code = $_POST['_product_tax_code'][ $post_id ];
		if( ! empty( $text_field_product_tax_code ) ) {
			update_post_meta( $post_id, 'product_tax_code', esc_attr( $text_field_product_tax_code ) );
		}
		
		//ships alone
		$text_field_ships_alone = $_POST['_ships_alone'][ $post_id ];
		if( ! empty( $text_field_ships_alone ) ) {
			update_post_meta( $post_id, 'ships_alone', esc_attr( $text_field_ships_alone ) );
		}
		
		//fee adjustment
		$text_field_exclude_from_fee_adjustments = $_POST['_exclude_from_fee_adjustments'][ $post_id ];
		if( ! empty( $text_field_exclude_from_fee_adjustments ) ) {
			update_post_meta( $post_id, 'exclude_from_fee_adjustments', esc_attr( $text_field_exclude_from_fee_adjustments ) );
		}
		
		//mfr part number
		$text_field_jet_mfr_part_number = $_POST['_jet_mfr_part_number'][ $post_id ];
		if( ! empty( $text_field_jet_mfr_part_number ) ) {
			update_post_meta( $post_id, 'jet_mfr_part_number', esc_attr( $text_field_jet_mfr_part_number ) );
		}
		
		//number units
		$text_field_number_units_for_price_per_unit = $_POST['_number_units_for_price_per_unit'][ $post_id ];
		if( ! empty( $text_field_number_units_for_price_per_unit ) ) {
			update_post_meta( $post_id, 'number_units_for_price_per_unit', esc_attr( $text_field_number_units_for_price_per_unit ) );
		}
		
		//types of unit
		$type_of_unit_for_price_per_unit = $_POST['_type_of_unit_for_price_per_unit'][ $post_id ];
		if( ! empty( $type_of_unit_for_price_per_unit ) ) {
			update_post_meta( $post_id, 'type_of_unit_for_price_per_unit', esc_attr( $type_of_unit_for_price_per_unit ) );
		}
		//prop 65 settings
		$text_field_prop_65 = $_POST['_prop_65'][ $post_id ];
		if( ! empty( $text_field_prop_65 ) ) {
			update_post_meta( $post_id, 'prop_65', esc_attr( $text_field_prop_65 ) );
		}
		
		//legal disclaimer
		$legal_disclaimer_description = $_POST['_legal_disclaimer_description'][ $post_id ];
		if( ! empty( $legal_disclaimer_description ) ) {
			update_post_meta( $post_id, 'legal_disclaimer_description', esc_attr( $legal_disclaimer_description ) );
		}
		
		//bullets settings
		$bullet_one = $_POST['_bullet_one'][ $post_id ];
		if( ! empty( $bullet_one ) ) {
			update_post_meta( $post_id, '_bullet_1', esc_attr( $bullet_one ) );
		}
		
		//buttlet two settings
		$bullet_two = $_POST['_bullet_two'][ $post_id ];
		if( ! empty( $bullet_two ) ) {
			update_post_meta( $post_id, '_bullet_2', esc_attr( $bullet_two ) );
		}
		//bullet three
		$bullet_three = $_POST['_bullet_three'][ $post_id ];
		if( ! empty( $bullet_three ) ) {
			update_post_meta( $post_id, '_bullet_3', esc_attr( $bullet_three ) );
		}
		
		//bullet four
		$bullet_four = $_POST['_bullet_four'][ $post_id ];
		if( ! empty( $bullet_four ) ) {
			update_post_meta( $post_id, '_bullet_4', esc_attr( $bullet_four ) );
		}
		
		//bullet five
		$bullet_five = $_POST['_bullet_five'][ $post_id ];
		if( ! empty( $bullet_five ) ) {
			update_post_meta( $post_id, '_bullet_5', esc_attr( $bullet_five ) );
		}
		
		//package length settings
		$package_length = $_POST['_package_length'][ $post_id ];
		if( ! empty( $package_length ) ) {
			update_post_meta( $post_id, 'package_length', esc_attr( $package_length ) );
		}
		
		//package width
		$package_width = $_POST['_package_width'][ $post_id ];
		if( ! empty( $package_width ) ) {
			update_post_meta( $post_id, 'package_width', esc_attr( $package_width ) );
		}
		
		//package height settings
		$package_height = $_POST['_package_height'][ $post_id ];
		if( ! empty( $package_height ) ) {
			update_post_meta( $post_id, 'package_height', esc_attr( $package_height ) );
		}
		// Hidden field
		$hidden = $_POST['_hidden_field'][ $post_id ];
		if( ! empty( $hidden ) ) {
			update_post_meta( $post_id, '_hidden_field', esc_attr( $hidden ) );
		}
		//save checkbox value
		$cpsia_cautionary_statement 		= 	$_POST['cpsia_cautionary_statements'][ $post_id ];
		//	print_r($cpsia_cautionary_statement); die('ok');
		if(!empty($cpsia_cautionary_statement))
			update_post_meta( $post_id, 'cpsia_cautionary_statements', $cpsia_cautionary_statement );
		
		$jet_node_id = get_option('jet_node_id');
		$jet_node_id = json_decode($jet_node_id);
		if(!empty($jet_node_id)){
			foreach($jet_node_id as $key => $value){
		
		
				if(trim($pricetype) == 'fullfillment_wise'){
					update_post_meta( $post_id, 'p_'.$value,$_POST['p_'.$value][ $post_id ]);
				}
				if(trim($stocktype) == 'fullfillment_wise'){
					update_post_meta( $post_id, 's_'.$value, $_POST['s_'.$value][ $post_id ]);
				}
		
				$enable_exception = isset( $_POST['sipping_exception_settings'][$value][ $post_id ] ) ? 'yes' : 'no';
				update_post_meta( $post_id, 'sipping_exception_settings_'.$value, $enable_exception );
		
				//service level
				$_service_level = $_POST['_service_level'][$value][ $post_id ];
				if( !empty( $_service_level ) )
					update_post_meta( $post_id, 'jet_service_level_'.$value, esc_attr( $_service_level ) );
		
				// Select
				$_shipping_methods = $_POST['_shipping_methods'][$value][ $post_id ];
				if( !empty( $_shipping_methods )  )
					update_post_meta( $post_id, 'jet_shipping_methods_'.$value, esc_attr( $_shipping_methods ) );
		
				// Select
				$_override_type = $_POST['_override_type'][$value][ $post_id ];
				if( !empty( $_override_type ) )
					update_post_meta( $post_id, 'jet_override_type_'.$value, esc_attr( $_override_type ) );
		
				// Text Field
				$shipping_charge_amount = $_POST['jet_shipping_charge_amount'][$value][ $post_id ];
				if( !empty( $shipping_charge_amount ) )
					update_post_meta( $post_id, 'jet_shipping_charge_amount_'.$value, esc_attr( $shipping_charge_amount ) );
		
		
				// Select
				$shipping_exception_type = $_POST['shipping_exception_type_id'][$value][ $post_id ];
				if( !empty( $shipping_exception_type ) ){
					update_post_meta( $post_id,'jet_shipping_exception_type_'.$value, esc_attr( $shipping_exception_type ) );
				}
			}
		}
		
		$jet_return_id = get_option('jet_return_id');
		$jet_return_id = json_decode($jet_return_id);
		// Checkbox
		$return_exception_setting = isset( $_POST['return_exception_setting'][ $post_id ] ) ? 'yes' : 'no';
		update_post_meta( $post_id, 'return_exception_setting', $return_exception_setting );
		
		// Text Field
		$jet_time_to_return = $_POST['jet_time_to_return'][ $post_id ];
		if( !empty( $jet_time_to_return ) )
			update_post_meta( $post_id, 'jet_time_to_return', esc_attr( $jet_time_to_return ) );
		
		$_return_ids = $_POST['return_id'][ $post_id ];
		if( !empty( $_return_ids )  )
			update_post_meta( $post_id, 'return_id', json_encode($_return_ids));
		unset($_return_ids);
		
		//print_r($_POST); die;
		// Select
		$_return_shipping_methods = $_POST['_return_shipping_methods'][ $post_id ];
		
		if( !empty( $_return_shipping_methods )  )
			update_post_meta( $post_id, '_return_shipping_methods', json_encode($_return_shipping_methods));
		unset($_return_shipping_methods);
		
		//jet product title
		$jet_title = $_POST['_jet_title'][ $post_id ];
		if( ! empty( $jet_title ) ) {
			update_post_meta( $post_id, '_jet_title',esc_attr( $jet_title ) );
		}
	}
	
	/**
	 * function for creating meta box for performing jet related order operations.
	 */
	public function addOrderMetaBox(){
		
		global $post;

		$order_id = $post->ID;
		$order_action 			= 	get_post_meta($order_id,'order_action',true);
		$shipping_carrier 		= 	get_post_meta($order_id,'shipping_carrier',true);
		$tracking_number		= 	get_post_meta($order_id,'tracking_number',true);
		$ship_to_date			= 	get_post_meta($order_id,'ship_to_date',true);
		$exp_delv_date			=	get_post_meta($order_id,'exp_delv_date',true);
		$carrier_pickup_date	=	get_post_meta($order_id,'carrier_pickup_date',true);
		$request_service_level  = 	get_post_meta($order_id,'request_service_level',true);
		$fullfillment_nodeorder = 	get_post_meta($order_id,'order_for_fullfillment_node',true);
		$order_type				= 	get_post_meta($order_id,'order_type_jet',true);

		global $wpdb;
		$table_name = $wpdb->prefix.'jet_order_detail';
		$qry = "SELECT `woocommerce_order_id` from `$table_name` where 1 ;";
		$resultdata = $wpdb->get_results($qry);
		for($i=0;$i<count($resultdata);$i++)
		{
			$woo_order_ids[] = $resultdata[$i]->woocommerce_order_id;
		}

		if(empty($woo_order_ids)){
			?>
		<div id="not_jet_order_settings" class="panel woocommerce_options_panel">
		        <div class="options_group">
			        <p class="form-field">
		               <h3 style="text-align:center;"><b><?php  _e('Not Jet Order ', 'woocommerce-jet-integration'); ?></b></h3>
		            </p>
		        </div>
		        </div>
			<?php return;
			}
			?>
			<?php if(in_array($order_id, $woo_order_ids)) {?>
			
			<div id="jet-loading" class="loading-style-bg" style="display: none;">
		 		<img src="<?php echo plugin_dir_url(__dir__);?>css/BigCircleBall.gif">
		 		<p class="loading-content">Processing... Please Wait..</p>
			</div>
			<?php $order_status_jet	= get_post_meta($order_id,'order_action',true);
					//$order_status_jet = "acknowledged";
					if(empty($order_status_jet))
					{
						$order_status_jet	=	__('Ready', 'woocommerce-jet-integration');
					}
					?>
			<div id="jet_order_settings" class="panel woocommerce_options_panel">
		        <div class="options_group">
			        <p class="form-field">
		               <h3 style="text-align:center;"><?php  _e('Jet order status :', 'woocommerce-jet-integration'); echo $order_status_jet; ?></h3>
		            </p>
		        </div>
		        <div class="options_group custom_tab_options"> 
			
							 <p class="form-field">
		                    <input type="hidden"  name="order_action" value="<?php echo $order_action;?>" id="order_action_txt"/>
		                    <input type="hidden"  name="order_type_jet" value="<?php echo $order_type;?>" id="order_action_txt"/>
		            		</p>
		        <?php 
		        if($order_status_jet=='rejected'){?>
		        	<h1 style="text-align:center;"><?php _e('ORDER REJECTED ','woocommerce-jet-integration');?></h1>
		        <?php 
		        }
		        if($order_status_jet	==	'Ready'){
				?>
				    <!-- field first -->
				    <p class="form-field">
		                <label><?php _e('Select Order Action:', 'woocommerce'); ?></label>
			            <!--  <select  id="selected_order_action" name="selected_order_action">
			             	<option value="acknowledge_order"><?php _e('Acknowledge','woocommerce-jet-integration')?></option>
			             </select> -->
			                <input type="button"  class="button primary submit_order_action" name="order_action" value="Acknowledge Order" data-action_id='acknowledge_order' data-order_id = "<?php echo $order_id;?>" id="submit_order_actions"/>
			               <!--  <span class="visibility"><a class="button primary" href="<?php //echo menu_page_url('jet_order_reject_return_page', false).'&order_id='.$order_id;?>">Reject Order</a></span> -->
		            </p>
		            </div>
						<?php 
					}
					else if($order_status_jet	==	'acknowledged'){ ?>
				     <p class="form-field">
		                    <input type="hidden"  name="order_action" value="<?php echo $order_action;?>" id="order_action_txt"/>
		                    <input type="hidden"  name="order_type_jet" value="<?php echo $order_type;?>" id="order_action_txt"/>
		            </p>
				    <!-- field first -->
				    <p class="form-field">
		                <label><?php _e('Select Order Action:', 'woocommerce'); ?></label>
			            <!--  <select id="selected_order_action" name="selected_order_action">
			             	<option value="order_ship"><?php _e('Ship','woocommerce-jet-integration')?></option>
			             </select>
			                <input type="button"  class="button primary" name="order_action" value="Submit action" data-order_id = "<?php echo $order_id;?>" id="submit_order_action"/>
			               !-->
			          <input type="button"  class="button primary submit_order_action" name="order_actions" value="Ship Order" data-action_id='order_ship' data-order_id = "<?php echo $order_id;?>" id="submit_order_actionss"/>      
		            </p>
		            <p class="form-field">
		                <label><?php _e('Order For Fullfillment Node:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="fullfillment_order_node" value="<?php echo $fullfillment_nodeorder;?>" id="fullfillment_order_node" readonly/>
		            </p>
		            
		            <p class="form-field">
		                <label><?php _e('Shipping Carrier Used:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="shipping_carrier" value="<?php echo $shipping_carrier;?>" id="shipping_carrier"/>
		           
		            </p>
		             <p class="form-field">
		                <label><?php _e('Request Service Level:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="request_service_level" value="<?php echo $request_service_level;?>" id="request_service_level"/>
		            </p>
		            
		             <p class="form-field">
		                <label><?php _e('Tracking Number:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="tracking_number" value="<?php echo $tracking_number;?>" id="ced_jet_tracking_number" />
		            </p>
		            
		             <p class="form-field">
		                <label><?php _e('Ship To Date:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="ship_to_date" value="<?php echo $ship_to_date;?>" id="ship_to_date"/>
			                
		            </p>
		            
		             <p class="form-field">
		                <label><?php _e('Expected Delivery Date:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="exp_delv_date" value="<?php echo $exp_delv_date;?>" id="exp_delv_date"/>
		            </p>
		             <p class="form-field">
		                <label><?php _e('Carrier Pickup Date:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="carrier_pickup_date" value="<?php echo $carrier_pickup_date;?>" id="carrier_pickup_date"/>
		            </p>
		            
		     
		         </div>
		           <table cellspacing="0" cellpadding="0" class="woocommerce_order_items">
				<thead>
					<tr>
						<th class="line_cost sortable"><?php _e('Sku','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Qty Order','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Requested qty Cancelled','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Qty shipped','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('qty Cancelled','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('qty remains','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Return Address','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('RMA Number','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Day To Return','woocommerce-jet-integration');?></th>
						<th cass="line_cost sortable"><?php _e('Shipment Id','woocommerce-jet-integration');?></th>
						
					</tr>
				</thead>
				<tbody id="jet_order_line_items">
				<?php  
					global $wpdb;
		            $table_name = $wpdb->prefix.'jet_order_detail';
		            $qry = "SELECT * from `$table_name` where `woocommerce_order_id` = '$order_id' ;";
		            $resultdata = $wpdb->get_results($qry);
		           
		            $serialize_data 	= json_decode($resultdata[0]->order_all_item);
		            
		            $merchant_order_id 	= $serialize_data->merchant_order_id;
		            	
		            $items_data		= $serialize_data->order_items;
		            
		            foreach($items_data as $k => $valdata){

			            $sku 			= 	$valdata->merchant_sku;
			            $unq_id 		= 	$sku.'A'.$order_id;
			            
			            $order_qty 		= 	$valdata->request_order_quantity;
			            $cancel_qty 	= 	$valdata->request_order_cancel_qty;
						$rma_number		=	$unq_id.'-'.$unq_id;
			            $shipment_id	=	$sku.$order_id.'-'.$unq_id;
			            ?>
					<tr id="<?php echo $unq_id;?>">
					
						<td class="line_cost sortable">
							 <input type="text" size="50" name="sku<?php echo $unq_id?>" value="<?php echo $sku?>" id="sku<?php echo $unq_id?>" class="item_sku" readonly/>
						</td>
						
						<td  class="line_cost sortable">
						 <input type="text" size="50" name="qty_order<?php echo $unq_id?>" value="<?php echo $order_qty?>" id="qty_order<?php echo $unq_id?>" class="item_qty_order" readonly/>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_cancel<?php echo $unq_id?>" value="<?php echo $cancel_qty?>" id="qty_cancel<?php echo $unq_id?>" class="item_qty_cancel" readonly/>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_shipped<?php echo $unq_id?>" value="<?php echo $order_qty?>" id="qty_shipped<?php echo $unq_id?>" class="item_qty_shipped" />
							 <p class="qty_shipped_notification" style="display:none; border: 1px solid red"><?php _e('can not ship more than the requested products','woocommerce-jet-integration'); ?></p>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_cancelled<?php echo $unq_id?>" value=0 id="qty_cancelled<?php echo $unq_id?>" class="item_qty_cancelled" />
							 <p class="qty_shipped_notification" style="display:none; border: 1px solid red"><?php _e('can not ship more than the requested products','woocommerce-jet-integration'); ?></p>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_remains<?php echo $unq_id?>" value="<?php echo $order_qty?>" id="qty_remains<?php echo $unq_id?>" class="item_qty_remains" readonly />
						</td>
						<td  class="line_cost sortable">
							 <select name="return_addr<?php echo $unq_id?>" value="" id="return_addr<?php echo $unq_id?>" >
							 	<option value="1"><?php _e('Yes','woocommerce-jet-integration');?></option>
							 	<option value="0"><?php _e('NO','woocommerce-jet-integration');?></option>
							 </select>
						</td>
						<td  class="line_cost sortable">
							 <input type="text" size="50" name="rma_number<?php echo $unq_id?>" value="<?php echo $rma_number?>" id="rma_number<?php echo $unq_id?>" class="item_rma_number" readonly/>
						</td>
						<td class="line_cost sortable">
							 <input type="number" min="6" max="30" step="1" name="day_to_return<?php echo $unq_id?>"  id="day_to_return<?php echo $unq_id?>" class="item_day_to_return"/>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="shipment_id<?php echo $unq_id?>" value="<?php echo $shipment_id;?>" id="shipment_id<?php echo $unq_id?>" class="item_shipment_id" readonly/>
						</td>
						  
				</tr>
				<?php } ?>
			</tbody>	
			</table>
		        </div>  
		      <?php }
		      else if($order_status_jet	==	'completed'){ 

				global $wpdb;
				$table_name = $wpdb->prefix.'jet_order_detail';
				$qry = "SELECT * from `$table_name` where `woocommerce_order_id` = '$order_id' ;";
				$resultdata = $wpdb->get_results($qry);
 
				$serialize_data   = json_decode($resultdata[0]->shipment_data);
				$tracking_number  = $serialize_data->shipments['0']->shipment_tracking_number;
 
				$items_data		= $serialize_data->shipments['0']->shipment_items;
		      		?>
		          <p class="form-field">
		                <label><?php _e('Order For Fullfillment Node:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="fullfillment_order_node" value="<?php echo $fullfillment_nodeorder;?>" id="fullfillment_order_node" readonly/>
		            </p>
		            
		            <p class="form-field">
		                <label><?php _e('Shipping Carrier Used:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="shipping_carrier" value="<?php echo $shipping_carrier;?>" id="shipping_carrier" readonly/>
		            </p>
		             <p class="form-field">
		                <label><?php _e('Request Service Level:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="request_service_level" value="<?php echo $request_service_level;?>" id="request_service_level" readonly/>
		            </p>
		            
		             <p class="form-field">
		                <label><?php _e('Tracking Number:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="tracking_number" value="<?php echo $tracking_number;?>" id="ced_jet_tracking_number" />
		            </p>
		            
		             <p class="form-field">
		                <label><?php _e('Ship To Date:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="ship_to_date" value="<?php echo $ship_to_date;?>" id="ship_to_date" />
		            </p>
		            
		             <p class="form-field">
		                <label><?php _e('Expected Delivery Date:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="exp_delv_date" value="<?php echo $exp_delv_date;?>" id="exp_delv_date" />
		            </p>
		             <p class="form-field">
		                <label><?php _e('Carrier Pickup Date:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="carrier_pickup_date" value="<?php echo $carrier_pickup_date;?>" id="carrier_pickup_date"  />
		            </p>
		          <div class="options_group custom_tab_options">
		           <table cellspacing="0" cellpadding="0" class="woocommerce_order_items">
				<thead>
					<tr>
						<th class="line_cost sortable"><?php _e('Sku','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Qty Order','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Qty Canceled','woocommerce-jet-integration');?></th>

						<th class="line_cost sortable"><?php _e('Qty shipped','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('qty Cancelled','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('qty remains','woocommerce-jet-integration');?></th>

						<th class="line_cost sortable"><?php _e('Return Address','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('RMA Number','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Day To Return','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Shipment Id','woocommerce-jet-integration');?></th>
					</tr>
				</thead>
				<tbody id="jet_order_line_items">
				<?php  
				
		         // echo '<pre>'; print_r($items_data);die;
		            if(!empty($items_data)) :
		            foreach($items_data as $k => $valdata){

			            $sku 			= 	$valdata->merchant_sku;
			            $unq_id 		= 	$sku.'A'.$order_id;
			            $shipment_id	=	$valdata->shipment_item_id;

			            $order_qty 		= 	get_post_meta($order_id.$sku,'reqqtyShipped', true);
			            $cancel_qty     = 	get_post_meta($order_id.$sku,'reqqtyCancelled', true);

			            $shippedQty 	= 	get_post_meta($order_id.$sku,'qtyShipped', true);//dinesh change $valdata->response_shipment_sku_quantity;
			            $cancelledQty   = 	get_post_meta($order_id.$sku,'qtyCancelled', true);// $valdata->response_shipment_cancel_qty;dinesh change
			           // print_r($order_qty); print_r($shippedQty); print_r($cancelledQty); die;
			        
			            $rma_num = 'rma_'.$order_id.'_'.$sku;
			            $day_to_ret = 'day_to_return_'.$order_id.'_'.$sku;
			            $return_addr = 'return_address_'.$order_id.'_'.$sku;
			            
			           $rma_number = get_post_meta($order_id,$rma_num,true);
			           
			           
			           
			            $qtyRemains = $order_qty - ( $shippedQty + $cancelledQty);
			            $condtnlRead = '';

			            if($qtyRemains <=0)
			            	$condtnlRead = readonly;

			            $day_to_return  =    get_post_meta($order_id,$day_to_ret,true);
			            //$valdata->days_to_return; 
			            
			            $return_location =  get_post_meta($order_id,$return_addr,true);//$valdata->return_location;
			            if(isset($day_to_return) && isset($return_location) && $day_to_return > 0){
			            	$selected_val = 'yes';
			            }
			            else{
			            	$day_to_return = 0;
			            	$selected_val = 'No';
			            }
			            ?>
					<tr id="<?php echo $unq_id;?>">
					
						<td class="line_cost sortable">
							 <input type="text" size="50" name="sku<?php echo $unq_id?>" value="<?php echo $sku?>" id="sku<?php echo $unq_id?>" class="item_sku" readonly/>
						</td>
						
						<td  class="line_cost sortable">
						 <input type="text" size="50" name="qty_order<?php echo $unq_id?>" value="<?php echo $order_qty?>" id="qty_order<?php echo $unq_id?>" class="item_qty_order" readonly/>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_cancel<?php echo $unq_id?>" value="<?php echo $cancel_qty?>" id="qty_cancel<?php echo $unq_id?>" class="item_qty_cancel" readonly/>
						</td>

						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_shipped<?php echo $unq_id?>" value="<?php echo $shippedQty?>" id="qty_shipped<?php echo $unq_id?>" class="item_qty_shipped" <?php echo $condtnlRead?>/>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_cancelled<?php echo $unq_id?>" value="<?php echo $cancelledQty; ?> " id="qty_cancelled<?php echo $unq_id?>" class="item_qty_cancelled" <?php echo $condtnlRead?>/>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_remains<?php echo $unq_id?>" value="<?php echo $qtyRemains?>" id="qty_remains<?php echo $unq_id?>" class="item_qty_remains" readonly />
						</td>

						<td  class="line_cost sortable">
							 <select name="return_addr<?php echo $unq_id?>" value="" id="return_addr<?php echo $unq_id?>" >
							 	<option value="<?php echo $selected_val;?>"><?php _e($selected_val,'woocommerce-jet-integration');?></option>
							 </select>
						</td>
						<td  class="line_cost sortable">
							 <input type="text" size="50" name="rma_number<?php echo $unq_id?>" value="<?php echo $rma_number?>" id="rma_number<?php echo $unq_id?>" class="item_rma_number" readonly/>
						</td>
						<td class="line_cost sortable">
							 <input type="number" min="6" max="30" step="1"  name="day_to_return<?php echo $unq_id?>"  id="day_to_return<?php echo $unq_id?>" class="item_day_to_return" />
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="shipment_id<?php echo $unq_id?>" value="<?php echo $shipment_id;?>" id="shipment_id<?php echo $unq_id?>" class="item_shipment_id" readonly/>
						</td>
						<input type="hidden" name="shipStatus<?php echo $unq_id?>" value="<?php echo $qtyRemains ?>" id="shipStatus<?php echo $unq_id?>">
						  
				</tr>
				<?php } endif;// end for loop?>
			</tbody>	
			</table> 
			</div>	
				<?php 
		
		      		
		      }
		      else if($order_status_jet	==	'processing'){ 
				global $wpdb;
				$table_name = $wpdb->prefix.'jet_order_detail';
				$qry = "SELECT * from `$table_name` where `woocommerce_order_id` = '$order_id' ;";
				$resultdata = $wpdb->get_results($qry);
 
				$serialize_data   = json_decode($resultdata[0]->shipment_data);
				$tracking_number  = $serialize_data->shipments['0']->shipment_tracking_number;
				$items_data		= $serialize_data->shipments['0']->shipment_items;?>


		      	<p class="form-field">
		                    <input type="hidden"  name="order_action" value="<?php echo $order_action;?>" id="order_action_txt"/>
		                    <input type="hidden"  name="order_type_jet" value="<?php echo $order_type;?>" id="order_action_txt"/>
		            </p>
				    <!-- field first -->
				    <p class="form-field">
		                <label><?php _e('Select Order Action:', 'woocommerce-jet-integration'); ?></label>
			            <!--  <select id="selected_order_action" name="selected_order_action">
			             	<option value="order_ship"><?php _e('Ship','woocommerce-jet-integration')?></option>
			             </select>
			                <input type="button"  class="button primary" name="order_action" value="Submit action" data-order_id = "<?php echo $order_id;?>" id="submit_order_action"/>
			               !-->
			          <input type="button"  class="button primary submit_order_action" name="order_actions" value="Ship Order" data-action_id='order_ship' data-order_id = "<?php echo $order_id;?>" id="submit_order_actionss"/>      
		            </p>
				<!-- field first -->
				    <p class="form-field">
		                <label><?php _e('Order For Fullfillment Node:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="fullfillment_order_node" value="<?php echo $fullfillment_nodeorder;?>" id="fullfillment_order_node" readonly/>
		            </p>
		            
		            <p class="form-field">
		                <label><?php _e('Shipping Carrier Used:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="shipping_carrier" value="<?php echo $shipping_carrier;?>" id="shipping_carrier" readonly/>
		            </p>
		             <p class="form-field">
		                <label><?php _e('Request Service Level:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="request_service_level" value="<?php echo $request_service_level;?>" id="request_service_level" readonly/>
		            </p>
		            
		             <p class="form-field">
		                <label><?php _e('Tracking Number:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="tracking_number" value="<?php echo $tracking_number;?>" id="ced_jet_tracking_number" />
		            </p>
		            
		             <p class="form-field">
		                <label><?php _e('Ship To Date:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="ship_to_date" value="<?php echo $ship_to_date;?>" id="ship_to_date" />
		            </p>
		            
		             <p class="form-field">
		                <label><?php _e('Expected Delivery Date:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="exp_delv_date" value="<?php echo $exp_delv_date;?>" id="exp_delv_date" />
		            </p>
		             <p class="form-field">
		                <label><?php _e('Carrier Pickup Date:', 'woocommerce-jet-integration'); ?></label>
			                <input type="text"  name="carrier_pickup_date" value="<?php echo $carrier_pickup_date;?>" id="carrier_pickup_date"  />
		            </p>
		          <div class="options_group custom_tab_options">
		           <table cellspacing="0" cellpadding="2" class="woocommerce_order_items">
				<thead>
					<tr>
						<th class="line_cost sortable"><?php _e('Sku','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Qty Order','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Qty Canceled','woocommerce-jet-integration');?></th>

						<th class="line_cost sortable"><?php _e('Qty shipped','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('qty Cancelled','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('qty remains','woocommerce-jet-integration');?></th>

						<th class="line_cost sortable"><?php _e('Return Address','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('RMA Number','woocommerce-jet-integration');?></th>
						<th class="line_cost sortable"><?php _e('Day To Return','woocommerce-jet-integration');?></th>
						<th cass="line_cost sortable"><?php _e('Shipment Id','woocommerce-jet-integration');?></th>
					</tr>
				</thead>
				<tbody id="jet_order_line_items">
				<?php  
				
		            
		            if(!empty($items_data)) :
		            foreach($items_data as $k => $valdata){

			            $sku 			= 	$valdata->merchant_sku;
			            $unq_id 		= 	$sku.'A'.$order_id;
			            
			          //  $order_qty 		= 	$valdata->response_shipment_sku_quantity;
			          //  $cancel_qty 	= 	$valdata->response_shipment_cancel_qty;
			            $rma_number		=	$valdata->RMA_number;
			            $shipment_id	=	$valdata->shipment_item_id;

			            $order_qty 		= 	get_post_meta($order_id.$sku,'reqqtyShipped', true);
			            $cancel_qty     = 	get_post_meta($order_id.$sku,'reqqtyCancelled', true);

			            $shippedQty 	= 	get_post_meta($order_id.$sku,'qtyShipped', true);//$valdata->response_shipment_sku_quantity; dinesh change
			            $cancelledQty   = 	get_post_meta($order_id.$sku,'qtyCancelled', true);//$valdata->response_shipment_cancel_qty; dinesh change
			           
			           
			             
			            $qtyRemains = $order_qty - ( $shippedQty + $cancelledQty);
			            $condtnlRead = '';

			            if($qtyRemains <=0)
			            	$condtnlRead = readonly;

			            $day_to_return  =   $valdata->days_to_return; 
			            
			            $return_location = $valdata->return_location;
			            if(isset($day_to_return) && isset($return_location) && $day_to_return > 0){
			            	$selected_val = 'yes';
			            }
			            else{
			            	$day_to_return = 0;
			            	$selected_val = 'No';
			            }
			            ?>
					<tr id="<?php echo $unq_id;?>">
					
						<td class="line_cost sortable">
							 <input type="text" size="50" name="sku<?php echo $unq_id?>" value="<?php echo $sku?>" id="sku<?php echo $unq_id?>" class="item_sku" readonly/>
						</td>
						
						<td  class="line_cost sortable">
						 <input type="text" size="50" name="qty_order<?php echo $unq_id?>" value="<?php echo $order_qty?>" id="qty_order<?php echo $unq_id?>" class="item_qty_order" readonly/>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_cancel<?php echo $unq_id?>" value="<?php echo $cancel_qty?>" id="qty_cancel<?php echo $unq_id?>" class="item_qty_cancel" readonly/>
						</td>

						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_shipped<?php echo $unq_id?>" value="<?php echo $shippedQty?>" id="qty_shipped<?php echo $unq_id?>" class="item_qty_shipped" <?php echo $condtnlRead?>/>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_cancelled<?php echo $unq_id?>" value="<?php echo $cancelledQty;?>" id="qty_cancelled<?php echo $unq_id?>" class="item_qty_cancelled" <?php echo $condtnlRead?>/>
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="qty_remains<?php echo $unq_id?>" value="<?php echo $qtyRemains?>" id="qty_remains<?php echo $unq_id?>" class="item_qty_remains" readonly />
						</td>

						<td  class="line_cost sortable">
							 <select name="return_addr<?php echo $unq_id?>" value="" id="return_addr<?php echo $unq_id?>" >
							 	<option value="<?php echo $selected_val;?>"><?php _e($selected_val,'woocommerce-jet-integration');?></option>
							 </select>
						</td>
						<td  class="line_cost sortable">
							 <input type="text" size="50" name="rma_number<?php echo $unq_id?>" value="<?php echo $rma_number?>" id="rma_number<?php echo $unq_id?>" class="item_rma_number" readonly/>
						</td>
						<td class="line_cost sortable">
							 <input type="number"  min="6" max="30" step="1" name="day_to_return<?php echo $unq_id?>"  id="day_to_return<?php echo $unq_id?>" class="item_day_to_return" />
						</td>
						<td  class="line_cost sortable">
							 <input type="text"  size="50" name="shipment_id<?php echo $unq_id?>" value="<?php echo $shipment_id;?>" id="shipment_id<?php echo $unq_id?>" class="item_shipment_id" readonly/>
						</td>
						<input type="hidden" name="shipStatus<?php echo $unq_id?>" value="<?php echo $qtyRemains?>" id="shipStatus<?php echo $unq_id?>">
						  
				</tr>
				<?php } endif;// end for loop?>
			</tbody>	
			</table> 
			</div>	
				<?php 
		}
			} else{?>
		       <div id="not_jet_order_settings" class="panel woocommerce_options_panel">
		        <div class="options_group">
			        <p class="form-field">
		               <h3 style="text-align:center;"><b><?php  _e('Not Jet Order ', 'woocommerce-jet-integration'); ?></b></h3>
		            </p>
		        </div>
		        </div>
		      <?php }   
			}

	public  function saveOrderMetaBox($post_id){
		
		$woo_order_id = $post_id;
		
		if(isset($_POST['tracking_number']) && isset($_POST['shipping_carrier']) && isset($_POST['ship_to_date']) && isset($_POST['carrier_pickup_date']) && isset($_POST['request_service_level']) ){
		$shipping_carrier 		= 	$_POST['shipping_carrier'];
		$tracking_number		= 	$_POST['tracking_number'];
		$ship_to_date			= 	$_POST['ship_to_date'];
		$exp_delv_date			=	$_POST['exp_delv_date'];
		$carrier_pickup_date	=	$_POST['carrier_pickup_date'];
		$request_service_level	= 	$_POST['request_service_level'];
		
		$order_type				=   $_POST['order_type_jet'];
		
		
		update_post_meta($woo_order_id,'shipping_carrier',$shipping_carrier);
		update_post_meta($woo_order_id,'tracking_number',$tracking_number);
		update_post_meta($woo_order_id,'ship_to_date',$ship_to_date);
		update_post_meta($woo_order_id,'exp_delv_date',$exp_delv_date);
		update_post_meta($woo_order_id,'carrier_pickup_date',$carrier_pickup_date);
		update_post_meta($woo_order_id,'request_service_level',$request_service_level);
		
		update_post_meta($woo_order_id,'order_type_jet',$order_type);
		}
	}

	public function addProfileRelatedMetaBox($postType){

		$postTypes = array('product');
		global $post;
    	$product = get_product( $post->ID );
    	if ( in_array( $postType, $postTypes )) {
	        add_meta_box(
	            'assignedProfileDetail'
	            ,__( 'Jet Profile Details', 'woocommerce-jet-integration' )
	            ,array( $this, 'profileMetaBoxContent' )
	            ,$postType
	            ,'side'
	            ,'low'
	        );
    	}
	}

	public function profileMetaBoxContent(){

		global $post;
		$productID 		= 	$post->ID;
		$profileID 		=	get_post_meta($productID, 'productProfileID', true);
		$profileDetail 	= 	$this->modelAction->getProfileDetail($profileID);

		
		if(is_array($profileDetail) && !empty($profileDetail)){

			$profiledata		= 	$profileDetail[0];
				
			$name 				= 	$profiledata->profile_name;
			$categoryAttrdata	= 	json_decode($profiledata->profile_category);
			$categoryAttrdata	=	(array)$categoryAttrdata;
			
			$all_item_specific  = 	json_decode($profiledata->item_specific);
				
			$item_specific 		= 	$all_item_specific->item_specific;

			
			$mappedStandardCode =	$item_specific->skuMappedWith;
			$brand 				= 	$item_specific->brand;
			$country_manuf		= 	$item_specific->country_manufac;
			//echo $country_manuf;die;
			$safety_warning 	= 	$item_specific->safety_warning;
			$fullfillment		= 	$item_specific->fullfillment_time;
			$map_price 			= 	$item_specific->map_price;
			$legal_desc			= 	$item_specific->legal_desc;
			$tax_code			= 	$item_specific->tax_code;
			$manuf_ret_price	= 	$item_specific->manufac_retail_price;
			$map_implem			= 	$item_specific->map_implementation;
			
			$tax_code_val 		= 	$item_specific->product_tax_code;
			$ship_alone_val 	= 	$item_specific->ship_alone;
			$prop65_val			= 	$item_specific->prop65;
			$package_length		= 	$item_specific->package_length;
			$package_weight		= 	$item_specific->package_width;
			$package_height		= 	$item_specific->package_height;
			$cpsia_statement	= 	$item_specific->cpsia_statement;?>

			<p><center><?php echo $name; ?></center></p>
			<table border="1px solid" width="100%">
				<tr>
					<td><?php _e('Brand','woocommerce-jet-integration'); ?></td>
					<td><?php echo $brand; ?></td>
				</tr>
				<tr>
					<td><?php _e('Country Manufacturer','woocommerce-jet-integration'); ?></td>
					<td><?php echo $country_manuf; ?></td>
				</tr>
				<tr>
					<td><?php _e('Safety Warning','woocommerce-jet-integration'); ?></td>
					<td><?php echo $safety_warning; ?></td>
				</tr>
				<tr>
					<td><?php _e('fullfillment Days','woocommerce-jet-integration'); ?></td>
					<td><?php echo $fullfillment; ?></td>
				</tr>
				<tr>
					<td><?php _e('Map Price','woocommerce-jet-integration'); ?></td>
					<td><?php echo $map_price; ?></td>
				</tr>
				<tr>
					<td><?php _e('Legal Disclaimer','woocommerce-jet-integration'); ?></td>
					<td><?php echo $legal_desc; ?></td>
				</tr>
				<tr>
					<td><?php _e('Package length','woocommerce-jet-integration'); ?></td>
					<td><?php echo $package_length; ?></td>
				</tr>
				<tr>
					<td><?php _e('package width','woocommerce-jet-integration'); ?></td>
					<td><?php echo $package_weight; ?></td>
				</tr>
				<tr>
					<td><?php _e('package height','woocommerce-jet-integration'); ?></td>
					<td><?php echo $package_height; ?></td>
				</tr>
			</table>

		<?php }else{

			_e('please select profile for this product','woocommerce-jet-integration');
		}
	}
}