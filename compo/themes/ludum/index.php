<?php get_header(); ?>
<div id="body">
	<!-- Side Bar -->
	<?php get_sidebar(); ?>
	<!-- Content -->
	<div id="content" class="narrowcolumn">
		<!-- Event -->
		<?php if ( function_exists('ldjam_show_bar') ) { echo ldjam_show_bar(); } ?>
		<?php if ( function_exists('c2_navigation') ) { 
			c2_navigation(
				"ludum-dare-37",
				"Ludum Dare 37",
				"https://ldjam.com"
				);
		} ?>
		<!-- Posts -->
<?php	if (have_posts()) { ?>
<?php		while (have_posts()) { ?>
<?php			the_post(); ?>
<?php			if ( current_user_can('edit_others_posts') ) {
					if ( get_the_author_meta('user_level') == 1 ) { ?>
		<div class="postflag" style="background-color: #D64;">
			<div style="float:left">NEW USER</div>
			<div style="float:right"><?php show_promote_buttons(); ?></div>
		</div>
<?php 				} elseif ( current_user_can('delete_users') && (isset($_GET["admin"]) && ($_GET["admin"] === "1")) ) { ?>
		<div class="postflag" style="background-color: #555;">
			<div style="float:left">EXISTING USER</div>
			<div style="float:right"><?php show_murder_buttons(); ?></div>
		</div>
<?php 				} ?>
					
<?php				if ( $post->post_status == 'pending' ) { ?>
		<div class="postflag">
			<div style="float:left">PENDING</div>
			<div style="float:right"><?php show_publish_buttons(); ?></div>
		</div>
<?php 				} ?>
<?php			} /* can edit_other_posts */ ?>
				
<?php 			if ( get_the_author_meta('display_name') == 'news' ) { ?>
		<div class="ld-news post" id="post-<?php the_ID(); ?>">
<?php			} else if ( get_the_author_meta('user_level') == 10 ) { ?>
		<div class="ld-admin post" id="post-<?php the_ID(); ?>">
<?php 			} else if ( is_sticky() ) { ?>
		<div class="ld-sticky post" id="post-<?php the_ID(); ?>">
<?php 			} else if ( get_post_meta(get_the_ID(), '_liked', true) >= 4 ) { ?>
		<div class="ld-love post" id="post-<?php the_ID(); ?>" >
<?php 			} else if ( get_the_author_meta('user_level') == 7 ) { ?>
		<div class="ld-mod post" id="post-<?php the_ID(); ?>">
<?php 			} else { ?>
		<div class="ld-post post" id="post-<?php the_ID(); ?>" >
<?php			} ?>


<?php			$TimeDiff = $_SERVER['REQUEST_TIME'] - get_post_time('U', true);/*get_the_time('U');*/ ?>

<?php /* BEGIN */
				$PostAge = "RIGHT NOW!";
				$IsNew = true;
				if ( $TimeDiff > (2*24*60*60) ) {
					$PostAge = floor($TimeDiff / (24*60*60)) . " days ago";
					$IsNew = false;
				}
				else if ( $TimeDiff > (24*60*60) ) {
					$PostAge = floor($TimeDiff / (24*60*60)) . " day ago";
				}
				else if ( $TimeDiff > (2*60*60) ) {
					$PostAge = floor($TimeDiff / (60*60)) . " hours ago";
				}
				else if ( $TimeDiff > (60*60) ) {
					$PostAge = floor($TimeDiff / (60*60)) . " hour ago";
				}
				else if ( $TimeDiff > (2*60) ) {
					$PostAge = floor($TimeDiff / (1*60)) . " minutes ago";
				}
/* END */ ?>	
				
<?php			$minimize = get_post_meta($post->ID,'minimize',false); ?>
				
<?php			if ( count($minimize) == 0 ) { ?>
			<div class="header">
				<div style="float: right;">
					<?php echo get_avatar(get_the_author_id(),$size='56',$default='' ); ?>
				</div>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                <div>Posted by <?php the_author_posts_link(); ?><?php $aff = get_the_author_meta('affiliation', get_the_author_meta('ID')); if (($aff != null) && ($aff != '')) { echo ' of ' . $aff; } ?><?php $twitter = get_the_author_meta('twitter', get_the_author_meta('ID')); if (($twitter != null) && ($twitter != '')) { echo ' (twitter: <a target="_blank" href="http://twitter.com/' . $twitter . '">@' . $twitter . '</a>)'; } ?></div>
				<small><?php echo $PostAge; ?> | <?php the_time('F jS, Y g:i a') ?> <!-- by <?php the_author() ?> --></small>
			</div>
			<div class="body">
				<?php echo my_get_buttons(); ?>
				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
			</div>
			<div class="footer">
				<?php echo my_get_buttons(); ?>

				<div class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> |<?php edit_post_link(' Edit', '', ' |'); ?><?php if(function_exists('getILikeThis')) getILikeThis('get'); ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></div>
			</div>
<?php			} else { ?>
			<div class="minimize">
				<div style="float: right;"><?php echo get_avatar(get_the_author_id(),$size='56',$default='' ); ?></div>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<div>Posted by <?php the_author_posts_link(); ?><?php $aff = get_the_author_meta('affiliation', get_the_author_meta('ID')); if (($aff != null) && ($aff != '')) { echo ' of ' . $aff; } ?><?php $twitter = get_the_author_meta('twitter', get_the_author_meta('ID')); if (($twitter != null) && ($twitter != '')) { echo ' (twitter: <a target="_blank" href="http://twitter.com/' . $twitter . '">@' . $twitter . '</a>)'; } ?></div>
				<small><?php echo $PostAge; ?> | <?php the_time('F jS, Y g:i a') ?> | <?php if(function_exists('getILikeCount')) echo getILikeCount('get') . " love"; ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></small>
			</div>
<?php			} ?>
		</div>
	
<?php		}/*endwhile;*/ ?>
	
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>
	
<?php	} else { ?>
		<div id="error">
			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
		</div>
<?php	} /*endif;*/ ?>
	</div>
</div>
<?php get_footer(); ?>
