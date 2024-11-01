/*
Now write out the applicable links
*/
createCookie('t', 1);
var c = readCookie('t');
if(c && document.getElementById) {
	var l = readCookie('bm');
	var gt = imgGotoOff;
	var ct = imgClearOff;
	if(l) {
		gt = imgGotoOn;
		ct = imgClearOn;
	}
	document.write('<a href="#" onClick="bm();return false;"><img src="'+imgTag+'" alt="Tag This Page" border="0"></a>');
	document.write('<a href="#" onClick="gto();return false;"><img src="'+gt+'" alt="Goto Tag" border="0" id="gtc"></a>');
	document.write('<a href="#" onClick="bmc();return false;"><img src="'+ct+'" alt="Clear Tag" border="0" id="rmc"></a>');
	document.write('<a href="#" onMouseOver="document.getElementById(\'bmh\').style.visibility=\'visible\';" onMouseOut="document.getElementById(\'bmh\').style.visibility=\'hidden\';" onClick="return false;"><img src="'+imgInfo+'" alt="" border="0"></a>');
	document.write('<div id="bmh" style="padding:4px;font-size:1;margin: -150px 0 0 -150px;font-family:sans-serif;position:absolute;width:150px;background-color:#EDEDED;border: 1px solid #CFCFCF;visibility:hidden;text-align:left;">COMIC BOOKMARK<br /><br />Click "Tag Page" to bookmark any comic page. Then when you return to the site you can always pick up where you left off by clicking "Goto Tag".<br /><br />Note: Deleting browser cookies will clear your tag.</div>');
}

/*
Below are our functions for this little script
*/
function bm() {
	if(document.getElementById) {
		document.getElementById('gtc').src = imgGotoOn;
		document.getElementById('rmc').src = imgClearOn;
	}
	createCookie("bm", window.location, cl);
}

function bmc() {
	if(document.getElementById) {
		document.getElementById('gtc').src = imgGotoOff;
		document.getElementById('rmc').src = imgClearOff;

	}
	createCookie("bm","",-1);
}

function gto() {
	var g = readCookie('bm');
	if(g) {
		window.location = g;
	}	
}

/*
The follow functions have been borrowed from Peter-Paul Koch.
Please find them here: http://www.quirksmode.org
*/
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	} else var expires = "";
	document.cookie = name+"="+value+expires+"; path="+comicDir;
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
