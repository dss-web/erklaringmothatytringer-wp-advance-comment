jQuery(document).on( 'change', '.wpad_select_all' , function(){
 	if( jQuery(this).is(':checked') ){
		jQuery(this).closest('li').nextAll('li').find('input').prop( 'checked' , true );
	} else {
		jQuery(this).closest('li').nextAll('li').find('input').prop( 'checked' , false );
	}
 });

 jQuery(document).on( 'click' , '.wpad_choose_posts' , function(){
 	jQuery(this).next('ul').slideToggle();
 });

 jQuery(document).on( 'change' , '[name="replace_comment_form"]' , function(){
 	if( jQuery(this).is(':checked') && jQuery(this).val() == 'enable_certain_pages' ){
 		jQuery('.wpad_pages_posts').slideDown('fast');
 	} else {
		jQuery('.wpad_pages_posts').slideUp('fast');	
	}
 });

/***********************************
JS for Total Comments Form List Page
***********************************/

 function wpad_bulk_action( redirectUrl, arg1, value1, arg2, value2 , action ) {
   	var form = jQuery('<form action="' + redirectUrl + '" method="post">' +
   	'<input type="hidden" name="'+ arg1 +'" value="' + value1 + '"></input>' + 
   	'<input type="hidden" name="'+ arg2 +'" value="' + value2 + '"></input>' + 
   	'<input type="hidden" name="' + action + '" value="1"></input>' + 
   	'</form>');
   	jQuery('body').append(form);
   	jQuery(form).submit();
 }

 jQuery(document).on( 'click' , '.apply_bulk_comment_form_list' , function(){
 	var count = 0;
 	var commene_ids = [];

 	jQuery('.comment_forms_checkbox').each(function(){
 		if( jQuery(this).find('input[type="checkbox"]').is(':checked') ){
 			commene_ids[count] = jQuery(this).find('input[type="checkbox"]').val();
 			count++;
 		}
 	});

 	if( jQuery('.wpad_bulk_action select').val() == '' ){
 		alert( 'Please select an option from the dropdown.' );
 		return;
 	}

 	if( count < 1 ){
 		alert( 'Select some comment forms.' );
 		return;
 	}

 	var bulk_action_option = jQuery('.wpad_bulk_action select').val();
 	var url = translate.admin_url + 'admin.php?page=wpad_comment_form_list';

 	if( bulk_action_option == 'trash' ){
 		wpad_bulk_action( url , "bulk_action", bulk_action_option , 'ids' , commene_ids , 'bulk_trash' );	
 	} else if( bulk_action_option == 'restore' ) {
 		wpad_bulk_action( url , "bulk_action", bulk_action_option , 'ids' , commene_ids , 'bulk_restore' );
 	} else{
 		wpad_bulk_action( url , "bulk_action", bulk_action_option , 'ids' , commene_ids , 'bulk_delete_permanent' );
 	}

 });

/*******************************
JS for Help Page
*******************************/

jQuery(document).on( 'click' , '.accordion_container #wpad_add_forms a' , function(){
 	jQuery(this).next('.content').slideToggle();
});

/*******************************
JS for Edit Comment Page Backend
*******************************/

jQuery(document).ready(function(){
 	window.editData = new FormData();
});

function edit_text_field( selected ){
 	var value = selected.find('input[type="text"]').val();
 	var key = selected.find('input[type="text"]').attr('name').split(':');
 	editData.append( key[1] , value );
}

function edit_textarea_field( selected ){
 	var value = selected.find('textarea').val();
 	var key = selected.find('textarea').attr('name').split(':');
 	editData.append( key[1] , value );
}

 function edit_radio_field( selected ){
 	var value = selected.find('input[type="radio"]:checked').val();
 	var key = selected.find('input[type="radio"]').attr('name').split(':');
 	editData.append( key[1] , value );
 }
 function edit_checkbox_field( selected ){
 	var check = [];
 	selected.find('input[type="checkbox"]').each(function(i){
 		if( jQuery(this).is(':checked') ){
 			check[i] = jQuery(this).val();
 		}
 	});
 	var value = check.join(',');
 	var key = selected.find('input[type="checkbox"]').attr('name').split(':');
 	editData.append( key[1] , value );
 }
 function edit_select_field( selected ){
 	var value = selected.find('select').val();
 	var key = selected.find('select').attr('name').split(':');
 	editData.append( key[1] , value );
 }	
 function edit_email_field( selected ){
 	var value = selected.find('input[type="text"]').val();
 	var key = selected.find('input[type="text"]').attr('name').split(':');
 	editData.append( key[1] , value );
 }
 function edit_url_field( selected ){
 	var value = selected.find('input[type="text"]').val();
 	var key = selected.find('input[type="text"]').attr('name').split(':');
 	editData.append( key[1] , value );
 }
 function edit_multiselect_field( selected ){
 	var value = selected.find('select').val();
 	var key = selected.find('select').attr('name').split(':');
 	editData.append( key[1] , value );
 }
 jQuery(document).on( 'click' , '#wpad_save_comment_edit' , function(){
 	jQuery('.wpad_editcomment tr').each(function(){
 		var selected = jQuery(this);
 		var class_name = jQuery(this).attr('class');
 		editData.append( "action" , "wpad_edit_comment_saved" );
 		switch( class_name ){
 			case 'text':
 				edit_text_field( selected );
 				break;
 			case 'textarea':
 				edit_textarea_field( selected );
 				break;
 			case 'radio':
 				edit_radio_field( selected );
 				break;
 			case 'checkbox':
 				edit_checkbox_field( selected );
 				break;
 			case 'select':
 				edit_select_field( selected );
 				break;
 			case 'email':
 				edit_email_field( selected );
 				break;
 			case 'url':
 				edit_url_field( selected );
 				break;
 			case 'multiselect':
 				edit_multiselect_field( selected );
 				break;
 			default:
 				break;
 		}
 	});
 	editData.append( "comment" , jQuery('#comment_textarea').val() );
 	editData.append( 'comment_id' , jQuery('[name="edit_commentid"]').val() );
 	editData.append( 'comment_status' , jQuery('[name="comment_status"]:checked').val() );
 	jQuery.ajax({
 		url : translate.admin_ajax,
 		type : 'POST',
 		processData: false, 
 		contentType: false,
 		data : editData,
 		beforeSend : function(){
 			jQuery('.wpad_submit_reply_loader').show();
 		},
 		success : function(){
 			window.location.href = window.location.href + '&status=updated';
 		}
 	});
 });
 /*******************************
JS for Reply Form Page Backend
*******************************/
 jQuery(document).on( 'click' , '#wpad_save_reply' , function(){
 	jQuery.ajax({
 		url : translate.admin_ajax,
 		type : 'POST',
 		data : {
 			action : 'wpad_save_reply',
 			comment_post_ID : jQuery('[name=comment_post_ID]').val(),
 			comment_author : jQuery('[name=comment_author]').val(),
 			comment_author_email : jQuery('[name=comment_author_email]').val(),
 			comment_author_url : jQuery('[name=comment_author_url]').val(),
 			comment_content : jQuery('#reply_comment_textarea').val(),
 			comment_parent : jQuery('[name=comment_parent]').val(),
 			user_id : jQuery('[name=user_id]').val(),
 			comment_author_IP : jQuery('[name=comment_author_IP]').val(),
 			comment_agent : jQuery('[name=comment_agent]').val(),
 			comment_date : jQuery('[name=comment_date]').val(),
 			comment_approved : jQuery('[name=comment_status]:checked').val()
 		},
 		beforeSend : function(){
 			jQuery('.wpad_submit_reply_loader').show();
 		},
 		success : function(){
 			window.location.href = window.location.href + '&status=updated';
 		}
 	});
 });
 /*******************************
JS for Edit Form Page Backend
*******************************/
 jQuery(document).on( 'click' , '#wpuf-metabox-editor .nav-tab-wrapper a' , function(){
 	var selected_id = jQuery(this).attr('for');
 	jQuery('#wpuf-metabox-editor .nav-tab-wrapper a').each(function(){
 		var ids = jQuery(this).attr('for');
 		jQuery(ids).hide();
 		jQuery(this).removeClass('nav-tab-active');
 	});
 	jQuery(selected_id).show();
 	jQuery(this).addClass('nav-tab-active');
 });
 jQuery(document).on('click','.edit-comment-status', function(){
 	jQuery('.comment_status_wrapper').show();
 });
 jQuery(document).on( 'click' , '.cancel-comment-status' ,function(){
 	jQuery('.comment_status_wrapper').hide();
 });
 jQuery(document).on( 'click' , '.save-comment-status' , function(){
 	var selected = jQuery('#comment_status').val();
 	jQuery('#comment-status-display').text(selected);
 	jQuery('.comment_status_wrapper').hide();
 });
 jQuery(document).on( 'click' , '.wpad-clone-field' , function(){
 	//var selected = jQuery(this).closest('.radio_option_wrapper').clone();
 	var object = '<div class="radio_option_wrapper2">' +
 		'<input type="text" value="" name="label">' +
 		'<input type="text" value="" name="value">' +
 		'<img src="' + translate.plugin_dir_path + 'images/add.png" class="wpad-clone-field" title="add another choice" alt="add another choice" style="cursor:pointer; margin:0 3px;">' + 
 		'<img src="' + translate.plugin_dir_path + 'images/remove.png" title="remove this choice" alt="remove this choice" class="wpad-remove-field" style="cursor:pointer;">' +
 	'</div>';
 	jQuery( object ).insertAfter( jQuery(this).closest('div') );
 });
 jQuery(document).on( 'click' , '.wpad-remove-field' , function(){
 	var selected = jQuery(this).closest( '.wpad_radio_option' );
 	var count_div = 0; 
 	selected.find( '.wpuf-clone-field div' ).each(function(){
 		count_div++;
 	});
 	if( count_div > 1 ){
 		jQuery(this).closest('div').remove();	
 	}
 });

 jQuery(document).on('click','.wpad_toggle_all',function(){
 	jQuery('#wpuf-form-editor li').each(function(){
 		jQuery(this).find('.wpuf-form-holder').slideToggle();
 	});
 });

 function stripHtmltags( string ){
 	if( string == undefined ){
 		return;
 	}
 	var new_string = string.replace(/<\/?[^>]+(>|$)/g, "");
	var replace_slash = new_string.replace(/\\/g, '');
	return replace_slash;
 }

 function custom_field_email_required( selected , i ){
 	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	datas[i]['meta_key'] = 'user_email';
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['placeholder_text'] = stripHtmltags( selected.find('.wpad_placeholder input').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['size'] = stripHtmltags( selected.find('.wpad_size input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_user_email]').val() );	
 }

 function custom_field_user_name( selected , i ){
 	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	datas[i]['meta_key'] = 'user_name';
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['placeholder_text'] = stripHtmltags( selected.find('.wpad_placeholder input').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['size'] = stripHtmltags( selected.find('.wpad_size input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_user_name]').val() );	
 }

 function custom_field_text( selected , i ){
 	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	var meta_key = stripHtmltags( selected.find('.wpad_meta_key input').val() );
	datas[i]['meta_key'] = meta_key.replace(/\s+/g, '_').toLowerCase();
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['placeholder_text'] = stripHtmltags( selected.find('.wpad_placeholder input').val() );
	datas[i]['default_value'] = stripHtmltags( selected.find('.wpad_default_value input').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['size'] = stripHtmltags( selected.find('.wpad_size input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_text]').val() );
	datas[i]['show_admin'] = stripHtmltags( selected.find('.wpad_show_only_admin input:checked').val() );
 }

function custom_field_user_image( selected , i ){
 	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	var meta_key = stripHtmltags( selected.find('.wpad_meta_key input').val() );
	datas[i]['meta_key'] = meta_key.replace(/\s+/g, '_').toLowerCase();
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_user_image]').val() );
	datas[i]['show_to'] = stripHtmltags( selected.find('.wpad_show_to input:checked').val() );
	datas[i]['preview'] = stripHtmltags( selected.find('.wpad_preview_image input:checked').val() );
 }

 function comment_area( selected , i ){
 	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['placeholder_text'] = stripHtmltags( selected.find('.wpad_placeholder input').val() );
	datas[i]['default_value'] = stripHtmltags( selected.find('.wpad_default_value input').val() );
	datas[i]['editor'] = 'textarea';
	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['rows'] = stripHtmltags( selected.find('.wpad_rows input').val() );
	datas[i]['column'] = stripHtmltags( selected.find('.wpad_columns input').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_comment]').val() ); 
	datas[i]['meta_key'] = 'wpad_comment';
 }

 function custom_field_textarea( selected , i ){
 	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	var meta_key = stripHtmltags( selected.find('.wpad_meta_key input').val() );
	datas[i]['meta_key'] = meta_key.replace(/\s+/g, '_').toLowerCase();
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['placeholder_text'] = stripHtmltags( selected.find('.wpad_placeholder input').val() );
	datas[i]['default_value'] = stripHtmltags( selected.find('.wpad_default_value input').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['rows'] = stripHtmltags( selected.find('.wpad_rows input').val() );
	datas[i]['column'] = stripHtmltags( selected.find('.wpad_columns input').val() );
	datas[i]['editor'] = 'textarea';
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_textarea]').val() );
	datas[i]['show_admin'] = stripHtmltags( selected.find('.wpad_show_only_admin input:checked').val() );
 }

 function custom_field_radio( selected , i ){
 	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	var meta_key = stripHtmltags( selected.find('.wpad_meta_key input').val() );
	datas[i]['meta_key'] = meta_key.replace(/\s+/g, '_').toLowerCase();
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_radio]').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['show_admin'] = stripHtmltags( selected.find('.wpad_show_only_admin input:checked').val() );
	datas[i]['options'] = {};
 	/*
	** Save the label and option value
	*/
 	selected.find('.wpad_radio_option .wpuf-clone-field div').each(function(j){
 		datas[i]['options'][j] = {};
 		var label = jQuery(this).find('input[name=label]').val();
		var option = jQuery(this).find('input[name=value]').val();
 		// One condition needs to be true
		if( label != '' || option != '' ){
 			if( label != '' ){
				datas[i]['options'][j]['label'] = stripHtmltags( label );	
 				// If option is null then it will take the label value
				if( option == '' ){
					datas[i]['options'][j]['option'] = stripHtmltags( label );	
				}
 			}
			
			if( option != '' ){
				
				datas[i]['options'][j]['option'] = stripHtmltags( option );	
 				// If label is null then it will take the option value
				if( label == '' ){
					datas[i]['options'][j]['label'] = stripHtmltags( option );
				}
 			}		
 		}
 	});
 }

 function custom_field_checkbox( selected , i ){
 	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	var meta_key = stripHtmltags( selected.find('.wpad_meta_key input').val() );
	datas[i]['meta_key'] = meta_key.replace(/\s+/g, '_').toLowerCase();
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_checkbox]').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['show_admin'] = stripHtmltags( selected.find('.wpad_show_only_admin input:checked').val() );
	datas[i]['options'] = {};
 	/*
	** Save the label and option value
	*/
 	selected.find('.wpad_radio_option .wpuf-clone-field div').each(function(j){
 		datas[i]['options'][j] = {};
		var label = jQuery(this).find('input[name=label]').val();
		var option = jQuery(this).find('input[name=value]').val();
 		// One condition needs to be true
		if( label != '' || option != '' ){
 			if( label != '' ){
				datas[i]['options'][j]['label'] = stripHtmltags( label );	
 				// If option is null then it will take the label value
				if( option == '' ){
					datas[i]['options'][j]['option'] = stripHtmltags( label );	
				}
 			}
			
			if( option != '' ){
				
				datas[i]['options'][j]['option'] = stripHtmltags( option );	
 				// If label is null then it will take the option value
				if( label == '' ){
					datas[i]['options'][j]['label'] = stripHtmltags( option );
				}
 			}		
 		}
 	});
 }

 function custom_field_select( selected , i ){
 	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	var meta_key = stripHtmltags( selected.find('.wpad_meta_key input').val() );
	datas[i]['meta_key'] = meta_key.replace(/\s+/g, '_').toLowerCase();
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_select]').val() );
	datas[i]['select_first_option'] = stripHtmltags( selected.find('.wpad_select_text input').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['show_admin'] = stripHtmltags( selected.find('.wpad_show_only_admin input:checked').val() );
 	datas[i]['options'] = {};
 	/*
	** Save the label and option value
	*/
 	selected.find('.wpad_radio_option .wpuf-clone-field div').each(function(j){
 		datas[i]['options'][j] = {};
 		var label = jQuery(this).find('input[name=label]').val();
 		datas[i]['options'][j]['label'] = stripHtmltags( label );
 		var option = jQuery(this).find('input[name=value]').val();
 		datas[i]['options'][j]['option'] = stripHtmltags( option );
 	});
 }

 function custom_field_email( selected , i ){
 	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	var meta_key = stripHtmltags( selected.find('.wpad_meta_key input').val() );
	datas[i]['meta_key'] = meta_key.replace(/\s+/g, '_').toLowerCase();
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] =stripHtmltags(  selected.find('.wpad_css_class_name input').val() );
	datas[i]['placeholder_text'] = stripHtmltags( selected.find('.wpad_placeholder input').val() );
	datas[i]['default_value'] = stripHtmltags( selected.find('.wpad_default_value input').val() );
	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['size'] = stripHtmltags( selected.find('.wpad_size input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_email]').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['show_admin'] = stripHtmltags( selected.find('.wpad_show_only_admin input:checked').val() );
 }

 function custom_field_url( selected , i ){
 	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	var meta_key = stripHtmltags( selected.find('.wpad_meta_key input').val() );
	datas[i]['meta_key'] = meta_key.replace(/\s+/g, '_').toLowerCase();
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['placeholder_text'] = stripHtmltags( selected.find('.wpad_placeholder input').val() );
	datas[i]['default_value'] = stripHtmltags( selected.find('.wpad_default_value input').val() );
	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['size'] = stripHtmltags( selected.find('.wpad_size input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_url]').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['show_admin'] = stripHtmltags( selected.find('.wpad_show_only_admin input:checked').val() );
 }

 function custom_field_multi_select( selected , i ){
 	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['required'] = stripHtmltags( selected.find('.required-field [type=radio]:checked').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	var meta_key = stripHtmltags( selected.find('.wpad_meta_key input').val() );
	datas[i]['meta_key'] = meta_key.replace(/\s+/g, '_').toLowerCase();
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
	datas[i]['css_name'] = stripHtmltags( selected.find('.wpad_css_class_name input').val() );
	datas[i]['input_name'] = stripHtmltags( selected.find('[name=input_multi_select]').val() );
	datas[i]['select_first_option'] = stripHtmltags( selected.find('.wpad_select_text input').val() );
	datas[i]['error_message'] = stripHtmltags( selected.find('.wpad_error_message input').val() );
	datas[i]['show_admin'] = stripHtmltags( selected.find('.wpad_show_only_admin input:checked').val() );
	datas[i]['options'] = {};
 	/*
	** Save the label and option value
	*/
 	selected.find('.wpad_radio_option .wpuf-clone-field div').each(function(j){
 		datas[i]['options'][j] = {};
		var label = jQuery(this).find('input[name=label]').val();
		datas[i]['options'][j]['label'] = stripHtmltags( label );
		var option = jQuery(this).find('input[name=value]').val();
		datas[i]['options'][j]['option'] = stripHtmltags( option );
 	});
 }

 function custom_field_section_break( selected , i ){
 	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	datas[i]['help_text'] = stripHtmltags( selected.find('.wpad_help_text textarea').val() );
 }

 function custom_field_html( selected , i ){
 	datas[i]['custom_field'] = stripHtmltags( selected.find('.wpad_custom_field input').val() );
	datas[i]['label'] = stripHtmltags( selected.find('.wpad_label input').val() );
	datas[i]['help_text'] = selected.find('.wpad_help_text textarea').val();
 }

 function advance_validation( selected , i , custom_field ){
 	if ( typeof validation_text == 'function' ) {
 		validation_text( selected , i , custom_field );
 	} 
 }

 function save_post_ids(){
 	var all_post = {};
	var post_ids = {};
	all_post['all_posts'] = {};
	post_ids['post_ids'] = {};
 	jQuery('.wpad_pages_posts ul').each(function(i){
		
		if( jQuery(this).find('.select_all_li label input').is(':checked') ){
			all_post['all_posts'][i] = jQuery(this).find('label input').val();
		} else {
 			jQuery(this).find('li').each(function(){
 				if( jQuery(this).find('label input').is(':checked') ){
					var id = jQuery(this).find('label input').val();
					post_ids['post_ids'][id] = translate.comment_id;
				}
 			});
			
		}
 	});
 	return [all_post,post_ids];
 }

 function save_extras_parameters(){
 	var check = jQuery('[name=replace_comment_form]:checked').val();
	var comment_form_id = translate.comment_id;
	datas['extras'] = {};
 	switch (check){
 		case 'none':
			datas['extras']['none'] = '';
			break;
 		case 'show_on_all':
			datas['extras']['show_on_all'] = comment_form_id;
			break;
		
		case 'enable_certain_pages':
			datas['extras']['enable_certain_pages'] = save_post_ids();
			break;
 		default:
			break;
 	}
 }

 jQuery(document).on( 'click' , '[name=save_comment]' , function(){
 	window.datas = {};
 	jQuery( '#wpuf-form-editor li' ).each(function( i ){
 		datas[i] = {};
		var custom_field = jQuery(this).find('.wpad_custom_field input').val();
 		switch( custom_field ){
 			case 'user_name':
				custom_field_user_name( jQuery(this) , i );
				advance_validation( jQuery(this) , i , 'user_name' );
				break;
 			case 'text':
				custom_field_text( jQuery(this) , i );
				advance_validation( jQuery(this) , i , 'text' );
				break;
 			case 'comment_area':
				comment_area( jQuery(this) , i );
				advance_validation( jQuery(this) , i , 'comment_area' );
				break;
 			case 'user_email':
				custom_field_email_required( jQuery(this) , i );
				advance_validation( jQuery(this) , i , 'user_email' );
				break;
 			case 'textarea':
				custom_field_textarea( jQuery(this) , i );
				advance_validation( jQuery(this) , i , 'textarea' );
				break;
 			case 'radio':
				custom_field_radio( jQuery(this) , i );
				break;
 			case 'checkbox':
				custom_field_checkbox( jQuery(this) , i );
				advance_validation( jQuery(this) , i , 'checkbox' );
				break;
 			case 'select':
				custom_field_select( jQuery(this) , i );
				break;
 			case 'email':
				custom_field_email( jQuery(this) , i );
				break;
 			case 'url':
				custom_field_url( jQuery(this) , i );
				break;
 			case 'multi_select':
				custom_field_multi_select( jQuery(this) , i );
				break;
 			case 'section_break':
				custom_field_section_break( jQuery(this) , i );
				break;
 			case 'html':
				custom_field_html( jQuery(this) , i );
				break;
			case 'user_image':
				custom_field_user_image( jQuery(this) , i );
				break;
 			default:
				break;
 		}		
 	});
 	datas['other'] = {};
 	x = jQuery('[name=comment_title]').val();
 	x = x.replace(/\\/g, "");
 	datas['other']['comment_title'] = stripHtmltags( x );
 	var guest_comment = ( jQuery('[name=guest_comment]:checked').val() == undefined ) ? 'enable' : 'disable';
 	
 	// Comment Form settings
 	datas['other']['display_roles'] = stripHtmltags( jQuery('[name=display_roles]:checked').val() );
 	datas['other']['comment_automatically_approve'] = stripHtmltags( jQuery('[name=comment_automatically_approve]:checked').val() );
	datas['other']['comment_status'] = stripHtmltags( jQuery('[name=comment_status]').val() );
	datas['other']['comment_listing'] = stripHtmltags( jQuery('[name=comment_listing]:checked').val() );
	datas['other']['comment_order_by'] = stripHtmltags( jQuery('[name=comment_order_by]:checked').val() );
	datas['other']['guest_comment'] = stripHtmltags( guest_comment );
	datas['other']['user_name_show'] = stripHtmltags( jQuery('[name=user_name_show]:checked').val() );
	datas['other']['user_email_show'] = stripHtmltags( jQuery('[name=user_email_show]:checked').val() );
	datas['other']['comment_time'] = stripHtmltags( jQuery('[name=comment_time]:checked').val() );
	datas['other']['comment_position'] = stripHtmltags( jQuery('[name=comment_position]:checked').val() );
	datas['other']['submit_label'] = stripHtmltags( jQuery('[name=submit_label]').val() );
	datas['other']['no_of_column'] = stripHtmltags( jQuery('[name=no_of_column]:checked').val() );
	
	// Notification	to admin
	datas['other']['notification'] = stripHtmltags( jQuery('[name=enable_notification]:checked').val() );
	datas['other']['notification_to'] = stripHtmltags( jQuery('[name=notification_to]').val() );
	datas['other']['notification_subject'] = stripHtmltags( jQuery('[name=notification_subject]').val() );
	datas['other']['notification_message'] = jQuery('[name=notification_message]').val();	

	// Notification to users
	datas['other']['disable_approve_notification'] = stripHtmltags( jQuery('[name=disable_approve_notification]:checked').val() );
	datas['other']['mail_from_name'] = stripHtmltags( jQuery('[name=mail_from_name]').val() );
	datas['other']['mail_from_email'] = stripHtmltags( jQuery('[name=mail_from_email]').val() );
	datas['other']['mail_approved_subject'] = stripHtmltags( jQuery('[name=mail_approved_subject]').val() );
	datas['other']['confirm_notification_message'] = jQuery('[name=confirm_notification_message]').val();

	// Pagination
	datas['other']['pagination'] = stripHtmltags( jQuery('[name=pagination]:checked').val() );
	datas['other']['pagination_per_page'] = stripHtmltags( jQuery('[name=pagination_per_page]').val() );
	datas['other']['pagination_position'] = stripHtmltags( jQuery('[name=pagination_position]:checked').val() );
	datas['other']['text_for_first_page'] = stripHtmltags( jQuery('[name=text_for_first_page]').val() );
	datas['other']['text_for_last_page'] = stripHtmltags( jQuery('[name=text_for_last_page]').val() );
	datas['other']['text_for_previous_page'] = stripHtmltags( jQuery('[name=text_for_previous_page]').val() );
	datas['other']['text_for_next_page'] = stripHtmltags( jQuery('[name=text_for_next_page]').val() );
	
	// Like / Dislike Button
	datas['other']['like_dislike_btn'] = stripHtmltags( jQuery('[name=like_dislike_btn]:checked').val() );
	datas['other']['choose_button'] = stripHtmltags( jQuery('[name=choose_button]').val() );
	datas['other']['button_position'] = stripHtmltags( jQuery('[name=button_position]').val() );
	
	// Custom layout
	datas['other']['custom_layout'] = jQuery('[name=custom_layout]').val();
	
 	/*
	** save extras parameters
	*/
 	save_extras_parameters();
	
	var element = datas;
 	//console.log( element );
 	function dialog_box( selector , height ){
 		height = ( height != null ? height : 110 );
 		jQuery( selector ).dialog({
			modal: true,
			resizable: false,
			width : 450,
			height : height,
			draggable: false,
			open: function(event, ui) {
			  	jQuery("body").css({ overflow: 'hidden' }) // Disable scrolling on open dialog
			},
			close: function(event, ui) {
			  	jQuery("body").css({ overflow: 'inherit' }) // Enable scrolling on close dialog
			}
		});
	}
 	// **************************************
	// Display Errors for the Empty Meta Keys
	// ************************************** 
 	for( var i = 0 ; i < ( Object.keys(element).length - 2 ) ; i++ ){
 		if( element[i]['meta_key'] == '' ){
 			dialog_box( "#wpad_dialog" , null );
 			return;
 		}
 	}
 	// ***********************************
	// Display Errors for the Empty Labels
	// *********************************** 
 	for( var i = 0 ; i < ( Object.keys(element).length - 2 ) ; i++ ){
 		if( element[i]['label'] == '' ){
 			dialog_box( "#wpad_dialog_label" , null );
 			return;
 		}
 	}
  	// ******************************************
	// Display Errors for the Duplicate Meta Keys
	// ****************************************** 
 	function multiple_same_key( value ){
 		jQuery('#wpad_dialog_same_keys .multiple_keys').text(value);
 		dialog_box( "#wpad_dialog_same_keys" , 200 );
 	}
 	function checkIfArrayIsUnique(arr) {
 	    var map = {}, i, size;
 	    for (i = 0, size = arr.length; i < size; i++){
 	        if (map[arr[i]]){
 	        	multiple_same_key( arr[i] );
 	            return false;
 	        }
 	        map[arr[i]] = true;
 	    }
 	    return true;
 	}
 	var all_meta_keys = [];
 	for( var i = 0 ; i < ( Object.keys(element).length - 2 ) ; i++ ){
 		if( element[i]['meta_key'] != undefined ){
			all_meta_keys[i] = element[i]['meta_key'];	
		}		
 	}
 	reset_meta_keys = all_meta_keys.filter(function(){return true;});
 	if( checkIfArrayIsUnique(reset_meta_keys) == false ){
 		return;
 	}
 	jQuery.ajax({
 		url : translate.admin_ajax,
 		type : 'post',
 		data : {
 			action : 'save_comment_form',
 			form : element,
 			id : translate.comment_id
 		},
 		beforeSend : function(){
 			jQuery('#publishing-action img').show();
 		},
 		success : function(){
 			var location = window.location.href + '&status=updated';
 			window.location = location;
 		}
 	});
 });
 function hide_show_empty_alert(){
 	if( jQuery('.wpuf-form-editor li').length ){
 		jQuery('.wpuf-updated').hide();
 	} else {
 		jQuery('.wpuf-updated').show();
 	}
 }

function check_user_image(){

	var exist = true;
	jQuery('.wpad_custom_field').each(function(){

		var name = jQuery(this).find('input').val();

		if( name == 'user_image' ){
			exist = false;
		} 

	});

	if( exist == false ){
		return false;
	}

}

 jQuery(document).on( 'click' , '.comment_form_buttons button' , function(){

 	var element = jQuery(this).val();
 	var selected_item = jQuery(this);
 	var loader = '<img src="' + translate.plugin_dir_path + '/images/small_loader.gif' + '">';

 	// User image field cannot be multiple
 	if( element == 'user_image' && check_user_image() == false ){
 		alert( 'You cannot add more than one User Image field.' );
 		return;
 	}

 	jQuery.ajax({
 		url : translate.admin_ajax,
 		type : 'post',
 		dataType : 'json',
 		data : {
 			action : 'wpad_comment_element',
 			type : element
 		},
 		beforeSend : function(){
 			selected_item.append(loader);
 		},
 		success : function( result ){
 			selected_item.find('img').remove();
 			jQuery('#wpuf-form-editor').append( result.content );
 			hide_show_empty_alert();
 		}
 	});
 });
 // Hide / Show the element
 jQuery(document).on( 'click' , '.wpuf-actions .wpuf-toggle' , function(){
 	jQuery(this).closest('.wpuf-actions').parent().next('div').slideToggle('fast');
 });
 // Remove Comment Element
 jQuery(document).on('click','.wpuf-remove',function(){
 	x = confirm("Are you sure you want do delete the element ?");
 	if (x == true) {
         jQuery(this).closest('li').remove();
         hide_show_empty_alert();
     } 
 });
 jQuery( document ).ready(function(){
 	/*
	** Sortable Tabs
	*/
 	jQuery( ".sortable" ).sortable({
       	placeholder: "ui-state-highlight",
       	handle: ".wpuf-legend"
     });
 });
 /*******************************
JS for comment list page backend
*******************************/
 jQuery( document ).on( 'click' , '[name=filter_action]' , function(){
 	var comment_user = jQuery('[name=comment_user]').val();
 	var comment_post = jQuery('[name=comment_post]').val();
 	var comment_order_by = jQuery('[name=comment_order_by]').val();
 	var comment_order = jQuery('[name=comment_order]').val();
 	jQuery('#comment_user_hide').attr( 'value' , comment_user );
 	jQuery('#comment_post_hide').attr( 'value' , comment_post );
 	jQuery('#comment_order_by_hide').attr( 'value' , comment_order_by );
 	jQuery('#comment_order_hide').attr( 'value' , comment_order );
 	var status = jQuery('.status').attr( 'id' );
 	get_comments( status , page_no = null );
 });
 function delete_comment( selected ){
 	var comment_id = selected.attr('comment_id');
 	jQuery.ajax({
 		url : translate.admin_ajax,
 		type : 'post',
 		dataType : 'json',
 		data : {
 			action : 'delete_comment',
 			comment_id : comment_id
 		},
 		beforeSend : function(){
 			selected.closest('tr').remove();
 		},
 		success : function( result ){
 			update_count( result );
 		}
 	});
 }
 function before_update_count( status , selected ){
 	if( status == 'hold' ){
 		selected.closest('tr').addClass('unapproved');
 	} else if( status == 'approve' ){
 		selected.closest('tr').removeClass('unapproved');
 	} else if( status == 'spam' ){
 		selected.closest('tr').remove();
 	}
 }
 function update_comment_status( status , selected , action ){
 	action = action || null;
 	var comment_id = selected.attr('comment_id');
 	jQuery.ajax({
 		url : translate.admin_ajax,
 		type : 'post',
 		dataType : 'json',
 		data : {
 			action : 'update_comment_status',
 			comment_status : status,
 			comment_id : comment_id,
 		},
 		beforeSend : function(){
 			if( action == 'remove' ){
 				selected.closest('tr').remove();
 			} else {
 				before_update_count( status , selected );
 			}
 		},
 		success : function( result ){
 			update_count( result );
		}
 	});	
 }
 function default_status(){
 	jQuery('.subsubsub a').each(function(){
 		jQuery(this).removeClass('current');
 	});
 	jQuery('.subsubsub .all a').addClass('current');
 	jQuery('.status').attr( 'id' , 'all' );
 }
 jQuery(document).on( 'click' , '.wpad_showing_results button' , function(){
 	var value = jQuery(this).val();
 	var status = '';
 	var comment_user = '';
 	var comment_post = '';
 	var comment_order_by = '';
 	var comment_order = '';
 	if( value == 'status' ){
 		get_comments( 'all' , null );
 		default_status();
 	} else if( value == 'user' ){
 		status = jQuery('.status').attr('id');
 		comment_post = jQuery('#comment_post_hide').val();
 		comment_order_by = jQuery('#comment_order_by_hide').val();
 		comment_order = jQuery('#comment_order_hide').val();
 		get_comments( status , null , 'all' , comment_post , comment_order_by , comment_order );
 		jQuery('select[name=comment_user]').val('');
 		jQuery('#comment_user_hide').val('');
 	} else if( value == 'post' ){
 		status = jQuery('.status').attr('id');
 		comment_user = jQuery('#comment_user_hide').val();
 		comment_order_by = jQuery('#comment_order_by_hide').val();
 		comment_order = jQuery('#comment_order_hide').val();
 		get_comments( status , null , comment_user , 'all' , comment_order_by , comment_order );
 		jQuery('select[name=comment_post]').val('');
 		jQuery('#comment_post_hide').val('');
 	} else if( value == 'order_by' ) {
 		status = jQuery('.status').attr('id');
 		comment_post = jQuery('#comment_post_hide').val();
 		comment_user = jQuery('#comment_user_hide').val();
 		comment_order = jQuery('#comment_order_hide').val();
 		get_comments( status , null , comment_user , comment_post , 'all' , comment_order );
 		jQuery('select[name=comment_order_by]').val('');
 		jQuery('#comment_order_by_hide').val('');
 	} else if( value == 'order' ) {
 		status = jQuery('.status').attr('id');
 		comment_post = jQuery('#comment_post_hide').val();
 		comment_user = jQuery('#comment_user_hide').val();
 		comment_order_by = jQuery('#comment_order_by_hide').val();
 		get_comments( status , null , comment_user , comment_post , comment_order_by , 'all' );
 		jQuery('select[name=comment_order]').val('');
 		jQuery('#comment_order_hide').val('');
 	}
 });
 function get_comments( status , page_no, user , post, order_by , order ){
 	page_no = page_no || null;
 	user = user || null;
 	post = post || null;
 	order_by = order_by || null;
 	order = order || null;
 	if( page_no == null ){
 		page_no = 1;
 	}
 	jQuery.ajax({
 		url : translate.admin_ajax,
 		type : 'post',
 		dataType : 'json',
 		data : {
 			action : 'get_wpad_comments',
 			comment_status : status,
 			page : page_no,
 			comment_user : ( user != null ) ? user : jQuery('#comment_user_hide').val(),
 			comment_post : ( post != null ) ? post : jQuery('#comment_post_hide').val(),
 			comment_order_by : ( order_by != null ) ? order_by : jQuery('#comment_order_by_hide').val(),
 			comment_order : ( order != null ) ? order : jQuery('#comment_order_hide').val(),
 		},
 		beforeSend : function(){
 			jQuery('#the-comment-list').empty();
 			jQuery('#the-comment-list').append('<tr style="background:#fff">' +
 				'<td colspan="4" style="text-align:center" class="loader_td" >' +
 				'<img src="' + translate.plugin_dir_path + '/images/loader.gif">' +
 				'</td></tr>' );
 		},
 		success : function( result ){
 			jQuery('.loader_td').hide();
 			jQuery('#the-comment-list').append( result.content );
 			jQuery('.pagination ul').empty();
 			jQuery('.pagination ul').append( result.pagination );
 			jQuery('.wpad_showing_results').empty();
 			jQuery('.wpad_showing_results').append( result.showing_results );
 			update_count( result );
 			show_hide_bulk_action( status );
 		}
 	});
 }
 function show_hide_bulk_action( status ){
 	if ( status == 'hold' ){
 		jQuery('#bulk-action-selector-top option').show();
		jQuery('#bulk-action-selector-top option[value="unapprove"]').hide();
		jQuery('#bulk-action-selector-top option[value="not_spam"]').hide();
		jQuery('#bulk-action-selector-top option[value="restore"]').hide();
		jQuery('#bulk-action-selector-top option[value="delete_permanently"]').hide();
 	} else if ( status == 'approve' ){
 		jQuery('#bulk-action-selector-top option').show();
		jQuery('#bulk-action-selector-top option[value="approve"]').hide();
		jQuery('#bulk-action-selector-top option[value="not_spam"]').hide();
		jQuery('#bulk-action-selector-top option[value="restore"]').hide();
		jQuery('#bulk-action-selector-top option[value="delete_permanently"]').hide();
 	} else if ( status == 'spam' ){
 		jQuery('#bulk-action-selector-top option').show();
		jQuery('#bulk-action-selector-top option[value="unapprove"]').hide();
		jQuery('#bulk-action-selector-top option[value="approve"]').hide();
		jQuery('#bulk-action-selector-top option[value="restore"]').hide();
		jQuery('#bulk-action-selector-top option[value="spam"]').hide();
		jQuery('#bulk-action-selector-top option[value="trash"]').hide();
 	} else if( status == 'trash' ){
 		jQuery('#bulk-action-selector-top option').show();
		jQuery('#bulk-action-selector-top option[value="unapprove"]').hide();
		jQuery('#bulk-action-selector-top option[value="approve"]').hide();
		jQuery('#bulk-action-selector-top option[value="trash"]').hide();
		jQuery('#bulk-action-selector-top option[value="not_spam"]').hide();
 	}
 }
 function update_count( result ){
 	// Update Status
 	jQuery('.pending_count').text( result.status.hold );
 	jQuery('.approve_count').text( result.status.approve );
 	jQuery('.spam_count').text( result.status.spam );
 	jQuery('.trash_count').text( result.status.trash );
 }
 jQuery( document ).ready(function(){
 	get_comments( status = null );
 });
 jQuery( document ).on( 'click' , '.pagination ul li a' , function(){
 	var status = jQuery('.status').attr( 'id' );
 	var page_no = jQuery(this).attr('page_no');
 	get_comments( status , page_no );
 });
 jQuery( document ).on( 'click' , '.subsubsub a' , function(){
 	var selected = jQuery(this);
 	jQuery('.status').attr('id' , jQuery(this).attr('id') );
 	jQuery('.subsubsub a').each(function(){
 		jQuery(this).removeClass('current');
 	});
 	selected.addClass('current');
 });
 jQuery( document ).on( 'click' , '#bulk_action' , function(){
 	var i = 0;
 	var ids = [];
 	jQuery('[name=check_ids]').each(function(){
 		if( jQuery(this).is(':checked') ){
 			ids[i++] = jQuery(this).val();
 		}
 	});
 	if( ids.length == '' ){
 		alert('Please choose some comments');
 		return;
 	}
 	jQuery.ajax({
 		url : translate.admin_ajax,
 		type : 'post',
 		data : {
 			action : 'bulk_action',
 			comment_ids : ids,
 			do_action : jQuery('#bulk-action-selector-top').val()
 		},
 		beforeSend : function(){
 			jQuery('.bulk_loader').show();
 		},
 		success : function( result ){
 			location.reload();
 		}
 	});
 });

jQuery( document ).on( 'change' , '[name="report_all_post"]' ,function(){

	if( jQuery(this).val() != '' ){

		var post_id = jQuery(this).val();
		var link = translate.admin_url + 'admin.php?page=wpad_reported_comment&post_id=' + post_id;
		location.href = link;

	}

});

jQuery( document ).on( 'click', '#reset_flagged_reports' , function(){

	var comment_id = jQuery(this).attr( 'comment_id' );
	var selected = jQuery(this);

	jQuery.ajax({
		url : translate.admin_ajax,
		type : 'POST',
		data : {
			action : 'wpad_reset_flagged_reports',
			comment_id : comment_id
		},
		beforeSend : function(){
			selected.text('Resetting ...');
		},
		success : function(){

			console.log(selected.closest('tr').find('.wpad_count_reported a'));
			selected.closest('tr').find('.wpad_count_reported a').remove();
			selected.closest('.reset_flagged_reports').remove();
			
		}

	});

});