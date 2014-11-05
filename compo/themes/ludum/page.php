<?php get_header(); ?>
<div id="body">
	<div id="content" class="fullcolumn botpad">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="ld-post post" id="post-<?php the_ID(); ?>">
			<div class="header"><h2><?php the_title(); ?></h2></div>
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
				<br />
			</div>
		</div>
		<?php endwhile; endif; ?>
	<?php edit_post_link('Edit this entry.', '<div>', '</div>'); ?>
	</div>
</div>
<?php get_footer(); ?>