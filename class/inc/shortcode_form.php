<?php

/*
** If comments_template() tag is used then it will replace that with this.
*/
$wpad_frontend_comment_form = new wpad_frontend_comment_form();
$id = $wpad_frontend_comment_form->get_the_selected_comment_form();
echo do_shortcode('[wpad-comment-form id="' . $id . '"]'); 