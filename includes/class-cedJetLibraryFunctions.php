<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class cedJetLibraryFunctions{

	private static $_instance;

	public static function getInstance() {

		if( !self::$_instance instanceof self )
			self::$_instance = new self;
		return self::$_instance;

	}
	
	public static function woocommerce_wp_select_multiple($field){
	
		global $thepostid, $post, $woocommerce;
	
		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['value']         = isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : array() );
		
		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . $field['name'] . '" class="' . esc_attr( $field['class'] ) . '" multiple="multiple">';
	
		
		foreach ( $field['options'] as $key => $value ) {
	
			$data = '';
			if(is_array($field['value'])){
				if(in_array( $key, $field['value'] )){
					$data = 'exist';
				}else{
					$data = ''; 
				}
			}else{
				$data = '';
			}
			
			echo '<option value="' . esc_attr( $key ) . '" ' . ( !empty($data) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
	
		}
	
		echo '</select> ';
	
		if ( ! empty( $field['description'] ) ) {
			
				echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		}
		echo '</p>';
	}
	
	public static function cedcommerce_text_with_unit_select($field){
		
		global $thepostid, $post, $woocommerce;
		
		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['class']         = isset( $field['class'] ) ? $field['class'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['value1']        = isset( $field['value1'] ) ? $field['value1'] : ( get_post_meta( $thepostid, $field['id'], true ) );
		$field['value2']        = isset( $field['value2'] ) ? $field['value2'] : ( get_post_meta( $thepostid, $field['id'].'_unit', true ) );
		
		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><input type="text" name="'.$field['name'].'" value="'.$field['value1'].'"><select class="second_dropdown" id="' . esc_attr( $field['id'] ) . '" name="' . $field['name'] . '_unit" class="' . esc_attr( $field['class'] ) . '" >';
		
		foreach ( $field['options'] as $key => $value ) {
		
			echo '<option value="' . esc_attr( $key ) . '" ' . ( ( $key == $field['value2'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
		
		}
		
		echo '</select> ';
		
		if ( ! empty( $field['description'] ) ) {
		
			if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				
				echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
			} else {
				
				echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			}
		
		}
		echo '</p>';
	}
	
}