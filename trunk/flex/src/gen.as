// ActionScript file
import compo.*;
import compo.dgOeuvres;

import mx.controls.Alert;
import mx.managers.PopUpManager;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;

import spark.components.Label;

[Bindable] public var uti:Object;

public function testerGen(gen:String, fct):void
{
	
}

protected function testerMoteur_resultHandler(event:ResultEvent):void
{
	// TODO Auto-generated method stub
	
}

public function faultHandlerService(fault:FaultEvent):void
{
	var str:String;
	str = "Code: "+fault.fault.faultCode.toString()+"\n"+
		"Detail: "+fault.fault.faultDetail.toString()+"\n"+
		"String: "+fault.fault.faultString.toString()+"\n";
	
	Alert.show(str, "ERREUR : Gen AIR");
}

public function login():void
{
	/*création de la fenêtre de login
	var twLog:twLogin= twLogin(
		PopUpManager.createPopUp(this, twLogin, true));
	twLog.callback = init;
	PopUpManager.centerPopUp(twLog);
	*/
	this.uti = new Object(); 
	uti.login = "samszo";
	uti.idUti = "2";
	uti.role = "administrateur";
	boxGen.visible = true;
	//
} 

private function init():void {

	utiLog.text = uti.login;
}

public function ShowSelection(idOeu:int):void{
	if(idOeu==-1){
		dgOeuParam.visible = false;	
	}else{
		dgOeuParam.visible=true;
		dgOeuParam.idOeu = idOeu;
		dgOeuParam.bInit = true;
		dgOeuParam.init();
	}
}

public function verifDico():Boolean
{
	//vérifie que l'oeuvre possède bien un exemplaire de chaque dictionnaire
	var verifDico:Array = new Array();
	for each(var dico:Object in dgOeuParam.arrDicoAssos){
		verifDico[dico.type]=1;
	}
	var strVerif:String = "";
	if(!verifDico["concepts"])strVerif+="concepts\n";
	if(!verifDico["conjugaisons"])strVerif+="conjugaisons\n";
	if(!verifDico["déterminants"])strVerif+="déterminants\n";
	if(!verifDico["négations"])strVerif+="négations\n";
	if(!verifDico["pronoms_complement"])strVerif+="pronoms_complement\n";
	if(!verifDico["pronoms_sujet"])strVerif+="pronoms_sujet\n";
	if(!verifDico["syntagmes"])strVerif+="syntagmes\n";
	if(strVerif!=""){
		strVerif = 	"Il manque des dictionnaires.\nVeuillez ajouter à votre oeuvre le(s) dictionnaire(s) suivant(s):\n"+strVerif;
		Alert.show(strVerif, "Vérification dictionnaire");
		return false;
	}
	return true;
}

