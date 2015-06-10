# Usage #
```
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://ttv-api.s3.amazonaws.com/twitch.min.js"></script>
<script>
		var TwitchTV_APIKey = 'YOUR_TWITCH_API_KEY';
		var TwitchTV_Game = 'Ludum Dare';
		var TwitchTV_FAQ = '/compo/streaming-faq/';
		var TwitchTV_BaseDir = 'wp-content/plugins/twidget/';
</script>
<link rel="stylesheet" type="text/css" href="wp-content/plugins/twidget/twidget.css" />
<script src="wp-content/plugins/twidget/twidget.js"></script>

<span id="TwitchTV">Please Wait, loading Twitch.tv widget...</span>

<script>
	$("#TwitchTV").html( GetTwitchTVWidget() );
	InitTwitchTV();
</script>
```

or to play nicer (??).

```
<script>
	setTimeout( function(){
			$("#TwitchTV").html( GetTwitchTVWidget() );
			InitTwitchTV();
		}, 1000 );
</script>
```