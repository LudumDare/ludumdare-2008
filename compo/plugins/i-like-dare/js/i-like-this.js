function likeThis(postId) {
	if (postId != '') {
		jQuery('#iLikeThis-'+postId+' .counter').text('...');
		
		jQuery.post(blogUrl + "/wp-content/plugins/i-like-dare/like.php",
			{ id: postId },
			function(data){
				jQuery('#iLikeThis-'+postId+' .counter-off').addClass('counter');
				jQuery('#iLikeThis-'+postId+' .counter').removeClass('counter-off');
				jQuery('#iLikeThis-'+postId+' .counter').text(data);
//				jQuery('#iLikeThis-'+postId+' .counter').removeAttr('style');
			});
	}
}