<?php get_header(); ?>
<div id="body">
	<!-- Content -->
	<div id="content" class="widecolumn">

	<?php if (have_posts()) : ?>

		<h2 class="pagetitle">Search Results</h2>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>


		<?php while (have_posts()) : the_post(); ?>

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


				<div class="minimize">
					<div style="float: right;"><?php echo get_avatar(get_the_author_id(),$size='56',$default='' ); ?></div>
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<div>Posted by <?php the_author_posts_link(); ?><?php $aff = get_the_author_meta('affiliation', get_the_author_ID()); if (($aff != null) && ($aff != '')) { echo ' of ' . $aff; } ?><?php $twitter = get_the_author_meta('twitter', get_the_author_ID()); if (($twitter != null) && ($twitter != '')) { echo ' (twitter: <a target="_blank" href="http://twitter.com/' . $twitter . '">@' . $twitter . '</a>)'; } ?></div>
					<small><?php echo $PostAge; ?> | <?php the_time('F jS, Y g:i a') ?> | <?php if(function_exists(getILikeCount)) echo getILikeCount('get') . " love"; ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></small>
				</div>

			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">No posts found. Try a different search?</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

	</div>
</div>
<?php get_footer(); ?>