<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if(isset($_GET['return_id']))
{
	$id = $_GET['return_id'];
}
else{
	_e('error please try again','woocommerce-jet-integration');
	exit;
}
global $wpdb;
$table_name = $wpdb->prefix.'jet_errorfile_info';
$qry = "SELECT * from `$table_name` where `id` = '$id' ;";
$resultdata = $wpdb->get_results($qry);
$jet_file_id = '';
$error = '';
if(!empty($resultdata)){
	$error_array = json_decode($resultdata[0]->error);
	$jet_file_id = $resultdata[0]->jet_file_id;
	foreach ($error_array as $key =>$error)
	{
		$error = '- '.$error;
	}
}
?>

<?php 
?>
<div id="jet-loading" class="loading-style-bg" style="display: none;">
	<img src="<?php echo plugin_dir_url(__dir__);?>css/BigCircleBall.gif">
	<p class="loading-content">Processing... Please Wait..</p>
</div>

<div tabindex="0" aria-label="Main content" id="wpbody-content">
	<div id="icon-woocommerce" class="icon32 icon32-woocommerce-settings"><br></div>
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a class="nav-tab  " href="<?php echo site_url();?>/wp-admin/admin.php?page=jet_store_integration"><?php _e('Jet Configuration','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=manage_jet_attributes"><?php _e('Category Mapping','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=jet_profile_settings"><?php _e('Profile','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=manage_jet_product"><?php _e('Manage products','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=jet_orders"><?php _e('Orders','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=order_return"><?php _e('Return','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=order_refund"><?php _e('Refund','woocommerce-jet-integration');?></a>
		<a class="nav-tab nav-tab-active" href="<?php echo site_url();?>/wp-admin/admin.php?page=upload_error"><?php _e('Upload Error File','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=mass_cat_assign"><?php _e('Mass Category Assign','woocommerce-jet-integration');?></a>
		

	</h2>
	
	<br class="clear">
	<?php _e('For enable this setting Please get Premium Version : ');?>
	<a class="" href="http://cedcommerce.com/woocommerce-extensions/jet-woocommerce-integration" style="background-color: #0073aa; color: white; padding:3px; font-size: 14px; text-decoration: none;" >
		<?php _e('Get Premium Version','woocommerce-jet-integration')?>
	</a>
	<br class="clear">
</div>