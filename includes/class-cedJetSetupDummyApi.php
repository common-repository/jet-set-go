<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'class-productManagement.php';
class cedJetSetupDummyApi{

	private static $_instance;

	public static function getInstance() {
		self::$_instance = new self;
		if( !self::$_instance instanceof self )
			self::$_instance = new self;

		return self::$_instance;

	}

	public function __construct() {
		$this->productManagement		=	cedJetProductManagement::getInstance();
		
	}
	
	public function enable_product_api($product_id,$file_type,$jet_file_id){
		$this->productManagement->uploadProducts($file_type,$product_id,$jet_file_id);
	}
	
	
}