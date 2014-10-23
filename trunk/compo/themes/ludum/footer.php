	<hr />
	<div id="footer">
		<p style="color: #ffffff;">
			<p>All posts, images, and comments are owned by their creators.</p>
			<?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds.
		</p>
	</div>
</div>

		<?php wp_footer(); ?>
</body>
</html>
<?php do_action("compo2_cache_end"); ?>