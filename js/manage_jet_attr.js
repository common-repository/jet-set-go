/**
 * mapping of categories 
 */

var ajaxurl 	= 	map_cat.ajaxurl;
var delmapcat	=	map_cat.delmapcat;
var	editmapcat	=	map_cat.edmapcat;


jQuery(window).load(function() {
	jQuery( '.editMappedCat' ).attr( 'disabled', 'disabled' );
	
	var editmapcat_secure = editmapcat;
	
	jQuery.post(
			ajaxurl,
			{
				'action' : 'get_all_jet_category',
				'edit_sec':	editmapcat_secure,
			},
			function(response){
				jQuery( '.editMappedCat' ).removeAttr( 'disabled' );
				var all_jet_cat = jQuery.parseJSON(response);
				var drop_val = '';
				for(var i = 0;i<all_jet_cat.length;i++){
					drop_val += '<option value="'+all_jet_cat[i]["csv_cat_id"]+'"rel="'+all_jet_cat[i]["name"]+'">'+all_jet_cat[i]["name"]+'</option>';
				}
				
				//jQuery('#jetInsertedCatID').val();
				jQuery('.cat_image_load').hide();
				jQuery('#jetInsertedCatID').show();
				jQuery('.jetCatbutton').show();
				//jQuery('#jetInsertedCatID').select2('data', null)
				jQuery('#jetInsertedCatID').html(drop_val);
				 
				
			}
		);
	
	// insert code here
	});

		jQuery("#catMapButton").live('click',function(){
		var wooCatID	=	jQuery('#wooSelectedCat').val();
		var jetCatID	=	jQuery('#jetInsertedCatID').val();
		var jetcatname	=	jQuery('#jetInsertedCatID').find(':selected').attr("rel");
		//alert(jetcatname);
		
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
		
		var cat_map_nonce = jQuery('#catmapnonce').val();
		jQuery( "#jet-loading" ).show();
		jQuery.post(
				ajaxurl,
				{
					'action' : 'cedJetCategoryMapping',
					'wooCatID': wooCatID,
					'jetCatID':jetCatID,
					'jet_security':cat_map_nonce,
					'jetCatName':jetcatname,
					
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
					'action' : 'update_edit_cat',
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
		//var delcatmap   =   jQuery('#delmapcat').val();
		
		var r = confirm("Are you sure?");
		if(r== true)
		{
			var wooCatId	=	jQuery(this).val();
			
			jQuery.post(
				ajaxurl,
				{
					'action' : 'cedDeleteMappedCat',
					'wooCatID': wooCatId,
					'del_secure':delmapcat,
					
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
	