<?php 
$form = new wpad_edit_comment_form();
$id = !empty( $_GET['comment_id'] ) ? $_GET['comment_id'] : '';
$comment_forms = get_option('wpad_comment_forms_on_posts');
$comment_forms_on_posts = $form->get_comments_show( $comment_forms , $id );
$form->check_previlage();
?>
<div class="wrap">
	<?php $form->get_form_title(); ?>
	<?php 
	if( isset( $_GET['status'] ) && $_GET['status'] == 'updated' ): ?>
		<div class="updated notice notice-success is-dismissible below-h2" id="message">
			<p><?php _e( 'Comment form updated.' , 'wpad' ); ?></p>
			<button class="notice-dismiss" type="button">
				<span class="screen-reader-text"><?php _e( 'Dismiss this notice.' , 'wpad' ); ?></span>
			</button>
		</div>
		<?php 
	endif; ?>
	<form id="post" method="post" name="post">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
							<?php $title = $form->get_comment_title( $id ); ?>
							<input type="text" autocomplete="off" id="title" size="30" name="comment_title" value="<?php echo $title; ?>">
						</div>
					</div>
				</div>
				<div class="postbox-container" id="postbox-container-1">
					<?php $form->display_woocommerce_message(); ?>
					
					<div class="meta-box-sortables ui-sortable" id="side-sortables">
						<div class="postbox" id="wpuf-metabox-fields">
							<h3 class="hndle">
								<span><?php _e( 'Comment Elements' , 'wpad' ); ?></span>
							</h3>
							<div class="inside">
								<h2><?php _e( 'Free Plan' , 'wpad' ); ?></h2>
								<div class="wpuf-form-buttons comment_form_buttons">
									<button type="button" class="button" value="text"><?php _e( 'Text' , 'wpad' ); ?></button>
						            <button type="button" class="button" value="textarea"><?php _e( 'Textarea' , 'wpad' ); ?></button>
						            <button type="button" class="button" value="radio"><?php _e( 'Radio Button' , 'wpad' ); ?></button>
						            <button type="button" class="button" value="checkbox"><?php _e( 'Checkbox' , 'wpad' ); ?></button>
						            <button type="button" class="button" value="select"><?php _e( 'Dropdown' , 'wpad' ); ?></button>
						            <button type="button" class="button" value="email"><?php _e( 'Email' , 'wpad' ); ?></button>
						            <button type="button" class="button" value="url"><?php _e( 'Url' , 'wpad' ); ?></button>
						            <button type="button" class="button" value="multi_select"><?php _e( 'Multi Select' , 'wpad' ); ?></button>
						           
    							</div>
    							<h2><?php _e( 'Others' , 'wpad' ); ?></h2>
    							<div class="wpuf-form-buttons comment_form_buttons">
    								<button type="button" class="button" value="section_break"><?php _e( 'Section Break' , 'wpad' ); ?></button>
    								<button type="button" class="button" value="html"><?php _e( 'HTML' , 'wpad' ); ?></button>
    								<button type="button" class="button" value="user_image"><?php _e( 'User Image' , 'wpad' ); ?></button>
    							</div>
    							<h2><?php _e( 'Premium Plan' , 'wpad' ); ?></h2>
								<div class="wpuf-form-buttons">
									<button class="button" disabled=""><?php _e( 'Image Upload' , 'wpad' ); ?></button>
									<button class="button" disabled=""><?php _e( 'File Upload' , 'wpad' ); ?></button>
									<button class="button" disabled=""><?php _e( 'Star Rating' , 'wpad' ); ?></button>
						            <button class="button" disabled=""><?php _e( 'Datepicker' , 'wpad' ); ?></button>
						            <button class="button" disabled=""><?php _e( 'Google Map' , 'wpad' ); ?></button>
						            <button class="button" disabled=""><?php _e( 'Really Simple Captcha' , 'wpad' ); ?></button>
						            <button class="button" disabled=""><?php _e( 'reCaptcha' , 'wpad' ); ?></button>
    							</div>
    							<div class="wpad_other_options">
	    							<h2><?php _e( 'How do you want to show the comment form ?' , 'wpad' ); ?></h2>
	    							<p class="description">
	    								<strong>--------------------------- Note ---------------------------</strong> 
	    								<br><br>
		    							1) To show the comment form <code>comment_form()</code> tag should be enable on the .php file where the post will be display or you can use above shortcode manually.
		    							<br><br>
	    							
		    							2) Do not use shortcode and select the options below. It will display two comment forms and it will create conflict.
		    							<br><br>
	    								3) If the comment form doesn't display on some pages then <code>comment_form()</code> tag is not used on that file. You can put <code>comment_form()</code> tag on the file or use the shortcode above.
	    							</p>
	    							<br>
    								<label>
    									<input type="radio" name="replace_comment_form" value="show_on_all" <?php echo checked( $comment_forms_on_posts , 'show_on_all' ); ?>>Replace default wordpress comment form.
    								</label>
    								<p class="description">
    									<?php _e( '( It will override the default comment form for all pages & posts. )' , 'wpad' );?>
    								</p>
    								
    								<?php $form->default_comment_exist_warning(); ?>
    								<br>
    								<label>
    									<input type="radio" name="replace_comment_form" value="enable_certain_pages" <?php echo checked( $comment_forms_on_posts , 'enable_certain_pages' ); ?>>Choose Pages / Posts
    								</label>
    								<p class="description">
    									<?php _e( '( The comment form will be displayed on certain pages / posts. )' , 'wpad' );?>
    								</p>
    								<?php 
    								if( $comment_forms_on_posts == 'enable_certain_pages' ){
    									$show_hide_dropdown = '';
    								} else {
    									$show_hide_dropdown = 'display:none';
    								}?>
    								<div class="wpad_pages_posts" style="<?php echo $show_hide_dropdown; ?>">
    									<?php $form->get_all_post_types( $comment_forms_on_posts , $_GET['comment_id'] ); ?>
    								</div>
    								<br>
    								
    								<label>
    									<input type="radio" name="replace_comment_form" value="none" <?php echo checked( $comment_forms_on_posts , 'none' ); ?>>None of above
    								</label>
    								<p class="description">
    									<?php _e( '(Use above shortcode manually.The comment form will be shown where the shortcode is used.)' , 'wpad' );?>
    								</p>
    							</div>
    							<div id="submitpost" class="submitbox">
    								<div id="major-publishing-actions">
        								<div id="publishing-action">
        									<img src="<?php echo admin_url() . 'images/spinner.gif'; ?>" style="display:none">
                							<input type="button" value="Update" id="publish" class="button button-primary button-large" name="save_comment">
                            			</div>
        								<div class="clear"></div>
				            		</div>
				        		</div>
    						</div>
						</div>
					</div>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<div id="wpuf-metabox-editor" class="postbox ">
							<div class="inside">
								<h2 class="nav-tab-wrapper">
						            <a id="wpad-editor-tab" class="nav-tab nav-tab-active" for="#wpad-metabox" href="javascript:void(0)"><?php _e( 'Form Editor' , 'wpad' ); ?></a>
						            <a id="wpad-post-settings-tab" class="nav-tab" for="#wpad-metabox-settings" href="javascript:void(0)"><?php _e( 'Form Settings' , 'wpad' ); ?></a>
						            <a id="wpad-post-settings-tab" class="nav-tab" for="#wpad-pagination-settings" href="javascript:void(0)"><?php _e( 'Pagination' , 'wpad' ); ?></a>
						            <a id="wpad-notification-tab" class="nav-tab" for="#wpad-metabox-notification" href="javascript:void(0)"><?php _e( 'Notification' , 'wpad' ); ?></a>
						            <a id="wpad-like-dislike-tab" class="nav-tab" for="#wpad-like-dislike" href="javascript:void(0)"><?php _e( 'Like/Dislike Button' , 'wpad' ); ?></a>
						            <a id="wpad-like-dislike-tab" class="nav-tab" for="#wpad-custom-layout" href="javascript:void(0)"><?php _e( 'Custom Layout' , 'wpad' ); ?></a>
                    			</h2>
                    			<div class="tab-content">
									<div id="wpad-metabox" style="display: block;">
										<div style="margin-bottom: 10px">
								            <button type="button" class="button wpad_toggle_all"><?php _e( 'Toggle All' , 'wpad' ); ?></button>
								        </div>
								        <div class="wpuf-updated" style="display: block;">
								            <p><?php _e( 'Click on a form element to add to the editor' , 'wpad' ); ?></p>
								        </div>
								        <ul id="wpuf-form-editor" class="wpuf-form-editor unstyled sortable">
								        	<!-- If the datas are on the database then it will displaly -->
								        	<?php 
								        	$form->comment_section( $id );
								        	$form->backend_display_form_elements( $id ); 
								        	?>
										</ul>
									</div>
									<div id="wpad-metabox-settings" style="display:none">
										<table class="form-table">
											<?php $option = get_option( 'wpad_comment_form' ); 
											$comment_status = !empty($option[$id]['other']['comment_status']) ? $option[$id]['other']['comment_status'] : '';
											$guest_comment = !empty($option[$id]['other']['guest_comment']) ? $option[$id]['other']['guest_comment'] : '';
											$user_name_show = !empty( $option[$id]['other']['user_name_show'] ) ? $option[$id]['other']['user_name_show'] : '';
											$user_email_show = !empty( $option[$id]['other']['user_email_show'] ) ? $option[$id]['other']['user_email_show'] : '';
											$comment_time = !empty( $option[$id]['other']['comment_time'] ) ? $option[$id]['other']['comment_time'] : '2';
											$comment_position = !empty( $option[$id]['other']['comment_position'] ) ? $option[$id]['other']['comment_position'] : '1';
											$submit_button = !empty( $option[$id]['other']['submit_label'] ) ? $option[$id]['other']['submit_label'] : 'Submit';
											$no_of_column = !empty( $option[$id]['other']['no_of_column'] ) ? $option[$id]['other']['no_of_column'] : 2;
											$comment_listing = !empty( $option[$id]['other']['comment_listing'] ) ? $option[$id]['other']['comment_listing'] : 'disable';
											$comment_order_by = !empty( $option[$id]['other']['comment_order_by'] ) ? $option[$id]['other']['comment_order_by'] : 'DESC';
											$auto_approve = !empty( $option[$id]['other']['comment_automatically_approve'] ) ? $option[$id]['other']['comment_automatically_approve'] : '';
											$display_roles = !empty( $option[$id]['other']['display_roles'] ) ? $option[$id]['other']['display_roles'] : '';
											?>
											<tbody>
												
												<!-- 
												  	Display Roles 
												-->

												<tr>
													<th><?php _e( 'Roles' , 'wpad' ); ?></th>
													<td>
														<label>
															<input <?php checked( $display_roles , 'disable' );?> type="checkbox" value="disable" name="display_roles">
															Disable
														</label>
														<p class="description">
															If enabled the user roles will be shown. eg. administrator, subscriber, author etc. To change the user roles background color. Go to WP Advanced Comment > Settings > Roles Tab and choose your colors.
														</p>
													</td>
												</tr>

												<!-- 
												  Moderation 
												-->

												<tr>
													<th><?php _e( 'Moderation' , 'wpad' ); ?></th>
													<td>
														<label>
															<input <?php checked( $auto_approve , 'yes' );?> type="checkbox" name="comment_automatically_approve" value="yes">
															Comments must be automatically approved.
														</label>   
													</td>
												</tr>

												<!-- 
												  Comment Status 
												-->
												<tr>
													<th><?php _e( 'Comment Status' , 'wpad' ); ?></th>
													<td>
														<select name="comment_status">
															<option <?php selected( $comment_status , 'published' );?> value="published"><?php _e( 'Published' , 'wpad' ); ?></option>
															<option <?php selected( $comment_status , 'unpublished' );?> value="unpublished"><?php _e( 'Unpublished' , 'wpad' ); ?></option>
															<option <?php selected( $comment_status , 'show_comments' );?> value="show_comments"><?php _e( 'Unpublished form but show the comments' , 'wpad' ); ?></option>
														</select>
													</td>
												</tr>
												<!-- 
												  	Show Comments from relative forms. 
												-->
												<tr>
													<th>
														<?php _e( 'Comment Listings' , 'wpad' ); ?>
													</th>
													<td>
														<label>
															<input type="radio" name="comment_listing" value="enable" <?php checked( $comment_listing, 'enable' );?> > <?php _e( 'Enable' , 'wpad' ); ?>
														</label>
													
														<p class="description"><?php _e( 'Enable this if you want to show the comments that has been submitted form this form only.' , 'wpad' ); ?></p>
														<br>
														<label>
															<input type="radio" name="comment_listing" value="disable" <?php checked( $comment_listing, 'disable' );?> > <?php _e( 'Disable' , 'wpad' ); ?>
														</label>
													
														<p class="description"><?php _e( 'Disabling this will show all the comments on a post from different comment forms.' , 'wpad' ); ?></p>
													</td>
												</tr>
									
												<!-- 
												  	Comments Order By 
												-->
												<tr>
													<th>
														<?php _e( 'Comment Listing Order' , 'wpad' ); ?>
													</th>
													<td>
														<label>
															<input type="radio" name="comment_order_by" value="ASC" <?php checked( $comment_order_by, 'ASC' );?> > <?php _e( 'Latest Comments Last' , 'wpad' ); ?>
														</label>
														<br>
														<label>
															<input type="radio" name="comment_order_by" value="DESC" <?php checked( $comment_order_by, 'DESC' );?> > <?php _e( 'Latest Comments First' , 'wpad' ); ?>
														</label>
													</td>
												</tr>
												<!-- 
													Guest Comment 
												-->
												<tr>
													<th><?php _e( 'Guest Comment' , 'wpad' ); ?></th>
													<td>
														<label>
															<input type="checkbox" name="guest_comment" <?php checked( $guest_comment , 'disable' );?> value="disable"><?php _e( 'Disable Guest Comments' , 'wpad' ); ?>
														</label>
														<p class="description"><?php _e( 'Unregistered users will be able to submit comments' , 'wpad' ); ?></p>
													</td>
												</tr>
												<!--  
													Show Fields to Logged in Users
												-->
												<tr>
													<th><?php _e( 'Show Fields to Logged in Users' , 'wpad' ); ?></th>
													<td>
														<label>
															<input type="checkbox" name="user_name_show" value="enable" <?php checked( $user_name_show, 'enable' );?> > <?php _e( 'Show User Name Field' , 'wpad' ); ?>
														</label>
													
														<p class="description"><?php _e( 'Username field will be displayed for the logged in users but cannot edit the field' , 'wpad' ); ?></p>
														<br>
														<label>
															<input type="checkbox" name="user_email_show" value="enable" <?php checked( $user_email_show, 'enable' );?> > <?php _e( 'Show User Email Field' , 'wpad' ); ?>
														</label>
													
														<p class="description"><?php _e( 'User email field will be displayed for the logged in users but cannot edit the field' , 'wpad' ); ?></p>
													
													</td>
												</tr>
												<!--  
													Comment Date
												-->
												<tr>
													<th><?php _e( 'Comment Date' , 'wpad' ); ?></th>
													<td>
														<label>
															<input type="radio" name="comment_time" value="1" <?php checked( $comment_time , '1' );?> > <?php _e( 'Publish Date' , 'wpad' ); ?>
														</label>
														<label>
															<input type="radio" name="comment_time" value="2" <?php checked( $comment_time , '2' );?> > <?php _e( 'Time Ago' , 'wpad' ); ?>
														</label>														
													</td>
												</tr>
												<!--  
													Comment Position
												-->
												<tr>
													<th><?php _e( 'Comment Position' , 'wpad' ); ?></th>
													<td>
												
														<label>
															<input type="radio" name="comment_position" value="1" <?php checked( $comment_position , '1' );?> > <?php _e( 'Show Comment First & Comment Metas at Last' , 'wpad' ); ?>
														</label>
														
														<br>
														<label>
															<input type="radio" name="comment_position" value="2" <?php checked( $comment_position , '2' );?> > <?php _e( 'Show Comment Metas First & Comment at Last' , 'wpad' ); ?>
														</label>
													</td>
												</tr>
												<!--  
													Submit Button Label
												-->
												<tr>
													<th>
														<?php _e( 'Submit Button Label' , 'wpad' ); ?>
													</th>
													<td>
														<input type="text" name="submit_label" value="<?php echo $submit_button; ?>" size="30">
													</td>
												</tr>
												<!--  
													No of Column
												-->
												<tr>
													<th>
														<?php _e( 'Layout' , 'wpad' ); ?>
													</th>
													<td>
														<?php 
														if( empty($no_of_column) ){
															$column = 'checked';
														} else {
															$column = '';
														}
														?>	
														<label>
															<input type="radio" name="no_of_column" value="1" <?php checked( $no_of_column , '1' );?> > <?php _e( 'One column' , 'wpad' ); ?>
														</label>
														
														<label>
															<input type="radio" name="no_of_column" value="2" <?php echo $column; checked( $no_of_column , '2' );?> > <?php _e( 'Two column' , 'wpad' ); ?>
														</label>
													</td>
												</tr>
											</tbody>
										</table>
									
									</div>
									<div id="wpad-pagination-settings" style="display:none">
										<table class="form-table">
											<?php 
											$pagination = !empty( $option[$id]['other']['pagination'] ) ? $option[$id]['other']['pagination'] : 'disable';
											$pagination_per_page = !empty( $option[$id]['other']['pagination_per_page'] ) ? $option[$id]['other']['pagination_per_page'] : '';
											$pagination_position = !empty( $option[$id]['other']['pagination_position'] ) ? $option[$id]['other']['pagination_position'] : 'top_bottom';
											$text_for_first_page = !empty( $option[$id]['other']['text_for_first_page'] ) ? $option[$id]['other']['text_for_first_page'] : '&laquo; First';
											$text_for_last_page = !empty( $option[$id]['other']['text_for_last_page'] ) ? $option[$id]['other']['text_for_last_page'] : 'Last &raquo;';
											$text_for_previous_page = !empty( $option[$id]['other']['text_for_previous_page'] ) ? $option[$id]['other']['text_for_previous_page'] : '&lsaquo; Previous';
											$text_for_next_page = !empty( $option[$id]['other']['text_for_next_page'] ) ? $option[$id]['other']['text_for_next_page'] : 'Next &rsaquo;';
											?>
											<tbody>
											
												<!-- 
												  	Pagination 
												-->
												<tr>
													<th>
														<?php _e( 'Pagination' , 'wpad' ); ?>
													</th>
													<td>
														<label>
															<input type="checkbox" name="pagination" value="enable" <?php checked( $pagination, 'enable' );?> > <?php _e( 'Enable' , 'wpad' ); ?>
														</label>
														<select name="pagination_per_page"> 
															<option value="" disabled="">No of comments</option>
															<option value="1" <?php selected( $pagination_per_page, 1 );?>>1</option>
															<option value="2" <?php selected( $pagination_per_page, 2 );?> >2</option>
															<option value="3" <?php selected( $pagination_per_page, 3 );?> >3</option>
															<option value="4" <?php selected( $pagination_per_page, 4 );?> >4</option>
															<option value="5" <?php selected( $pagination_per_page, 5 );?> >5</option>
															<option value="6" <?php selected( $pagination_per_page, 6 );?> >6</option>
															<option value="7" <?php selected( $pagination_per_page, 7 );?> >7</option>
															<option value="8" <?php selected( $pagination_per_page, 8 );?> >8</option>
															<option value="9" <?php selected( $pagination_per_page, 9 );?> >9</option>
															<option value="10" <?php selected( $pagination_per_page, 10 );?> >10</option>
														</select>
														<p class="description"><?php _e( 'Select no of comments to show per page.' , 'wpad' ); ?></p>
													</td>
												</tr>
												<!-- 
												  	Pagination Position
												-->
												<tr>
													<th>
														<?php _e( 'Comment Position' , 'wpad' ); ?>
													</th>
													<td>
														<label>
															<input type="radio" name="pagination_position" value="top" <?php checked( $pagination_position , 'top' );?> > <?php _e( 'Top' , 'wpad' ); ?>
														</label>
														<br>
														<label>
															<input type="radio" name="pagination_position" value="bottom" <?php checked( $pagination_position , 'bottom' );?> > <?php _e( 'Bottom' , 'wpad' ); ?>
														</label>
														<br>
														<label>
															<input type="radio" name="pagination_position" value="top_bottom" <?php checked( $pagination_position, 'top_bottom' );?> > <?php _e( 'Top & Bottom' , 'wpad' ); ?>
														</label>
													</td>
												</tr>
												<!-- 
												  	Text for first page
												-->
												<tr>
													<th><?php _e( 'Text for first page' , 'wpad' ); ?></th>
													<td>
														<input type="text" name="text_for_first_page" value="<?php echo $text_for_first_page; ?>"> 
													</td>
												</tr>
												<!-- 
												  	Text for Last page
												-->
												<tr>
													<th><?php _e( 'Text for Last page' , 'wpad' ); ?></th>
													<td>
														<input type="text" name="text_for_last_page" value="<?php echo $text_for_last_page; ?>"> 
													</td>
												</tr>
												<!-- 
												  	Text for Previous Page
												-->
												<tr>
													<th><?php _e( 'Text for Previous page' , 'wpad' ); ?></th>
													<td>
														<input type="text" name="text_for_previous_page" value="<?php echo $text_for_previous_page; ?>"> 
													</td>
												</tr>
												<!-- 
												  	Text for Next Page
												-->
												<tr>
													<th><?php _e( 'Text for Next page' , 'wpad' ); ?></th>
													<td>
														<input type="text" name="text_for_next_page" value="<?php echo $text_for_next_page; ?>"> 
													</td>
												</tr>
											</tbody>
										</table>
									</div>

									<div id="wpad-metabox-notification" style="display:none">
									
										<h3><?php _e( 'New Comment Notification for Administrator' , 'wpad' ); ?></h3>
										<?php 
										$notification = !empty($option[$id]['other']['notification']) ? $option[$id]['other']['notification'] : '';
										$notification_to = !empty($option[$id]['other']['notification_to']) ? $option[$id]['other']['notification_to'] : get_option( 'admin_email' );
										$notification_subject = !empty($option[$id]['other']['notification_subject']) ? $option[$id]['other']['notification_subject'] : 'New Comment';
										$message = '<p>Hi Admin,</p><p>A new comment has been posted in your site %sitename% (%siteurl%).</p><p>Here are the details.</p><p><strong>Post Title :</strong> %post_title%</p><p><strong>Comment Author :</strong> %comment_author%</p><p><strong>Comment Author Email :</strong> %comment_author_email%</p><p><strong>Comment :</strong> %comment%</p><p><strong>Comment Link :</strong> %permalink%</p>';
										$notification_message = !empty($option[$id]['other']['notification_message']) ? $option[$id]['other']['notification_message'] : $message; 
										?>
										<table class="form-table">
											<tbody>
											
												<tr>
													<th><?php _e( 'Notification' , 'wpad' ); ?></th>
													<td>
														<label>
															<input <?php checked( $notification , 'enable' ); ?> type="checkbox" name="enable_notification" value="enable">
															<?php _e( 'Enable Comment Notification' , 'wpad' ); ?>
														</label>
													</td>
												</tr>
												<tr>
													<th><?php _e( 'To' , 'wpad' ); ?></th>	
													<td>
														<input type="text" name="notification_to" value="<?php echo $notification_to; ?>" size="40">
														<p class="description">
															Note : For multiple emails use comma separated email.
														</p>
													</td>
												</tr>
												<tr>
													<th><?php _e( 'Subject' , 'wpad' ); ?></th>
													<td>
														<input type="text" name="notification_subject" value="<?php echo $notification_subject; ?>" size="40">
													</td>
												</tr>
												<tr>
													<th><?php _e( 'Message' , 'wpad' ); ?></th>
													<td>
											
														<?php
														
														$form->get_the_wp_editor( $notification_message , 'notification_message' );

														?>
													</td>
												</tr>
											</tbody>
										</table>
										
										<h3><?php _e( 'Comment Approved Notifications to the users' , 'wpad' ); ?></h3>
										
										<?php 
										$from_name = !empty($option[$id]['other']['mail_from_name']) ? $option[$id]['other']['mail_from_name'] : get_bloginfo( 'name' );
										$from_email = !empty($option[$id]['other']['mail_from_email']) ? $option[$id]['other']['mail_from_email'] : get_option( 'admin_email' );
										$mail_approved_subject = !empty($option[$id]['other']['mail_approved_subject']) ? $option[$id]['other']['mail_approved_subject'] : 'Comment Approved';
										$disable_approve_notification = !empty($option[$id]['other']['disable_approve_notification']) ? $option[$id]['other']['disable_approve_notification'] : '';
										?>
										<table class="form-table">
											<tbody>												
												<tr>
													<th><?php _e( 'Notification' , 'wpad' ); ?></th>
													<td>
														<label>
															<input <?php checked( $disable_approve_notification , 'disable' ); ?> type="checkbox" name="disable_approve_notification" value="disable">
															<?php _e( 'Disable Comment Notification' , 'wpad' ); ?>
															<p class="description">
																Note : If enabled, a checkbox will be visible to the users. If the user tick the checkbox then only the mail will be sent.
															</p>
														</label>
													</td>
												</tr>

												<tr>
													<th><?php _e( 'From Name' , 'wpad' ); ?></th>
													<td>
														<input type="text" name="mail_from_name" size="40" value="<?php echo $from_name; ?>">
													</td>
												</tr>

												<tr>
													<th><?php _e( 'From Email' , 'wpad' ); ?></th>
													<td>
														<input type="text" name="mail_from_email" size="40" value="<?php echo $from_email; ?>">
													</td>
												</tr>

												<tr>
													<th><?php _e( 'Subject' , 'wpad' ); ?></th>
													<td>
														<input type="text" name="mail_approved_subject" size="40" value="<?php echo $mail_approved_subject; ?>">
													</td>
												</tr>

												<tr>
													<th><?php _e( 'Message' , 'wpad' ); ?></th>
													<td>
														<?php
														$confirm_notification_message = !empty($option[$id]['other']['confirm_notification_message']) ? $option[$id]['other']['confirm_notification_message'] : '<p>Your comment has been approved on the %sitename% ( %siteurl% ).</p><p><strong>Comment Link :</strong> %permalink%</p>';													
														$form->get_the_wp_editor( $confirm_notification_message , 'confirm_notification_message' );
														?>
													</td>
												</tr>

											</tbody>
										</table>

										<h3><?php _e( 'You may use in message:' , 'wpad' ); ?></h3>
										<p>
											<code>%comment_author%</code>,
											<code>%comment_author_email%</code>,
											<code>%comment%</code>,
											<code>%post_title%</code>,
											<code>%sitename%</code>,
											<code>%siteurl%</code>,
											<code>%permalink%</code>
										<br><br>
										<code>%custom_{METAKEY_OF_CUSTOM_FIELD}%</code> e.g: <code>%custom_address%</code> where <code>address</code> is a meta key</p>

									</div>

									<div id="wpad-like-dislike" style="display:none">
										<?php 
										$like_dislike_btn = !empty($option[$id]['other']['like_dislike_btn']) ? $option[$id]['other']['like_dislike_btn'] : '';
										$choose_button = !empty($option[$id]['other']['choose_button']) ? $option[$id]['other']['choose_button'] : 'both';
										$button_position = !empty($option[$id]['other']['button_position']) ? $option[$id]['other']['button_position'] : 'top';
										?>
										<table class="form-table">
											<tbody>
												
												<tr>
													<th></th>
													<td>
														<label>
															<input <?php checked( $like_dislike_btn , 'enable' ); ?> type="checkbox" name="like_dislike_btn" value="enable">
															<?php _e( 'Enable Like/Dislike Button' , 'wpad' ); ?>
														</label>
													</td>
												</tr>
												<tr>
													
													<th><?php _e( 'Button' , 'wpad' ); ?></th>
													<td>
														
														<select name="choose_button">
															<option value="both" <?php selected( $choose_button , 'both' );?>>Both like and dislike button</option>
															<option value="like" <?php selected( $choose_button , 'like' );?>>Like button only</option>
															<option value="dislike" <?php selected( $choose_button , 'dislike' );?>>Dislike button only</option>
														</select>
													</td>
												</tr>
												<tr>
													
													<th><?php _e( 'Button Position' , 'wpad' ); ?></th>
													<td>
														
														<select name="button_position">
															<option value="top" <?php selected( $button_position , 'top' );?>>Above Comment</option>
															<option value="bottom" <?php selected( $button_position , 'bottom' );?>>Below Comment</option>
														</select>
													</td>
												</tr>
											</tbody>
											
										</table>
									</div>

									<div id="wpad-custom-layout" style="display:none">
										<?php 
										$custom_layout = !empty( $option[$id]['other']['custom_layout'] ) ? $option[$id]['other']['custom_layout'] : '';
										?>
										<table class="form-table">
											<tbody>												
												<tr>
													<th><?php _e( 'Custom Layout' , 'wpad' ); ?></th>
													<td>
														<label>
															<textarea name="custom_layout" rows="10" cols="90"><?php echo $custom_layout; ?></textarea>
														</label>
													</td>
												</tr>
												<tr>
													<th></th>
													<td>
														<h4>You may use :</h4>
														<p><code>%gravatar%</code> for the user avatar ,</p>
														<p><code>%edit_button%</code> for the edit link on the frontend ,</p>
														<p><code>%comment_author%</code> for the author of the comment ,</p>
														<p><code>%comment_time%</code> for the comment time , </p>
														<p><code>%comment%</code> for the author comment , </p>
														<p><code>%like_dislike_btn%</code> for the Like & Dislike Button</p>
														<p><code>%custom_field_{NAME_OF_CUSTOM_FIELD}%</code> for the custom field</p>

														<h4>eg. Use this code for example</h4>

														<?php 
														$code_eg = "<div class='wpad_content_comment'><div class='wpad_front_gravatar'>%gravatar%%edit_button%</div><div class='wpad_content_wrap'><strong>%comment_author% </strong>said : <span class='wpad_right wpad_time'>%comment_time%</span><p>%comment%</p><div class='wpad_comment_meta'>%custom_field_{METAKEY_OF_CUSTOM_FIELD}%</div></div></div>";

														echo '<code>' . htmlspecialchars($code_eg) . '</code>'; ?>
														
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
                    		</div>
                    	</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<div id="wpad_dialog" title="Something is Wrong" style="display:none">
  		<p><?php _e( 'Meta keys should not be empty. Please correct them and try agian. <br>Thank you.' , 'wpad' ); ?></p>
	</div>
	<div id="wpad_dialog_label" title="Something is Wrong" style="display:none">
  		<p><?php _e( 'Field Labels should not be empty. Please correct them and try agian. <br>Thank you.' , 'wpad' ); ?></p>
	</div>
	<div id="wpad_dialog_same_keys" title="Something is Wrong" style="display:none">
  		<p><?php _e( 'Meta keys with same value are not allowed. Please correct them and try agian. <br><br>Multiple meta keys <span class="multiple_keys">address</span> found.<br><br>Thank you.' , 'wpad' ); ?></p>
	</div>
</div>