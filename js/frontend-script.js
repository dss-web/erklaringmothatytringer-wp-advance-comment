jQuery(document).ready(function(){


	/*
	** On document load delete the default comment
	*/

	jQuery('#wp_advance_comment_form').nextAll('div').remove();
	
	// 
	// jQuery add method for multi select
	// 

	jQuery.validator.addMethod( "multi_select", function( value, element , params ) {
	    var count = 0;

	    jQuery(element).find('option:selected').each(function(){

	    	if( jQuery(this).val() != '' ){
	    		count++;
	    	}

	    });

	    return count > 0;
	}, 'Please select atleast one element.');

	// 
	// jQuery add method for Email
	// 

	jQuery.validator.addMethod("laxEmail", function(value, element) {
		var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
	  	return this.optional( element ) || pattern.test(value);
	}, 'Please enter a valid email address.');

	var text_comment_report = ( translate.report_textarea == 'enable' ) ? true : false;

	jQuery(".wpad_report_comment_form").validate({
		
		errorElement: "div",

		errorPlacement: function(error, element) {
	 		error.appendTo( element.closest("div").find(".wpad_error").show() )
   		},

		rules: {

            'text_comment_report': {
                required: text_comment_report,
            },

            'report_option' : {
            	required: true,
            }
            
        },
        
        submitHandler: function (form) {

        	if( jQuery('[name="report_option"]:checked').val() == undefined ){
        		return;
        	}

        	jQuery.ajax({
        		url : translate.admin_ajax,
				type : 'POST',
				data : {
					action : 'wpad_save_comment_report',
					message : jQuery('[name="text_comment_report"]').val(),
					option : jQuery('[name="report_option"]:checked').val(),
					comment_id : report_comment_id
				},
				beforeSend : function(){
					jQuery('.wpad_report_button img').show();
				}, 
				success : function(){
					jQuery('.wpad_report_comment_form')[0].reset();
					jQuery('.wpad_report_button img').hide();
					jQuery("#wpad_report_comment").dialog("close");
					remove_btn_report();
					dialog_box( '#wpad_thank_you_report_comment' , null );
				}
        	});

        }

	});
	
});

function remove_btn_report(){

	jQuery('.wpad_comment_report').each(function(){

		if( jQuery(this).attr('comment_id') == report_comment_id ){

			jQuery(this).remove();

		}

	});

}

jQuery(document).on( 'click' , '.pagination_wpad li a' , function(){

	var page_no = jQuery(this).attr('page_no');
	jQuery.ajax({
		url : translate.admin_ajax,
		type : 'POST',
		data : {
			action : 'get_pagination_content',
			post_id : jQuery('input[name="pagination_post"]').val(),
			page_no : page_no,
			layout : jQuery('[name="wpad_layout"]').text(),
			form_id : jQuery('[name="wpad_form_id_list"]').val()
		},
		dataType : 'json',
		beforeSend : function(){
			jQuery('.wpad_list_comments_frontend_wrapper').css( 'opacity' , '0.2' );
		},
		success : function( results ){
			jQuery('.wpad_list_comments_frontend_wrapper').css( 'opacity' , '1' );
			jQuery('.wpad_list_comments_frontend_wrapper').empty();
			jQuery('.wpad_list_comments_frontend_wrapper').html( results.content );
			jQuery('html, body').animate({
	            scrollTop: jQuery('.wpad_list_comments_frontend_wrapper').offset().top-50
	        }, 'fast' );
		}

	});

});

/*
** Like dislike scripts
*/

jQuery(document).on( 'click' , '.like_btn_wrap a' ,function(){

	var selected = jQuery(this);
	var comment_id = jQuery(this).attr('comment_id');
	jQuery.ajax({
		url : translate.admin_ajax,
		type : 'POST',
		dataType : 'json',
		data : {
			action : 'wpad_add_likes',
			comment_id : comment_id
		},
		beforeSend : function(){
			selected.prop( 'disabled' , true );
			var success_img = translate.plugin_url + '/images/like_success.png';
			selected.find('.wpad_like_btn img').attr( 'src' , success_img );
			selected.find('.wpad_like_text').addClass('wpad_liked');
		},
		success : function( result ){
			
			selected.prop( 'disabled' , false );
			if( result.result == 'success' ){
				selected.next('.wpad_like_counter').text(result.count_likes);	
				selected.find('.wpad_already_voted').text('Thanks for your vote').show();
				selected.find('.wpad_already_voted').delay(1000).fadeOut(500);
				selected.closest('.wpad_like_dislike_wrapper').find('.wpad_dislike_counter').text(result.count_dislikes);

				// remove Class
				selected.closest('.wpad_like_dislike_wrapper').find('.wpad_dislike_text').removeClass('wpad_liked');

				// Replace Image
				var replace_img = translate.plugin_url + '/images/dislike.png';
				selected.closest('.wpad_like_dislike_wrapper').find('.wpad_dislike_btn img').attr( 'src' , replace_img );

			} else {
				selected.find('.wpad_already_voted').text('Already voted!').show();
				selected.find('.wpad_already_voted').delay(1000).fadeOut(500);
			}
		}
	});

});

jQuery(document).on( 'click' , '.dislike_btn_wrap a' ,function(){

	var selected = jQuery(this);
	var comment_id = jQuery(this).attr('comment_id');
	jQuery.ajax({
		url : translate.admin_ajax,
		type : 'POST',
		dataType : 'json',
		data : {
			action : 'wpad_add_dislikes',
			comment_id : comment_id
		},
		beforeSend : function(){
			selected.prop( 'disabled' , true );
			var success_img = translate.plugin_url + '/images/dislike_success.png';
			selected.find('.wpad_dislike_btn img').attr( 'src' , success_img );
			selected.find('.wpad_dislike_text').addClass('wpad_liked');
		},
		success : function( result ){
			
			selected.prop( 'disabled' , false );
			if( result.result == 'success' ){
				selected.next('.wpad_dislike_counter').text(result.count_dislikes);	
				selected.find('.wpad_already_voted').text('Thanks for your vote').show();
				selected.find('.wpad_already_voted').delay(1000).fadeOut(500);
				selected.closest('.wpad_like_dislike_wrapper').find('.wpad_like_counter').text(result.count_likes);

				// remove Class
				selected.closest('.wpad_like_dislike_wrapper').find('.wpad_like_text').removeClass('wpad_liked');

				// Replace Image
				var replace_img = translate.plugin_url + '/images/like.png';
				selected.closest('.wpad_like_dislike_wrapper').find('.wpad_like_btn img').attr( 'src' , replace_img );

			} else {
				selected.find('.wpad_already_voted').text('Already voted!').show();
				selected.find('.wpad_already_voted').delay(1000).fadeOut(500);
			}
		}
	});

});

jQuery(document).on( 'change' , '.wpad_form_group input[type="file"]' , function(){

	var valid_ext = ['image/jpg','image/jpeg','image/png','image/gif','image/bmp'];

	var file = document.getElementById( jQuery(this).attr('id') );

	var file_val = file.files[0];

	var selected = jQuery(this);

	// Hide the image if the file is not selected
	if( file.value.length == 0 ){
		selected.next('.wpad_image_preview').hide().attr('src', '' );
	}

	if( valid_ext.indexOf( file_val.type ) != -1 ){

		/* Start Image Preview */
		if (file.files && file.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				selected.next('.wpad_image_preview').show().attr('src', e.target.result);
			}
			
			reader.readAsDataURL(file.files[0]);
		}

	} else {
		selected.next('.wpad_image_preview').hide().attr('src', '' );
	}

	jQuery("#wp_advance_comment_form").valid();

});

function dialog_box( selector , height ){

	jQuery( selector ).dialog({
		modal: true,
		resizable: false,
		my: "center bottom",
 		at: "center top",
		width : 500,
		autoResize:true,
		draggable: false,
		open: function(event, ui) {
			jQuery(this).closest(".ui-dialog").addClass( 'wpad_dialog' );
		  	jQuery("body").css({ overflow: 'hidden' }) // Disable scrolling on open dialog
		  	jQuery(this).closest(".ui-dialog")
        	.find(".ui-dialog-titlebar-close")
        	.removeClass("ui-dialog-titlebar-close")
        	.html("<span class='ui-button-icon-primary ui-icon ui-icon-closethick'></span>");
		},
		close: function(event, ui) {
		  	jQuery("body").css({ overflow: 'inherit' }) // Enable scrolling on close dialog
		}
	});
}

jQuery(document).on( 'click' , '.wpad_comment_report' , function(){

	window.report_comment_id = jQuery(this).attr( 'comment_id' );
	dialog_box( "#wpad_report_comment" , 400 );

});

jQuery(document).on( 'click' , '.report_dismiss' , function(){

	jQuery('.wpad_report_comment_form')[0].reset();
	jQuery("#wpad_report_comment").dialog("close");

});