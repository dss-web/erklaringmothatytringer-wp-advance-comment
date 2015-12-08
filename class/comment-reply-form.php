<?php

// 
// This is a comment reply form 
// 

class wpad_reply_form {

	function __construct(){

		add_action('admin_menu', array( $this, 'reply_form_menu') );

		add_action( 'wp_ajax_wpad_save_reply' , array( $this , 'wpad_save_reply' ) );

		if( !(defined( 'DOING_AJAX' ) && DOING_AJAX) ){

			if( isset( $_GET['page'] ) && $_GET['page'] == 'wpad_reply_form' ){

				$this->custom_title_page();

				add_action( 'admin_init', array( $this , 'wpad_custom_menu_class' ) );

			}

		}

	}

	function custom_title_page(){

		echo '<title>' . __( "Reply Comment" , 'wpad' ) . '</title>';

	}

	function wpad_custom_menu_class(){

		global $menu;

		if( ( isset( $_GET['page'] ) && $_GET['page'] == 'wpad_reply_form' ) ){

			foreach( $menu as $key => $value ){

		        if( 'WP Advanced Comment' == $value[3] ){

			        $menu[$key][4] .= " wp-has-current-submenu";

			    }

		    }

		}

	}

	function wpad_save_reply(){

		$data = array(

		    'comment_post_ID' => $_POST['comment_post_ID'],

		    'comment_author' => $_POST['comment_author'],

		    'comment_author_email' => $_POST['comment_author_email'],

		    'comment_author_url' => $_POST['comment_author_url'],

		    'comment_content' => $_POST['comment_content'],

		    'comment_type' => '',

		    'comment_parent' => $_POST['comment_parent'],

		    'user_id' => $_POST['user_id'],

		    'comment_author_IP' => $_POST['comment_author_IP'],

		    'comment_agent' => $_POST['comment_agent'],

		    'comment_date' => $_POST['comment_date'],

		    'comment_approved' => $_POST['comment_approved'],

		);

		$comment_id = wp_insert_comment($data);

		die;

	}

	function reply_form_menu(){

		add_submenu_page( '' , 'WP Advanced Comment Reply Form', 'Reply Comment Form', 'manage_options', 'wpad_reply_form', array($this, 'reply_comment_form'));

	}

	function get_comment_details( $results ){ ?>

		<table class="form-table editcomment">

			<tbody>

				<tr>

					<td class="first"><?php _e( "Profile Pic :" , 'wpad' ); ?></td>

					<td>
						<?php 
						$comment_form = new wpad_frontend_comment_form();
						$wpad_advance_comment = new wpad_advance_comment();

						$object = $wpad_advance_comment->get_comment_in_array( $_GET['parentid'] );

						$option = get_option( 'wpad_comment_form' ); 

						$commnet_form_id = $_GET['formid'];

						if( !empty( $option[$commnet_form_id] ) ){

							echo $comment_form->validate_gravatar( $results[0]['comment_author_email'] , $option[$commnet_form_id] , $object , 150 );

						} else {

							echo $comment_form->validate_gravatar( $results[0]['comment_author_email'] , null , $object , 150 );

						}

						
						?>
					</td>

				</tr>

				<tr>

					<td class="first"><?php _e( "Name :" , 'wpad' ); ?></td>

					<td><?php echo $results[0]['comment_author']?></td>

				</tr>

				<tr>

					<td class="first">

					<?php _e( "E-mail" , 'wpad' ); ?> ( <a href="mailto:<?php echo $results[0]['comment_author_email']?>"><?php _e( "Send E-mail" , 'wpad' ); ?></a> ) :</td>

					<td><?php echo $results[0]['comment_author_email']?></td>

				</tr>

				<tr>

					<td class="first">

					<?php _e( "Comment :" , 'wpad' ); ?></td>

					<td><p><?php echo $results[0]['comment_content']?></p></td>

				</tr>

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

					<?php _e( "Submitted On :" , 'wpad' ); ?></td>

					<td><p><?php echo date( 'M d, Y \a\t g:i A' , strtotime($results[0]['comment_date']) ); ?></p></td>

				</tr>

				<?php 

				if( $results[0]['comment_parent'] != 0 ){ ?>

					<tr>

						<td class="first"><?php _e( "In Reply To :" , 'wpad' ); ?></td>

						<td><a href="#"><?php echo get_comment_author( $results[0]['comment_parent'] ); ?></a></td>

					</tr>

					<?php 

				} ?>

			</tbody>

		</table>

		<?php

	}

	function update_notice( $status ){ 

		if( $status == 'updated' ) { ?>

			<div class="updated notice notice-success is-dismissible below-h2" id="message">

				<p><?php _e( "You have successfully replied to this comment." , 'wpad' ); ?></p>

				<button class="notice-dismiss" type="button">

					<span class="screen-reader-text"><?php _e( "Dismiss this notice." , 'wpad' ); ?></span>

				</button>

			</div>

			<?php 

		}

	}

	function reply_comment_form(){ 

		global $wpdb;

		$comment_id = $_GET['parentid']; 

		$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments

			WHERE `comment_ID` = {$comment_id}" , ARRAY_A ); 

		// ****************************************** 
		// If no results found show the error message
		// ******************************************

		if( count($results) < 1 ){

			echo '<div class="wpad_access_error"><p>Oops !!! Something Went Wrong. <a href="' . admin_url('admin.php?page=wp_advance_comment') . '">Click Here</a> to go back.</p></div>';

			die;

		} ?>

		<div class="wrap">

			<h2><?php _e( "Reply Form" , 'wpad' ); ?></h2>

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

						$content = __( 'Write Your Comment Here.' , 'wpad' );

						$editor_id = 'reply_comment_textarea';

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

													<input type="radio" value="1" name="comment_status" checked="checked"><?php _e( "Published" , 'wpad' ); ?>

												</label>

												<br>

												<label class="waiting">

													<input type="radio" value="0" name="comment_status"><?php _e( "Draft" , 'wpad' ); ?>

												</label>

											</div>

										</div>

										<div id="major-publishing-actions">

											<div id="publishing-action">

												<img style="display:none" class="wpad_submit_reply_loader" src="<?php echo includes_url('/images/spinner.gif'); ?>">

												<input id="wpad_save_reply" class="button button-primary" type="button" value="Save" name="save">

											</div>

											<div class="clear"></div>

											<div>

												<input type="hidden" name="comment_post_ID" value="<?php echo $results[0]['comment_post_ID'];?>">

												<input type="hidden" name="comment_author" value="<?php echo get_the_author_meta( 'user_login' , get_current_user_id() );?>">

												<input type="hidden" name="comment_author_email" value="<?php echo get_the_author_meta( 'user_email' , get_current_user_id() );?>">

												<input type="hidden" name="comment_author_url" value="http://">

												<input type="hidden" name="comment_parent" value="<?php echo $_GET['parentid']; ?>">

												<input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">

												<input type="hidden" name="comment_author_IP" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>">

												<input type="hidden" name="comment_agent" value="<?php echo $_SERVER['HTTP_USER_AGENT']; ?>">

												<input type="hidden" name="comment_date" value="<?php echo current_time('mysql'); ?>">

											</div>

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

$reply_form = new wpad_reply_form();