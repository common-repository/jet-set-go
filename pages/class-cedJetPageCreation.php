<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class cedJetActivation{

	private static $_instance;

	public static function getInstance() {

		if( !self::$_instance instanceof self )
			self::$_instance = new self;

		return self::$_instance;

	}
	
	public function createPages(){
		
		add_menu_page( 'Woocommerce Jet Integration', 'Jet', 'manage_options', 'jet_store_integration', '',CEDJETINTEGRATION.'jet_dashicon.png', '27.5' );
		
		
		$this->add_all_menu();
	}
	
	function jet_licence_settings(){
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
	}
	
	/**
	 * Add all menu views
	 */
	function add_all_menu(){
		add_submenu_page( 'jet_store_integration', 'Jet Api Settings','Jet Configuration' ,'manage_options','jet_store_integration' ,array($this,'configure_jet_api_settings'));

		add_submenu_page( 'jet_store_integration', 'Map Categories','Map Categories' ,'manage_options','manage_jet_attributes' ,array($this,'create_and_manage_jet_attribute'));
		add_submenu_page( 'jet_store_integration', 'Profile','Profile' ,'manage_options','jet_profile_settings' ,array($this,'jet_profile_settings'));
		
		add_submenu_page( 'jet_store_integration', 'Manage Product','Manage Products' ,'manage_options','manage_jet_product' ,array($this,'manage_jet_products'));
		add_submenu_page( 'jet_store_integration', 'Jet Orders','Orders' ,'manage_options','jet_orders' ,array($this,'jet_order_settings'));
		add_submenu_page( 'jet_store_integration', 'Return','Return' ,'manage_options','order_return' ,array($this,'return_location_settings'));
		add_submenu_page( 'jet_store_integration', 'Refund ','Refund' ,'manage_options','order_refund' ,array($this,'order_refund_settings'));
		add_submenu_page( 'jet_store_integration', 'Upload Errors','Upload Error' ,'manage_options','upload_error' ,array($this,'upload_error_settings'));
		add_submenu_page( 'jet_store_integration', 'Mass Category Assign','Mass Category Assign' ,'manage_options','mass_cat_assign',array($this,'mass_category_assign_to_products'));
		
		add_options_page('Add New Profile', 'Add New Profile', 'manage_options', 'profile_settings', array($this,'add_new_profile'));
	}
	
	
	/**
	 * Add new profile for configure settings
	 */
	function add_new_profile(){
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once 'add_new_profile.php';
	}
	
	/**
	 * Create and manage jet attributes
	 */
	function create_and_manage_jet_attribute()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('manage_jet_attributes.php');
	}
	
	/**
	 * Configure jet api Configuration settings
	 */
	function configure_jet_api_settings()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('configure_jet_api_settings.php');
	}
	
	/**
	 * Export Category From jet
	 */
	function export_category_csv_from_jet()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('export_category_from_api.php');
	}
	
	
	/**
	 * Manage Jet Products
	 */
	function manage_jet_products()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('manage_jet_products.php');
	}
	
	/**
	 * Profile settings for jet product
	 */
	function jet_profile_settings(){
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('jet_profile_settings.php');
	}

	/**
	 * Return Location settings
	 */
	function return_location_settings()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('return_location_settings.php');
	}
	
	/**
	 * Refund Location settings
	 */
	function order_refund_settings()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('order_refund_settings.php');
	}
	
	/**
	 * Load Jet order refund panel
	 */
	function jet_order_refund_panel()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('order_refund_pannel.php');
	}
	
	/**
	 * Jet order submit
	 */
	
	function jet_order_refund_submit()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('jet_refund_settings_submit.php');
	}
	/**
	 * Upload error file
	 */
	function upload_error_settings()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('upload_errors_settings.php');
	}
	
	/**
	 * Mass Category Assign to products
	 */
	function mass_category_assign_to_products(){
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once('mass_category_assign.php');
	}
	
	/**
	 * upload error return
	 */
	function jet_upload_error_return()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once 'upload_error_return.php';
	}
	
	/**
	 * Jet order settings
	 */
	function jet_order_settings()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You don\'t have sufficient permissions to access this page.','woocommerce-jet-integration'));
		}
		include_once 'jet_order_settings.php';
		
	}
}