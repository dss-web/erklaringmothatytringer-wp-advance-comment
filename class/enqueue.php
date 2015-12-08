<?php

class wpad_enqueue{

	function __construct(){

		add_action( 'admin_menu' , array( $this , 'remove_comment_menu' ) );
		add_action( 'admin_menu', array( $this, 'wpad_enqueue_scripts') );
		add_action( 'wp_enqueue_scripts', array( $this , 'wpad_enqueue_front' ) );
		add_action( 'admin_notices', array( $this , 'warning_messages' ) );
		add_action( 'wp_ajax_wpad_do_not_bother' , array( $this , 'wpad_do_not_bother' ) );
		add_action( 'wp_ajax_wpad_akismet_no_api' , array( $this , 'wpad_akismet_no_api' ) );

		// Delete options on Deactivate and uninstall
		register_deactivation_hook( 'wp-advance-comment/index.php' , array( $this , 'on_deactivation' ) );

	}

	public static function on_deactivation(){

		delete_option( 'wpad_akismet_no_api_notice' );
		delete_option( 'wpad_akismet_notice' );        

	}

	function wpad_akismet_no_api(){

		if( $_POST['action'] == 'wpad_akismet_no_api' && $_POST['value'] == 1 ){

			update_option( 'wpad_akismet_no_api_notice' , 'off' );

		}
		die;

	}

	function wpad_do_not_bother(){

		if( $_POST['action'] == 'wpad_do_not_bother' && $_POST['value'] == 1 ){

			update_option( 'wpad_akismet_notice' , 'off' );

		}
		die;

	}

	function remove_comment_menu(){

		global $menu;

		foreach( $menu as $key => $value ){

			if( $value[2] == 'edit-comments.php' ){

				unset( $menu[$key] );

			}

		}

	}

	function wpad_enqueue_front(){

		/*
		** This will be enqueue when shortcode is enable	
		*/

		wp_register_style( 'wpad_style_front', plugin_dir_url( dirname( __FILE__ ) ) . 'css/frontend-style.css' );

		wp_register_script( 'wpad_validate_jquery', plugin_dir_url( dirname( __FILE__ ) ) . 'js/jquery.validate.min.js' , array(), '1.0.0', false );	

		wp_register_script( 'wpad_validate_additional_method', plugin_dir_url( dirname( __FILE__ ) ) . 'js/methods.min.js' , array(), '1.0.0', false );

		$this->check_jquery_settings();

		wp_register_script( 'wpad_front_scripts', plugin_dir_url( dirname( __FILE__ ) ) . 'js/frontend-script.js' , array(), '1.0.0', false );

		$this->localize_scripts();

	}

	function localize_scripts(){

		$translation_array = array(

			'admin_ajax' => admin_url( 'admin-ajax.php' ),
			'plugin_url' => plugin_dir_url( dirname( __FILE__ ) ),
			'report_textarea' => get_option( 'wpad_required_user_message' )

		);

		wp_localize_script( 'wpad_front_scripts', 'translate', $translation_array );

		wp_enqueue_script( 'wpad_front_scripts' );

	}

	function check_jquery_settings(){

		if( get_option( 'disable_jquery' ) != 'disable' ){

			wp_enqueue_script( 'jquery' );

		}

		if( get_option( 'disable_jquery_validation' ) != 'disable' ){

			wp_enqueue_script( 'wpad_validate_jquery' );

			wp_enqueue_script( 'wpad_validate_additional_method' );

		}

	}

	function wpad_enqueue_scripts(){

		if( empty($_GET['page']) ){

			return;

		}

		$link = array('wpad_addons','wpad_comment_form_edit','wp_advance_comment', 'wpad_comment_form_list' , 'wpad_reply_form' , 'wpad_saved_edit_form' , 'wpad_help' , 'wpad_comment_setting_page' , 'wpad_reported_comment' );

		/*
		// Include Styles and Scripts for only Wp advance comment pages
		*/

		if( in_array($_GET['page'], $link) ){

			wp_register_script( 'wpad_script', plugin_dir_url( dirname( __FILE__ ) ) . 'js/scripts.js' );

			$id = !empty( $_GET['comment_id'] ) ? $_GET['comment_id'] : '';

			$translation_array = array(

				'admin_ajax' => admin_url( 'admin-ajax.php' ),

				'plugin_dir_path' => plugin_dir_url( dirname( __FILE__ ) ),

				'comment_id' => $id,

				'admin_url' => admin_url()

			);

			wp_localize_script( 'wpad_script', 'translate', $translation_array );

			/*
			// Enqueued script with localized data.
			*/

			wp_enqueue_script( 'wpad_script' );

			wp_enqueue_style( 'wpad-style', plugin_dir_url( dirname( __FILE__ ) ) . 'css/style.css' , array(), '1.0.0', false );

			wp_enqueue_script( 'jquery-ui-sortable' );

			wp_enqueue_script( 'jquery-ui-dialog' );

			wp_enqueue_style( 'wp-jquery-ui-dialog' );

			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );

			// Get Wordpress defaults icons

			wp_enqueue_style( 'dashicons' );

		}		

	} 
	function warning_messages() {

		$akismet_key_api = get_option( 'wordpress_api_key' ); 
		$active_plugins = get_option( 'active_plugins' ); 
		$flag = false;

		if( !empty( $active_plugins ) ){

			foreach( $active_plugins as $key => $value ){

				if( $value == 'akismet/akismet.php' ){
					$flag = true;
				}

			}

		}

		if( $flag == false ){ 

			$show_message = get_option('wpad_akismet_notice'); 

			if( $show_message != 'off' ){ ?>

				<div class="error wpad_error_message deactivated_akismet" style="border-color: hsla(341, 69%, 47%, 0.83) !important;padding-bottom: 12px !important;">
			        <p><?php _e( "It seems like Akismet Plugin is deactivated or not installed. Please enable it to protect from spam. This plugin is used by WP Advanced Comment.", 'wpad' ); ?></p>
			        <button class="button skip do_not_bother">No, don't bother me again</button>
			        <img src="<?php echo includes_url('/images/spinner.gif');?>" class="wpad_loader1" style="display:none">
			    </div>

			    <script>

			    	jQuery(document).on( 'click' , '.deactivated_akismet .do_not_bother' , function(){

						jQuery.ajax({
							url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
							type : 'POST',
							data : {
								action : 'wpad_do_not_bother',
								value : 1
							},
							beforeSend : function(){
								jQuery('.wpad_loader1').show();
							}, 
							success : function(){
								jQuery('.deactivated_akismet').remove();
							}

						});

					});

			    </script>

				<?php

			}

		}

		elseif( empty($akismet_key_api) ){ 

			$akismet_no_api_notice = get_option( 'wpad_akismet_no_api_notice' );

			if( $akismet_no_api_notice != 'off' ){

				$message = "It seems like you haven't entered the Akismet API key. Please get an API Key from <a href='http://akismet.com/wordpress/' target='blank'>here</a>"; ?>

				<div class="error akismet_no_api" style="border-color: hsla(341, 69%, 47%, 0.83) !important;padding-bottom: 12px !important;">
			        <p><?php _e( $message , 'wpad' ); ?></p>
			        <button class="button skip do_not_bother">No, don't bother me again</button>
			        <img src="<?php echo includes_url('/images/spinner.gif');?>" class="wpad_loader1" style="display:none">
			    </div>

			    <script>

			    	jQuery(document).on( 'click' , '.akismet_no_api .do_not_bother' , function(){

						jQuery.ajax({
							url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
							type : 'POST',
							data : {
								action : 'wpad_akismet_no_api',
								value : 1
							},
							beforeSend : function(){
								jQuery('.wpad_loader1').show();
							}, 
							success : function(){
								jQuery('.akismet_no_api').remove();
							}

						});

					});

			    </script>

			<?php

			}

		}

	}

}

$wpad_enqueue = new wpad_enqueue();