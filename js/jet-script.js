/*global woocommerce_admin_meta_boxes ,woocommerce_admin_meta_boxes, woocommerce_admin, accounting, woocommerce_admin_meta_boxes_order */

jQuery.noConflict();
var all_mask_product = new Array();
var current_page	=	'';
var siteurl 		=	global.siteurl;

jQuery(document).ready(function(){
	/**
	 * uploading selected products on jet.
	 */
	 jQuery('#upload_product_button').click(function(){
	 	var file_upload_type 	= 	jQuery('#file_upload').val();
	 	var all_upload_product 	= 	new Array();
	 	var i 					= 	0;
	 	var check_id;
	 	var product_upload_nonce = jQuery('#product_upload_nonce_check').val();
	 	
	 	jQuery('.product_row').each(function(){
	 		if(jQuery(this).find('.unique_check').is(':checked'))
	 		{
	 			all_upload_product[i] = jQuery(this).find('.unique_check').val();
	 			console.log(all_upload_product);
	 			i++;
	 		}
	 	});
	 	
	 	if(all_upload_product.length == 0){
	 		alert('Please Select any Product To Upload');
	 	}
	 	else{
	 		jQuery( "#jet-loading" ).show();
	 		jQuery.post(
	 			ajaxurl,
	 			{
	 				'action' : 'upload_product_on_jet',
	 				'upload_type':file_upload_type,
	 				'all_upload_product_id':all_upload_product,
	 				'pupload_nonce':product_upload_nonce,
	 			},
	 			function(response){
	 				jQuery( "#jet-loading" ).hide();
	 				window.location.reload();
	 			}
	 			);
	 	}
	 });
	});
jQuery(document).ready(function(){

	jQuery(".item_qty_shipped").on('input',function(e){
		var shippedQty 	= 	jQuery(this).val();
		var cancleQty  =   jQuery(this).closest("tr").find(".item_qty_cancelled").val();
		var orderQty 	=	jQuery(this).closest("tr").find(".item_qty_order").val();
		var ordercancleQty   =   jQuery(this).closest("tr").find(".item_qty_cancel").val();

		if((parseInt(shippedQty) + parseInt(cancleQty) ) > (parseInt(orderQty) + parseInt(ordercancleQty)))
		{
			jQuery(this).closest("tr").find(".qty_shipped_notification").show();
		}
		else if((parseInt(shippedQty) + parseInt(cancleQty) ) <= (parseInt(orderQty) + parseInt(ordercancleQty)))
		{
			jQuery(this).closest("tr").find(".qty_shipped_notification").hide();
		}
	});

	jQuery(".item_qty_cancelled").on('input',function(e){
		var cancleQty 	= 	jQuery(this).val();
		var shippedQty  =   jQuery(this).closest("tr").find(".item_qty_shipped").val();
		var orderQty 	=	jQuery(this).closest("tr").find(".item_qty_order").val();
		var ordercancleQty   =   jQuery(this).closest("tr").find(".item_qty_cancel").val();

		if((parseInt(shippedQty) + parseInt(cancleQty) ) > (parseInt(orderQty) + parseInt(ordercancleQty)))
		{
			jQuery(this).closest("tr").find(".qty_shipped_notification").show();
		}
		else if((parseInt(shippedQty) + parseInt(cancleQty) ) <= (parseInt(orderQty) + parseInt(ordercancleQty)))
		{
			jQuery(this).closest("tr").find(".qty_shipped_notification").hide();
		}
	});


	/**
	 * Archiving all the products uploaded on jet but not in store. 
	 */
	 jQuery('#archive_missing_product').click(function(){
	 	jQuery( "#jet-loading" ).show();
	 	var check_acrhive = jQuery('#archive_misssync').val(); 
	 	jQuery.post(
	 		ajaxurl,
	 		{
	 			'action' : 'archive_missed_product',
	 			'arc_nonce':check_acrhive,
	 		},
	 		function(response){
	 			jQuery( "#jet-loading" ).hide();
	 			window.location.reload();
	 		}
	 		);
	 });

	 


	/**
	 * Updating product status manually.
	 */
	 jQuery('#update_product_status').click(function(){
	 	var check_nonce_status = jQuery('#update_pro_sync').val(); 
	 	jQuery( "#jet-loading" ).show();
	 	jQuery.post(
	 		ajaxurl,
	 		{
	 			'action' : 'update_product_status',
	 			'ups_nonce':check_nonce_status,	
	 		},
	 		function(response){
	 			jQuery( "#jet-loading" ).hide();
					//console.log(response);
					window.location.reload();
				}
				);
	 });


	/**
	 * Single product upload for setup api.
	 */ 
	 jQuery('#send_sku,#send_price,#send_inventory,#send_shippingexception,#send_returnexception').click(function(){
	 	var id = jQuery('#demo_upload').val();
	 	var file_type = jQuery(this).attr('name');
	 	jQuery( "#jet-loading" ).show();

	 	if(id == '0'){
	 		alert("Please select any product for enable api");
	 		jQuery( "#jet-loading" ).hide();
	 		return;
	 	}else{
	 		jQuery.post(
	 			ajaxurl,
	 			{
	 				'action' : 'activate_and_resubmit_product_on_jet',
	 				'activate_upload_type':'demo_upload',
	 				'single_product_id':id,
	 				'demo_file_upload':'demo_file_upload',
	 				'file_type':file_type,

	 			},
	 			function(response){
	 				jQuery( "#jet-loading" ).hide();
	 				alert(response);
	 				window.location.reload();

	 			}
	 			);
	 	}
	 });


	/**
	 * Upload Error File 
	 */
	 jQuery('#resubmit_error_file').click(function(){
	 	var jet_file_id = jQuery('#jet_file_id').val()
	 	jQuery( "#jet-loading" ).show();
	 	jQuery.post(
	 		ajaxurl,
	 		{
	 			'action' : 'activate_and_resubmit_product_on_jet',
	 			'activate_upload_type':'activate_and_resubmit',
	 			'error_file_upload':'error_file_upload',
	 			'jet_file_id':jet_file_id,

	 		},
	 		function(response){
	 			jQuery( "#jet-loading" ).hide();
	 			alert(response);
	 			window.location.href	=	siteurl+'/wp-admin/admin.php?page=upload_error';
	 		}
	 		);
	 });

	/**
	 * delete error file.
	 */
	 jQuery('#delete_error_file').click(function(){
	 	var jet_file_id = jQuery('#jet_file_id').val()
	 	jQuery( "#jet-loading" ).show();
	 	jQuery.post(
	 		ajaxurl,
	 		{
	 			'action' : 'deleteErrorFile',
	 			'jet_file_id':jet_file_id,

	 		},
	 		function(response){
	 			jQuery( "#jet-loading" ).hide();
	 			window.location.href	=	siteurl+'/wp-admin/admin.php?page=upload_error';
	 		}
	 		);
	 });

	/**
	 * ship to date timepicker.
	 */
	 jQuery('#ship_to_date').datetimepicker({
	 	dateFormat : 'yy-mm-dd',
	 	timeFormat: "hh:mm:ss",
	 });


	/**
	 * expected delivery date timepicker.
	 */
	 jQuery('#exp_delv_date').datetimepicker({
	 	dateFormat : 'yy-mm-dd',
	 	timeFormat: "hh:mm:ss",
	 });


	/**
	 * carrier picker date timepicker.
	 */
	 jQuery('#carrier_pickup_date').datetimepicker({
	 	dateFormat : 'yy-mm-dd',
	 	timeFormat: "hh:mm:ss",
	 });



	  jQuery(".assing-profile").on('click',function(){
	 	
	 	var pid	=	jQuery(this).attr('pId');
	 	var slctdProfileID;
	 	jQuery.post(
	 		ajaxurl,
	 		{
	 			'action' : 'assignProfileHtml',
	 			'pid': pid
	 		},
	 		function(response){
	 			jQuery.fancybox.open({
	 				'content' :response,
	 				'width' : 700,
	 				'height': 50,
	 				'beforeShow' : function(){

	 					slctdProfileID = jQuery('#selectedProfile :selected').val();
	 				},
	 				'beforeClose' : function(){
	 					slctdProfileID = jQuery('#selectedProfile :selected').val();
	 				},
	 				'afterClose': function(){
	 					if(slctdProfileID == 'none' || slctdProfileID == undefined || slctdProfileID == null)
	 					{
	 						return;
	 					}
	 					jQuery.post(
	 						ajaxurl,
	 						{
	 							'action'	: 	'assignProductProfileID',
	 							'profileID'	:	slctdProfileID,
	 							'prdctid' 	: 	pid
	 						},
	 						function(response){
	 							jQuery("#profile_"+pid).html(response);
	 							window.location.reload();
										});//function response
									} //function after close
					}); //facny box end
	 		}
	 		);
	 });

	/*
	 * dynamic attribute creation for selected category.
	 */
	/* jQuery('.selectit input[type="checkbox"]').click(function(){
			var cat_id;
			var cat_name;
			var select_status;
			var jet_cat_ids = {};
			var jet_id 		= jQuery(this).attr('id');
			var jet_cat_id 	= jQuery(this).attr('value');
			var jet_parent 	= jQuery(this).parents("li").last().attr("id");
			var top_parent 	= jQuery.trim(jQuery('#'+jet_parent+' '+'label:first').text());
			var str 	   	= 'Jet Categories';
			
			if (jQuery(this).prop('checked')==true){
				select_status = true;
			}else{
				select_status = false;
			}
			if(top_parent.trim() == str.trim()){
				
				jQuery('#'+jet_parent+' ul li').each(function(){
						
					jQuery(this).each(function(){
							
						if(jQuery(this).find( ":checkbox" ).prop('checked')==true)
						{
							cat_id = jQuery(this).find( ":checkbox" ).val();
							cat_name = jQuery(this).children().eq(0).text();
							jet_cat_ids[cat_name] = cat_id;
						}
					});
						
				})
					
			}else{
				return;
			}
			var size = 0;
			var html;
			var hide_box;
			for (var key in jet_cat_ids){
				  if (jet_cat_ids.hasOwnProperty(key))
					  {
					  	size++;
					  }
				}
			if(size==1)
				{
				if(select_status)
					{
						hide_box	=	false;
						html		= 	'<b>Would You Like To Make This Categories Attribute As Jet Variation</b>';
						html		+=	'</br></br><select id="dropdownId" name="CatSelectName">';
						for (var key in jet_cat_ids){
							  if (jet_cat_ids.hasOwnProperty(key))
							  {
								  html += '<option value ='+jet_cat_ids[key]+' class="catlist">YES</option>';
							  }
						}
						html	+= '<option value ="no" class="catlist">NO</option></select><input type="button" onclick="jQuery.fancybox.close()" value="Select Category" />';
					}
				if(!(select_status))
					{
						hide_box	=	true;
						html		=	'<b>This Remaining Category Is Used As Jet Variation.</b>';
						html		+= 	'</br></br><select id="dropdownId" name="CatSelectName">';
						for (var key in jet_cat_ids){
							  if (jet_cat_ids.hasOwnProperty(key))
							  {
								  html 	+= 	'<option value ='+jet_cat_ids[key]+' class="catlist">YES</option>';
							  }
						}
						html	+=	'<input type="button" onclick="jQuery.fancybox.close()" value="OK" />';
					}
				}
			if(size>1)
				{
					hide_box	=	false;
					var slctd_cat_id;
					html = '<details><summary><b>Select The Category You Want To Create Attributes.</b></summary> </details> </br></br><select id="dropdownId" name="CatSelectName">';
					html += '<option value ="no" class="catlist">ANOTHER CATEGORY/PREVIOUS CATEGORY</option>';
					for (var key in jet_cat_ids){
						  if (jet_cat_ids.hasOwnProperty(key))
						  {
							  html += '<option value ='+jet_cat_ids[key]+' class="catlist">'+key+'</option>';
						  }
					}
					html += '</select> <input type="button" onclick="jQuery.fancybox.close()" value="Select Category" />';
				}
			
			jQuery.fancybox.open({
					'content' :html,
					'width' : 700,
					'height': 50,
					'closeBtn' : false,
					'beforeShow' : function(){
						if(hide_box)
						{
							jQuery('#dropdownId').hide();
						}
						 slctd_cat_id = jQuery('#dropdownId :selected').val();
						},
					'beforeClose' : function(){
						slctd_cat_id = jQuery('#dropdownId :selected').val();
					},
					'afterClose': function(){
						if(slctd_cat_id=='no')
							{
								alert('Fine');
								return;
							}
						jQuery('#jet_selected_category').attr('value',slctd_cat_id);
						 jQuery.post(
							ajaxurl,
								{
									'action' 		: 	'get_all_attributes_of_selected_category_for_variation',
									'jet_cat_id'	:	slctd_cat_id,
								},
								function(response){
									jQuery('.woocommerce_attribute').each(function(){
										if (jQuery(this).attr('data-taxonomy').match("^pa_jet")) {
											var $parent = jQuery(this);
											if ( $parent.is( '.taxonomy' ) ) {
												$parent.find( 'select, input[type=text]' ).val('');
												$parent.hide();
												jQuery( 'select.attribute_taxonomy' ).find( 'option[value="' + $parent.data( 'taxonomy' ) + '"]' ).removeAttr( 'disabled' );
											} else {
												$parent.find( 'select, input[type=text]' ).val('');
												$parent.hide();
												attribute_row_indexes();
											}
										}
									})
								    var mySelect 	= jQuery('#product_attributes p:last-child .attribute_taxonomy' );//.html('hello');
								    var all_attr 	= jQuery.parseJSON(response);
									var option 		= '<option value="">Custom product attribute</option>';
									jQuery('#hiddenCategory').val(slctd_cat_id);
									for(var i = 0;i<all_attr.length;i++)
										{
											if(all_attr[i] != null){
												var size         = i;
												var attribute 	 = 'pa_'+all_attr[i]['attribute_name'];
												var $wrapper     = jQuery( "#product_attributes").closest( '#product_attributes' ).find( '.product_attributes' );
												var product_type = jQuery( 'select#product-type' ).val();
												var data         = {
													action:   'woocommerce_add_attribute',
													taxonomy: attribute,
													i:        size,
													security: woocommerce_admin_meta_boxes.add_attribute_nonce
												};
												$wrapper.block({
													message: null,
													overlayCSS: {
														background: '#fff',
														opacity: 0.6
													}
												});
												jQuery.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
													$wrapper.append( response );

													if ( product_type !== 'variable' ) {
														$wrapper.find( '.enable_variation' ).hide();
													}
													
													if ( product_type == 'variable' ) {
														jQuery('.enable_variation').each(function(){
															var $check = jQuery(this).children().children();
															$check.prop('checked',true);
														});
													}

													jQuery( document.body ).trigger( 'wc-enhanced-select-init' );
													attribute_row_indexes();
													$wrapper.unblock();

													jQuery( document.body ).trigger( 'woocommerce_added_attribute' );
												});
												if ( attribute ) {
													jQuery( 'select.attribute_taxonomy' ).find( 'option[value="' + attribute + '"]' ).attr( 'disabled','disabled' );
													jQuery( 'select.attribute_taxonomy' ).val( '' );
												}
											}//end not null
										}//end for loop
									});//function response
								} //function after close
							}); //facny box end
});*/

	 /**
	  * attribute row indexing function.
	  */
	  function attribute_row_indexes() {
	  	jQuery( '.product_attributes .woocommerce_attribute' ).each( function( index, el ) {
	  		jQuery( '.attribute_position', el ).val( parseInt( jQuery( el ).index( '.product_attributes .woocommerce_attribute' ), 10 ) );
	  	});
	  }


	 /**
	  * hide and show the extra tabs for other than simple type of product.
	  */
	  jQuery( 'select#product-type' ).change( function () {

			// Get value
			var select_val = jQuery( this ).val();
			
			if(	'simple'	===	select_val	){
				jQuery('.custom_tab').show();
			}
			else{
				jQuery('.custom_tab').hide();
			}

		}).change();

	/**
	 * deleting useless uploaded files..
	 */

	 jQuery("#delete_uploaded_file").click(function(){
	 	jQuery("#jet-loading").show();
	 	jQuery.post(
	 		ajaxurl,
	 		{
	 			'action': 'delete_useless_files'
	 		},
	 		function(response){

	 			if(response == 0){
	 				alert("Uploaded Product's Useless Files Deleted Successfully");
	 			}else{
	 				alert(response);
	 			}

	 			jQuery("#jet-loading").hide();
	 		}
	 		);

	 });

	});

/**
 * Mass product upload
 */
 jQuery('#mass_all_product').hide();

 var counter = 1;
 jQuery(document).on( 'change', '#jet_mass_product_upload',function(){
 	var action_type	=	jQuery(this).val();
 	var html			=	'';

 	var all_html = '';
 	if(action_type == 'choose'){ 
		//jQuery('#mass_all_product').html(html);
		//jQuery('#mass_all_product').show();
		return;
		
	}else{
		jQuery.post(
			ajaxurl,
			{
				'action' : 'get_all_product_of_jet',
				'action_type': action_type,
				'counter' : counter,
				
			},
			function(response){
				
				var all_product = jQuery.parseJSON(response);
				console.log(all_product);
				if(all_product == null || all_product == 0){
					
					if(counter != 1){
						return;
					}else{
						alert('No any product Selected as Jet category');
						return;
					}
				}else{ 

					jQuery('#mass_all_product').show();
					html += '';
					//html +='<select id="mass_product_upload" multiple="multiple" name="bulk_product_multi" class="select">';
					for(var i in all_product['id'])
					{
						if(all_product['id'][i] == 'error'){
							continue;
						}
						
						html += '<option value='+all_product['id'][i]+' >'+all_product['name'][i]+'</option>';
						all_mask_product[i] = all_product['id'][i];
					}
					// html += '</select>';
					if(counter != 1){
						var exstdData = jQuery('#mass_product_upload').html();
						exstdData += html;
						jQuery('#mass_product_upload').html(exstdData);
					}else{
						jQuery('#mass_product_upload').select2('data', null)
						jQuery('#mass_product_upload').html(html);
					}

					var html1 = '';
					html1 += '</br></br>';
					html1 += '<select id="action_for_upload">';
					html1 +='<option value="mass_product_upload">Mass Product Upload</option>';
					html1 +='<option value="mass_archive">Mass Archive</option>';
					html1 +='<option value="mass_unarchive">Mass Unarchive</option>';
					html1 +='</select>';
					html1 += '<input type="button" id="mass_product_submit" value="submit" class="button-secondary">';
					jQuery('#mass_all_product_submit').replaceWith(html1); 

					counter++;
					jQuery('#jet_mass_product_upload').trigger('change');
				}
			});
}

});

jQuery(document).ready(function() { 
	// need to reinitialize select2 for options to take effect
	jQuery('#mass_product_upload').select2({
		placeholder: "Select Product For Upload"
		
	});
	
	jQuery('#list_map_profile_product').select2({
		placeholder: "Select Products"
	});
	
	jQuery('#list_all_profile').select2({
		placeholder: "Select Profile"
	});
	
	jQuery('#list_all_profile_on_cat').select2({
		placeholder: "Select Profile"
	});
	
});

jQuery(document).on('click','#mass_product_submit',function(){
	var all_product_val = 	jQuery('#mass_product_upload').val();
	var action_type		=	jQuery('#jet_mass_product_upload').val();

	var actual_mass_product_id = new Array();
	var set_action = jQuery('#action_for_upload').val();

	if(set_action == 'mass_product_upload'){
		call_action = 'ced_actual_mass_product_upload';

	}

	if(set_action == 'mass_archive'){
		call_action = 'ced_actual_mass_archive';
	}

	if(set_action == 'mass_unarchive'){
		call_action = 'ced_actual_mass_unarchive';
	}
	//For upload all product
	if(action_type == 'all_product'){
		actual_mass_product_id = all_mask_product;
	}
	
	//For upload Selected product
	if(action_type == 'selected_product'){
		if(all_product_val == null){
			alert('Please Select Any product to Upload');
		}else{
			var parse_arr = new Array();
			for(var j=0;j<all_product_val.length;j++){
				parse_arr[j] = parseInt(all_product_val[j]);
			}
			actual_mass_product_id = parse_arr;
		}
	}
	
	//For upload Excluded selected product
	if(action_type == 'exclude_selected_product'){
		if(all_product_val == null){
			alert('Please Select Any product to exclude From Upload');
		}else{
			var parse_arr = new Array();
			for(var j=0;j<all_product_val.length;j++){
				parse_arr[j] = parseInt(all_product_val[j]);
			}
			jQuery.grep(all_mask_product, function(el) {
				if (jQuery.inArray(el,parse_arr) == -1){
					actual_mass_product_id.push(el);
				}
			});
		}
	}
	
	var bulk_upload_nc = jQuery('#mass_upload_nonce_chk').val();
	if(actual_mass_product_id != ''){
		jQuery.post(
			ajaxurl,
			{
				'action' : call_action,
				'mass_upload_type': action_type,
				'all_mass_product_id':actual_mass_product_id,
				'bupbm_nonce':bulk_upload_nc,

			},
			function(response){
				alert('Thanks,Your Mass Action Has been Schedule In Background.');
			});
	}
});

jQuery(document).ready(function(){
	jQuery('#priceTypeSelect').change(function() {
		var selected = jQuery(this).val();
		if(selected == 'sale_price')
		{
			jQuery('#jetPriceField').hide();
			jQuery('#fullfillmetWisePrice').hide();
		}
		else if(selected == 'main_price')
		{
			jQuery('#jetPriceField').hide();
			jQuery('#fullfillmetWisePrice').hide();
		}
		else if(selected == 'otherPrice')
		{
			jQuery('#fullfillmetWisePrice').hide();
			jQuery('#jetPriceField').show();
		}
		else if(selected == 'fullfillment_wise')
		{
			jQuery('#jetPriceField').hide();
			jQuery('#fullfillmetWisePrice').show();
		}
	});
	
	jQuery('#stockTypeSelect').change(function() {
		var selected = jQuery(this).val();
		if(selected == 'central')
		{
			jQuery('#jetStockField').hide();
			jQuery('#fullfillmetWiseStock').hide();
		}
		else if(selected == 'default')
		{
			jQuery('#jetStockField').hide();
			jQuery('#fullfillmetWiseStock').hide();
		}
		else if(selected == 'other')
		{
			jQuery('#fullfillmetWiseStock').hide();
			jQuery('#jetStockField').show();
		}
		else if(selected == 'fullfillment_wise')
		{
			jQuery('#jetStockField').hide();
			jQuery('#fullfillmetWiseStock').show();
		}
	});
});

jQuery(document).ready(function(){	
	
	jQuery("#catMapButton").live('click',function(){
		var wooCatID	=	jQuery('#wooSelectedCat').val();
		var jetCatID	=	jQuery('#jetInsertedCatID').val();
		
		if(!(jQuery('#jetInsertedCatID').val())){
			alert('please provide jet category ID');
			jQuery(".jetCatIdTextField").val();
			jQuery("#wooSelectedCat").val();
			return;
		}
		
		if(wooCatID == 'none'){
			alert('please select woo category');
			jQuery(".jetCatIdTextField").val();
			jQuery("#wooSelectedCat").val();
			return;
		}
		
		jQuery( "#jet-loading" ).show();
		jQuery.post(
			ajaxurl,
			{
				'action' : 'cedJetCategoryMapping',
				'wooCatID': wooCatID,
				'jetCatID':jetCatID,
				
			},
			function(response){
				jQuery( "#jet-loading" ).hide();
				if(response != ''){
					var all_mapped_cat = jQuery.parseJSON(response);
					if(typeof(all_mapped_cat) === 'string'){
						alert(eval(response));
					}
					else{
						var html = '';
						for(var i = 0;i<all_mapped_cat.length;i++){
							html += '<tr>';
							html +='<td>'+all_mapped_cat[i]['woo_cat_name']+'</td>';
							html +='<td>'+all_mapped_cat[i]['jet_cat_id']+'</td>';
							html +='<td>';
							html +='<button value="'+all_mapped_cat[i]['woo_cat_id']+'" class="editMappedCat">Edit</button>';
							html +='<button value="'+all_mapped_cat[i]['woo_cat_id']+'" class="deleteMappedCat">Delete</button>';
							html +='</td>';
							html +='</tr>';
						}
						jQuery('.mapped_jet_cat').html(html);
						
					}	
				}
				jQuery(".jetCatIdTextField").val();
				jQuery("#wooSelectedCat").val();
			});
});

jQuery('.editMappedCat').live('click',function(){
	
	var wooCatId	=	jQuery(this).val();
	var html =	'Jet Category ID:<select  id="jetCatId"><p class="jetCatbutton" style="display: none;"><input class="fancy_map" type="button" onclick="jQuery.fancybox.close()" value="Map" ></select><span class="fancy_map_span" style="color:blue;">Wait Jet Category Loading....</span></p>';
	jQuery.fancybox.open({
		'content' :html,
		'width' : 700,
		'height': 50,
		'closeBtn' : false,
		'beforeShow' : function(){
			
			jetCatId = jQuery('#jetCatId').val();
		},
		'beforeClose' : function(){
			jetCatId = jQuery('#jetCatId').val();
		},
		'afterClose': function(){
			if(jetCatId ==null)
			{
				return;
			}
			jQuery.post(
				ajaxurl,
				{
					'action'	: 	'updateMappedCatId',
					'wooCatId'	:	wooCatId,
					'jetCatId' 	: 	jetCatId
				},
				function(response){
					
							//alert(response);
							
							if(response != ''){
								var all_mapped_cat = jQuery.parseJSON(response);
								if(typeof(all_mapped_cat) === 'string'){
									alert(eval(response));
								}
								else{
									var html = '';
									for(var i = 0;i<all_mapped_cat.length;i++){
										html += '<tr>';
										html +='<td>'+all_mapped_cat[i]['woo_cat_name']+'</td>';
										html +='<td>'+all_mapped_cat[i]['jet_cat_id']+'</td>';
										html +='<td>';
										html +='<button value="'+all_mapped_cat[i]['woo_cat_id']+'" class="editMappedCat">Edit</button>';
										html +='<button value="'+all_mapped_cat[i]['woo_cat_id']+'" class="deleteMappedCat">Delete</button>';
										html +='</td>';
										html +='</tr>';
									}
									jQuery('.mapped_jet_cat').html(html);
									
								}	
							}
							});//function response
						} //function after close
		}); //facny box end
jQuery(document).find( '#jetCatId' ).hide();
jQuery(document).find( '.fancy_map_span' ).show();
jQuery(document).find( '.fancy_map' ).hide();
jQuery.post(
	ajaxurl,
	{
		'action' : 'get_all_jet_category',
	},
	function(response){
		jQuery(document).find( '.fancy_map_span' ).hide();
		jQuery(document).find( '.fancy_map' ).show();
		jQuery(document).find( '#jetCatId' ).show();
		var all_jet_cat = jQuery.parseJSON(response);
		var drop_val = '';
		for(var i = 0;i<all_jet_cat.length;i++){
			drop_val += '<option value="'+all_jet_cat[i]["csv_cat_id"]+'">'+all_jet_cat[i]["name"]+'</option>';
		}
					//jQuery('#jetInsertedCatID').val();
					//jQuery('.cat_image_load').hide();
					//jQuery('#jetInsertedCatID').show();
					jQuery('.jetCatbutton').show();
					
					jQuery('#jetCatId').select2()
					jQuery('#jetCatId').html(drop_val);
					
					
				});
});

jQuery('.deleteMappedCat').live('click',function(){
	var r = confirm("Are you sure?");
	if(r== true)
	{
		var wooCatId	=	jQuery(this).val();
		jQuery.post(
			ajaxurl,
			{
				'action' : 'cedDeleteMappedCat',
				'wooCatID': wooCatId,
				
			},
			function(response){
				if(response != ''){
					var all_mapped_cat = jQuery.parseJSON(response);
					if(typeof(all_mapped_cat) === 'string'){
						alert(eval(response));
					}
					else{
						var html = '';
						for(var i = 0;i<all_mapped_cat.length;i++){
							html += '<tr>';
							html +='<td>'+all_mapped_cat[i]['woo_cat_name']+'</td>';
							html +='<td>'+all_mapped_cat[i]['jet_cat_id']+'</td>';
							html +='<td>';
							html +='<button value="'+all_mapped_cat[i]['woo_cat_id']+'" class="editMappedCat">Edit</button>';
							html +='<button value="'+all_mapped_cat[i]['woo_cat_id']+'" class="deleteMappedCat">Delete</button>';
							html +='</td>';
							html +='</tr>';
						}
						jQuery('.mapped_jet_cat').html(html);
						
					}	
				}
				alert('Category Deleted Successfully');
			});
	}
});

jQuery('.selectit input[type="checkbox"]').click(function(){

	var wooCatId 	= jQuery(this).attr('value');

	if (jQuery(this).is( ":checked" )){
		jQuery.post(
			ajaxurl,
			{
				'action' : 'addDynamicCatAttr',
				'wooCatId': wooCatId,

			},
			function(response){
				jQuery("#jet_attribute_settings").append(response);
						//jQuery('.variable_cat_body').append(response);
					});
	}
	else{
		jQuery('.options_group[data-wid="'+wooCatId+'"]').remove();
	}
});

jQuery(".expand-image").live('click',function(){
	var divId	=	jQuery(this).attr("value");

	jQuery("#"+divId).toggle();
});

	/**
	 * variable price hide and show 
	 */
	 jQuery(".variation_price").live('change',function(){
	 	var val =  jQuery(this).val();

	 	if(val == 'otherPrice'){
	 		jQuery(this).parent().next().next().hide();
	 		jQuery(this).parent().next().show();
	 	}

	 	if(val == 'fullfillment_wise'){
	 		jQuery(this).parent().next().next().show();
	 		jQuery(this).parent().next().hide()
	 	}

	 	if(val == 'main_price' || val == 'sale_price'){
	 		jQuery(this).parent().next().next().hide();
	 		jQuery(this).parent().next().hide()

	 	}
	 });



	/**
	 * variable stock hide and show 
	 */
	 jQuery(".variation_stock").live('change',function(){
	 	var val =  jQuery(this).val();

	 	if(val == 'other'){
	 		jQuery(this).parent().next().next().hide();
	 		jQuery(this).parent().next().show();
	 	}

	 	if(val == 'fullfillment_wise'){
	 		jQuery(this).parent().next().next().show();
	 		jQuery(this).parent().next().hide()
	 	}

	 	if(val == 'central' || val == 'default'){
	 		jQuery(this).parent().next().next().hide();
	 		jQuery(this).parent().next().hide()

	 	}
	 });

	//variable category show and hide
	jQuery(".variable_cat_heading").live('click',function(){
		jQuery(".variable_cat_body").hide();
		jQuery(this).next().toggle();
	});
	
	//remove the assigned profile.
	jQuery(".remove_profile").live('click',function(){
		
		var productId	=	jQuery(this).attr("value");
		
		var r = confirm("Are You Confirm to delete these profile");
		if (r == true) 
		{
			jQuery.post(
				ajaxurl,
				{
					'action' : 'removeProfileId',
					'pid'	 : productId,
					
				},
				function(response){
					jQuery("#profile_"+productId).empty();
					alert(response);
				});
			window.location.reload();
		}else{
			return;
		}
	});
	
	
	/** 
	 * Delete order of jet type
	 */

	 jQuery('#order_delete_action').click(function(){
	 	var file_upload_type 	= 	jQuery('#drop_order_action').val();
	 	var all_upload_product 	= 	new Array();
	 	var i 					= 	0;
	 	var check_id;
	 	var order_del_nonce		=	jQuery('#delete_jet_norder').val();

	 	jQuery('.product_row').each(function(){
	 		if(jQuery(this).find('.unique_check').is(':checked'))
	 		{
	 			all_upload_product[i] = jQuery(this).find('.unique_check').val();
	 			i++;
	 		}
	 	});

	 	if(all_upload_product.length == 0){
	 		alert('Please Select any order for delete');
	 	}
	 	else if(file_upload_type != 'delete'){
	 		alert('Please Select any Action');
	 	}
	 	else{

	 		jQuery( "#jet-loading" ).show();
	 		// jQuery.post(
	 		// 	ajaxurl,
	 		// 	{
	 		// 		'action' : 'delete_jet_order',
	 		// 		'upload_type':file_upload_type,
	 		// 		'all_delete_product_id':all_upload_product,
	 		// 		'djo_nonce':order_del_nonce,
	 		// 	},
	 		// 	function(response){
	 		// 		jQuery( "#jet-loading" ).hide();
				// 		//console.log(response);
				// 		window.location.reload();
				// 	}
				// 	);
}
});


	/**
	 * Delete error file 
	 */


	 jQuery('#error_file_delete_action').click(function(){
	 	var file_upload_type 	= 	jQuery('#drop_error_action').val();
	 	var all_upload_product 	= 	new Array();
	 	var file_del_nonce		=	jQuery('#error_file_delete_nonc').val();
	 	var i 					= 	0;

	 	var check_id;
	 	jQuery('.product_row').each(function(){
	 		if(jQuery(this).find('.unique_check').is(':checked'))
	 		{
	 			all_upload_product[i] = jQuery(this).find('.unique_check').val();
	 			i++;
	 		}
	 	});

	 	if(all_upload_product.length == 0){
	 		alert('Please Select any file for delete');
	 	}
	 	else if(file_upload_type != 'delete_error_file'){
	 		alert('Please Select any Action');
	 	}
	 	else{

	 		jQuery( "#jet-loading" ).show();
	 		jQuery.post(
	 			ajaxurl,
	 			{
	 				'action' : 'delete_jet_error_file',
	 				'upload_type':file_upload_type,
	 				'all_error_file_id':all_upload_product,
	 				'def_nonce':file_del_nonce,
	 			},
	 			function(response){
	 				jQuery( "#jet-loading" ).hide();
						//console.log(response);
						window.location.reload();
					}
					);
	 	}
	 });


	/**
	 * Delete reject order
	 */

	 jQuery('#reject_order_delete').click(function(){
	 	var file_upload_type 	= 	jQuery('#reject_order_delete_action').val();
	 	var all_upload_product 	= 	new Array();
	 	var i 					= 	0;
	 	var del_failed_nonce	=	jQuery('#delete_jet_nforder').val();

	 	var check_id;
	 	jQuery('.product_row').each(function(){
	 		if(jQuery(this).find('.unique_check').is(':checked'))
	 		{
	 			all_upload_product[i] = jQuery(this).find('.unique_check').val();
	 			i++;
	 		}
	 	});

	 	if(all_upload_product.length == 0){
	 		alert('Please Select any reject order for delete');
	 	}
	 	else if(file_upload_type != 'reject_delete'){
	 		alert('Please Select any Action');
	 	}
	 	else{

	 		jQuery( "#jet-loading" ).show();
	 		jQuery.post(
	 			ajaxurl,
	 			{
	 				'action' : 'delete_jet_reject_order',
	 				'upload_type':file_upload_type,
	 				'all_reject_order_id':all_upload_product,
	 				'dfo_nonce':del_failed_nonce,
	 			},
	 			function(response){
						//alert(response);
						jQuery( "#jet-loading" ).hide();
						//console.log(response);
						window.location.reload();
					}
					);
	 	}
	 });

jQuery('a').each(function() {
	jQuery("a[href^='options-general.php?page=bulk_product_upload_page']").remove();
	jQuery("a[href^='options-general.php?page=jet_settlement_order']").remove();
	jQuery("a[href^='options-general.php?page=jet_settlement_return']").remove();
	jQuery("a[href^='options-general.php?page=enable_return_api']").remove();
	jQuery("a[href^='options-general.php?page=configure_return_settings']").remove();
	jQuery("a[href^='options-general.php?page=configure_extra_settings']").remove();
	jQuery("a[href^='options-general.php?page=profile_settings']").remove();
	jQuery("a[href^='options-general.php?page=jet_order_refund_submit_page']").remove();
});



});


/**
 * Mass Product Inventory submit
 */
 jQuery(document).on('click','#jet_inventory_syncronize',function(){

 	jQuery( "#jet-loading" ).show();
 	var sync_nonce_check = jQuery('#inv_sync_nonce_check').val();

 	jQuery.post(
 		ajaxurl,
 		{
 			'action' : 'jet_inventory_syncronize',
 			'upload_type':'inventory',
 			'inp_nonce':sync_nonce_check,
 		},
 		function(response){
 			jQuery( "#jet-loading" ).hide();
 			return true;			
 		}
 		);
 });

/**
 * Change prodfile product dropdown
 */
 jQuery(document).on('change','select[name="linked_proID"]',function(){
 	var id = jQuery(this).val();
 	var html = '';
 	jQuery( "#jet-loading" ).show();
 	jQuery.post(
 		ajaxurl,
 		{
 			'action' : 'get_meta_fields',
 			'id':id
 		},
 		function(response){

 			var jsonResponse = jQuery.parseJSON(response);
 			jQuery.each( jsonResponse, function( key, value ) {
 				html += '<tbody id="pro_id_append">';
 				html += '<tr class="iedit author-self mobicnct-banner-listing level-0 post-'+ key +' type-product status-publish hentry product_row" id="'+ key +'">';
 				html += '<td class="check-column" scope="row">';
 				html += '<label for="cb-select-'+ key +'" class="screen-reader-text"></label>';
 				html += '<input type="checkbox" value="'+ key +'" class="unique_check" name="unique_post[]" id="cb-select-'+ key +'">';
 				html += '<div class="locked-indicator"></div></td>';
 				html += '<td class="name column-id" >';
 				html += '<span class="b_id" >'+ key +'</span></td>';
 				html += '<td class="name column-id" >';
 				html += '<span class="b_id" >'+ value +'</span>';
 				html += '</td></tr></tbody>';
 			});

 			jQuery("#pro_id_append").replaceWith(html);
 			jQuery( "#jet-loading" ).hide();
 		}
 		);
});

/**
 * Enable demo jet api 
 */
 jQuery(document).on('click','#enable_jet_api',function(){
 	jQuery( "#jet-loading" ).show();
 	jQuery.post(
 		ajaxurl,
 		{
 			'action':'enable_demo_jet_api'
 		},function(response){
 			jQuery( "#jet-loading" ).hide();
 			alert(response);
 			window.location.reload();
 		}
 		);
 });

/**
 * For refund order select
 */

 jQuery(document).ready(function() { 
	// need to reinitialize select2 for options to take effect

	jQuery('#jetInsertedCatID').select2({
		placeholder: "Please Select category"
	});
	jQuery('#jetInsertedCatID').hide();
	jQuery('.jetCatbutton').hide();
	
	jQuery('#mass_cat_assign_manage').select2({
		placeholder: "Please Select category"
	});
	jQuery('#mass_cat_assign_manage').hide();
	
	jQuery('#file_upload').select2({
		placeholder: "Please Select category"
	});
	jQuery('#file_upload').hide();
	
	jQuery('.shipping_carrier_used').select2({
	});
	jQuery('.shipping_carrier_used').hide();
	
});
 

 jQuery(document).on('click','#Mapped_bulk_category_with_products',function(){
 	var all_mapped_product 	= 	new Array();
 	var all_selected_cat	=	new Array();
 	var check_id;
 	var i 					=  	0;	
 	jQuery('.product_row').each(function(){
 		if(jQuery(this).find('.unique_check').is(':checked'))
 		{
 			var pro_id = jQuery(this).find('.unique_check').val();
 			var cat_id = jQuery(this).find('#mass_cat_assign').val();
 			if((pro_id != null || pro_id != '') && cat_id != 'not_selected'){
 				all_mapped_product[i] 	= pro_id;
 				all_selected_cat[i] 	= cat_id;
 				i++;
 			}
 		}
 	});
 	if(all_mapped_product.length == 0 || all_selected_cat.length == 0 ){
 		alert('Atleast any one product is map with any one category');
 	}
 	else{
 		jQuery( "#jet-loading" ).show();
 		jQuery.post(
 			ajaxurl,
 			{
 				'action' : 'mass_category_assign_to_products',
 				'upload_type':'mass_category_mapping_with_products',
 				'selected_product_ids':all_mapped_product,
 				'selected_cat_ids':all_selected_cat,
 			},
 			function(response){
 				jQuery( "#jet-loading" ).hide();
 				window.location.reload();
 			}
 			);
 	}
 });


/**
 * Assign profile to products from mass category assign 
 */
 jQuery(document).on('click','#Mapped_bulk_profile_check_products',function(){
 	var all_mapped_product 			= 	new Array();
 	var profile_assign_nonce 		=	jQuery('#check_profile_assign_on_cat').val();
 	var profile_id					=	jQuery('#list_all_profile_on_cat').val();
 	var check_id;
 	var i 					=  	0;	
 	jQuery('.product_row').each(function(){
 		if(jQuery(this).find('.unique_check').is(':checked'))
 		{
 			var pro_id = jQuery(this).find('.unique_check').val();
 			if((pro_id != null && pro_id != '')){
 				all_mapped_product[i] 	= pro_id;
 				i++;
 			}
 		}
 	});

 	if(all_mapped_product.length == 0){
 		alert('Please Select Any One Product For Assign Profile.');
 	}else if(profile_id == ''){
 		alert('Please Select Any One Profile to Assign Products.');
 	}
 	else{

 		jQuery( "#jet-loading" ).show();
 		jQuery.post(
 			ajaxurl,
 			{
 				'action' : 'mass_profile_assign_to_products_by_check',
 				'upload_type':'mass_profile_assign_by_check_products',
 				'selected_product_ids':all_mapped_product,
 				'profile_id':profile_id,
 				'paoca_nonce':profile_assign_nonce,

 			},
 			function(response){
 				jQuery( "#jet-loading" ).hide();
 				alert(response);
 				window.location.reload();
 			}
 			);
 	}

 });

/**
 * uploading selected products on jet.
 */
 jQuery(document).on('click','#jet_mass_cat_assign',function(){
 	var category 	= 	jQuery('#mass_cat_assign_manage').val();
 	var all_upload_product 	= 	new Array();
 	var i 					= 	0;
 	var check_id;
 	var product_upload_nonce = jQuery('#product_upload_nonce_check').val();

 	jQuery('.product_row').each(function(){
 		if(jQuery(this).find('.unique_check').is(':checked'))
 		{
 			all_upload_product[i] = jQuery(this).find('.unique_check').val();
 			i++;
 		}
 	});
 	if(all_upload_product.length == 0){
 		alert('Please Select any Product To Map');
 	}
 	else if(category=='not_selected'){
 		alert('Please select a valid category');
 	}
 	else{
 		jQuery( "#jet-loading" ).show();
 		jQuery.post(
 			ajaxurl,
 			{
 				'action' : 'mass_cat_manage_product',
 				'cat_id':category,
 				'all_upload_product_id':all_upload_product,
 				'pupload_nonce':product_upload_nonce,
 			},
 			function(response){
 				jQuery( "#jet-loading" ).hide();
 				window.location.reload();
 			}
 			);
 	}

 });
