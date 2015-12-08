<?php 

class wpad_advance_comment{

	function __construct() {

		add_action( 'admin_menu', array( $this, 'register_wp_advance_comment') );

		add_action( 'wp_ajax_get_wpad_comments' , array( $this , 'get_wpad_comments' ) );

		add_action( 'wp_ajax_update_comment_status' , array( $this , 'update_comment_status' ) );

		add_action( 'wp_ajax_delete_comment' , array( $this , 'delete_comment' ) );

		add_action( 'wp_ajax_bulk_action' , array( $this , 'bulk_action' ) );

		add_action( 'wp_ajax_wpad_reset_flagged_reports' , array( $this , 'wpad_reset_flagged_reports' ) );

		global $wpdb;

        $this->db = $wpdb;

	}

	function trash_comment_edit_page(){ 

		if( isset( $_GET['action'] ) && $_GET['action'] == 'trash' && isset( $_GET['commentid'] ) ){ 

			global $wpdb; 

			$results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}comments

				WHERE `comment_ID` = {$_GET['commentid']}" , ARRAY_A ); 

			if( count($results) < 1 ){ ?>

				<div class="error notice is-dismissible below-h2" id="moderated">

			    	<p><?php _e( "Sorry we couldn't find the comment you were looking for." , "wpad" ); ?><br></p>

			    	<button class="notice-dismiss" type="button">

			    		<span class="screen-reader-text"><?php _e( "Dismiss this notice." , 'wpad' ); ?></span>

			    	</button>

			    </div>

				<?php

			} else { 

				wp_set_comment_status( $_GET['commentid'] , 'trash' ); ?>

				<div class="updated notice is-dismissible below-h2" id="moderated">

			    	<p><?php _e( "1 comment moved to the Trash." , 'wpad' ); ?><br></p>

			    	<button class="notice-dismiss" type="button">

			    		<span class="screen-reader-text"><?php _e( "Dismiss this notice." , 'wpad' ); ?></span>

			    	</button>

			    </div>

				<?php

			} 

		}

	}

	function plugin_url(){

		return plugin_dir_url( dirname( __FILE__ ) );

	}

	function bulk_action(){

		$action = $_POST['do_action'];

		$ids = $_POST['comment_ids'];

		if( $action == 'unapprove' ){

			foreach( $ids as $id ){

				$this->update_comment_status_bulk( $id, 'hold' );

			}

		} elseif( $action == 'approve' ){

			foreach( $ids as $id ){

				$this->update_comment_status_bulk( $id, 'approve' );

			}

		} elseif( $action == 'spam' ){

			foreach( $ids as $id ){

				$this->update_comment_status_bulk( $id, 'spam' );

			}

		} elseif( $action == 'delete_permanently' ){

			foreach( $ids as $id ){

				wp_delete_comment( $id );

			}

		} elseif( $action == 'restore' ){

			foreach( $ids as $id ){

				wp_set_comment_status( $id , 'hold' );

			}

		} elseif( $action == 'spam' ){

			foreach( $ids as $id ){

				wp_set_comment_status( $id , 'spam' );

			}

		} elseif( $action == 'not_spam' ){

			foreach( $ids as $id ){

				wp_set_comment_status( $id , 'hold' );

			}

		} else {

			foreach( $ids as $id ){

				$this->update_comment_status_bulk( $id, 'trash' );

			}

		}

	}

	function sent_mail_bulk_approve( $comment_id , $status ){

		if( $status == 'approve' ){
			$wpad_save_data = new wpad_save_data();
			$wpad_save_data->send_mail_after_approve_comment( $comment_id );
		}

	}

	function update_comment_status_bulk( $comment_id, $status ){

		wp_set_comment_status( $comment_id, $status );
		$this->sent_mail_bulk_approve( $comment_id ,  $status );

	}

	function delete_comment(){

		$comment_id = $_POST['comment_id'];

		wp_delete_comment( $comment_id );

		$data = array(

			'status' => $this->get_comments_status()

		);

		echo json_encode( $data );

		die;

	}

	function row_action_trash( $value ){ ?>

		<div class="row-actions">

			<span class="spam">

				<a comment_id="<?php echo $value->comment_ID;?>" class="" onclick="update_comment_status('spam', jQuery(this))" href="javascript:void(0)">

					<?php _e( "Spam" , 'wpad' ); ?>

				</a>

			</span>

			<span class="untrash unapprove"> | 

				<a comment_id="<?php echo $value->comment_ID;?>" class="" onclick="update_comment_status('hold', jQuery(this), 'remove')" href="javascript:void(0)">

					<?php _e( "Restore" , 'wpad' ); ?>

				</a>

			</span>

			<span class="delete"> | 

				<a class="" href="javascript:void(0)" comment_id="<?php echo $value->comment_ID;?>" onclick="delete_comment( jQuery(this) )">

					<?php _e( "Delete Permanently" , 'wpad' ); ?>

				</a>

			</span>

		</div>

		<?php

	}

	function row_action_spam( $value ){ ?>

		<span class="unspam unapprove">

			<a href="javascript:void(0)" comment_id="<?php echo $value->comment_ID;?>" class="" onclick="update_comment_status('hold', jQuery(this), 'remove')">

				<?php _e( "Not Spam" , 'wpad' ); ?>

			</a>

		</span>

		<span class="delete"> | 

			<a class="" href="javascript:void(0)" comment_id="<?php echo $value->comment_ID;?>" onclick="delete_comment( jQuery(this) )">

				<?php _e( "Delete Permanently" , 'wpad' ); ?>

			</a>

		</span>

		<?php

	}

	function row_action_pending_all( $value ){ 

		$comment_object = get_option( 'wpad_comment_form' ); 

		$commnet_form_id = get_comment_meta( $value->comment_ID , 'wpad_comment_form_id' , true ); ?>

		<span class="approve">

			<a comment_id="<?php echo $value->comment_ID;?>" class="" onclick="update_comment_status('approve' , jQuery(this) )" href="javascript:void(0)">

				<?php _e( "Approve" , 'wpad' ); ?>

			</a>

		</span>

		<span class="unapprove">

			<a comment_id="<?php echo $value->comment_ID;?>" class="" onclick="update_comment_status('hold', jQuery(this))" href="javascript:void(0)">

				<?php _e( "Unapprove" , 'wpad' ); ?>

			</a>

		</span>

		<span class="reply hide-if-no-js"> | 

			<a href="<?php echo admin_url('admin.php?page=wpad_reply_form') . '&parentid=' . $value->comment_ID . '&formid=' . $commnet_form_id; ?>" title="Reply to this comment" class="">

				<?php _e( "Reply" , 'wpad' ); ?>

			</a>

		</span>

		<span class="edit"> | 

			<a title="Edit comment" href="<?php echo admin_url('admin.php?page=wpad_saved_edit_form') . '&commentid=' . $value->comment_ID . '&formid=' . $commnet_form_id; ?>">

				<?php _e( "Edit" , 'wpad' ); ?>

			</a>

		</span>

		<span class="spam"> | 

			<a comment_id="<?php echo $value->comment_ID;?>" class="" onclick="update_comment_status('spam', jQuery(this),'remove')" href="javascript:void(0)">

				<?php _e( "Spam" , 'wpad' ); ?>

			</a>

		</span>

		<span class="trash"> | 

			<a comment_id="<?php echo $value->comment_ID;?>" class="" onclick="update_comment_status('trash', jQuery(this),'remove')" href="javascript:void(0)">

				<?php _e( "Trash" , 'wpad' ); ?>

			</a>

		</span>

		<?php

	}

	function update_comment_status(){

		$status = $_POST['comment_status'];

		$comment_id = $_POST['comment_id'];

		do_action( 'wpad_before_comment_status_change' , $comment_id );

		wp_set_comment_status( $comment_id, $status );

		do_action( 'wpad_after_comment_status_change' , $comment_id );

		$data = array(

			'status' => $this->get_comments_status()

		);

		echo json_encode( $data );

		die;

	}

	function get_comments_status(){

		$status = array('hold','approve', 'spam', 'trash');

		$results = array();

		foreach( $status as $value ){

			$args = array(

				'status' => $value

			);

			$comments = get_comments( $args ); 

			$results[$value] = count($comments);

		}

		return $results;

	}

	function reply_to( $comment_parent , $post_link ){ 

		$comments = get_comment( $comment_parent );

		if( !empty( $comments ) ){

			echo '| In reply to ';

			echo '<a target="blank" href="' . $post_link . '">' . $comments->comment_author . '</a>';

		}

	}

	function view_page( $post_id ){

		echo '<a href="' . get_permalink( $post_id ) . '">View Page</a>';

	}

	function comment_count( $post_id ){

		$args = array(

			'post_id' => $post_id

		);

		$comments = get_comments( $args ); 

		echo count( $comments );

	}

	function get_avatar( $user_id ){

		echo get_avatar( $user_id , 32 );

	}

	function comment_status_class( $value , $comment_status){

		if( $value->comment_approved == 0 && $comment_status != 'spam' && $comment_status != 'trash' ){

			return 'unapproved';

		}

	}

	function get_comment_users(){

		$table = $this->db->prefix . 'comments';

		$sql = "SELECT DISTINCT `user_id` FROM {$table}";

		$results = $this->db->get_results( $sql , ARRAY_A );

		$users = array();

		foreach( $results as $user_id ){

			$users[] = $user_id['user_id']; // Get User Ids

		}

		return $this->get_user_name($users);

	}

	function get_user_name( $user_id ){

		$names = array();

		foreach( $user_id as $id){

			if( $id == 0 ){

				$names[] = array( 'name' => 'Guest', 'id' => 'guest' ); ;

			} else {

				$names[] = array('name' => get_the_author_meta( 'user_login' , $id ) , 'id' => $id );

			}

		}

		return $names;

	}

	function get_post_names(){

		$table = $this->db->prefix . 'comments';

		$sql = "SELECT DISTINCT `comment_post_ID` FROM {$table}";

		$results = $this->db->get_results( $sql , ARRAY_A );

		$post = array();

		foreach( $results as $post_id ){

			$post[] = $post_id['comment_post_ID']; // Get User Ids

		}

		return $this->get_post_name($post);

	}

	function get_post_name( $post_ids ){

		$names = array();

		foreach( $post_ids as $id ){

			$title = get_the_title( $id );

			$names[] = array( 'title' => $title , 'id' => $id );

		}

		return $names;

	}

	function get_all_user_ids(){

		global $wpdb;

		$results = $wpdb->get_results("SELECT DISTINCT ID FROM {$wpdb->prefix}users" , ARRAY_A );

		if( !empty($results) ){

			$ids = array();

			foreach( $results as $value ){

				$ids[] = $value['ID'];

			}

		}

		return $ids;

	}

	function get_the_user_name( $comment_user ){

		if( $comment_user == 'guest' ){

			return 'Guest';

		} else {

			return get_the_author_meta( 'user_login' , $comment_user );

		}

	}

	function showing_results( $comment_status , $comment_user , $post , $order_by , $order ){ 

		ob_start(); 

		$comment_status = empty($comment_status) ? 'all' : $comment_status;

		if( !empty( $comment_status ) ){ 

			if( $comment_status == 'all' ){

				$default_status = true;

				$disabled_status = 'disabled';

				$button = '';

			} else {

				$disabled_status = '';

				$button = 'button-primary button-large';

				$default_status = false;

			} ?>

			<button class="button <?php echo $button; ?>" <?php echo $disabled_status; ?> type="button" value="status">

				<strong><?php _e( "Status" , 'wpad' ); ?> :</strong> <?php echo $comment_status; 

				if( $default_status != true ){ ?>

					<span class="dashicons dashicons-dismiss"></span>

					<?php 

				} ?>

			</button>

			<?php 

		} 

		$user_name = empty( $comment_user ) ? 'all' : $this->get_the_user_name( $comment_user ); 

		if( $user_name == 'all' || $comment_user == 'all' ){

			$default_user = true;

			$disabled_user = 'disabled';

			$button = '';

		} else {

			$disabled_user = '';

			$button = 'button-primary button-large';

			$default_user = false;

		} ?>

		<button class="button <?php echo $button; ?>" type="button" value="user" <?php echo $disabled_user; ?>>

			<strong><?php _e( "User" , 'wpad' ); ?> :</strong> <?php echo ( empty( $user_name ) ? 'all' : $user_name ); 

			if( $default_user != true ){ ?>

				<span class="dashicons dashicons-dismiss"></span>

				<?php 

			} ?>

		</button>

		<?php

		$post_title = !empty( $post ) ? $post : 'all';  

		if( $post_title == 'all' ){

			$default_post = true;

			$disabled_post = 'disabled';

			$button = '';

		} else {

			$disabled_post = '';

			$default_post = false;

			$button = 'button-primary button-large';

		} ?>

			<button class="button <?php echo $button; ?>" type="button" value="post" <?php echo $disabled_post; ?>>

				<strong><?php _e( "Post" , 'wpad' ); ?> :</strong> <?php echo ( $post_title == 'all' ? 'all' : get_the_title( $post_title ) ); 

				if( $default_post != true ){ ?>

					<span class="dashicons dashicons-dismiss"></span>

					<?php 

				} ?>

			</button>

			<?php 

		if( !empty( $order_by ) ){ 

			if( $order_by == 'comment_author'){

				$default_author = true;

				$disabled_author = 'disabled';

				$button = '';

			} else {

				$button = 'button-primary button-large';

				$default_author = false;

				$disabled_author = '';

			}?>

			<button class="button <?php echo $button; ?>" <?php echo $disabled_author; ?> type="button" value="order_by">

				<strong><?php _e( "Order By" , 'wpad' ); ?> :</strong> <?php echo  str_replace('_', ' ', $order_by ); 

				if( $default_author != true ){ ?>

					<span class="dashicons dashicons-dismiss"></span>

					<?php 

				} ?>

			</button>

			<?php 

		} 

		if( !empty( $order ) ){

			if( $order == 'DESC'){

				$default_order = true;

				$disabled_order = 'disabled';

				$button = '';

			} else {

				$button = 'button-primary button-large';

				$disabled_order = '';

				$default_order = false;

			} ?>

			<button class="button <?php echo $button; ?>" <?php echo $disabled_order; ?> type="button" value="order">

				<strong><?php _e( "Order" , 'wpad' ); ?> :</strong> <?php echo $order; 

				if( $default_order != true ){ ?>

					<span class="dashicons dashicons-dismiss"></span>

					<?php 

				} ?>

			</button>

			<?php 

		}

		return $content = ob_get_clean();

	}

	function get_comment_link( $post_id , $comment_id ){

		return get_permalink( $post_id ) . '#wpad_comment_' . $comment_id;

	}

	function get_wpad_comments(){ 

		$page_no = empty($_POST['page']) ? '0' : $_POST['page'];

		$range = 10;

		if( $page_no == 0 || $page_no == 1 ){

			$page_no = 0;

		} else {

			$page_no = ( $page_no - 1 )  * $range;

		}

		$comment_status = !empty( $_POST['comment_status'] ) ? $_POST['comment_status'] : '';

		$order = ( !empty( $_POST['comment_order'] )  && $_POST['comment_order'] != 'all' ) ? $_POST['comment_order'] : 'DESC';

		$comment_order_by = ( !empty( $_POST['comment_order_by'] )  && $_POST['comment_order_by'] != 'all' ) ? $_POST['comment_order_by'] : 'comment_author';

		$comment_post = ( !empty ( $_POST['comment_post'] )  && $_POST['comment_post'] != 'all' ) ? $_POST['comment_post'] : 0;

		$comment_user = ( !empty( $_POST['comment_user'] ) && $_POST['comment_user'] != 'all' ) ? $_POST['comment_user'] : '';

		$args = array(

			'order' => $order,

			'status' => $comment_status,

			'number' => $range,

			'orderby' => $comment_order_by,

			'offset' => $page_no,

			'post_id' => $comment_post,

			'user_id' => $comment_user

		);

		if( $comment_user == 'guest' ) {

			$ids = $this->get_all_user_ids();

			$args['author__not_in'] = $ids;

		}

		$comments = get_comments( $args ); 

		$content = '';

		if( !empty( $comments ) ):

			foreach ( $comments as $value ){ 

				$status_class = $this->comment_status_class( $value , $comment_status);

				ob_start();

				?>

				<tr class="<?php echo $status_class; ?>">

					<th class="check-column" scope="row">		

						<input type="checkbox" value="<?php echo $value->comment_ID; ?>" name="check_ids">

					</th>

					<td class="author column-author">

						<strong>

							<?php
							$comment_form = new wpad_frontend_comment_form();

							$option = get_option( 'wpad_comment_form' ); 

							$commnet_form_id = get_comment_meta( $value->comment_ID , 'wpad_comment_form_id' , true );
							
							$form_ids = !empty($option[$commnet_form_id]) ? $option[$commnet_form_id] : '';
							
							$object = $this->get_comment_in_array( $value->comment_ID );

							echo $comment_form->validate_gravatar( $value->comment_author_email , $form_ids , $object , 32 ); ?>

							<?php echo $value->comment_author; ?>

						</strong>

						<br>

						<a href="mailto:<?php echo $value->comment_author_email; ?>">

							<?php echo $value->comment_author_email; ?>

						</a>

						<br>	

						<?php echo $value->comment_author_IP; ?>

					</td>

					<td class="comment column-comment">

						<div class="submitted-on">

							<?php _e( "Submitted on" , 'wpad' ); ?> 

							<?php $post_link = $this->get_comment_link( $value->comment_post_ID , $value->comment_ID ); ?>

							<a href="<?php echo $post_link; ?>" target="blank" >

								<?php echo date('d M, Y \a\t g:i a', strtotime($value->comment_date_gmt)); ?>

							</a> 

							<?php $this->reply_to( $value->comment_parent , $post_link ); ?>

						</div>

						<p><?php echo $value->comment_content; ?></p>

						<div class="row-actions">

							<?php 

							if( $comment_status == 'all' || $comment_status == '' || $comment_status == 'hold' || $comment_status == 'approve' ){

								$this->row_action_pending_all( $value );

							} elseif( $comment_status == 'spam' ){

								$this->row_action_spam( $value );

							} elseif( $comment_status == 'trash' ){

								$this->row_action_trash( $value );

							}

							$this->reset_flagged_reports( $value->comment_ID );

							?>

						</div>

					</td>

					<td class="response column-response">

						<div class="response-links">

							<span class="post-com-count-wrapper">

								<a href="<?php echo get_edit_post_link( $value->comment_post_ID ); ?>" class="comments-edit-item-link">

									<?php echo get_the_title( $value->comment_post_ID ); ?> 

								</a>

								<?php $this->view_page( $value->comment_post_ID ); ?>

								<span class="post-com-count-wrapper">

									<a class="post-com-count post-com-count-approved" href="javascript:void(0)">

										<span class="comment-count-approved"><?php $this->comment_count( $value->comment_post_ID ); ?></span>

									</a>

								</span>

							</span> 
							
						</div>

					</td>

					<td class="wpad_count_reported">
			
						<?php 
						$this->count_reported( $value->comment_ID , $value->comment_post_ID ); 
						?>
					
					</td>

				</tr>

				<?php 

				$content .= ob_get_clean();

			}

			$total_comments = $this->count_total_comments( $args , $_POST['page'] );

		else:

			$content = '<tr><td colspan="4">' . __( "No comments found." , 'wpad' ) . '</td></tr>';

			$total_comments = '';

		endif;

		$data = array(

			'content' => $content,

			'status' => $this->get_comments_status(),

			'pagination' => $total_comments,

			'showing_results' => $this->showing_results( $comment_status , $comment_user , $comment_post , $comment_order_by , $order )

		);

		echo json_encode( $data );

		die;

	}

	function wpad_reset_flagged_reports(){

		$comment_id = $_POST['comment_id'];

		delete_comment_meta( $comment_id, 'comment_flagged_reports' );
		delete_comment_meta( $comment_id, 'no_of_flags' );

		die;

	}

	function reset_flagged_reports( $comment_id ){ 

		$reports = get_comment_meta( $comment_id, 'comment_flagged_reports', true ); 

		if( !empty($reports) ){ ?>

			<span class="reset_flagged_reports"> | 
				<a href="javascript:void(0)" id="reset_flagged_reports" comment_id="<?php echo $comment_id; ?>">Reset Flagged Reports</a>
			</span>

			<?php
		}
	}

	function count_reported( $comment_id , $post_id ){

		$count_report = get_comment_meta( $comment_id, 'no_of_flags', true );

		if( !empty($count_report) ){ ?>
			
			<a href="<?php echo admin_url() . 'admin.php?page=wpad_reported_comment&post_id=' . $post_id . '&comment_id=' . $comment_id;?>">
				<?php echo $count_report; ?>
			</a>

			<?php
		}	

	}

	function get_comment_in_array( $comment_id ){

		global $wpdb;
		$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments
			WHERE `comment_ID` = {$comment_id}" , ARRAY_A );
		return $results[0];

	}

	function count_total_comments( $args , $page_no ){

		$args['number'] = ''; // All Posts

		unset( $args['offset'] ) ;

		$comments = get_comments( $args ); 

		$count_posts = count($comments);

	   	if($count_posts != 0){

            $page = ceil($count_posts/10);

            $range = 0;

            $range_increase = $range + 1;

            $showitems = ($range * 2)+1;

            $pagination = '<span class="displaying-num">' . $count_posts . ' items</span>';

            $pagination .= '<li><a class="not_click">' . 'Page ' . $page_no . ' of ' . $page . '</a></li>';

            if($page_no > 2 && ($page_no > $range_increase) && ($showitems < $page)){

                $pagination .= '<li><a href="javascript:void(0)" page_no="1">&laquo;</a></li>';

            }

            if($page_no > 1 && $showitems < $page){

                $pagination .= '<li><a href="javascript:void(0)" page_no="' . ($page_no - 1) . '">&lsaquo;</a></li>';

            }

            for($i = 1; $i <= $page; $i++){ 

                if (1 != $page &&( !($i >= $page_no+$range+1 || $i <= $page_no-$range-1) || $page <= $showitems )){

                    if($page_no == $i){

                          $active = 'active not_click';

                    } else{

                          $active ='';

                    }

                    $pagination .= '<li><a class="' . $active . '" href="javascript:void(0)" page_no="' . $i . '">' . $i . '</a></li>';

                }

            }

            if ($page_no < $page && $showitems < $page){

                $pagination .= '<li><a href="javascript:void(0)" page_no="' . ($page_no + 1) . '">&rsaquo;</a></li>';

            }

            if ($page_no < $page-1 &&  $page_no+$range-1 < $page && $showitems < $page){

                $pagination .= '<li><a href="javascript:void(0)" page_no="' . $page . '">&raquo;</a></li>';

            }

        	return $pagination;

        }

	}

	function register_wp_advance_comment() {

		add_menu_page( 'WP Advanced Comment', 'WP Advanced Comment', 'manage_options', 'wp_advance_comment', array(&$this, 'backend_settings') , 'dashicons-testimonial' , 25 );

		$this->pending_count();

	}

	function pending_count(){

		global $menu;

		global $wpdb;

		$results = $wpdb->get_results( "SELECT `comment_ID` FROM {$wpdb->prefix}comments

			WHERE `comment_approved` = '0'" , ARRAY_A );

		if( count( $results )  > 0 ){

			foreach( $menu as $key => $value ){

				if( $value[2] == 'wp_advance_comment' ){

					$menu[$key][0] .= " <span class='update-plugins'><span class='pending-count'>" . count( $results ) . '</span></span>';

				}

			}

			return $menu;

		}

	}

	function backend_settings(){ 

		include 'inc/all-comments.php';

	}

}

$comment_advance = new wpad_advance_comment();