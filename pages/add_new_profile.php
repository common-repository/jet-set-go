<?php if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}



require_once CEDJET_DIRPATH.'/includes/class-cedJetLibraryFunctions.php';

if(isset($_POST['add_meta_keys'])){
	
	if(isset($_POST['unique_post']) && count($_POST['unique_post']) > 0){
		
		$get_fields 	=	 array();
		$metaKeysToAdd 	=	 $_POST['unique_post']; 
		$get_fields 	= 	 get_option('jet_metaKeys_for_profile',true);
		
		if(empty($get_fields))
			$all_meta 		= 	 $metaKeysToAdd;
		else
			$all_meta 		= 	 array_merge($metaKeysToAdd,$get_fields);
		
		
		$all_meta 		= 	 array_unique($all_meta);
		update_option('jet_metaKeys_for_profile', $all_meta);
	}
}

if(isset($_POST['save'])){
	
	$profile_name 		= 	$_POST['jet_profile_name'];
	$category     		= 	$_POST['Prodile_cat'];
	$item_specific		=	array();

	$taxonomy     = 'product_cat';
	$orderby      = 'name';
	$empty        = 0;
	
	$args = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => $orderby,
			'hide_empty'   => $empty
	);
	$all_categories = get_categories( $args );
	
	$jetSelectedNodeID	=	array();
	
	foreach($all_categories as $category):
	
		$jetSelectedNodeID[$category->term_id]	=	$category->category_nicename;
	endforeach;
	
	$mappedCategories	=	get_option('cedWooJetMapping',false);
	
	if(!empty($mappedCategories)){
		
		$catBasedAttr	=	array();
		
		foreach($mappedCategories as $mappedwoocat	=> $mappedjetcat){
	
			$mappedAttributes		=	get_option($mappedjetcat.'_linkedAttributes',false);
	
			if($mappedAttributes){
	
				$mappedAttributes	=	json_decode($mappedAttributes);
				$jetAttrInfo	=	array();
				if(is_array($mappedAttributes)){
	
					$allAttrInfo	=	array();
	
					foreach($mappedAttributes as $jetAttrID){
	
						global $wpdb;
						$table_name 	= 	$wpdb->prefix.'jet_attributes_table';
						$qry			= 	"SELECT * FROM `$table_name` WHERE `jet_attr_id`=$jetAttrID;";
						$attrInfo		= 	$wpdb->get_results($qry);
						$allAttrInfo[]	=	$attrInfo;
					}
	
					$jetAttrInfo[$mappedjetcat]	=	$allAttrInfo;
				}
			}
			if(!empty($jetAttrInfo) && $jetAttrInfo != ''):
				foreach($jetAttrInfo as $jetNode => $mappedCAT):
					foreach($mappedCAT as $attrARRAY):
						$attrObject = $attrARRAY[0];
						$tempName	=	$jetNode."_".$attrObject->jet_attr_id;
						if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit)) ){
							$catBasedAttr[$tempName]					=	$_POST[$tempName];
							$catBasedAttr[$tempName.'_unit']				=	$_POST[$tempName.'_unit'];
							$catBasedAttr[$tempName.'_attributeMeta']		=	$_POST[$tempName.'_attributeMeta'];
						}
						else{
							$catBasedAttr[$tempName]					=	$_POST[$tempName];
							$catBasedAttr[$tempName.'_attributeMeta']	=	$_POST[$tempName.'_attributeMeta'];
						}
					endforeach;
				endforeach;
			endif;
		}
	}
	$catBasedAttr	=	json_encode($catBasedAttr);
	
	//item specific settings
	$mappedStandardCode			=	$_POST['skuMappedWith'];
	
	$brandIndex					=	empty($_POST['brand']) ? empty($_POST['jetbrand_attributeMeta']) ? null: 'jetbrand_attributeMeta' : 'brand';
	$brandValue					=	empty($_POST['brand']) ? empty($_POST['jetbrand_attributeMeta']) ? null:$_POST['jetbrand_attributeMeta'] :$_POST['brand'];
	
	$cmIndex					=	empty($_POST['country_manufac']) ? empty($_POST['cm_attributeMeta']) ? null: 'cm_attributeMeta' : 'country_manufac';
	$cmValue					=	empty($_POST['country_manufac']) ? empty($_POST['cm_attributeMeta']) ? null:$_POST['cm_attributeMeta'] :$_POST['country_manufac'];
	
	$sfIndex					=	empty($_POST['safety_warning']) ? empty($_POST['sw_attributeMeta']) ? null: 'sw_attributeMeta' : 'safety_warning';
	$sfValue					=	empty($_POST['safety_warning']) ? empty($_POST['sw_attributeMeta']) ? null:$_POST['sw_attributeMeta'] :$_POST['safety_warning'];
	
	$ftIndex					=	empty($_POST['fullfillment_time']) ? empty($_POST['ft_attributeMeta']) ? null: 'ft_attributeMeta' : 'fullfillment_time';
	$ftValue					=	empty($_POST['fullfillment_time']) ? empty($_POST['ft_attributeMeta']) ? null:$_POST['ft_attributeMeta'] :$_POST['fullfillment_time'];
	
	$mpIndex					=	empty($_POST['map_price']) ? empty($_POST['mp_attributeMeta']) ? null: 'mp_attributeMeta' : 'map_price';
	$mpValue					=	empty($_POST['map_price']) ? empty($_POST['mp_attributeMeta']) ? null:$_POST['mp_attributeMeta'] :$_POST['map_price'];
	
	$plIndex					=	empty($_POST['package_length']) ? empty($_POST['pl_attributeMeta']) ? null: 'pl_attributeMeta' : 'package_length';
	$plValue					=	empty($_POST['package_length']) ? empty($_POST['pl_attributeMeta']) ? null:$_POST['pl_attributeMeta'] :$_POST['package_length'];
	
	$pwIndex					=	empty($_POST['package_width']) ? empty($_POST['pw_attributeMeta']) ? null: 'pw_attributeMeta' : 'package_width';
	$pwValue					=	empty($_POST['package_width']) ? empty($_POST['pw_attributeMeta']) ? null:$_POST['pw_attributeMeta'] :$_POST['package_width'];
	
	$phIndex					=	empty($_POST['package_height']) ? empty($_POST['ph_attributeMeta']) ? null: 'ph_attributeMeta' : 'package_height';
	$phValue					=	empty($_POST['package_height']) ? empty($_POST['ph_attributeMeta']) ? null:$_POST['ph_attributeMeta'] :$_POST['package_height'];
	
	$mrIndex					=	empty($_POST['manufacturer_retail_price']) ? empty($_POST['mr_attributeMeta']) ? null: 'mr_attributeMeta' : 'manufacturer_retail_price';
	$mrValue					=	empty($_POST['manufacturer_retail_price']) ? empty($_POST['mr_attributeMeta']) ? null:	$_POST['mr_attributeMeta'] :$_POST['manufacturer_retail_price'];
	
	$legal_desc					=	$_POST['legal_description'];
	$map_implem					=	$_POST['map_implementation'];
	
	$otherselectedCode			=	$_POST['other_standard_code'];
	$otherselectedCodeValue		=	$_POST['otherCode_attributeMeta'];
	
	$tax_code_val				=	$_POST['product_tax_code'];
	$ship_alone_val				=	$_POST['ship_alone'];
	$prop65_val					=	$_POST['prop65'];

	$cpsia_statement			=	$_POST['cpsia_cautionary_statements'];
	$bullets					=	array();
	$bullets['bullets1']		=	$_POST['bullet_1'];
	$bullets['bullets2']		=	$_POST['bullet_2'];
	$bullets['bullets3']		=	$_POST['bullet_3'];
	$bullets['bullets4']		=	$_POST['bullet_4'];
	$bullets['bullets5']		=	$_POST['bullet_5'];
	
	//print_r($jetbackorder);die;
	
	if(empty($profile_name)){
		$_SESSION['profile_error'][] = 'Please Select Profile Name';
	}elseif(empty($category)){
			$_SESSION['profile_error'][] = 'Please Select Profile Category';
	}else{
		global $wpdb;
		$table_name = $wpdb->prefix.'jet_profile_settings';
		
		if(!empty($otherselectedCode) && $otherselectedCode != 'none'){
			
			$item_specific['item_specific']['otherSelectedCode']		=	$otherselectedCode;
			$item_specific['item_specific']['otherSelectedCodeValue']	=	$otherselectedCodeValue;
		}
		
		$item_specific['item_specific']['skuMappedWith']			=	$mappedStandardCode;
		
		$item_specific['item_specific'][$brandIndex]				=	$brandValue;
		$item_specific['item_specific'][$cmIndex]					=	$cmValue;
		$item_specific['item_specific'][$sfIndex]					=	$sfValue;
		$item_specific['item_specific'][$ftIndex]					=	$ftValue;
		$item_specific['item_specific'][$mpIndex]					=	$mpValue;
		$item_specific['item_specific']['legal_desc']				=	$legal_desc;
		$item_specific['item_specific']['tax_code']					=	$tax_code;
		$item_specific['item_specific'][$mrIndex]					=	$mrValue;
		$item_specific['item_specific']['map_implementation']		=	$map_implem;

		$item_specific['item_specific']['brand_attribute_meta']		= 	$brand_attribute_meta;
		$item_specific['item_specific']['product_tax_code']			= 	$tax_code_val;			
		$item_specific['item_specific']['ship_alone']				= 	$ship_alone_val;			
		$item_specific['item_specific']['prop65']					= 	$prop65_val;				
		$item_specific['item_specific'][$plIndex]					= 	$plValue;			
		$item_specific['item_specific'][$pwIndex]					= 	$pwValue;			
		$item_specific['item_specific'][$phIndex]					= 	$phValue;			
		$item_specific['item_specific']['cpsia_statement']			= 	$cpsia_statement;		
		$item_specific['item_specific']['bullets']					=	$bullets;
		//print_r($item_specific);die;
		if(isset($_POST['profile_id']) && !empty($_POST['profile_id'])){
			$profile_id = $_POST['profile_id'];
			$item_specific =  json_encode($item_specific);
			$qry = "UPDATE `$table_name` SET `profile_name` = '$profile_name', `profile_category` = '$catBasedAttr', `item_specific` = '$item_specific' where profile_id = $profile_id;";
			//die($qry);
			$wpdb->query($qry);
			$_SESSION['profile_success'] 	= 	'Your Profile Settings Updated successfully';
		}else{
		$_SESSION['profile_success'] 	= 	'Your Profile Settings Save successfully ,Go to profile listing';
		$item_specific = json_encode($item_specific);
		$qry = "INSERT INTO `$table_name` ( `profile_name`, `profile_category`, `item_specific`) VALUES ('".$profile_name."', '".$catBasedAttr."', '".$item_specific."');";
		$wpdb->query($qry);
		$lastid = $wpdb->insert_id;
		$url =  site_url()."/wp-admin/admin.php?page=profile_settings&action=edit&profile=$lastid";
		?>
		<script>
			jQuery(document).ready(function(){
				var url = '<?php echo $url?>'; 
				window.location.href	=	url;
			});
		</script>
		<?php 
		}	
	}
}

if(isset($_GET['action']) && isset($_GET['profile'])){
	
	global $wpdb;
	$profile_id 		= 	$_GET['profile']; 
	$table_name 		= 	$wpdb->prefix.'jet_profile_settings';
	$qry 				= 	"SELECT * FROM `$table_name` where profile_id = $profile_id;";
	$profile_data 		= 	$wpdb->get_results($qry);
	
	
	$profiledata		= 	$profile_data[0];	
	
	$name 				= 	$profiledata->profile_name;
	$categoryAttrdata	= 	json_decode($profiledata->profile_category);
	$categoryAttrdata	=	(array)$categoryAttrdata; 
	$all_item_specific  = 	json_decode($profiledata->item_specific);
	
	$item_specific 		= 	$all_item_specific->item_specific;
	
	$mappedStandardCode =	$item_specific->skuMappedWith;
	//print_r($item_specific);die;
	//brand data
	if(isset($item_specific->brand))
		$brand 			=	 $item_specific->brand;
	
	if(isset($item_specific->jetbrand_attributeMeta))
		$jetbrand_attributeMeta	=	$item_specific->jetbrand_attributeMeta;
	//end
	
	//cm data
	if(isset($item_specific->country_manufac))
		$country_manuf 			=	 $item_specific->country_manufac;
	
	if(isset($item_specific->cm_attributeMeta))
		$cm_attributeMeta	=	$item_specific->cm_attributeMeta;
	//end
	
	//cm data
	if(isset($item_specific->safety_warning))
		$safety_warning 			=	 $item_specific->safety_warning;
	
	if(isset($item_specific->sw_attributeMeta))
		$sw_attributeMeta	=	$item_specific->sw_attributeMeta;
	//end
	
	//cm data
	if(isset($item_specific->fullfillment_time))
		$fullfillment 			=	 $item_specific->fullfillment_time;
	
	if(isset($item_specific->ft_attributeMeta))
		$ft_attributeMeta	=	$item_specific->ft_attributeMeta;
	//end
	
	//cm data
	if(isset($item_specific->map_price))
		$map_price 			=	 $item_specific->map_price;
	
	if(isset($item_specific->mp_attributeMeta))
		$mp_attributeMeta	=	$item_specific->mp_attributeMeta;
	//end
	
	//cm data
	if(isset($item_specific->package_length))
		$package_length 			=	 $item_specific->package_length;
	
	if(isset($item_specific->pl_attributeMeta))
		$pl_attributeMeta	=	$item_specific->pl_attributeMeta;
	//end
	
	//cm data
	if(isset($item_specific->package_width))
		$package_width 			=	 $item_specific->package_width;
	
	if(isset($item_specific->pw_attributeMeta))
		$pw_attributeMeta	=	$item_specific->pw_attributeMeta;
	//end
	
	//cm data
	if(isset($item_specific->package_height))
		$package_height 			=	 $item_specific->package_height;
	
	if(isset($item_specific->ph_attributeMeta))
		$ph_attributeMeta	=	$item_specific->ph_attributeMeta;
	//end
	
	//cm data
	if(isset($item_specific->manufacturer_retail_price))
		$manuf_ret_price 			=	 $item_specific->manufacturer_retail_price;
	
	if(isset($item_specific->mr_attributeMeta))
		$mr_attributeMeta	=	$item_specific->mr_attributeMeta;
	//end
	
//	$brand 				= 	$item_specific->brand;
//	$country_manuf		= 	$item_specific->country_manufacturer;
	
	$otherselectedCode	=	$item_specific->otherSelectedCode;
	$otherselectedCodeValue	=	$item_specific->otherSelectedCodeValue;
	$legal_desc			= 	$item_specific->legal_desc;
	$tax_code			= 	$item_specific->tax_code;
	$map_implem			= 	$item_specific->map_implementation;
	$brand_attribute_meta = $item_specific->brand_attribute_meta;
	
	$tax_code_val 		= 	$item_specific->product_tax_code;
	$ship_alone_val 	= 	$item_specific->ship_alone;
	$prop65_val			= 	$item_specific->prop65;

	$cpsia_statement	= 	$item_specific->cpsia_statement;
	if(isset($item_specific->bullets)){
		//print_r($item_specific->bullets);
		$bullet1 = isset($item_specific->bullets->bullets1)? $item_specific->bullets->bullets1 : '';
		$bullet2 = isset($item_specific->bullets->bullets2)? $item_specific->bullets->bullets2 : '';
		$bullet3 = isset($item_specific->bullets->bullets3)? $item_specific->bullets->bullets3 : '';
		$bullet4 = isset($item_specific->bullets->bullets4)? $item_specific->bullets->bullets4 : '';
		$bullet5 = isset($item_specific->bullets->bullets5)? $item_specific->bullets->bullets5 : '';
		
	}
	if(isset($item_specific->jetbackorder)){
		$jetback_order = $item_specific->jetbackorder;
		
	}
}?>

<div tabindex="0" aria-label="Main content" id="wpbody-content">
<div id="icon-woocommerce" class="icon32 icon32-woocommerce-settings"><br></div>
<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
	<a class="nav-tab  " href="<?php echo site_url();?>/wp-admin/admin.php?page=jet_store_integration"><?php _e('Jet Configuration','woocommerce-jet-integration');?></a>
	<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=manage_jet_attributes"><?php _e('Category Mapping','woocommerce-jet-integration');?></a>
	<a class="nav-tab nav-tab-active" href="<?php echo site_url()?>/wp-admin/admin.php?page=jet_profile_settings"><?php _e('Profile','woocommerce-jet-integration');?></a>
	<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=manage_jet_product"><?php _e('Manage products','woocommerce-jet-integration');?></a>
	<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=jet_orders"><?php _e('Orders','woocommerce-jet-integration');?></a>
	<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=order_return"><?php _e('Return','woocommerce-jet-integration');?></a>
	<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=order_refund"><?php _e('Refund','woocommerce-jet-integration');?></a>		
	<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=upload_error"><?php _e('Upload Error File','woocommerce-jet-integration');?></a>
	<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=mass_cat_assign"><?php _e('Mass Category Assign','woocommerce-jet-integration');?></a>
	
</h2>
<div >
	<?php // unset($_SESSION['upload_product_error']);
	 if(isset($_SESSION['profile_error'])){
			$all_error = $_SESSION['profile_error'];	
			foreach($all_error as $index => $error){
				?><div class="error settings-error notice is-dismissible"><?php echo $error.'<br>';?></div><?php 
			}
		}
		unset($_SESSION['profile_error']);
		?>
			<?php if(isset($_SESSION['profile_success'])){ ?>
				<div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated">
					<?php echo $_SESSION['profile_success'];?></div>
			<?php }
			unset($_SESSION['profile_success']);
			
			$attributes	=	wc_get_attribute_taxonomies();
			
			$attrOptions	=	array();
			$attrOptions[null]	=	__('jet related meta field','woocommerce-jet-integration');
			
			$addedMetaKeys = get_option('jet_metaKeys_for_profile', false);
				
			if($addedMetaKeys && count($addedMetaKeys) > 0){
				
				foreach ($addedMetaKeys as $metaKey){

					$attrOptions[$metaKey]	=	$metaKey;
				}
			}
			if(!empty($attributes)){
				
				foreach($attributes as $attributesObject){
						
					$attrOptions[$attributesObject->attribute_name]	=	$attributesObject->attribute_label;
				}
			}
			?>
		
	</div>	
<br class="clear">
	<div class="wrap jet-page">
		<h2><?php _e('New Profile','woocommerce-jet-integration')?></h2>
			<div style="display:block !important" class="updated" id="message">
				<p><b><?php _e('You Can Create Profile Here and Assign to any Product','woocommerce-jet-integration')?></b></p>
			</div>
	
<form action="" method="post">
<div id="poststuff">
   <div class="metabox-holder columns-2" id="post-body">
   <!-- #postbox-container-2 -->
<div class="postbox-container" id="postbox-container-2">
	<div class="meta-box-sortables ui-sortable">
		<div id="profileGeneralSettings" class="postbox">
			<h3><span><?php _e('General Profile settings','woocommerce-jet-integration')?></span></h3>
			<div class="inside">
				<div style="margin-bottom:5px;" id="profiletitle">
					<div id="titlewrap">
						<label class="text_label"><?php _e('Profile name *','woocommerce-jet-integration')?></label>
							<input type="text" style="width:65%;" autocomplete="off" id="title" value="<?php if(isset($name)){echo $name;}?>" size="30" name="jet_profile_name">
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		<div id="profileExtraSettings" class="postbox">
			<h3><span><?php _e('Standard Code Setting','woocommerce-jet-integration')?></span></h3>
			<div class="inside">
				<div style="margin-bottom:5px;">
					<div>
						<?php woocommerce_wp_select(
									array(
									'id'      => 'skuMappedWith',
									'label'   => __( 'Map Sku With', 'woocommerce-jet-integration' ),
									'description' => __( 'Use Your Sku As Selected Code Type', 'woocommerce-jet-integration' ),
									'value'	=>	isset($mappedStandardCode)?$mappedStandardCode:'',
									'options' => array(
									'choose'   => __( 'Select Code Type', 'woocommerce-jet-integration' ),
									'ASIN'   => __( 'ASIN', 'woocommerce-jet-integration' ),
									'UPC'   => __( 'UPC', 'woocommerce-jet-integration' ),
									'UPC-E'   => __( 'UPC-E', 'woocommerce-jet-integration' ),
									'GTIN-14'   => __( 'GTIN-14', 'woocommerce-jet-integration' ),
									'ISBN-13'   => __( 'ISBN-13', 'woocommerce-jet-integration' ),
									'ISBN-10'   => __( 'ISBN-10', 'woocommerce-jet-integration' ),
									'GTIN-14'   => __( 'GTIN-14', 'woocommerce-jet-integration' ),
									'EAN'   => __( 'EAN', 'woocommerce-jet-integration' ),
									'mfr_part_number'	=> __( 'MFR Part Number', 'woocommerce-jet-integration' ),
									)
									)
									);?>
					</div>
					<div >
						<?php  woocommerce_wp_select(
									array(
									'id'      => 'other_standard_code',
									'label'   => __( 'Standard Code Type', 'woocommerce-jet-integration' ),
									'value'	=>	isset($otherselectedCode)?$otherselectedCode:'',
									'options' => array(
									'choose'   => __( 'Select Code Type', 'woocommerce-jet-integration' ),
									'ASIN'   => __( 'ASIN', 'woocommerce-jet-integration' ),
									'UPC'   => __( 'UPC', 'woocommerce-jet-integration' ),
									'UPC-E'   => __( 'UPC-E', 'woocommerce-jet-integration' ),
									'GTIN-14'   => __( 'GTIN-14', 'woocommerce-jet-integration' ),
									'ISBN-13'   => __( 'ISBN-13', 'woocommerce-jet-integration' ),
									'ISBN-10'   => __( 'ISBN-10', 'woocommerce-jet-integration' ),
									'GTIN-14'   => __( 'GTIN-14', 'woocommerce-jet-integration' ),
									'EAN'   => __( 'EAN', 'woocommerce-jet-integration' ),
									)
									)
									); 
						  woocommerce_wp_select(
										array(
										'id'      => 'otherCode_attributeMeta',
										'label'   => __( 'Standard Code Value Map From :', 'woocommerce-jet-integration' ),
										'value'   => isset($otherselectedCodeValue)?$otherselectedCodeValue:'',
										'options' => $attrOptions,
										)
										); ?>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		<div id="category_map_with_custom" class="postbox">
			<h3><span><?php _e('Category Attributes','woocommerce-jet-integration')?></span></h3>
			<?php 
			$taxonomy     = 'product_cat';
			$orderby      = 'name';
			$empty        = 0;
			
			$args = array(
					'taxonomy'     => $taxonomy,
					'orderby'      => $orderby,
					'hide_empty'   => $empty
			);
			$all_categories = get_categories( $args );
			
			$jetSelectedNodeID	=	array();
			
			foreach($all_categories as $category):
				$jetSelectedNodeID[$category->term_id]	=	$category->category_nicename;
			endforeach;
			
			$mappedCategories	=	get_option('cedWooJetMapping',false);
			
			if(!empty($mappedCategories)){
	
				foreach($mappedCategories as $mappedwoocat	=> $mappedjetcat){
					
					$mappedAttributes		=	get_option($mappedjetcat.'_linkedAttributes',false);
		
					if($mappedAttributes){
					
						$mappedAttributes	=	json_decode($mappedAttributes);
						$jetAttrInfo	=	array();
						if(is_array($mappedAttributes)){
					
							$allAttrInfo	=	array();
							
							foreach($mappedAttributes as $jetAttrID){
							
								global $wpdb;
								$table_name 	= 	$wpdb->prefix.'jet_attributes_table';
								$qry			= 	"SELECT * FROM `$table_name` WHERE `jet_attr_id`=$jetAttrID;";
								$attrInfo		= 	$wpdb->get_results($qry);
								$allAttrInfo[]	=	$attrInfo;
							}
							
							$jetAttrInfo[$mappedjetcat]	=	$allAttrInfo;
						}
					}

					if(!empty($jetAttrInfo) && isset($jetAttrInfo)):
						
						foreach($jetAttrInfo as $jetNode => $mappedCAT):
						$wooCatID 	=	array_search($jetNode, $jetSelectedNodeID);
						$term	=	get_term_by('id', $mappedwoocat, 'product_cat');
						
						$mappedWooCatName	=	'';
						
						if(isset($term)){
						
							$mappedWooCatName	=	$term->name;
						    		}?>
						    	
						    	<div class="options_group" style="margin: 10px;" >
						    		<p><?php _e($mappedWooCatName." JET Attributes",'wocommerce-jet-integration');?>
						    			<img class="expand-image" value="<?php echo $jetNode; ?>" style="float: right;" src="<?php echo CEDJETINTEGRATION; ?>expand.png" height="16" width="16" />
						    		</p>
						    	</div>
						    	<div class="options_group" id="<?php echo $jetNode;?>" style="display: none; margin: 10px;">
						    	<table class="attribute-profile-table">
						    	<input type="hidden" name="select_cat_<?php echo $jetNode;?>" >
						      <?php foreach($mappedCAT as $attrARRAY):
						      
						    		$attrObject = isset($attrARRAY[0])?$attrARRAY[0]:array();
//print_R($attrObject);
						      		if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit)) ):
						      		
						      		$values	=	json_decode($attrObject->unit);
						      		
						      		$assocValues			=	array();
						      		$assocValues['none']	=	'Select A Value';
						      		 
						      		foreach($values as $VALUE):
						      			$assocValues[$VALUE]	=	$VALUE;
						      		endforeach;
						      			$tempName3	=	$jetNode."_".$attrObject->jet_attr_id;
						      			?><tr><td><?php 
						      			cedJetLibraryFunctions::cedcommerce_text_with_unit_select(
											array(
												'id' => $tempName3,
												'name' => $tempName3,
												'label' => __($attrObject->name , 'woocommerce-jet-integration'),
												'value1'=>  $categoryAttrdata[$tempName3],
												'value2'=>	$categoryAttrdata[$tempName3.'_unit'],
												'options' => $assocValues,
										)
						      		);
						      		?></td><td>OR&nbsp;&nbsp;</td><td><?php 
									$tempSelected3	=	$categoryAttrdata[$tempName3.'_attributeMeta'] ? $categoryAttrdata[$tempName3.'_attributeMeta']:'';
									woocommerce_wp_select(
										array(
										'id'      => $tempName3.'_attributeMeta',
										'value'   => $tempSelected3,
										'options' => $attrOptions,
										)
										);
									?></td></tr><?php 
						      		endif;
						    		if($attrObject->freetext == 1):?>
						    		<tr><td>
							    		<p class="form-field dimensions_field">
											<label for="jetAttributes"><?php echo $attrObject->name;?></label>
											<?php $tempName		=	$jetNode."_".$attrObject->jet_attr_id;?>
											<?php $tempValue	=	isset($categoryAttrdata[$tempName])?$categoryAttrdata[$tempName]:'';?>
											<input type="text" value="<?php echo $tempValue;?>" name="<?php echo $tempName;?>" size="5" >
											</td><td>OR&nbsp;&nbsp;</td><td>
											<?php 
											$tempSelected	=	isset($categoryAttrdata[$tempName.'_attributeMeta']) ? $categoryAttrdata[$tempName.'_attributeMeta']:'';
											woocommerce_wp_select(
											array(
											'id'      => $tempName.'_attributeMeta',
											'value'   => isset($tempSelected)?$tempSelected:'',
											'options' => $attrOptions,
											)
											);?>
											</td></tr>
										</p>
						    	<?php endif;
						    		if($attrObject->freetext == 0 && !empty($attrObject->values) && empty($attrObject->unit)):
						    			?><tr><td><?php 

						    			$values	=	json_decode($attrObject->values);
						    			
						    			$assocValues	=	array();
						    			$assocValues['none']	=	'Select A Value';
						    			
						    			if(!empty($values)){
						    			foreach($values as $VALUE):
						    				$assocValues[$VALUE]	=	$VALUE;
						    			endforeach;
						    			}
						    			$tempName2		=	$jetNode.'_'.$attrObject->jet_attr_id;
						    			$tempValue2		=	isset($categoryAttrdata[$tempName2]) ? $categoryAttrdata[$tempName2]:'';
						    			woocommerce_wp_select(
												array(
												'id'      => $tempName2,
												'label'   => __( $attrObject->name, 'woocommerce-jet-integration' ),
												'value'       => isset($tempValue2)?$tempValue2:'',
												'options' => $assocValues,
												)
											);
						    			?></td><td>OR&nbsp;&nbsp;</td><td><?php 
											$tempSelected2	=	isset($categoryAttrdata[$tempName2.'_attributeMeta']) ? $categoryAttrdata[$tempName2.'_attributeMeta']:'';
											woocommerce_wp_select(
											array(
											'id'      => $tempName2.'_attributeMeta',
											'value'   => isset($tempSelected2)?$tempSelected2:'',
											'options' => $attrOptions,
											)
											);
											?></td></tr>
											<?php 
						    		endif;?>
						    	<?php endforeach;?>
						    	</table>
						    	</div>
						    	
						    	<?php endforeach;?>
						    <?php endif;?>
			    	<?php } ?>
				<?php }?>
				
			<div class="clear"></div>
		</div>

		<div id="itemspecifies" class="postbox">
			<h3><span><?php _e('Item Specifics','woocommerce-jet-integration')?></span></h3>
			<div class="inside format">
			<table id="item-specific-profile" class="wp-list-table widefat fixed striped posts">
			<thead>
			<td><b><?php _e('Field','woocommerce-jet-integration');?></b></td>
			<td><b><?php _e('custom value','woocommerce-jet-integration');?></b></td>
			<td><b><?php _e('use attribute value','woocommerce-jet-integration');?></b></td>
			</thead>
			<tr><td>
				<label class="text_label">
					<?php _e('Brand','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input type="text" style="width:90%;" id="brand" value="<?php if(isset($brand)){echo $brand;}?>" size="30" name="brand">
				</td><td>
				<?php
				woocommerce_wp_select(
											array(
											'id'      => 'jetbrand_attributeMeta',
											'value'   => isset($jetbrand_attributeMeta)?$jetbrand_attributeMeta:'',
											'options' => $attrOptions,
											)
											); ?>
											</td></tr>
				<tr><td>
				<label class="text_label">
					<?php _e('Country Manufacturer','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input type="text" style="width:90%;" autocomplete="off" id="country_manufac" value="<?php if(isset($country_manuf)){echo $country_manuf;}?>" size="30" name="country_manufac">
				</td><td>
				<?php
				woocommerce_wp_select(
											array(
											'id'      => 'cm_attributeMeta',
											'value'   => isset($cm_attributeMeta)?$cm_attributeMeta:'',
											'options' => $attrOptions,
											)
											); ?>
											</td></tr>
				<tr><td>
				<label class="text_label" >
					<?php _e('Safety Warning','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input type="text" style="width:90%;" autocomplete="off" id="safety_warning" value="<?php if(isset($safety_warning)){echo $safety_warning;}?>" size="30" name="safety_warning">
				</td><td>
				<?php
				woocommerce_wp_select(
											array(
											'id'      => 'sw_attributeMeta',
											'value'   => isset($sw_attributeMeta)?$sw_attributeMeta:'',
											'options' => $attrOptions,
											)
											); ?>
											</td></tr>
				<tr><td>
				<label class="text_label" >
					<?php _e('Fullfillment Time','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input type="text" style="width:90%;" autocomplete="off" id="fullfillment_time" value="<?php if(isset($fullfillment)){echo $fullfillment;}?>" size="30" name="fullfillment_time">
				</td><td>
				<?php
				woocommerce_wp_select(
											array(
											'id'      => 'ft_attributeMeta',
											'value'   => isset($ft_attributeMeta)?$ft_attributeMeta:'',
											'options' => $attrOptions,
											)
											); ?>
											</td></tr>
				<tr><td>
				<label class="text_label" >
					<?php _e('Map Price','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input class="short wc_input_price" type="text" style="width:90%;" autocomplete="off" id="map_price" value="<?php if(isset($map_price)){echo $map_price;}?>" size="30" name="map_price">
				</td><td>
				<?php
				$tempSelected2 = isset($brand_attribute_meta)?$brand_attribute_meta:'';
				woocommerce_wp_select(
											array(
											'id'      => 'mp_attributeMeta',
											'value'   => isset($mp_attributeMeta)?$mp_attributeMeta:'',
											'options' => $attrOptions,
											)
											); ?>
											</td></tr>
											<tr><td>
				<label class="text_label" >
					<?php _e('Package Length:','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input type="text" style="width:90%;" autocomplete="off" id="package_length" value="<?php if(isset($package_length)){echo $package_length;}?>" size="30" name="package_length">
				</td><td>
				<?php
				$tempSelected2 = isset($brand_attribute_meta)?$brand_attribute_meta:'';
				woocommerce_wp_select(
											array(
											'id'      => 'pl_attributeMeta',
											'value'   => isset($pl_attributeMeta)?$pl_attributeMeta:'',
											'options' => $attrOptions,
											)
											); ?>
											</td></tr>
				<tr><td>
				<label class="text_label" >
					<?php _e('Package Width:','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input type="text" style="width:90%;" autocomplete="off"  value="<?php if(isset($package_width)){echo $package_width;}?>" size="30" name="package_width">
				</td><td>
				<?php
				$tempSelected2 = isset($brand_attribute_meta)?$brand_attribute_meta:'';
				woocommerce_wp_select(
											array(
											'id'      => 'pw_attributeMeta',
											'value'   => isset($pw_attributeMeta)?$pw_attributeMeta:'',
											'options' => $attrOptions,
											)
											); ?>
											</td></tr>
				<tr><td>
				<label class="text_label" >
					<?php _e('Package Height:','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input type="text" style="width:90%;" autocomplete="off" id="package_height" value="<?php if(isset($package_height)){echo $package_height;}?>" size="30" name="package_height">
				</td><td>
				<?php
				woocommerce_wp_select(
											array(
											'id'      => 'ph_attributeMeta',
											'value'   => isset($ph_attributeMeta)?$ph_attributeMeta:'',
											'options' => $attrOptions,
											)
											); ?>
											</td></tr>
											<tr><td>
				<label class="text_label" >
					<?php _e('Manufacturer suggested retail price:','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input type="text" style="width:90%;" autocomplete="off"  value="<?php if(isset($manuf_ret_price)){echo $manuf_ret_price;}?>" size="30" name="manufacturer_retail_price">
				</td><td>
				<?php
				woocommerce_wp_select(
											array(
											'id'      => 'mr_attributeMeta',
											'value'   => isset($mr_attributeMeta)?$mr_attributeMeta:'',
											'options' => $attrOptions,
											)
											); ?>
											</td></tr>
				<tr><td>
				<label class="text_label" >
					<?php _e('Legal Disclaimer Description:','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<input type="text" style="width:90%;" autocomplete="off" id="legal_description" value="<?php if(isset($legal_desc)){echo $legal_desc;}?>" size="30" name="legal_description">
				</td></tr>
				<tr><td>
				<label class="text_label" >
					<?php _e('Product Tax Code:','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<?php 
					$product_tax_code = array(
						'select',
						'Toilet Paper',
						'Thermometers',
						'Sweatbands',
						'SPF Suncare Products',
						'Sparkling Water',
						'Smoking Cessation',
						'Shoe Insoles',
						'Safety Clothing',
						'Pet Foods',
						'Paper Products',
						'OTC Pet Meds',
						'OTC Medication',
						'Oral Care Products',
						'Non-Motorized Boats',
						'Non Taxable Product',
						'Mobility Equipment',
						'Medicated Personal Care Items',
						'Infant Clothing',
						'Helmets',
						'Handkerchiefs',
						'Generic Taxable Product',
						'General Grocery Items',
						'General Clothing',
						'Fluoride Toothpaste',	
						'Feminine Hygiene Products',
						'Durable Medical Equipment',
						'Drinks under 50 Percent Juice',
						'Disposable Wipes',
						'Disposable Infant Diapers',
						'Dietary Supplements',
						'Diabetic Supplies',
						'Costumes',
						'Contraceptives',
						'Contact Lens Solution',
						'Carbonated Soft Drinks',
						'Car Seats',
						'Candy with Flour',
						'Candy',
						'Breast Pumps',
						'Braces and Supports',
						'Bottled Water Plain',
						'Beverages with 51 to 99 Percent Juice',
						'Bathing Suits',
						'Bandages and First Aid Kits',
						'Baby Supplies',
						'Athletic Clothing',
						'Adult Diapers',
					);?>
					<select id="product_tax_code" name="product_tax_code" style="width:90%;">
		           <?php foreach($product_tax_code as $key => $val){
		           		if(isset($tax_code_val) && $val == $tax_code_val){  ?>
		           			<option selected="selected" value="<?php echo $tax_code_val?>"><?php echo $tax_code_val;?></option>
		           		<?php }else{?>
		           			<option value="<?php echo $val?>"><?php echo $val;?></option>
		           		<?php } 
		           }?>
		       </select>
				</td>
				<td></td>
				</tr>
				
				<tr><td>
				<label class="text_label" >
					<?php _e('Map Implementation:','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<?php $implementation = array(
						'select',
						'no restrictions on product based pricing',
						'Jet member savings on product only visible to logged in Jet members',
						'Jet member savings never applied to product',
				);
				
				?>
				<select name="map_implementation" style="width:90%;">
		           <?php foreach($implementation as $key => $val){
		           		if(isset($map_implem) && $val == $map_implem){  ?>
		           			<option selected="selected" value="<?php echo $map_implem?>"><?php echo $map_implem;?></option>
		           		<?php }else{?>
		           			<option value="<?php echo $val?>"><?php echo $val;?></option>
		           		<?php } 
		           }?>
		       </select>
				</td>
				<td></td></tr>
				
				<!-- change 3feb -->
				
				<tr><td>
				<label class="text_label" >
					<?php _e('Ship Alone','woocommerce-jet-integration')?>
				</label>
				</td><td>
				<?php 
				 $ship_alone = array(
						'true',
						'false',
				);
				?>
				
				<select id="ship_alone" name="ship_alone" style="width:90%;">
		           <?php foreach($ship_alone as $key => $val){
		           		if(isset($ship_alone_val) && $val == $ship_alone_val){  ?>
		           			<option selected="selected" value="<?php echo $ship_alone_val?>"><?php echo $ship_alone_val;?></option>
		           		<?php }else{?>
		           			<option value="<?php echo $val?>"><?php echo $val;?></option>
		           		<?php } 
		           }?>
		       </select>
		       </td>
		       <td></td>
		      </tr>
				<tr><td>
				<label class="text_label" >
					<?php _e('Bullets:','woocommerce-jet-integration')?>
				</label>
				<td>
					<p class="">
	           
	           		<input type="text"  name="bullet_1" value="<?php echo $bullet1; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	           		<input type="text"  name="bullet_2" value="<?php echo $bullet2; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	           		<input type="text"  name="bullet_3" value="<?php echo $bullet3; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	           		<input type="text"  name="bullet_4" value="<?php echo $bullet4; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	           		<input type="text"  name="bullet_5" value="<?php echo $bullet5; ?>" placeholder="<?php _e('bullet', 'woocommerce-jet-integration'); ?>" />
	        </p>
	        <td></td>
				</td>
				</td></tr>
				<tr><td>
				<label class="text_label" >
					<?php _e('Prop-65','woocommerce-jet-integration')?>
				</label>
				<?php 
				 $prop65 = array(
						'true',
						'false',
				);
				?>
				</td><td>
				<select id="prop65" name="prop65" style="width:90%;">
		           <?php foreach($prop65 as $key => $val){
		           		if(isset($prop65_val) && $val == $prop65_val){  ?>
		           			<option selected="selected" value="<?php echo $prop65_val?>"><?php echo $prop65_val;?></option>
		           		<?php }else{?>
		           			<option value="<?php echo $val?>"><?php echo $val;?></option>
		           		<?php } 
		           }?>
		       </select>
		       </td>
		       <td></td></tr>
				<tr><td colspan="2">
				 <?php $statement = array(	
		    					'no warning applicable' => 'no warning applicable',
		    					'choking hazard small parts' => 'choking hazard small parts',
		    					'choking hazard is a small ball' =>'choking hazard is a small ball',
		    					'choking hazard is a marble' =>'choking hazard is a marble',
		    					'choking hazard contains a small ball' => 'choking hazard contains a small ball',
		    					'choking hazard contains a marble' => 'choking hazard contains a marble',
		    					'choking hazard balloon' => 'choking hazard balloon');
		    
	        cedJetLibraryFunctions::woocommerce_wp_select_multiple( array(
		        'id' => 'cpsia_cautionary_statements_profile',
		        'class'=> 'profileMultiSelect',
		        'name' => 'cpsia_cautionary_statements[]',
		        'label' => __('CPSIA causionary statements', 'woocommerce-jet-integration'),
		        'value'	=>  isset($cpsia_statement)?$cpsia_statement:'',
		        'options' => $statement
		        )
		        );
	        ?>
	        </td>
	        <td></td></tr>
				<!-- end -->
				</table>
			</div>
			</div>
				
		</div>
		
		</div> <!-- .meta-box-sortables -->
	<div class="postbox-container" id="postbox-container-1">
		<div class="meta-box" id="side-sortables">
<!-- 
		<div id="profileHelpBox" class="postbox">
			<h3><span><?php _e('Help','woocommerce-jet-integration')?></span></h3>
			<div class="inside">
				<p>
					<?php _e('You can Apply this profile to one or more than one products, for those products the field values are picked up from the provided value or selected meta field if any.','woocommerce-jet-integration')?>
				</p>
			</div>
		</div>
 -->
			<!-- first sidebox -->
		<div id="submitdiv" class="postbox">
		<!-- <h3><span><?php _e('Use','woocommerce-jet-integration')?></span></h3> -->	
			<div class="inside">
				<div class="submitbox" id="profilesubmitpost">
				<!-- <div class="misc-pub-section">
						<p><?php _e('You can apply this profile to products from manage product tab before uploading(re-upload the product if you want product data modifications according to newly assigned profile).','woocommerce-jet-integration')?></p>
					</div>  -->	
					<div id="major-publishing-actions">
						<div id="publishing-action">
							<input type="hidden" value="<?php if(isset($profile_id)){echo $profile_id;}?>" name="profile_id">
							<input type="submit" name="save" class="button-primary" id="publish" value="Save profile">
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<form method="post">
		<div id="allMetaFields" class="postbox">
  <?php 
   
   $products_IDs = array();
   
   $all_products = new WP_Query( array(
     'post_type' => array('product', 'product_variation'),
     'post_status' => 'publish',
     'posts_per_page' => 100
   ) );
   $products = $all_products->posts;
   $first_id  = $all_products->posts['0']->ID;
   
   foreach ( $products as $product ) {
    $product_IDs[] = $product->ID;
   }
   
         // Get all the data.
         $getPostCustom = get_post_custom($first_id);
         $product       = wc_get_product( $first_id );
   ?>
   <p>
   <label><?php _e('Select Product','woocommerce-jet-integration')?></label>
   <select name="linked_proID" style="width: 50%;"  class="wc-select">
    <?php  
    foreach($product_IDs as $key => $pro ){
     $product_id = $pro;
     
     ?>
     <option value="<?php echo $product_id;?>"><?php echo get_the_title($product_id);?></option>
     <?php }?> 
   </select>
   </p>
   <h2>
   
   <?php _e("Example Meta Fields for product: ","woocommerce-jet-integration");?><b><?php echo $product->get_formatted_name();?></b></h2>
    <div style="overflow:auto;height:600px;">
   <table class="wp-list-table widefat fixed striped posts">
    <thead>
     <tr>
      <th style="width:3%" class="manage-column column-cb check-column" id="cb" scope="col">
       <label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All','woocommerce-jet-integration');?></label>
        <input type="checkbox" id="cb-select-all-1">
      </th>
        <th style="width:10%" class="manage-column column-name sortable desc" id="banner_title" scope="col">
          <a href=""><span><?php _e('Meta Field Key','woocommerce-jet-integration');?></span>
           </a>
        </th>
        <th style="width:10%" class="manage-column column-name sortable desc" id="banner_title" scope="col">
          <a href=""><span><?php _e('Meta Field value','woocommerce-jet-integration');?></span>
           </a>
        </th>
       </tr>
      </thead>
      <tbody id="pro_id_append">
       <?php foreach($getPostCustom as $customPostKey => $customPostValue) :?>
       <tr class="iedit author-self mobicnct-banner-listing level-0 post-<?php echo $customPostKey;?> type-product status-publish hentry product_row" id="<?php echo $customPostKey;?>">
      <td class="check-column" scope="row">
       <label for="cb-select-<?php echo $customPostKey;?>" class="screen-reader-text"></label>
        <input type="checkbox" value="<?php echo $customPostKey;?>" class="unique_check" name="unique_post[]" id="cb-select-<?php echo $customPostKey;?>">
       <div class="locked-indicator"></div>
      </td>
      <td class="name column-id" >
       <span class="b_id" ><?php echo $customPostKey;?></span>
      </td>
      <td class="name column-id" >
       <span class="b_id" ><?php echo $customPostValue[0];?></span>
      </td>
     </tr>
       <?php endforeach;?>
      </tbody>
      <tfoot>
       <tr>
      <th style="width:3%" class="manage-column column-cb check-column" id="cb" scope="col">
       <label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All','woocommerce-jet-integration');?></label>
        <input type="checkbox" id="cb-select-all-1">
      </th>
        <th style="width:10%" class="manage-column column-name sortable desc" id="banner_title" scope="col">
          <a href=""><span><?php _e('Meta Field Key','woocommerce-jet-integration');?></span>
           </a>
        </th>
        <th style="width:10%" class="manage-column column-name sortable desc" id="banner_title" scope="col">
          <a href=""><span><?php _e('Meta Field value','woocommerce-jet-integration');?></span>
           </a>
        </th>
       </tr>
      </tfoot>
     </table>
      <p class="submit">
    <input class="button-primary" type="submit" value="Add Meta Keys" name="add_meta_keys">
   </p>
    </div> 
    
     
  </div>
	</form>
	</div>
</div> <!-- #postbox-container-1 -->
</div> 
	</div> <!-- #post-body -->
		<br class="clear">
	</div> <!-- #poststuff -->
</div>