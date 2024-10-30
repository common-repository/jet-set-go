<?php
 /**
 * Plugin Name: Jet Set Go
 * Plugin URI: http://cedcommerce.com/woocommerce-extensions/jet-woocommerce-integration
 * Description: Allow Merchant To Integrate Your Store With Jet API and manage your product and order easily.
 * Version: 1.0.3
 * Author: CedCommerce
 * Author URI: http://cedcommerce.com/
 * Developer: CedCommerce
 * Developer URI: http://cedcommerce.com/
 * Text Domain: woocommerce-jet-integration
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /languages
 */

 if (!defined('ABSPATH'))
 {
	exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
{
	
	//delete_option('enable_api');die;
	define('CEDJETINTEGRATION', plugin_dir_url(__FILE__));
	define('CEDJET_DIRPATH',plugin_dir_path(__FILE__));

	if ( !defined('ABSPATH') )
		define('ABSPATH', dirname(__FILE__) . '/');
	
	require_once(ABSPATH .'wp-settings.php');
	error_reporting(0);
	//including the main file of the plugin.
	
	
	include_once('cedJetIntegration.php');
	
	//instance of the main cedJetIntegration class.
	$cedJetInstance = cedJetIntegration::getInstance();
	
	//register activation hook.
	register_activation_hook( __FILE__ , array( $cedJetInstance , 'jetActivate' ) );
	
	//register de-activation hook.
	register_deactivation_hook(__FILE__ , array( $cedJetInstance , 'jetDeactivate' ) );
	
	//enqueing the script and style.
	add_action( 'admin_enqueue_scripts',array($cedJetInstance,'cedJetEnqueScript'),0);
	
	//creating pages and subpages.
	add_action('admin_menu', array($cedJetInstance,'cedJetPages'));
	
	//adding custom tab in product edit page.
	add_action('woocommerce_product_write_panel_tabs', array($cedJetInstance,'cedJetCustomTab'));
	
	//custom tab fields.
	add_action('woocommerce_product_write_panels', array($cedJetInstance,'cedJetcustomTabFields'));
	
	//processing product custom tab values.
	add_action('woocommerce_process_product_meta', array($cedJetInstance,'cedJetProcessProductMeta'));
	
	//get all attributes of selected category for variation
	add_action( 'wp_ajax_get_all_attributes_of_selected_category_for_variation', array($cedJetInstance,'cedJetDynamicAttributes'));
	
	//setup dummy data for enable Enable api  
	add_action( 'wp_ajax_activate_and_resubmit_product_on_jet',array($cedJetInstance,'cedJetActivateAndResubmitProduct'));
	
	//delete the error file
	add_action( 'wp_ajax_deleteErrorFile',array($cedJetInstance,'cedJetDeleteErrorFile'));
	
	// product uploader function.
	add_action( 'wp_ajax_upload_product_on_jet', array($cedJetInstance,'cedJetUploadProduct'));
	
	//adding mapped attribute data by selecting category 
	add_action( 'wp_ajax_addDynamicCatAttr',array($cedJetInstance,'cedjetaddDynamicCatAttr'));
	
	
	//mapping woocommerce categories with jet specified categories.
	add_action( 'wp_ajax_cedJetCategoryMapping',array($cedJetInstance,'woojetCategoryMapping'));
	
	
	add_action( 'wp_ajax_cedDeleteMappedCat',array($cedJetInstance,'cedDeleteMapCatEntry'));
	
	
	add_action( 'wp_ajax_updateMappedCatId',array($cedJetInstance,'cedUpdateMappedCatId'));
	
	//assigning the selected profile.
	add_action( 'wp_ajax_assignProfileHtml',array($cedJetInstance,'cedAssignProfile'));
	
	
	add_action( 'wp_ajax_assignProductProfileID',array($cedJetInstance,'cedSetProductProfile'));
	
	
	add_action( 'wp_ajax_removeProfileId',array($cedJetInstance,'cedRemoveProfile'));
	
	
	add_action( 'wp_ajax_update_product_status', array($cedJetInstance,'cedUpdateProductStatus'));
	
	
	add_action( 'wp_ajax_archive_missed_product', array($cedJetInstance,'cedArchiveProductStatus'));
	

	add_action( 'add_meta_boxes', array( $cedJetInstance, 'cedJetProfileMetaBox' ) );
	
	
	/**
	 * Extra settings For Variable product For Jet Attributes Settings
	 */
	// Add Variation Settings
	add_action( 'woocommerce_product_after_variable_attributes',array($cedJetInstance,'variation_settings'), 10, 3 );
	// Save Variation Settings
	add_action( 'woocommerce_save_product_variation',array($cedJetInstance,'save_variation_settings'), 10, 2 );
	
	/**
	 * start session on init.
	 */
	add_action('init', array($cedJetInstance,'jetStartSession'),1);
	
	add_action('admin_init','cedAutoPluginUpdate');
	
	function cedAutoPluginUpdate(){

		// $referer = $_SERVER['HTTP_HOST'];
		// $postdata = http_build_query(array('action' => 'update', 'referer'=>$referer));
		// require_once CEDJET_DIRPATH.'plugin-updates/plugin-update-checker.php';
		// $PluginUpdateChecker = PucFactory::buildUpdateChecker(
		// 	"http://demo.cedcommerce.com/woocommerce/update_notifications/woocommerce_jet_integration/update.php?$postdata",
		// 	CEDJET_DIRPATH.'woocomerce-jet-integration.php'
		// 	);


		global $wpdb;
		$installed_ver = get_option( "return_table_updated" );

		if ( empty($installed_ver) || $installed_ver == null ) {

			$return_detail_table_name 		= 	 $wpdb->prefix.'jet_return_detail';
			$sql6 		= "DROP TABLE ". $return_detail_table_name;
			$wpdb->query($sql6);

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

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $tbl6 );
update_option( "return_table_updated",'table_update');
}


$installed_refund	 =	get_option( "refund_table_updated" );
$check 				 =	get_option('table_created');
if(empty($check)){
	if(empty($installed_refund) || $installed_refund == null){

		$refund_detail_table_name 		= $wpdb->prefix.'jet_refund_detail';
		$sql = "ALTER TABLE " . $refund_detail_table_name . " ADD COLUMN woo_order_id VARCHAR(255) AFTER refund_status;";
		$wpdb->query($sql);
		update_option('refund_table_updated','table_updated');
	}
}


     	// update attributes table
$check_attr		 =	get_option('attr_table_updated');



if(empty($check_attr)){
	$attribute_table_name 			=	 $wpdb->prefix.'jet_attributes_table';

	$sql_jet_attr_id	 	=	"ALTER TABLE `$attribute_table_name` MODIFY COLUMN `jet_attr_id` bigint(20) unsigned NOT NULL default 0;";
	$sql_jet_attr_values 	=	"ALTER TABLE `$attribute_table_name` MODIFY COLUMN `values` text default NULL;";
	$sql_jet_attr_name	 	=	"ALTER TABLE `$attribute_table_name` MODIFY COLUMN `name` text NOT NULL;";
	$sql_jet_attr_unit	  	=	"ALTER TABLE `$attribute_table_name` MODIFY COLUMN `unit` text default NULL;";

	$wpdb->query($sql_jet_attr_id);
	$wpdb->query($sql_jet_attr_values);
	$wpdb->query($sql_jet_attr_name);
	$wpdb->query($sql_jet_attr_unit);

	update_option('attr_table_updated','attr_table_done_updated');
}

}
	//end session on logout.
add_action('wp_logout',array($cedJetInstance,'jetEndSession'));

	//end session on login.
add_action('wp_login',array($cedJetInstance, 'jetEndSession'));

	//order related operations.

	//adding meta box on order page.
add_action('add_meta_boxes',array($cedJetInstance,'cedJetOrderMetaBox'));

	//saving the meta box fields data of order
add_action('save_post', array($cedJetInstance,'saveJetOrderMetaBox'));

	//mass product upload on jet
add_action( 'wp_ajax_get_all_product_of_jet', array($cedJetInstance,'get_all_product_of_jet'));


	//actual mass product upload
add_action( 'wp_ajax_ced_actual_mass_product_upload', array($cedJetInstance,'start_mass_product_upload'));

	//upload product by category
add_action('wp_ajax_mass_upload_by_category',array($cedJetInstance,'start_mass_product_upload'));


	//mass archive product on jet
add_action( 'wp_ajax_ced_actual_mass_archive', array($cedJetInstance,'start_mass_archive_product'));


	//mass unarchieve for product
add_action( 'wp_ajax_ced_actual_mass_unarchive', array($cedJetInstance,'start_mass_unarchive_product'));


	//delete error file from db

add_action( 'wp_ajax_delete_jet_error_file', array($cedJetInstance,'delete_jet_error_file'));

	//for remove menu tab from settings tab
add_action( 'admin_menu', array($cedJetInstance,'remove_admin_submenus' ));


	//archieve product from jet when product is trash
add_action( 'transition_post_status', array($cedJetInstance,'archive_on_trash_product'), 10, 3 );

	//update inventory when 
add_action( 'woocommerce_order_status_completed',array($cedJetInstance,'update_inventory_on_jet'));


	//actual mass inventory  upload 
add_action( 'wp_ajax_jet_inventory_syncronize', array($cedJetInstance,'start_mass_inventory_upload'));

add_action('wp_ajax_delete_useless_files', array($cedJetInstance, 'cedDeleteUselessFiles'));


add_action('wp_ajax_get_meta_fields', array($cedJetInstance, 'get_all_meta_fields'));

	//enable demo jet api
add_action('wp_ajax_enable_demo_jet_api', array($cedJetInstance, 'call_for_enable_jet_api'));

	//get all jet category
add_action('wp_ajax_get_all_jet_category',array($cedJetInstance,'get_all_jet_categories'));
add_action('wp_ajax_update_edit_cat',array($cedJetInstance,'get_all_jet_categories_update'));

	//validate jet product
add_action('wp_ajax_validate_product_on_jet',array($cedJetInstance,'validate_jet_products'));

	//assign profile to products by check
add_action('wp_ajax_mass_profile_assign_to_products_by_check',array($cedJetInstance,'assign_profile_to_product_by_check'));

	//mass profile mapping
add_action('wp_ajax_mass_profile_mapping',array($cedJetInstance,'map_bulk_products_with_profile'));

	//mass category assign profile to products
add_action('wp_ajax_mass_category_assign_to_products',array($cedJetInstance,'mass_cat_assign_to_products'));

add_action('wp_ajax_mass_cat_manage_product',array($cedJetInstance,'mass_cat_manage_product'));
	
	/**
	 * This function is used to load language'.
	 * @name ced_wuoh_load_text_domain()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	
	function ced_jet_load_text_domain($name)
	{
		$domain = 'woocommerce-jet-integration';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, CEDJET_DIRPATH .'languages/'.$domain.'-' . $locale . '.mo' );
		$var=load_plugin_textdomain( $domain, false, plugin_basename( dirname(__FILE__) ) . '/languages' );
	}
	add_action('plugins_loaded', 'ced_jet_load_text_domain');
	
	add_filter( 'posts_where', 'change_posts_where', 10, 2 );
	function change_posts_where( $where, &$wp_query )
	{
		
		global $wpdb;
		if(is_admin()){
			if(isset($_GET['manage_search'])){
				$fields = $_GET['search_text'];

				if ( isset($fields) && !empty($fields)) {
					
					$mappedCategories	=	get_option('cedWooJetMapping',false);
					if(!empty($mappedCategories)){
						$all_woo_name	=	array();
						foreach($mappedCategories as $mappedwoocat	=> $mappedjetcat){
							$term	=	get_term_by('id', $mappedwoocat, 'product_cat');
							if(isset($term)){
								$all_woo_name[$mappedwoocat]	=	$term->name;
							}
						}
					}
					if(!empty($all_woo_name)){
						$all_ids = '';
						foreach($all_woo_name as $woocat => $catname){
							$match = preg_match("/$fields/", $catname);
							if($match){
								$all_ids .= $woocat.',';
							}
						}
						$all_ids = rtrim($all_ids,',');
						if(!empty($all_ids)){
							$cat_id = array_search($fields, $all_woo_name);
							$where .= 'AND ( '.$wpdb->term_relationships.'.term_taxonomy_id IN ('.$all_ids.'))';
							$where .= ' OR ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like( $fields ) ) . '%\'';
						}else{
							$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $fields ) ) . '%\'';
						}
					}else{ 
						$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $fields ) ) . '%\'';
					}
				}
			}
		}
		return $where;
	}
	
	/**
	 * function for returning products ids for jet use.
	 */
	function ced_jet_get_mapped_products(){

		global $wpdb;

		$all_woo_cat = array();
		$product_ids = array();

		$mappedCategories = get_option('cedWooJetMapping',false);
		if(!empty($mappedCategories)){
			foreach($mappedCategories as $mappedwoocat => $mappedjetcat){

				$all_woo_cat[] = $mappedwoocat;
			}
		}

		
		if(count($all_woo_cat)){

			$mapped_woo_cat = implode(',',$all_woo_cat);
			$query = 'SELECT '.$wpdb->prefix.'posts.ID FROM '.$wpdb->prefix.'posts INNER JOIN '.$wpdb->prefix.'term_relationships ON ('.$wpdb->prefix.'posts.ID = '.$wpdb->prefix.'term_relationships.object_id) WHERE 1=1  AND (
				'.$wpdb->prefix.'term_relationships.term_taxonomy_id IN ('.$mapped_woo_cat.')
				) AND '.$wpdb->prefix.'posts.post_type = "product" AND ('.$wpdb->prefix.'posts.post_status = "publish") GROUP BY '.$wpdb->prefix.'posts.ID ORDER BY '.$wpdb->prefix.'posts.post_date DESC';

$result =  $wpdb->get_results($query);

$product_ids = array();
if(!empty($result) && is_array($result)){

	foreach($result as $key => $productID){
		$pro_id 	=	$productID->ID;
		$_product 	= 	get_product($pro_id);
		if(!empty($_product) && $_product->is_type('variable'))
		{
			foreach($variations as $key => $variation)
			{
				$product_ids[]   = $variation['variation_id'];
			}
		}elseif(!empty($_product) && $_product->is_type('simple')){
			$product_ids[] = $_product->id;
		}
	}
}
}

return $product_ids;
wp_die();
}

	/**
	 * function for chekcing that the selected attribute is of variant type or not.
	 */
	function check_if_variant($attrID){
		global $wpdb;
		$qry = 'SELECT `id`,`variant` FROM '.$wpdb->prefix.'jet_attributes_table WHERE `jet_attr_id` = '.$attrID;
		$result =  $wpdb->get_results($qry);
		if(!empty($result) && is_array($result)){
			foreach($result as $variant){
				if(isset($variant->variant)){
					if($variant->variant == 1){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}
	
}else{

	function ced_jet_plugin_error_notice(){
		?>
		<div class="error notice is-dismissible">
			<p><?php _e( 'Woocommerce is not activated, please activate woocommerce first to install and use jet integration.', 'woocommerce-jet-integration' ); ?></p>
		</div>
		<?php
	}

	add_action( 'admin_init', 'ced_jet_plugin_deactivate' );

	function ced_jet_plugin_deactivate(){

		// deactivate_plugins( plugin_basename( FILE ) );

		add_action( 'admin_notices', 'ced_jet_plugin_error_notice' );
	}
}
