<?php if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $wpdb;
$table_name 	= $wpdb->prefix.'jet_profile_settings';

if(isset($_POST['delete'])){
	$unique_id = $_POST['unique_profile_id'];
	if(count($unique_id) > 0){
		$ids 	   = implode("','",$unique_id);	
		$qry1	   = "DELETE FROM `$table_name` WHERE profile_id IN ('$ids');";
		$_SESSION['delete_success'] = 'Profile deleted Successfully ';
		$wpdb->query($qry1);
	}else{
		$_SESSION['delete_error'] = 'Please Select any profile for delete';	
	}		
}

//select data for list profile
$qry 			= "SELECT * FROM `$table_name`;";
$profile_data 	= $wpdb->get_results($qry); 
//echo '<pre>';print_r($profile_data);die;
?>

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
	<br class="clear">
	<h4><?php _e('Please get the Premium Version of this Plugin ');?></h4>
	<a class="" href="http://cedcommerce.com/woocommerce-extensions/jet-woocommerce-integration" style="background-color: #0073aa; color: white; padding:3px; font-size: 14px; text-decoration: none;" >
		<?php _e('Get Premium Version','woocommerce-jet-integration')?>
	</a>
	<br class="clear">
	<?php  if(isset($_SESSION['delete_error'])){
		?><div class="error settings-error notice is-dismissible"><?php echo $_SESSION['delete_error'];?></div><?php 
	}
	unset($_SESSION['delete_error']);
	?>
	
	<?php if(isset($_SESSION['delete_success'])){ ?>
	<div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated">
		<?php echo $_SESSION['delete_success'];?></div>
		<?php }
		unset($_SESSION['delete_success']);
		?>
		
		<br class="clear">
		<div class="wrap">
			<div> 
				<p></p>
			</div>
			<form action="" method="post">
				<div id="delete_profile">
					<a href="<?php echo site_url()?>/wp-admin/admin.php?page=profile_settings&action=add-profile" class="button" ><?php _e('Add New Profile','woocommerce-jet-integration');?> </a>
					<?php if(!empty($profile_data)){?>
					<input type="submit" name="delete" style="color:red" value ="Delete  Profile" class="button" id="delete">
					<?php }?>
				</div>

				<br>
				<table class="wp-list-table widefat fixed striped posts">
					<thead>
						<tr>
							<th style="width:5%" class="manage-column column-cb check-column" id="cb" scope="col">
								<label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All','woocommerce-jet-integration');?></label>
								<input type="checkbox" id="cb-select-all-1">
							</th>
							<th style="width:25%" class="manage-column column-name sortable desc" id="profile_id" scope="col">
								<a href=""><span><?php _e('Profile','woocommerce-jet-integration');?></span>
									<span class="sorting-indicator"></span>
								</a>
							</th>
							<th></th>
							<th style="width:25%" class="manage-column column-edit sortable desc" id="product_edit" scope="col">
								<a href=""><span><?php _e('Edit','woocommerce-jet-integration');?></span>
									<span class="sorting-indicator"></span>
								</a>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($profile_data)){
							foreach($profile_data as $index => $profile){
								?>
								<tr class="iedit author-self level-0 post-<?php ?> type-product status-publish hentry product_row" id="<?php  ?>">
									<th class="check-column" scope="row">
										<label for="cb-select-<?php ?>" class="screen-reader-text"></label>
										<input type="checkbox" value="<?php echo $profile->profile_id;?>" class="unique_check" name="unique_profile_id[]" id="cb-select-<?php echo $profile->profile_id?>">
										<div class="locked-indicator"></div>
									</th>
									<td class="name column-name">
										<span class="profile_name" ><?php echo $profile->profile_name;?></span>
									</td>
									<td></td>
									<td class="name column-edit">
										<span class="profile_edit">
											<a href="<?php echo site_url()?>/wp-admin/admin.php?page=profile_settings&action=edit&profile=<?php echo $profile->profile_id;?>" ><?php _e('Edit Profile','woocommerce-jet-integration');?> </a>
										</span>
									</td>
								</tr>
								<?php }//foreach?>
								<?php }else{ ?>
								<tr class="iedit author-self level-0 post type-product status-publish hentry product_row" >
									<td></td>
									<td><?php _e('No profile Created','woocommerce-jet-integration');?></td>
									<td></td>
									<td></td>
								</tr>	
								<?php }?>
							</tbody>
						</form>		
						<tfoot>
							<tr>
								<th style="width:5%" class="manage-column column-cb check-column" id="cbf" scope="col">
									<label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All','woocommerce-jet-integration');?></label>
									<input type="checkbox" id="cb-select-all-1">
								</th>
								<th style="width:25%" class="manage-column column-name sortable desc" id="profile_id_foot" scope="col">
									<a href=""><span><?php _e('Profile','woocommerce-jet-integration');?></span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th></th>
								<th style="width:25%" class="manage-column column-sku sortable desc" id="product_edit_foot" scope="col">
									<a href=""><span><?php _e('Edit','woocommerce-jet-integration');?></span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>