<?php

/*
** This function will be called when the plugin will be uninstalled. 
*/

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'wpad_comment_form' );
delete_option( 'wpad_comment_forms_on_posts' ); 