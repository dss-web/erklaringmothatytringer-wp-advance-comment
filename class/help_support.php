<?php

/**
* This is page for Help or Support 
*/

class wpad_help_support {

	function __construct() {

		add_action( 'admin_menu', array( $this, 'register_wp_help_support' ) );

	}

	function register_wp_help_support(){

		add_submenu_page( 'wp_advance_comment', 'Help/Support', 'Help/Support', 'manage_options', 'wpad_help', array($this, 'backend_help_page'));

	}

	function backend_help_page(){ ?>

		<div class="wrap">

			<h2>WP Advance Comment Support</h2>

			<div class="accordion_container">

				<div id="wpad_add_forms">

					<a href="javascript:void(0)" class="support_heading">How to add Comment forms ???</a>

					<div class="content" style="display:none">

						1. Install the WP Advance Comment plugin and activate it.<br>

						2. Click on the comment forms menu under WP Advance Plugin menu or <a target="blank" href="<?php echo admin_url('/admin.php?page=wpad_comment_form_list'); ?>">click here</a><br>

						3. Create new form <br>

						4. Add title, custom fields and manage form settings and click update.<br>

						5. After saving you will get a shortcode for that form.<br>

						6. See on the right hand side under comment elements you can find shortcode for that form.<br>

						7. Copy that shortcode and place it on your widget/WP Editor or on the .php file using <?php echo htmlspecialchars("<?php echo do_shortcode( 'YOUR_SHORTCODE' ); ?>"); ?> 

					</div>

					<a href="javascript:void(0)" class="support_heading">How to create your own comment list design on the frontend ???</a>

					<div class="content" style="display:none">

						By default there is only one comment list design for the frontend. If you want to create your own design to meet with your theme then follow this steps.<br><br>

						<h3>You may use : </h3><br>

						<code>%gravatar%</code> for the user avatar ,<br>

						<code>%edit_button%</code> for the edit link on the frontend ,<br>

						<code>%comment_author%</code> for the author of the comment ,<br>

						<code>%comment_time%</code> for the comment time , <br>

						<code>%comment%</code> for the author comment , <br>

						<code>%custom_field_{NAME_OF_CUSTOM_FIELD}%</code> for the custom field

						<br><br>

						eg. Use this shortcode <br><br>

						<?php 

						$example = '[wpad-comment-form id="1" layout=' . '"<div class=' . "'wpad_content_comment'><div class='wpad_front_gravatar'>%gravatar%%edit_button%</div><div class='wpad_content_wrap'><strong>%comment_author% </strong>said : <span class='wpad_right wpad_time'>%comment_time%</span><br />%comment%<br><div class='wpad_comment_meta'>%custom_field_{NAME_OF_CUSTOM_FIELD}%</div></div></div>" . '"]'; 

						echo '<code>' . htmlspecialchars($example) . '</code>'; ?><br><br>

						As you can see new attribute "layout" has been added. This will allow you to add new layout for the comment list on the frontend. If you are happy with the default layout do not use "layout" attribute on the shortcode it will take the default layout.

					</div>

					<a href="javascript:void(0)" class="support_heading">Wordpress default comment vanishes when activating this plugin. Why ???</a>

					<div class="content" style="display:none">

						This is because WP Advanced Comment replaces wordpress default comment form with its own fuctionality. You can create your own comment forms with custom fields and display the comment form where ever you need. If you want only one comment form for the whole site. You can choose first option ( Replace default wordpress comment form ) in the add/edit comment form. 

					</div>

					<a href="javascript:void(0)" class="support_heading">Can I use more than one comment forms in one page ???</a>

					<div class="content" style="display:none">
						No, you cannot use more than one comment form in one page because the form id is same for all and it will create conflict with the jquery validation plugin. The comment forms will be displayed but will not work.
					</div>

					<a href="javascript:void(0)" class="support_heading">Should I enable / disable jquery validation plugin on the settings page ???</a>

					<div class="content" style="display:none">
						Well, If you are using jquery validation plugin already then you can disable it. 
					</div>

					<a href="javascript:void(0)" class="support_heading">Does this plugin protects from the spam ???</a>

					<div class="content" style="display:none">
						Yes, this plugin protects form spam. This plugin is integrated with the wordpress built in plugin Akismet. You will need to activate Akismet, by default Akismet will be disabled you will need to enable it and put the API Key for that plugin and you are good to go.
					</div>

				</div>

			</div>

		</div>

		<?php

	}

}

$wpad_help_support = new wpad_help_support();