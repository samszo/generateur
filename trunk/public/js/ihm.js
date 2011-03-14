

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-3573757-14']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();


function GetAjax(url){
	//$('#loadImage').show();
	//$('#resultat').load(url, {limit: 25}, function(){$('#loadImage').hide();});
	$('#resultat').html("<b>CALCUL EN COURS</b>");
	$('#resultat').load(url);
}