
var TwitchTV_Limit = 4;				// Number of Streams to get //
var TwitchTV_CurrentStream = -1;		// Current Stream (default 0) //
var TwitchTV_VideoActive = false;	// Has the video been activated once //
var TwitchTV_HasMoreStreams = true; // Show the More button //

// **** //

var TwitchTV_Streams = [];			// The Array of Streams returned by TwitchAPI //

function GetTwitchTVPlayer( ChannelName, Width, Height, AutoStart, Volume ) {
	var MyText = "";
	
	// height=378 width=620

	MyText += '<object type="application/x-shockwave-flash" height="';
	MyText += Height;
	MyText += '" width="';
	MyText += Width;
	MyText += '" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=';
	MyText += ChannelName;
	MyText += '" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=';
	MyText += ChannelName;
	MyText += '&auto_play=';
	MyText += AutoStart;
	MyText += '&start_volume=';
	MyText += Volume;
	MyText += '" /></object>';

	return MyText;			
}

function ShowTwitchTVVideo( AutoStart ) {
	var Streams = TwitchTV_Streams;
	var MyText = "";
	
	if ( (TwitchTV_CurrentStream >= 0) && (TwitchTV_CurrentStream < Streams.length) ) {			
		var Stream = Streams[TwitchTV_CurrentStream];

		var Name = Stream.channel.display_name;
		var Viewers = Stream.viewers;

		MyText += GetTwitchTVPlayer( Stream.channel.name, 282, 188, AutoStart, 25 );
	}
	else {
		//MyText += '<img src="'+TwitchTV_BaseDir+'TVStatic.gif" width="282" height="171" />';
		MyText += "...";
	}

	var Out = $("#TTV_Video");
	Out.html( MyText );
}

function OnTwitchTVStopProp( e ) {
	e.stopPropagation();
}

function OnTwitchTVClicked( ClickId ) {
	var Streams = TwitchTV_Streams;
	var Index = +ClickId.replace("TTV_ItemId_","");

	var New = $("#"+ClickId);

	if ( (TwitchTV_CurrentStream >= 0) && (TwitchTV_CurrentStream < Streams.length) ) {
		var Old = $("#TTV_ItemId_" + TwitchTV_CurrentStream);
		
//		if ( Old != New ) {
			Old.removeClass( 'ItemSelected' );
			Old.addClass( 'Item' );
//		}
	}
	
	if ( (Index >= 0) && (Index < Streams.length) ) {
		New.removeClass( 'Item' );
		New.addClass( 'ItemSelected' );
	}
		
	TwitchTV_CurrentStream = Index;
	
	ShowTwitchTVVideo( true );
}

function GetTwitchTVStreams() {
	var Streams = TwitchTV_Streams;
	var MyText = "";
	
	for ( var idx = 0; idx < Streams.length; idx++ ) {
		var Stream = Streams[idx];

		var Name = Stream.channel.display_name.replace(new RegExp("_", 'g')," ");
		var Viewers = Stream.viewers;
		
		if ( idx == TwitchTV_CurrentStream ) {
			MyText += "<div class='ItemSelected' onclick='OnTwitchTVClicked(this.id)' id='TTV_ItemId_" + idx + "'>";
		}
		else {
			MyText += "<div class='Item' onclick='OnTwitchTVClicked(this.id)' id='TTV_ItemId_" + idx + "'>";
		}
		MyText += '<img class="Item_Image" src="' + Stream.preview.small + '" width="80" height="50" />';
		MyText += '<span class="Item_Body">';
		MyText += '<span class="Item_Name">' + Name + '</span><br />';
		MyText += '<span class="Item_Viewers">Viewers: <b>' + Viewers + '</b></span>';
		MyText += '<span class="Item_Link">( <a href="' + Stream.channel.url + '" target="_blank" onclick="OnTwitchTVStopProp(event)">...</a> )</span>';
		MyText += '</span>';
		MyText += "</div>";
	}
	
	if ( TwitchTV_HasMoreStreams ) {
		MyText += '<div id="TTV_More" class="ItemMore"><span class="ItemMore_Button" onclick="LoadTwitchTVStreamsButton()">More</span></div>';
	}
	
//			MyText += "<div>";
//			MyText += '<a href="http://www.twitch.tv/directory/game/' + encodeURI( TwitchTV_Game ) + '">View All Streams</a>';
//			MyText += "</div><br/>";

	return MyText;			
}

function LoadTwitchTVStreams() {
	Twitch.api({method: 'streams', params: {game:TwitchTV_Game, limit:TwitchTV_Limit, offset:TwitchTV_Streams.length} }, function(error, list) {
		var MyText = "";

		if (error) {
			console.log(error);
			
			MyText += "Streams: " + error + ".<br />";
		}
		else {
			console.log(list);
			
			if ( list.streams.length != TwitchTV_Limit ) {
				TwitchTV_HasMoreStreams = false;
			}
			
			TwitchTV_Streams = TwitchTV_Streams.concat( list.streams );
			
			MyText += GetTwitchTVStreams();
			
			if ( !TwitchTV_VideoActive ) {
				ShowTwitchTVVideo( false );
				TwitchTV_VideoActive = true;
			}
		}

		var Out = $("#TTV_Streams");
		Out.html( MyText );
	});
}

function LoadTwitchTVStreamsButton() {
	$("#TTV_More").html( '<span class="ItemMore_Button">...</span>' );			
	LoadTwitchTVStreams();
}

function InitTwitchTV() {
	Twitch.init({clientId: TwitchTV_APIKey}, function(error, status) {
		if (error) {
			// error encountered while loading
			console.log(error);
			
			MyText += "Twitch.TV API: " + error + ".<br />";
		}
		else {
			LoadTwitchTVStreams();
		}
	});			
}

function GetTwitchTVWidget() {
	var MyText = "";
//	var Bullet = "&bull;";
	var Bullet = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
	MyText += '<div id="TTV">';
		MyText += '<div class="Widget">';
			MyText += '<div id="TTV_Video" class="Head">...</div>';
			MyText += '<div id="TTV_Streams" class="Body">Loading...</div>';
		MyText += '</div>';
		MyText += '<div class="Foot">';
			MyText += '<div class="FootBody">';
				MyText += '<a href="http://twitch.tv" target="_blank"><img src="'+TwitchTV_BaseDir+'TwitchGlitch.svg" height="24" class="FootImage" /></a><a href="http://www.twitch.tv/directory/game/' + encodeURI( TwitchTV_Game ) + '" target="_blank"> View All Streams</a>' + Bullet + '<a href="' + TwitchTV_FAQ + '">FAQ</a>' + Bullet + '<img src="'+TwitchTV_BaseDir+'Standby.svg" height="16" />';
			MyText += '</div>';
			MyText += '<div class="FootEdge"></div>';
		MyText += '</div>';
		MyText += '<br />';
	MyText += '</div>';
	
	return MyText;
}
	