<?php

/* 
** Edit the saved comment
*/

class wpad_edit_saved_comment{

	function __construct(){

		add_action('admin_menu', array( $this, 'saved_edit_menu') );

		if( !(defined( 'DOING_AJAX' ) && DOING_AJAX) ){
			
			if( isset( $_GET['page'] ) && $_GET['page'] == 'wpad_saved_edit_form' ){
				$this->custom_title_page();
				add_action( 'admin_init', array( $this , 'wpad_custom_menu_class' ) );
			}
		}

		add_action( 'wp_ajax_wpad_edit_comment_saved' , array( $this , 'wpad_edit_comment_saved' ) );
	}

	function custom_title_page(){
		echo '<title>' .  __( "Edit Comment" , 'wpad' ) . '</title>';
	}

	function set_comment_status( $comment_id , $comment_status ){

		if( $comment_status == '1' ){
 			wp_set_comment_status( $comment_id , 'approve' );
		} elseif( $comment_status == '0' ){
			wp_set_comment_status( $comment_id , 'hold' );
		} else {
			wp_set_comment_status( $comment_id , 'spam' );
		}

	}

	function update_comment(){

		$commentarr = array();
		$commentarr['comment_ID'] = $_POST['comment_id'];
		$commentarr['comment_content'] = $_POST['comment'];
		$update_success = wp_update_comment($commentarr);

	}

	function wpad_edit_comment_saved(){

		$ignore = array( 'comment_status' , 'comment_id' , 'comment' , 'action' );
		foreach( $_POST as $key => $value ){

			if( !in_array( $key , $ignore) ){
				update_comment_meta( $_POST['comment_id'] , $key , $value );	
			}
			
		}
		$this->set_comment_status( $_POST['comment_id'] , $_POST['comment_status'] );
		$this->update_comment( $_POST );
		
		die;

	}

	function wpad_custom_menu_class(){

		global $menu;
		
		if( ( isset( $_GET['page'] ) && $_GET['page'] == 'wpad_saved_edit_form' ) ){
			foreach( $menu as $key => $value ){
		        if( 'WP Advanced Comment' == $value[3] ){
			        $menu[$key][4] .= " wp-has-current-submenu";
			    }
		        
		    }
		}
		
	}

	function saved_edit_menu(){
		add_submenu_page( '' , 'WP Advanced Comment Edit Form', 'Edit Comment Form', 'manage_options', 'wpad_saved_edit_form', array($this, 'edit_comment_form'));
	}

	function update_notice( $status ){ 
		if( $status == 'updated' ) { ?>
			<div class="updated notice notice-success is-dismissible below-h2" id="message">
				<p><?php _e( "You have successfully updated this comment." , 'wpad' ); ?></p>
				<button class="notice-dismiss" type="button">
					<span class="screen-reader-text"><?php _e( "Dismiss this notice." , 'wpad' ); ?></span>
				</button>
			</div>
			<?php 
		}
	}

	function custom_field_text( $value , $comment_id ){ 
		$meta_value = get_comment_meta( $comment_id, $value['meta_key'] , true );?>
		<tr class="text">
			<td class="first"><?php echo $value['label']; ?> :</td>
			<td><input type="text" name="<?php echo $value['input_name'] . ':' . $value['meta_key']; ?>" value="<?php echo $meta_value; ?>"></td>
		</tr>
		<?php
	}

	function custom_field_textarea( $value , $comment_id ){ 
		$meta_value = get_comment_meta( $comment_id, $value['meta_key'] , true );?>
		<tr class="textarea">
			<td class="first"><?php echo $value['label']; ?> :</td>
			<td>
				<textarea name="<?php echo $value['input_name'] . ':' . $value['meta_key']; ?>"><?php echo $meta_value; ?></textarea>
			</td>
		</tr>
		<?php
	}

	function custom_field_radio( $value , $comment_id ){ 
		$meta_value = get_comment_meta( $comment_id, $value['meta_key'] , true );?>
		<tr class="radio">
			<td class="first"><?php echo $value['label']; ?> :</td>
			<td>
				<?php 
				foreach( $value['options'] as $option_key => $option_value ): ?>
					<label>
						<input <?php checked( $meta_value, $option_value['option'] ); ?> type="radio" name="<?php echo $value['input_name'] . ':' . $value['meta_key']; ?>" value="<?php echo $option_value['option']; ?>"><?php echo $option_value['label']; ?>
					</label>
					<br>
					<?php 
				endforeach; ?>
			</td>
		</tr>
		<?php
	}

	function custom_field_checkbox( $value , $comment_id ){
		$meta_value = explode( ',' , get_comment_meta( $comment_id, $value['meta_key'] , true ) );?>
		<tr class="checkbox">
			<td class="first"><?php echo $value['label']; ?> :</td>
			<td>
				<?php 
				foreach( $value['options'] as $option_key => $option_value ): 
					if( in_array( $option_value['option'] , $meta_value) ){
						$checked = 'checked';
					} else {
						$checked = '';
					} ?>
					<label>
						<input <?php echo $checked; ?> type="checkbox" name="<?php echo $value['input_name'] . ':' . $value['meta_key']; ?>" value="<?php echo $option_value['option']; ?>"><?php echo $option_value['label']; ?>
					</label>
					<br>
					<?php 
				endforeach; ?>
			</td>
		</tr>
		<?php
	}

	function custom_field_select( $value , $comment_id ){ 
		$meta_value = get_comment_meta( $comment_id, $value['meta_key'] , true );?>
		<tr class="select">
			<td class="first"><?php echo $value['label']; ?> :</td>
			<td>
				<?php 
				if( is_array( $value['options'] ) && !empty( $value['options'] ) ){
					echo '<select name="' . $value['input_name'] . ':' . $value['meta_key'] . '" >';
					if( isset( $value['select_first_option'] ) ){
						echo '<option value="">' . $value['select_first_option'] . '</option>';
					}
					foreach( $value['options'] as $option_key => $option_value ): ?>
						<option <?php selected( $meta_value, $option_value['option'] ); ?> value="<?php echo $option_value['option']; ?>"><?php echo $option_value['label']; ?></option>
						<?php 
					endforeach; 
					echo '</select>';
				} ?>
			</td>
		</tr>
		<?php
	}

	function custom_field_email( $value , $comment_id ){ 
		$meta_value = get_comment_meta( $comment_id, $value['meta_key'] , true );?>
		<tr class="email">
			<td class="first"><?php echo $value['label']; ?> :</td>
			<td><input type="text" name="<?php echo $value['input_name'] . ':' . $value['meta_key']; ?>" value="<?php echo $meta_value; ?>"></td>
		</tr>
		<?php
	}

	function custom_field_url( $value , $comment_id ){ 
		$meta_value = get_comment_meta( $comment_id, $value['meta_key'] , true );?>
		<tr class="url">
			<td class="first"><?php echo $value['label']; ?> :</td>
			<td><input type="text" name="<?php echo $value['input_name'] . ':' . $value['meta_key']; ?>" value="<?php echo $meta_value; ?>"></td>
		</tr>
		<?php
	}

	function custom_field_multi_select( $value , $comment_id ){
		$meta_value = explode( ',' , get_comment_meta( $comment_id, $value['meta_key'] , true ) );?>
		<tr class="multiselect">
			<td class="first"><?php echo $value['label']; ?> :</td>
			<td>
				<?php 
				if( is_array( $value['options'] ) && !empty( $value['options'] ) ){
					echo '<select multiple name="' . $value['input_name'] . ':' . $value['meta_key'] . '">';
					foreach( $value['options'] as $option_key => $option_value ): 
						if( in_array( $option_value['option'] , $meta_value) ){
							$selected = 'selected';
						} else {
							$selected = '';
						} ?>
						<option <?php echo $selected; ?> value="<?php echo $option_value['option']; ?>"><?php echo $option_value['label']; ?></option>
						<?php 
					endforeach; 
					echo '</select>';
				} ?>
			</td>
		</tr>
		<?php
	}

	function get_comment_metas(){
		$commnet_form_id = $_GET['formid'];
		$comment_id = $_GET['commentid'];
		$comment_details = get_option('wpad_comment_form');
		if( !empty($comment_details[$commnet_form_id]) ){
			foreach( $comment_details[$commnet_form_id] as $key => $value ){
				if( $key != 'other' ){
					if( isset( $value['custom_field'] ) ){
						switch ( $value['custom_field'] ) {
							case 'text':
								$this->custom_field_text( $value , $comment_id );
								break;							
							case 'textarea':
								$this->custom_field_textarea( $value , $comment_id );
								break;
							case 'radio':
								$this->custom_field_radio( $value , $comment_id );
								break;
							case 'checkbox':
								$this->custom_field_checkbox( $value , $comment_id );
								break;
							case 'select':
								$this->custom_field_select( $value , $comment_id );
								break;
							case 'email':
								$this->custom_field_email( $value , $comment_id );
								break;
							case 'url':
								$this->custom_field_url( $value , $comment_id );
								break;
							case 'multi_select':
								$this->custom_field_multi_select( $value , $comment_id );
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

	function get_comment_details( $results ){ ?>
		<table class="form-table editcomment wpad_editcomment">
			<tbody>
	
				<tr>
					<td class="first"><?php _e( "Profile Pic :" , 'wpad' ); ?></td>
					<td>
						<?php 

						$comment_form = new wpad_frontend_comment_form();
						$wpad_advance_comment = new wpad_advance_comment();

						$object = $wpad_advance_comment->get_comment_in_array( $_GET['commentid'] );

						$option = get_option( 'wpad_comment_form' ); 

						$commnet_form_id = $_GET['formid'];

						$db_option = !empty($option[$commnet_form_id]) ? $option[$commnet_form_id] : '';

						echo $comment_form->validate_gravatar( $results[0]['comment_author_email'] , $db_option , $object , 150 ); ?>
					</td>
				</tr>
				<tr>
					<td class="first"><?php _e( "Name :" , 'wpad' ); ?></td>
					<td><input type="text" name="user_login" value="<?php echo $results[0]['comment_author']; ?>" disabled /></td>
				</tr>
				<tr>
					<td class="first">
					<?php _e( "E-mail" , 'wpad' ); ?> ( <a href="mailto:<?php echo $results[0]['comment_author_email']?>"><?php _e( "Send E-mail" , 'wpad' ); ?></a> ) :</td>
					<td><input type="text" name="user_email" value="<?php echo $results[0]['comment_author_email']; ?>" disabled /></td>
				</tr>
				<?php $this->get_comment_metas(); ?>
				<tr>
					<td class="first">
						<?php _e( "IP Address :" , 'wpad' ); ?> 
					</td>
					<td>
						<a href="http://whois.arin.net/rest/ip/<?php echo $results[0]['comment_author_IP']; ?>">
							<?php echo $results[0]['comment_author_IP']; ?>
						</a>
					</td>
				</tr>
				<tr>
					<td class="first">
					<?php _e( "Submitted On :" , 'wpad' ); ?> </td>
					<td><p><?php echo date( 'M d, Y \a\t g:i A' , strtotime($results[0]['comment_date']) ); ?></p></td>
				</tr>
				<?php 
				if( $results[0]['comment_parent'] != 0 ){ ?>
					<tr>
						<td class="first"><?php _e( "In Reply To :" , 'wpad' ); ?> </td>
						<td><a href="#"><?php echo get_comment_author( $results[0]['comment_parent'] ); ?></a></td>
					</tr>
					<?php 
				} ?>
			</tbody>
		</table>
		<?php
	}

	function validate_edit_form(){
		global $wpdb;
		$comment_id = $_GET['commentid']; 
		$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments
			WHERE `comment_ID` = {$comment_id}" , ARRAY_A ); 
		$db_form_id = get_comment_meta( $comment_id , 'wpad_comment_form_id' , true );
	
		// ****************************************** 
		// If no results found show the error message
		// ******************************************
		if( !isset( $_GET['formid'] ) || $db_form_id != $_GET['formid'] || count($results) < 1 ){
			echo '<div class="wpad_access_error"><p>Oops !!! Something Went Wrong. <a href="' . admin_url('admin.php?page=wp_advance_comment') . '">Click Here</a> to go back.</p></div>';
			die;
		}
		return $results;
	}

	function edit_comment_form(){ 
		$results = $this->validate_edit_form(); ?>
		
		<div class="wrap">
			<h2><?php _e( "Edit Comment" , 'wpad' ); ?></h2>
			<div id="poststuff">
				
				<?php
				$status = isset( $_GET['status'] ) ?  $_GET['status'] : '';
				$this->update_notice( $status ); ?>
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content" class="edit-form-section">
						<div id="namediv" class="stuffbox">
							<h3>
								<label for="name"><?php _e( "Comment Details" , 'wpad' ); ?></label>
							</h3>
							<div class="inside">
								<?php $this->get_comment_details( $results ); ?>
							</div>
						</div>
						<div id="postdiv" class="postarea">
						<?php
						$content = $results[0]['comment_content'];
						$editor_id = 'comment_textarea';
						$settings = array( 
							'wpautop' => true, 
							'media_buttons' => false , 
							'textarea_rows' => 8 , 
							'tinymce' => false
						);
						wp_editor( $content, $editor_id , $settings );
						?>
						</div>
					</div>
					<div id="postbox-container-1" class="postbox-container">
						<div id="submitdiv" class="stuffbox">
							<h3>
								<span class=""><?php _e( "Status" , 'wpad' ); ?></span>
							</h3>
							<div class="inside">
								<div id="submitcomment" class="submitbox">
									<div id="minor-publishing">
										<div id="misc-publishing-actions">
											<div id="comment-status-radio" class="misc-pub-section misc-pub-comment-status">
												<label class="approved">
													<input <?php checked( $results[0]['comment_approved'], 1 );?> type="radio" value="1" name="comment_status"><?php _e( "Approved" , 'wpad' ); ?>
												</label>
												<br>
												<label class="waiting">
													<input <?php checked( $results[0]['comment_approved'], 0 );?> type="radio" value="0" name="comment_status"><?php _e( "Pending" , 'wpad' ); ?>
												</label>
												<br>
												<label class="spam">
													<input <?php checked( $results[0]['comment_approved'], 'spam' );?> type="radio" value="spam" name="comment_status"><?php _e( "Spam" , 'wpad' ); ?>
												</label>
											</div>
										</div>
										<div id="major-publishing-actions">
											<div id="delete-action">
												<a class="submitdelete deletion" href="<?php echo admin_url('admin.php?page=wp_advance_comment&action=trash') . '&commentid=' . $_GET['commentid']; ?>"><?php _e( "Move to Trash" , 'wpad' ); ?></a>
											</div>
											<div id="publishing-action">
												<img style="display:none" class="wpad_submit_reply_loader" src="<?php echo includes_url('/images/spinner.gif'); ?>">
												<input id="wpad_save_comment_edit" class="button button-primary" type="button" value="Update" name="save">
											</div>
											<div class="clear"></div>
											<input type="hidden" name="edit_commentid" value="<?php echo $_GET['commentid']; ?>">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

}
$saved_comment = new wpad_edit_saved_comment();