<?php $comments = new wpad_advance_comment(); ?>
<div class="wrap">
    <h2><?php _e( 'WP Advanced Comment' , 'wpad' ); ?></h2>    
    <?php $comments->trash_comment_edit_page(); ?>
    <ul class="subsubsub">
		<li class="all">
			<a class="current" href="javascript:void(0)" onclick="get_comments('all')"><?php _e( 'All' , 'wpad' ); ?></a> |
		</li>
		<li class="moderated">
			<a id="hold" href="javascript:void(0)" onclick="get_comments('hold')"><?php _e( 'Pending' , 'wpad' ); ?> <span class="count">(<span class="pending_count">0</span>)</span></a> |
		</li>
		<li class="approved">
			<a id="approve" href="javascript:void(0)" onclick="get_comments('approve')"><?php _e( 'Approved' , 'wpad' ); ?> <span class="count">(<span class="approve_count">0</span>)</span></a> |
		</li>
		<li class="spam">
			<a id="spam" href="javascript:void(0)" onclick="get_comments('spam')"><?php _e( 'Spam' , 'wpad' ); ?> <span class="count">(<span class="spam_count">0</span>)</span></a> |
		</li>
		<li class="trash">
			<a id="trash" href="javascript:void(0)" onclick="get_comments('trash')"><?php _e( 'Trash' , 'wpad' ); ?> <span class="count">(<span class="trash_count">0</span>)</span></a>
		</li>
	</ul>
	<input type="hidden" id="all" class="status">
	<form id="comments-form" method="get" action="">
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<label class="screen-reader-text" for="bulk-action-selector-top"><?php _e( 'Select bulk action' , 'wpad' ); ?></label>
				<select id="bulk-action-selector-top" name="action" autocomplete="off">
					<option selected="selected" value="-1" disabled=""><?php _e( 'Bulk Actions' , 'wpad' ); ?></option>
					<option value="unapprove"><?php _e( 'Unapprove' , 'wpad' ); ?></option>
					<option value="approve"><?php _e( 'Approve' , 'wpad' ); ?></option>
					<option value="spam"><?php _e( 'Mark as Spam' , 'wpad' ); ?></option>
					<option value="trash"><?php _e( 'Move to Trash' , 'wpad' ); ?></option>
					<option value="not_spam" style="display:none"><?php _e( 'Not Spam' , 'wpad' ); ?></option>
					<option value="restore" style="display:none"><?php _e( 'Restore' , 'wpad' ); ?></option>
					<option value="delete_permanently" style="display:none"><?php _e( 'Delete Permanently' , 'wpad' ); ?></option>
				</select>
				
				<input type="button" value="Apply" class="button action" id="bulk_action" name="">
				
				<img style="display:none" class="bulk_loader" src="<?php echo $comments->plugin_url() . 'images/small_loader.gif'; ?>">
			</div>
			<div class="alignleft actions">
				<?php 
				$names = $comments->get_comment_users(); 
				$post_name = $comments->get_post_names(); 
				if( !empty($names) ){ ?>
					<select name="comment_user" autocomplete="off">
						<option value="" selected=""><?php _e( 'All Users' , 'wpad' ); ?></option>
						<?php 
						foreach( $names as $value ){ ?>
							
							<option value="<?php echo $value['id']; ?>">
								<?php echo $value['name']; ?>
							</option>
						<?php 
					} ?>
					</select>
					
					<?php 
				} 
				if( !empty($post_name) ) { ?>
					<select name="comment_post" autocomplete="off">
						<option value="" selected=""><?php _e( 'All Posts' , 'wpad' ); ?></option>
						<?php 
						foreach( $post_name as $value ){ ?>
							<option value="<?php echo $value['id']; ?>">
							<?php echo $value['title']; ?>
							</option>
							<?php 
						} ?>
					</select>
					<?php 
				} ?>
				<select name="comment_order_by" autocomplete="off">
					<option value="" selected=""><?php _e( 'Order By' , 'wpad' ); ?></option>
					<option value="comment_approved"><?php _e( 'Comment Approved' , 'wpad' ); ?></option>
					<option value="comment_author"><?php _e( 'Comment Author' , 'wpad' ); ?></option>
					<option value="comment_author_email"><?php _e( 'Comment Author Email' , 'wpad' ); ?></option>
					<option value="comment_author_IP"><?php _e( 'Comment Author IP' , 'wpad' ); ?></option>
					<option value="comment_author_url"><?php _e( 'Comment Author Url' , 'wpad' ); ?></option>
					<option value="comment_content"><?php _e( 'Comment Content' , 'wpad' ); ?></option>
					<option value="comment_date"><?php _e( 'Comment Date' , 'wpad' ); ?></option>
					<option value="comment_date_gmt"><?php _e( 'Comment Date Gmt' , 'wpad' ); ?></option>
					<option value="comment_ID"><?php _e( 'Comment ID' , 'wpad' ); ?></option>
					<option value="comment_karma"><?php _e( 'Comment Karma' , 'wpad' ); ?></option>
					<option value="comment_parent"><?php _e( 'Comment Parent' , 'wpad' ); ?></option>
					<option value="comment_post_ID"><?php _e( 'Comment Post ID' , 'wpad' ); ?></option>
					<option value="comment_type"><?php _e( 'Comment Type' , 'wpad' ); ?></option>
					<option value="user_id"><?php _e( 'User Id' , 'wpad' ); ?></option>
				</select>
				<select name="comment_order" autocomplete="off">
					<option value="" selected="">Order</option>
					<option value="ASC"><?php _e( 'Ascending' , 'wpad' ); ?></option>
					<option value="DESC"><?php _e( 'Decending' , 'wpad' ); ?></option>
				</select>
				<input type="hidden" id="comment_user_hide" value="">
				<input type="hidden" id="comment_post_hide" value="">
				<input type="hidden" id="comment_order_by_hide" value="">
				<input type="hidden" id="comment_order_hide" value="">
				<input type="button" value="Filter" class="button" id="post-query-submit" name="filter_action">
			</div>
			<div class="tablenav-pages one-page pagination"><ul></ul></div>
			<br class="clear">
		</div>
		<div class="wpad_showing_results"></div>
		<table class="widefat fixed comments striped ">
			<thead>
				<tr>
					<th class="check-column"><input id="cb-select-all-1" type="checkbox" autocomplete="off"></th>
				
					<th  class="column-author"><?php _e( 'Author' , 'wpad' ); ?></th>
					<th  class="column-comment"><?php _e( 'Comment' , 'wpad' ); ?></th>
					<th  class="column-response"><?php _e( 'In Response To' , 'wpad' ); ?></th>
					<th  class="column-reported"><?php _e( 'Reported' , 'wpad' ); ?></th>
				</tr>
			</thead>
			<tbody id="the-comment-list"></tbody>
			<tfoot>
				<tr>
					<th class="check-column"><input id="cb-select-all-1" type="checkbox"></th>
				
					<th  class="column-author"><?php _e( 'Author' , 'wpad' ); ?></th>
					<th  class="column-comment"><?php _e( 'Comment' , 'wpad' ); ?></th>
					<th  class="column-response"><?php _e( 'In Response To' , 'wpad' ); ?></th>
					<th  class="column-reported"><?php _e( 'Reported' , 'wpad' ); ?></th>
				</tr>
			</tfoot>
		</table>
	</form>
</div>