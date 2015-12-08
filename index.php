<?php /*

Plugin Name: Erkl&aelig;ringmothatytringer WP Advanced Comment
Version: 0.3
Author: DSS | Ravi Shakya
Description: This plugin allows you to create comment forms and manage your comments more easily and create custom fields for the comments.
Text Domain : wpad

*/

define( 'WPAD_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );

/*
Include Necessary Classes
*/

include 'class/comment-list.php';
include 'class/enqueue.php';
include 'class/frontend-save-data.php';
include 'class/comment-form-listings.php';
include 'class/edit-comment-form.php';
include 'class/settings.php';
include 'class/comment-reply-form.php';
include 'class/saved-comment-edit.php';
include 'class/add-ons.php';
include 'class/help_support.php';
include 'shortcodes/comment-form.php';
include 'class/remove_default_comment_tag.php';
include 'class/like-dislike.php';
include 'class/flagged-comment.php';
