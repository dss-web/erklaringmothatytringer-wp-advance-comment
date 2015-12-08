<?php 

/*
** This is the Option page / Settings page
*/

class wpad_settings_page{

	function __construct(){

		add_action( 'admin_menu', array( $this, 'register_wp_settings' ) );
		add_action( 'admin_init', array( $this, 'register_wpad_settings' ) );

	}

	function register_wp_settings(){

		add_submenu_page( 'wp_advance_comment', 'WP Advanced Comment Settings Page', 'Settings', 'manage_options', 'wpad_comment_setting_page', array($this, 'backend_settings_page'));

	}

	function register_wpad_settings() {

		register_setting( 'wpad_settings_group', 'disable_jquery' );
		register_setting( 'wpad_settings_group', 'disable_jquery_validation' );
		register_setting( 'wpad_settings_group' , 'report_comment_options' );
		register_setting( 'wpad_settings_group' , 'disable_textarea_report_comment' );
		register_setting( 'wpad_settings_group' , 'wpad_disable_reporting' );	
		register_setting( 'wpad_settings_group' , 'wpad_required_user_message' );
		register_setting( 'wpad_settings_group' , 'wpad_flaging_threshold' );
		register_setting( 'wpad_settings_group' , 'wpad_email_flagged_reports' );		

		$this->register_roles_settings();

	}

	function register_roles_settings(){
		
		$roles = $this->get_role_names();
		
		foreach( $roles as $key => $value ){

			register_setting( 'wpad_settings_group', 'role_color_' . $key );

		}
	}

	function get_role_names() {

		global $wp_roles;

		if ( ! isset( $wp_roles ) )
		    $wp_roles = new WP_Roles();

		$roles = $wp_roles->get_names();
		$roles['guest'] = 'Guest';
		return $roles;
	
	}

	function display_roles_colors(){

		$roles = $this->get_role_names();

		foreach( $roles as $key => $value ){ ?>

			<tr valign="top">
				<th scope="row"><?php echo ucwords( str_replace( '_' , ' ' , $key ) ); ?></th>
		    	<td>

		    		<?php 
		    		$name = 'role_color_' . $key; 
		    		$default_value = get_option($name) ? get_option($name) : '#1E90FF';
		    		?>

		    		<input name="<?php echo $name; ?>" type="text" value="<?php echo $default_value; ?>" class="<?php echo $name; ?>" data-default-color="<?php echo $default_value; ?>" />
		    		<script>

		    			jQuery(document).ready(function($){

		    				var myOptions = {
							    palettes: true
							};

						    jQuery(<?php echo "'." . $name . "'"; ?>).wpColorPicker(myOptions);
						});

		    		</script>
		    		
		    	</td>
	    	</tr>

			<?php

		}

	}

	function backend_settings_page(){ ?>

		<div class="wrap">

			<h2><?php _e( "Settings Page" , 'wpad' ); ?></h2>

			<form method="post" action="options.php">

    			<?php settings_fields( 'wpad_settings_group' ); ?>

    			<?php do_settings_sections( 'wpad_settings_group' ); ?>

    			<div id="wpuf-metabox-editor" class="wpad_settings_page">

	    			<h2 class="nav-tab-wrapper">

			            <a id="wpad-editor-tab" class="nav-tab nav-tab-active" for="#wpad-jquery-section" href="javascript:void(0)"><?php _e( 'jQuery' , 'wpad' ); ?></a>

			            <a id="wpad-post-settings-tab" class="nav-tab" for="#wpad-akismet-settings" href="javascript:void(0)"><?php _e( 'Akismet Integration' , 'wpad' ); ?></a>

			            <a id="wpad-post-settings-tab" class="nav-tab" for="#wpad-roles-color" href="javascript:void(0)"><?php _e( 'Roles' , 'wpad' ); ?></a>

			            <a id="wpad-post-settings-tab" class="nav-tab" for="#wpad-report-comment" href="javascript:void(0)"><?php _e( 'Moderation' , 'wpad' ); ?></a>

	    			</h2>

	    			<div class="tab-content">

					    <table class="form-table" id="wpad-jquery-section">

					        <tr valign="top">

					        	<th scope="row"><?php _e( "Disable jQuery" , 'wpad' ); ?></th>

						        <td>

						        	<label>

						        		<?php 

						        		$disable_jquery = get_option( 'disable_jquery' ); 

						        		?>

						        		<input type="checkbox" name="disable_jquery" value="disable" <?php checked( $disable_jquery , 'disable' );?> />

						        		<?php _e( "Disable Plugin's Jquery" , 'wpad' ); ?>

						        	</label>

						        	<p class="description"><?php _e( "Disable this if it conflicts with other jquery" , 'wpad' ); ?></p>

						        </td>

					        </tr>

					        <tr valign="top">

					        	<th scope="row"><?php _e( "Disable Validation Plugin" , 'wpad' ); ?></th>

					        	<td>

					        		<label>

					        			<?php 

						        		$disable_jquery_validation = get_option( 'disable_jquery_validation' ); 

						        		?>

					        			<input type="checkbox" name="disable_jquery_validation" value="disable" <?php checked( $disable_jquery_validation , 'disable' );?> />

					        			<?php _e( "Disable Jquery Validation Plugin" , 'wpad' ); ?>

					        		</label>

					        		<p class="description"><?php _e( "Disable this if the site already has jquery validation plugin." , 'wpad' ); ?></p>

					        	</td>

					        </tr>
				        			     
					    </table>

					    <table class="form-table" id="wpad-akismet-settings" style="display:none">
					    	<td valign="top">
					    		<?php $akismet_key_api = get_option( 'wordpress_api_key' );

					    		if( !empty($akismet_key_api) ){
					    			echo '<div class="wpad_success"><p>' . __( 'You are connected to Akismet. Great !!!' , 'wpad' ) . '</p></div>';
					    		} else {
					    			echo "<div class='wpad_error'><p>" . __( "You are not connected to Akismet. If you don't want spam please enable the Akismet plugin which comes with wordpress by default and put the API Key" , 'wpad' ) . "</p></div>";
					    		}?>
					    		
					    	</td>
					    </table>

					    <table class="form-table" id="wpad-roles-color" style="display:none">
					    	
					    	
					    	<?php $this->display_roles_colors(); ?>


					    </table>

					    <table class="form-table" id="wpad-report-comment" style="display:none">
					    	
					    	<?php 

					    	$report_comment_options = "Spam or scam,Violence or harmful behaviour,Sexually explicit content,I don't like this comment,This comment is harassing or bullying me";
					    	$db_report_option = get_option('report_comment_options');
					    	$report_options = !empty( $db_report_option ) ? $db_report_option : $report_comment_options;

					    	$textarea_report_comment = get_option('disable_textarea_report_comment');
					    	$disable_textarea_report_comment = !empty( $textarea_report_comment ) ? $textarea_report_comment : '';
					    	
					    	$reporting = get_option('wpad_disable_reporting');
					    	$disable_reporting = !empty($reporting) ? $reporting : '';

					    	$user_message = get_option('wpad_required_user_message');
					    	$required_user_message = !empty($user_message) ? $user_message : '';

					    	$flaging_threshold = get_option('wpad_flaging_threshold');
					    	$wpad_flaging_threshold = !empty($flaging_threshold) ? $flaging_threshold : '';

					    	$wpad_email_flagged = get_option('wpad_email_flagged_reports');
					    	$wpad_email_flagged_reports = !empty($wpad_email_flagged) ? $wpad_email_flagged : '';
					    	?>

					    	<tr>
					    		<th><?php _e( "Disable comment flagging" , 'wpad' ); ?></th>
					    		<td>
					    			<label>
					    				<input type="checkbox" name="wpad_disable_reporting" value="disable" <?php checked( $disable_reporting , 'disable' );?>>
					    				<?php _e( 'Disable your visitors from flagging comments as inappropriate' , 'wpad' ); ?>
					    			</label>
					    		</td>
					    	</tr>

					    	<tr>
					    		
					    		<th><?php _e( "Report Comment Options" , 'wpad' ); ?></th>
					    		<td>
					    			<textarea name="report_comment_options" rows="5" cols="70"><?php echo $report_options; ?></textarea>
					    			<p class="description">
					    				<?php _e( 'Enter your options separated with commas for the report comment.' , 'wpad' ); ?>
					    			</p>
					    		</td>

					    	</tr>

					    	<tr>
					    		<th><?php _e( "Textarea Comment" , 'wpad' ); ?></th>
					    		<td>
					    			
					    			<label>
					    				<input type="checkbox" name="disable_textarea_report_comment" value="disable" <?php checked( $disable_textarea_report_comment, 'disable' );?>>
					    				<?php _e( 'Disable textarea on report comment' , 'wpad' ); ?>
					    					
					    			</label>
					    			
					    		</td>
					    	</tr>

					    	<tr>
					    		<th><?php _e( "Mandatory User Message" , 'wpad' ); ?></th>
					    		<td>
					    			<label>
					    				<input type="checkbox" name="wpad_required_user_message" value="enable" <?php checked( $required_user_message, 'enable' );?>>
					    				<?php _e( 'Enable' , 'wpad' ); ?>
					    				
					    			</label>
					    			<p class="description">
					    				
					    				<?php _e( 'User must write a message why he/she flagged it.' , 'wpad' ); ?>

					    			</p>
					    		</td>
					    	</tr>

					    	<tr>
					    		<th><?php _e( "Flagging Threshold" , 'wpad' ); ?></th>
					    		<td>
					    			<input type="text" name="wpad_flaging_threshold" value="<?php echo $wpad_flaging_threshold; ?>">
					    			<p class="description">

					    				<?php _e( "Amount of user reports needed to send a comment to moderation ? Leave blank if you don't want to use." , 'wpad' ); ?>
					    				
					    			</p>
					    		</td>
					    	</tr>

					    	<tr>
					    		<th><?php _e( "Email Reports" , 'wpad' ); ?></th>
					    		<td>

					    			<label>
					    				<input type="checkbox" name="wpad_email_flagged_reports" value="enable" <?php echo checked( $wpad_email_flagged_reports , 'enable' );?>>
					    				
					    				<?php _e( "Enable" , 'wpad' ); ?>
					    			</label>
					    			
					    		</td>
					    	</tr>

					    </table>

				    </div>

			    </div>

    			<?php submit_button(); ?>

			</form>

		</div>

		<?php

	}

}

$settings = new wpad_settings_page();