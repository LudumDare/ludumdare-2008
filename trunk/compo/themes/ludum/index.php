<?php get_header(); ?>


	<div id="content" class="narrowcolumn">
<?php /* <!--
<?php if (isset($_REQUEST["mythumb_nav"])): ?>
<p><div id='mythumb'><?php mythumb_nav(); ?></div></p>
<?php else: ?>
<form method='post' action='<?php echo get_option('home'); ?>/tag/final/'><input type='hidden' name='mythumb_nav' value='1'><input type='submit' value='Show me the GRID!'></form>
<?php endif; ?>
-->

<a name="the-entries"/> */ ?>

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

		<?php if ( current_user_can('edit_others_posts') ) { ?>
			<?php if ( get_the_author_meta('user_level') == 1 ) { ?>
				<div class="postflag" style="background-color: #D64;">
					<div style="float:left">NEW USER</div>
					<div style="float:right"><?php show_promote_buttons(); ?> Promote to Author | QUARANTINE | delete</div>
				</div>
			<?php } ?>
			<?php if ( $post->post_status == 'pending' ) { ?>
				<div class="postflag">
					<div style="float:left">PENDING</div>
					<div style="float:right"><?php show_publish_buttons(); ?> APPROVE POST | remove</div>
				</div>
			<?php } ?>
		<?php } ?>
		
		<?php if ( get_the_author_meta('display_name') == 'news' ) { ?>
			<div class="post" style="background: #f0fff0 url('/compo/wp-content/themes/ludum/povimg/News.png') no-repeat top right;" id="post-<?php the_ID(); ?>">
		<?php } else if ( get_the_author_meta('user_level') == 10 ) { ?>
			<div class="post" style="background: #fffff0 url('/compo/wp-content/themes/ludum/povimg/Admin.png') no-repeat top right;" id="post-<?php the_ID(); ?>">
		<?php } else if ( is_sticky() ) { ?>
			<div class="post" style="background: #f8f8ff url('/compo/wp-content/themes/ludum/povimg/Featured.png') no-repeat top right;" id="post-<?php the_ID(); ?>">
		<?php } else if ( get_post_meta(get_the_ID(), '_liked', true) >= 4 ) { ?>
			<div class="post" style="background: #fff0f0 url('/compo/wp-content/themes/ludum/povimg/Heart.png') no-repeat top right;" id="post-<?php the_ID(); ?>" >
		<?php } else if ( get_the_author_meta('user_level') == 7 ) { ?>
			<div class="post" style="background: #ffffff url('/compo/wp-content/themes/ludum/povimg/Moderator.png') no-repeat top right;" id="post-<?php the_ID(); ?>">
		<?php } else { ?>
			<div class="post" id="post-<?php the_ID(); ?>" >
		<?php } ?>
				<div style="float: right;"><?php echo get_avatar(get_the_author_id(),$size='56',$default='' ); ?></div>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                	<div>Posted by <?php the_author_posts_link(); ?><?php $aff = get_the_author_meta('affiliation', get_the_author_ID()); if (($aff != null) && ($aff != '')) { echo ' of ' . $aff; } ?><?php $twitter = get_the_author_meta('twitter', get_the_author_ID()); if (($twitter != null) && ($twitter != '')) { echo ' (twitter: <a target="_blank" href="http://twitter.com/' . $twitter . '">@' . $twitter . '</a>)'; } ?></div>

				<?php $minimize = get_post_meta($post->ID,'minimize',false); ?>
				<?php if ( count($minimize) == 0 ) { ?>
					<small><?php the_time('F jS, Y g:i a') ?> <!-- by <?php the_author() ?> --></small>

					<?php echo my_get_buttons(); ?>
					<div class="entry">
						<?php the_content('Read the rest of this entry &raquo;'); ?>
					</div>
					<?php echo my_get_buttons(); ?>
	
					<div class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> |<?php edit_post_link(' Edit', '', ' |'); ?><?php if(function_exists(getILikeThis)) getILikeThis('get'); ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></div>
				<?php } else { ?>
					<small><?php the_time('F jS, Y g:i a') ?> | <?php if(function_exists(getILikeCount)) echo getILikeCount('get') . " love"; ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></small>				
				<?php } ?>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
