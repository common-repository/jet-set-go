<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'class-cedJetSetupDummyApi.php';
require_once 'class-cedJetFileUploadHelper.php';
require_once 'class-cedJetDBHelper.php';
require_once 'class-cedJetLibraryFunctions.php';
require_once 'class-productManagement.php';

class cedJetAjaxHandler{
	
	private static $_instance;
	
	public static function getInstance() {
		self::$_instance = new self;
		if( !self::$_instance instanceof self )
			self::$_instance = new self;

		return self::$_instance;

	}
	
	public function __construct() {
		
		$this->setupapi			=	cedJetSetupDummyApi::getInstance();
		$this->fileUploadHelper	=	cedJetFileUploadHelper::getInstance();
		$this->dbHelper			=	cedJetDBHelper::getInstance();
		$this->libraryAction 	= 	cedJetLibraryFunctions::getInstance();
		$this->product_validate	=	cedJetProductManagement::getInstance();
		$this->registerHooks();
		
	}
	
	public function registerHooks(){
		
		//hook for mass product upload
		add_action('mass_product_upload_done',array($this,'upload_mass_product_through_cron'),10,1);
		
		//hook for mass product archieve
		add_action('mass_product_archieve_done',array($this,'mass_product_archieve_through_cron'),10,1);
		
		//hook for mass product unarchieve
		add_action('mass_product_unarchieve_done',array($this,'mass_product_unarchieve_through_cron'),10,1);
		
		//hook for mass product inventory upload
		add_action('mass_product_inventory_upload_done',array($this,'upload_mass_product_inventory_through_cron'),10,1);
		
	}
	
	public function cedCategoryMapping(){
		if(check_ajax_referer( 'catmapnonce', 'jet_security' )){
			if(isset($_POST['wooCatID']) && isset($_POST['jetCatID'])){

				$wooCatID	=	$_POST['wooCatID'];
				$jetCatID	=	$_POST['jetCatID'];
				
				if(!empty($wooCatID) && !empty($jetCatID)){

					$mappedArray	=	get_option('cedWooJetMapping',false);

					if(empty($mappedArray)){

						$mappedArray				=	array();
						$mappedArray[$wooCatID]		=	$jetCatID;
						
					}else{
						
						if(is_array($mappedArray)){

							$tempArray = array();
							foreach ($mappedArray as $mappedWoocat => $mappedjetcat){
								
								$tempArray[]	=	$mappedWoocat;
							}
							if(in_array($wooCatID, $tempArray)){
								
								echo json_encode('Selected woo category is already mapped with a jet Category.');
								exit;
							}else{
								
								$mappedArray[$wooCatID]		=	$jetCatID;
							}
						}
					}

					update_option('cedWooJetMapping', $mappedArray);
					$update_mapped_cat = get_option('cedWooJetMapping');
					$all_map_cat_details = array();
					$i = 0;
					foreach($update_mapped_cat as $wooCatID => $jetCatId){
						$term	=	get_term_by('id', $wooCatID, 'product_cat');
						if(isset($term)){
							$mappedWooCatName	=	$term->name;
						}
						$all_map_cat_details[$i]['woo_cat_id'] 		= $wooCatID;
						$all_map_cat_details[$i]['woo_cat_name'] 	= $mappedWooCatName;
						$all_map_cat_details[$i]['jet_cat_id'] 		= $jetCatId;
						$i++;	
					}

					$mappedWooCatName	=	'';
					$this->dbHelper->insertAttributeData($jetCatID);

				//_e('selected woo category is mapped with jet category successfully.','woocommerce-jet-integration');

					echo json_encode($all_map_cat_details);
					exit;
					
				}else{
					echo json_encode ('Error while mapping, please try again.');
					exit;
				}
			}else{
				echo json_encode ('please provide complet information to map');
				exit;
			}
		} 
	}
	
	public function cedActivateAndResubmitProduct(){
		
		if(isset($_POST['activate_upload_type']))
		{
			if(isset($_POST['error_file_upload']))
			{
				global $wpdb;
				$jet_file_id        = '';
				$jet_file_id 		= 	$_POST['jet_file_id'];
				$table_name 		=	$wpdb->prefix.'jet_file_info';
				$qry 				= 	"SELECT `woocommerce_batch_info`,`file_type` from `$table_name` where `jet_file_id` = '$jet_file_id' ;";
				$resultdata 		= 	$wpdb->get_results($qry);
				$commaseperate_ids  = 	$resultdata[0]->woocommerce_batch_info;
				$ids 				= 	explode(',', $commaseperate_ids);
				$product_id 		= 	$ids;
				$file_type			=   $resultdata[0]->file_type;
			}

			if(isset($_POST['demo_file_upload']))
			{

				$product_id[0] 		=	$_POST['single_product_id'];
				$file_type  		= 	$_POST['file_type'];
				if($product_id[0] == '0')
				{
					echo __('Select Any Product For Upload','woocommerce-jet-integration') ;
					exit;
				}
			}
			$this->setupapi->enable_product_api($product_id,$file_type,$jet_file_id);
		}
	}
	
	public function deleteMappedOptionCatEntry(){
		
		if(check_ajax_referer('delmapcat','del_secure')){
			if(isset($_POST['wooCatID'])){

				$wooCatId	=	$_POST['wooCatID'];
				$mappedCats	=	get_option('cedWooJetMapping',true);

				foreach($mappedCats as $wooid => $jetCatId):

					if($wooid	==	$wooCatId){

						unset($mappedCats[$wooid]);
					}
					endforeach;

					update_option('cedWooJetMapping', $mappedCats);

					$update_mapped_cat = get_option('cedWooJetMapping');
					$all_map_cat_details = array();
					$i = 0;
					foreach($update_mapped_cat as $wooCatID => $jetCatId){
						$term	=	get_term_by('id', $wooCatID, 'product_cat');
						if(isset($term)){
							$mappedWooCatName	=	$term->name;
						}
						$all_map_cat_details[$i]['woo_cat_id'] 		= $wooCatID;
						$all_map_cat_details[$i]['woo_cat_name'] 	= $mappedWooCatName;
						$all_map_cat_details[$i]['jet_cat_id'] 		= $jetCatId;
						$i++;
					}

					$mappedWooCatName	=	'';

					echo json_encode($all_map_cat_details);

					exit;
				}
			}
		}

		public function UpdateMappedCatId(){

			if(isset($_POST)){

				$wooCatId	=	$_POST['wooCatId'];
				$jetCatId	=	$_POST['jetCatId'];

				$mappedCats	=	get_option('cedWooJetMapping',true);
				foreach($mappedCats as $wooid => $jetId):

					if($wooid	==	$wooCatId){
						$mappedCats[$wooid]	=	$jetCatId;
					}
					endforeach;

					update_option('cedWooJetMapping', $mappedCats);

					$update_mapped_cat = get_option('cedWooJetMapping');
					$all_map_cat_details = array();
					$i = 0;
					foreach($update_mapped_cat as $wooCatID => $jetCatId){
						$term	=	get_term_by('id', $wooCatID, 'product_cat');
						if(isset($term)){
							$mappedWooCatName	=	$term->name;
						}
						$all_map_cat_details[$i]['woo_cat_id'] 		= $wooCatID;
						$all_map_cat_details[$i]['woo_cat_name'] 	= $mappedWooCatName;
						$all_map_cat_details[$i]['jet_cat_id'] 		= $jetCatId;
						$i++;
					}

					$mappedWooCatName	=	'';
					$this->dbHelper->insertAttributeData($jetCatId);

					echo json_encode($all_map_cat_details);
					exit;
				}
			}

			public function createProfileHtml(){

				if(isset($_POST['pid'])){

					$productID 		=	$_POST['pid'];
					$profileInfo	=	$this->dbHelper->getAllSavedProfileName();
					if(empty($profileInfo)){
						_e('please set atleast one profile to assign','woocommerce-jet-integration');
						exit;
					}else{

						$ifSet = get_post_meta($productID, 'productProfileID');
						if(!empty($ifSet) && count($ifSet)){

							_e('Profile is already assigned to this product please remove existing profile','woocommerce-jet-integration');
							exit;
						}

						echo '<select name="profileName" id="selectedProfile"><option value="none">select a profile</option>';
						foreach($profileInfo as $profileID => $profileName){

							echo '<option value="'.$profileID.'">'.$profileName.'</option>';
						}
						echo '</select><input type="button" onclick="jQuery.fancybox.close()" value="Assign" />';
						exit;
					}
				}
			}

			public function assignProfileToProduct(){

				if(isset($_POST['prdctid']) && isset($_POST['profileID'])){

					$productID		=	$_POST['prdctid'];
					$profileID		=	$_POST['profileID'];
					$profileDetail	=	$this->dbHelper->getProfileDetail($profileID);
					$profileName	=	$profileDetail[0]->profile_name;

					update_post_meta($productID, 'productProfileID', $profileID);
					?><img class="remove_profile" value="<?php echo $productID;?>" src="<?php echo CEDJETINTEGRATION; ?>/remove.png" height="16" width="16" />
					<span class="p_type"><center><?php echo $profileName;?></center></span><?php 

					exit;
				}else{
					_e('error while assigning profile to product','woocommerce-jet-integration');
					exit;
				}
			}

			public function removeProfile(){

				if(isset($_POST)){

					$productID	=	$_POST['pid'];
					delete_post_meta($productID, 'productProfileID');

					_e('Removed','woocommerce-jet-integration');
					exit;
				}
				_e('error while removing','woocommerce-jet-integraton');
				exit;
			}

			public function appendMappedCategoryHtml(){

				if(isset($_POST['wooCatId'])){

					$wooCatId	=	$_POST['wooCatId'];

					$mappedCats	=	get_option('cedWooJetMapping',true);

					foreach($mappedCats as $wooid => $jetId):

						if($wooid	==	$wooCatId){

							$jetNodeID	=	$jetId;

							$mappedAttributes		=	get_option($jetNodeID.'_linkedAttributes',false);
							if($mappedAttributes){

								$mappedAttributes	=	json_decode($mappedAttributes);
								if(is_array($mappedAttributes)){

									$jetAttrInfo[$jetNodeID]	=	$this->dbHelper->fetchAttrDetails($mappedAttributes);
								}
							}
							if(!empty($jetAttrInfo) && count($jetAttrInfo)):
								foreach($jetAttrInfo as $jetNode => $mappedCAT):
									$wooCatID 	=	$wooid;
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
								<div class="options_group" data-wid="<?php echo $wooCatID;?>" >
									<p><?php _e($mappedWooCatName." JET Attributes",'wocommerce-jet-integration');?>
										<input type="radio" class="jet-category-select" name="selectedCatAttr" value="<?php echo $jetNode;?>" <?php echo $check;?>>
										<img class="expand-image" value="<?php echo $jetNode; ?>" style="float: right;" src="<?php echo CEDJETINTEGRATION; ?>expand.png" height="16" width="16" />
										<img class="help_tip" data-tip="<?php _e("Select this if you want to send the attributes mapped with this jet category(only single category attributes can be sent to jet.)", "woocommerce-jet-integration");?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
									</p>
								</div>
								<div class="options_group" id="<?php echo $jetNode;?>" data-wid="<?php echo $wooCatID;?>" style="display: none">
									<?php foreach($mappedCAT as $attrARRAY):

									$attrObject = $attrARRAY[0];

									if($attrObject->freetext == 2 || ($attrObject->freetext == 0 && !empty($attrObject->unit)) ):

										$values	=	json_decode($attrObject->unit);

									$assocValues			=	array();
									$assocValues['none']	=	'Select A Value';
					      	//	print_r($values); die("adsf");

									if(isset($values) && count($values)>1):
										foreach($values as $VALUE):
											$assocValues[$VALUE]	=	$VALUE;
										endforeach;

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
										endif;
										if($attrObject->freetext == 1):?>
										<p class="form-field dimensions_field">
											<label for="jetAttributes"><?php echo $attrObject->name;?></label>
											<?php $tempName	 =	$jetNode."_".$attrObject->jet_attr_id?>
											<?php $tempValue =	get_post_meta($post->ID , $tempName , true);?>
											<input type="text" value="<?php echo $tempValue;?>" name="<?php echo $tempName;?>" size="5" >
										</p>
									<?php endif;
									if($attrObject->freetext == 0 && !empty($attrObject->values) && empty($attrObject->unit) ):

										$values	=	json_decode($attrObject->values);

									$assocValues			=	array();
									$assocValues['none']	=	'Select A Value';

									if( !empty($values) && isset($values) && count($values) > 1) :
										foreach($values as $VALUE):
											$assocValues[$VALUE]	=	$VALUE;
										endforeach;

										woocommerce_wp_select(
											array(
												'id'      => $jetNode."_".$attrObject->jet_attr_id,
												'label'   => __( $attrObject->name, 'woocommerce-jet-integration' ),
												'description' => __( 'Select a value.', 'woocommerce-jet-integration' ),
												'value'       => get_post_meta( $post->ID, $jetNode."_".$attrObject->jet_attr_id , true ),
												'options' => $assocValues,
												)
											);
										endif;
										endif;?>
									<?php endforeach;?>
								</div>
							<?php endforeach;
							endif;
						}
						endforeach;
					}
					exit;
				}

	//mass product upload on jet
				public function all_product_of_jet(){

					if(isset($_POST['action_type'])){

						$mappedCategories	=	get_option('cedWooJetMapping',false);

						if(!empty($mappedCategories)){
							$all_woo_cat	=	array();
							foreach($mappedCategories as $mappedwoocat	=> $mappedjetcat){
								$all_woo_cat[]	=	$mappedwoocat;
							}
						}

						$paged = ($_POST['counter']) ? $_POST['counter'] : 1;

						$args = array(
							'post_type' => 'product',
							'paged'					=> $paged,
							'posts_per_page'        => '600',
							'tax_query' => array(
								array(
									'taxonomy'      => 'product_cat',
												'field' 		=> 'term_id', //This is optional, as it defaults to 'term_id'
												'terms'         => $all_woo_cat,
												'operator'      => 'IN' ,// Possible values are 'IN', 'NOT IN', 'AND'.
												),
								),
							);

						$loop = new WP_Query($args);	

						$identifier = false;
						while ( $loop->have_posts() ) {
							$identifier = true;
							$loop->the_post();
							$_product = get_product($loop->post->ID );

							if($_product->is_downloadable('yes')){
								$product_ids['id'][] 	= 'error';
								$product_ids['name'][]	= 'downloadable product';
								continue;
							}


							$product_ids['id'][] 	= $_product->id;
							$product_ids['name'][]	= $_product->post->post_title;
						}
						
						if(!empty($product_ids)){
							echo json_encode($product_ids);
							exit;
						}
					}
				}

	//uploading mass product on jet dsfds
				public function uploading_mass_product(){

					if(isset($_POST['mass_upload_type'])){
						if(check_admin_referer('check_mass_pro_upload_nonce','bupbm_nonce')){
							$all_product_id = array();
							$all_product_id = $_POST['all_mass_product_id'];

							$running = get_option('mass_running');
							if(!empty($running)){
								echo $running;
								exit;
							}
					/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_upload.txt","a") or die("Can't open the requested file");
					fwrite($fp, ("Mass Product Upload start on date: ".date('Y-m-d H:i:s')));
					fwrite($fp,("\n"));
					fclose($fp); */
					$this->call_schedule_function($all_product_id);
				}	
				
		}elseif(isset($_POST['cat_mass_upload_type'])){ //upload by category selection
			if(check_admin_referer('jet_bulk_cat_check','mubc_nonce')){
				$cat_id 	= $_POST['selected_cat_id'];
				$args 		= array(
					'post_type' 		=> 	array('product'),
					'post_status' 		=> 	'publish',
					'tax_query'         => 	array(
						array(
							'taxonomy'      => 'product_cat',
															'field' 		=> 'term_id', //This is optional, as it defaults to 'term_id'
															'terms'         =>  array($cat_id),
															'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
															)
						),
					'posts_per_page' => -1,
					);

				$loop 			= new WP_Query($args);
				$all_product_id = array();

				while ( $loop->have_posts() ) {
					$loop->the_post();
					$_product 			= 	get_product($loop->post->ID );
					$product_id 		= 	$_product->id;
					if($_product->is_downloadable('yes'))
						continue;	

					if($_product->is_type('variable') || $_product->is_type('simple')){
						$all_product_id[] 	= 	$product_id;
					}

				}

				if(!empty($all_product_id)){
					$running = get_option('mass_running');
					if(!empty($running)){
						echo $running;
						exit;
					}
				/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_upload.txt","a") or die("Can't open the requested file");
				fwrite($fp, ("Mass Product Upload start on date: ".date('Y-m-d H:i:s')));
				fwrite($fp,("\n"));
				fclose($fp); */
				$this->call_schedule_function($all_product_id);
			}
		}
	}	 
}
	//hook define for mass product upload on jet
public function call_schedule_function($all_product_id){
	
	if ( !wp_next_scheduled( 'mass_product_upload_done' ) ) {
		wp_schedule_single_event(time(),'mass_product_upload_done',array($all_product_id));
	}
	
}
	//for cron
public function upload_mass_product_through_cron($all_product_id){
	
	
		/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_upload.txt","a") or die("Can't open the requested file");
		fwrite($fp, ("Mass Product initila Upload start for product id jet extension: "));
		foreach($all_product_id as $key => $id){
			fwrite($fp,$id.',');
		}
		fwrite($fp,("\n"));
		fclose($fp); */

		$send_sku  = array();
		$new_array = array();
		$send_sku  = array_slice($all_product_id,0,10);
		$new_array = array_diff($all_product_id,$send_sku);


		if(!empty($send_sku)){

			//print_r($send_sku);
			$mass_upload_running = 'There is also performing mass upload please ,try later';
			update_option('mass_running',$mass_upload_running);
			$type 			= 	'MerchantSKUs';
			$file_id 		= 	'';

			/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_upload.txt","a") or die("Can't open the requested file");
			fwrite($fp, ("Mass Product Upload start for product id jet extension: "));
			foreach($send_sku as $key => $id){
				fwrite($fp,$id.',');
			}
			fwrite($fp,("\n"));
			fclose($fp); */

			$file_type = 'upload';

			require_once CEDJET_DIRPATH.'/includes/class-productManagement.php';
			$mass_upload = cedJetProductManagement::getInstance();
			$mass_upload->uploadProducts($file_type,$send_sku);

		}

		if(!empty($new_array)){
			$this->upload_mass_product_through_cron($new_array);
		}
		else{
			$complete_mass_upload = 'Your Mass Product Upload Done successfully';
			update_option('mass_complete',$complete_mass_upload);
			delete_option('mass_running');
			/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_upload.txt","a") or die("Can't open the requested file");
			fwrite($fp,("\n  -----------------------------------  End Mass Product Upload  --------------------------------------------\n"));
			fclose($fp); */
			wp_clear_scheduled_hook('mass_product_upload_done');
		}

	}
	//mass product upload end
	/**
	 * Mass archieve for jet
	 */
	//archive product on jet
	public function mass_archive_product(){

		if(isset($_POST['mass_upload_type'])){

			$all_product_id = array();
			$all_product_id = $_POST['all_mass_product_id'];

			$running = get_option('mass_running');
			if(!empty($running)){
				echo $running;
				exit;
			}
			/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/mass_archieve.txt","a") or die("Can't open the requested file");
			fwrite($fp, ("Mass Product Archieve Upload start on date: ".date('Y-m-d H:i:s')));
			fwrite($fp,("\n"));
			fclose($fp); */
			$this->call_archive_schedule_function($all_product_id);

		}
	}
	
	//hook define for mass archieve product
	public function call_archive_schedule_function($all_product_id){

		if ( !wp_next_scheduled( 'mass_product_archieve_done' ) ) {
			wp_schedule_single_event(time(),'mass_product_archieve_done',array($all_product_id));
		}
	}
	
	/**
	 * Dashboard reporting
	 * vacation_mode()
	 *   */
	
	public function vacation_mode(){
		update_option('check_vacation', 'on');
		$all_product_id = get_option('all_product_ids',false);
		$running = get_option('mass_running');
		if(!empty($running)){
			echo $running;
			exit;
		}
		$this->call_archive_schedule_function($all_product_id);
	}
	/**
	 * Dashboard reporting
	 * vacation_mode_off()
	 *   */
	public function vacation_mode_off(){
		update_option('check_vacation', 'off');
	$all_product_id = get_option('all_product_ids',false);
	$running = get_option('mass_running');
			if(!empty($running)){
				echo $running;
				exit;
			}
			$this->call_unarchive_schedule_function($all_product_id);
	}
	
	//for cron
	public function mass_product_archieve_through_cron($all_product_id){


		/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/mass_archieve.txt","a") or die("Can't open the requested file");
		fwrite($fp, ("Mass Archieve Product for product id jet extension: "));
		foreach($all_product_id as $key => $id){
			fwrite($fp,$id.',');
		}
		fwrite($fp,("\n"));
		fclose($fp); */

		$send_sku  = array();
		$new_array = array();
		$send_sku  = array_slice($all_product_id,0,10);
		$new_array = array_diff($all_product_id,$send_sku);


		if(!empty($send_sku)){

			//print_r($send_sku);
			$mass_upload_running = 'There is also performing mass Archieve upload please ,try later';
			update_option('mass_running',$mass_upload_running);
			$type 			= 	'MerchantSKUs';
			$file_id 		= 	'';

			/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/mass_archieve.txt","a") or die("Can't open the requested file");
			fwrite($fp, ("Mass Archieve Product for product id through cron jet extension: "));
			foreach($send_sku as $key => $id){
				fwrite($fp,$id.',');
			}
			fwrite($fp,("\n"));
			fclose($fp); */

			$file_type = 'Archive';

			require_once CEDJET_DIRPATH.'/includes/class-productManagement.php';
			$mass_archieve = cedJetProductManagement::getInstance();
			$mass_archieve->uploadProducts($file_type,$send_sku);

		}

		if(!empty($new_array)){
			$this->mass_product_archieve_through_cron($new_array);
		}
		else{
			$complete_mass_upload = 'Your Mass Archieve Product Upload Done successfully';
			update_option('mass_complete',$complete_mass_upload);
			delete_option('mass_running');
			/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/mass_archieve.txt","a") or die("Can't open the requested file");
			fwrite($fp,("\n  -----------------------------------  End Mass Archive Product  --------------------------------------------\n"));
			fclose($fp); */
			wp_clear_scheduled_hook('mass_product_archieve_done');
		}

	}
	
	// mass archieve product end from jet
	/**
	 * Mass Unarchieve product
	 */
	
	//unarchive product on jet
	public function mass_unarchive_product(){


		if(isset($_POST['mass_upload_type'])){

			$all_product_id = array();
			$all_product_id = $_POST['all_mass_product_id'];

			$running = get_option('mass_running');
			if(!empty($running)){
				echo $running;
				exit;
			}
			/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/mass_unarchieve.txt","a") or die("Can't open the requested file");
			fwrite($fp, ("Mass Product Unarchieve start on date: ".date('Y-m-d H:i:s')));
			fwrite($fp,("\n"));
			fclose($fp); */
			$this->call_unarchive_schedule_function($all_product_id);

		}

	}
	
	
	//hook define for mass archieve product
	public function call_unarchive_schedule_function($all_product_id){

		if ( !wp_next_scheduled( 'mass_product_unarchieve_done' ) ) {
			wp_schedule_single_event(time(),'mass_product_unarchieve_done',array($all_product_id));
		}

	}
	
	
	//for cron
	public function mass_product_unarchieve_through_cron($all_product_id){


		/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/mass_unarchieve.txt","a") or die("Can't open the requested file");
		fwrite($fp, ("Mass unarchieve Product for product id jet extension: "));
		foreach($all_product_id as $key => $id){
			fwrite($fp,$id.',');
		}
		fwrite($fp,("\n"));
		fclose($fp); */

		$send_sku  = array();
		$new_array = array();
		$send_sku  = array_slice($all_product_id,0,10);
		$new_array = array_diff($all_product_id,$send_sku);


		if(!empty($send_sku)){

			//print_r($send_sku);
			$mass_upload_running = 'There is also performing mass unarchieve upload please ,try later';
			update_option('mass_running',$mass_upload_running);
			$type 			= 	'MerchantSKUs';
			$file_id 		= 	'';

			/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/mass_unarchieve.txt","a") or die("Can't open the requested file");
			fwrite($fp, ("Mass unarchieve Product for product id through cron jet extension: "));
			foreach($send_sku as $key => $id){
				fwrite($fp,$id.',');
			}
			fwrite($fp,("\n"));
			fclose($fp); */

			$file_type = 'Unarchive';

			require_once CEDJET_DIRPATH.'/includes/class-productManagement.php';
			$mass_archieve = cedJetProductManagement::getInstance();
			$mass_archieve->uploadProducts($file_type,$send_sku);

		}

		if(!empty($new_array)){
			$this->mass_product_unarchieve_through_cron($new_array);
		}
		else{
			$complete_mass_upload = 'Your Mass unarchieve Product Upload Done successfully';
			update_option('mass_complete',$complete_mass_upload);
			delete_option('mass_running');
			/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/mass_unarchieve.txt","a") or die("Can't open the requested file");
			fwrite($fp,("\n  -----------------------------------  End Mass Archive Product  --------------------------------------------\n"));
			fclose($fp); */
			wp_clear_scheduled_hook('mass_product_unarchieve_done');
		}
	}
	
	public function errorFileDeletion(){
		
		if(isset($_POST)){
			
			$jet_file_id 		= 	$_POST['jet_file_id'];
			
			$this->dbHelper->deleteErrorFile($jet_file_id);
			
		}
	}

	/**
	 * Delete jet order
	 */
	public function delete_jet_order(){
		global $wpdb;
		$table_name 	= $wpdb->prefix.'jet_order_detail';

		if(isset($_POST['action'])){

			$unique_id = $_POST['all_delete_product_id'];


			if(count($unique_id) > 0){
				$ids 	   = implode("','",$unique_id);
				$qry1	   = "DELETE FROM `$table_name` WHERE woocommerce_order_id IN ('$ids');";
				$_SESSION['order_delete_success'][] =  "Order Id ". $ids .'  deleted Successfully ';
				$wpdb->query($qry1);

				foreach($unique_id as $key => $wooOrderID){
					wp_delete_post($wooOrderID,true);
				}

			}else{
				$_SESSION['order_delete_error'][] = 'Order Can\'t be deleted Due to some error,please try Again';
			}
		}

	}
	
	/**
	 * Delete Jet error file 
	 */
	public function delete_error_file_jet(){
		
		global $wpdb;
		$table_name 	= $wpdb->prefix.'jet_errorfile_info';

		if(isset($_POST['action'])){

			$unique_id = $_POST['all_error_file_id'];


			if(count($unique_id) > 0){
				$ids 	   = implode("','",$unique_id);
				$qry1	   = "DELETE FROM `$table_name` WHERE id IN ('$ids');";
				$wpdb->query($qry1);
				$_SESSION['error_file_success'][] =  $ids .' File deleted Successfully ';

			}else{
				$_SESSION['file_error'][] = 'Error file Can\'t be deleted Due to some error,please try Again';
			}
		}
	}
	
	/**
	 * Delete Reject order 
	 */
	public function delete_reject_order(){

		global $wpdb;
		$table_name 	= $wpdb->prefix.'jet_order_import_error';

		if(isset($_POST['action'])){

			$unique_id = $_POST['all_reject_order_id'];

			if(count($unique_id) > 0){
				$ids 	   = implode("','",$unique_id);
				$qry1	   = "DELETE FROM `$table_name` WHERE id IN ('$ids');";
				$wpdb->query($qry1);
				$_SESSION['delete_reject_success'][] =  "Order Id ". $ids .'  deleted Successfully ';

			}else{
				$_SESSION['delete_reject_fail'][] = 'Order Can\'t be deleted Due to some error,please try Again';
			}
		}
	}
	
	/**
	 * Delete return order
	 */
	public function delete_return_order(){

		global $wpdb;
		$table_name 	= $wpdb->prefix.'jet_return_detail';

		if(isset($_POST['action'])){

			$unique_id = $_POST['all_return_order_id'];
			if(count($unique_id) > 0){
				$ids 	   = implode("','",$unique_id);
				$qry1	   = "DELETE FROM `$table_name` WHERE id IN ('$ids');";
				$wpdb->query($qry1);
				$_SESSION['delete_return_success'][] =  "Return Id ". $ids .'  deleted Successfully ';

			}else{
				$_SESSION['delete_return_fail'][] = 'Return Can\'t be deleted Due to some error,please try Again';
			}
		}
	}
	
	/**
	 * Mass Product inventory upload 
	 */
		//uploading mass product on jet dsfds
	public function uploading_mass_product_inventory(){
		
		$args 		= 	array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1 );
		$products 	= 	new WP_Query( $args );
		$max_range 	=	$products->found_posts;
		
		$running 	= 	get_option('mass_inventory_running');
		
		if(!empty($running)){
			echo $running;
			exit;
		}
		
	/* 	$fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_inventory_upload.txt","a") or die("Can't open the requested file");
		fwrite($fp, ("Mass Product Inventory Upload start on date: " .date('Y-m-d H:i:s')));
		fwrite($fp,("\n"));
		fclose($fp); */
		
		
		
		
		//under jet review inventory upload
		$under_jet_review_status = rawurlencode("Under Jet Review");
		
		/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_inventory_upload.txt","a") or die("Can't open the requested file");
		fwrite($fp, ("Mass Product Inventory Upload for status: Under Jet Review"));
		fwrite($fp,("\n"));
		fclose($fp); */
		$under_jet_review_sku = $this->get_sku_by_status($under_jet_review_status,$max_range);

		if (count($under_jet_review_sku) != 0)
		{
			$this->upload_mass_product_inventory_through_cron($under_jet_review_sku);
		}
		//End under jet review inventory upload
		

		
		//missing listing data inventory upload
		$missing_listing_data_status = rawurlencode("Missing Listing Data");
		/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_inventory_upload.txt","a") or die("Can't open the requested file");
		fwrite($fp, ("Mass Product Inventory Upload for status: Missing Listing Data"));
		fwrite($fp,("\n"));
		fclose($fp); */
		$missing_listing_data_sku = $this->get_sku_by_status($missing_listing_data_status,$max_range);

		if (count($missing_listing_data_sku) != 0)
		{
			$this->upload_mass_product_inventory_through_cron($missing_listing_data_sku);
		}
		//end missing lsiting data
		
		//available for sale inventory upload
		$available_for_purchase_status = rawurlencode("Available for Purchase");
		/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_inventory_upload.txt","a") or die("Can't open the requested file");
		fwrite($fp, ("Mass Product Inventory Upload for status: Available for Purchase"));
		fwrite($fp,("\n"));
		fclose($fp); */
		$available_for_purchase_sku = $this->get_sku_by_status($available_for_purchase_status,$max_range);

		
		if (count($available_for_purchase_sku) != 0)
		{
			$this->upload_mass_product_inventory_through_cron($available_for_purchase_sku);
		}
		//end available for sale

	}


		/**
		 * Actual mass product 
		 * @param unknown $all_product_id
		 */
		public function upload_mass_product_inventory_through_cron($all_product_id){

			
			
			$send_sku  = array();
			$new_array = array();
			$send_sku  = array_slice($all_product_id,0,10);
			$new_array = array_diff($all_product_id,$send_sku);

			if(!empty($send_sku)){

				$mass_upload_running = 'There is also performing mass Inventory upload please ,try later';
				update_option('mass_inventory_running',$mass_upload_running);
				
				//$fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_inventory_upload.txt","a") or die("Can't open the requested file");
				
				foreach($send_sku as $key => $pid){
					//fwrite($fp, ("\n ID: ".$pid));
					$inventory_array = $this->get_product_inventory_information($pid);
					
					if(!empty($inventory_array)){
						//($fp, ("Actual Product Inventory Upload For: ".$pid));
						//fwrite($fp, ("Actual Product Inventory-: ".$inventory_array[$pid]['fulfillment_nodes'][0]['quantity']));
						
						$inventry	=	$this->fileUploadHelper->CPutRequest('/merchant-skus/'.trim($pid).'/inventory',json_encode($inventory_array));
					}
				}//END LOOP FOR SEND SKU
				
				//fwrite($fp,("\n"));
				//($fp);
				}//end IF FOR SKU ARRAY

				if(!empty($new_array)){
					$this->upload_mass_product_inventory_through_cron($new_array);
				}
				else{
					$complete_mass_upload = 'Your Mass Inventory Product Upload Done successfully';
					update_option('mass_inventory_complete',$complete_mass_upload);
					$_SESSION['mass_inventory_complete'] = $complete_mass_upload;
					delete_option('mass_inventory_running');
					/* $fp = fopen($_SERVER['DOCUMENT_ROOT']."/bulk_inventory_upload.txt","a") or die("Can't open the requested file");
					fwrite($fp,("\n  -----------------------------------  End Mass Inventory Product Upload  --------------------------------------------\n"));
					fclose($fp); */
					
				}

			}

		/**
		 * 
		 * @param unknown $status
		 * @param unknown $max_range
		 * @return multitype:mixed
		 */
		public function get_sku_by_status($status,$max_range){

			$response 			= 	$this->fileUploadHelper->CGetRequest('merchant-skus/bystatus/'.$status.'/0/'.$max_range);
			$response_decode 	= 	json_decode($response,true);

			//CGetRequest('/portal/merchantskus?from=0&size=$max_range&statuses='.$raw_encode);
			$all_sku = array();
			if(!empty($response_decode)){
				if(is_array($response_decode) && isset($response_decode['sku_urls']) && count($response_decode['sku_urls'])>0){
					foreach ($response_decode['sku_urls'] as $sku) {
						$all_sku[] 	= 	str_replace('merchant-skus/','',$sku);
						$sku_id 	= 	str_replace('merchant-skus/','',$sku);
						$decode 	=  	rawurldecode($status);
						update_post_meta((int)$sku_id,'jet_product_status',(string)$decode);
					}
				}
			}else{
				$get_response 			= 	$this->fileUploadHelper->CGetRequest('/portal/merchantskus?from=0&size='.$max_range.'&statuses='.$status);
				$response_decode_set 	= 	json_decode($get_response,true);
				$all_merchant_sku		=	$response_decode_set['merchant_skus'];	
				foreach($all_merchant_sku as $key => $product_data){
					$sku 	= $product_data['merchant_sku'];
					$decode =  rawurldecode($status);
					update_post_meta((int)$sku,'jet_product_status',(string)$decode);
					$all_sku[] = $sku;
				}

			}
			return $all_sku;
		}
		
		/**
		 * Get Actual Inventory and type for product
		 */
		public function get_product_inventory_information($product_id){

			
			$select_stock				=	get_post_meta($product_id,'jetStockSelect',true);
			
			if(empty($select_stock))
				$select_stock = 'central';
			
			$select_stock				=	trim($select_stock);
			
			$selected_value				=  trim(get_post_meta($product_id,'jetStock',true));

			$jet_node_id	= get_option( 'jet_node_id');
			$jet_node_id    = json_decode($jet_node_id);

			if(!empty($jet_node_id)){

				//if stock is set to centarl stock
				if('central' == $select_stock){
					$stock	 						= 	get_post_meta($product_id,'_stock',true);
					if(empty($stock)){
						$_SESSION['upload_inventory_error'][] = __('please set stock of the product for product: '.$product_name.'','woocommerce-jet-integration');
					}
					else{
						foreach($jet_node_id as $key => $fullfillment_id){
							// Add price
							$qty	= $stock;
							$node1['fulfillment_node_id']="$fullfillment_id";
							$node1['quantity']=(int)$qty;
							$inventory[$product_id]['fulfillment_nodes'][]=$node1; // inventory
						}
					}
				}

				//if stock is set to default stock 99
				if('default' == $select_stock){
					foreach($jet_node_id as $key => $fullfillment_id){
						// Add price
						$qty	= 99;
						$node1['fulfillment_node_id']="$fullfillment_id";
						$node1['quantity']=(int)$qty;
						$inventory[$product_id]['fulfillment_nodes'][]=$node1; // inventory
					}
				}

				//if select other price for store
				if('other' == $select_stock){
					$stock 						= 	get_post_meta($product_id,'jetStock',true);

					if(empty($stock)){
						$_SESSION['upload_inventory_error'][] = __('Please set Other Stock option for product: '.$product_name.'','woocommerce-jet-integration');
					}else{

						foreach($jet_node_id as $key => $fullfillment_id){
							// Add price
							$qty	= $stock;
							$node1['fulfillment_node_id']="$fullfillment_id";
							$node1['quantity']=(int)$qty;
							$inventory[$product_id]['fulfillment_nodes'][]=$node1; // inventory
						}
						
					}
				}

				//if select fullmillment type to set stock

				if('fullfillment_wise' == $select_stock){

					foreach($jet_node_id as $key => $fullfillment_id){

						$stock 	=	 get_post_meta($product_id, 's_'.$fullfillment_id, true);//$this->product['p_'.$fullfillment_id];
						if(empty($stock)){
							$_SESSION['upload_inventory_error'][] = __('please set stock for fullfillment node id : '.$fullfillment_id  .' for product: '.$product_name.'','woocommerce-jet-integration');
							//return;
						}

						$qty	= $stock;
						$node1['fulfillment_node_id']="$fullfillment_id";
						$node1['quantity']=(int)$qty;
						$inventory[$product_id]['fulfillment_nodes'][]=$node1; // inventory

					}
					
				}
				
				return $inventory;		
			}else{
				$_SESSION['upload_inventory_error'][] = __('Fullfillment is not set please go to jet configuration settings and save fullfillment settings','woocommerce-jet-integration');
				return ;
			}
		}
		
		
		//mass product upload end
		
		/**
		 * delete useless files.
		 */
		public function delete_useless_files($path){

			/* global $wpdb;
			$table_name  = $wpdb->prefix.'jet_errorfile_info';
			$qry    = "DELETE FROM `$table_name` WHERE 1;";
			$wpdb->query($qry); */

			if (is_dir($path) === true)
			{
				$files = array_diff(scandir($path), array('.', '..'));

				foreach ($files as $file)
				{
					$this->delete_useless_files(realpath($path) . '/' . $file);
				}

				return rmdir($path);
			}

			else if (is_file($path) === true)
			{
				return unlink($path);
			}

			return false;
		}
		
		/**
		 * Get All jet Categories
		 */
		public function get_jet_all_categories(){

			if(check_admin_referer('edmapcat','edit_sec')){
				$cat_action		=	'get_all_jet_category';
				$cat_url 		=	"http://demo.cedcommerce.com/jet/demo_get_category.php?token_id=2016_ced_jet_team&action=$cat_action";
			//$cat_url 		=	"http://demo.cedcommerce.com/jet/demo_get_category.php?token_id=2016_ced_jet_team&action=get_all_jet_category";
				$ch_cat  = curl_init();
				curl_setopt($ch_cat, CURLOPT_URL, $cat_url);
				curl_setopt($ch_cat, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch_cat, CURLOPT_HEADER, 0);
				curl_setopt($ch_cat, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch_cat, CURLOPT_CONNECTTIMEOUT ,0);
			curl_setopt($ch_cat, CURLOPT_TIMEOUT, 400); //timeout in seconds
			$get_category = curl_exec($ch_cat);
			$get_category = str_replace('][',',', $get_category);
			$get_all = json_decode($get_category);
			
			echo json_encode($get_all);exit;
		}
	}


	public function update_edit_cat(){
		global $update_cat;
		if(!empty($_SESSION['all_cat_set'])){
			echo json_encode($_SESSION['all_cat_set']);exit;
		}
		else
		{
			$cat_action		=	'get_all_jet_category';
			$cat_url 		=	"http://demo.cedcommerce.com/jet/demo_get_category.php?token_id=2016_ced_jet_team&action=$cat_action";
				//$cat_url 		=	"http://demo.cedcommerce.com/jet/demo_get_category.php?token_id=2016_ced_jet_team&action=get_all_jet_category";
			$ch_cat  = curl_init();
			curl_setopt($ch_cat, CURLOPT_URL, $cat_url);
			curl_setopt($ch_cat, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch_cat, CURLOPT_HEADER, 0);
			curl_setopt($ch_cat, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch_cat, CURLOPT_CONNECTTIMEOUT ,0);
				curl_setopt($ch_cat, CURLOPT_TIMEOUT, 400); //timeout in seconds
				$get_category = curl_exec($ch_cat);
				$get_category = str_replace('][',',', $get_category);
				$get_all = json_decode($get_category);
				echo json_encode($get_all);	exit;
			}

		}
		
		/**
		 * Validate jet products
		 */
		
		public function validate_all_jet_product(){
			
			$jet_node_id = get_option('jet_node_id');
			$jet_node_id = json_decode($jet_node_id);
			foreach ($jet_node_id as $key => $value) {
				$fulfillment_node_id=$value;
			}
			// print_r($jet_node_id);die;
			$all_product_ids  		= 	$_POST['all_list_product_id'];
			$get_all_data 			=	$this->product_validate->validate_jet_product_data($all_product_ids);
			
			$all_data 				=   $get_all_data['all_data'];
			//echo json_encode($all_data);die("xdc");
			$all_sku_data			=	$all_data['sku'];
			//echo json_encode($all_sku_data);die("xdc");
			$all_price_data			=	$all_data['price'];
			
			$all_inventory_data		=	$all_data['inventory'];
			
			unset($_SESSION['upload_common_msg']);
			unset($_SESSION['upload_product_error']);

			$all_new_price_data 	=   array();
			$all_new_inventory_data	=	array();
			
			if(!empty($all_price_data)){
				foreach($all_price_data as $index => $price){
					
					$price_pid 		= key($price);
					$all_new_price_data[$price_pid] = $all_price_data[$index][$price_pid];
			// print_r($all_new_price_data);
				}
			}
			// print_r($all_new_price_data);
			if(!empty($all_inventory_data)){
				foreach($all_inventory_data as $index => $stock){
					$stock_pid 		= key($stock);
					$all_new_inventory_data[$stock_pid] = $all_inventory_data[$index][$stock_pid];
				}
			}
			
			$validated_data 	= 	array();
			

			try{
				if(!empty($all_sku_data)){
					foreach($all_sku_data as $key => $product){

						$error_msg			=	array();
						// print_r($products);
						$pid		=	'';
						if(WC()->version < "3.0.0")
						{
					
							$pid 		= 	key($product);
							$products 	= 	new WC_Product($pid);
							$type		=	''; 
							$pro_type   =  $products->post->post_parent;	
							if(isset($pro_type) && $pro_type != 0){
						// print_r($pro_type);
								$new_pid 	= 	$pro_type;
								$type       =   'variable';
							}elseif(isset($pro_type) && $pro_type == 0){
								$new_pid = $pid;
								$type = 'simple';
							}
							$quantity					=	'';
							$main_image_url				=	'';
							$regular_price				=	'';
					//sku details
							$product_title 				= 	$product[$pid]['product_title'];
							$jet_browse_node_id 		= 	$product[$pid]['jet_browse_node_id'];
							$brand						=	$product[$pid]['brand'];
							// print_r($brand);echo'cxcxcx';
							$product_tax_code			=	$product[$pid]['product_tax_code'];
							$product_description		=	$product[$pid]['product_description'];
							$main_image_url				=	$product[$pid]['main_image_url'];
							$map_implementation			=	$product[$pid]['map_implementation'];
							$map_price					=	$product[$pid]['map_price'];
							$mfr_part_number			=	$product[$pid]['mfr_part_number'];

							$standard_product_code		=	$product[$pid]['standard_product_codes'][0]['standard_product_code'];
							$standard_product_code_type	=	$product[$pid]['standard_product_codes'][0]['standard_product_code_type'];
							$ASIN 						= 	'';
							if(isset($product[$pid]['ASIN']))
								$ASIN	=	$product[$pid]['ASIN'];
					//price details
							if(!empty($all_new_price_data)){
								$regular_price				=	$all_new_price_data[$pid]['price'];
								$fulfillment_node_id		=	$all_new_price_data[$pid]['fulfillment_nodes'][0]['fulfillment_node_id'];
								$fulfillment_node_price		=	$all_new_price_data[$pid]['fulfillment_nodes'][0]['fulfillment_node_price'];

						//regular price validation
								if(empty($regular_price))
									$error_msg[] = __('Regular Price Missing','woocommerce-jet-integration');
								elseif(empty($fulfillment_node_price) || $fulfillment_node_price == 0)
									$error_msg[] = __('Product Price Missing','woocommerce-jet-integration');
								elseif(empty($fulfillment_node_id))
									$error_msg[] = __('Fullfillment Node missing','woocommerce-jet-integration');

							}else{
								$error_msg[] = __('Fullfillment Node missing','woocommerce-jet-integration');
							}
					//inventory details
							if(!empty($all_new_inventory_data)){
								$qty_fulfillment_node_id	=	$all_new_inventory_data[$pid]['fulfillment_nodes'][0]['fulfillment_node_id'];
								$quantity					=	$all_new_inventory_data[$pid]['fulfillment_nodes'][0]['quantity'];

						//Inventory Pricing
								if(empty($quantity))
									$error_msg[] = __('Quantity Missing','woocommerce-jet-integration');
							}

							if(empty($product_title))
								$error_msg[] = __('Product Title Missing','woocommerce-jet-integration');
							if(empty($jet_browse_node_id))
								$error_msg[] = __('Category Missing','woocommerce-jet-integration');
							if(empty($product_description))
								$error_msg[] = __('Product Description Missing','woocommerce-jet-integration');
							if(empty($main_image_url))
								$error_msg[] = __('Main Image Missing','woocommerce-jet-integration');

					//map implementation and map price valdation
							if(isset($product[$pid]['map_implementation'])){
								if(!empty($map_implementation) && $map_implementation == '102' ){
									if(empty($map_price))
										$error_msg[] = __('Map Implementation set, but Map Price Missing','woocommerce-jet-integration');
								}
							}

					// standard product code validates
							$codetype  						= 	trim($standard_product_code_type);
							$codevalue						= 	trim($standard_product_code);
							$standard_value_count			=  	strlen($codevalue);
					//secho $codetype;die;
							if(!empty($ASIN)  && strlen($ASIN) != 10){
								$error_msg[] = __('Asin Value Missing or Must be 10 character','woocommerce-jet-integration');
							}

							if($codetype == 'select'){
								$codetype = '';
							}

							if(!empty($codetype)){
								if(empty($codevalue)){
									$error_msg[] = __('Standard code value is missing.','woocommerce-jet-integration');
								}elseif($codetype == 'UPC' && $standard_value_count != 12){
									$error_msg[] = __('UPC value is must be of 12 character.','woocommerce-jet-integration');
								}elseif($codetype == 'upce' && $standard_value_count != 6){
									$error_msg[] = __('UPC-E value is must be of 6 character.','woocommerce-jet-integration');
								}elseif($codetype == 'GTIN-14' && $standard_value_count != 14){
									$error_msg[] = __('GTIN-14 value is must be of 14 character.','woocommerce-jet-integration');
								}elseif($codetype == 'ISBN-13' && $standard_value_count != 13){
									$error_msg[] = __('ISBN-13 value is must be of 13 character.','woocommerce-jet-integration');
								}elseif($codetype == 'ISBN-10' && $standard_value_count != 10){
									$error_msg[] = __('ISBN-10 value is must be of 10 character.'.$this->product[name].'','woocommerce-jet-integration');
								}elseif($codetype == 'EAN' && $standard_value_count != 13){
									$error_msg[] = __('EAN value is must be of 13 character.','woocommerce-jet-integration');
								}
							}

							if(empty($codetype) && empty($ASIN)){
								if(empty($mfr_part_number)){
									$error_msg[] = __('mfr part number is missing','woocommerce-jet-integration');
								}
								if(empty($brand)){
									$error_msg[] = __('Brand is missing','woocommerce-jet-integration');
								}
							}

						}
						else
						{
							$pid 		= 	key($product);
							$products 	= 	wc_get_product($pid);
							$ced_product_type=$products->get_type();
							$type		=	''; 
							if($ced_product_type=='variation')
							{
								$pro_type   =  $products->get_parent_id();
								// print_r($pro_type);	die;
							}
							else
							{
								$pro_type   =  $products->get_parent_id();
								
							}
							if(isset($pro_type) && $pro_type != 0){
						// print_r($pro_type);
								$new_pid 	= 	$pro_type;
								$type       =   'variable';
							}elseif(isset($pro_type) && $pro_type == 0){
								$new_pid = $pid;
								$type = 'simple';
							}
							$quantity					=	'';
							$main_image_url				=	'';
							$regular_price				=	'';
						//sku details

									// print_r($products->get_data()->meta_);
							$pictureUrl = wp_get_attachment_image_url( get_post_meta( $pid,'_thumbnail_id',true), 'full' ) ? wp_get_attachment_image_url( get_post_meta( $pid,'_thumbnail_id',true), 'full' ) : '';
							$product_title 				= 	$product[$pid]['product_title'];
							$jet_browse_node_id 		= 	$product[$pid]['jet_browse_node_id'];
							$brand						=	$product[$pid]['brand'];
							// print_r($brand);echo'cxcxcx';
							$product_tax_code			=	$product[$pid]['product_tax_code'];
							$product_description		=	$product[$pid]['product_description'];
							$main_image_url				=	$pictureUrl;
							$map_implementation			=	$product[$pid]['map_implementation'];
							$map_price					=	$product[$pid]['map_price'];
							$mfr_part_number			=	$product[$pid]['mfr_part_number'];

							$standard_product_code		=	$product[$pid]['standard_product_codes'][0]['standard_product_code'];
							$standard_product_code_type	=	$product[$pid]['standard_product_codes'][0]['standard_product_code_type'];
							$ASIN 						= 	'';
							if(isset($product[$pid]['ASIN']))
								$ASIN	=	$product[$pid]['ASIN'];
					//price details
							if(!empty($all_new_price_data)){
								$regular_price				=	$all_new_price_data[$pid]['price'];
								$fulfillment_node_id		=	$all_new_price_data[$pid]['fulfillment_nodes'][0]['fulfillment_node_id'];
								$fulfillment_node_price		=	$all_new_price_data[$pid]['fulfillment_nodes'][0]['fulfillment_node_price'];

						//regular price validation
								if(empty($regular_price))
									$error_msg[] = __('Regular Price Missing','woocommerce-jet-integration');
								elseif(empty($fulfillment_node_price) || $fulfillment_node_price == 0)
									$error_msg[] = __('Product Price Missing','woocommerce-jet-integration');
								elseif(empty($fulfillment_node_id))
									$error_msg[] = __('Fullfillment Node missing','woocommerce-jet-integration');

							}else{
								$error_msg[] = __('Fullfillment Node missing','woocommerce-jet-integration');
							}
					//inventory details
							if(!empty($all_new_inventory_data)){
								$qty_fulfillment_node_id	=	$all_new_inventory_data[$pid]['fulfillment_nodes'][0]['fulfillment_node_id'];
								$quantity					=	$all_new_inventory_data[$pid]['fulfillment_nodes'][0]['quantity'];
							// print_r($quantity);

						//Inventory Pricing
								if(empty($quantity))
									$error_msg[] = __('Quantity Missing','woocommerce-jet-integration');
							}

							if(empty($product_title))
								$error_msg[] = __('Product Title Missing','woocommerce-jet-integration');
							if(empty($jet_browse_node_id))
								$error_msg[] = __('Category Missing','woocommerce-jet-integration');
							if(empty($product_description))
								$error_msg[] = __('Product Description Missing','woocommerce-jet-integration');
							if(empty($main_image_url))
								$error_msg[] = __('Main Image Missing','woocommerce-jet-integration');

					//map implementation and map price valdation
							if(isset($product[$pid]['map_implementation'])){
								if(!empty($map_implementation) && $map_implementation == '102' ){
									if(empty($map_price))
										$error_msg[] = __('Map Implementation set, but Map Price Missing','woocommerce-jet-integration');
								}
							}

					// standard product code validates
							$codetype  						= 	trim($standard_product_code_type);
							$codevalue						= 	trim($standard_product_code);
							$standard_value_count			=  	strlen($codevalue);
					//secho $codetype;die;
							if(!empty($ASIN)  && strlen($ASIN) != 10){
								$error_msg[] = __('Asin Value Missing or Must be 10 character','woocommerce-jet-integration');
							}

							if($codetype == 'select'){
								$codetype = '';
							}

							if(!empty($codetype)){
								if(empty($codevalue)){
									$error_msg[] = __('Standard code value is missing.','woocommerce-jet-integration');
								}elseif($codetype == 'UPC' && $standard_value_count != 12){
									$error_msg[] = __('UPC value is must be of 12 character.','woocommerce-jet-integration');
								}elseif($codetype == 'upce' && $standard_value_count != 6){
									$error_msg[] = __('UPC-E value is must be of 6 character.','woocommerce-jet-integration');
								}elseif($codetype == 'GTIN-14' && $standard_value_count != 14){
									$error_msg[] = __('GTIN-14 value is must be of 14 character.','woocommerce-jet-integration');
								}elseif($codetype == 'ISBN-13' && $standard_value_count != 13){
									$error_msg[] = __('ISBN-13 value is must be of 13 character.','woocommerce-jet-integration');
								}elseif($codetype == 'ISBN-10' && $standard_value_count != 10){
									$error_msg[] = __('ISBN-10 value is must be of 10 character.'.$this->product[name].'','woocommerce-jet-integration');
								}elseif($codetype == 'EAN' && $standard_value_count != 13){
									$error_msg[] = __('EAN value is must be of 13 character.','woocommerce-jet-integration');
								}
							}

							if(empty($codetype) && empty($ASIN)){
								if(empty($mfr_part_number)){
									$error_msg[] = __('mfr part number is missing','woocommerce-jet-integration');
								}
								if(empty($brand)){
									$error_msg[] = __('Brand is missing','woocommerce-jet-integration');
								}
							}
							// if($type == 'variable'){
							// 	$validated_data[$new_pid]['product_type'] 	= 'variable'; 	
							// 	$validated_data[$new_pid][$pid] = $error_msg;

							// }elseif($type == 'simple'){
							// 	$validated_data[$pid]['product_type'] 		= 'simple';
							// 	$validated_data[$pid][]		 				= $error_msg;
							// }	
							// }
					//assign all error message to product
							// print_r($ced_child_id);

						} 
						
					//assign all error message to product
							if($type == 'variable'){
								$validated_data[$new_pid]['product_type'] 	= 'variable'; 	
								$validated_data[$new_pid][$pid] = $error_msg;

							}elseif($type == 'simple'){
								$validated_data[$pid]['product_type'] 		= 'simple';
								$validated_data[$pid][]		 				= $error_msg;
							}	 
				}//END For loop
				echo json_encode($validated_data);exit;
				
			}//end if
		}catch(Exception $e){
			echo json_encode(array('error'));exit;
		}	
	}
}