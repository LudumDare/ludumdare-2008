		<div id="footer">
			<div class="text">All posts, images, and comments are owned by their creators.</div>
			<div class="query"><?php echo get_num_queries(); ?> queries executed in <?php timer_stop(1); ?> seconds.</div>
		</div>
	</div>
<?php wp_footer(); ?>
</body>
</html>
<?php do_action("compo2_cache_end"); ?>