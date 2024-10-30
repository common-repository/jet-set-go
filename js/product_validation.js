jQuery(document).ready(function(){
	var get_list_product 	= 	new Array();
	var i 					= 	0;
	
	jQuery('.product_row').each(function(){
			get_list_product[i] = jQuery(this).find('.unique_check').val();
			i++;
	});
	
	if(get_list_product.length == 0){
		return;
	}
	else{
		jQuery.post(
				ajaxurl,
				{
					'action' :'validate_product_on_jet',
					'all_list_product_id':get_list_product,
				},
				function(response){
					console.log(response);
					var all_validated_data = jQuery.parseJSON(response);
					console.log(all_validated_data);
					//jQuery('.validation_message p').css('color','red');
					if(all_validated_data.length != 0 && all_validated_data[0] != 'error'){
						jQuery('.product_row').each(function(){
							var row_pid = jQuery(this).find('.unique_check').val();
							for (i in all_validated_data) {
								var error_message = '';
							   if(row_pid == i){
								   var type = all_validated_data[i]['product_type'];
								   if(type == 'variable'){
									   for(var_product_error in all_validated_data[i]){
										   if(var_product_error != 'product_type'){
											   var var_count = '';
											   error_message = '';
											   var_count = all_validated_data[i][var_product_error].length;
											   var_count = parseInt(var_count);
											   //alert(var_count);
											   if(var_count > 0){
												   //error_message += '<span><b>Error For Product id:'+var_product_error+'</b></span>';
												   error_message += '<div class="actual_validation_error_message"><p><span><a href="javascript:void(0)" class="error_exist" style="color:red">Not Ready</a></span></p>';
												   error_message += '<span class="upload_validation_error" style="display:none;"><ol style="color:red;">';
											   for(var k = 0 ;k < var_count;k++){ 
												      error_message +='<li><p>'+all_validated_data[i][var_product_error][k]+'</p></li>';
												}
											   		error_message += '</ol></span></div>'
											   }
											   if(error_message != ''){
												   jQuery(this).find('td:nth-child(12) .validation_message').append(error_message);
												   //jQuery(this).find('td:nth-child(12) .validation_message span').css('color','red');
											   }
											   
											   if(error_message == ''){
												   jQuery(this).find('td:nth-child(12) .validation_message').append('<p style="color:green"><span>Ready</span></p>');
												   //jQuery(this).find('td:nth-child(11) .validation_message p').css('color','green');
											   }
										   }//end if
									   }//end for loop for simple product
									   
								   }
								   
								   if(type == 'simple'){
									   for(product_error in all_validated_data[i]){
										   if(product_error != 'product_type'){
											   var count = all_validated_data[i][product_error].length;
											   count = parseInt(count);
											   if(count > 0){
												   error_message += '<div class="actual_validation_error_message"><p><span><a href="javascript:void(0)" class="error_exist" style="color:red">Not Ready</a></span></p>';
												   error_message += '<span class="upload_validation_error" style="display:none;"><ol style="color:red;">';
												   for(var j = 0 ;j< count;j++){ 
												      error_message +='<li><p>'+all_validated_data[i][product_error][j]+'</p></li>';
												   }
												   error_message += '</ol></span></div>'
											   }
											   
										   }//end if
									   }//end for loop for simple product
									   if(error_message != ''){
										   jQuery(this).find('td:nth-child(12) .validation_message').append(error_message);
										   //jQuery(this).find('td:nth-child(11) .validation_message p').css('color','red');
									   }
									   
									   if(error_message == ''){
										   jQuery(this).find('td:nth-child(12) .validation_message').append('<p style="color:green"><span>Ready</span></p>');
										  // jQuery(this).find('td:nth-child(11) .validation_message p').css('color','green');
									   }
								   }
								   
							   }
								     
							}
							
							//jQuery(this).find('td:nth-child(11) .validation_message').append(error_message);
						
						});//end loop
					}
					
				}
			);
	}
	
	//
	
});

jQuery(document).on('click','.error_exist',function(){
	var msg = jQuery(this).parent().parent().next().html();
	
	jQuery.fancybox.open({
		'content' :msg,
		'width' : 700,
		'height': 50,
	});
});

//upload by category
jQuery(document).on('click','#submit_bycat_upload',function(){
	var woo_cat_id		= 	jQuery('#upload_product_by_cat').val();
	var action_type		= 	'all_product';
	var bulk_by_cat		=	jQuery('#bulk_by_cat').val();
	
	
	if(woo_cat_id != 'not_selected'){
		jQuery("#jet-loading").show();
		jQuery.post(
			ajaxurl,
			{
				'action' : 'mass_upload_by_category',
				'cat_mass_upload_type': action_type,
				'mubc_nonce':bulk_by_cat,
				'selected_cat_id':woo_cat_id,

			},
			function(response){
				jQuery("#jet-loading").hide();
						//alert(response);
						alert('Thanks,Your Mass Product Upload by Categories Has been Schedule In Background.');
					});
	}
});

//mapped profile with product id in bulk
jQuery(document).on('click','#map_mass_product_profiles',function(){
	
	var profile_id 			= 	jQuery('#list_all_profile').val();
	var all_product_ids		=	jQuery('#list_map_profile_product').val();
	var mass_pro_assgn		=	jQuery('#massproassign').val();
	
	if(profile_id !== null && all_product_ids !== null){
		jQuery("#jet-loading").show();
		jQuery.post(
			ajaxurl,
			{
				'action' : 'mass_profile_mapping',
				'profile_id': profile_id,
				'all_map_products_id':all_product_ids,
				'mpa_nonce':mass_pro_assgn,

			},
			function(response){
				jQuery("#jet-loading").hide();
				alert(response);
			});
	}else{
		alert('Please Select any product and any one Profile For Map.');
	}
	
	
});


/**
* Load all products
*/

jQuery( window ).load(function(e) {
	get_productss();
});
var pro_counter = 1;

function get_productss(){

	// Run code
	
	var action_type = 'products';
	// jQuery(document).on( 'change', '#list_map_profile_product',function(){
		
		jQuery.post(
			ajaxurl,
			{
				'action' : 'get_all_product_of_jet',
				'action_type': action_type,
				'counter' : pro_counter,

			},
			function(response)
			{console.log(response);
				var html = '';
				pro_counter++;
				var all_mask_product={};
					//if(response !== null || response !== 0 || typeof response ==='object' || typeof response !== 'undefined' ){
						var all_product = jQuery.parseJSON(response);

						if(all_product == null || all_product == 0)
						{
							return;
						}
						else
						{ 

							html += '';
						//html +='<select id="mass_product_upload" multiple="multiple" name="bulk_product_multi" class="select">';
						for(var i in all_product['id'])
						{
							if(all_product['id'][i] == 'error')
							{
								continue;
							}
							
							html += '<option value='+all_product['id'][i]+' >'+all_product['name'][i]+'</option>';
							all_mask_product[i] = all_product['id'][i];
						}
						// html += '</select>';
						if(pro_counter != 1)
						{
							var exstdData = jQuery('#list_map_profile_product').html();
							exstdData += html;
							jQuery('#list_map_profile_product').html(exstdData);
						}else
						{
							jQuery('#list_map_profile_product').select2('data', null)
							jQuery('#list_map_profile_product').html(html);
						}

						 //jQuery('#list_map_profile_product').trigger('change');
						 get_productss();
						}
					//}
				}
				);


	/*jQuery(document).on( 'change', '#list_map_profile_product',function(e){
		
		jQuery.post(
				ajaxurl,
				{
					'action' : 'get_all_product_of_jet',
					'action_type': action_type,
					'counter' : pro_counter,
					
				},
				function(response){
					var html = '';
					
					//if(response !== null || response !== 0 || typeof response ==='object' || typeof response !== 'undefined'){
					 var all_product = jQuery.parseJSON(response);
					 console.log(all_product);
					 if(all_product == null || all_product == 0) {
						 if(pro_counter != 1){
							 return false;
						 }else{
							 return false;
						 }
					}
					 else{ 
					 	html += '';
						//html +='<select id="mass_product_upload" multiple="multiple" name="bulk_product_multi" class="select">';
						console.log("enter correct");
						for(var i in all_product['id'])
						{
							 html += '<option value='+all_product['id'][i]+' >'+all_product['name'][i]+'</option>';
							 all_mask_product[i] = all_product['id'][i];
						}
						// html += '</select>';
						 if(pro_counter != 1){
							 var exstdData = jQuery('#list_map_profile_product').html();
							 exstdData += html;
							 jQuery('#list_map_profile_product').html(exstdData);
							 
						 }else{
							 jQuery('#list_map_profile_product').select2('data', null)
							 jQuery('#list_map_profile_product').html(html);
							
						 }
						 pro_counter++;
						 jQuery('#list_map_profile_product').trigger('change');
						 
					 }
				//  } 
			});*/
			//});
	//});

}

