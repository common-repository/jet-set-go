/**
 * adding fulfillment box for multiple fulfillment ids.
 */
jQuery('#add_fullfillment').click(function(){
	var id,name;
	id = jQuery('#all_fullfillment_node').attr('value');
	id = parseInt(id);
	id = id + 1;
	
	var append_fullfillment   = '<fieldset class="list_fullfillment" data-id="'+id+'" id="fullfillment_'+id+'">';
		append_fullfillment  += '<input type="text" class="regular-text" value="" placeholder="Enter node id" id="node_id_'+id+'" name="node_id_'+id+'"><a href="javascript:void(0)" class="remove_fullfillment" id="'+id+'">Click For remove</a>';
		append_fullfillment  += '</fieldset>';
	jQuery('.multiple_fullfillment').append(append_fullfillment);
	jQuery('#all_fullfillment_node').attr('value',id);
});

/**
 * removing fulfillment id box.
 */
jQuery(document).on('click','.remove_fullfillment',function(){ 
	var remove_id = jQuery(this).attr('id');
	remove_id 	  = parseInt(remove_id);
	var last;
	var r = confirm("Are You Confirm to delete these Fullfillment");
    if (r == true) 
    {
    	var del_fullfillment_value = jQuery('#node_id_'+remove_id).val();
    	jQuery('#fullfillment_'+remove_id).remove();
    	last = jQuery('.list_fullfillment:last-child').data('id');
    	jQuery('#all_fullfillment_node').attr('value',last);
    	jQuery( "#jet-loading" ).show();
		jQuery.post(
				ajaxurl,
				{
					'action' : 'remove_fullfillment_action',
					'remove_id': del_fullfillment_value,
					'remove_action':'remove_action',
				},
				function(response){
					alert(del_fullfillment_value+' Is Deleted Successfully');
					jQuery( "#jet-loading" ).hide();
				}
			);
   }else{
      return;
   }
});


/**
 * adding return id box for multiple return ids.
 */
jQuery('#add_returnid').click(function(){
var rid,rname;

rid = jQuery('#all_return_ids').attr('value');
rid = parseInt(rid);
rid = rid + 1;

var append_returnid   = '<fieldset class="list_returnid" data-id="'+rid+'" id="returnid_'+rid+'">';
append_returnid  += '<input type="text" class="regular-text" value="" placeholder="Enter return id" id="return_id_'+rid+'" name="return_id_'+rid+'"><a href="javascript:void(0)" class="remove_returnid" id="'+rid+'">Click For remove</a>';
append_returnid  += '</fieldset>';
jQuery('.multiple_returnid').append(append_returnid);
jQuery('#all_return_ids').attr('value',rid);
});

/**
 * removing return ids.
 */
jQuery(document).on('click','.remove_returnid',function(){ 
var remove_id = jQuery(this).attr('id');
remove_id 	  =  parseInt(remove_id);

var last;
var r = confirm("Are You Confirm to delete this return id");
if (r == true) 
{
	var del_retrn_value = jQuery('#return_id_'+remove_id).val();
	jQuery('#returnid_'+remove_id).remove();
	last = jQuery('.list_returnid:last-child').data('id');
	jQuery('#all_return_ids').attr('value',last);
	jQuery( "#jet-loading" ).show();
	jQuery.post(
			ajaxurl,
			{
				'action' : 'remove_returnid_action',
				'remove_id': del_retrn_value,
				'remove_action':'remove_action',
			},
			function(response){ 
				alert(del_retrn_value+' Is Deleted Successfully');
				jQuery( "#jet-loading" ).hide();
				}
			);
   }else{
      return;
   }
});