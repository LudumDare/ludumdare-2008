////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                                                    
//  unityobject.js - v0.4                                                                                             
//                                                                                                                    
//  Revision History:                                                                                                 
//  2008-OCT-05 : Script updated to offer compatibility with WordPress UnityObject, a plugin for WordPress blog users 
//  2008-SEP-11 : Script updated to allow for the specification of a custom install prompt image (tom@unity3d.com) 
//  2008-JAN-21 : Script updated to fix some Windows IE errors (tom@unity3d.com/joe@unity3d.com)               
//  2007-NOV-28 : Script updated to fix a few minor errors (tom@unity3d.com)                                          
//  2007-NOV-15 : Script updated from milkytreat's v0.3 by Tom Higgins (tom@unity3d.com)                              
//                - it's now compatible with both Unity 1.0 and 2.0 content                                           
//                - it now uses 'ut' instead of 'otee' in the code                                                    
//                - there are changes to the parameters that are provided when creating the UnityObject versus those  
//                  that need to be added by calling addParam                                                         
//                - for Unity 2.0 content this script now takes advantage of the fact that the user doesn't need to   
//                  quit the browser when installing the Unity Web Player                             
//                                                                                                                    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

var gAutomaticReloadForwardObject = null;
function automaticReloadForward () {
	if (gAutomaticReloadForwardObject != null)
		gAutomaticReloadForwardObject.automaticReload();
}

if (typeof ut == "undefined") { var ut = new Object(); }
ut.UnityObject = function (u, i, w, h, v, url, img) {
		
    if (!document.getElementById) { return; }
    this.attributes = new Array();
    this.params = new Object();
    if (u && u != "") { this.setAttribute("src", u); }
    if (i && i != "") { this.setAttribute("id", i); }
    if (w && w != "") { this.setAttribute("width", w); }
    if (h && h != "") { this.setAttribute("height", h); }
    if (v && v != "") { 
		this.playerVersion = v; 
    } else { 
		this.playerVersion = 2; 
    }
    if (url && url != "") { this.setAttribute("redirectUrl", url); }
	if (img && img != "") { 
		this.setAttribute("installImg", img); 
	} else {
		this.setAttribute("installImg", "http://webplayer.unity3d.com/installation/getunity.png");
	}
	
}


ut.UnityObject.prototype = {	
    setAttribute: function (aName, aValue) {
		    this.attributes[aName] = aValue;
	},
    addParam: function (aName, aValue) {
		    this.params[aName] = aValue;
	  },
    automaticReload: function () {
			gAutomaticReloadForwardObject = null;
			navigator.plugins.refresh();
			if (this.detectUnityWebPlayer()) {
				window.location.reload();
			} else {
				gAutomaticReloadForwardObject = this;
				setTimeout('automaticReloadForward()', 500);
			}
    },
    detectUnityWebPlayer: function () {
      var tInstalled = false;
      if (navigator.appVersion.indexOf("MSIE") != -1 && navigator.appVersion.toLowerCase().indexOf("win") != -1) {
			tInstalled = DetectUnityWebPlayerActiveX();
      } else {
       	if (this.playerVersion == 1) {
       		if (navigator.mimeTypes && navigator.mimeTypes["application/x-unity"] && navigator.mimeTypes["application/x-unity"].enabledPlugin) {
            if (navigator.plugins && navigator.plugins["Unity Web Player"]) {
               tInstalled = true;	
           	}
          }
        } else if (this.playerVersion >= 2) {
        	if (navigator.mimeTypes && navigator.mimeTypes["application/vnd.unity"] && navigator.mimeTypes["application/vnd.unity"].enabledPlugin) {
          	if (navigator.plugins && navigator.plugins["Unity Player"]) {
            	tInstalled = true;	
           	}
          }
        }
      }
      return tInstalled;	
    },
    findEar: function () {
		    this.unityEar = "";
        if (navigator.appVersion.indexOf("MSIE") != -1 && navigator.appVersion.toLowerCase().indexOf("win") != -1) {
			      this.unityEar = document.getElementById(this.getAttribute("id") + "_object");
		    } else if (navigator.appVersion.toLowerCase().indexOf("safari") != -1) {
			      this.unityEar = document.getElementById(this.getAttribute("id") + "_object")
		    } else {
			      this.unityEar = document.getElementById(this.getAttribute("id") + "_embed");
		    }
    	  document.Unity = this.unityEar;
    },	  
	  getAttribute: function (aName) {
		    return this.attributes[aName];
	  },  
	  getParams: function () {
		    return this.params;
	  },	    
    getInstallerPath: function () {
		var tDownloadURL = "";
		if (this.playerVersion == 1) {
			tDownloadURL = "";
		}
		else if (this.playerVersion >= 2) {
			if (navigator.platform == "MacIntel") {
				tDownloadURL = "http://webplayer.unity3d.com/download_webplayer-2.x/webplayer-i386.dmg";
			}
			else if (navigator.platform == "MacPPC") {
				tDownloadURL = "http://webplayer.unity3d.com/download_webplayer-2.x/webplayer-ppc.dmg";
			}
			else if (navigator.platform.toLowerCase().indexOf("win") != -1) {
				tDownloadURL = "http://webplayer.unity3d.com/download_webplayer-2.x/UnityWebPlayer.exe";
			}
		}
		return tDownloadURL;
    },   
    msg: function (aObj, aFunc, aVar) {
        this.unityEar.SendMessage(aObj, aFunc, aVar);
    },
    write: function (aElementId) {
    
    	// Write the VB detection script once
		if (navigator.appVersion.indexOf("MSIE") != -1 && navigator.appVersion.toLowerCase().indexOf("win") != -1) {
			document.write(" \n");
			document.write("<script language='VBscript'> \n");
			document.write("function DetectUnityWebPlayerActiveX \n");
			document.write("on error resume next \n");
			document.write("dim tControl \n");
			document.write("dim res \n");
			document.write("res = 0 \n");
			if (this.playerVersion == 1) {
				document.write("set tControl = CreateObject(\"UnityWebPlayer.UnityWebPlayerAXCtrl.1\") \n");
			} else if (this.playerVersion >= 2) {
				document.write("set tControl = CreateObject(\"UnityWebPlayer.UnityWebPlayer.1\") \n");
			}
			document.write("if IsObject(tControl) then \n");
			document.write("res = 1 \n");
			document.write("end if \n");
			document.write("DetectUnityWebPlayerActiveX = res\n");
			document.write("end function\n");
			document.write("</script>\n");
		}
    
		if (this.detectUnityWebPlayer()) {
       		  document.write(this.writeEmbedDOM());
       		  this.findEar();
        	  return true;
        } else {
            if (this.getAttribute("altHTML")) {
                document.write(this.getAttribute("altHTML"));
            } else if (this.getAttribute("redirectUrl")) {
                document.location.replace(this.getAttribute("redirectUrl"));
            } else {
				document.write(this.writeInstallPrompt());
            }
			return false;
        }
    },
    writeEmbedDOM: function() {
    	  var tUniSrc = "";
    	  if (this.playerVersion == 1) {
		        tUniSrc += "<object classid='clsid:36D04559-44B7-45E0-BA81-E1508FAB359F' codebase='http://otee.dk/download_webplayer/UnityWebPlayer.cab' ";
    	  } else if (this.playerVersion >= 2) {
    	  	  tUniSrc += "<object classid='clsid:444785F1-DE89-4295-863A-D46C3A781394' codebase='http://webplayer.unity3d.com/download_webplayer-2.x/UnityWebPlayer.cab#version=2,0,0,0";
    	  }
		    tUniSrc += "id='" + this.getAttribute("id") + "_object' width='" + this.getAttribute("width") + "' height='" + this.getAttribute("height") + "'><param name='src' value='" + this.getAttribute("src")+"' />"; 
		    var params = this.getParams();
    	  for(var key in params) {
        	  tUniSrc += "<param name='" + key + "' value='" + params[key] + "' />";
    	  }
    	  if (this.playerVersion == 1) {
    	      tUniSrc += "<embed type='application/x-unity' pluginspage='http://www.unity3d.com/unity-web-player-1.x' ";
    	  } else if (this.playerVersion >= 2) {
    	  	  tUniSrc += "<embed type='application/vnd.unity' pluginspage='http://www.unity3d.com/unity-web-player-2.x' ";
    	  } 
    	  tUniSrc += "id='" + this.getAttribute("id") + "_embed' width='" + this.getAttribute("width") + "' height='" + this.getAttribute("height") + "' src='" + this.getAttribute("src") + "' ";
     	  var params = this.getParams();
    	  for(var key in params){
        	  tUniSrc += [key] + "='" + params[key] + "' ";
    	  }
     	  tUniSrc += " /></object\>";
    	  return tUniSrc;
    }, 
	writeInstallPrompt: function () {
				
		var tPrompt = "<div align='center' id='UnityPrompt' style=' width: " + this.getAttribute("width") + "px;";
		if (this.getAttribute("backgroundcolor")) {
			tPrompt += " background-color: #" + this.GetAttribute("backgroundcolor") + ";";
		}
		tPrompt += "'> \n";
		if (this.playerVersion == 1) {
			tPrompt += "<a href='http://www.unity3d.com/unity-web-player-1.x'><img src='" + this.getAttribute("installImg") + "' border='0'/></a> \n";
			tPrompt += "</div> \n";
		} else if (this.playerVersion >= 2) {
			var tInstallerPath = this.getInstallerPath();
			if (tInstallerPath != "") {
				tPrompt += "<a href='" + tInstallerPath + "'><img src='" + this.getAttribute("installImg") + "' border='0'/></a> \n";
			} else {
				tPrompt += "<a href='http://www.unity3d.com/unity-web-player-2.x'><img src='" + this.getAttribute("installImg") + "' border='0'/></a> \n";
			}
			tPrompt += "</div> \n";
			this.automaticReload();
		}
		return tPrompt;
	}
}

if (!document.getElementById && document.all) { document.getElementById = function(id) { return document.all[id]; }}
var UnityObject = ut.UnityObject;