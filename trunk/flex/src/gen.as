// ActionScript file
import compo.*;
import compo.dgOeuvres;

import mx.managers.PopUpManager;

[Bindable] public var uti:Object;

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
		dgOeuParam.Init();
	}
}
