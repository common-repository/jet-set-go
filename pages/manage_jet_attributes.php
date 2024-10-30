<?php 
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$taxonomy     = 'product_cat';
$orderby      = 'name';
$empty        = 0;

$args = array(
	'taxonomy'     => $taxonomy,
	'orderby'      => $orderby,
	'hide_empty'   => $empty
	);
$all_categories = get_categories( $args );
?>

<div tabindex="0" aria-label="Main content" id="wpbody-content">
	<div id="icon-woocommerce" class="icon32 icon32-woocommerce-settings"><br></div>
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=jet_store_integration"><?php _e('Jet Configuration','woocommerce-jet-integration');?></a>
		<a class="nav-tab nav-tab-active " href="<?php echo site_url();?>/wp-admin/admin.php?page=manage_jet_attributes"><?php _e('Category Mapping','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=jet_profile_settings"><?php _e('Profile','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=manage_jet_product"><?php _e('Manage products','woocommerce-jet-integration');?></a>
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
</div>
<div style="clear: both;"></div>
<div id="jet-loading" class="loading-style-bg" style="display: none;">
	<img src="<?php echo plugin_dir_url(__dir__);?>css/BigCircleBall.gif">
	<p class="loading-content"><?php _e('Processing... Please Wait..','woocommerce-jet-integration')?></p>
</div>
<div id="total-wrap">
	<div id="table-Wrap">
		<table>
			<thead>
				<tr>
					<th><?php _e('Woo Categories','woocommerce-jet-integration');?></th>
					<th colspan="2"><?php _e('Mapped Jet category ID','woocommerce-jet-integration');?></th>
				</tr>
			</thead>
			<tr>
				<td>
					<select id="wooSelectedCat" name="wooSelectedCat">
						<option value="none"><?php _e('select woo category','woocommerce-jet-integration');?></option>
						<?php $categoriesArray	=	array();?>
						<?php foreach($all_categories as $category):?>
							<?php $categoriesArray[$category->term_id]	=	$category->category_nicename; ?>
							<option value="<?php echo $category->term_id; ?>"><?php echo $category->category_nicename; ?></option>
						<?php endforeach;?>
					</select>
				</td>
				<td>
					<select class="jetCatIdTextField" name="jetCatId" id="jetInsertedCatID"></select>
					
					<p class="cat_image_load"><span style="color:blue;"><?php _e('Wait Jet Category Loading....')?></span>
						<img height="20px" width="20px" class="license_loading_image cat_image_loader" src="<?php echo CEDJETINTEGRATION;?>images/loading.gif">
					</p>
				</td>
				<td>
					<p class="jetCatbutton">
						<button name="catMapButton" id="catMapButton"><?php _e('Map','woocommerce-jet-integration');?></button>
						<input type="hidden" name="catmapnoncess" id="catmapnonce" value="<?php echo wp_create_nonce('catmapnonce');?>" >
					</p>
					
				</td>
			</tr>
		</table>
		<table id="mappedWooJetCatListing" >
			<thead>
				<tr>
					<th><?php _e('Woo category','woocommerce-jet-integration');?></th>
					<th><?php _e('Mapped jet cat ID','woocommerce-jet-integration');?></th>
					<th><?php _e('Action','woocommerce-jet-integration');?></th>
				</tr>
			</thead>
			<tbody class="mapped_jet_cat">
				<?php $mappedIds	=	get_option('cedWooJetMapping',true);?>
				<?php
				if(!empty($mappedIds) && $mappedIds !=1){ 
					if(count($mappedIds)):?>
					<?php foreach($mappedIds as $wooid => $jetCatId):?>
						<tr>
							<td><?php echo ($categoriesArray[$wooid]);?></td>
							<td><?php echo $jetCatId;?></td>
							<td>
								<button class="deleteMappedCat" value="<?php echo $wooid;?>" ><?php _e('Delete','woocommerce-jet-integration');?></button>	
							</td>
						</tr>
					<?php endforeach;?>
					
				<?php endif;?>
				<?php }else{
					?>
					<tr>
						<td colspan="3"> <?php _e('No Any Category is Map','woocommerce-jet-integration'); ?></td>
					</tr>
					<?php 
				}?>	
			</tbody>
		</table>
		
	</div>
</div>