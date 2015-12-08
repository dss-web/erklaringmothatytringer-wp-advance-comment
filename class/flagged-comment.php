<?php

/**
* This page will display all the reported comments and its details. 
*/

class wpad_reported_comment{
	
	function __construct(){
		add_action( 'admin_menu', array( $this, 'register_wpad_reported_comment') );
	}

	function register_wpad_reported_comment(){
		add_submenu_page( 'wp_advance_comment', 'Reported Comments', 'Reported Comments', 'manage_options', 'wpad_reported_comment', array($this, 'list_reported_comments'));

	}

	function get_all_comments(){

		global $wpdb;
		$results = $wpdb->get_results( "SELECT DISTINCT comment_post_ID FROM {$wpdb->prefix}comments" , ARRAY_A );

		foreach( $results as $value ){ 

			$post_id = $value['comment_post_ID']; 
			$selected = !empty( $_GET['post_id'] ) ? $_GET['post_id'] : ''; ?>

			<option value="<?php echo $post_id; ?>" <?php selected( $selected, $post_id ); ?>>
				<?php echo get_the_title( $post_id ); ?>
			</option>

			<?php
		}

	}

	function get_reported_comments( $results ){ 

		$count_flag = 0;
		foreach( $results as $value ){
						
			$comment_id = $value['comment_ID'];

			// Check for the flagged commets
			$flagged_reports = get_comment_meta( $comment_id , 'comment_flagged_reports' , true );	

			if( !empty( $flagged_reports ) ){

				$count_flag = $count_flag + count($flagged_reports);

			}
			
			if( !empty( $flagged_reports ) ){

				foreach( $flagged_reports as $key1 => $value1 ){ ?>

					<tr>
						
						<td class="author column-author">
							
							<strong>

								<?php
								$comment_form = new wpad_frontend_comment_form();

								$option = get_option( 'wpad_comment_form' ); 

								$commnet_form_id = get_comment_meta( $comment_id , 'wpad_comment_form_id' , true );

								$form_ids = !empty( $option[$commnet_form_id] ) ? $option[$commnet_form_id] : '';

								$wpad_advance_comment = new wpad_advance_comment();
								$object = $wpad_advance_comment->get_comment_in_array( $comment_id );

								echo $comment_form->validate_gravatar( $value['comment_author_email'] , $form_ids , $object , 32 ); ?>

								<?php echo $value['comment_author']; ?>

							</strong>

							<br>

							<a href="mailto:<?php echo $value['comment_author_email']; ?>">

								<?php echo $value['comment_author_email']; ?>

							</a>

							<br>	

							<?php echo $value['comment_author_IP']; ?>

						</td>

						<td class="comment column-comment">

							<div class="submitted-on">

								<?php _e( "Submitted on" , 'wpad' ); 
								$post_link = $wpad_advance_comment->get_comment_link( $value['comment_post_ID'] , $value['comment_ID'] ); ?>

								<a href="<?php echo $post_link; ?>" target="blank" >

									<?php echo date('d M, Y \a\t g:i a', strtotime( $value['comment_date_gmt'] )); ?>

								</a> 

							</div>

							<p><?php echo $value['comment_content']; ?></p>

						</td>
					
						<td class="report column-report">
							<strong><?php _e( "Option Selected :" , 'wpad' ); ?> </strong>
							<br>
							<p><?php echo $value1['option']; ?></p>

							<?php 
							if( !empty($value1['message']) ){ ?>

								<br><strong><?php _e( "User Message : " , 'wpad' ); ?></strong>
								<br>
								<p><?php echo $value1['message']; ?></p>
								
								<?php 
							} ?>

						</td>

						<td class="response column-response">

							<div class="response-links">

								<span class="post-com-count-wrapper">

									<a href="<?php echo get_edit_post_link( $value['comment_post_ID'] ); ?>" class="comments-edit-item-link">

										<?php echo get_the_title( $value['comment_post_ID'] ); ?> 

									</a>

									<?php $wpad_advance_comment->view_page( $value['comment_post_ID'] ); ?>

									<span class="post-com-count-wrapper">

										<a class="post-com-count post-com-count-approved" href="javascript:void(0)">

											<span class="comment-count-approved"><?php $wpad_advance_comment->comment_count( $value['comment_post_ID'] ); ?></span>

										</a>

									</span>

								</span> 
								
							</div>

						</td>

					</tr>

					<?php
				}

			} 

		}

		if( $count_flag < 1 ){ ?>

			<tr>
				<td colspan="4" style="text-align:center">
					<?php _e( 'No flagged comments found for this post.' , 'wpad' ); ?>
				</td>
			</tr>

			<?php
		}

	}

	function get_wpad_comments(){

		$selected = !empty( $_GET['post_id'] ) ? $_GET['post_id'] : '0';
		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}comments
			WHERE `comment_post_ID` = {$selected}";

		if( !empty( $_GET['comment_id'] ) ){

			$query .= " AND `comment_ID` = {$_GET['comment_id']}";

		}

		$results = $wpdb->get_results( $query , ARRAY_A );

		if( count($results) > 0 ){ 

			$this->get_reported_comments( $results );

		} else { ?>

			<tr>
				<td colspan="4" style="text-align:center">
					
					<?php _e( 'Please select post / page from above select box.' , 'wpad' ); ?>
				</td>
			</tr>

			<?php
		}
	}

	function list_reported_comments(){ ?>
		
		<div class="wrap">
			<h2><?php _e( 'Reported Comments' , 'wpad' ); ?></h2>

			<div class="tablenav top">
				
				<div class="alignleft actions">

					<select name="report_all_post">
						<option value=""><?php _e( 'Choose Post / Page' , 'wpad' ); ?></option>
						<?php $this->get_all_comments(); ?>
					</select>

					<table class="widefat fixed comments striped ">
						
						<thead>
							
							<tr>
								<th><?php _e('Author','wpad'); ?></th>
								<th><?php _e('Comment','wpad'); ?></th>
								<th><?php _e('Reported Message','wpad'); ?></th>
								<th><?php _e('In Response To','wpad'); ?></th>
							</tr>

						</thead>

						<tbody id="the-comment-list2">

							<?php $this->get_wpad_comments(); ?>							

						</tbody>

						<tfoot>
							
							<tr>
								<th><?php _e('Author','wpad'); ?></th>
								<th><?php _e('Comment','wpad'); ?></th>								
								<th><?php _e('Reported Message','wpad'); ?></th>
								<th><?php _e('In Response To','wpad'); ?></th>
							</tr>

						</tfoot>

					</table>

				</div>

			</div>

		</div>

		<?php
	}
}

$wpad_reported_comment = new wpad_reported_comment();