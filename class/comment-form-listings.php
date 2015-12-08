<?php

/**
* Display all the comment forms on wpad_comment_form_list page 
*/

class wpad_comment_form_list{

	function __construct(){
	
		$this->bulk_actions();
		add_action('admin_menu', array( $this, 'list_form_menu') );
		
	}

	function bulk_actions(){

		$this->bulk_action_trash();
		$this->bulk_action_restore();
		$this->bulk_delete_permanent();

	}

	function row_actions(){

		$this->permanent_delete_form();
		$this->restore_comment_form();
		$this->duplicate_comment_form();
		$this->trash_comment_form();

	}

	function bulk_delete_permanent(){

		if( isset( $_POST['bulk_delete_permanent'] ) && $_POST['bulk_delete_permanent'] == 1 ){

			$object = get_option( 'wpad_comment_form' );

			if( !empty( $object ) ){

				foreach( $object as $key => $value ){

					if( in_array( $key , explode( ',' , $_POST['ids'] ) ) ){

						unset( $object[$key] );
						$this->unset_key_database( $key );

					}

				}

				update_option( 'wpad_comment_form' , $object );

			}

		}

	}

	function bulk_action_restore(){

		if( isset( $_POST['bulk_restore'] ) && $_POST['bulk_restore'] == 1 ){

			$object = get_option( 'wpad_comment_form' );

			if( !empty( $object ) ){

				foreach( $object as $key => $value ){

					if( in_array( $key , explode( ',' , $_POST['ids'] ) ) ){

						$object[$key]['other']['comment_status'] = 'published';

					}

				}

				update_option( 'wpad_comment_form' , $object );

			}

		}

	}

	function bulk_action_trash(){

		if( isset( $_POST['bulk_trash'] ) && $_POST['bulk_trash'] == 1 ){

			$object = get_option( 'wpad_comment_form' );	

			if( !empty($object) ){

				foreach( $object as $key => $value ){

					if( in_array( $key , explode( ',' , $_POST['ids'] ) ) ){

						$object[$key]['other']['comment_status'] = 'trash';

					}

				}

				update_option( 'wpad_comment_form' , $object );

			}

		}

	}

	function unset_key_database( $id ){
		
		$db_forms = get_option( 'wpad_comment_forms_on_posts' );

		if( !empty($db_forms) ){

			foreach( $db_forms as $key => $value ){

				if( $key == $id ){

					unset( $db_forms[$key] );

				}

			}

		}

		update_option( 'wpad_comment_forms_on_posts' , $db_forms );

	}

	function permanent_delete_form(){

		if( isset( $_GET['delete_permanent_id'] ) ){

			$object = get_option( 'wpad_comment_form' );

			if( !empty($object) ){

				foreach( $object as $key => $value ){

					if( $key == $_GET['delete_permanent_id'] ){

						unset( $object[$key] );
						$this->unset_key_database( $key );

					}

				}

				update_option( 'wpad_comment_form' , $object );

			}

		}

	}

	function restore_comment_form(){

		if( isset( $_GET['restore_id'] ) ){

			$object = get_option( 'wpad_comment_form' );

			$restore_arr = array();

			if( !empty($object) ){

				foreach( $object as $key => $value ){

					if( $key == $_GET['restore_id'] ){

						$object[$key]['other']['comment_status'] = 'published';

					}

				}

				update_option( 'wpad_comment_form' , $object );

			}		

		}

	}

	function success_message( $message ){ ?>

		<div class="updated notice notice-success is-dismissible below-h2" id="message">
			<p><?php echo $message; ?></p>
			<button class="notice-dismiss" type="button">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>

		<?php
	}

	function duplicate_comment_form(){

		if( isset( $_GET['duplicate_id'] ) ){

			$object = get_option( 'wpad_comment_form' );

			$duplicate_arr = array();

			if( !empty($object) ){

				foreach( $object as $key => $value ){

					if( $key == $_GET['duplicate_id'] ){

						$duplicate_arr = $object[$key];

					}

				}

				$random_key = $this->random_key( $object );

				$duplicate_arr['other']['comment_title'] = $duplicate_arr['other']['comment_title'] . ' ( Duplicate )';

				$new_key = array( $random_key => $duplicate_arr );

				$new_arr = array_replace( $new_key , $object );

				update_option( 'wpad_comment_form' , $new_arr );

				$this->success_message( '1 comment form successfully duplicated.' );

			}

		}

	}

	function trash_comment_form(){

		if( isset( $_GET['trash_id'] ) ){

			$object = get_option( 'wpad_comment_form' );

			if( !empty( $object ) ){

				foreach( $object as $key => $value ){

					if( $key == $_GET['trash_id'] ){

						$object[$key]['other']['comment_status']  = 'trash';

					}

				}

				update_option( 'wpad_comment_form' , $object );

				$this->success_message( '1 comment form moved to the Trash.' );

			}

		}

	}

	function list_form_menu(){

		add_submenu_page( 'wp_advance_comment', 'WP Advanced Comment Forms', 'Comment Forms', 'manage_options', 'wpad_comment_form_list', array($this, 'list_comment_form'));

	}

	function get_comment_status( $status ){

		if( $status == 'published' ){

			echo '<span class="wpad_form_published_status">' . _e( 'Published' , 'wpad' ) . '</span>';

		} 

		elseif( $status == 'unpublished' ){

			echo '<span class="wpad_form_pending_status">' . _e( 'Unpublished' , 'wpad' ) . '</span>';

		} 

		elseif( $status == 'trash' ){

			echo '<span class="wpad_form_pending_status">' . _e( 'Trashed' , 'wpad' ) . '</span>';

		} 

		else{

			echo '<span class="wpad_form_show_comments_status">' . _e( 'Only Show Comments' , 'wpad' ) . '</span>';

		}

	}

	function get_guest_comment_status( $status ){

		if( $status == 'enable' ){

			echo '<img src="' . plugin_dir_url( dirname( __FILE__ ) ) . '/images/tick.png' . '" />';

		} else {

			echo '<img src="' . plugin_dir_url( dirname( __FILE__ ) ) . '/images/cross.png' . '" />';

		}

	}

	function random_key( $data ){

		$exception_values = array();

		if( !empty($data) ){ 

			foreach( $data as $key => $value ){

				$exception_values[] = $key;

			}

		}

		return $this->randWithout( 1 , 1000 , $exception_values );

	}

	function randWithout($from, $to, array $exceptions) {

    	sort($exceptions); // lets us use break; in the foreach reliably

    	$number = rand($from, $to - count($exceptions)); // or mt_rand()

    	foreach ($exceptions as $exception) {

        	if ($number >= $exception) {

            	$number++; // make up for the gap

        	} else /*if ($number < $exception)*/ {

            	break;

        	}

    	}

    	return $number;

	}

	function no_of_published_comments( $data ){

		$count = 0;

		if( !empty( $data) ){

			foreach( $data as $key => $value ){

				if( $value['other']['comment_status'] == 'published' ){

					$count++;

				}

			}

		}

		return $count;

	}

	function no_of_unpublished_comments( $data ){

		$count = 0;

		if( !empty( $data) ){

			foreach( $data as $key => $value ){

				if( $value['other']['comment_status'] == 'unpublished' ){

					$count++;

				}

			}

		}

		return $count;	

	}

	function no_of_show_only_comments( $data ){

		$count = 0;

		if( !empty( $data) ){

			foreach( $data as $key => $value ){

				if( $value['other']['comment_status'] == 'show_comments' ){

					$count++;

				}

			}

		}

		return $count;

	}

	function trash_comments( $data ){

		$count = 0;

		if( !empty( $data) ){

			foreach( $data as $key => $value ){

				if( $value['other']['comment_status'] == 'trash' ){

					$count++;

				}

			}

		}

		return $count;

	}

	function sub_menu_comment_list(){ 

		$comment_status = !empty( $_GET['comment_status'] ) ? $_GET['comment_status'] : 'all'; 

		$data = get_option( 'wpad_comment_form' ); ?>

		<ul class="subsubsub">

			<li class="all">

				<a href="<?php echo admin_url( 'admin.php?page=wpad_comment_form_list' );?>" class="<?php echo $comment_status == 'all' ? 'current' : ''; ?>">

					All

				</a> |

			</li>

			<li class="all">

				<a class="<?php echo $comment_status == 'published' ? 'current' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=wpad_comment_form_list&comment_status=published' );?>">

					Published 

					<span class="count">

						(<span class=""><?php echo $this->no_of_published_comments( $data ); ?></span>)

					</span>

				</a> |

			</li>

			<li class="all">

				<a class="<?php echo $comment_status == 'unpublished' ? 'current' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=wpad_comment_form_list&comment_status=unpublished' );?>">

					Unpublished 

					<span class="count">

						(<span class=""><?php echo $this->no_of_unpublished_comments( $data ); ?></span>)

					</span>

				</a> |

			</li>

			<li class="all">

				<a href="<?php echo admin_url( 'admin.php?page=wpad_comment_form_list&comment_status=show_comments' );?>" class="<?php echo $comment_status == 'show_comments' ? 'current' : ''; ?>">

					Show Only Comments 

					<span class="count">

						(<span class=""><?php echo $this->no_of_show_only_comments( $data ); ?></span>)

					</span>

				</a> |

			</li>

			<li class="all">

				<a href="<?php echo admin_url( 'admin.php?page=wpad_comment_form_list&comment_status=trash' );?>" class="<?php echo $comment_status == 'trash' ? 'current' : ''; ?>">

					Trash 

					<span class="count">

						(<span class=""><?php echo $this->trash_comments( $data ); ?></span>)

					</span>

				</a>

			</li>

		</ul>

		<?php

	}

	function get_status_title( $title , $key ){

		if( isset($_GET['comment_status']) && $_GET['comment_status'] == 'trash' ) {

			echo $title;

		} else { ?>

			<a href="<?php echo site_url();?>/wp-admin/admin.php?page=wpad_comment_form_edit&comment_id=<?php echo $key; ?>"><?php echo $title; ?></a>

			<?php

		}

	}

	function get_comment_status_column( $key ){

		if( isset($_GET['comment_status']) && $_GET['comment_status'] == 'trash' ) { ?>

			<span class="edit">

				<a title="Restore this item" href="<?php echo site_url();?>/wp-admin/admin.php?page=wpad_comment_form_list&restore_id=<?php echo $key; ?>&comment_status=trash"><?php _e( 'Restore' , 'wpad' ); ?></a> 

				|

			</span>

			<span class="trash">

				<a href="<?php echo site_url();?>/wp-admin/admin.php?page=wpad_comment_form_list&delete_permanent_id=<?php echo $key; ?>&comment_status=trash" title="Delete Permanently"><?php _e( 'Delete Permanently' , 'wpad' ); ?></a>

				|

			</span>

			<?php

		} else{ 

			$status = !empty($_GET['comment_status']) ? $_GET['comment_status'] : ''; ?>

			<span class="edit">

				<a title="Edit this item" href="<?php echo site_url();?>/wp-admin/admin.php?page=wpad_comment_form_edit&comment_id=<?php echo $key; ?>"><?php _e( 'Edit' , 'wpad' ); ?></a> 

				|

			</span>

			<span class="trash">

				<a href="<?php echo site_url();?>/wp-admin/admin.php?page=wpad_comment_form_list&trash_id=<?php echo $key . '&comment_status=' . $status; ?>" title="Trash this item"><?php _e( 'Trash' , 'wpad' ); ?></a>

				|

			</span>

			<?php

		}

	}

	function get_list_content( $value , $key ){

		$title = ( !empty($value['other']['comment_title']) ? $value['other']['comment_title'] : '(no title)' ); ?>

		<tr>

			<th class="check-column comment_forms_checkbox" scope="row">

				<input type="checkbox" value="<?php echo $key; ?>" name="post[]" id="">

				<div class="locked-indicator"></div>

			</th>

			<td>

				<strong>

					<?php 

					$this->get_status_title( $title , $key );

					?>

				</strong>

				<div class="row-actions">

					<?php $this->get_comment_status_column( $key ); 

					if( isset($_GET['comment_status']) && $_GET['comment_status'] == 'trash' ){

						$additional_parameters = '&comment_status=trash'; 

					} else {

						$additional_parameters = '';

					}

					$status = !empty($_GET['comment_status']) ? $_GET['comment_status'] : ''; ?>

					<span class="duplicate">

						<a href="<?php echo site_url();?>/wp-admin/admin.php?page=wpad_comment_form_list&duplicate_id=<?php echo $key . $additional_parameters . '&comment_status=' . $status; ?>" title="Duplicate this item"><?php _e( 'Duplicate' , 'wpad' ); ?></a>

					</span>

				</div>

			</td>

			<td class="comment_status">

				<?php $this->get_comment_status( $value['other']['comment_status'] ); ?>

			</td>

			<td>

				<?php 

				$guest_comment = isset($value['other']['guest_comment']) ? $value['other']['guest_comment'] : '';

				$this->get_guest_comment_status( $guest_comment ); ?>

			</td>

		</tr>

		<?php

	}

	function empty_comment_status( $count_posts ){

		if( $count_posts == 0 ){ ?>

			<tr>

				<td colspan="4" style="text-align:center">

					<?php _e( "No comments found." , 'wpad' ); ?>

				</td>

			</tr>

			<?php

		}

	}

	function bulk_action(){ 

		if( isset( $_GET['comment_status'] ) && $_GET['comment_status'] == 'trash' ){ ?>

			<div class="tablenav top wpad_bulk_action">

				<div class="alignleft bulkactions">

					<label for="bulk-action-selector-top" class="screen-reader-text">

						Select bulk action

					</label>

					<select name="action" id="bulk-action-selector-top">

						<option value="" selected="selected">Bulk Actions</option>

						<option value="restore">Restore</option>

						<option value="delete_permanent">Delete Permanently</option>

					</select>

					<input type="button" id="" class="button apply_bulk_comment_form_list" value="Apply">

				</div>

			</div>

			<?php

		} else { ?>

			<div class="tablenav top wpad_bulk_action">

				<div class="alignleft bulkactions">

					<label for="bulk-action-selector-top" class="screen-reader-text">

						Select bulk action

					</label>

					<select name="action" id="bulk-action-selector-top">

						<option value="" selected="selected">Bulk Actions</option>

						<option value="trash">Move to Trash</option>

					</select>

					<input type="button" id="" class="button apply_bulk_comment_form_list" value="Apply">

				</div>

			</div>

			<?php

		}

	}

	function new_comment_ids(){

		$data = get_option( 'wpad_comment_form' ); 

		$new_comment_id = $this->random_key( $data );

		return $new_comment_id;

	}

	function list_comment_form(){ ?>

		<div class="wrap">

			<?php 

			$this->row_actions();

			$data = get_option( 'wpad_comment_form' ); 

			$new_comment_id = $this->random_key( $data ); ?>

			<h2>

				<?php _e( 'Comment Forms' , 'wpad' ); ?>

				<a class="page-title-action" href="<?php echo site_url();?>/wp-admin/admin.php?page=wpad_comment_form_edit&comment_id=<?php echo $new_comment_id; ?>">Add New</a>

			</h2>

			<?php 

			$this->sub_menu_comment_list();

			$this->bulk_action(); 
			
			?>

			<table class="wp-list-table widefat fixed striped posts comments_list">

				<thead>

					<tr>

						<th class="manage-column column-cb check-column" id="cb" scope="col">

							<label for="cb-select-all-1" class="screen-reader-text"><?php _e( 'Select All' , 'wpad' ); ?></label>

							<input type="checkbox" id="cb-select-all-1">

						</th>

						<th style="width:35%"><?php _e( 'Title' , 'wpad' ); ?></th>

						<th><?php _e( 'Status' , 'wpad' ); ?></th>

						<th><?php _e( 'Guest Comment' , 'wpad' ); ?></th>

					</tr>

				</thead>

				<tbody>

					<?php 

					if( !empty($data) ):

						$count_posts = 0;

						foreach( $data as $key => $value ):

							if( !empty($_GET['comment_status']) ){

								if( $value['other']['comment_status'] == $_GET['comment_status'] ){

									$this->get_list_content( $value , $key );

									$count_posts++;

								}

							} else {

								if( $value['other']['comment_status'] != 'trash' ){

									$this->get_list_content( $value , $key );

									$count_posts++;

								}

							}

						endforeach;

						$this->empty_comment_status( $count_posts );

					else: ?>

						<tr>

							<td colspan="4" style="text-align:center"><?php _e( "You don't have any comment forms. " , 'wpad' ); ?><a href="<?php echo site_url();?>/wp-admin/admin.php?page=wpad_comment_form_edit&comment_id=<?php echo $new_comment_id; ?>"><?php _e( "Click Here" , 'wpad' ); ?></a> <?php _e( "to create one." , 'wpad' ); ?></td>

						</tr>



						<?php

					endif; ?>

				</tbody>

				<tfoot>

					<tr>

						<th class="manage-column column-cb check-column" id="cb" scope="col">

							<label for="cb-select-all-1" class="screen-reader-text"><?php _e( "Select All" , 'wpad' ); ?></label>

							<input type="checkbox" id="cb-select-all-1">

						</th>

						<th><?php _e( "Title" , 'wpad' ); ?></th>

						<th><?php _e( "Status" , 'wpad' ); ?></th>

						<th><?php _e( "Guest Comment" , 'wpad' ); ?></th>

					</tr>

				</tfoot>

			</table>

		</div>

		<?php 

	} 

}

$wpad_comment_form_list = new wpad_comment_form_list();