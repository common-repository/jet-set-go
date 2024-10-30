<?php 
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Delete all Jet Configuration Details
 *  
 */

if(isset($_POST['reset_api_configuration'])){
	delete_option('jetcom_token');
	delete_option( 'jet_api_url');
	delete_option( 'jet_user');
	delete_option( 'jet_password');
	delete_option( 'jet_node_id');
	delete_option( 'jet_email_id');
	delete_option( 'jet_store_name');

}

/**
 * Save all Api Configuration Details 
 */

if(isset($_POST['api_configuration'])){
	
	$_POST['api_configuration'] = sanitize_post($_POST['api_configuration'] ,'db');
	
	
	$api_url 						= 	$_POST['api_url'];
	$user 							= 	$_POST['user'];
	$password 						= 	$_POST['password'];
	$email_id 						= 	$_POST['email_id'];
	$store_name 					= 	$_POST['store_name'];
	
	$fullfillment_central_settings = $_POST['fullfillment_central_settings'];
	
	$no_of_fullfillment_node = $_POST['all_fullfillment_node'];
	
	$all_node = array();
	for($i = 1; $i<= $no_of_fullfillment_node;$i++){
		$val = $_POST['node_id_'.$i];
		if(!empty($val)){
			$all_node['node_id_'.$val] = $_POST['node_id_'.$i];
		}
	}
	$node_id  = $all_node;
	
	$all_node = json_encode($all_node);
	
	if(empty($email_id))
		$email_id = 'sales@test.com';
	
	if( ' ' == $api_url||  '' == $user || '' == $password || empty($node_id) )
	{
		if(empty($node_id))
		{
			add_settings_error(
				'myUniqueIdentifyer2',
				esc_attr( 'settings_updated' ),
				'Atleast One Fulfillment id is necessary to work with jet.',
				'error'
				);
		}
		else{
			add_settings_error(
				'myUniqueIdentifyer',
				esc_attr( 'settings_updated' ),
				'Enter All Configuration details',
				'error'
				);
		}
	}else{	
		
		update_option( 'jet_api_url',	$api_url);
		update_option( 'jet_user',		$user );
		update_option( 'jet_password',  $password );
		update_option( 'jet_node_id', 	$all_node );
		update_option('jet_return_id', $all_returnid);
		update_option( 'jet_email_id', 	$email_id );
		update_option( 'jet_store_name',$store_name );
		update_option('fullfillment_central_settings',$fullfillment_central_settings);
		update_option( 'sync_product_update',$sync_product_update);

		delete_option('jetcom_token');

		add_settings_error(
			'myUniqueIdentifyer3',
			esc_attr( 'settings_updated' ),
			'Record Updated Successfully.',
			'updated'
			);
	}
	
	
}

// Jet configuration settings
$jet_api_url 	= get_option('jet_api_url') ? get_option('jet_api_url') : 'https://merchant-api.jet.com/api/';
$jet_user 		= get_option('jet_user');
$jet_password 	= get_option('jet_password');
$jet_node_id 	= get_option('jet_node_id');

$jet_node_id = json_decode($jet_node_id);
$jet_email_id = get_option('jet_email_id');
$jet_store_name = get_option('jet_store_name');

$fullfillment_central_settings = get_option('fullfillment_central_settings');
$check = '';
if($fullfillment_central_settings === 'yes'){
	$check = 'checked="checked"';
}


/**
 * Delete all Jet location settings
 *
 */

if(isset($_POST['reset_location_settings'])){

	// return location settings
	delete_option( 'jet_first_address');
	delete_option( 'jet_second_address');
	delete_option( 'jet_city');
	delete_option( 'jet_state');
	delete_option( 'jet_zip_code');
	delete_option('jet_return_id');

}

/**
 * Save all Api Configuration Details
 */

if(isset($_POST['return_location_save'])){

	$_POST['return_location_save'] = sanitize_post($_POST['return_location_save'] ,'db');
// print_r($_POST['return_location_save']);die;
	$no_of_return_id	=	$_POST['all_return_ids'];
	$all_returnid = array();
	for($j = 1; $j<= $no_of_return_id;$j++){
		$rval = $_POST['return_id_'.$j];
		if(!empty($rval)){
			$all_returnid['return_id_'.$rval] = $_POST['return_id_'.$j];
		}
	}
	$return_id_  = $all_returnid;
	$all_returnid = json_encode($return_id_);
	// return configuration
	$first_address 	=	 $_POST['first_address'];
	$second_address = 	 $_POST['second_address'];
	$city 			= 	 $_POST['city'];
	$state 			= 	 $_POST['state'];
	
	$zip_code = intval( $_POST['zip_code'] );
	if ( ! $zip_code ) {
		$zip_code = '';
	}

	if ( strlen( $zip_code ) > 5 ) {
		$zip_code = substr( $zip_code, 0, 5 );
	}

	// return location settings
	update_option( 'jet_return_id',	$all_returnid);
	update_option( 'jet_first_address',	$first_address);
	update_option( 'jet_second_address',$second_address );
	update_option( 'jet_city',  $city );
	update_option( 'jet_state', $state );
	update_option( 'jet_zip_code',$zip_code );

	add_settings_error(
		'myUniqueIdentifyer3',
		esc_attr( 'settings_updated' ),
		'Record Updated Successfully.',
		'updated'
		);

}

// Jet configuration settings

$jet_return_id	=	get_option('jet_return_id');
$jet_return_id	=	json_decode($jet_return_id);

//return location settings
$jet_first_address = get_option('jet_first_address');
$jet_second_address = get_option('jet_second_address');
$jet_city = get_option('jet_city');
$jet_state = get_option('jet_state');
$jet_zip_code = get_option('jet_zip_code');

/**
 * Save all Api Configuration Details
 */

if(isset($_POST['jet_extra_settings'])){

	$_POST['jet_extra_settings'] = sanitize_post($_POST['jet_extra_settings'] ,'db');

	$auto_order_acknowledge			= 	$_POST['automatic_order_acknowledge'];
	$sync_product_update 			=	$_POST['sync_product_update'];
	
	$archieve_parent_product 		=	$_POST['archive_parent_product'];
	
	$default_qty	 				=   $_POST['jet_default_stock'];
	$delivery_day					=	$_POST['jet_delivery_day'];
	$notify_data					=	$_POST['jet_limit_stock_notify'];
	$notify_data_select				=	$_POST['select_jet_limit_stock_notify'];
	$notify_email_id				=	$_POST['notify_email_id'];
	if($default_qty >= 0){
		update_option( 'jet_default_stock',$default_qty);
	}
	else{
		update_option( 'jet_default_stock',0);
	}
	
	update_option('notify_mail_address',$notify_email_id);
	update_option('notify_data_yn',$notify_data);
	update_option('select_data_notify', $notify_data_select);
	
	update_option( 'sync_product_update',$sync_product_update);
	update_option( 'auto_order_acknowledge',$auto_order_acknowledge);
	update_option('archieve_variable_settings', $archieve_parent_product);
	if($delivery_day <= 0)
		update_option('jet_delivery_day',5);
	else 
		update_option('jet_delivery_day',$delivery_day);
	
	add_settings_error(
		'myUniqueIdentifyer3',
		esc_attr( 'settings_updated' ),
		'Record Updated Successfully.',
		'updated'
		);
}

// Jet configuration settings

$auto_acknowledge 			= get_option('auto_order_acknowledge');
$sync_auto_update 			= get_option('sync_product_update');
$archieve_var_settings 		= get_option('archieve_variable_settings');
$delivery_day				= get_option('jet_delivery_day');
$notify_mail_address		= get_option('notify_mail_address');	
$notify_data_yn				= get_option('notify_data_yn');
$select_data_notify			= get_option('select_data_notify');

if(!empty($select_data_notify)){
	if($select_data_notify == 'yes'){
		$notify_yes = 'selected="selected"';
	}else{
		$notify_no = 'selected="selected"';
	}
}else{
	$notify_yes = 'selected="selected"';
}

if(!empty($auto_acknowledge)){
	if($auto_acknowledge == 'yes'){
		$yes = 'selected="selected"';
	}else{
		$no = 'selected="selected"';
	}
}else{
	$yes = 'selected="selected"';
}

//syncronize auto update

if($sync_auto_update == 'yes'){
	$s_yes = 'selected="selected"';
}else{
	$s_no = 'selected="selected"';
}

if(!empty($archieve_var_settings)){
	if($archieve_var_settings == 'yes'){
		$archieve_yes = 'selected="selected"';
	}else{
		$archieve_no = 'selected="selected"';
	}
}else{
	$archieve_yes = 'selected="selected"';
}
$stock = get_option('jet_default_stock');
?>
<div id="jet-loading" class="loading-style-bg" style="display: none;">
	<img src="<?php echo plugin_dir_url(__dir__);?>css/BigCircleBall.gif">
	<p class="loading-content">Processing... Please Wait..</p>
</div>
<div tabindex="0" aria-label="Main content" id="wpbody-content">
	<div id="icon-woocommerce" class="icon32 icon32-woocommerce-settings"><br></div>
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a class="nav-tab nav-tab-active " href="<?php echo site_url();?>/wp-admin/admin.php?page=jet_store_integration"><?php _e('Jet Configuration','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=manage_jet_attributes"><?php _e('Category Mapping','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=jet_profile_settings"><?php _e('Profile','woocommerce-jet-integration');?></a>
		<a class="nav-tab" href="<?php echo site_url();?>/wp-admin/admin.php?page=manage_jet_product"><?php _e('Manage products','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=jet_orders"><?php _e('Orders','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=order_return"><?php _e('Return','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=order_refund"><?php _e('Refund','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=upload_error"><?php _e('Upload Error File','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=mass_cat_assign"><?php _e('Mass Category Assign','woocommerce-jet-integration');?></a>
		
	</h2>
	<div class="alert alert-error">
		<?php if(settings_errors()){
			echo  settings_errors();
		}if(isset($success)){
			echo '<div class="updated"><p>'.$success.'</p></div>';
		}
		?>
	</div>
	
	
	<ul class="subsubsub">
		<li>
			<!-- <a class="current" href="admin.php?page=jet_store_integration"> <?php //_e('JET Details','woocommerce-jet-integration')?></a> -->
			<a href = "<?php echo get_admin_url()?>admin.php?page=jet_store_integration&tab=jet_configuration" class="<?php if($_GET['tab']=='jet_configuration' || !isset($_GET['tab'])){?> nav-tab-active<?php }?>"><?php _e('JET Details','woocommerce-jet-integration');?></a>
			|
		</li>
		<li>
			<!-- <a class="" href="admin.php?page=configure_return_settings"><?php //_e('Return location settings','woocommerce-jet-integration')?></a> -->
			<a href = "<?php echo get_admin_url() ?>admin.php?page=jet_store_integration&tab=return_location" class="<?php if(isset($_GET['tab']) && $_GET['tab']=='return_location' ){?>nav-tab-active<?php }?> "><?php _e('Return location settings','woocommerce-jet-integration');?></a>
			
		</li>
		
		
	</ul>
	<br class="clear">
	<h4><?php _e('Please get the Premium Version of this Plugin ');?></h4>
	<a class="" href="http://cedcommerce.com/woocommerce-extensions/jet-woocommerce-integration" style="background-color: #0073aa; color: white; padding:3px; font-size: 14px; text-decoration: none;" >
		<?php _e('Get Premium Version','woocommerce-jet-integration')?>
	</a>
	<br class="clear">
	<!-- Extra settings for -->
	<?php if(isset($_GET['tab']) && $_GET['tab']=='extra_settings'  && ($_GET['page'] =='jet_store_integration')){?>
	<div class="wrap">
		<h4><?php _e('EXTRA CONFIGURATION SETTINGS','woocommerce-jet-integration');?></h4>
		<form action="#" method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php _e('Auto Order Acknowledge','woocommerce-jet-integration');?></th>
						<td>
							<fieldset>
								<select name="automatic_order_acknowledge" >
									<option value="yes" <?php echo $yes; ?> ><?php _e('Yes','woocommerce-jet-integration');?></option>
									<option value="No" <?php echo $no;?> ><?php _e('No','woocommerce-jet-integration');?></option>
								</select>
							</fieldset>
						</td>
					</tr>	
					
					<tr>
						<th scope="row"><?php _e('Update Product Sync','woocommerce-jet-integration');?></th>
						<td>
							<fieldset><select name="sync_product_update" >
								<option value="No" <?php echo  $s_no;?>><?php _e('No','woocommerce-jet-integration');?></option>
								<option value="yes" <?php echo $s_yes;?>><?php _e('Yes','woocommerce-jet-integration');?></option>
							</select>
						</fieldset>
					</td>
				</tr>
				
				
				<tr>
					<th scope="row"><?php _e('Archieve Child also on Archieve of parent','woocommerce-jet-integration');?></th>
					<td>
						<fieldset><select name="archive_parent_product" >
							<option value="yes" <?php echo $archieve_yes;?>><?php _e('Yes','woocommerce-jet-integration');?></option>
							<option value="No" <?php echo  $archieve_no;?>><?php _e('No','woocommerce-jet-integration');?></option>
						</select>
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Update Inventory with default quantity (when product quantity become zero)','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="text" name="jet_default_stock" value="<?php echo $stock;?>" >
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Enter Delivery Days For any shipment by shipstation','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="text" name="jet_delivery_day" value="<?php echo $delivery_day;?>" >
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Threshold Qty:','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<select name="select_jet_limit_stock_notify" >
							<option value="yes" <?php echo $notify_yes;?>><?php _e('Yes','woocommerce-jet-integration');?></option>
							<option value="No" <?php echo $notify_no;?>><?php _e('No','woocommerce-jet-integration');?></option>
						</select>
						<input type="text" name="jet_limit_stock_notify" value="<?php echo $notify_data_yn; ?>" placeholder="Threshhold Qty" >
						<?php _e('Select yes and fill threshhold limit (inventory will be checked product wise during shipping)','woocommerce-jet-integration');?>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Notification Mail','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="email" class="regular-text" <?php echo $check;?> value="<?php echo $notify_mail_address;?>" placeholder="Enter Email id" id="notify_email_id" name="notify_email_id">
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><h4><?php _e('CRON SETTINGS','woocommerce-jet-integration');?></h4></th>
			</tr>
			<tr>
				<th scope="row"><?php _e('Cron Path For Order','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input size="100" readonly type="text" name="order_cron" value="<?php echo CEDJET_DIRPATH.'includes/class_jet_crone.php';?>" >
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Cron Path For Inventory Syncronization','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input size="100" readonly type="text" name="order_cron" value="<?php echo CEDJET_DIRPATH.'includes/class-inventory-sync-cron.php';?>" >
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Cron Path For Return','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input size="100" readonly type="text" name="order_cron" value="<?php echo CEDJET_DIRPATH.'includes/class-order-return-cron.php';?>" >
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<p class="submit"><input type="submit" value="Save" class="button button-primary" id="submit_api_configuration" name="jet_extra_settings"></p>
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
</form>
</div>
<?php }elseif(isset($_GET['tab']) && $_GET['tab']=='return_location'  && ($_GET['page'] =='jet_store_integration')){ ?>
<div class="wrap">
	<h3><?php _e('RETURN LOCATION SETTINGS.','woocommerce-jet-integration');?></h3>
	<form action="#" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php _e('Return Id','woocommerce-jet-integration');?></th>
					<td>
						<?php _e('Click For Add More Retrun ID:')?><a href="javascript:void(0)" id="add_returnid"><img src="<?php echo plugin_dir_url(__dir__).'files/favicon.png'?>" height="20px" width="20px"></a>
						<div class="multiple_returnid" >
							<?php if(count($jet_return_id) < 1){?>
							<fieldset class="list_returnid" data-id="1" id="returnid_1">
								<input type="text" class="regular-text" value="" placeholder="Enter Return ID" id="return_id_1" name="return_id_1">
							</fieldset>
							<?php }?>	
							<?php if(!empty($jet_return_id)){ $id = 1;?>
							<?php foreach($jet_return_id as $keyid => $valueid){ ?>
							<fieldset class="list_returnid" data-id="<?php echo $id;?>" id="returnid_<?php echo $id?>">
								<input type="text" class="regular-text" value="<?php echo $valueid;?>" placeholder="Enter node id" id="return_id_<?php echo $id?>" name="return_id_<?php echo $id?>">
								<?php if($id > 1){?>
								<a href="javascript:void(0)" class="remove_returnid" id="<?php echo $id?>"><?php _e('Click For remove');?></a>
								<?php }
								$id = $id+1;?>
							</fieldset>
							<?php } 
						} //close if?>	
						<?php if(count($jet_return_id) < 1){?>											
						<input type="hidden" name="all_return_ids" value="1" id="all_return_ids" class="start">
						<?php }else{ $id = $id-1;?>
						<input type="hidden" name="all_return_ids" value="<?php echo $id;?>" id="all_return_ids">
						<?php }?>
					</div>
				</td>
			</tr>


			<tr>
				<th scope="row"><?php _e('First Address','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="text" class="regular-text" value="<?php echo $jet_first_address;?>" placeholder="Enter First Address" id="first_address" name="first_address">
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Second Address','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="text" class="regular-text" value="<?php echo $jet_second_address;?>" placeholder="Second Address" id="second_address" name="second_address">
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('City','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="text" class="regular-text" value="<?php echo $jet_city;?>" placeholder="Enter city" id="city" name="city">
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('State','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="text" class="regular-text" value="<?php echo $jet_state;?>" placeholder="Enter State" id="state" name="state">
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Zip Code','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="text" class="regular-text" value="<?php echo $jet_zip_code;?>" placeholder="Enter Zip code" id="zip_code" name="zip_code">
					</fieldset>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<fieldset>
						<p class="submit"><input type="submit" value="Save" class="button button-primary" id="submit_api_configuration" name="return_location_save">
							<input type="submit" value="Reset" class="button button-primary" id="reset_api_configuration" name="reset_location_settings"></p>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
	</form>	
</div>

<?php }elseif(isset($_GET['tab']) && ($_GET['tab']=='required') && ($_GET['page'] =='jet_store_integration')){?>
<!-- For required things   -->
<div class="required_fiels_div">
	<table class="required_fiels_tabl"><tr>
		<?php if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 5){?>
		
		<td style="color:green" ><img alt="done" src="<?php  echo CEDJETINTEGRATION;?>images/done.png">
			<?php _e('Server has PHP version :','woocommerce-jet-integration');echo PHP_VERSION;
			?>
		</td></tr><tr><?php
	}  
	else {?>

	<td style="color:red"><img alt="done" src="<?php  echo CEDJETINTEGRATION;?>images/cancel.png">	<?php  _e('PHP version is lower then PHP5 please ask hosting provider to upgrade php version' ,'woocommerce-jet-integration');?></td></tr><tr><?php 

}
if  (in_array  ('curl', get_loaded_extensions())) { 
	$version=curl_version();?>

	<td style="color:green"><img alt="done" src="<?php  echo CEDJETINTEGRATION;?>images/done.png"><?php  print_r( 'cURL version:'.$version['version'] ); ?></td></tr><tr><?php 
}
else {?>
<td style="color:red"><img alt="done" src="<?php  echo CEDJETINTEGRATION;?>images/cancel.png"><?php _e('cURL is NOT activated on your server please contact to your hosting provider','woocommerce-jet-integration'); ?></td></tr><tr><?php
}
if(isset($jet_api_url) && isset($jet_user) && isset($jet_password) && isset($jet_node_id) && isset($jet_email_id) && isset($jet_store_name)){?>

<td style="color:green"><img alt="done" src="<?php  echo CEDJETINTEGRATION;?>images/done.png"><?php _e( 'Jet detais Done' , 'woocommerce-jet-integration');?></td></tr><tr><?php 
}else{
	
	?><td style="color:red"><img alt="done" src="<?php  echo CEDJETINTEGRATION;?>images/cancel.png"><?php _e( ' Please complete Jet Details' ,'woocommerce-jet-integration')  ?><a href = "<?php echo get_admin_url()?>admin.php?page=jet_store_integration&tab=jet_configuration" class="<?php if($_GET['tab']=='jet_configuration' || !isset($_GET['tab'])){?> nav-tab-active<?php }?>"><?php _e('JET Details','woocommerce-jet-integration');?></a></td></tr><tr><?php

}

if(!empty($jet_return_id) && !empty($jet_first_address) && !empty($jet_city) && !empty($jet_state) && !empty($jet_zip_code)){
	?>
	<td style="color:green"><img alt="done" src="<?php  echo CEDJETINTEGRATION;?>images/done.png"><?php _e( "Return location settings Done" ,'woocommerce-jet-integration')?></td></tr><tr><?php
}
else {
	
	?><td style="color:red"><img alt="done" src="<?php  echo CEDJETINTEGRATION;?>images/cancel.png"><?php _e( "Please complete Return location settings  ",'woocommerce-jet-integration')?><a href = "<?php echo get_admin_url() ?>admin.php?page=jet_store_integration&tab=return_location" class="<?php if(isset($_GET['tab']) && $_GET['tab']=='return_location' ){?>nav-tab-active<?php }?> "><?php _e('Return location settings','woocommerce-jet-integration');?></a></td></tr><tr><?php

}
?>
</table>
<div class="imp_note_jet"><?php _e('**Please make sure that corn on you server is running properly for jet to fetch order automatically.','woocommerce-jet-integration'); ?></div>
<div class="imp_note_jet"><?php _e('**Please make sure all the details are correct.','woocommerce-jet-integration'); ?></div>	
</div>				
<?php }	elseif(isset($_GET['tab']) && ($_GET['tab']=='jet_configuration') || ($_GET['page'] =='jet_store_integration')){?>


<div class="wrap">
	<h3><?php _e('CONFIGURATION DETAILS.','woocommerce-jet-integration');?></h3>
	<form action="#" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php _e('Api url','woocommerce-jet-integration');?></th>
					<td>
						<fieldset>
							<input type="text" class="regular-text" value="<?php echo $jet_api_url;?>" placeholder="Enter Api url" id="api_url" name="api_url">
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e('Api User','woocommerce-jet-integration');?></th>
					<td>
						<fieldset>
							<input type="text" class="regular-text" value="<?php echo $jet_user;?>" placeholder="Enter User" id="user" name="user">
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Secret','woocommerce-jet-integration');?></th>
					<td>
						<fieldset>
							<input type="password" class="regular-text" value="<?php echo $jet_password;?>" placeholder="Enter Password" id="user_password" name="password">
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Fulfillment Node Ids','woocommerce-jet-integration');?></th>
					<td>
						<?php _e('Click For Add More Fullfillment Node ID:')?><a href="javascript:void(0)" id="add_fullfillment"><img src="<?php echo plugin_dir_url(__dir__).'files/favicon.png'?>" height="20px" width="20px"></a>
						<div class="multiple_fullfillment" >
							<?php if(count($jet_node_id) < 1){?>
							<fieldset class="list_fullfillment" data-id="1" id="fullfillment_1">
								<input type="text" class="regular-text" value="" placeholder="Enter node id" id="node_id_1" name="node_id_1">
							</fieldset>
							<?php }?>	
							<?php if(!empty($jet_node_id)){ $id = 1;?>
							<?php foreach($jet_node_id as $key => $value){ ?>
							<fieldset class="list_fullfillment" data-id="<?php echo $id;?>" id="fullfillment_<?php echo $id?>">
								<input type="text" class="regular-text" value="<?php echo $value;?>" placeholder="Enter node id" id="node_id_<?php echo $id?>" name="node_id_<?php echo $id?>">
								<?php if($id > 1){?>
								<a href="javascript:void(0)" class="remove_fullfillment" id="<?php echo $id?>"><?php _e('Click For remove');?></a>
								<?php }
								$id = $id+1;?>
							</fieldset>
							<?php } 
						} //close if?>	
						<?php if(count($jet_node_id) < 1){?>											
						<input type="hidden" name="all_fullfillment_node" value="1" id="all_fullfillment_node" class="start">
						<?php }else{ $id = $id-1;?>
						<input type="hidden" name="all_fullfillment_node" value="<?php echo $id;?>" id="all_fullfillment_node">
						<?php }?>
					</div>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Email Id','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="email" class="regular-text" <?php echo $check;?> value="<?php echo $jet_email_id;?>" placeholder="Enter Email id" id="email_id" name="email_id">
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Store Name','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<input type="text" class="regular-text" value="<?php echo $jet_store_name;?>" placeholder="Enter Store Name" id="store_name" name="store_name">
					</fieldset>
				</td>
			</tr>	
			<tr>
				<td>
				</td>
				<td>
					<fieldset>
						<p class="submit"><input type="submit" value="Save" class="button button-primary" id="submit_api_configuration" name="api_configuration">
							<input type="submit" value="Reset" class="button button-primary" id="reset_api_configuration" name="reset_api_configuration">	
						</p>
					</fieldset>
				</td>
			</tr>	
		</tbody>
	</table>
</form>	
</div>
<?php }
