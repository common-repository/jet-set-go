<?php 
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}


global $wpdb;
$table_name 		= 	$wpdb->prefix.'jet_profile_settings';
$qry 				= 	"SELECT `profile_id`, `profile_name` FROM `$table_name` where 1;";
$profile_data 		= 	$wpdb->get_results($qry);

$profileName		= 	array();
$existedProfileIds	=	array();
if(!empty($profile_data) && count($profile_data)){
	
	foreach($profile_data as $retrndData){
		
		$profileName[$retrndData->profile_id]	=	$retrndData->profile_name;
		$existedProfileIds[]					=	$retrndData->profile_id;
	}
}
?>

<div id="jet-loading" class="loading-style-bg" style="display: none;">
	<img src="<?php echo plugin_dir_url(__dir__);?>css/BigCircleBall.gif">
	<p class="loading-content"><?php _e('Processing... Please Wait..','wocommerce-jet-integration');?></p>
</div>

<div tabindex="0" aria-label="Main content" id="wpbody-content">
	<div id="icon-woocommerce" class="icon32 icon32-woocommerce-settings"><br></div>
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a class="nav-tab  " href="<?php echo site_url();?>/wp-admin/admin.php?page=jet_store_integration"><?php _e('Jet Configuration','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=manage_jet_attributes"><?php _e('Category Mapping','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url()?>/wp-admin/admin.php?page=jet_profile_settings"><?php _e('Profile','woocommerce-jet-integration');?></a>
		<a class="nav-tab nav-tab-active" href="<?php echo site_url();?>/wp-admin/admin.php?page=manage_jet_product"><?php _e('Manage products','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=jet_orders"><?php _e('Orders','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=order_return"><?php _e('Return','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=order_refund"><?php _e('Refund','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=upload_error"><?php _e('Upload Error File','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=mass_cat_assign"><?php _e('Mass Category Assign','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=sale_data"><?php _e('Sales Data','woocommerce-jet-integration');?></a>
		<a class="nav-tab " href="<?php echo site_url();?>/wp-admin/admin.php?page=Dash_board"><?php _e('DashBoard','woocommerce-jet-integration');?></a>
	</h2>
	<br class="clear">
	<?php _e('For enable this setting Please get Premium Version : ');?>
	<a class="" href="http://cedcommerce.com/woocommerce-extensions/jet-woocommerce-integration" style="background-color: #0073aa; color: white; padding:3px; font-size: 14px; text-decoration: none;" >
		<?php _e('Get Premium Version','woocommerce-jet-integration')?>
	</a>
	<br class="clear">
	<div >
		<?php if(isset($_SESSION['file_sucess'])){
			?><div class="updated settings-error notice is-dismissible"><?php echo "File Uploaded Successfully" . '<br>';?></div>
			<?php }
			unset($_SESSION['file_sucess']);
			?>
			
			<?php if(isset($_SESSION['mass_inventory_complete'])){
				delete_option('mass_inventory_complete');
				?><div class="updated settings-error notice is-dismissible"><?php echo $_SESSION['mass_inventory_complete'] . '<br>';?></div>
				<?php }
				unset($_SESSION['mass_inventory_complete']);
				?>
			</div>
			<div >
	<?php // unset($_SESSION['upload_product_error']);
	if(isset($_SESSION['upload_common_msg'])){
		$all_error = $_SESSION['upload_common_msg'];	
		foreach($all_error as $index => $error){
			if(is_string($error)):
				?><div class="updated settings-error notice is-dismissible"><?php echo $error.'<br>';?></div><?php
			endif; 
		}
	}
	unset($_SESSION['upload_common_msg']);
	?>
</div>
<div >
	<?php // unset($_SESSION['upload_product_error']);
	if(isset($_SESSION['upload_product_error'])){
		$all_error = $_SESSION['upload_product_error'];	
		foreach($all_error as $index => $error){
			if(is_string($error)):
				?><div class="error settings-error notice is-dismissible"><?php echo $error.'<br>';?></div><?php
			endif; 
		}
	}
	unset($_SESSION['upload_product_error']);
	?>
</div>
<div >
	<?php // unset($_SESSION['upload_product_error']);
	if(isset($_SESSION['Mapped_mass_cat_to_products_manage'])){
		$all_error = $_SESSION['Mapped_mass_cat_to_products_manage'];	
		foreach($all_error as $index => $error){
			if(is_string($error)):
				?><div class="error settings-error notice is-dismissible"><?php echo $error.'<br>';?></div><?php
			endif; 
		}
	}
	unset($_SESSION['Mapped_mass_cat_to_products_manage']);
	?>
</div>
<div >
	<?php // unset($_SESSION['upload_product_error']);
	if(isset($_SESSION['archieve_message'])){
		$all_error = $_SESSION['archieve_message'];	
		foreach($all_error as $index => $error){
			if(is_string($error)):
				?><div class="updated settings-error notice is-dismissible"><?php echo $error.'<br>';?></div><?php
			endif; 
		}
	}
	unset($_SESSION['archieve_message']);
	?>
</div>
<div >
	<?php // unset($_SESSION['upload_product_error']);
	if(isset($_SESSION['unarchieve_message'])){
		$all_error = $_SESSION['unarchieve_message'];	
		foreach($all_error as $index => $error){
			if(is_string($error)):
				?><div class="updated settings-error notice is-dismissible"><?php echo $error.'<br>';?></div><?php
			endif; 
		}
	}
	unset($_SESSION['unarchieve_message']);
	?>
</div>
<ul class="subsubsub">
	<li>
		<!-- <a class="current" href="admin.php?page=manage_jet_product"> <?php //_e('Jet Products','woocommerce-jet-integration')?></a> -->
		<a href = "<?php echo get_admin_url()?>admin.php?page=manage_jet_product&tab=upload_product" class="<?php if($_GET['tab']=='upload_product' || !isset($_GET['tab'])){?> nav-tab-active<?php }?>"><?php _e('Jet Products','woocommerce-jet-integration');?></a>
		|
	</li>
	<li>
		<!-- <a class="" href="admin.php?page=bulk_product_upload_page"><?php //_e('Bulk Product Upload','woocommerce-jet-integration')?></a> -->
		<a href = "<?php echo get_admin_url() ?>admin.php?page=manage_jet_product&tab=bulk_upload_product" class="<?php if(isset($_GET['tab']) && $_GET['tab']=='bulk_upload_product' ){?>nav-tab-active<?php }?> "><?php _e('Bulk Product Upload','woocommerce-jet-integration');?></a>
	</li>
</ul>
<br class="clear">

<?php if(isset($_GET['tab']) && isset($_GET['page']) && $_GET['tab']=='bulk_upload_product'  && ($_GET['page'] =='manage_jet_product')){?>
<div class="wrap">
	<?php $mass_upload_status = get_option('mass_complete');?>
	<?php if(!empty($mass_upload_status)){?>
	<div >
	<?php // unset($_SESSION['upload_product_error']);
	if(isset($_SESSION['upload_product_error'])){
		$all_error = $_SESSION['upload_product_error'];	
		foreach($all_error as $index => $error){
			?><div class="error settings-error notice is-dismissible"><?php echo $error.'<br>';?></div><?php 
		}
	}
	unset($_SESSION['upload_product_error']);
	?>
</div>
<?php }?>
<div style="color:red;">
	<?php 
	if(!empty($mass_upload_status)){
		echo $mass_upload_status;
		delete_option('mass_complete');
	}
	$running_status = get_option('mass_running');
	if(!empty($running_status)){
		echo $running_status;
	}

	?>
</div>
<input type="hidden" name="mass_upload_nonce" value="<?php echo wp_create_nonce('check_mass_pro_upload_nonce')?>" id="mass_upload_nonce_chk" >
<form action="#" method="post">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php _e('Mass Upload By Product','woocommerce-jet-integration');?></th>
				<td>
					<fieldset>
						<select name="jet_mass_product_upload" id="jet_mass_product_upload">
							<option value="choose" ><?php _e('Please Select Mass Product Upload Type','woocommerce-jet-integraion');?></option>
							<option value="all_product"  ><?php _e('All Products','woocommerce-jet-integration');?></option>
							<option value="selected_product" ><?php _e('Selected Products','woocommerce-jet-integration');?></option>
							<option value="exclude_selected_product" ><?php _e('Exclude Selected Products','woocommerce-jet-integration');?></option>
						</select>
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="mass_all_product" class="mass_product_all_data" hidden><br>
		<h4><?php _e('Select Products For Upload','woocommerce-jet-integration');?></h4>
		<select id="mass_product_upload" multiple="multiple" name="bulk_product_multi" class="js-example-basic-multiple">
		</select>
		<div id="mass_all_product_submit"></div>
		<hr>
	</div> 
</form>
<div>
	
	<?php 
	$mappedCategories	=	get_option('cedWooJetMapping',false);
	
	if(!empty($mappedCategories)){?>
	
	<table class="form-table">
		<tr rowspan="5">
			<td><b ><?php _e('OR','woocommerce-jet-integration')?></b></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Mass Upload By Category','woocommerce-jet-integration');?></th>
			<td><fieldset>
				<select name="upload_product_by_cat" id="upload_product_by_cat">
					<option value="not_selected"><?php _e('Select Category')?></option>
					<?php foreach($mappedCategories as $woo_cat_id => $jet_cat_id){ ?>
					<?php $cat_name = '';?>
					<?php $cat_name = get_term_by('term_id',$woo_cat_id,'product_cat', 'ARRAY_A');?>
					<option value="<?php echo $woo_cat_id;?>"><?php echo $cat_name['name'] ?></option>
					<?php }?>
				</select>
			</fieldset>
		</td>
		<td>
			<button id="submit_bycat_upload" class="button-secondary"><?php _e('Upload Products By Category','woocommerce-jet-integration');?></button>
			<input type="hidden" name="bulk_by_cat" id="bulk_by_cat" value="<?php echo wp_create_nonce('jet_bulk_cat_check')?>" >
		</td>
	</tr>
</table>	
<?php }	?>
<hr>
</div>
<!-- Bulk Profile assign -->

<?php 
global $wpdb;
$table_name 	= 	$wpdb->prefix.'jet_profile_settings';
$qry			= 	"SELECT * FROM `$table_name` WHERE 1";
$profileInfo	= 	$wpdb->get_results($qry);

?>
<div>
	<table class="form-table">
		<tr >
			<th scope="row" style="color:#098FD9"><?php _e('Bulk Profile Mapping','woocommerce-jet-integration');?></th>
		</tr>
		<tr>
			<th scope="row"><?php _e('Mapped Jet Products','woocommerce-jet-integration');?></th>
			<td>
				<div id="show_msg"></div><fieldset>
				<select name="list_map_profile_product" id="list_map_profile_product" multiple="multiple">
						<?php /* if(!empty($all_map_product)){?>
							<?php foreach($all_map_product as $pro_id => $value){ ?>
								<option value="<?php echo $pro_id;?>"><?php echo $value[0]; ?></option>
							<?php }?>
							<?php }*/?>
						</select>
					</fieldset>
				</td>
				
			</tr>
			<tr>
				<th scope="row"><?php _e('Profiles','woocommerce-jet-integration');?></th>
				<td><fieldset>
					<select name="list_all_profile" id="list_all_profile">
						<?php if(!empty($profileInfo)){?>
						<?php foreach($profileInfo as $index => $profile){ ?>
						<option value="<?php echo $profile->profile_id;?>"><?php echo $profile->profile_name; ?></option>
						<?php }?>
						<?php }?>
					</select>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th scope="row"></th>
			<td>
				<fieldset>
					<button id="map_mass_product_profiles" class="button-secondary"><?php _e('Map Profile','woocommerce-jet-integration');?></button>
					<input type="hidden" name="mass_pro_assign" value="<?php echo wp_create_nonce('jet_mass_profile_check')?>" id="massproassign">
				</fieldset>
			</td>
		</tr>
	</table>	
	<hr>
</div>

<!-- End Bulk Assign Profile -->

</div>

<?php }elseif((isset($_GET['tab']) && isset($_GET['page']) && $_GET['tab']=='upload_product') || ($_GET['page'] =='manage_jet_product')){?>
<div class="wrap">
	<div class="file_upload_mangpro">
		<select id="file_upload" name="file_upload">
			<option value="upload"><?php _e('Upload','woocommerce-jet-integration');?></option>
			<option value="Archive"><?php _e('Archive','woocommerce-jet-integration');?></option>
			<option value="Unarchive"><?php _e('Unarchive','woocommerce-jet-integration');?></option>
		</select>
		<input type="button" name="upload_product_button" value="submit" class="button" id="upload_product_button">
	</div>
	<?php $taxonomy     = 'product_cat';
	$orderby      = 'name';
	$empty        = 0;

	$args = array(
		'taxonomy'     => $taxonomy,
		'orderby'      => $orderby,
		'hide_empty'   => $empty
		);
	$all_categories = get_categories( $args );
	if(!empty($all_categories)){?>
	
	<fieldset class="manage_prod_cat">
		<select name="mass_cat_assign" id="mass_cat_assign_manage">
			<option value="not_selected"><?php _e('Select Category')?></option>
			<?php $categoriesArray	=	array();?>
			<?php foreach($all_categories as $category):?>
				<?php $categoriesArray[$category->term_id]	=	$category->category_nicename; ?>
				<option value="<?php echo $category->term_id; ?>"><?php echo $category->category_nicename; ?></option>
			<?php endforeach;?>
		</select>
		<input type="button" name="jet_mass_cat_assign" value="Map Category" class="button" id="jet_mass_cat_assign">
	</fieldset>
	
	<?php }?>
	
	<input type="hidden" name="upload_nonce" value="<?php echo wp_create_nonce('product_upload_nonce_check')?>" id="product_upload_nonce_check">
	<input type="hidden" name="inv_sync" value="<?php echo wp_create_nonce('jet_inventory_nonce_check')?>" id="inv_sync_nonce_check">
	<input type="hidden" name="update_pro_sync" value="<?php echo wp_create_nonce('jet_pstatus_nonce_check')?>" id="update_pro_sync">
	<input type="hidden" name="archive_misssync" value="<?php echo wp_create_nonce('jet_archnonce_check')?>" id="archive_misssync">

	<div class="jet_mange_pro_btn">
		<input type="button" name="archive_missing_product" value="Archive Missing Product" class="button" id="archive_missing_product" style="float:right;">
		<input type="button" name="update_product_status" value="Update Product Status" class="button" id="update_product_status" style="float:right;">
		<input type="button" name="jet_inventory_syncronize" value="Inventory Syncronize" class="button" id="jet_inventory_syncronize" style="float:right;">
	</div>
	<form action='' method="get">
		<p class="search-box">
			<label for="listing-search-input-search-input" class="screen-reader-text"><?php _e('Search:','woocommerce-jet-integration');?></label>
			<input type="hidden" value="manage_jet_product" name="page" id="listing-search-input-search-input">
			<input type="search" value="" name="search_text" id="listing-search-input-search-input">
			<input type="submit" value="Search" name="manage_search" class="button" id="search-submit"></p>
			
			<table class="wp-list-table widefat fixed striped posts" id="tbl_sort_product">
				<thead>
					<tr>
						<th style="width:3%" class="manage-column column-cb check-column" id="cb" scope="col">
							<label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All','woocommerce-jet-integration');?></label>
							<input type="checkbox" id="cb-select-all-1">
						</th>
						<th class="manage-column column-name sortable desc" id="product_id" scope="col" style="width:4%">
							<a href=""><span><?php _e('ID','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						<th style="width:9%" class="manage-column column-name sortable desc" id="product_image" scope="col">
							<a href=""><span><?php _e('Image','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						
						<th style="width:14%" class="manage-column column-name sortable desc" id="product_title" scope="col">
							<a href=""><span><?php _e('Title','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						<th style="width:4%" class="manage-column column-sku sortable desc" id="product_price" scope="col">
							<a href=""><span><?php _e('Price','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						<th style="width:4%" class="manage-column column-price sortable desc" id="product_qty" scope="col">
							<a href="">
								<span><?php _e('Qty','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						<th style="width:19%" class="manage-column column-qty sortable desc" id="product_cat" scope="col">
							<a href="">
								<span><?php _e('Category','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						<th style="width:10%" class="manage-column column-qty sortable desc" id="product_status" scope="col">
							<a href="">
								<span><?php _e('Profile','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						<th style="width:6%" class="manage-column column-qty sortable desc" id="product_type" scope="col">
							<a href="">
								<span><?php _e('Type','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						<th style="width:12%" class="manage-column column-qty sortable desc" id="jet_product_status" scope="col">
							<a href="">
								<span><?php _e('Jet Product Status','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						<th style="" class="manage-column column-qty sortable desc" id="action" scope="col">
							<a href="">
								<span><?php _e('Action','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
						<th style="width:7%;" class="manage-column column-qty sortable desc" id="Status_for_upload" scope="col">
							<a href="">
								<span><?php _e('Upload Status','woocommerce-jet-integration');?></span>
								
							</a>
						</th>
					</tr>
				</thead>

				
				<?php 
				$mappedCategories	=	get_option('cedWooJetMapping',false);
				
				if(!empty($mappedCategories)){
					$all_woo_cat	=	array();
					foreach($mappedCategories as $mappedwoocat	=> $mappedjetcat){
						
						$all_woo_cat[]	=	$mappedwoocat;		
					}
				}
				
				$paged = (isset($_GET['paged'])) ? $_GET['paged'] : 1;
				//print_r($paged);die;
/* 	if(isset($_GET['manage_search'])){ 
		$fields = $_GET['search_text'];
		$args = array(
		'post_type' => 'product',
		'meta_value'   => $fields,
		'meta_compare' => 'LIKE',
		
		);
}else{ */
	$args = array(
		'post_type' => array('product'),
		'post_status' => 'publish',
		'paged'					=> $paged,
		'posts_per_page'        => '8',
		'tax_query'             => array(
			array(
				'taxonomy'      => 'product_cat',
							'field' 		=> 'term_id', //This is optional, as it defaults to 'term_id'
							'terms'         => $all_woo_cat,
							'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
							)
			)
		);
	//}
	$loop = new WP_Query($args);
	?>
	
	
	
	<tbody class="the-list">
		<?php 
		
	$limit = 1; // number of rows in page
	
	$num_of_pages = $loop->max_num_pages;
	
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'prev_text' => __( '&laquo;', 'text-domain' ),
		'next_text' => __( '&raquo;', 'text-domain' ),
		'total' => $num_of_pages,
		'current' => $paged
		) );
	if ( $page_links )
	{
		echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
	}

	while ( $loop->have_posts() ) {
		$loop->the_post();  
		$_product = get_product($loop->post->ID ); 
		$product_id = $_product->id;
		
		if($_product->is_downloadable('yes'))
			continue;
		if($_product->product_type=='grouped')
			continue;
		if($_product->product_type=='external')
			continue;
		
		if($_product->is_type('variable'))
		{
			$variations			=	$_product->get_available_variations();
		}
		else{
			unset($variations);
		}
		
		$category 			= 	get_the_terms($product_id, "product_cat");
		if ($category)
		{
			$categories = '';
			foreach ($category as $term)
			{
				$categories .= $term->name.',';
			}
		}
		////for auto selecting jetattr when only one category is mapped(change on 14sept2016)
		$productCats 			= 	get_the_terms($post->ID, "product_cat");
		$mappedIDs				=	get_option('cedWooJetMapping',true);
		if(!empty($productCats)){
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
		}

		$jetAttrInfo	=	array();
		if(!empty($jetSelectedNodeID)){
			foreach($jetSelectedNodeID as $wooNodeID => $jetNodeID){
				
				$mappedAttributes		=	get_option($jetNodeID.'_linkedAttributes',false);
				if($mappedAttributes){
					
					$mappedAttributes	=	json_decode($mappedAttributes);
					if(is_array($mappedAttributes)){
						foreach($mappedAttributes as $jetAttrID){
							global $wpdb;
							$table_name 				= 	$wpdb->prefix.'jet_attributes_table';
							$qry						= 	"SELECT * FROM `$table_name` WHERE `jet_attr_id`=$jetAttrID;";
							$jetAttrInfo[$jetNodeID]	= 	$wpdb->get_results($qry);	
						}
					}
				}
			}
			$enable 				= 	get_post_meta($product_id,'selectedCatAttr', true);
			if(count($jetAttrInfo)==1 && !isset($enable)){
					//ECHO $product_id;
				update_post_meta($product_id,'selectedCatAttr',$jetNodeID);
			}
			if(isset($variations)){
				foreach ( $variations as $key=>$value ){
					$enable11 				= 	get_post_meta($value['variation_id'], $value['variation_id'].'_selectedCatAttr',true);
					if( $enable11=='' && count($jetAttrInfo)==1 ){
							//echo $value['variation_id'];
						update_post_meta($value['variation_id'],$value['variation_id'].'_selectedCatAttr',$jetNodeID);
					}
				}
			}
		}
		?>
		<tr class="iedit author-self jet-product-listing level-0 post-<?php echo $product_id;?> type-product status-publish hentry product_row" id="<?php echo $product_id;?>">
			<th class="check-column" scope="row">
				<label for="cb-select-<?php echo $product_id;?>" class="screen-reader-text"></label>
				<input type="checkbox" value="<?php echo $product_id;?>" class="unique_check" name="unique_post[]" id="cb-select-<?php echo $product_id;?>">
				<div class="locked-indicator"></div>
			</th>
			<td class="name column-id" style="width:4%">
				<span class="p_id" ><?php echo  $product_id;?></span>
				<span><a class="assing-profile" href="javascript:void(0)" pId="<?php echo  $product_id;?>">Assign Profile</a></span>
			</td>
			
			<td class="name column-name">
				<?php if(isset($variations))
				{
					if(WC()->version < "3.0.0"){?>
					<?php foreach($variations as $key => $variation){ ?>
					<span class="p_image"><img class="attachment-100x70 wp-post-image" width="70" height="35" src="<?php echo $variation['image_src'];?>"></span>
					<?php }?>
					<?php } 
					else {?>
					<?php foreach($variations as $key => $variation){ ?>
					<span class="p_image"><img class="attachment-100x70 wp-post-image" width="70" height="35" src="<?php echo $variation['image']['src'];?>"></span>
					<?php }?>
					<?php }?>
					<?php } else{ ?>
					<span class="p_image"><?php echo $_product->get_image(array(100,70));?></span>
					<?php }?>	
				</td>
				<td class="name column-sku">
					<?php if(isset($variations))
					{?>
					<?php foreach($variations as $key => $variation){ ?>
					<span class="product_fixed_name"><?php echo get_post_meta( $variation['variation_id'], '_jet_title', true );?></span></br>
					<?php }?>
					<?php } else{ ?>
					<span class="product_fixed_name"><?php echo $_product->get_title();?></span>
					<?php }?>	
				</td>
				<td class="name column-price">
					<?php if(isset($variations))
					{?>
					<?php foreach($variations as $key => $variation){ ?>
					<span class="p_price"><?php echo  $variation['display_price'];?></span></br>
					<?php }?>
					<?php } else{ ?>
					<span class="p_price"><?php echo  $_product->get_price();?></span>
					<?php }?>	
				</td>
				<td class="name column-qty">
					<?php if(isset($variations))
					{?>
					<?php foreach($variations as $key => $variation){ ?>
					<span class="p_qty"><?php echo  $variation['max_qty'];?></span></br>
					<?php }?>
					<?php } else{ ?>
					<span class="p_qty"><?php echo $_product->get_stock_quantity();?></span>
					<?php } ?>
				</td>
				<td class="name column-visibility">
					<span class="p_category"><?php echo $categories; $categories = '';?></span>
				</td>
				<td class="name column-visibility" id="profile_<?php echo $product_id;?>">
					<?php $assignedProfileID	= get_post_meta($product_id,'productProfileID',true); if(in_array($assignedProfileID, $existedProfileIds)):?><img class="remove_profile" value="<?php echo $product_id;?>" src="<?php echo CEDJETINTEGRATION; ?>/remove.png" height="16" width="16" /><?php endif;?>
					<span class="p_type"><center><?php echo get_post_meta($product_id,'productProfileID',true) ? $profileName[get_post_meta($product_id,'productProfileID',true)]: _e('Not Assigned ','woocommerce-jet-integration'); ?></center></span>
				</td>
				<td class="name column-visibility">
					<span class="p_product_status"><?php  echo $_product->product_type;?></span>
				</td>
				<td class="name column-visibility">
					<?php if(isset($variations))
					{?>
					<?php foreach($variations as $key => $variation){ ?>
					<span class="p_jet_status"><?php echo get_post_meta($variation['variation_id'],'jet_product_status',true)?get_post_meta($variation['variation_id'],'jet_product_status',true):'Not Uploaded';?></span></br>
					<?php }?>
					<?php } else{ ?>
					<span class="p_jet_status"><?php $current_value = get_post_meta($product_id,'jet_product_status',true); if(!empty($current_value)){ echo $current_value; }else{ _e('Not Uploaded','woocommerce-jet-integration');}?></span>
					<?php } ?>
				</td>
				<td class="name column-visibility"  style="width:7%; ">
					<span class="visibility"><a href="<?php echo site_url();?>/wp-admin/post.php?post=<?php echo $product_id;?>&action=edit"><?php _e('View Details','woocommerce-jet-integration')?></a></span><abbr></abbr>
				</td>
				<td class="name column-visibility"  style="width:3%; ">
					<span class="validation_message"></span>
				</td>
			</tr>
			<?php } ?>
		</tbody>	

		<tfoot>
			<tr>
				<th style="" class="manage-column column-cb check-column" id="cb" scope="col">
					<label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All','woocommerce-jet-integration');?></label>
					<input type="checkbox" id="cb-select-all-1">
				</th>
				<th class="manage-column column-name sortable desc" id="product_id" scope="col" style="width:4%">
					<a href=""><span><?php _e('ID','woocommerce-jet-integration');?></span>

					</a>
				</th>
				<th style="" class="manage-column column-name sortable desc" id="product_image" scope="col">
					<a href=""><span><?php _e('Image','woocommerce-jet-integration');?></span>

					</a>
				</th>

				<th style="" class="manage-column column-name sortable desc" id="product_title" scope="col">
					<a href=""><span><?php _e('Title','woocommerce-jet-integration');?></span>

					</a>
				</th>
				<th style="" class="manage-column column-sku sortable desc" id="product_price" scope="col">
					<a href=""><span><?php _e('Price','woocommerce-jet-integration');?></span>

					</a>
				</th>
				<th style="" class="manage-column column-price sortable desc" id="product_qty" scope="col">
					<a href="">
						<span><?php _e('Qty','woocommerce-jet-integration');?></span>

					</a>
				</th>
				<th style="" class="manage-column column-qty sortable desc" id="product_cat" scope="col">
					<a href="">
						<span><?php _e('Category','woocommerce-jet-integration');?></span>

					</a>
				</th>
				<th style="" class="manage-column column-qty sortable desc" id="product_status" scope="col">
					<a href="">
						<span><?php _e('Profile','woocommerce-jet-integration');?></span>

					</a>
				</th>
				<th style="" class="manage-column column-qty sortable desc" id="product_type" scope="col">
					<a href="">
						<span><?php _e('Type','woocommerce-jet-integration');?></span>

					</a>
				</th>
				<th style="" class="manage-column column-qty sortable desc" id="jet_product_status" scope="col">
					<a href="">
						<span><?php _e('Jet Product Status','woocommerce-jet-integration');?></span>

					</a>
				</th>
				<th style="" class="manage-column column-qty sortable desc" id="action" scope="col">
					<a href="">
						<span><?php _e('Action','woocommerce-jet-integration');?></span>

					</a>
				</th>
				<th style="" class="manage-column column-qty sortable desc" id="status_for_upload" scope="col">
					<a href="">
						<span><?php _e('Upload Status','woocommerce-jet-integration');?></span>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</form>
<?php // echo $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;?>
<div class="navigation">
	<div class="alignleft"><?php previous_posts_link('&laquo; Previous'); ?></div>
	<div class="alignright"><?php next_posts_link('More &raquo;') ?></div>
</div>
</div>
<?php
$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __( '&laquo;', 'text-domain' ),
	'next_text' => __( '&raquo;', 'text-domain' ),
	'total' => $num_of_pages,
	'current' => $paged
	) );
if ( $page_links )
{
	echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
}?> 
</div>