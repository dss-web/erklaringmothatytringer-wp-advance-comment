<?php 
/**
* Edit Comment Form
*/
class wpad_edit_comment_form{

	function __construct(){

		if( ( isset( $_GET['page'] ) && $_GET['page'] == 'wpad_comment_form_edit' ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ){
			add_action( 'admin_menu', array( $this, 'edit_comment_form_menu') );
			add_action( 'wp_ajax_wpad_comment_element' , array( $this , 'wpad_comment_element' ) );
			add_action( 'wp_ajax_save_comment_form' , array( $this , 'save_comment_form' ) );
			add_filter( 'tiny_mce_before_init', array( $this , 'wpse24113_tiny_mce_before_init' ) );
		}

		if( !(defined( 'DOING_AJAX' ) && DOING_AJAX) ){
			if( isset( $_GET['page'] ) && $_GET['page'] == 'wpad_comment_form_edit' ){
				$this->custom_title_page();
				add_action( 'admin_init', array( $this , 'wpad_custom_menu_class' ) );
			}
		}

	}

	function get_the_wp_editor( $content , $editor_id ){

		$settings = array(
			'textarea_name' => $editor_id,
			'editor_height' => '250px',
			'wpautop' => false,
			'tinymce' => array( 
	            'content_css' =>  WPAD_PLUGIN_DIR . 'css/wp_editor.css',
	        )
		);
		$settings = apply_filters( 'wpad_backend_editors' , $settings );
		wp_editor( $content, $editor_id , $settings );

	}

	function wpse24113_tiny_mce_before_init( $initArray ) {

		if( isset( $_GET['page'] ) && $_GET['page'] == 'wpad_comment_form_edit' ){ 

			$str = "'" . ltrim ($initArray['selector'], '#') . "'";
			$selector = '"' . $initArray['selector'] . '"';

			$initArray['setup'] = "function(ed){
		     	ed.on('keyup', function(type,e){
			        var content = tinyMCE.get($str).getContent();
			        jQuery($selector).val(content);
			        return;

			    });
				
				ed.on('NodeChange', function(type,e){
			        var content = tinyMCE.get($str).getContent();
			        jQuery($selector).val(content);
			        return;
			    });
		    }";

		}

	    return $initArray;
	}

	function get_form_title(){
		$data = get_option( 'wpad_comment_form' );
		$comment_id = $_GET['comment_id'];
		if( isset($data[$comment_id]) && is_array( $data[$comment_id] ) && !empty( $data[$comment_id] ) ){
			echo '<h2>' ; _e( 'Edit Comment Form' , 'wpad' ); 
			$data = new wpad_comment_form_list();
			$new_id = $data->new_comment_ids(); ?>
			<a class="page-title-action" href="<?php echo site_url();?>/wp-admin/admin.php?page=wpad_comment_form_edit&comment_id=<?php echo $new_id; ?>">Add New</a>
			<?php
			
			echo '</h2>';
			
		} else {
			echo '<h2>' ; _e( 'Add New Comment Form' , 'wpad' ); echo '</h2>';
		}
	}
	function custom_title_page(){
		echo '<title>' . __( "Edit Form" , 'wpad' ) . '</title>';
	}
	function check_previlage(){
		if( !isset( $_GET['comment_id'] )  || $_GET['comment_id'] > 1000  ){
			echo '<div class="wpad_access_error"><p>Oops !!! Something Went Wrong. <a href="' . admin_url('admin.php?page=wp_advance_comment') . '">Click Here</a> to go back.</p></div>';
			die;
		}
	}
	function wpad_custom_menu_class(){
		global $menu;
	
		if( ( isset( $_GET['page'] ) && $_GET['page'] == 'wpad_comment_form_edit' ) ){
			foreach( $menu as $key => $value ){
		        if( 'WP Advanced Comment' == $value[3] ){
			        $menu[$key][4] .= " wp-has-current-submenu";
			    }
	    
		    }
		}
	}
	/*
	** Get the Comment title
	*/
	function get_comment_title( $id ){
		$data = get_option( 'wpad_comment_form' );
		if( !empty($data) ){
		foreach( $data as $key => $value ){
				if( $key == $id ){
				
					return $data[$id]['other']['comment_title'];
				}
			}	
			
		}	
	}
	function text_validation_method( $key , $value1 , $custom_field ){
		if( class_exists('wpad_comment_validation') ){
			$validate = new wpad_comment_validation();
			$validate->text_validation( $key , $value1 , $custom_field );
		}
	}
	function user_name_html( $key, $value1 = null ){ ?>
		<!-- User Name -->
		<li class="post_title">
			<input type="hidden" name="input_user_name" value="user_name_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="user_name">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php _e( "Title :" , 'wpad' ); ?> <strong><?php _e( "Name" , 'wpad' ); ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required :" , 'wpad' ); ?></label>	            
		            <?php 
		            if( !empty($value1['required']) ){ 
		            	if( $value1['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>0" <?php checked( $checked, 'yes' ); ?>> <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>0" <?php checked( $checked, 'no' ); ?>> <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	<?php 
		        	$label = !empty( $value1['label'] ) ? $value1['label'] : 'Name'; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $value1['help_text'] ) ? $value1['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $value1['css_name'] ) ? $value1['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          PlaceHolder Text 
		        -->
		        <div class="wpuf-form-rows wpad_placeholder">
		        	<?php 
		        	$placeholder = !empty( $value1['placeholder_text'] ) ? $value1['placeholder_text'] : ''; 
		        	?>
		            <label><?php _e( "PlaceHolder Text" , 'wpad' ); ?>
		            	<a tool_tip="Text for HTML5 placeholder attribute." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="placeholder_<?php echo $key; ?>0" value="<?php echo $placeholder; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $value1['error_message'] ) ? $value1['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <!-- 
		          Size
		        -->
		        <div class="wpuf-form-rows wpad_size">
		        	<?php 
		        	$size = !empty( $value1['size'] ) ? $value1['size'] : '40'; 
		        	?>
		            <label><?php _e( "Size" , 'wpad' ); ?>
		            	<a tool_tip="Size of this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="size_<?php echo $key; ?>" value="<?php echo $size; ?>">
		        </div>
		        <?php $this->text_validation_method( $key , $value1 , 'user_name' ); ?>
			</div>
		</li>
		<?php 
	}
	function email_html( $key, $value1 = null ){ ?>
		<!-- User Email -->
		
		<li class="post_title">
			<input type="hidden" name="input_user_email" value="user_email_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="user_email">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php _e( "Title :" , 'wpad' ); ?> <strong><?php _e( "Email" , 'wpad' ); ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($value1['required']) ){ 
		            	if( $value1['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>1" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>1" <?php checked( $checked, 'no' ); ?>> <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	<?php 
		        	$label = !empty( $value1['label'] ) ? $value1['label'] : 'Email'; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		          	Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $value1['help_text'] ) ? $value1['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          	CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $value1['css_name'] ) ? $value1['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          	PlaceHolder Text 
		        -->
		        <div class="wpuf-form-rows wpad_placeholder">
		        	<?php 
		        	$placeholder = !empty( $value1['placeholder_text'] ) ? $value1['placeholder_text'] : ''; 
		        	?>
		            <label><?php _e( "PlaceHolder Text" , 'wpad' ); ?>
		            	<a tool_tip="Text for HTML5 placeholder attribute." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="placeholder_<?php echo $key; ?>" value="<?php echo $placeholder; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $value1['error_message'] ) ? $value1['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <!-- 
		          Size
		        -->
		        <div class="wpuf-form-rows wpad_size">
		        	<?php 
		        	$size = !empty( $value1['size'] ) ? $value1['size'] : '40'; 
		        	?>
		            <label><?php _e( "Size" , 'wpad' ); ?>
		            	<a tool_tip="Size of this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="size_<?php echo $key; ?>" value="<?php echo $size; ?>">
		        </div>
		        <?php $this->text_validation_method( $key , $value1 , 'email'); ?>
			</div>
		</li>
		<?php 
	}
	function comment_section_html( $key, $value1 = null ){ ?>
		<!-- Comment Text Area -->
    	<li class="post_title">
    		<input type="hidden" name="input_comment" value="comment_<?php echo $key; ?>3">
    		
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="comment_area">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php _e( "Title :" , 'wpad' ); ?> <strong><?php _e( "Comment Field" , 'wpad' ); ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- 
					Title 
				-->
				
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($value1['required']) ){ 
		            	if( $value1['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?>> <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?>> <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	<?php 
		        	$label = !empty( $value1['label'] ) ? $value1['label'] : 'Comment'; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		          	Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $value1['help_text'] ) ? $value1['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          	CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
					<?php 
		        	$css_name = !empty( $value1['css_name'] ) ? $value1['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          PlaceHolder Text 
		        -->
		        <div class="wpuf-form-rows wpad_placeholder">
		        	<?php 
		        	$placeholder = !empty( $value1['placeholder_text'] ) ? $value1['placeholder_text'] : ''; 
		        	?>
		            <label><?php _e( "PlaceHolder Text" , 'wpad' ); ?>
		            	<a tool_tip="Text for HTML5 placeholder attribute." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="placeholder_<?php echo $key; ?>" value="<?php echo $placeholder; ?>">
		        </div>
		        <!-- 
		          Default Value
		        -->
		        <div class="wpuf-form-rows wpad_default_value">
		        	<?php 
		        	$default_value = !empty( $value1['default_value'] ) ? $value1['default_value'] : ''; 
		        	?>
		            <label><?php _e( "Default Value" , 'wpad' ); ?>
		            	<a tool_tip="Default value for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="default_value_<?php echo $key; ?>" value="<?php echo $default_value; ?>">
		        </div>
		        <!-- 
		          Rows
		        -->
		        <div class="wpuf-form-rows wpad_rows">
		        	<?php 
		        	$rows = !empty( $value1['rows'] ) ? $value1['rows'] : '5'; 
		        	?>
		            <label><?php _e( "Rows" , 'wpad' ); ?>
		            	<a tool_tip="Number of rows in textarea." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="rows_<?php echo $key; ?>" value="<?php echo $rows; ?>">
		        </div>
		        <!-- 
		          	Columns
		        -->
		        <div class="wpuf-form-rows wpad_columns">
		        	<?php 
		        	$columns = !empty( $value1['column'] ) ? $value1['column'] : '25'; 
		        	?>
		            <label><?php _e( "Columns" , 'wpad' ); ?>
		            	<a tool_tip="Number of columns in textarea." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="columns_<?php echo $key; ?>" value="<?php echo $columns; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $value1['error_message'] ) ? $value1['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <!-- 
		          Choose Editor
		        -->
		        <div class="wpuf-form-rows wpad_editor">
		        	<label><?php _e( "Editor" , 'wpad' ); ?>
		            	<a tool_tip="Choose an Editor." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<div class="wpuf-form-sub-fields">
		                <label><input checked="" type="radio" value="textarea" name="comment_editor"> Textarea </label>
		                <label><input disabled="" type="radio" value="wp_editor" name="comment_editor"><span class="required"> <?php _e( "WP Editor ( Only For Premium Members )" , 'wpad' ); ?></span></label>
		            </div>
		        </div>
		        <?php $this->text_validation_method( $key , $value1 , 'comment' ); ?>
			</div>
		</li>
		<?php
	}
	/*
	 If Comment section not saved on the database then show on the top section
	*/
	function comment_section( $id  ){
		$key_pass = wp_generate_password( 25, false, false );
		$data = get_option( 'wpad_comment_form' );
		$flag = 0;
		if( !empty($data) ):
			foreach( $data as $key => $value ){
				if( $key == $id ){
					
					foreach( $data[$id] as $key1 => $value1 ){
						if( empty( $value1['custom_field'] ) && is_array($value1) && ( $key1 != 'other' ) ){ 
							$this->user_name_html( $key , $value1 );
							$this->email_html( $key , $value1 );
							$this->comment_section_html( $key , $value1 );
						}
						if( $flag == 0 ){
							$flag = 1;	
						}
					}
				}
			}
		else:
			$this->display_default_forms( $key_pass );
			$flag = 1;
		endif;
		
		if( $flag == 0 ){
			$this->display_default_forms( $key_pass );
		}
	}
	function display_default_forms( $key ){
		$this->user_name_html( $key );
		$this->email_html( $key );
		$this->comment_section_html( $key );
	}
	/*
	 This section will display the saved form elements on the database on to the backend comment form
	*/
	function backend_display_form_elements( $id ){
		$data = get_option( 'wpad_comment_form' );
		if(!empty($data) ){
			foreach( $data as $key => $value ){
				if( $key == $id ){
			
					foreach( $data[$id] as $key1 => $value1 ){
						$key = wp_generate_password( 25, false, false );
						if( isset( $value1['custom_field'] ) ){
							switch ( $value1['custom_field'] ) {
								case 'text':
									$this->text( $key , 'Text' , 'text' , $value1);
									break;
								case 'user_name':
									$this->user_name_html( $key , $value1 );
									break;
								case 'user_email':
									$this->email_html( $key , $value1 );
									break;
								case 'comment_area':
									$this->comment_section_html( $key , $value1 );
									break;
								case 'textarea':
									$this->textarea( $key , 'Textarea' , 'textarea' , $value1 );
									break;
								case 'radio':
									$this->radio( $key , 'Radio' , 'radio' , $value1 );
									break;
								case 'checkbox':
									$this->checkbox( $key , 'Checkbox' , 'checkbox' , $value1 );
									break;
								case 'select':
									$this->select( $key , 'Select' , 'select' , $value1 );
									break;
								case 'email':
									$this->email( $key , 'Email' , 'email' , $value1 );
									break;
								case 'url':
									$this->url( $key , 'Url' , 'url' , $value1 );
									break;
								case 'multi_select':
									$this->multi_select( $key , 'Multiple Select' , 'multi_select' , $value1 );
									break;

								case 'section_break':
									$this->section_break( $key , 'Section Break' , 'section_break' , $value1 );
									break;

								case 'html':
									$this->html( $key , 'HTML' , 'html' , $value1 );
									break;

								case 'user_image':
									$this->user_image( $key , 'User Image' , 'user_image' , $value1 );
									break;
							
								default:
									# code...
									break;
							}
						}
					}
				}
			}
		}	
	}
	function edit_comment_form_menu(){
		add_submenu_page( null, 'WP Advanced Comment Forms Edit', 'Edit Comment Form', 'manage_options', 'wpad_comment_form_edit', array($this, 'edit_comment_form'));
	}
	function remove_duplicate_show_on_all( $comment_id , $data ){
		foreach( $data as $key => $value ){
			if( $comment_id != $key ){
				if( !empty($data[$key]) ){
					unset( $data[$key] );
				}
			}
			
		}
		return $data;
	}
	function get_enable_certain_pages_post_ids( $data ){
		$new_arr = array();
		foreach( $data as $key => $value ){
			$new_arr[] = $key;
		}
		return $new_arr;
	}
	function remove_post_ids( $comment_id , $data ){
		if( empty($data[$comment_id]['enable_certain_pages']['post_ids']) ){
			return $data;
		}
		$result = $this->get_enable_certain_pages_post_ids( $data[$comment_id]['enable_certain_pages']['post_ids'] );
		foreach( $data as $key => $value ){
			if( $comment_id != $key ){
				if( !empty($data[$key]['enable_certain_pages']['post_ids']) ){
					
					foreach( $data[$key]['enable_certain_pages']['post_ids'] as $key1 => $value1 ){
						if( in_array( $key1 , $result ) ){
							unset( $data[$key]['enable_certain_pages']['post_ids'][$key1] );
						}
					}
				}
			}
			
		}
		return $data;
	}
	function get_enable_certain_pages_all_posts( $data ){
		$new_arr = array();
		foreach( $data as $key => $value ){
			$new_arr[] = $value;
		}
		return $new_arr;
	}
	function remove_all_posts( $comment_id , $data ){
		if( empty($data[$comment_id]['enable_certain_pages']['all_posts']) ){
			return $data;
		}
		$result = $this->get_enable_certain_pages_all_posts( $data[$comment_id]['enable_certain_pages']['all_posts'] );
		foreach( $data as $key => $value ){
			if( $comment_id != $key ){
				if( !empty($data[$key]['enable_certain_pages']['all_posts']) ){
					
					foreach( $data[$key]['enable_certain_pages']['all_posts'] as $key1 => $value1 ){
						if( in_array( $value1 , $result ) ){
							unset( $data[$key]['enable_certain_pages']['all_posts'][$key1] );
						}
					}
				}
			}
			
		}
		return $data;
	}
	function default_comment_exist_warning(){ 
		$data = get_option( 'wpad_comment_forms_on_posts' ); 
		$comment_id = $_GET['comment_id']; 
		$flag = false;
		if( !empty($data) ){ 
			foreach( $data as $key => $value ){ 
				if( !empty( $value['show_on_all'] ) ){ 
					$flag = true;
				} 
			}
		}
		if( $flag == true && !empty( $comment_id ) ){
			foreach( $data as $key => $value ){ 

				// Show the mesasge to the other comment forms
	
				if( !empty($value['show_on_all']) && $value['show_on_all'] != $comment_id ){
					echo '<p class="description">( There is already a wordpress default form saved on the database. If you select this then it will override previous datas and save this as the new default commment form. )</p>';
				}
			}
		}
	}
	/*
	** Save Extras to the database
	*/
	function comment_forms_on_posts( $extras , $key ){
	
		$comment_forms_on_posts = get_option( 'wpad_comment_forms_on_posts' );
		if( !empty($extras['show_on_all']) ){
			if( empty($comment_forms_on_posts) ){
				$data = array(
					$key => array(
						'show_on_all' => $extras['show_on_all']
					)
				);
				update_option( 'wpad_comment_forms_on_posts' , $data );
			} else {
				unset($comment_forms_on_posts[$key]);
				$comment_forms_on_posts[$key]['show_on_all'] = $extras['show_on_all'];
				$results1 = $this->remove_duplicate_show_on_all( $key , $comment_forms_on_posts );
				update_option( 'wpad_comment_forms_on_posts' , $results1 );
			}
		} elseif( !empty($extras['enable_certain_pages']) ){
			$newArray = call_user_func_array( "array_merge" , $extras['enable_certain_pages']);
			if( empty($comment_forms_on_posts) ){
				
				$data = array(
					$key => array(
						'enable_certain_pages' => $newArray
					)
				);
				update_option( 'wpad_comment_forms_on_posts' , $data );
			} else {
				unset($comment_forms_on_posts[$key]);
				$comment_forms_on_posts[$key]['enable_certain_pages'] = $newArray;
				$result2 = $this->remove_post_ids( $key , $comment_forms_on_posts );
				$result3 = $this->remove_all_posts( $key , $result2 );
				update_option( 'wpad_comment_forms_on_posts' , $result3 );
			}
		} else {
			if( !empty( $comment_forms_on_posts ) ){
				unset( $comment_forms_on_posts[$key] );
			}
			$comment_forms_on_posts[$key] = 'none';
			update_option( 'wpad_comment_forms_on_posts' , $comment_forms_on_posts );
		}
		$comment_forms_on_posts = get_option( 'wpad_comment_forms_on_posts' );

	}
	function stripslashes_deep($value) {
	    $value = is_array($value) ?
	                array_map('stripslashes_deep', $value) :
	                stripslashes($value);
	    return $value;
	}
	/*
	** Save the datas onto the database
	*/
	function save_comment_form(){
		$key = $_POST['id'];
		// Extras will be saved on another meta key
		$this->comment_forms_on_posts( $_POST['form']['extras'] , $key );
		unset( $_POST['form']['extras'] );
		
		$data = array();

		$data[$key] = $this->stripslashes_deep( $_POST['form'] );

		$db_value = get_option( 'wpad_comment_form' );
		if( !empty($db_value) ){
			unset( $db_value[$key] );
		}
		if( empty($db_value) ){
			update_option( 'wpad_comment_form', $data );
		} else {
			$new_arr = array_replace( $data , $db_value );
			update_option( 'wpad_comment_form', $new_arr );
		}
		die;
	}
	/*
	** Get HTML of the Edit Form
	*/
	function edit_comment_form(){ 
		include 'inc/edit-form-html.php'; 
	}
	function section_break( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title wpad_section_break">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)"><?php _e( "Remove" , 'wpad' ); ?></a>
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
	        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		   
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this section." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this section." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
			</div>
		</li>
		<?php
	}
	function html( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)"><?php _e( "Remove" , 'wpad' ); ?></a>
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
	        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		   
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		         	HTML
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "HTML Codes" , 'wpad' ); ?>
		            	<a tool_tip="Paste Your Html Codes Here" class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>" rows="10"><?php echo $help_text; ?></textarea>
		        </div>
			</div>
		</li>
		<?php
	}

	/*
	** Get the Custom field User Image
	*/

	function user_image( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
			<input type="hidden" name="input_user_image" value="user_image_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)"><?php _e( "Remove" , 'wpad' ); ?></a>
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">

				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($data['required']) ){ 
		            	if( $data['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>

		        <!-- 
		           Meta Key 
		        -->
		        <div class="wpuf-form-rows wpad_meta_key">
		        	
		        	<?php 
		        	$meta_key = !empty( $data['meta_key'] ) ? $data['meta_key'] : ''; 
		        	?>
		            <label>
		            <?php _e( "Meta Key" , 'wpad' ); ?> <span class="required">*</span>
		            	<a tool_tip="Name of the meta key this field will save to." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="metakey_<?php echo $key; ?>" value="<?php echo $meta_key; ?>">
		        </div>

		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>

		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $data['css_name'] ) ? $data['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>

		        <!-- 
		         	Error Message
		        --> 
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $data['error_message'] ) ? $data['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		   
		        <!-- 
		         	Show To
		        -->
		        <br>
		        <div class="wpuf-form-rows wpad_show_to">
		        	
		        	<?php 
		        	$show_to = !empty( $data['show_to'] ) ? $data['show_to'] : 'guest'; 
		        	?>
		        	<label>
		        		<?php _e( "Enable For" , 'wpad' ); ?>
		        	</label>

		        	<div class="wpad_fields_wrapper">

			        	<label>
			        		<input type="radio" name="wpad_show_to" value="guest" <?php checked( $show_to , 'guest' );?>>
			        		Guests
			        	</label>
			        	<br>
			        	<label>
			        		<input type="radio" name="wpad_show_to" value="logged_in" <?php checked( $show_to , 'logged_in' );?>>
			        		Logged In Users
			        	</label>
			        	<br>
			        	<label>
			        		<input type="radio" name="wpad_show_to" value="both" <?php checked( $show_to , 'both' );?>>
			        		Both
			        	</label>
			        	<br>
			        	<p class="description">
			        		Note : The user image uploaded from this field will be shown on the comment list only. If the logged in user already has an avatar set then choose first option i.e. Guests. The comment list will show the avatar from <code>get_avatar()</code> function for the logged in user if the logged in user has set the avatar/profile picture.
			        	</p>

			        </div>

		        </div>

		        <!-- 
		         	Pewview Image
		        -->

		        <div class="wpuf-form-rows wpad_preview_image">
		        	<label>
		        		<?php _e( "Preview" , 'wpad' ); ?>
		        		<a class="wpad_tooltip" tool_tip="Show the user's selected image preview before uploading.">
		        			<span></span>
		        		</a>
		        	</label>
		        	<?php 
		            if( !empty($data['preview']) ){ 
		            	if( $data['preview'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="preview_user_image" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="preview_user_image" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>

		        <?php $this->text_validation_method( $key , $data , 'text' ); ?>

			</div>
		</li>
		<?php
	}

	/*
	** Get the Custom field Text
	*/
	function text( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
			<input type="hidden" name="input_text" value="text_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)"><?php _e( "Remove" , 'wpad' ); ?></a>
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($data['required']) ){ 
		            	if( $data['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		           Meta Key 
		        -->
		        <div class="wpuf-form-rows wpad_meta_key">
		        	
		        	<?php 
		        	$meta_key = !empty( $data['meta_key'] ) ? $data['meta_key'] : ''; 
		        	?>
		            <label>
		            <?php _e( "Meta Key" , 'wpad' ); ?> <span class="required">*</span>
		            	<a tool_tip="Name of the meta key this field will save to." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="metakey_<?php echo $key; ?>" value="<?php echo $meta_key; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $data['css_name'] ) ? $data['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          PlaceHolder Text 
		        -->
		        <div class="wpuf-form-rows wpad_placeholder">
		        	<?php 
		        	$placeholder = !empty( $data['placeholder_text'] ) ? $data['placeholder_text'] : ''; 
		        	?>
		            <label><?php _e( "PlaceHolder Text" , 'wpad' ); ?>
		            	<a tool_tip="Text for HTML5 placeholder attribute." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="placeholder_<?php echo $key; ?>" value="<?php echo $placeholder; ?>">
		        </div>
		        <!-- 
		          Default Value
		        -->
		        <div class="wpuf-form-rows wpad_default_value">
		        	<?php 
		        	$default_value = !empty( $data['default_value'] ) ? $data['default_value'] : ''; 
		        	?>
		            <label><?php _e( "Default Value" , 'wpad' ); ?>
		            	<a tool_tip="Default value for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="default_value_<?php echo $key; ?>" value="<?php echo $default_value; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $data['error_message'] ) ? $data['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <!-- 
		          Size
		        -->
		        <div class="wpuf-form-rows wpad_size">
		        	<?php 
		        	$size = !empty( $data['size'] ) ? $data['size'] : '40'; 
		        	?>
		            <label><?php _e( "Size" , 'wpad' ); ?>
		            	<a tool_tip="Size of this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="size_<?php echo $key; ?>" value="<?php echo $size; ?>">
		        
		        </div>
		        <!--  
					Show to users only
		        -->
		        <div class="wpuf-form-rows wpad_show_only_admin">
		        	
		        	<?php 
		        	if( isset($data['show_admin']) && $data['show_admin'] == 1 ){
		        		$show_admin = 1;
		        	} else {
		        		$show_admin = '';
		        	}?>
		        	<label>
		        		<?php _e( "Hide/Show" , 'wpad' ); ?>
		        		<a tool_tip="Tick if you want to show this field to admin only." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<label class="wpad_check">
		        		<input <?php checked( $show_admin , 1 ); ?> type="checkbox" name="show_admin_<?php echo $key; ?>" value="1"><?php _e( "Show to admin only" , 'wpad' ); ?>
		        	</label>
		        </div>
		        <?php $this->text_validation_method( $key , $data , 'text' ); ?>
			</div>
		</li>
		<?php
	}
	/*
	** Get the Custom field Textarea 
	*/
	function textarea( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
			<input type="hidden" name="input_textarea" value="textarea_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)"><?php _e( "Remove" , 'wpad' ); ?></a>
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($data['required']) ){ 
		            	if( $data['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		           Meta Key 
		        -->
		        <div class="wpuf-form-rows wpad_meta_key">
		        	
		        	<?php 
		        	$meta_key = !empty( $data['meta_key'] ) ? $data['meta_key'] : ''; 
		        	?>
		            <label>
		            <?php _e( "Meta Key" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Name of the meta key this field will save to." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="metakey_<?php echo $key; ?>" value="<?php echo $meta_key; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $data['css_name'] ) ? $data['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          PlaceHolder Text 
		        -->
		        <div class="wpuf-form-rows wpad_placeholder">
		        	<?php 
		        	$placeholder = !empty( $data['placeholder_text'] ) ? $data['placeholder_text'] : ''; 
		        	?>
		            <label><?php _e( "PlaceHolder Text" , 'wpad' ); ?>
		            	<a tool_tip="Text for HTML5 placeholder attribute." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="placeholder_<?php echo $key; ?>" value="<?php echo $placeholder; ?>">
		        </div>
		        <!-- 
		          Default Value
		        -->
		        <div class="wpuf-form-rows wpad_default_value">
		        	<?php 
		        	$default_value = !empty( $data['default_value'] ) ? $data['default_value'] : ''; 
		        	?>
		            <label><?php _e( "Default Value" , 'wpad' ); ?>
		            	<a tool_tip="Default value for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="default_value_<?php echo $key; ?>" value="<?php echo $default_value; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $data['error_message'] ) ? $data['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <!-- 
		          Rows
		        -->
		        <div class="wpuf-form-rows wpad_rows">
		        	<?php 
		        	$rows = !empty( $data['rows'] ) ? $data['rows'] : '5'; 
		        	?>
		            <label><?php _e( "Rows" , 'wpad' ); ?>
		            	<a tool_tip="Number of rows in textarea." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="rows_<?php echo $key; ?>" value="<?php echo $rows; ?>">
		        </div>
		        <!-- 
		          	Columns
		        -->
		        <div class="wpuf-form-rows wpad_columns">
		        	<?php 
		        	$columns = !empty( $data['column'] ) ? $data['column'] : '25'; 
		        	?>
		            <label><?php _e( "Columns" , 'wpad' ); ?>
		            	<a tool_tip="Number of columns in textarea." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="columns_<?php echo $key; ?>" value="<?php echo $columns; ?>">
		        </div>
		        <!-- 
		          Choose Editor
		        -->
		        <div class="wpuf-form-rows wpad_editor">
		        	<label><?php _e( "Editor" , 'wpad' ); ?>
		            	<a tool_tip="Choose an Editor." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<div class="wpuf-form-sub-fields">
		                <label><input checked="" type="radio" value="textarea" name="comment_editor_<?php echo $key; ?>"> Textarea </label>
		                <label><input disabled="" type="radio" value="wp_editor" name="comment_editor"><span class="required"> <?php _e( "WP Editor ( Only For Premium Members )" , 'wpad' ); ?> </span></label>
		            </div>
		        </div>
		        <!--  
					Show to users only
		        -->
		        <div class="wpuf-form-rows wpad_show_only_admin">
		        	
		        	<?php 
		        	if( isset($data['show_admin']) && $data['show_admin'] == 1 ){
		        		$show_admin = 1;
		        	} else {
		        		$show_admin = '';
		        	}?>
		        	<label>
		        		<?php _e( "Hide/Show" , 'wpad' ); ?>
		        		<a tool_tip="Tick if you want to show this field to admin only." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<label class="wpad_check">
		        		<input <?php checked( $show_admin , 1 ); ?> type="checkbox" name="show_admin_<?php echo $key; ?>" value="1"><?php _e( "Show to admin only" , 'wpad' ); ?>
		        	</label>
		        </div>
		        <?php $this->text_validation_method( $key , $data , 'textarea' ); ?>
			</div>
		</li>
		<?php 
	}
	/*
	** Get the Custom field Radio Button 
	*/
	function radio( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
			<input type="hidden" name="input_radio" value="radio_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)"><?php _e( "Remove" , 'wpad' ); ?></a>
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($data['required']) ){ 
		            	if( $data['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		           Meta Key 
		        -->
		        <div class="wpuf-form-rows wpad_meta_key">
		        	
		        	<?php 
		        	$meta_key = !empty( $data['meta_key'] ) ? $data['meta_key'] : ''; 
		        	?>
		            <label>
		            <?php _e( "Meta Key" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Name of the meta key this field will save to." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="metakey_<?php echo $key; ?>" value="<?php echo $meta_key; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $data['css_name'] ) ? $data['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $data['error_message'] ) ? $data['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        
		        <div class="wpuf-form-rows">
                    <label><?php _e( "Options" , 'wpad' ); ?></label>
                    <div class="wpuf-form-sub-fields wpuf-options wpad_radio_option">
                
				        <div class="wpuf-option-label-value">
				        	<span><?php _e( "Label" , 'wpad' ); ?></span>
				        	<span style="display: inline;" class="wpuf-option-value"><?php _e( "Value" , 'wpad' ); ?></span>
				        </div>
                		<div class="wpuf-clone-field ">
            				
                			<?php
                			if( !empty($data['options']) ):
	                			foreach( $data['options'] as $key => $value ){ 
	                				
	                				$this->radio_options( $value );
		    					} 
		    				
		    				else:
		    					$this->radio_options();
		    				endif; 
		    				?>
                		</div>
        
                	</div> <!-- .wpuf-form-sub-fields -->
                    
                    
                </div>
                <!--  
					Show to users only
		        -->
		        <div class="wpuf-form-rows wpad_show_only_admin">
		        	
		        	<?php 
		        	if( isset($data['show_admin']) && $data['show_admin'] == 1 ){
		        		$show_admin = 1;
		        	} else {
		        		$show_admin = '';
		        	}?>
		        	<label><?php _e( "Hide/Show" , 'wpad' ); ?>
		        		<a tool_tip="Tick if you want to show this field to admin only." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<label class="wpad_check">
		        		<input <?php checked( $show_admin , 1 ); ?> type="checkbox" name="show_admin_<?php echo $key; ?>" value="1"><?php _e( "Show to admin only" , 'wpad' ); ?>
		        	</label>
		        </div>
			</div>
		</li>
		<?php
	}
	/*
	** Get The custom field checkbox
	*/
	function checkbox( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
		<input type="hidden" name="input_checkbox" value="checkbox_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)"><?php _e( "Remove" , 'wpad' ); ?></a>
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($data['required']) ){ 
		            	if( $data['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		           Meta Key 
		        -->
		        <div class="wpuf-form-rows wpad_meta_key">
		        	
		        	<?php 
		        	$meta_key = !empty( $data['meta_key'] ) ? $data['meta_key'] : ''; 
		        	?>
		            <label>
		            <?php _e( "Meta Key" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Name of the meta key this field will save to." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="metakey_<?php echo $key; ?>" value="<?php echo $meta_key; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $data['css_name'] ) ? $data['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $data['error_message'] ) ? $data['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <div class="wpuf-form-rows">
                    <label><?php _e( "Options" , 'wpad' ); ?></label>
                    <div class="wpuf-form-sub-fields wpuf-options wpad_radio_option">
                
				        <div class="wpuf-option-label-value">
				        	<span><?php _e( "Label" , 'wpad' ); ?></span>
				        	<span style="display: inline;" class="wpuf-option-value"><?php _e( "Value" , 'wpad' ); ?></span>
				        </div>
                		<div class="wpuf-clone-field ">
            				
                			<?php
                			if( !empty($data['options']) ):
	                			foreach( $data['options'] as $key2 => $value2 ){ 
	                				
	                				$this->checkbox_options( $value2 );
		    					} 
		    				
		    				else:
		    					$this->checkbox_options();
		    				endif; 
		    				?>
                		</div>
        
                	</div> <!-- .wpuf-form-sub-fields -->
                    
                    
                </div>
                <!--  
					Show to users only
		        -->
		        <div class="wpuf-form-rows wpad_show_only_admin">
		        	
		        	<?php 
		        	if( isset($data['show_admin']) && $data['show_admin'] == 1 ){
		        		$show_admin = 1;
		        	} else {
		        		$show_admin = '';
		        	} ?>
		        	<label><?php _e( "Hide/Show" , 'wpad' ); ?>
		        		<a tool_tip="Tick if you want to show this field to admin only." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<label class="wpad_check">
		        		<input <?php checked( $show_admin , 1 ); ?> type="checkbox" name="show_admin_<?php echo $key; ?>" value="1"><?php _e( "Show to admin only" , 'wpad' ); ?>
		        	</label>
		        </div>
		        <?php $this->text_validation_method( $key , $data , 'checkbox' ); ?>
			</div>
		</li>
		<?php 
	}
	/*
	** Get The Custom field Select
	*/
	function select( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
			<input type="hidden" name="input_select" value="select_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)"><?php _e( "Remove" , 'wpad' ); ?></a>
	                <a class="wpuf-toggle" href="javascript:void(0)"><?php _e( "Toggle" , 'wpad' ); ?></a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($data['required']) ){ 
		            	if( $data['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		           Meta Key 
		        -->
		        <div class="wpuf-form-rows wpad_meta_key">
		        	
		        	<?php 
		        	$meta_key = !empty( $data['meta_key'] ) ? $data['meta_key'] : ''; 
		        	?>
		            <label>
		            <?php _e( "Meta Key" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Name of the meta key this field will save to." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="metakey_<?php echo $key; ?>" value="<?php echo $meta_key; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $data['css_name'] ) ? $data['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          Select Text
		        -->
		        <div class="wpuf-form-rows wpad_select_text">
		        	<?php 
		        	$select_first_option = !empty( $data['select_first_option'] ) ? $data['select_first_option'] : ''; 
		        	?>
		            <label><?php _e( "Select Text" , 'wpad' ); ?>
		            	<a tool_tip="First element of the select dropdown. Leave this empty if you don't want to show this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="dropdown_first_<?php echo $key; ?>" value="<?php echo $select_first_option; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $data['error_message'] ) ? $data['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <div class="wpuf-form-rows">
                    <label><?php _e( "Options" , 'wpad' ); ?></label>
                    <div class="wpuf-form-sub-fields wpuf-options wpad_radio_option">
                
				        <div class="wpuf-option-label-value">
				        	<span><?php _e( "Label" , 'wpad' ); ?></span>
				        	<span style="display: inline;" class="wpuf-option-value"><?php _e( "Value" , 'wpad' ); ?></span>
				        </div>
                		<div class="wpuf-clone-field ">
            				
                			<?php
                			if( !empty($data['options']) ):
	                			foreach( $data['options'] as $key => $value ){ 
	                				
	                				$this->checkbox_options( $value );
		    					} 
		    				
		    				else:
		    					$this->checkbox_options();
		    				endif; 
		    				?>
                		</div>
        
                	</div> <!-- .wpuf-form-sub-fields -->
                    
                    
                </div>
                <!--  
					Show to users only
		        -->
		        <div class="wpuf-form-rows wpad_show_only_admin">
		        	
		        	<?php 
		        	if( isset($data['show_admin']) && $data['show_admin'] == 1 ){
		        		$show_admin = 1;
		        	} else {
		        		$show_admin = '';
		        	}?>
		        	<label><?php _e( "Hide/Show" , 'wpad' ); ?>
		        		<a tool_tip="Tick if you want to show this field to admin only." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<label class="wpad_check">
		        		<input <?php checked( $show_admin , 1 ); ?> type="checkbox" name="show_admin_<?php echo $key; ?>" value="1"><?php _e( "Show to admin only" , 'wpad' ); ?>
		        	</label>
		        </div>
			</div>
		</li>
		<?php 
	}
	function checkbox_options( $value = null ){ 
		$label = !empty( $value['label'] ) ? $value['label'] : '';
		$option = !empty( $value['option'] ) ? $value['option'] : ''; ?>
		<div class="radio_option_wrapper">
			<input type="text" value="<?php echo $label; ?>" name="label">
			<input type="text" value="<?php echo $option; ?>" name="value">
    		<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/add.png' ;?>" class="wpad-clone-field" title="add another choice" alt="add another choice" style="cursor:pointer; margin:0 3px;">
			<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/remove.png' ;?>" title="remove this choice" alt="remove this choice" class="wpad-remove-field" style="cursor:pointer;">
		</div>
	 	<?php 
	}
	function radio_options( $value = null ){ 
		$label = !empty( $value['label'] ) ? $value['label'] : '';
		$option = !empty( $value['option'] ) ? $value['option'] : ''; ?>
		<div class="radio_option_wrapper">
			<input type="text" value="<?php echo $label; ?>" name="label">
			<input type="text" value="<?php echo $option; ?>" name="value">
    		<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/add.png' ;?>" class="wpad-clone-field" title="add another choice" alt="add another choice" style="cursor:pointer; margin:0 3px;">
			<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/remove.png' ;?>" title="remove this choice" alt="remove this choice" class="wpad-remove-field" style="cursor:pointer;">
		</div>
	 	<?php 
	}
	/*
	** Get the Custom field Email
	*/
	function email( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
			<input type="hidden" name="input_email" value="email_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)">Remove</a>
	                <a class="wpuf-toggle" href="javascript:void(0)">Toggle</a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($data['required']) ){ 
		            	if( $data['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		           Meta Key 
		        -->
		        <div class="wpuf-form-rows wpad_meta_key">
		        	
		        	<?php 
		        	$meta_key = !empty( $data['meta_key'] ) ? $data['meta_key'] : ''; 
		        	?>
		            <label>
		            <?php _e( "Meta Key" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Name of the meta key this field will save to." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="metakey_<?php echo $key; ?>" value="<?php echo $meta_key; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $data['css_name'] ) ? $data['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          PlaceHolder Text 
		        -->
		        <div class="wpuf-form-rows wpad_placeholder">
		        	<?php 
		        	$placeholder = !empty( $data['placeholder_text'] ) ? $data['placeholder_text'] : ''; 
		        	?>
		            <label><?php _e( "PlaceHolder Text" , 'wpad' ); ?>
		            	<a tool_tip="Text for HTML5 placeholder attribute." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="placeholder_<?php echo $key; ?>" value="<?php echo $placeholder; ?>">
		        </div>
		        <!-- 
		          Default Value
		        -->
		        <div class="wpuf-form-rows wpad_default_value">
		        	<?php 
		        	$default_value = !empty( $data['default_value'] ) ? $data['default_value'] : ''; 
		        	?>
		            <label><?php _e( "Default Value" , 'wpad' ); ?>
		            	<a tool_tip="Default value for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="default_value_<?php echo $key; ?>" value="<?php echo $default_value; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $data['error_message'] ) ? $data['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <!-- 
		          Size
		        -->
		        <div class="wpuf-form-rows wpad_size">
		        	<?php 
		        	$size = !empty( $data['size'] ) ? $data['size'] : '40'; 
		        	?>
		            <label><?php _e( "Size" , 'wpad' ); ?>
		            	<a tool_tip="Size of this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="size_<?php echo $key; ?>" value="<?php echo $size; ?>">
		        </div>
		        <!--  
					Show to users only
		        -->
		        <div class="wpuf-form-rows wpad_show_only_admin">
		        	
		        	<?php 
		        	if( isset($data['show_admin']) && $data['show_admin'] == 1 ){
		        		$show_admin = 1;
		        	} else {
		        		$show_admin = '';
		        	} ?>
		        	<label><?php _e( "Hide/Show" , 'wpad' ); ?>
		        		<a tool_tip="Tick if you want to show this field to admin only." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<label class="wpad_check">
		        		<input <?php checked( $show_admin , 1 ); ?> type="checkbox" name="show_admin_<?php echo $key; ?>" value="1"><?php _e( "Show to admin only" , 'wpad' ); ?>
		        	</label>
		        </div>
			</div>
		</li>
		<?php
	}
	/*
	** Get the Custom field URL
	*/
	function url( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
			<input type="hidden" name="input_url" value="url_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)">Remove</a>
	                <a class="wpuf-toggle" href="javascript:void(0)">Toggle</a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($data['required']) ){ 
		            	if( $data['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		           Meta Key 
		        -->
		        <div class="wpuf-form-rows wpad_meta_key">
		        	
		        	<?php 
		        	$meta_key = !empty( $data['meta_key'] ) ? $data['meta_key'] : ''; 
		        	?>
		            <label>
		            <?php _e( "Meta Key" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Name of the meta key this field will save to." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="metakey_<?php echo $key; ?>" value="<?php echo $meta_key; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $data['css_name'] ) ? $data['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          PlaceHolder Text 
		        -->
		        <div class="wpuf-form-rows wpad_placeholder">
		        	<?php 
		        	$placeholder = !empty( $data['placeholder_text'] ) ? $data['placeholder_text'] : ''; 
		        	?>
		            <label><?php _e( "PlaceHolder Text" , 'wpad' ); ?>
		            	<a tool_tip="Text for HTML5 placeholder attribute." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="placeholder_<?php echo $key; ?>" value="<?php echo $placeholder; ?>">
		        </div>
		        <!-- 
		          Default Value
		        -->
		        <div class="wpuf-form-rows wpad_default_value">
		        	<?php 
		        	$default_value = !empty( $data['default_value'] ) ? $data['default_value'] : ''; 
		        	?>
		            <label><?php _e( "Default Value" , 'wpad' ); ?>
		            	<a tool_tip="Default value for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="default_value_<?php echo $key; ?>" value="<?php echo $default_value; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $data['error_message'] ) ? $data['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <!-- 
		          	Size
		        -->
		        <div class="wpuf-form-rows wpad_size">
		        	<?php 
		        	$size = !empty( $data['size'] ) ? $data['size'] : '40'; 
		        	?>
		            <label><?php _e( "Size" , 'wpad' ); ?>
		            	<a tool_tip="Size of this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="size_<?php echo $key; ?>" value="<?php echo $size; ?>">
		        </div>
		        <!--  
					Show to users only
		        -->
		        <div class="wpuf-form-rows wpad_show_only_admin">
		        	
		        	<?php 
		        	if( isset($data['show_admin']) && $data['show_admin'] == 1 ){
		        		$show_admin = 1;
		        	} else {
		        		$show_admin = '';
		        	} ?>
		        	<label><?php _e( "Hide/Show" , 'wpad' ); ?>
		        		<a tool_tip="Tick if you want to show this field to admin only." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<label class="wpad_check">
		        		<input <?php checked( $show_admin , 1 ); ?> type="checkbox" name="show_admin_<?php echo $key; ?>" value="1"><?php _e( "Show to admin only" , 'wpad' ); ?>
		        	</label>
		        </div>
			</div>
		</li>
		<?php
	}
	/*
	** Get The Custom field Multi Select
	*/
	function multi_select( $key , $custom_field , $type , $data = null ){ ?>
		<li class="post_title">
			<input type="hidden" name="input_multi_select" value="multi_select_<?php echo $key; ?>">
			<div class="wpad_custom_field">
				<input type="hidden" name="custom_field" value="<?php echo $type; ?>">
			</div>
			<div class="wpuf-legend" title="Click and Drag to rearrange">
				<div class="wpuf-label">
					<?php echo $custom_field; ?> <?php _e( "Title :" , 'wpad' ); ?> <strong><?php echo $data['label']; ?></strong>
				</div>
				<div class="wpuf-actions">
	                <a class="wpuf-remove" href="javascript:void(0)">Remove</a>
	                <a class="wpuf-toggle" href="javascript:void(0)">Toggle</a>
	            </div>
			</div>
			<div class="wpuf-form-holder">
				<!-- Title -->
				<div class="wpuf-form-rows required-field">
		            <label><?php _e( "Required" , 'wpad' ); ?></label>
		            <?php 
		            if( !empty($data['required']) ){ 
		            	if( $data['required'] == 'yes' ){
		            		$checked = 'yes';
		            	} else {
		            		$checked = 'no';
		            	}
		            } else {
		            	$checked = 'yes';
		            }
		            ?>
		            <div class="wpuf-form-sub-fields">
		                <label><input type="radio" value="yes" name="required_<?php echo $key; ?>" <?php checked( $checked, 'yes' ); ?> > <?php _e( "Yes" , 'wpad' ); ?> </label>
		                <label><input type="radio" value="no" name="required_<?php echo $key; ?>" <?php checked( $checked, 'no' ); ?> > <?php _e( "No" , 'wpad' ); ?> </label>
		            </div>
		        </div>
		        
		        <!-- 
		           Label 
		        -->
		        <div class="wpuf-form-rows wpad_label">
		        	
		        	<?php 
		        	$label = !empty( $data['label'] ) ? $data['label'] : ''; 
		        	?>
		            <label><?php _e( "Field Label" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Enter a title of this field." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="label_<?php echo $key; ?>" value="<?php echo $label; ?>">
		        </div>
		        <!-- 
		           Meta Key 
		        -->
		        <div class="wpuf-form-rows wpad_meta_key">
		        	
		        	<?php 
		        	$meta_key = !empty( $data['meta_key'] ) ? $data['meta_key'] : ''; 
		        	?>
		            <label>
		            <?php _e( "Meta Key" , 'wpad' ); ?><span class="required"> *</span>
		            	<a tool_tip="Name of the meta key this field will save to." class="wpad_tooltip"><span></span></a>
		        	</label>
		            <input type="text" class="" name="metakey_<?php echo $key; ?>" value="<?php echo $meta_key; ?>">
		        </div>
		        <!-- 
		          Help Text 
		        -->
		        <div class="wpuf-form-rows wpad_help_text">
		        	<?php 
		        	$help_text = !empty( $data['help_text'] ) ? $data['help_text'] : ''; 
		        	?>
		            <label><?php _e( "Help Text" , 'wpad' ); ?>
		            	<a tool_tip="Give the user some information about this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <textarea class="" name="help_text_<?php echo $key; ?>"><?php echo $help_text; ?></textarea>
		        </div>
		        <!-- 
		          CSS Class Name 
		        -->
		        <div class="wpuf-form-rows wpad_css_class_name">
		        	<?php 
		        	$css_name = !empty( $data['css_name'] ) ? $data['css_name'] : ''; 
		        	?>
		            <label><?php _e( "CSS Class Name" , 'wpad' ); ?>
		            	<a tool_tip="Add a CSS class name for this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class=""  name="css_class_name_<?php echo $key; ?>" value="<?php echo $css_name; ?>">
		        </div>
		        <!-- 
		          Select Text
		        -->
		        <div class="wpuf-form-rows wpad_select_text">
		        	<?php 
		        	$select_first_option = !empty( $data['select_first_option'] ) ? $data['select_first_option'] : ''; 
		        	?>
		            <label><?php _e( "Select Text" , 'wpad' ); ?>
		            	<a tool_tip="First element of the select dropdown. Leave this empty if you don't want to show this field." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="dropdown_first_<?php echo $key; ?>" value="<?php echo $select_first_option; ?>">
		        </div>
		        <!-- 
		          	Error Message
		        -->
		        <div class="wpuf-form-rows wpad_error_message">
		        	<?php 
		        	$error_message = !empty( $data['error_message'] ) ? $data['error_message'] : ''; 
		        	?>
		            <label><?php _e( "Error Message" , 'wpad' ); ?>
		            	<a tool_tip="Add custom error message. Leave it blank to show default message." class="wpad_tooltip"><span></span></a>
		            </label>
		            <input type="text" class="" name="error_message_<?php echo $key; ?>" value="<?php echo $error_message; ?>">
		        </div>
		        <div class="wpuf-form-rows">
                    <label><?php _e( "Options" , 'wpad' ); ?></label>
                    <div class="wpuf-form-sub-fields wpuf-options wpad_radio_option">
                
				        <div class="wpuf-option-label-value">
				        	<span><?php _e( "Label" , 'wpad' ); ?></span>
				        	<span style="display: inline;" class="wpuf-option-value"><?php _e( "Value" , 'wpad' ); ?></span>
				        </div>
                		<div class="wpuf-clone-field ">
            				
                			<?php
                			if( !empty($data['options']) ):
	                			foreach( $data['options'] as $key => $value ){ 
	                				
	                				$this->checkbox_options( $value );
		    					} 
		    				
		    				else:
		    					$this->checkbox_options();
		    				endif; 
		    				?>
                		</div>
        
                	</div> <!-- .wpuf-form-sub-fields -->
                    
                    
                </div>
                <!--  
					Show to users only
		        -->
		        <div class="wpuf-form-rows wpad_show_only_admin">
		        	
		        	<?php 
		        	if( isset($data['show_admin']) && $data['show_admin'] == 1 ){
		        		$show_admin = 1;
		        	} else {
		        		$show_admin = '';
		        	} ?>
		        	<label><?php _e( "Hide/Show" , 'wpad' ); ?>
		        		<a tool_tip="Tick if you want to show this field to admin only." class="wpad_tooltip"><span></span></a>
		        	</label>
		        	<label class="wpad_check">
		        		<input <?php checked( $show_admin , 1 ); ?> type="checkbox" name="show_admin_<?php echo $key; ?>" value="1"><?php _e( "Show to admin only" , 'wpad' ); ?>
		        	</label>
		        	
		        </div>
			</div>
		</li>
		<?php 
	}

	/*
	** Get Elements
	*/

	function wpad_comment_element(){

		$key = wp_generate_password( 25, false, false );
		$custom_field = ucwords( str_replace( '_', ' ' , $_POST['type'] ) );
		ob_start();

		switch ( $_POST['type'] ) {

			case 'text':
				$this->text( $key , $custom_field , $_POST['type'] );
				break;
			case 'textarea':
				$this->textarea( $key , $custom_field , $_POST['type'] );
				break;
			case 'radio':
				$this->radio( $key , $custom_field , $_POST['type'] );
				break;
			case 'checkbox':
				$this->checkbox( $key , $custom_field , $_POST['type'] );
				break;
			case 'select':
				$this->select( $key , $custom_field , $_POST['type'] );
				break;
			case 'email':
				$this->email( $key , $custom_field , $_POST['type'] );
				break;
			case 'url':
				$this->url( $key , $custom_field , $_POST['type'] );
				break;
			case 'multi_select':
				$this->multi_select( $key , $custom_field , $_POST['type'] );
				break;
			case 'section_break':
				$this->section_break( $key , $custom_field , $_POST['type'] );
				break;
			case 'html':
				$this->html( $key , $custom_field , $_POST['type'] );
				break;
			case 'user_image':
				$this->user_image( $key , $custom_field , $_POST['type'] );
			default:
				# code...
				break;

		}

		$content = ob_get_clean();
		$data = array(
			'content' => $content
		);
		echo json_encode( $data );
		die;
	}

	function get_only_post_id_key( $post_ids ){
		$arr = array();
		foreach( $post_ids as $key => $value ){
			$arr[] = $key;
		}
		return $arr;
	}
	function enable_all_post_ids( $comment_forms_on_posts , $comment_id ){
		$comment_forms = get_option('wpad_comment_forms_on_posts');
		if( $comment_forms_on_posts == 'enable_certain_pages' && !empty($comment_forms[$comment_id]['enable_certain_pages']['all_posts']) ){
			$post_types = $comment_forms[$comment_id]['enable_certain_pages']['all_posts'];
		} else {
			$post_types = array();
		}
		return $post_types;
	}
	function enable_certain_pages_post_ids( $comment_forms_on_posts , $comment_id ){
		$comment_forms = get_option('wpad_comment_forms_on_posts');
		if( $comment_forms_on_posts == 'enable_certain_pages' && !empty($comment_forms[$comment_id]['enable_certain_pages']['post_ids']) ){
			$post_ids = $comment_forms[$comment_id]['enable_certain_pages']['post_ids'];
			$post_ids = $this->get_only_post_id_key( $post_ids );
		} else {
			$post_ids = array();
		}
		return $post_ids;
	}
	function drop_down_button_posttype($posttype){
		echo '<a href="javascript:void(0)" class="button wpad_choose_posts">'.
    			ucwords($posttype) .
    			'<span class="dashicons dashicons-arrow-down wpad_arraw_down"></span>
    		</a>';
	}
	function select_all_option_edit( $select_all_check , $posttype ){
		echo '<ul class="wpad_post_type_pages" style="display:none">';
		echo '<li class="select_all_li">
			<label>
				<input ' . $select_all_check . ' value="'. $posttype .'" class="wpad_select_all" type="checkbox" name="' . 'wpad_all_posts_' . $posttype .'">' . '<strong>Select All ' . $posttype . '</strong>
			</label>
			<p class="description">( It will select all the posts/pages including the new ones. )
			</p>
		</li>';
	}
	function display_woocommerce_message(){
		
		$result = $this->check_woocommerce_active();
		if( $result == true ){ ?>
			<div class="woocommerce_notice">
				<h3 class="hndle">
					<span><?php _e( 'WooCommerce Advanced Product Reviews Available Soon ...' , 'wpad' ); ?></span>
				</h3>
				<p>
					It seems like you are using WooCommerce plugin. WP Advance Comment works with the WooCommerce reviews. If you want you can buy an extension to enable comment forms on the WooCommerce products. We will notify you when this plugin will be ready for use.  
				</p>
			</div>
			<?php
		}
	}
	function check_woocommerce_active( $posttype = null ){
		if( class_exists( 'WooCommerce' ) ){
			return true;
 
		} else {
			return false;
		
		}
	}
	function get_post_type_post( $posttype , $comment_forms_on_posts , $comment_id ){
		$post_ids = $this->enable_certain_pages_post_ids( $comment_forms_on_posts , $comment_id );
		$all_post_types = $this->enable_all_post_ids( $comment_forms_on_posts , $comment_id );
		$woocommerce = $this->check_woocommerce_active( $posttype );
		$skip_post_type = array( 'shop_order' , 'product' , 'shop_coupon' );
		if( class_exists('wpad_woocommerce') ){
			unset( $skip_post_type[1] );
		}
		if( $woocommerce == true && in_array( $posttype , $skip_post_type ) ){
			return;
		}
		$args = array(
			'post_type' => $posttype,
			'showposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		);
		$wpad_query = new WP_Query( $args );
		if( $wpad_query->have_posts() ):
			$select_all_check = in_array( $posttype , $all_post_types ) ? 'checked="checked"' : '';
			$this->drop_down_button_posttype( $posttype );
			$this->select_all_option_edit( $select_all_check , $posttype );
			while( $wpad_query->have_posts() ): $wpad_query->the_post();
				global $post;
				// $check and $check_post_type will not be active at the same time.
				$check = in_array( $post->ID , $post_ids ) ? 'checked="checked"' : '';
				$check_post_type = in_array( $post->post_type , $all_post_types ) ? 'checked="checked"' : '';
				echo '<li>
				<label>
				<input ' . $check . $check_post_type . 'type="checkbox" name="wpad_selected_posts_comment_form" value="' . $post->ID . '">' . get_the_title( $post->ID ) . '
				</label>
				</li>';
			endwhile;
			echo '</ul>';
		endif;
	}
	function get_all_post_types( $comment_forms_on_posts , $comment_id ){
		$post_types = get_post_types( '', 'names' );
    	unset( $post_types['attachment'] , $post_types['revision'], $post_types['nav_menu_item'] );
    	foreach( $post_types as $post ){ 
    		$this->get_post_type_post( $post , $comment_forms_on_posts , $comment_id );
    	}
	}
	function get_comments_show( $comment_forms , $id ){
		if( !empty( $comment_forms[$id] ) && is_array($comment_forms[$id]) && array_key_exists( "show_on_all" , $comment_forms[$id] ) ){
			return 'show_on_all';
		} elseif( !empty( $comment_forms[$id] ) && is_array($comment_forms[$id]) && array_key_exists( "enable_certain_pages" , $comment_forms[$id] ) ) {
			return 'enable_certain_pages';
		} else {
			return 'none';
		}
	}
}
$edit_comment_form = new wpad_edit_comment_form();