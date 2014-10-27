<?php get_header(); ?>
<div id="body">
	<div id="content" class="widecolumn">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
		</div>

		<?php if ( current_user_can('edit_others_posts') ) { ?>
			<?php if ( get_the_author_meta('user_level') == 1 ) { ?>
				<div class="postflag" style="background-color: #D64;">
					<div style="float:left">NEW USER</div>
					<div style="float:right"><?php show_promote_buttons(); ?></div>
				</div>
			<?php } ?>
			<?php if ( $post->post_status == 'pending' ) { ?>
				<div class="postflag">
					<div style="float:left">PENDING</div>
					<div style="float:right"><?php show_publish_buttons(); ?></div>
				</div>
			<?php } ?>
		<?php } ?>

	<?php if ( get_the_author_meta('display_name') == 'news' ) { ?>
		<div class="ld-news post" id="post-<?php the_ID(); ?>">
	<?php } else if ( get_the_author_meta('user_level') == 10 ) { ?>
		<div class="ld-admin post" id="post-<?php the_ID(); ?>">
	<?php } else if ( is_sticky() ) { ?>
		<div class="ld-sticky post" id="post-<?php the_ID(); ?>">
	<?php } else if ( get_post_meta(get_the_ID(), '_liked', true) >= 4 ) { ?>
		<div class="ld-love post" id="post-<?php the_ID(); ?>" >
	<?php } else if ( get_the_author_meta('user_level') == 7 ) { ?>
		<div class="ld-mod post" id="post-<?php the_ID(); ?>">
	<?php } else { ?>
		<div class="ld-post post" id="post-<?php the_ID(); ?>" >
	<?php } ?>
		<div class="header">
			<div style="float: right;"><?php echo get_avatar(get_the_author_id(),$size='56',$default='' ); ?></div>
			<h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <div>Posted by <?php the_author_posts_link(); ?><?php $aff = get_the_author_meta('affiliation', get_the_author_ID()); if (($aff != null) && ($aff != '')) { echo ' of ' . $aff; } ?><?php $twitter = get_the_author_meta('twitter', get_the_author_ID()); if (($twitter != null) && ($twitter != '')) { echo ' (twitter: <a target="_blank" href="http://twitter.com/' . $twitter . '">@' . $twitter . '</a>)'; } ?></div>
            <small><?php the_time('F jS, Y g:i a') ?></small>
                        
            <?php echo my_get_buttons(); ?>
		</div>
		<div class="body" style="position:relative">
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<?php echo my_get_buttons(); ?>
				<?php the_tags( '<p>Tags: ', ', ', '</p>'); ?>
			</div>
			<div style="padding-right:10px;position:absolute;right:0;bottom:0;"><?php if(function_exists(getILikeThis)) getILikeThis('get'); ?></div>
		</div>
		<div class="footer">
			<p class="postmetadata">
				<small>
					This entry was posted
					<?php /* This is commented, because it requires a little adjusting sometimes.
						You'll need to download this plugin, and follow the instructions:
						http://binarybonsai.com/archives/2004/08/17/time-since-plugin/ */
						/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); echo time_since($entry_datetime); echo ' ago'; */ ?>
					on <?php the_time('l, F jS, Y') ?> at <?php the_time() ?>
					and is filed under <?php the_category(', ') ?>.
					You can follow any responses to this entry through the <?php comments_rss_link('RSS 2.0'); ?> feed.

					<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
						// Both Comments and Pings are open ?>
						You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(); ?>" rel="trackback">trackback</a> from your own site.

					<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
						// Only Pings are Open ?>
						Responses are currently closed, but you can <a href="<?php trackback_url(); ?> " rel="trackback">trackback</a> from your own site.

					<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
						// Comments are open, Pings are not ?>
						You can skip to the end and leave a response. Pinging is currently not allowed.

					<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
						// Neither Comments, nor Pings are open ?>
						Both comments and pings are currently closed.

					<?php } edit_post_link('Edit this entry.','',''); ?>

				</small>
			</p>

		</div>
		</div>

		<?php comments_template(); ?>	
		<?php endwhile; else: ?>
			<p>Sorry, no posts matched your criteria.</p>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>
