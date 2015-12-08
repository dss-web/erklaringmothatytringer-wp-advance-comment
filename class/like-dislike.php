<?php 

/*
** This page is for like or dislike a comment
*/

class wpad_like_dislike {

	function count_likes($comment_id){
		$comment_likes = get_comment_meta( $comment_id, 'comment_likes', true );

		if( empty($comment_likes) ){
			return 0;
		} else {
			return count($comment_likes);	
		}
		
	}

	function count_dislikes($comment_id){

		$comment_dislikes = get_comment_meta( $comment_id, 'comment_dislikes', true );

		if( empty($comment_dislikes) ){
			return 0;
		} else {
			return count($comment_dislikes);	
		}

	}

	function like_image( $comment_id ){
		$comment_likes = get_comment_meta( $comment_id, 'comment_likes', true );
		$user_ip = $_SERVER['REMOTE_ADDR'];

		if( !empty($comment_likes) && in_array( $user_ip , $comment_likes ) ){ ?>
			<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/like_success.png'; ?>">
			<?php
		} else { ?>
			<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/like.png'; ?>">
			<?php
		}

	}

	function dislike_image( $comment_id ){

		$comment_likes = get_comment_meta( $comment_id, 'comment_dislikes', true );
		$user_ip = $_SERVER['REMOTE_ADDR'];

		if( !empty( $comment_likes ) && in_array( $user_ip , $comment_likes ) ){ ?>
			<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/dislike_success.png'; ?>">
			<?php
		} else { ?>
			<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/dislike.png'; ?>">
			<?php
		}

	}

	function like_text($comment_id){

		$comment_likes = get_comment_meta( $comment_id, 'comment_likes', true );
		$user_ip = $_SERVER['REMOTE_ADDR'];

		if( is_array( $comment_likes ) && in_array( $user_ip , $comment_likes ) ){ 
			return 'wpad_liked';
		} else { 
			return;
		}

	}

	function dislike_text($comment_id){

		$comment_likes = get_comment_meta( $comment_id, 'comment_dislikes', true );
		$user_ip = $_SERVER['REMOTE_ADDR'];

		if( !empty( $comment_likes ) && in_array( $user_ip , $comment_likes ) ){ 
			return 'wpad_liked';
		} else { 
			return;
		}
		
	}

	function get_buttons( $comment_id , $buttons ){ 

		ob_start(); ?>

		<div class="wpad_like_dislike_wrapper">

			<?php 
			if( $buttons == 'both' || $buttons == 'like' ){ ?>
				<span class="like_btn_wrap">

					<a class="tooltips" href="javascript:void(0)" comment_id="<?php echo $comment_id; ?>">

						<span class="wpad_already_voted" style="display:none"></span>

						<span class="wpad_like_btn">
							<?php $this->like_image( $comment_id ); ?>
						</span>

						<?php $class_like = $this->like_text($comment_id); ?>
						<span class="wpad_like_text  <?php echo $class_like; ?>">
							<?php _e( 'Like' , 'wpad' ); ?>
						</span>
					</a>
					
					<span class="wpad_like_counter">
						<?php echo $this->count_likes($comment_id); ?>
					</span>
				</span>
				<?php 
			} 

			if( $buttons == 'both' || $buttons == 'dislike' ){ ?>

				<span class="dislike_btn_wrap">
					<a href="javascript:void(0)" comment_id="<?php echo $comment_id; ?>" class="tooltips">

						<span class="wpad_already_voted" style="display:none"></span>

						<span class="wpad_dislike_btn">
							<?php $this->dislike_image( $comment_id ); ?>
						</span>

						<?php $class_like = $this->dislike_text($comment_id); ?>
						<span class="wpad_dislike_text <?php echo $class_like; ?>">
							<?php _e( 'Dislike' , 'wpad' ); ?>
						</span>
					</a>
					<span class="wpad_dislike_counter">
						<?php echo $this->count_dislikes($comment_id); ?>
					</span>
				</span>

				<?php 
			} ?>
		</div>

		<?php

		$content = ob_get_clean();
		return $content;
	}

}