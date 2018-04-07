var supportBrowser = true;

function readyJs(fn) {
  if (document.readyState != 'loading'){
    fn();
  } else if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', fn);
  } else {
    document.attachEvent('onreadystatechange', function() {
      if (document.readyState != 'loading')
        fn();
    });
  }
}

readyJs(function() {

	function isIE () {
	  var myNav = navigator.userAgent.toLowerCase();
	  return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
	}

	var elErrorMsg = document.getElementById("errorIE");

	if (isIE () && isIE () > 9) {
		elErrorMsg.innerHTML = 'Ups! Su navegador no está actualizado. Le recomendamos que utilice la última versión de <a href="https://www.google.com/chrome/">Chrome</a> o <a href="https://www.mozilla.org/es-ES/firefox/new/">Firefox</a>.';
		supportBrowser = false;
	}else if(typeof jQuery == 'undefined') {
  	elErrorMsg.innerHTML = 'Ups! Se ha producido un problema de conexión, Le recomendamos refrescar el sitio web para su correcto funcionamiento.';
  	supportBrowser = false;
  }else if(!navigator.cookieEnabled){
  	elErrorMsg.innerHTML = 'Ups! Su navegador deben tener las cookies habilitadas para utilizar el sitio web.';
  	supportBrowser = false;
  }

  if(!supportBrowser){elErrorMsg.style.display = 'block';}

});
