
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

		MyText += GetTwitchTVPlayer( Stream.channel.name, 282, 188, AutoStart, 25 );
	}
	else {
		MyText += '<div style="vertical-align:middle;display:table-cell;width:282px;height:188px;"><span class="Intense">Live Streaming Video!</span><br />Click on a video to start playing<br /><br /><span style="font-size:20px;">To stream here, set your<br /><object data="'+TwitchTV_BaseDir+'TwitchLogo.svg" height="30" style="vertical-align:middle;" type="image/svg+xml"></object> game to <span class="Standout">' + TwitchTV_Game + '</span></span></div>';
	}

	var Out = $("#TTV_Video");
	Out.html( MyText );
}

function OnTwitchTVStopProp( e ) {
	e.stopPropagation();
}

function OnTwitchTVClicked( ClickId ) {
	var Streams = TwitchTV_Streams;

	if ( (TwitchTV_CurrentStream >= 0) && (TwitchTV_CurrentStream < Streams.length) ) {
		var Old = $("#TTV_ItemId_" + TwitchTV_CurrentStream);

		Old.removeClass( 'ItemSelected' );
		Old.addClass( 'Item' );
	}
		
	if ( ClickId != null ) {
		var Index = +ClickId.replace("TTV_ItemId_","");
	
		var New = $("#"+ClickId);
	
		if ( (Index >= 0) && (Index < Streams.length) ) {
			New.removeClass( 'Item' );
			New.addClass( 'ItemSelected' );
		}
			
		TwitchTV_CurrentStream = Index;
		
		ShowTwitchTVVideo( true );
	}
	else {
		TwitchTV_CurrentStream = -1;
		ShowTwitchTVVideo( false );
	}
}

function GetTwitchTVStreams() {
	var Streams = TwitchTV_Streams;
	var MyText = "";
	
	for ( var idx = 0; idx < Streams.length; idx++ ) {
		var Stream = Streams[idx];

		var Name = Stream.channel.display_name.replace(new RegExp("_", 'g')," ");
		var Viewers = Stream.viewers;
		
		//Stream.channel.display_name	// nice name (but still underscores) //
		//Stream.channel.name			// lower case name slug //
		//Stream.channel.status			// The full status string people set //
		//Stream.channel.game			// The game (duh) //
		//Stream.channel.created_at		// When created (e.g. "2012-07-14T15:14:34Z") //
		//Stream.channel.mature			// null if not set //
		//Stream.channel.updated_at		// When updated (e.g. "2013-04-15T20:51:46Z") //
		//Stream.channel.logo			// 300x300 PNG image of the streamer's avatar //
		//Stream.channel.url			// twitch url //
		//background,banner,video_banner// more images (usually channel off images) //
		//Stream.broadcaster			// Name of the app they are running (xsplit) //
		//Stream.viewers				// How many viewers //
		//Stream.name					// munged username (e.g. live_user_archonthwizard) //
		//Stream.name					// Game again //
		//Stream.channel.teams[]		// Array of Teams this player is part of (can be length 0) //
		//display_name,name,info,created_at,updated_at,logo,background,banner -- same, but teams //
		
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
		MyText += '<span class="Item_Link"><a href="' + Stream.channel.url + '" target="_blank" onclick="OnTwitchTVStopProp(event)">( ... )</a></span>';
		MyText += '</span>';
		MyText += "</div>";
	}
	
	if ( TwitchTV_HasMoreStreams ) {
		MyText += '<div class="ItemMore">';
			MyText += '<div class="ItemMore_Body" onclick="LoadTwitchTVStreamsButton()">';
				MyText += '<div class="ItemMore_ButtonLeft"></div>';
				MyText += '<div id="TTV_More" class="ItemMore_Button">';
					MyText += '<div class="ItemMore_Text">More</div>';
				MyText += '</div>';
				MyText += '<div class="ItemMore_ButtonRight"></div>';
			MyText += '</div>';
		MyText += '</div>';
//		MyText += '<div class="ItemMore_FarEdge"></div>';
	}

	return MyText;			
}

function LoadTwitchTVStreams() {
	Twitch.api({method: 'streams', params: {game:TwitchTV_Game, limit:TwitchTV_Limit, offset:TwitchTV_Streams.length} }, function(error, list) {
		var MyText = "";

		if (error) {
			//console.log(error);
			
			MyText += "Stream Fetch " + error + ".<br />";
		}
		else {
			//console.log(list);
			
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

function SetTwitchStandbyButton() {
	$("#TTV_Standby_Container").html( '<object id="TTV_Standby" data="'+TwitchTV_BaseDir+'ImgStandby.svg" width="22" height="22" type="image/svg+xml" />' );
	
	var svg = document.getElementById("TTV_Standby");
	svg.addEventListener("load",function(){
		try {
			var svgDoc = svg.contentDocument;
			var Thing = svgDoc.getElementById("TTV_Standby_Icon");
			Thing.addEventListener("click", function(){OnTwitchTVClicked(null);},false);
		}
		catch (e) {	
			console.log("Unable to bind function to Standby");
		}
	},false);
}

function InitTwitchTV() {
	Twitch.init({clientId: TwitchTV_APIKey}, function(error, status) {
		if (error) {
			// error encountered while loading
			//console.log(error);
			
			MyText += "Twitch API " + error + ".<br />";
		}
		else {
			SetTwitchStandbyButton();
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
			MyText += '<div id="TTV_Video" class="Head"></div>';
			MyText += '<div id="TTV_Streams" class="Body">Loading...</div>';
			MyText += '<div class="FarEdge"></div>';
		MyText += '</div>';
		MyText += '<div class="Foot">';
			MyText += '<div class="FootBody">';
				MyText += '<span class="FootImg">';
					MyText += '<object data="'+TwitchTV_BaseDir+'ImgTwitchGlitch.svg" width="24" height="24" type="image/svg+xml" />';
//					MyText += '<object data="'+TwitchTV_BaseDir+'ImgTwitchGlitch.svg" width="24" height="24" type="image/svg+xml" onclick="javascript:window.open(' + "'http://twitch.tv', '_blank'" + ')" />';
//					MyText += '<a href="http://twitch.tv" target="_blank"><object data="'+TwitchTV_BaseDir+'ImgTwitchGlitch.svg" width="24" height="24" type="image/svg+xml" /></a>';
//					MyText += '<a href="http://twitch.tv" target="_blank"><img src="'+TwitchTV_BaseDir+'ImgTwitchGlitch.svg" height="24" /></a>';
//					MyText += '<div class="ImgTwitch"><a href="http://twitch.tv" target="_blank"></a></div>';
				MyText += '</span>';
				MyText += "&nbsp;&nbsp;";
				MyText += '<span class="FootText">';
					MyText += '<a href="http://www.twitch.tv/directory/game/' + encodeURI( TwitchTV_Game ) + '" target="_blank"> View All Streams</a>';
				MyText += '</span>';
				MyText += Bullet;
				MyText += '<span class="FootText">';
					MyText += '<a href="' + TwitchTV_FAQ + '">FAQ</a>';
				MyText += '</span>';
//				MyText += Bullet;
//				MyText += Bullet;
//				MyText += Bullet;
				MyText += '<span id="TTV_Standby_Container" class="FootImg2">';
					MyText += '<object id="TTV_Standby" data="'+TwitchTV_BaseDir+'ImgStandby.svg" width="22" height="22" type="image/svg+xml" />';
//					MyText += '<img src="'+TwitchTV_BaseDir+'ImgStandby.svg" height="16" />';
//					MyText += '<div class="ImgPower"><a href="http://twitch.tv" target="_blank"></a></div>';
				MyText += '</span>';
			MyText += '</div>';
			MyText += '<div class="FootEdge"></div>';
		MyText += '</div>';
	MyText += '</div>';
	
	return MyText;
}
	