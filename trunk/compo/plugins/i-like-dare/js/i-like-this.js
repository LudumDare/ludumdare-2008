function likeThis(postId) {
	if (postId != '') {
		jQuery('#iLikeThis-'+postId+' .counter').text('...');
		
		jQuery.post(blogUrl + "/wp-content/plugins/i-like-this/like.php",
			{ id: postId },
			function(data){
				jQuery('#iLikeThis-'+postId+' .counter').text(data);
			});
	}
}