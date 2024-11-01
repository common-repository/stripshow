jQuery(document).ready(function($){
	existing_cookie = checkCookie('stripshow_bookmark');
	$('.bookmark-set').click(function() {
		createCookie('stripshow_bookmark',permalink,31);
		checkCookie('stripshow_bookmark');
		});
	$('.bookmark-clear').click(function() {
		if (checkCookie('stripshow_bookmark')) {
			createCookie('stripshow_bookmark','',-1);
			checkCookie('stripshow_bookmark');
			}
		});
	});

function checkCookie(name) {
	var existing_cookie = readCookie(name);
	if (!existing_cookie) {
		jQuery('.bookmark-goto > a').hide();
		jQuery('.bookmark-clear > a').hide();
		return false;
	} else {
		jQuery('.bookmark-goto > a').attr('href',existing_cookie);
		jQuery('.bookmark-goto > a').show();
		jQuery('.bookmark-clear > a').show();
		return existing_cookie;
		}
	}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	} else var expires = "";
	if (sitepath == '') sitepath = '/';
	var cookie_string =  name+"="+value+expires+"; path="+sitepath;
	document.cookie = cookie_string;
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

