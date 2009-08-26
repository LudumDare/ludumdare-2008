	<div id="sidebar">
		<ul>
                        <p>Join the GameCompo.Com <a href='http://www.gamecompo.com/mailing-list/'>mailing list</a> and stay informed!</pL>
                        
			<?php 	/* Widgetized sidebar, if you have the plugin installed. */
					if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
			<li>
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</li>

			<!-- Author information is disabled per default. Uncomment and fill in your details if you want to use it.
			<li><h2>Author</h2>
			<p>A little something about you, the author. Nothing lengthy, just an overview.</p>
			</li>
			-->

			<?php if ( is_404() || is_category() || is_day() || is_month() ||
						is_year() || is_search() || is_paged() ) {
			?> <li>

			<?php /* If this is a 404 page */ if (is_404()) { ?>
			<?php /* If this is a category archive */ } elseif (is_category()) { ?>
			<p>You are currently browsing the archives for the <?php single_cat_title(''); ?> category.</p>

			<?php /* If this is a yearly archive */ } elseif (is_day()) { ?>
			<p>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for the day <?php the_time('l, F jS, Y'); ?>.</p>

			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<p>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for <?php the_time('F, Y'); ?>.</p>

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<p>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for the year <?php the_time('Y'); ?>.</p>

			<?php /* If this is a monthly archive */ } elseif (is_search()) { ?>
			<p>You have searched the <a href="<?php echo bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for <strong>'<?php the_search_query(); ?>'</strong>. If you are unable to find anything in these search results, you can try one of these links.</p>

			<?php /* If this is a monthly archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<p>You are currently browsing the <a href="<?php echo bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives.</p>

			<?php } ?>

			</li> <?php }?>

			<?php /*wp_list_pages('title_li=<h2>Pages</h2>' );*/ ?>
                        <?php wp_list_bookmarks('categorize=0&category_name=links'); ?>

<!--
                        <h2>Countdown</h2>
                        
                        <ul>
                        <li style='font-size:20px; color:#ff5555;'><?php include dirname(__FILE__)."/countdown.php"; ?>
                        </ul>
-->

<li id='countdown'><h2>Countdown</h2>
<div style='font-size:20px; color:#ff8844;'>
<?php function_exists('fergcorp_countdownTimer')?fergcorp_countdownTimer(1):NULL; ?>
</div>
</li>


   <li><h2><?php _e('Recent Comments'); ?></h2>
        <ul>
        <?php get_recent_comments(); ?>
        </ul>
   </li>


<h2><?php _e('Recent Tweets (<a href="http://search.twitter.com/search?q=%23LD48">Tag: #LD48</a>)'); ?></h2>
<?php // Get RSS Feed(s)
include_once(ABSPATH . WPINC . '/feed.php');

// Get a SimplePie feed object from the specified feed source.
$rss = fetch_feed('http://search.twitter.com/search.atom?q=%23LD48');

// Figure out how many total items there are, but limit it to 5. 
$maxitems = $rss->get_item_quantity(5); 

// Build an array of all the items, starting with element 0 (first element).
$rss_items = $rss->get_items(0, $maxitems); 
?>

<ul>
    <?php if ($maxitems == 0) echo '<li>No items.</li>';
    else
    // Loop through each feed item and display each item as a hyperlink.
    foreach ( $rss_items as $item ) : ?>
    <li>
        <strong><a href='<?php echo $item->get_author()->get_link(); ?>'><?php echo $item->get_author()->get_name(); ?></a>:</strong> 
        <a href='<?php echo $item->get_permalink(); ?>'
        title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
        <?php echo $item->get_title(); ?></a>
    </li>
    <?php endforeach; ?>
</ul>



                        <!--
			<li><h2>Archives</h2>
				<ul>
				<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</li>
			-->

			<?php wp_list_categories('orderby=name&show_count=1&title_li=<h2>Categories</h2>'); ?>
			
			<!--<h2>Tags</h2>
                        <?php compo_cloud(); ?>
                        -->
			<?php /* If this is the frontpage */ /* if ( is_home() || is_page() ) { */ ?>
				<?php /* wp_list_bookmarks(); */ ?>

				<li><h2>Meta</h2>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
					<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
					<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
					<?php wp_meta(); ?>
				</ul>
				</li>
			<?php /* } */ ?>
                        
                        <? 
                        
//                         wp_list_authors('exclude_admin=0&optioncount=1'); 
/*                        global $wpdb;
                        $e = array_pop($wpdb->get_results("select count(*) as c from $wpdb->users",ARRAY_A)); $v = $e["c"];
*/
                        ?>
<!--
                        <li><h2>Members (<?php /*echo $v;*/?>)</h2>
                        <ul>
-->
                        <? 
/*
                        if (isset($_REQUEST["drpetter_makes_unreasonable_demands_all_the_time"])) {
                            wp_list_authors('exclude_admin=0&optioncount=1&hide_empty=0'); 
                        } else {
                            echo "<p><a href='?drpetter_makes_unreasonable_demands_all_the_time=1'>Show all members</a></p>";
                            wp_list_authors('exclude_admin=0&optioncount=1');
                        }
*/                        
                        ?><!--</ul>-->
			<?php endif; ?>

			
			<li><h2>Recent Trophies</h2>
			<?php compo_trophy_sidebar(); ?>
		</ul>
	</div>

