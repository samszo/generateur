// ActionScript file
import compo.*;
import compo.dgOeuvres;

import mx.controls.Alert;
import mx.events.CloseEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;
import mx.rpc.remoting.RemoteObject;

import spark.components.Label;

[Bindable] public var uti:Object;
[Bindable] public var arrCtrls:Array;
[Bindable] public var arrVerifDico:Array;
[Bindable] public var ctrlActi:Object;
public var idItem:String;
public var dataItem:Array;
public var arrItem:Array;
public var rocItem:Object;
public var actionItem:String;
public var actisItem:String;
public var idDicoItem:String;

public function testerGen(txts:Array, ctrls:Array):void
{
	arrCtrls = ctrls;
	ROMOTEUR.Tester(txts, arrVerifDico);
}

protected function testerMoteur_resultHandler(event:ResultEvent):void
{
	var arrResult:Array = event.result as Array;
	var i:int = 0;
	for each(var txt:String in arrResult){
		arrCtrls[i].text = txt;
		i++;
	}	
}

public function verifActi(arrR:Array, action:String, actis:String, ROC:Object, id:String, data:Array, idDico:String):void
{
	idItem=id;
	dataItem=data;
	rocItem=ROC;
	actionItem=action;
	arrItem=arrR;
	actisItem=actis;
	idDicoItem=idDico;
	if(arrR['nbGen']!=0){
		if(arrR['nbUti']==1){
			if(action=="supprimer"){
				Alert.show("Vous ne pouvez pas supprimer l'item."
					+"\nIl est utilisé dans "+arrR['nbGen']+" expression(s)."
					+ "\nVous devez supprimer ces expresions avant de pouvoir supprimer l'item."
					,"Vérification disponibilité de l'item");										
			}else{
				Alert.show("L'item est utilisé dans "+arrR['nbGen']+" expression(s)."
					+ "\nVoulez-vous que ces expressions soient impactées : cliquer sur 'OUI'"
					+ "\nPréférez vous créer un nouvel item : cliquer sur 'NON'"
					,"Vérification disponibilité de l'item", 3, this, verifSoloItemHandler);										
			}	
		}else{
			var mess:String ="";
			if(action=="modifier"){
				mess="\nPréférez vous créer un nouvel item : cliquer sur 'NON'";
			}
			
			Alert.show("Vous ne pouvez pas "+action+" l'item."
				+"\nIl est utilisé dans "+arrR['nbGen']+" expression(s)."
				+"\nVoulez vous prévenir le(s) "+arrR['nbUti']+" utilisateur(s)  : cliquer sur 'OUI'"
				+ mess
				,"Vérification disponibilité de l'item", 3, this, verifMultiItemHandler);									
		}
	}else{
		if(action=="supprimer")
			rocItem.remove(idItem);
		if(action=="modifier"){
			rocItem.edit(idItem, dataItem);					
		}
	}	
}


private function verifSoloItemHandler(event:CloseEvent):void
{
	if (event.detail == Alert.YES) 
	{					
		rocItem.edit(idItem, dataItem);					
	}
	if (event.detail == Alert.NO) 
	{
		if(actionItem=="modifier"){
			rocItem.ajouter(dataItem);											
		}
	}
}

private function verifMultiItemHandler(event:CloseEvent):void
{
	if (event.detail == Alert.YES) 
	{
		ajoutActiUti(actisItem, arrItem['idsUti'], idDicoItem, false);
	}
	if (event.detail == Alert.NO && actionItem=="modifier") 
	{
		rocItem.ajouter(dataItem);					
	}
}

public function ajoutActiUti(actis:String, utis:String, idDico:String, ctrl:Object):void
{
	ctrlActi = ctrl;
	ROACTI.ajoutForUtis(actis, utis, dgOeuParam.idOeu, idDico);
}

protected function ajoutForUtis_resultHandler(event:ResultEvent):void
{
	// TODO Auto-generated method stub
	var t:String="1";
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
	//
	boxGen.visible = true;
	
} 

private function init():void {

	utiLog.text = uti.login;
}

public function ShowSelection(idOeu:int):void{
	//réinitiaise le détail des items du dico
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
	if(!arrVerifDico["déterminants"])strVerif+="déterminants\n";
	if(!arrVerifDico["negations"])strVerif+="négations\n";
	if(!arrVerifDico["pronoms_complement"])strVerif+="pronoms_complement\n";
	if(!arrVerifDico["pronoms_sujet"])strVerif+="pronoms_sujet\n";
	if(!arrVerifDico["syntagmes"])strVerif+="syntagmes\n";
	if(strVerif!=""){
		strVerif = 	"Il manque des dictionnaires.\nVeuillez ajouter à votre oeuvre le(s) dictionnaire(s) suivant(s):\n"+strVerif;
		Alert.show(strVerif, "Vérification dictionnaire");
		return false;
	}
	return true;
}

