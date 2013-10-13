// ActionScript file

import compo.*;
import compo.dgOeuvres;

import flash.events.MouseEvent;
import flash.utils.getQualifiedClassName;

import flashx.textLayout.conversion.TextConverter;

import mx.collections.ArrayCollection;
import mx.controls.Alert;
import mx.events.CloseEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;
import mx.rpc.remoting.RemoteObject;

import spark.components.Label;
import spark.components.RichText;

[Bindable] public var uti:Object;
[Bindable] public var arrCtrls:Array;
[Bindable] public var arrVerifDico:Array;
[Bindable] public var ctrlActi:Object;
[Bindable] public var allConcept:Array;
[Bindable] public var acConj:ArrayCollection;

public var idItem:String;
public var dataItem:Array;
public var arrItem:Array;
public var rocItem:Object;
public var actionItem:String;
public var actisItem:String;
public var idDicoItem:String;

//public var urlDomain:String = "http://localhost/generateur";
public var urlDomain:String = "http://generator.digitalartimag.org";
public var urlAPI:String = urlDomain+"/services/api.php";
public const ENDPOINT_IMPORT:String = urlDomain+"/services/import.php";
public const ENDPOINT_EXPORT:String = urlDomain+"/services/export.php";

protected function gereuti_clickHandler(event:MouseEvent):void
{
	var twU:twUti= twUti(
		PopUpManager.createPopUp(this, twUti, true));
	PopUpManager.centerPopUp(twU);
	
}


public function testerGen(txts:Array, ctrls:Array):void
{
	arrCtrls = ctrls;
	ROMOTEUR.Tester(txts, arrVerifDico);
}


public function verifierGen(txt:String, ctrl:Object):void
{
	arrCtrls = [ctrl];
	ROMOTEUR.Verifier(txt, arrVerifDico);
}

protected function testerMoteur_resultHandler(event:ResultEvent):void
{
	var arrResult:Array = event.result as Array;
	var i:int = 0;
	for each(var txt:String in arrResult){
		//vérifie le type du champ texte		 
		var oName:String = getQualifiedClassName(arrCtrls[i]);
		if(oName=="spark.components::RichText" || oName=="spark.components::RichEditableText")
			arrCtrls[i].textFlow = TextConverter.importToFlow(txt, TextConverter.TEXT_FIELD_HTML_FORMAT);
		else
			arrCtrls[i].text = txt;
		i++;
	}	
}

protected function verifierMoteur_resultHandler(event:ResultEvent):void
{
	//vérifie le type du champ texte		 
	var oName:String = getQualifiedClassName(arrCtrls[0]);
	if(oName=="spark.components::RichText" || oName=="spark.components::RichEditableText")
		arrCtrls[0].textFlow = TextConverter.importToFlow(event.result, TextConverter.TEXT_FIELD_HTML_FORMAT);
	else
		arrCtrls[0].text = event.result;
	
}

public function verifActi(arrR:Array, action:String, actis:String, ROC:Object, id:String, data:Array, idDico:String):void
{
	
	var twCM:twConfirmModif= twConfirmModif(
		PopUpManager.createPopUp(this, twConfirmModif, true));
	twCM.idItem=id;
	twCM.dataItem=data;
	twCM.rocItem=ROC;
	twCM.actionItem=action;
	twCM.arrItem=arrR;
	twCM.actisItem=actis;
	twCM.idDicoItem=idDico;
	PopUpManager.centerPopUp(twCM);

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
	//création de la fenêtre de login
	var twLog:twLogin= twLogin(
		PopUpManager.createPopUp(this, twLogin, true));
	twLog.callback = init;
	PopUpManager.centerPopUp(twLog);
	/*
	this.uti = new Object(); 
	uti.login = "samszo";
	uti.idUti = "2";
	uti.role = "administrateur";
	init();
	boxGen.visible = true;
	*/
	
} 

private function init():void {

	utiLog.text = uti.login;
	gmOeuvre.bAjout = uti.écriture;
	gmOeuvre.bRemove = uti.suppression;
	dgOeuParam.bModif = uti.écriture;
	dgOeuParam.bRemove = uti.suppression;
	
}


public function ShowSelection(idOeu:int):void{
	//réinitiaise le détail des items du dico
	dgOeuParam.tn.selectedIndex = 0;
	dgOeuParam.detailDico.oItem = false;
	dgOeuParam.detailDico.init();
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
	arrVerifDico = new Array();
	for each(var dico:Object in dgOeuParam.arrDicoAssos){
		/**TODO: rendre plus cohérente la gestion des types de dictionnaires
		 **/ 
		if(String(dico.type).substr(0,7)=="pronoms" ){
			if(arrVerifDico["pronoms"]){
				arrVerifDico["pronoms"]+=", "+dico.id_dico;
			}else{
				arrVerifDico["pronoms"]=dico.id_dico;
			}			
		}
		if(String(dico.type)=="négations" ){
			if(arrVerifDico["negations"]){
				arrVerifDico["negations"]+=", "+dico.id_dico;
			}else{
				arrVerifDico["negations"]=dico.id_dico;
			}			
		}
		if(arrVerifDico[dico.type]){
			arrVerifDico[dico.type]+=", "+dico.id_dico;
		}else{
			arrVerifDico[dico.type]=dico.id_dico;
		}
	}
	var strVerif:String = "";
	if(!arrVerifDico["concepts"])strVerif+="concepts\n";
	if(!arrVerifDico["conjugaisons"])strVerif+="conjugaisons\n";
	else ROCONJ.findByIdDicoVerbe(arrVerifDico["conjugaisons"]);
	if(!arrVerifDico["déterminants"])strVerif+="déterminants\n";
	if(!arrVerifDico["negations"])strVerif+="négations\n";
	if(!arrVerifDico["pronoms_complement"])strVerif+="pronoms_complement\n";
	if(!arrVerifDico["pronoms_sujet"])strVerif+="pronoms_sujet\n";
	if(!arrVerifDico["pronoms_sujet_indefini"])strVerif+="pronoms_sujet_indefini\n";
	if(!arrVerifDico["syntagmes"])strVerif+="syntagmes\n";
	if(strVerif!=""){
		strVerif = 	"Il manque des dictionnaires.\nVeuillez ajouter à votre oeuvre le(s) dictionnaire(s) suivant(s):\n"+strVerif;
		Alert.show(strVerif, "Vérification dictionnaire");
		return false;
	}
	return true;
}

protected function findByIdDicoVerbe_resultHandler(event:ResultEvent):void
{
	if(event.result){
		this.acConj = new ArrayCollection(event.result as Array); 
	}
}

