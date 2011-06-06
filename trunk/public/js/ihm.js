
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
	var ForceCalcul = $('input[name=ForceCalcul]').is(':checked')
	$('#resultat').html("<b>CALCUL EN COURS</b>");
	$('#resultat').load(url+"&ForceCalcul="+ForceCalcul);
}

function GetGene(){
	$('#result').html("<b>CALCUL EN COURS</b>");
	var url = 'capture1.php?';
	var xml = $('input[name=xml]').is(':checked');
	var err = $('input[name=err]').is(':checked');
	var gen = $('#gen').val();
	var nb = $('input[name=nb]').val();
	$('#result').load(url+"gen="+gen+"&xml="+xml+"&nb="+nb+"&err="+err);
}