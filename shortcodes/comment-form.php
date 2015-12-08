<?php 
 /*
** It displays a comment form on to the frontend
*/
 class wpad_frontend_comment_form{

 	function __construct(){

 		/*
 		** Created Shortcode 
 		*/

 		add_shortcode('wpad-comment-form', array( $this , 'show_comments' ) );

 		/*
 		** Ajax pagination on the comment list
		*/

		add_action( 'wp_ajax_get_pagination_content' , array( $this , 'show_comment_lists' ) );
		add_action( 'wp_ajax_nopriv_get_pagination_content' , array( $this , 'show_comment_lists' ) );

		/*
		** Like and dislike comment 
		*/

		add_action( 'wp_ajax_wpad_add_likes' , array( $this , 'add_likes_comment' ) );
		add_action( 'wp_ajax_nopriv_wpad_add_likes' , array( $this , 'add_likes_comment' ) );
		add_action( 'wp_ajax_wpad_add_dislikes' , array( $this , 'wpad_add_dislikes' ) );
		add_action( 'wp_ajax_nopriv_wpad_add_dislikes' , array( $this , 'wpad_add_dislikes' ) );

 	}

 	function get_the_selected_comment_form( $post_id = null ){

 		if( !empty($post_id) ){
 			$post = get_post( $post_id );
 		} else {
 			global $post;	
 		}
		
		$wpad = new wpad_remove_default_comment_tag( true );
		$comment_forms = get_option('wpad_comment_forms_on_posts');

		$id = $wpad->check_post_ids( $comment_forms , $post );

		if( $id == null ){
			$id = $wpad->check_all_post( $comment_forms , $post );
		}

		if( $id == null ){
			$id = $wpad->check_default_comment_selected( $comment_forms , $post );
		}

		if( $id == null ){
			$id = $wpad->check_comment_disable_all_posts( $comment_forms , $post );
		}

		if( $id == 'none' ){
			return 'error';
		} else {
			return $id;	
		}
		

	}
 	
 	/*
 	** When like button is clicked remove the user ip from the dislike metakey
 	*/

 	function remove_dislikes( $comment_id ){

 		$comment_dislikes = get_comment_meta( $comment_id, 'comment_dislikes', true );

		$ip = $_SERVER['REMOTE_ADDR'];

		if( !empty($comment_dislikes) ){

			foreach( $comment_dislikes as $key => $dislikes ){

				if( $ip == $dislikes ){

					unset( $comment_dislikes[$key] );

				}

			}

		}

		update_comment_meta( $comment_id, 'comment_dislikes', $comment_dislikes );

 	}

 	/*
 	** When dislike button is clicked remove the user ip from the like metakey
 	*/

 	function remove_likes( $comment_id ){

 		$comment_likes = get_comment_meta( $comment_id, 'comment_likes', true );

		$ip = $_SERVER['REMOTE_ADDR'];

		if( !empty($comment_likes) ){

			foreach( $comment_likes as $key => $likes ){

				if( $ip == $likes ){

					unset( $comment_likes[$key] );

				}

			}

		}

		update_comment_meta( $comment_id, 'comment_likes', $comment_likes );

 	}

 	/*
	** Save user ip address to the comment_dislikes meta key
 	*/

 	function wpad_add_dislikes(){

 		$comment_id = $_POST['comment_id'];
		$comment_dislikes = get_comment_meta( $comment_id, 'comment_dislikes', true );
		$ip = $_SERVER['REMOTE_ADDR'];

		if( is_array( $comment_dislikes ) && in_array( $ip , $comment_dislikes ) ){

			$data = array(
				'result' => 'error'
			);

		} else {

			$this->remove_likes( $comment_id );

			if( empty($comment_dislikes) ){
				update_comment_meta( $comment_id, 'comment_dislikes' , array($ip) ); 
			} else {
				array_push( $comment_dislikes , $ip );
				update_comment_meta( $comment_id, 'comment_dislikes' , $comment_dislikes ); 
			}
	 		
	 		$comment_dislikes = get_comment_meta( $comment_id, 'comment_dislikes', true );
	 		$comment_likes = get_comment_meta( $comment_id, 'comment_likes', true );

	 		$data = array(
				'result' => 'success',
				'count_dislikes' => empty($comment_dislikes) ? 0 : count( $comment_dislikes ),
				'count_likes' => empty($comment_likes) ? 0 : count( $comment_likes )
			);

		}

 		echo json_encode($data);
 		die;

 	}

 	/*
	** Save user ip address to the comment_likes meta key
 	*/

 	function add_likes_comment(){

 		$comment_id = $_POST['comment_id'];
		$comment_likes = get_comment_meta( $comment_id, 'comment_likes', true );
		$ip = $_SERVER['REMOTE_ADDR'];

		if( is_array( $comment_likes ) && in_array( $ip , $comment_likes ) ){

			$data = array(
				'result' => 'error'
			);

		} else {

			$this->remove_dislikes( $comment_id );

			if( empty($comment_likes) ){
				update_comment_meta( $comment_id, 'comment_likes' , array($ip) ); 
			} else {
				array_push( $comment_likes , $ip );
				update_comment_meta( $comment_id, 'comment_likes' , $comment_likes ); 
			}
	 		
	 		$comment_likes = get_comment_meta( $comment_id, 'comment_likes', true );
	 		$comment_dislikes = get_comment_meta( $comment_id, 'comment_dislikes', true );

	 		$data = array(
				'result' => 'success',
				'count_dislikes' => empty($comment_dislikes) ? 0 : count( $comment_dislikes ),
				'count_likes' => empty($comment_likes) ? 0 : count( $comment_likes )
			);

		}

 		echo json_encode($data);
 		die;

 	}

 	/*
	** Shortcode function
 	*/

 	function show_comments( $atts ){

 		/*
		** Show the comment form
		*/
 		wp_enqueue_style( 'wpad_style_front' );
 		wp_deregister_script( 'jquery-ui-dialog' );

		wp_enqueue_script( 'jquery-ui-dialog' , plugin_dir_url( dirname( __FILE__ ) ) . 'js/jquery-ui.min.js' , array() , '1.0.0' , false );

		wp_enqueue_style( 'wpad-ui-jquery' , plugin_dir_url( dirname( __FILE__ ) ) . 'css/jquery-ui.css' , array() , '1.0.0' , false );

		wp_enqueue_style( 'wpad-ui' , plugin_dir_url( dirname( __FILE__ ) ) . 'css/jquery-dialog.css' , array() , '1.0.0' , false );
		$shortcode = $this->comment_form( $atts );
		return apply_filters( 'comment_form_front' , $shortcode );

 	}

 	/*
	** Show like or dislike button on the comment list
 	*/

 	function show_like_dislike_button( $comment_id , $options , $position ){

 		if( !empty($options['like_dislike_btn']) && $options['like_dislike_btn'] == 'enable' ){
 			
 			if( !empty($options['button_position']) && $position == $options['button_position'] ){
 				
 				$button = !empty($options['choose_button']) ? $options['choose_button'] : 'both';
 				$btn = new wpad_like_dislike();
 				return $like_dislike = $btn->get_buttons( $comment_id , $button );
 				
 			} else {
 				return '';
 			}		

 		} else {
 			return '';	
 		} 		

 	}

 	/*
	** Show user name text field
 	*/

 	function get_user_name_field( $elements , $user_name = null , $disabled = null ){ 

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); 
 		$label = ( !empty($elements['label']) ? $elements['label'] : 'Name' ); ?>
 		<div class="wpad_form_group" ids="wpad_user_name">
     		<label><?php echo $label . $asterisk; ?></label>
     		<div class="input_container">
     			<div class="wpad_input_wrap">
       				<input <?php echo $required . ' ' . $disabled; ?> class="input_control <?php echo $elements['css_name']; ?>" type="text" name="<?php echo $elements['input_name']; ?>" placeholder="<?php echo $elements['placeholder_text']; ?>" value="<?php echo $user_name; ?>" size="<?php echo $elements['size']; ?>" key="<?php echo $elements['meta_key']; ?>" element="user_name">
       			</div>
       			<div class="wpad_error" style="display:none"></div>
       			<?php echo $this->help_text( $elements['help_text'] ); ?>
     		</div>
   		</div>
 		<?php 

 	}

 	/*
	** Show or hide the user name from the user
	*/

 	function get_user_name( $elements , $atts ){

 		$id = $atts['id'];
 		$user_id = get_current_user_id();
 		$data = get_option( 'wpad_comment_form' );
 		$display_user_name = ( !empty($data[$id]['other']['user_name_show']) ? $data[$id]['other']['user_name_show'] : '' );
 		if( is_user_logged_in() ){
 			$user_name = get_the_author_meta( 'user_login' , $user_id );
 			$disabled = 'disabled';
 			if( $display_user_name == 'enable' ){
 				$this->get_user_name_field( $elements , $user_name , $disabled );	
 			}
 		} else {
 			$this->get_user_name_field( $elements );
 		}

 	}

 	/*
	** Show the email field
	*/

 	function get_email_required_field_input( $elements , $user_email = null , $disabled = null ){ 

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); 
 		$label = ( !empty($elements['label']) ? $elements['label'] : 'Email' );  ?>
 		<div class="wpad_form_group" ids="wpad_user_email">
     		<label><?php echo $label . $asterisk; ?></label>
     		<div class="input_container">
     			<div class="wpad_input_wrap">
       				<input <?php echo $disabled; ?> class="<?php echo $required; ?> input_control <?php echo $elements['css_name']; ?>" type="text" name="<?php echo $elements['input_name']; ?>" placeholder="<?php echo $elements['placeholder_text']; ?>" value="<?php echo $user_email; ?>" size="<?php echo $elements['size']; ?>" laxEmail="true" key="<?php echo $elements['meta_key']; ?>" element="email">
      			
      			</div>
       			<div class="wpad_error" style="display:none"></div>
       			<?php echo $this->help_text( $elements['help_text'] ); ?>
     		</div>
   		</div>
 		<?php 

 	}

 	/*
	** Show or hide the email field
	*/

 	function get_email_required_field( $elements , $atts ){ 

 		$id = $atts['id'];
 		$user_id = get_current_user_id();
 		$data = get_option( 'wpad_comment_form' );
 		$display_email = !empty($data[$id]['other']['user_email_show']) ? $data[$id]['other']['user_email_show'] : '';
 		if( is_user_logged_in() ){
 			$user_email = get_the_author_meta( 'user_email' , $user_id );
 			$disabled = 'disabled';
 			if( $display_email == 'enable' ){
 				$this->get_email_required_field_input( $elements , $user_email , $disabled );	
 			}
 		} else {
 			$this->get_email_required_field_input( $elements );
 		}

 	}

 	/*
	** Get the HTML code
	*/

 	function get_html( $elements ){ 
 		echo '<div class="wpad_section_html">';
 		if( !empty($elements['help_text']) ){ 
 			echo $elements['help_text']; 
		
		} 
 		echo '</div>';
 	}

 	/*
	** Get Section Break
	*/

 	function get_section_break( $elements ){ 

 		echo '<div class="wpad_section_break">';
 		if( !empty($elements['label']) ){
			echo '<h4>' . $elements['label'] . '</h4>';
		}
 		if( !empty($elements['help_text']) ){ ?>
			
			<p><?php echo $elements['help_text']; ?></p>
			
			<?php 
		} ?>
		<div class="wpad_section_break_line"></div>
 		<?php
 		echo '</div>';

 	}

 	/*
	** Get the user image
	*/

	function get_user_image_field( $elements ){

		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); ?>
 		<div class="wpad_form_group" ids="wpad_user_image_element">
     		<label><?php echo $elements['label'] . $asterisk; ?></label>
     		<div class="input_container">
     			<div class="wpad_input_wrap">
    				<input <?php echo $required; ?> class="input_control <?php echo $elements['css_name']; ?>" type="file" name="<?php echo $elements['input_name']; ?>" key="<?php echo $elements['meta_key']; ?>" element="user_image" autocomplete="off" id="<?php echo $elements['meta_key']; ?>">
    				
    				<?php 
    				if( !empty( $elements['preview'] ) && $elements['preview'] == 'yes' ){ ?>

    					<img class="wpad_image_preview" src="" style="display:none">

    					<?php
    				} ?>
    				
    			</div>
    			<div class="wpad_error" style="display:none"></div>
    			<?php echo $this->help_text( $elements['help_text'] ); ?>
     		</div>
   		</div>
 		<?php 

	}

	/*
	** Show or hide the user image field to specific users
	*/

	function get_user_image( $elements ){ 

		if( $elements['show_to'] == 'guest' && !is_user_logged_in() ){
			$this->get_user_image_field( $elements );
		} 

		if( $elements['show_to'] == 'logged_in' && is_user_logged_in() ){
			$this->get_user_image_field( $elements );
		} 

		if( $elements['show_to'] == 'both' ) {
			$this->get_user_image_field( $elements );
		}

 	}

 	/*
	** Get the text field
	*/

 	function get_text_field( $elements ){ 

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); ?>
 		<div class="wpad_form_group" ids="wpad_text_element">
     		<label><?php echo $elements['label'] . $asterisk; ?></label>
     		<div class="input_container">
     			<div class="wpad_input_wrap">
       				<input <?php echo $required; ?> class="input_control <?php echo $elements['css_name']; ?>" type="text" name="<?php echo $elements['input_name']; ?>" placeholder="<?php echo $elements['placeholder_text']; ?>" value="<?php echo $elements['default_value']; ?>" size="<?php echo $elements['size']; ?>" key="<?php echo $elements['meta_key']; ?>" element="text">
       			</div>
       			<div class="wpad_error" style="display:none"></div>
       			<?php echo $this->help_text( $elements['help_text'] ); ?>
     		</div>
   		</div>
 		<?php 

 	}

 	/*
	** Get the Default comment field
	*/

 	function get_comment_field( $elements ){ 

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); 
 		$label = ( !empty($elements['label']) ? $elements['label'] : 'Comment' ); ?>
 		<div class="wpad_form_group" ids="wpad_comment">
     		<label><?php echo $label . $asterisk; ?></label>
     		<div class="input_container">
     			<div class="wpad_input_wrap">
       				<textarea <?php echo $required; ?> class="input_control <?php echo $elements['css_name']; ?>"  name="<?php echo $elements['input_name']; ?>" placeholder="<?php echo $elements['placeholder_text']; ?>" value="<?php echo $elements['default_value']; ?>" rows="<?php echo $elements['rows']; ?>" cols="<?php echo $elements['column']; ?>" key="<?php echo $elements['meta_key']; ?>" element="comment"></textarea>
       			</div>
       			<div class="wpad_error" style="display:none"></div>
       			<p class="wpad_help_text"><?php echo $elements['help_text']; ?></p>
     		</div>
   		</div>
 		<?php 

 	}

 	/*
	** Get the Textarea
	*/

 	function get_textarea_field( $elements ){ 

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); ?>
 		<div class="wpad_form_group" ids="wpad_textarea_element">
     		<label><?php echo $elements['label'] . $asterisk; ?></label>
     		<div class="input_container">
     			<div class="wpad_input_wrap">
       				<textarea <?php echo $required; ?> class="input_control <?php echo $elements['css_name']; ?>"  name="<?php echo $elements['input_name']; ?>" placeholder="<?php echo $elements['placeholder_text']; ?>" rows="<?php echo $elements['rows']; ?>" cols="<?php echo $elements['column']; ?>" key="<?php echo $elements['meta_key']; ?>" ><?php echo $elements['default_value']; ?></textarea>
      				
      			</div>
       			<div class="wpad_error" style="display:none"></div>
       			<?php echo $this->help_text( $elements['help_text'] ); ?>
     		</div>
   		</div>
 		<?php 

 	}

  	/*
	** Get the Radio Button
	*/

 	function get_radio_field( $elements ){

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' );

 		if( !empty($elements['options']) ): ?>

	 		<div class="wpad_form_group" ids="wpad_radio_element">
	     		<label><?php echo $elements['label'] . $asterisk; ?></label>
	     		<div class="input_container wpad_radio_wrapper">
	     			<div class="wpad_input_wrap">
	 	    			<?php 
	 	    			$key = wp_generate_password( 25, false, false );
	 	    			
		 	    			foreach( $elements['options'] as $value ){ ?>
		 	    				<label>
		 	      					<input <?php echo $required; ?> class="input_control <?php echo $elements['css_name']; ?>" type="radio" name="<?php echo $elements['input_name']; ?>" value="<?php echo $value['option']; ?>" key="<?php echo $elements['meta_key']; ?>" ><?php echo $value['label']; ?>
		 	      				</label>
		 	      				<?php 
		 	      			} 

	 	      			?>       			
	 	      		</div>
	 	      		<div class="wpad_error" style="display:none"></div>
	 	      		<?php echo $this->help_text( $elements['help_text'] ); ?>
	     		</div>
	   		</div>
	 		<?php 

 		endif;

 	}

 	/*
	** Get the Checkbox
	*/

 	function get_checkbox_field( $elements ){

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' );

 		if( !empty( $elements['options'] ) ): ?>
	 		<div class="wpad_form_group" ids="wpad_checkbox_element">
	     		<label><?php echo $elements['label'] . $asterisk; ?></label>
	     		<div class="input_container wpad_radio_wrapper">
	     			<div class="wpad_input_wrap">
	 	    			<?php 
	 	    			$key = wp_generate_password( 25, false, false );
	 	    			foreach( $elements['options'] as $value ){ ?>
	 	    				<label>
	 	      					<input <?php echo $required; ?> class="input_control <?php echo $elements['css_name']; ?>" type="checkbox" name="<?php echo $elements['input_name']; ?>" value="<?php echo $value['option']; ?>" key="<?php echo $elements['meta_key']; ?>"><?php echo $value['label']; ?>
	 	      				</label>
	 	      				<?php 
	 	      			}  ?> 	      			
	 	      		</div>
	 	      		<div class="wpad_error" style="display:none"></div>
	 	      		<?php echo $this->help_text( $elements['help_text'] ); ?>
	     		</div>
	   		</div>
 			<?php 
 		endif;
 	}

 	/*
	** Get the Select box
	*/

 	function get_select_field( $elements ){

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); ?>
 		<div class="wpad_form_group" ids="wpad_select_element">
     		<label><?php echo $elements['label'] . $asterisk; ?></label>
     		<div class="input_container wpad_radio_wrapper">
     			<div class="wpad_input_wrap">
 	    			<?php 
 	    			$key = wp_generate_password( 25, false, false );
 	    			if( $elements['options'] ): ?>
 	    				<select name="<?php echo $elements['input_name']; ?>" class="<?php echo $required; ?> <?php echo $elements['css_name']; ?>" key="<?php echo $elements['meta_key']; ?>" >
 			    			<?php 
 			    			if( $elements['select_first_option'] != '' ){
 		    					echo '<option value="" selected="selected">' . $elements['select_first_option'] . '</option>';
 		    				} 
 			    			foreach( $elements['options'] as $value ): ?>
 			      				<option value="<?php echo $value['option']; ?>"><?php echo $value['label']; ?></option>
 			      				<?php 
 			      			endforeach; ?> 
 		      			</select>
 		      		<?php 
 		      		endif; ?> 	      			
 	      		</div>
 	      		<div class="wpad_error" style="display:none"></div>
 	      		<?php echo $this->help_text( $elements['help_text'] ); ?>
     		</div>
   		</div>
 		<?php 

 	}

 	/*
	** Get the Email field
	*/

 	function get_email_field( $elements ){ 

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); ?>
 		<div class="wpad_form_group" ids="wpad_email_element">
     		<label><?php echo $elements['label'] . $asterisk; ?></label>
     		<div class="input_container">
     			<div class="wpad_input_wrap">
       				<input class="<?php echo $required; ?> input_control <?php echo $elements['css_name']; ?>" type="text" name="<?php echo $elements['input_name']; ?>" placeholder="<?php echo $elements['placeholder_text']; ?>" value="<?php echo $elements['default_value']; ?>" size="<?php echo $elements['size']; ?>" laxEmail="true" key="<?php echo $elements['meta_key']; ?>" >
      				
      			</div>
       			<div class="wpad_error" style="display:none"></div>
       			<?php echo $this->help_text( $elements['help_text'] ); ?>
     		</div>
   		</div>
 		<?php 

 	}

 	/*
	** Get the URL field
	*/

 	function get_url_field( $elements ){ 

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); ?>
 		<div class="wpad_form_group" ids="wpad_url_element">
     		<label><?php echo $elements['label'] . $asterisk; ?></label>
     		<div class="input_container">
     			<div class="wpad_input_wrap">
       				<input class="<?php echo $required; ?> input_control <?php echo $elements['css_name']; ?>" type="text" name="<?php echo $elements['input_name']; ?>" placeholder="<?php echo $elements['placeholder_text']; ?>" value="<?php echo $elements['default_value']; ?>" size="<?php echo $elements['size']; ?>" url="true" key="<?php echo $elements['meta_key']; ?>">
       			</div>
       			<div class="wpad_error" style="display:none"></div>
       			<?php echo $this->help_text( $elements['help_text'] ); ?>
     		</div>
   		</div>
 		<?php 

 	}

 	/*
	** Get the Multi select field
	*/

 	function get_multi_select_field( $elements ){

 		$required = ( $elements['required'] == 'yes' ? 'required' : '' ); 
 		$asterisk = ( $elements['required'] == 'yes' ? '<span class="wpad_required"> *</span>' : '' ); ?>
 		<div class="wpad_form_group" ids="wpad_multi_select_element">
     		<label><?php echo $elements['label'] . $asterisk; ?></label>
     		<div class="input_container wpad_radio_wrapper">
     			<div class="wpad_input_wrap">
 	    			<?php 
 	    			$key = wp_generate_password( 25, false, false );
 	    			if( $elements['options'] ): ?>
 	    				<select name="<?php echo $elements['input_name']; ?>" class="<?php echo $required; ?> <?php echo $elements['css_name']; ?>" multiple key="<?php echo $elements['meta_key']; ?>">
 			    			<?php 
 			    			if( $elements['select_first_option'] != '' ){
 		    					echo '<option value="">' . $elements['select_first_option'] . '</option>';
 		    				} 
 			    			foreach( $elements['options'] as $value ): ?>
 			      				<option value="<?php echo $value['option']; ?>"><?php echo $value['label']; ?></option>
 			      				<?php 
 			      			endforeach; ?> 
 		      			</select>
 		      		<?php 
 		      		endif; ?> 	      			
 	      		</div>
 	      		<div class="wpad_error" style="display:none"></div>
 	      		<?php echo $this->help_text( $elements['help_text'] ); ?>
     		</div>
   		</div>
 		<?php 

 	}

 	/*
	** Get the help text for the fields
	*/

 	function help_text( $data ){
 		if( $data != '' ){
 			return '<p class="wpad_help_text">' . $data . '</p>';
 		}
 	}

 	/*
	** If the guest has privilage then show the comment form to the guests
	*/

 	function check_guest_privalage( $data , $atts ){
 		$id = $atts['id'];
 		if( empty($data) ){
 			return;
 		}
 		foreach( $data as $key => $value ){
 			if( $key == $id ){
 				if( isset($data[$id]['other']['guest_comment']) && $data[$id]['other']['guest_comment'] == 'enable'){
 					return true;
 				} else {
 					return false;
 				}
 			}
 		}
 	}

 	/*
	** Count the no of comments on a the post
	*/

 	function count_comments( $post_id , $results , $option , $id , $status = null ){ 
 		$count = 0;
 		foreach( $results as $key => $value ){ 
 			$form_id = get_comment_meta( $value['comment_ID'] , 'wpad_comment_form_id' , true );
 			$display_comment_form = $this->check_comment_list_status( $form_id , $option , $id );
 			if( $display_comment_form == true ){
 				if( $form_id == $id ){
 					 ++$count;
 				}
 			} else {
 				++$count;
 			}
 		}
 		// If WP Advanced Comment WooCommerce Integration plugin installed
		if( $status == 'woocommerce' ){
 			return $count;
 		} else {
 			$title = get_the_title( $post_id );
			if( $count < 2 && $count > 0) {
 				echo '<h3>' . $count . __( " har signert p&aring; '{$title}'" , 'wpad' ) . ' </h3>';
 			} elseif( $count == 0 ){
 				echo 'Det er ingen som har signert. Bli den f&oslash;rste til &aring; signere.';
 			} else {
 				echo '<h3>' . $count  .  __( " har signert p&aring; '{$title}'" , 'wpad' ) . ' </h3>';
 			}
 		}
		
 	}

 	/*
	** Convert the comment dates in the time ago
	*/

 	function timeAgo($time_ago){
 	    $time_ago = strtotime($time_ago);
 	    $cur_time   = strtotime(date( 'd-m-Y H:i' ));
 	    $time_elapsed   = $cur_time - $time_ago;
 	    $seconds    = $time_elapsed ;
 	    $minutes    = round($time_elapsed / 60 );
 	    $hours      = round($time_elapsed / 3600);
 	    $days       = round($time_elapsed / 86400 );
 	    $weeks      = round($time_elapsed / 604800);
 	    $months     = round($time_elapsed / 2600640 );
 	    $years      = round($time_elapsed / 31207680 );
 	    // Seconds
 	    if($seconds <= 60){
 	        return __( "akkurat n&aring;" , 'wpad' );
 	    }
 	    //Minutes
 	    else if($minutes <=60){
 	        if($minutes==1){
 	            return  __( "ett minutt siden" , 'wpad' );
 	        }
 	        else{
 	            $wpad_min = "$minutes minutter siden";
 	            return __( $wpad_min , 'wpad' );
 	        }
 	    }
 	    //Hours
 	    else if($hours <=24){
 	        if($hours==1){
 	            return __( "&Eacute;n time siden" , 'wpad' );
 	        }else{
 	            $wpad_hr =  "$hours timer siden";
 	            return __( $wpad_hr , 'wpad' );
 	        }
 	    }
 	    //Days
 	    else if($days <= 7){
 	        if($days==1){
 	            return __( "ig&aring;r" , 'wpad' );
 	        }else{
 	            $wpad_days = "$days dager siden";
 	            return __( $wpad_days , 'wpad' );
 	        }
 	    }
 	    //Weeks
 	    else if($weeks <= 4.3){
 	        if($weeks==1){
 	            return __( "en uke siden" , 'wpad' );
 	        }else{
 	            $wpad_week = "$weeks uker siden";
 	            return __( $wpad_week , 'wpad' );
 	        }
 	    }
 	    //Months
 	    else if($months <=12){
 	        if($months==1){
 	            return __( "en m&aring;ned siden" , 'wpad' );
 	        }else{
 	            $wpad_month = "$months m&aring;neder siden";
 	            return __( $wpad_month , 'wpad' );
 	        }
 	    }
 	    //Years
 	    else{
 	        if($years==1){
 	            return __( "ett &aring;r siden" , 'wpad' );
 	        }else{
 	            $wpad_yr = "$years &aring;r siden";
 	            return __( $wpad_yr , 'wpad' );
 	        }
 	    }
 	}

 	/*
	** Check the user id on the database
	*/

 	function user_id_exists($user){

	    global $wpdb;

	    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user));

	    if($count == 1){ 
	    	return true; 
	    } else { 
	    	return false; 
	    }

	}

	/*
	** Get the user gravatar
	*/

 	function validate_gravatar( $email , $option = null , $object = null , $size = 150 ) {

 		$user_exist = $this->user_id_exists( $object['user_id'] );

 		if( !empty($option) ){

	 		foreach( $option as $key => $value ){

	 			if( !empty($value['custom_field']) && $value['custom_field'] == 'user_image' ){
	 				
	 				$avatar_id = get_comment_meta( $object['comment_ID'] , $value['meta_key'] , true );
		 			$image_attributes = wp_get_attachment_image_src( $avatar_id , 'thumbnail' );

		 			if( !is_array( $image_attributes ) ){
		 				return get_avatar( $email , $size );
		 			}

		 			if( $value['show_to'] == 'guest' && $user_exist == false ){

		 				/*
			 			** If user is guest, show guest image from comment meta
						*/
		 				return "<img width=" . $size . " height=" . $size . " src=" . $image_attributes[0] . ">";

		 			} 

		 			elseif( $value['show_to'] == 'logged_in' && $user_exist == true ){

		 				/*
			 			** If user exist, show logged in user's image from comment meta
						*/
		 				return "<img height=". $size ." width=". $size ." src=" . $image_attributes[0] . ">";

		 			} elseif( $value['show_to'] == 'both' ){

		 				return "<img height=". $size ." width=". $size ." src=" . $image_attributes[0] . ">";

		 			}

	 			}
	 			
	 		}

	 	}

 		return get_avatar( $email , $size );

 	}

 	/*
	** Show the fields to the administrator on the frontend on the comment list
	*/

 	function check_administrator( $show_admin ){
 		if( current_user_can('administrator') ){
 			// administrator can view everything
 			return true;
 		} else {
 			// Hide from user 
 			if( $show_admin == 1 ){
 				return false;
 			} else {
 				// show to the users
 				return true;
 			}
 		}
 	}

 	/*
	** Show the edit button on the front end to the administrator
 	*/

 	function edit_button_admin( $commentid , $comment_form_id ){ 
 		if( current_user_can( 'manage_options' ) ){ 
 			ob_start(); ?>
 			<a target="blank" href="<?php echo admin_url('/admin.php?page=wpad_saved_edit_form&'). 'commentid=' . $commentid . '&formid=' . $comment_form_id;?>" class="edit_comment_front">
 				<span class="dashicons dashicons-category"></span>
 				<?php _e( 'Edit' , 'wpad' ); ?>
 			</a>
 			<?php
 			return $content = ob_get_clean();
 		}
 	}

 	/*
	** Get the comment date and time 
 	*/

 	function post_comment_time( $comment_time , $value ){
 		if( $comment_time == '1'){
 			return date('M d, Y \a\t g:i A', strtotime($value['comment_date']) );
 		} else { 
 			return $this->timeAgo( $value['comment_date'] ); 
 		} 
 	}

 	/*
	** If 'enable' then the comments will be shown from the selected comment form only. 
	** If 'disable' then all the published comments will be shown for a single post.  
 	*/

 	function check_comment_list_status( $form_id , $option , $id ){
 		if( !empty($option) ){
 			foreach( $option as $key => $value ){
 				if( $key == $id ){
 					if(  isset($option[$id]['other']['comment_listing']) && $option[$id]['other']['comment_listing']  == 'enable' ){
 						return true;
 					} else {
 						return false;
 					}
 				}
 			}
 		}
 	}

 	/*
	** Get the total no of comments in a post.
 	*/

 	function get_total_comments( $post_id , $ids ){
 		if( empty($ids) ){
			$ids = 0;
		}
 		global $wpdb;
 		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}" . "comments 
 			WHERE comment_post_ID = {$post_id}
 			AND comment_ID IN ($ids)
 			AND comment_approved = 1", ARRAY_A );
 		return $results;
 	}

 	/*
	** Get the comments from the single comment form selected.
 	*/

 	function comments_listing_enable( $id , $post_id , $per_page , $offset , $order_by ){
 		global $wpdb;
 		$all_comments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}" ."commentmeta
 			WHERE `meta_key` = 'wpad_comment_form_id'
 			AND `meta_value` = {$id}" , ARRAY_A);
 		$comment_ids = array();
 		foreach( $all_comments as $key => $value ){
 			$comment_ids[] = $value['comment_id'];
 		}
 		if( empty($comment_ids) ){
 			$comment_ids[] = 0;
 		}
 		$ids = implode(',',$comment_ids);
 		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}" . "comments 
 			WHERE comment_post_ID = {$post_id}
 			AND comment_ID IN ($ids)
 			AND comment_approved = 1 
 			ORDER BY comment_ID {$order_by}
 			LIMIT {$per_page} OFFSET {$offset}", ARRAY_A );
 		return array( 'result' => $results , 'ids' => $ids );
 	}

 	/*
	** Get all the comments on the post.  
 	*/

 	function comment_listing_disable( $post_id , $per_page , $offset , $order_by ){
 		global $wpdb;
 		$all_comments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}" . "comments 
 			WHERE comment_post_ID = {$post_id}
 			AND comment_approved = 1", ARRAY_A );
 		$comment_ids = array();
 		foreach( $all_comments as $key => $value ){
 			$comment_ids[] = $value['comment_ID'];
 		}
 		$ids = implode(',',$comment_ids);
 		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}" . "comments 
 			WHERE comment_post_ID = {$post_id}
 			AND comment_approved = 1 
 			ORDER BY comment_ID {$order_by}
 			LIMIT {$per_page} OFFSET {$offset}", ARRAY_A );
 		return array( 'result' => $results , 'ids' => $ids );
 	}

 	/*
	** Check comment listing enabled or disabled
 	*/

 	function check_comment_listing_status( $option , $id ){
 		if( array_key_exists( 'comment_listing' , $option[$id]['other'] ) && $option[$id]['other']['comment_listing'] == 'enable' ){
 			return true;
 		} else {
 			return false;
 		}
 	}

 	/*
	** Get the comments
 	*/

 	function show_comment_lists( $id = null , $layout = null ){

 		$option = get_option( 'wpad_comment_form' ); 

 		if( !empty( $_POST ) ){

 			$post = get_post( $_POST['post_id'] );
 			$id = $_POST['form_id'];
 			$per_page = $option[$id]['other']['pagination_per_page'];
 			$offset = ( $_POST['page_no'] - 1 ) * $per_page;
 			$page_no = $_POST['page_no'];

 		} else {

 			global $post;
 			wp_reset_postdata(); wp_reset_query();
 			$offset = 0;
 			$page_no = 1;

 			if( isset($option[$id]['other']['pagination']) && $option[$id]['other']['pagination'] == 'enable' ){

 				$per_page = !empty($option[$id]['other']['pagination_per_page']) ? $option[$id]['other']['pagination_per_page'] : 10;	

 			} else {

 				$per_page = 999999;

 			}

 		}

 		$comment_time = $option[$id]['other']['comment_time'];
 		$status = $this->check_comment_listing_status( $option , $id );
 		$order_by = !empty($option[$id]['other']['comment_order_by']) ? $option[$id]['other']['comment_order_by'] : 'DESC';

 		if( $status == true ){
 			$data = $this->comments_listing_enable( $id , $post->ID , $per_page , $offset , $order_by );
 			$results = $data['result'];
 		} else {
 			$data = $this->comment_listing_disable( $post->ID , $per_page , $offset , $order_by );
 			$results = $data['result'];
 		}

 		if( !empty($results) ){ 

 			$total_comments = $this->get_total_comments( $post->ID , $data['ids'] ); 
 			ob_start(); ?>

 			<div class="wpad_list_comments_frontend">

 				<input type="hidden" name="pagination_post" value="<?php echo $post->ID; ?>">
 				
 				<?php
 				$this->count_comments( $post->ID , $total_comments , $option , $id );

 				/******************************
				// Display Pagination on Top
				*******************************/

 				$pagination_pos = !empty($option[$id]['other']['pagination_position']) ? $option[$id]['other']['pagination_position'] : 'top_bottom';

 				$position_top = array( 'top' , 'top_bottom' );

 				$pagination = !empty($option[$id]['other']['pagination']) ? $option[$id]['other']['pagination'] : 'disable';

 				$this->check_position_pagination( $pagination , $total_comments , $page_no , $per_page , $position_top , $pagination_pos , $option[$id] );

 				foreach( $results as $key => $value ){ 
 					$form_id = get_comment_meta( $value['comment_ID'] , 'wpad_comment_form_id' , true );
 					$display_comment_form = $this->check_comment_list_status( $form_id , $option , $id );
 					if( $display_comment_form == true ){
 						if( $form_id == $id ){
 							$this->get_comment_section( $value , $layout , $comment_time , $option , $id );
 						}
 					} else {
 						$this->get_comment_section( $value , $layout , $comment_time , $option , $id );
 					}
 				} 
 				/******************************
				// Display Pagination on Bottom
				*******************************/
 				$position_bottom = array('bottom','top_bottom');
 				$this->check_position_pagination( $pagination , $total_comments , $page_no , $per_page , $position_bottom , $pagination_pos , $option[$id] ); ?>
 			</div>	
 			<?php
 			$content = ob_get_clean();
 			if( empty($_POST) ){
 				return $content;	
 			} else {
 				$data = array( 'content' => $content );
 				echo json_encode( $data );
 				die;
 			} 
 		}
 	}

 	/*
	** Check if the pagination is enable or disable on the comment list.
 	*/

 	function check_position_pagination( $pagination , $total_comments , $page_no , $per_page , $valid_position ,  $db_position , $option ){
 		if( isset( $pagination ) && $pagination == 'enable' ){
 			if( in_array( $db_position , $valid_position ) ){
 				echo '<ul class="pagination_wpad">';
 				echo $this->paginate_links( count($total_comments) , $page_no , $per_page , $option );
 				echo '</ul>';
 			}
 		}
 	}


 	/*
	** Get the pagination for the comment.
 	*/

 	function paginate_links( $count_posts , $page_no , $per_page , $option ){ 
 		if($count_posts != 0){
             $page = ceil($count_posts/$per_page);
             $range = 1;
             $range_increase = $range + 1;
             $showitems = ($range * 2)+1;
             $pagination = '<li><a class="not_click">' . 'Side ' . $page_no . ' av ' . $page . '</a></li>';
             if($page_no > 2 && ($page_no > $range_increase) && ($showitems < $page)){
             	$first = !empty($option['other']['text_for_first_page']) ? $option['other']['text_for_first_page'] : '&laquo; F&oslash;rste';
                 $pagination .= '<li><a href="javascript:void(0)" page_no="1">' . $first . '</a></li>';
             }
             if($page_no > 1 && $showitems < $page){
             	$previous = !empty($option['other']['text_for_previous_page']) ? $option['other']['text_for_previous_page'] : '&lsaquo; Forrige';
                 $pagination .= '<li><a href="javascript:void(0)" page_no="' . ($page_no - 1) . '">' . $previous . '</a></li>';
             }
             for($i = 1; $i <= $page; $i++){ 
                 if (1 != $page &&( !($i >= $page_no+$range+1 || $i <= $page_no-$range-1) || $page <= $showitems )){
                     if($page_no == $i){
                           $active = 'active not_click';
                     } else{
                           $active ='';
                     }
                     $pagination .= '<li class="' . $active . '"><a href="javascript:void(0)" page_no="' . $i . '">' . $i . '</a></li>';
                 }
             }
             if ($page_no < $page && $showitems < $page){
             	$next = !empty($option['other']['text_for_next_page']) ? $option['other']['text_for_next_page'] : 'Neste &rsaquo;';
                 $pagination .= '<li><a href="javascript:void(0)" page_no="' . ($page_no + 1) . '">' . $next . '</a></li>';
             }
             if ($page_no < $page-1 &&  $page_no+$range-1 < $page && $showitems < $page){
             	$last = !empty($option['other']['text_for_last_page']) ? $option['other']['text_for_last_page'] : 'Siste &raquo;';
                 $pagination .= '<li><a href="javascript:void(0)" page_no="' . $page . '">' . $last . '</a></li>';
             }
         	return $pagination;
         }
 	}

 	/*
	** Check if user wants the default layout or the custom layout for the comments
 	*/

 	function get_comment_section( $value , $layout , $comment_time , $option , $id ){ ?>
 		<div class="wpad_row" id="<?php echo 'wpad_comment_' . $value['comment_ID'];?>">
 			<?php 
 			if( empty( $layout ) ){
 				$this->default_layout( $value , $comment_time , $option , $id );
 			} else {
 				echo $this->filter_custom_layout( $layout , $value , $comment_time , $id );
 			} ?>
 		</div>
 		<?php
 	}

 	/*
	** If user has chosen the custom layout the filter the codes.
 	*/

 	function filter_custom_layout( $layout , $value , $comment_time , $id ){

 		$option = get_option( 'wpad_comment_form' ); 

 		$comment_form_id = get_comment_meta( $value['comment_ID'] , 'wpad_comment_form_id' , true );

 		$layout = str_replace( "%gravatar%" , $this->validate_gravatar( $value['comment_author_email'] , $option[$id] , $value ) , $layout );

 		$layout = str_replace( "%edit_button%" , $this->edit_button_admin( $value['comment_ID'] , $comment_form_id ) , $layout );

 		$layout = str_replace( "%comment_author%" , $value['comment_author'] , $layout );

 		$layout = str_replace( "%comment_time%" , $this->post_comment_time( $comment_time , $value ) , $layout );

 		$layout = str_replace( "%comment%" , $value['comment_content'] , $layout );

 		$layout = str_replace( "%like_dislike_btn%" , $this->get_custom_like_btn( $value['comment_ID'] ) , $layout );

 		$layout = $this->custom_get_comment_metas( $layout , $id , $value );

 		return $layout;
 	}

 	function get_custom_like_btn($comment_id){

 		$btn = new wpad_like_dislike();
 		return $like_dislike = $btn->get_buttons( $comment_id , 'both' );

 	}

 	function check_comment_metas_custom_layout( $value1 ){

 		$custom_field = array( 'section_break' , 'user_image' );

 		if( !empty($value1['custom_field']) && in_array( $value1['custom_field'] , $custom_field ) ){

 			return true;

 		} else {

 			return false;

 		}

 	}

 	/*
	** show the comment metas
 	*/

 	function custom_get_comment_metas( $layout , $id , $value){
 		$data = get_option( 'wpad_comment_form' ); 
 		if( !empty( $data[$id] ) ): 
 			foreach( $data[$id] as $key => $value1 ){

 				$restrict_meta_key = array( 'user_name' , 'user_email' , 'wpad_comment' );
 				$restrice_custom_fields = $this->check_comment_metas_custom_layout( $value1 );

 				if( (!empty($value1['custom_field']) && $restrice_custom_fields == true ) || $key == 'other' ){

 					continue;

 				} else {

 					if( !in_array( $value1['meta_key']  , $restrict_meta_key ) ){
 						$comment_meta = get_comment_meta( $value['comment_ID'] , $value1['meta_key'] , true );
 						$comment_meta = !empty($comment_meta) ? $comment_meta : '';
 						$custom_field = '%custom_field_' . $value1['meta_key'] . '%';
 						$layout = str_replace( $custom_field , $comment_meta , $layout );
 					}	

 				}

 			}

 		endif;

 		return $layout;

 	}

 	function get_admin_role( $user_id , $display_roles ){

 		if( !empty($display_roles['display_roles']) && $display_roles['display_roles'] == 'disable' ){
 			return;
 		}

 		$user_data = get_userdata( $user_id );

 		if( !empty( $user_data ) ){

 			$name = 'role_color_' . $user_data->roles[0]; 
 			$background_color = get_option($name);

 			echo '<span style="background:' . $background_color . '" class="wpad_admin_tag">' . __(  str_replace( '_' , ' ' , $user_data->roles[0] ) , 'wpad' ) . '</span>';
 		} else {
 			echo '<span style="background:' . get_option('role_color_guest') . '" class="wpad_admin_tag">' . __( 'Guest' , 'wpad' ) . '</span>';
 		}

 	}

 	function report_comment( $comment_id ){ 

 		$reporting = get_option('wpad_disable_reporting'); 

 		if( !empty($reporting) && $reporting == 'disable' ){ 
 			return;
 		}

 		$comment_reports = get_comment_meta( $comment_id, 'comment_flagged_reports', true );

 		if( !empty($comment_reports) ){

 			foreach( $comment_reports as $value ){

				if( $value['ip'] == $_SERVER["REMOTE_ADDR"] ){

					return;

				} 				

 			}

 		}

 		?>

 		<a href="javascript:void(0)" class="wpad_comment_report" comment_id="<?php echo $comment_id; ?>"><?php _e( 'Report this comment' , 'wpad' ); ?></a>

 		<?php

 	}

 	/*
	** Get the default layout
 	*/

 	function default_layout( $value , $comment_time , $option , $id ){ ?>
 		<div class="wpad_content_comment">
 			<div class="wpad_front_gravatar">
			<img src="http://blogg.regjeringen.no/erklaringmothatytringer/files/2015/11/penn2.png">
 				<!-- ?php 
 				$comment_form_id = get_comment_meta( $value['comment_ID'] , 'wpad_comment_form_id' , true );
 				echo $this->validate_gravatar( $value['comment_author_email'] , $option[$id] , $value ); 
 				echo $this->edit_button_admin( $value['comment_ID'] , $comment_form_id ); -->
 			</div>
 			<div class="wpad_content_wrap">
 				<strong><?php echo $value['comment_author']; ?> </strong>
 				<?php _e( 'signerte.' , 'wpad' ); ?> 

 				<!-- ?php 
 				 $this->get_admin_role( $value['user_id'] , $option[$id]['other'] ); 
 				$this->report_comment( $value['comment_ID'] );
 				? -->

 				<span class="wpad_right wpad_time">
 					<?php 
 					echo $this->post_comment_time( $comment_time , $value );
 					?>
 				</span>
 				<!-- Show Comments -->
 				<?php 
 				if( $option[$id]['other']['comment_position'] == 1 ){

 					echo $this->show_like_dislike_button( $value['comment_ID'] , $option[$id]['other'] , 'top' );
					
					echo '<p>' . $value['comment_content'] . '</p>';

					echo $this->show_like_dislike_button( $value['comment_ID'] , $option[$id]['other'] , 'bottom' );

 				}?>
 				<!-- Get the comment meta -->
 				<?php 
 				$data = get_option( 'wpad_comment_form' ); 
 				if( !empty( $data[$id] ) ): ?>
 					<div class="wpad_comment_meta">
 						<ul>
 							<?php 
 							foreach( $data[$id] as $key => $value1 ){
 								$show_admin = isset($value1['show_admin']) ? $value1['show_admin'] : 0;
 								$privelage = $this->check_administrator( $show_admin ); 

 								if( isset($value1['meta_key']) && $key != 'other' && $value1['meta_key'] != 'user_name' && $value1['meta_key'] != 'user_email' && $value1['custom_field'] != 'user_image' && $value1['meta_key'] != 'wpad_comment' && $privelage == true ){

 									$meta_key = $value1['meta_key'];
 									$label = $value1['label'];
									
									$meta_value = get_comment_meta( $value['comment_ID'] , $meta_key , true );
 									if( !empty( $meta_value ) ){
 										if( $value1['custom_field'] == 'radio' ){
 											$radio_value = $this->get_corresponding_metakey( $value1 , $meta_value , 'radio' );
											$this->display_comment_metas_frontend( $label , $radio_value );
 										} elseif( $value1['custom_field'] == 'checkbox' ){
 											$check_value = $this->get_corresponding_metakey( $value1 , $meta_value , 'checkbox' );
											$this->display_comment_metas_frontend( $label , $check_value );
 										} else {
 											$this->display_comment_metas_frontend( $label , $meta_value );
 										}
										
									}
 								}
 							} ?>
 						</ul>
 					</div>
 					<?php
 				endif; ?>
 				<!-- Show Comments -->
 				<?php 
 				if( $option[$id]['other']['comment_position'] == 2 ){
 					echo '<p>' . $value['comment_content'] . '</p>';
 				}?>
 			</div>
 		</div>
 		<?php
 	}

 	/*
	** For the radio button or checkbox show the Option label not the value on the frontend.
 	*/

 	function get_corresponding_metakey( $value , $meta_value , $custom_field ){

 		switch ( $custom_field ) {
 			case 'radio':
				
				foreach( $value['options'] as $meta ){
 					if( $meta['option'] == $meta_value ){
 						return $meta['label'];
 					}
 				}
 				break;
 			case 'checkbox':
 				$check_arr = explode( ',' , $meta_value );
				$checked = array();
 				foreach( $value['options'] as $meta ){
 					if( in_array( $meta['option'] , $check_arr ) ){
 						$checked[] = $meta['label'];
 					}
 				}
				
				return implode( ' , ' , $checked );
 				break;
			
			default:
				break;
 		}
		
 	}

 	/*
	** Show the comment metas
 	*/

 	function display_comment_metas_frontend( $label , $meta_value ){
 		echo '<li>';
 		echo '<label>' . $label . ' : </label>
 		<span class="meta_value"> ' . $meta_value . ' </span>';
 		echo '</li>';
 	}

 	/*
 	** Show the one column design for the comment form
 	*/

 	function one_column_design( $column ){
 		if( $column == 1 ){
 			echo '<style>
 			.wpad_form_group label {
 				float: none !important;
 				padding-left: 0 !important;
 				text-align: left !important;
 			}
 			.wpad_form_group .input_container {
 				padding-left: 0 !important;
 			}
 			</style>';
 		}
 	}

 	function success_message_submit( $id ){

 		$wpad_save_data = new wpad_save_data();
 		$result = $wpad_save_data->comment_publish_pending( $id );

 		if( $result == 0 ){
 			_e( "Signeringen venter p&aring; moderering" , 'wpad' );
 		} else {
 			_e( "Your comment has been successfully published. Please refresh the page to see your comment." , 'wpad' );
 		}

 	}

 	/*
	** Show the comment form to the user.
 	*/

 	function display_form_user( $data , $id , $atts ){ 

 		// *******************************************
		// If show comments selected disable the form
		// *******************************************

 		if( $data[$id]['other']['comment_status'] != 'show_comments' ){  ?>
 			<div class="wpad_success_comment" style="display:none">
 				<p><?php $this->success_message_submit( $id ); ?></p>
 			</div>
 			<div class="wpad_comment_fields_wrapper">
 				<h3><?php _e( "Legg igjen en signatur" , 'wpad' ); ?></h3>
 				<p class="wpad_email_not_publish"><?php _e( "Eposten din blir ikke publisert. P&aring;krevde felt er markert." , 'wpad' ); ?></p>
 				<?php 
 				foreach( $data[$id] as $elements  ): 				
 					if( is_array($elements) ){
 						$meta_key = !empty($elements['meta_key']) ? $elements['meta_key'] : '';
 						do_action( 'wpad_before_field_' . $meta_key );
 						if( !empty($elements['custom_field']) ){
 							switch ( $elements['custom_field'] ) {
 								case 'text':
 									$this->get_text_field( $elements );
 									break;
 								case 'user_name':
 									$this->get_user_name( $elements , $atts );
 									break;
 								case 'user_email':
 									$this->get_email_required_field( $elements , $atts );
 									break;
 								case 'comment_area':
 									$this->get_comment_field( $elements );
 									break;
 								case 'textarea':
 									$this->get_textarea_field( $elements );
 									break;
 								case 'radio':
 									$this->get_radio_field( $elements );
 									break;
 								case 'checkbox':
 									$this->get_checkbox_field( $elements );
 									break;
 								case 'select':
 									$this->get_select_field( $elements );
 									break;
 								case 'email':
 									$this->get_email_field( $elements );
 									break;
 								case 'url':
 									$this->get_url_field( $elements );
 									break;
 								case 'multi_select':
 									$this->get_multi_select_field( $elements );
 									break;
 								case 'section_break':
 									$this->get_section_break( $elements );
 									break;
 								case 'html':
 									$this->get_html( $elements );
 									break;
 								case 'user_image':
 									$this->get_user_image( $elements );
 									break;
 								default:
 									break;
 							}
 						}
 						
 						do_action( 'wpad_after_field_' . $meta_key );

 					}

 				endforeach; 

 				// Enable/Disable the approved comment checkbox
 				$this->comment_approved_message_checkbox( $id , $data ); ?>

 			</div>

 			<div class="wpad_form_group">
 	    		<label></label>
 	    		<div class="input_container">
 	    			<input type="hidden" name="wpad_comment_form_id" value="<?php echo $id; ?>">
 	    			<input type="hidden" name="wpad_comment_form" value="1">
 	    			<?php $submit_name = !empty( $data[$id]['other']['submit_label'] ) ? $data[$id]['other']['submit_label'] : 'Signér'; ?>
 	      			<input type="submit" class="wpad_submit_comment wpad_btn wpad_primary" value="<?php echo $submit_name; ?>">
 	      			<img style="display:none" class="wpad_submit_loader" src="<?php echo includes_url('/images/spinner.gif'); ?>">
 	    		</div>
 	  		</div>
 			<?php
 		}
 	}

 	function comment_approved_message_checkbox( $id , $data ){

 		if( !empty($data) ){

	 		foreach( $data as $key => $value ){

	 			if( $key == $id ){

	 				if( empty( $data[$id]['other']['disable_approve_notification'] ) ){ ?>

	 					<div class="wpad_form_group">
		 					
		 					<label></label>
		 					<div class="input_container wpad_radio_wrapper">
		 						<div class="wpad_input_wrap">
			 						<label>
				 						<input type="checkbox" name="notification_approved_comment" value="enable">
				 						<?php _e('Send meg en epost n&aring;r signaturen er godkjent.','wpad'); ?>
				 					</label>
				 				</div>
		 					</div>

		 				</div>

	 					<?php 
	 				}

	 			}

	 		}

	 	}

 	}

 	/*
	** Check if the custom layout has been defined for the comment form.
 	*/

	function form_custom_layout( $layout , $data , $id ){

		if( !empty($data[$id]) ){

			if( empty( $layout ) ){

				if ( array_key_exists( "custom_layout" , $data[$id]['other'] ) && !empty( $data[$id]['other']['custom_layout'] ) ){

					return $layout = $data[$id]['other']['custom_layout'];

				} else {

					return $layout;

				}
				
			} else {

				return $layout;

			}

		} else {

			return $layout;

		}

	}

	function textarea_on_report_comment(){ 

		$disable = get_option('disable_textarea_report_comment');

		if( !empty($disable) && $disable == 'disable' ){
			return;
		} ?>

		<div>
			<h5>Your Message</h5>
			<textarea name="text_comment_report" class="input-control" rows="5" cols="20"></textarea>
			<div class="wpad_error"></div>
		</div>

		<?php
	}

	function report_thank_you(){ 

		$args = array();
		$args['title'] = '';
		$args['content'] = __( 'Your report has been successfully sent. We will look into it.' , 'wpad' ); 

		$args = apply_filters( 'wpad_report_thank_you', $args ); ?>

		<div id="wpad_thank_you_report_comment" title="<?php echo $args['title']; ?>" style="display:none">
			<p><?php echo $args['content']; ?></p>
		</div>

		<?php
	}

	function repost_comment_section(){ ?>
		
		<div id="wpad_report_comment" title="Report This Comment" style="display:none">

			<form class="wpad_report_comment_form">
	  		
		  		<?php 
		  		$report_comment_options = __( "Spam or scam,Violence or harmful behaviour,Sexually explicit content,I don't like this comment,This comment is harassing or bullying me" , 'wpad' );
		    	$db_report_option = get_option('report_comment_options');
		    	$report_options = !empty( $db_report_option ) ? $db_report_option : $report_comment_options;

		    	echo '<div class="wpad_report_option">';
		    	foreach( explode( ',' , $report_options ) as $value){ ?>

		    		<label>
						<input type="radio" name="report_option" value="<?php echo $value; ?>">
						<span><?php echo $value; ?></span>
					</label>

		    		<?php
		    	}
		    	echo '<div class="wpad_error"></div></div>';

		    	$this->textarea_on_report_comment(); ?>			

				<div class="wpad_report_button">
					<button class="wpad_primary wpad_btn wpad_send_report" type="submit">
						<?php _e( 'Report' , 'wpad' ); ?>
					</button>
					<button class="wpad_default wpad_btn report_dismiss">
						<?php _e( 'Cancel' , 'wpad' ); ?>
					</button>
					<img src="<?php echo admin_url() . 'images/spinner.gif'; ?>" style="display:none">
				</div>

			</form>

		</div>
		
		<?php
	}

 	/*
	** The comment form shortcode
	*/

 	function comment_form( $atts ){ 

 		extract(
 			shortcode_atts(
 				array(
       				'id' => '',
       				'layout' => ''
    			), $atts
    		)
    	);

 		$data = get_option( 'wpad_comment_form' );

 		$layout = $this->form_custom_layout( $layout , $data , $id );
 		
 		$guest_comment = $this->check_guest_privalage( $data , $atts );

 		$content = '';

 		if( empty($data) || !is_numeric($id) ){
 			return;
 		}

 		foreach( $data as $key => $value ):

 			if( $key == $id ): 

 				ob_start(); 

 				// *************************************************
				// If Unpublished is selected then dont show the form
				// *************************************************

 				$this->one_column_design( $data[$id]['other']['no_of_column'] );

 				if( $data[$id]['other']['comment_status'] == 'unpublished' ){
 					return;
 				} 

 				if( $data[$id]['other']['comment_status'] == 'trash' ){
 					if( current_user_can( 'administrator' ) ){
 						$wpad_remove_default_comment_tag = new wpad_remove_default_comment_tag();
						
						$wpad_remove_default_comment_tag->warning_css();
 						echo '<div class="wpad_notice">The comment form [wpad-comment-form id="' . $id . '"] is in trash so cannot be displayed here. If you want to show the comment form here please untrash it. This message was generated by the WP Advanced Comment. This message is shown to the administrator only.</div>';
					}
					
					return;
 				}
 				$this->repost_comment_section();
 				$this->report_thank_you();
 				?>
 				<form id="wp_advance_comment_form" method="post" class="wpad_form_horizontal">
 					
 					<span name="wpad_layout" style="display:none;">
 						<?php echo base64_encode( $layout ); ?>
 					</span>

 					<input type="hidden" name="wpad_form_id_list" value="<?php echo $key; ?>">
 					
 					<div class="wpad_list_comments_frontend_wrapper">
 						<?php 
						echo $this->show_comment_lists( $id , $layout ); 
						?>
 					</div>

 					<?php

 					/*
					** If user is not logged in and Guest comment is enable
					*/

 					if( $guest_comment == true && !is_user_logged_in() ){
 					  	$this->display_form_user( $data , $id , $atts );
 					} elseif( is_user_logged_in() ) {

 						/*
						** If user is logged in
						*/

 						$this->display_form_user( $data , $id , $atts );
 					} 
 					 ?>
 				</form>
 				<?php 
 				
 				$required_rules = $this->jquery_validate_rules( $data[$id] ); 

 				$required_messages = $this->jquery_validate_messages( $data[$id] ); 
 				wp_reset_postdata(); wp_reset_query();
 				global $post;
 				?>
 				<script>

	 				function get_user_image_file( selected , key ){

	 					var id = selected.find('input').attr( 'id' );
	 					var file = document.getElementById( id );
						var file_val = file.files[0];

						datas.append( key + "[]" , file_val);

	 				}

 					jQuery( document ).ready( function(){	

 						window.wpad_rules = <?php echo $required_rules; ?>;
 						window.wpad_message = <?php echo $required_messages; ?>;

 						jQuery("#wp_advance_comment_form").validate({

 							errorElement: "div",
 							errorClass : 'wpad_error_fields',

 							errorPlacement: function(error, element) {
						 		error.appendTo( element.closest("div").next(".wpad_error").show() )
					   		},

 					   		unhighlight: function(element) {
							    jQuery(element).removeClass('wpad_error_fields');
							    jQuery(element).closest('div').next('.wpad_error').hide();
							},

 							highlight: function(element) {
								jQuery(element).addClass('wpad_error_fields');
							    jQuery(element).closest('div').next('.wpad_error').show();
							},

 					   		invalidHandler: function(form, validator) {
 						        if (!validator.numberOfInvalids())
 						             return;
 						        jQuery('html, body').animate({
 						            scrollTop: jQuery(validator.errorList[0].element).offset().top-50
 						        }, 1000);
 						    },

 							rules : wpad_rules,
 							messages : wpad_message,

 							submitHandler: function(form) {
 								var meta_key = '';
 								datas = new FormData();
 								datas.append( 'action', 'wpad_save_comment' );
 								datas.append( 'post_id', "<?php echo $post->ID; ?>" );
 								datas.append( 'form_id', jQuery('input[name=wpad_comment_form_id]').val() );
 								datas.append( 'email_me_on_approve' , jQuery('input[name=notification_approved_comment]:checked').val() )
 								jQuery('#wp_advance_comment_form .wpad_form_group').each( function(i){
 									meta_key = jQuery(this).attr('ids');

 									switch( meta_key ){

 										case 'wpad_user_name':
 											datas.append( 'user_name[meta_value]', jQuery(this).find( 'input' ).val() );
 											datas.append( 'user_name[meta_key]', jQuery(this).find( 'input' ).attr('key') );
 											break;

 										case 'wpad_user_email':
 											datas.append( 'user_email[meta_value]', jQuery(this).find( 'input' ).val() );
 											datas.append( 'user_email[meta_key]', jQuery(this).find( 'input' ).attr('key') );
 											break;

 										case 'wpad_comment':
 											datas.append( 'comment[meta_value]' , jQuery(this).find( 'textarea' ).val() );
 											datas.append( 'comment[meta_key]', 'comment' );
 											break;

 										case 'wpad_text_element':
 											datas.append( 'text[meta_value][' + i + ']' , jQuery(this).find( 'input' ).val() );
 											datas.append( 'text[meta_key][' + i + ']' , jQuery(this).find( 'input' ).attr('key') );
 											break;

 										case 'wpad_textarea_element':
 											datas.append( 'textarea[meta_value][' + i + ']' , jQuery(this).find( 'textarea' ).val() );
 											datas.append( 'textarea[meta_key][' + i + ']' , jQuery(this).find( 'textarea' ).attr('key') );
 											break;

 										case 'wpad_radio_element':
 											datas.append( 'radio[meta_value][' + i + ']' , jQuery(this).find( 'input[type=radio]:checked' ).val() );
 											datas.append( 'radio[meta_key][' + i + ']' , jQuery(this).find( 'input[type=radio]' ).attr('key') );
 											break;

 										case 'wpad_checkbox_element':
 											var selected = [];
 											jQuery(this).find( 'input[type=checkbox]:checked' ).each(function() {
 											    selected.push( jQuery(this).val() );
 											});
 											datas.append( 'checkbox[meta_value][' + i + ']' , selected );
 											datas.append( 'checkbox[meta_key][' + i + ']' , jQuery(this).find( 'input[type=checkbox]:checked' ).attr('key') );
 											break;

 										case 'wpad_select_element':
 											datas.append( 'select[meta_value][' + i + ']' , jQuery(this).find( 'select' ).val() );
 											datas.append( 'select[meta_key][' + i + ']' , jQuery(this).find( 'select' ).attr('key') );
 											break;

 										case 'wpad_email_element':
 											datas.append( 'email[meta_value][' + i + ']' , jQuery(this).find( 'input' ).val() );
 											datas.append( 'email[meta_key][' + i + ']' , jQuery(this).find( 'input' ).attr('key') );
 											break;

 										case 'wpad_url_element':
 											datas.append( 'url[meta_value][' + i + ']' , jQuery(this).find( 'input' ).val() );
 											datas.append( 'url[meta_key][' + i + ']' , jQuery(this).find( 'input' ).attr('key') );
 											break;

 										case 'wpad_multi_select_element':
 											datas.append( 'multiselect[meta_value][' + i + ']' , jQuery(this).find( 'select' ).val() );
 											datas.append( 'multiselect[meta_key][' + i + ']' , jQuery(this).find( 'select' ).attr('key') );
 											break;

 										case 'wpad_user_image_element':
 											get_user_image_file( jQuery(this) , jQuery(this).find( 'input' ).attr('key') );
 											datas.append( 'user_image[meta_key][' + i + ']' , jQuery(this).find( 'input' ).attr('key') );
 											break;

 										default:
 											break;

 									}

 								});

 								jQuery.ajax({
 									url : "<?php echo admin_url('admin-ajax.php'); ?>",
 									type : 'POST',
 									data : datas,
 									processData: false,
                          			contentType: false,
 									beforeSend : function(){
 										jQuery('.wpad_submit_loader').show();
 									},
 									success : function(){
 										jQuery('.wpad_submit_loader').hide();	
 										jQuery('#wp_advance_comment_form')[0].reset();

 										// Reset preview image
 										jQuery('.wpad_image_preview').attr( 'src' , '' ).hide();

 										jQuery('.wpad_success_comment').show();
 										jQuery('html, body').animate({
 								            scrollTop: jQuery('.wpad_success_comment').offset().top-50
 								        }, 'fast' );
 									}
 								});
 							}
 						});
 					});
 				</script>
 				<?php 
 				$content .= ob_get_clean();
 			endif;
 		endforeach;
 		return $content;
 	}	

 	/*
	** Get the user custom message for the errors
 	*/

 	function jquery_validate_messages( $data ){
 		$messages = array();

 		foreach( $data as $key => $value ){

 			if( (isset( $value['required'] ) && $value['required'] == 'yes') || !empty($value['error_message']) || !empty( $value['more_validation'] ) ){
 				$input_name = $value['input_name'];
 				$parameters = array();

 				if( (isset( $value['error_message'] ) && !empty( $value['error_message'] )) || !empty( $value['more_validation'] ) ){
 					$parameters['required'] = !empty($value['error_message']) ? $value['error_message'] : __( 'This field is required' , 'wpad' );
 					$messages[$input_name] = $parameters;	
 				}

 				// If user image is selected
 				if( $value['custom_field'] == 'user_image' ){
 					$messages[$input_name]['accept'] = __( 'Only JPEG / PNG / GIF / BMP files allowed' , 'wpad' ); 
 				}	

 			}

 			if( !empty( $value['more_validation'] ) ){
 				$messages = $this->more_validation_messages( $value['more_validation'] , $messages , $value['input_name']  );	
 			}

 		}

 		return json_encode( $messages );
 	}

 	/*
	** IF the another validation plugin is installed then add more rules the the fields
 	*/

 	function more_validation( $condition , $rules , $input_name ){
 		foreach( $condition as $value ){
 			if( !empty( $value['key'] ) ){
 				if( $value['key'] == 'addmethod' ){
 					$rules[$input_name][$value['value']] = true;	
 				} else {
 					$method = array('number','digits','creditCard');
 					if( in_array( $value['key'] , $method ) ){
 						$data = true;
 					} else {
 						$data = isset($value['value']) ? $value['value'] : '';
 					}
 					$rules[$input_name][$value['key']] = $data;	
 				}
 			}
 		}
 		return $rules;
	}

	/*
	** IF the another validation plugin is installed then add more messages
 	*/

 	function more_validation_messages( $condition , $messages , $input_name ){
 		$input = explode( '_' , $input_name);
 		foreach( $messages as $key => $value ){
 			$key_name = explode( '_' , $key);
 			if( $key_name[0] == 'checkbox' ){
 				foreach( $condition as $con ){
 					if( $con['key'] == 'minlength' ){
 						$messages[$key]['minlength'] =  __( 'Please select at least ' , 'wpad' ) . $con['value'] . __( ' items.' , 'wpad' );
 					} elseif( $con['key'] == 'maxlength' ){
 						$messages[$key]['maxlength'] = __( 'Please select no more than ' , 'wpad' ) . $con['value'] . __( ' items.' , 'wpad' );
 					}
 				}
 			}
 		}
 		return $messages;
 	}

 	/*
	** This function will be used for the jquery validation rules
 	*/

 	function jquery_validate_rules( $data ){

 		$rules = array();
 		foreach( $data as $key => $value ){

 			if( isset( $value['required'] ) && $value['required'] == 'yes' ){
 				$parameters = array();

 				if( $value['custom_field'] == 'multi_select' ){
 					$parameters['multi_select'] = true; 
 				} else {
 					$parameters['required'] = true; 
 				}

 				$input_name = $value['input_name'];
 				$rules[$input_name] = $parameters;
 			}

 			/*
			** If user image is selected
			*/
			
			if( !empty($value['custom_field']) && $value['custom_field'] == 'user_image' ){
				$input_name = $value['input_name'];
				$parameters['accept'] = 'image/jpg,image/jpeg,image/png,image/gif,image/bmp'; 
				$rules[$input_name] = $parameters;
			}

 			if( !empty( $value['more_validation'] ) ){
 				$rules = $this->more_validation( $value['more_validation'] , $rules , $value['input_name']  );	
 			}

 		}

 		return json_encode( $rules );

 	}
}

 $comment_form = new wpad_frontend_comment_form();