<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class cedJetStylesScripts{

	private static $_instance;

	public static function getInstance() {

		if( !self::$_instance instanceof self )
			self::$_instance = new self;

		return self::$_instance;

	}
	
	public function enqueJetAdminScripts(){
		
		wp_enqueue_script( 'jet-jquery-ui.min', CEDJETINTEGRATION.'js/jquery-ui.min.js');
		//wp_register_script('admin-jet-script',CEDJETINTEGRATION.'js/jet-script.js','','',true);
		wp_enqueue_script('admin-jet-script',CEDJETINTEGRATION.'js/jet-script.js','','1254',true);
		//wp_enqueue_script('admin-jet-script');
		if(isset($_GET['page']) && $_GET['page'] == 'manage_jet_attributes')
		{	
			wp_register_script('admin-jet-script1',CEDJETINTEGRATION.'js/manage_jet_attr.js','','',true);
			wp_enqueue_script('admin-jet-script1');
			
			
			$translation = array(
					'siteurl' 			=> 	site_url(),
					'ajaxurl'			=>	admin_url('admin-ajax.php'),
					'delmapcat'			=>	wp_create_nonce('delmapcat'),
					'edmapcat'			=>  wp_create_nonce('edmapcat'),	
			
			);
			wp_localize_script('admin-jet-script1','map_cat', $translation);
		}
		wp_enqueue_script('admin-jet-script2',CEDJETINTEGRATION.'js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.js');
		wp_enqueue_script('admin-jet-script3',CEDJETINTEGRATION.'js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.min.js');
		wp_enqueue_script('admin-jet-script4',CEDJETINTEGRATION.'js/jquery.fancybox.pack.js');
		wp_enqueue_script('admin-jet-script5',CEDJETINTEGRATION.'js/select_two.js');//https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js
		if(isset($_GET['page']) && $_GET['page'] =='jet_store_integration')
		{
			wp_register_script('admin-jet-script6',CEDJETINTEGRATION.'js/jet_store_integration.js','','',true);
			wp_enqueue_script('admin-jet-script6');
		}
		wp_enqueue_style( 'admin-jet-css1',CEDJETINTEGRATION.'js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.css');
		wp_enqueue_style( 'admin-jet-css2',CEDJETINTEGRATION.'js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.min.css');
		wp_enqueue_style( 'admin-jet-css4',CEDJETINTEGRATION.'css/jquery.fancybox.css');
		wp_enqueue_style( 'admin-jet-css5',CEDJETINTEGRATION.'css/select_two.css');//https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css'
		wp_enqueue_style( 'admin-jet-css3',CEDJETINTEGRATION.'css/stylenew.css');
		
		if(isset($_GET['page']) && $_GET['page'] == 'manage_jet_product')
		{
			wp_register_script('admin-jet-script8',CEDJETINTEGRATION.'js/product_validation.js','','',true);
			wp_enqueue_script('admin-jet-script8');
		}
		
		
		$translation = array(
				'siteurl' 			=> 	site_url(),
		);
		wp_localize_script('admin-jet-script', 'global', $translation);
		
	}
}