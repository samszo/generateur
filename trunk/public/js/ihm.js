function GetAjax(url){
	//$('#loadImage').show();
	//$('#resultat').load(url, {limit: 25}, function(){$('#loadImage').hide();});
	$('#resultat').html("<b>CALCUL EN COURS</b>");
	$('#resultat').load(url);
}