=== WP Advanced Comment ===

Contributors: ravisakya , codepixelzmedia
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UCYQAARPK3Q72
Tags: comments, advance, advanced , wordpress comment, advanced comment, forms, wordpress comment, post comment, avatar, free, premium, comments with avatar, admin search,backend search,admin,ajax,edit comments,captcha, comments, contact form 7 captcha, google, no captcha, nocaptcha, page, plugin, posts, recaptcha, shortcode, sidebar, spam, widget, ajax pagination, pagination,create forms,drag and drop,enable comments,disable comments,email notification,advance search, jquery validation, custom fields comments, unlimited custom fields,unlimited comment forms,shortcodes comment,custom design comments,akismet,spam protection,spam
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Advanced comment is a powerful and easy to use AJAX based comment system for wordpress with drag and drop custom fields.

== Description ==

WP Advanced comment is a powerful and easy to use AJAX based comment system for wordpress with drag and drop fields that allows your website visitors to comment on articles, blogs and product pages.

= Now it supports AKISMET. You can say goodbye to spam now =

<blockquote>

Akismet is an advanced hosted anti-spam service aimed at thwarting the underbelly of the web. It efficiently processes and analyzes masses of data from millions of sites and communities in real time. To fight the latest and dirtiest tactics embraced by the world's most proficient spammers, it learns and evolves every single second of every single day. Because you have better things to do.<br><br>

</blockquote>

= Main Features ( Free Version ) =

<blockquote>

1.  Easy to create forms with drag and drop options.<br>
2.  Enable or disable guest comments.<br>
3.  Change comment forms status to published, unpublished or show only comments( disable comment form ).<br>
4.  Email notification of new comments with custom fields.<br>
5.  Advance search comments ( Backend ).<br>
6.  Uses of Jquery Validation plugin.<br>
7.  Available custom fields ( Text, Textarea, Radio Button, Checkbox, DropDown/Select, Multiselect, Url, Email, Setion Break, HTML Codes , User Image, Section Break , HTML )<br>
8.  Create unlimited custom fields.<br>
9.  Create unlimited comment forms.<br>
10. Options to show custom fields to admin only ( frontend ).<br>
11. Create your custom design for the comment list on the frontend to meet with you theme design.<br>
12. Ajax Pagination on Comments <br>
13. Integrated with Akismet.<br>
14. Like/Dislike a comment. <br>
15. Automatically approved the comments without moderation option.<br>
16. Notification to admin and users.<br>
17. User roles will be displayed on the comments. eg. administrator, author, subscriber etc.<br>
18. Report to admin if the comment is inappropriate.<br>
19. Comments can be unpublished automatically when there are many flags for a comment.<br><br>
</blockquote>

= Main Features ( Pro Version ) =

<blockquote>

All of the above features + <br>

1.  Additional custom fields ( WP Editor, Image upload, File upload, Star Rating, Datepicker, Google Map, Really Simple Captcha, reCaptcha )<br>
2.  Sticky comments to feature the best comments<br>
3.  Nested Comments<br><br>

</blockquote>

= WP Advanced Comment Extensions =

<blockquote>

1. WooCommerce Integration<br>
2. Frontend Comments Manager<br>
3. Advanced Validation<br>
4. First Letter Avatar<br><br>

</blockquote>

== Installation ==

1. Install WP Advanced Comment either via the WordPress.org plugin directory, or by uploading the files to your server.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Now this will replace the default comment menu on the backend and replace with WP Advance Comment menu.
1. To create a new comment form click on the comment forms under WP Advance comment menu and then click on add new.
1. Now by default three fields will be active ( Name, Email and Comment field ). If you want to add more fields to the comment form then on the right hand side you can see the custom fields. Click on the custom fields and it will show up on the form editor.
1. Now to display the comment form you can choose the shortcode which you will get when you save the form or you can choose the option under ( How do you want to show the comment form ? ).

Note:
Do not show more than one comment form on a page/post. It will create conflict.

== Screenshots ==

1. Listing Page

1. Comment Form

1. New/Edit Form

1. Settings Page

1. Comment Form with custom fields

1. Display Comment

== Changelog ==

= 0.2 =
* By default guest comment was disabled on the previous versions now it will be enabled by default.
* Shortcode to display comment form has been removed because it conflicts with the pro version.
* Now WP Advanced Comment will work on commnet_form() and comments_template() tag.

= 0.1.1 =
* Report to admin option added.
* When user flag a comment it will be mailed to the admin.
* Comment can be automatically unpublished if there are many flags for a comment.
* See WP Advanced Comment > Settings > Moderation.

= 0.1 =

* Images were not uploading properly on some themes. Fixed
* User roles added on the comments and will be displayed by default. 

= 0.0.9 =

* Added custom layout on the backend. Now you can create your own design for the comment list.
* New custom field ( User Avatar ) added. Now users can upload avatar.
* New option added to automatically published the comment without moderation.
* Email notification textarea changed to WP Editor
* When the comment will be approved the user will be notified by the email, if the option is enabled.

= 0.0.8 =

* Two new fields have been added. Now you can add section break and HTML fields on the comment forms. This way you can create headings and put fields under that headings.
* Like and dislike button added on the comment list. See tab ( Like / Dislike button ) under add/edit form. 

= 0.0.7 =

* Fixed backward slash problem when saving form.
* Added border color when error is displayed.
* Help text will be displayed in separate row.
* Error message position changed. Now it will be display above the help text.
* On disabling akismet, spam protection will also be disabled .
* When entering names for the meta keys it should be lowercase and it should not contain spaces.
* Bulk action fixes.
* Some posts / pages were giving blank comment forms when forms are deleted permanently. Fixed.

= 0.0.6 =

* Now you can add new comment forms from edit comment form pages.
* The trashed comment forms will not be shown on the frontend.
* Some messages or warnings will be displayed on the frontend for the administrator only.
* The comment forms are now integrated with the Akismet. Now you can say good by to spam.
* Now you don't have to use shortcodes to display the comment forms. You can choose posts / pages / custom post types from options to show the comment forms.
* Three options have been added to show the comment forms.<br><br>
	i. Replace default wordpress comment form with the WP Advanced Comment form. This will replace all the comment forms where comment_form() tag are used. Now you don't have to use shortcodes.<br><br>
	ii. Choose certain posts / pages / custom post types to show the comment forms.<br><br>
	iii. If you want to use the shortcode then select none from the option and it will disable comment form on all pages and you can use shortcode on widgets or where ever you want to.<br><br>

= 0.0.5 =

* Pagination added on to the comment list with extra features.
* Created pagination tab on the add/Edit form where you can control you pagintion settings.
* Comment list ordering select box added. Now you can choose how to display comment ascending or decending by date.
* Pagination can be shown on the top or bottom or both places.
* Problems on the enable/disable guest comment solved.
* Email notification were going when disabling also. This has been fixed.

= 0.0.4 =

* Now you can create unlimited comment forms.
* Now you can show comments from a single comment form and disable all the comments from previous forms or you can disable this feature and show all the comments. This feature is placed on Form Settings > Comment listings.
* If this plugin is activated then the default Comment menu will be removed from the dashboard and will replaced by WP Advance Comment.
* Forms can be duplicated.
* Muliple forms can be restore, delete permanently or move to trash.

= 0.0.3 =

* No of pending comments shown to the WP Advanced Comment menu.
* Edit link added to the front end and will be shown to the admin only.
* Now you can create your custom design for the frontend comments list.
* On the comment list page post title was displaying only one comment title : Fixed 
* Design font changed

= 0.0.2 =

* Backend style fixed to meet with the wordpress 4.3
* Name changed from WP Advance Comment to WP Advanced Comment


== Frequently Asked Questions ==

= How to add Comment forms ??? =

1. Install the WP Advance Comment plugin and activate it.
2. Click on the comment forms menu under WP Advance Plugin menu.
3. Create new form <br>
4. Add title, custom fields and manage form settings and click update.
5. After saving you will get a shortcode for that form.
6. See on the right hand side under comment elements you can find shortcode for that form.
7. Copy that shortcode and place it on your widget/WP Editor or on the .php file using `<?php echo do_shortcode( 'YOUR_SHORTCODE' ); ?>`

= How to create your own comment list design on the frontend ??? =

By default there is only one comment list design for the frontend. If you want to create your own design to meet with your theme then follow this steps.

= You may use : =

%gravatar% for the user avatar ,<br>
%edit_button% for the edit link on the frontend ,<br>
%comment_author% for the author of the comment ,<br>
%comment_time% for the comment time , <br>
%comment% for the author comment , <br>
%custom_field_{NAME_OF_CUSTOM_FIELD}%</code> for the custom field

eg. Use this shortcode

`[wpad-comment-form id="1" layout="<div class='wpad_content_comment'><div class='wpad_front_gravatar'>%gravatar%%edit_button%</div><div class='wpad_content_wrap'><strong>%comment_author% </strong>said : <span class='wpad_right wpad_time'>%comment_time%</span><br />%comment%<br><div class='wpad_comment_meta'>%custom_field_{NAME_OF_CUSTOM_FIELD}%</div></div></div>"]`

As you can see new attribute "layout" has been added. This will allow you to add new layout for the comment list on the frontend. If you are happy with the default layout do not use "layout" attribute on the shortcode it will take the default layout.

== Upgrade Notice ==

= 0.0.6 =
Now you can integrate WP Advanced Comment form with Akismet. This will protect your site from spam comments.