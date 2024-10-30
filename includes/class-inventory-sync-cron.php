<?php
/**
 *
 * @author developer
 * inventory syncronization on jet
 */

require_once ('../../../../wp-blog-header.php');
require_once (dirname(__FILE__).'/class-cedJetAjaxHandler.php');

class Class_jet_inventory_syncronize{

	public function __construct(){

		$this->inventory_syncronize		=	cedJetAjaxHandler::getInstance();
		$this->syncronize_inventory();
	}

	function syncronize_inventory(){
		$this->inventory_syncronize->uploading_mass_product_inventory();
	}
}
$objct_inv 		= 	new Class_jet_inventory_syncronize();