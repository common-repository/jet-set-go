<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class cedJetFileUploadHelper{

	private static $_instance;

	public static function getInstance() {

		if( !self::$_instance instanceof self )
			self::$_instance = new self;

		return self::$_instance;

	}
	
	public function __construct(){
		$this->_apiHost =	esc_url(get_option('jet_api_url'));
		$this->user 	=   get_option('jet_user');
		$this->pass 	= 	get_option('jet_password');
	}
	
	/**
	 * get token for file upload
	 */
	public function JrequestTokenCurl(){
	
		$ch = curl_init();
		$url= $this->_apiHost.'/Token';
		$postFields='{"user":"'.$this->user.'","pass":"'.$this->pass.'"}';
	
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postFields);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json;"));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	
		$server_output = curl_exec ($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($server_output, 0, $header_size);
		$body = substr($server_output, $header_size);
		curl_close ($ch);
		$token_data  = json_decode($body);
	
		if(is_object($token_data) && isset($token_data->id_token)){
			update_option('jetcom_token',$body);
			return json_decode($body);
		}else{
			return false;
		}
			
	}
	
	public function CGetRequest($method){
		// authorise current token
		$tObject = $this->Authorise_token();
		$ch = curl_init();
		$url= $this->_apiHost.$method;
	
	
		$headers[] = "Content-Type: application/json";
		$headers[] = "Authorization: Bearer $tObject->id_token";
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		$server_output = curl_exec ($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($server_output, 0, $header_size);
		$body = substr($server_output, $header_size);
		curl_close ($ch);
	
		return $body;
	}
	
	/**
	 * Check token for authorize.
	 * @return token
	 */
	public function Authorise_token(){
		$Jtoken = json_decode(get_option('jetcom_token',true));
		$refresh_token =false;
			
		if(is_object($Jtoken) && $Jtoken!=null){
			$ch = curl_init();
			$url= $this->_apiHost.'/authcheck';
	
	
			$headers = array();
			$headers[] = "Content-Type: application/json";
			$headers[] = "Authorization: Bearer $Jtoken->id_token";
	
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
			$server_output = curl_exec ($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($server_output, 0, $header_size);
			$body = substr($server_output, $header_size);
			curl_close ($ch);
	
			$bjson = json_decode($body);
	
			if(is_object($bjson) && $bjson->Message!='' && 	$bjson->Message=='Authorization has been denied for this request.')
			{
				// refresh token
				$refresh_token =true;
			}
	
		}else{
			$refresh_token =true;
		}
			
		if($refresh_token){
			$token_data = $this->JrequestTokenCurl();
			if($token_data != false){
				return $token_data;
			}else{
				$_SESSION['upload_common_msg'][] = 'API user & API password either or Invalid. Please set API user & API pass from jet configuration.';
				return;
			}
		}else{
			return $Jtoken;
		}
	
	}
	
	public function uploadFile($localfile ,$url){
	
		$headers = array();
		$headers[] = "x-ms-blob-type:BlockBlob";
	
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_PUT, 1);
		$fp = fopen ($localfile, 'r');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_INFILE, $fp);
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));
	
		$http_result = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
	
		curl_close($ch);
		fclose($fp);
	
	}
	
	public function CPostRequest($method,$postFields){
		// New way to post data
	
		$url= $this->_apiHost.$method;
	
		$tObject =$this->Authorise_token();
	
		$headers = array();
		$headers[] = "Content-Type: application/json";
		$headers[] = "Authorization: Bearer $tObject->id_token";
			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postFields);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	
	
		$server_output = curl_exec ($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($server_output, 0, $header_size);
		$body = substr($server_output, $header_size);
		curl_close ($ch);
	
		return $body;
	}
	
	public function CPutRequest($method, $post_field){
	
		
		$url= $this->_apiHost.$method;
		$ch = curl_init($url);
		$tObject =$this->Authorise_token();
	
		$headers = array();
		$headers[] = "Content-Type: application/json";
		$headers[] = "Authorization: Bearer $tObject->id_token";
	
	
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post_field);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		//curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	
		$server_output = curl_exec ($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($server_output, 0, $header_size);
		$body = substr($server_output, $header_size);
		curl_close ($ch);
	
		return $body;
	
	}
	
	function gzCompressFile($source, $level = 9){
		$dest = $source . '.gz';
		$mode = 'wb' . $level;
		$error = false;
		if ($fp_out = gzopen($dest, $mode)) {
			if ($fp_in = fopen($source,'rb')) {
				while (!feof($fp_in))
					gzwrite($fp_out, fread($fp_in, 1024 * 512));
				fclose($fp_in);
			} else {
				$error = true;
			}
			gzclose($fp_out);
		} else {
			$error = true;
		}
		if ($error)
			return false;
		else
			return $dest;
	}
	
	// function for fetching attribute details of the mapped category.
	public function get_category_attributes($catID) {
		
		
		$response	=	"";
		$response 	= 	$this->CGetRequest('/taxonomy/nodes/'.$catID.'/attributes');
		$attributes	=	json_decode($response,true);
		
		return $attributes;
		/* 
		$cat_action	=	'get_category';
		$cat_id 	=	$catID;
		$cat_url 	=	'http://demo.cedcommerce.com/jet/demo_get_category.php?token_id=2016_ced_jet_team&action='.$cat_action.'&cat_id='.$cat_id;
			
		$ch_cat = curl_init();
		curl_setopt($ch_cat, CURLOPT_URL, $cat_url);
		curl_setopt($ch_cat, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch_cat, CURLOPT_HEADER, 0);
		curl_setopt($ch_cat, CURLOPT_SSL_VERIFYPEER, 0);
			
		$get_category = curl_exec($ch_cat);
		curl_close ($ch_cat);
		
		return $get_category; */
	}
	
	public function get_attribute_detail($attrID) {
		
		$attr_action =	'get_attributes';
		$attr_id 	 =	$attrID;
		$attr_url 	 =	'http://demo.cedcommerce.com/jet/demo_get_category.php?token_id=2016_ced_jet_team&action='.$attr_action.'&attr_id='.$attr_id;
			
		$ch_attr = curl_init();
		curl_setopt($ch_attr, CURLOPT_URL, $attr_url);
		curl_setopt($ch_attr, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch_attr, CURLOPT_HEADER, 0);
		curl_setopt($ch_attr, CURLOPT_SSL_VERIFYPEER, 0);
		
		$get_attr = curl_exec($ch_attr);
		curl_close ($ch_attr);
		
		return $get_attr;
	}
}