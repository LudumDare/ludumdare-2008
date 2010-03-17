<?php get_header(); ?>

<div id="content" class="narrowcolumn">
<?php
/*
 	  <?php $meta = eup_get_extended_profile(); // call all new meta values
 	  		print_r($meta); // Get an overview whats in the object
 	  		echo $meta->email // echo an meta value ?>
*/
?>
<?php is_tag(); ?>
		<?php if (have_posts()) : ?>
 	  		<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>

	  <?php /* If this is an author archive */ if (is_author()) { ?>
		<h2 class="pagetitle">About <?php 
		$uid = get_query_var("author");
                $auth = get_userdata($uid);
                echo $auth->display_name;
		?></h2>
		<div class="post">
			<?php echo $auth->description; ?>
		</div>
		<?php } ?>

		<h2 class="pagetitle"><?php 
		$uid = get_query_var("author");
                $auth = get_userdata($uid);
                echo $auth->display_name;
		?>'s Trophies</h2>
<?php
if (is_author() && is_category()) {
?>

<?php
    $uid = get_query_var("author");
    $auth = get_userdata($uid);
    $mylink = get_option('home')."/author/{$auth->user_nicename}/?compo_action=form";
    echo "<form method=post action='$mylink'><input type='submit' value='Award a trophy!'></form>";
    
    compo_rate(get_query_var("cat"),get_query_var("author"));
} elseif (is_author()) {
    compo_trophy(get_query_var("author"));
} elseif (is_category()) {
    compo_results(get_query_var("cat"));
}
?>

 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle"><?php 
		$uid = get_query_var("author");
                $auth = get_userdata($uid);
                echo $auth->display_name;
		?>'s Archive</h2>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle">Blog Archives</h2>
 	  <?php } ?>


		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>



<?php
/*
<?php if (isset($_REQUEST["mythumb_nav"])): ?>
<p><div id='mythumb'><?php mythumb_nav(); ?></div></p>
<?php else: ?>
<form method='post'><input type='hidden' name='mythumb_nav' value='1'><input type='submit' value='Show me the GRID!'></form>
<?php endif; ?>
*/
?>

<?
#<div id='mythumb'>php mythumb_nav(); </div>
?>

		<?php while (have_posts()) : the_post(); ?>
<?php if ( get_the_author_meta('display_name') == 'news' ) { ?>
			<div class="post" style="background: #f0fff0;">
<?php } else if ( get_the_author_meta('user_level') == 10 ) { ?>
			<div class="post" style="background: #fffff0;">
<?php } else if ( is_sticky() ) { ?>
			<div class="post" style="background: #f7f0ff;">
<?php } else { ?>
			<div class="post">
<?php } ?>
			<div style="float: right;border: 1px solid #eee;padding: 2px;background: #fff;"><?php echo get_avatar(get_the_author_id(),$size='56',$default='' ); ?></div>
			<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                        <div>Posted by <?php the_author_posts_link(); ?></div>
				<small><?php the_time('l, F jS, Y g:i a') ?></small>
                                
				<?php echo my_get_buttons(); ?>
				<div class="entry">
					<?php the_content() ?>
				</div>
				<?php echo my_get_buttons(); ?>

				<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
				
				<?php get_tags(); ?>

			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
