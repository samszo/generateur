
//import sp from '../node_modules/synchronized-promise/dist/index.js';
//import sp from '../node_modules/synchronized-promise'
//const sp = require('synchronized-promise')
class moteur {
    constructor(params) {
        var me = this;
        this.api = params.api ? params.api : false;
        this.tgtContent = params.tgtContent ? params.tgtContent : false;
        this.choix = params.choix ? params.choix : false;
        this.oeuvre = params.oeuvre;
        this.appUrl = params.appUrl;
		//this.sp = sp.sp;
		//this.syncSearchClass = this.sp(me.oeuvre.searchClass);
        this.strct = []; 
        this.posis = [];
        this.caracts = []; 
        this.ordre = 0; 
        this.potentiel = 0;
        this.tables = {
            c:{type:'concepts',t:'gen_concepts',k:['lib','type']},
            d:{type:'déterminants',t:'gen_determinants',k:'num'}
        }; 
        this.init = function () {
            console.log('init moteur');
        }

        this.genere = function(g){
			let c, cls= [], p=[];
            //décompose le générateur
            me.ordre = 0
            for (var i = 0; i < g.valeur.length; i++) {
				me.strct[me.ordre]={};
                c = g.valeur.charAt(i);
                if(c == "["){
                    //c'est le début d'une classe on initialise les positions
                    //$this->arrPosi = false;
                    //on récupère la valeur de la classe et la position des caractères dans la chaine
					cls = getClassVals(g.valeur,i);
					me.strct[me.ordre]=cls;
                    i = cls.fin;
                }else if(c == "F"){
                    if(g.valeur.charAt(i+1)=="F"){
                        /*c'est la fin du segment
                        $this->arrSegment[$this->segment]["ordreFin"]= $this->ordre;
                        $this->segment ++;
                        */
                        i++;
                    }else{
                        //on ajoute le caractère
                        me.strct[me.ordre].txt = c;
                    }
                }else{
                    //on ajoute le caractère
                    me.strct[me.ordre].txt = c;
                }
                me.ordre ++;            
			} 
			console.log(me.strct);
			//récupère la définition des classes
			me.strct.forEach((s,i)=>{
				if(s.cls){
					s.cls.forEach((c,j)=>p.push({'i':i, 'j':j, p:getClass(c.k)}));
				}
			});
			Promise.all(pl.map(d=>d.p)).then((vals) => {


        }
	/*
	async function setClass(gen, i=0){
		let r = getClassVals(gen,i), p=[];
        r.cls.forEach(c=>p.push(getClass(c)));	        	
		Promise.all(p).then((values) => {
			return r.fin;
		});

	}
	*/

    function getClassVals(gen,i){

		if(!gen){
	        return "";
		}
		
		let cls=[],acc,g,deb = gen.indexOf("[",i), fin = gen.indexOf("]",deb+1);
		
		//dans le cas de ado par exemple = pas de crochet
		if(deb===false){
	        g = gen;
		}else if(!fin){
			me.strct[me.ordre].error = "Item mal formaté.<br/>il manque un ] : "+gen+"<br/>";			
        	return {"deb":deb,"fin":strlen(gen),"g":gen,"cls":[]};
		}else{
			//on récupère la valeur de la classe
	        g = gen.substring(deb+1, fin);
		}        
        
        //on récupère la définition de l'accord 
        acc = g.split("||");
	    g = acc[0];        	 	
        if(acc.length>1){
	        me.strct[me.ordre].accord = acc[1];
        }
        //on récupère le tableau des class
        g.split("|").forEach(c=>{
			//gestion du changement de position de la classe
			let posis=c.split("@");
			if(posis.length>1){
				//$this->trace("récupère le vecteur du déterminant ".$this->ordre);
				let vAdj, vSub, vDet = me.strct[me.ordre].vecteur, ordreDet = me.ordre;
				cls.push({'k':me.posis[0]});
				cls.push({'k':me.posis[1]});
				/*
				//change l'ordre pour que la class substantif soit placée après
				me.ordre ++;
				if(!me.strct[me.ordre])me.strct[me.ordre]={};
				me.strct[me.ordre].vecteur = vDet; 
				//calcul le substantifs
				getClass(me.posis[1]);
				vSub = me.strct[me.ordre].vecteur;
				//redéfini l'ordre pour que la class adjectif soit placée avant
				me.ordre --;
				//avec le vecteur du substantif
				me.strct[me.ordre].vecteur = vSub; 
				//calcul l'adjectif
				getClass(me.posis[0]);
				vAdj = me.strct[me.ordre-1].vecteur;        	
				//rédifini l'élision et le genre du déterminant avec celui de l'adjectif
				me.strct[ordreDet].vecteur.elision=vAdj.elision;
				me.strct[ordreDet].vecteur.genre=vSub.genre;
				*/
			}else{
				cls.push({'k':c});
			}
		})

        
        return {"deb":deb,"fin":fin,"g":g,"cls":cls};

    }

    function getClass(c){

		if(c=="")return;		
        
		//vérifie si la class est un déterminant
		if(!isNaN(c)){
			getDeterminant(c);
		}
		
		//vérifie si la class possède un déterminant de class
		let cSpe, dtm, p = c.indexOf("_");
		if(p>0){
			dtm = c.split("_");			
			switch (dtm[0]) {
				case "a":
					getAdjectifs(c);
					break;
				case "m":
					getSubstantif(c);
					break;
				case "v":
					getVerbe(c);
					break;
				case "s":
					getSyntagme(c,false);
					break;
			}
		}else if(c.substring(0,5)=="carac"){
			//la class est un caractère
			cSpe = c.replace("carac", "carac_");
			getClassSpe(cSpe);
		}else{
			//vérifie si la class possède un blocage d'information
			p = c.indexOf("#");
			if(p>0){
				cSpe = c.replace("#",""); 
				getSyntagme(cSpe);	
			}
	
			//vérifie si la class possède un blocage d'information
			if(c.substring(0,1)=="=" && Number.isNaN(Number(c.substring(1,1)))){
				getBlocage(c);	
			}        
			if(c.substring(0,1)=="=" && c.substring(1,1)=="x"){
				getBlocage(c);	
			}        
			
			//vérifie si la class est un type spécifique
			p = c.indexOf("-");
			if(p>0){
				cSpe = c.substring(0,p)+"_".c.substring(p+1);
				getClassSpe(cSpe);
			}			
		}
    }

	
	function getSubstantif(c, strct=false){

        //récupération du substantif
        if(!strct) strct = getAleaClass(c);

        if(strct){
        	
	        //ajoute le vecteur
	        me.strct[me.ordre].vecteur.genre = strct.genre;
	        me.strct[me.ordre].vecteur.elision = strct.elision;
	        
	        //ajoute le substantif
	        me.strct.substantif = this.strct;
		        	        
        }        
        return this.strct;
	}


	function getAleaClass(c){
		
        //cherche la définition de la class
        let alea, aCpt, cpt = getClassDef(c);
        
        //cas des class caract
        if(c.substring(0,7)=="carac_t" || c.substring(0,5)=="carac"){
        	//on vérifie que le caractère n'est pas déjà calculé
        	if(me.caracts[c]){
        		//on retourne le carac déjà choisi
        		return getClassSpe(me.caracts[c]);
        	}else{
        		//on récupère les possibilité de caracX
        		let cCarac = c.replace("carac_t","carac_"),
		            cptCarac = getClassDef(cCarac);
        		//on ajoute à la liste de choix les possibilité de caractX
                cptCarac["dst"].foreach(c=>{
                    if(!cpt.dst)cpt.dst=[];
                    cpt.dst.push(c);
                });
        	}
        	
        }
        
        //cas des classes théoriques et des erreurs
        if(cpt.dst.length<1) return false;

		me.strct[me.ordre].concept.id_concept = cpt.src.id_concept;
		me.strct[me.ordre].concept.id_dico = cpt.src.id_dico;
		
        //enregistre le potentiel
        me.potentiel += cpt.dst.length;
        
        if(me.choix=="tout" && me.niv < 1){
        	verifClass(cpt);
        	aCpt = false;
        }else{
	        //choisi un concept aléatoirement
            d3.randomInt(6)
            alea = d3.randomInt(cpt.dst.length-1);        	        
	        aCpt = getClassGen(cpt.dst[alea],c);        
        }

        if(aCpt){
        	//conserve le parent pour le lien vers l'administration
        	aCpt.idParent = cpt.src.id_concept;
	        //vérifie s'il faut conserver un caractx
	        if(c.substring(0,7)=="carac_t"){
	        	me.caracts[c] = cpt.dst[alea];
	        }
        }
        
        //vérifie s'il faut transférer le déterminant de verbe
        if(cpt.src.type=="v" && me.strct[me.ordre-1].determinant_verbe && !me.strct[me.ordre-1].verbe){       	
        	me.strct[me.ordre].determinant_verbe=me.strct[me.ordre-1].determinant_verbe;
        }
        //vérifie s'il faut transférer le déterminant dus substantif
        if((cpt.src.type=="m" || cpt.src.type=="carac") && me.strct[me.ordre-1].vecteur.pluriel){       	
			me.strct[me.ordre].vecteur.pluriel=me.strct[me.ordre-1].vecteur.pluriel;
        }
        
		return $cpt; 			
		
	}

    function getClassDef(c){

        //TODO:gestion du cache
        //récupère la définition de la class
        let def =c.split("_"),
		q = [me.tables.c.k[1]+',eq,'+def[0],me.tables.c.k[0]+',eq,'+def[1]],
		cpt = me.oeuvre.searchClass(me.tables.c,q);
		//cpt = me.syncSearchClass(me.tables.c,q);
        return cpt;
	}
    function getDeterminant(c){
        let cls = false, pluriel=false;
        if(!me.strct[me.ordre].vecteur)me.strct[me.ordre].vecteur = {};
        if(!me.strct[me.ordre].determinant)me.strct[me.ordre].determinant = {};
        if(!me.strct[me.ordre].determinant_verbe)me.strct[me.ordre].determinant_verbe = {};

        //vérifie si le déterminant est pour un verbe
        if(c.length > 6){
        	if(c==0 && me.ordre > 0){
        		//vérifie si le determinant n'est pas transmis
                for (let i = me.ordre-1; i >= 0; i--){
                    if(me.strct[i].determinant_verbe!=0){
                        cls = me.strct[i].determinant_verbe;
                        i=-1;
                    }
                }
        	}
			me.strct[me.ordre].determinant_verbe = cls;
	        return cls;
        }       	
        
        //vérifie s'il faut chercher le pluriel
        if(c >= 50){
        	pluriel = true;
        	c = c-50;
        }       			
        //ajoute le vecteur
        me.strct[me.ordre].vecteur.pluriel = pluriel; 
        //vérifie s'il faut chercher le déterminant
        if(c!=99 && c!=0){
            cls = me.oeuvre.searchClass(me.tables.d,[me.tables.d.k+',eq,'+c]);
			//cls = me.syncSearchClass(me.tables.d,[me.tables.d.k+',eq,'+c]);

            //ajoute le déterminant
            me.strct[me.ordre].determinant = cls;                                    
            return cls;
        }else{
            me.strct[me.ordre].determinant = cls;                                    
            return cls;
        }
        
        /*vérifie si le determinant n'est pas transmis
        if(c==0){
        	//la transmission se fait par [=x...]
        	for($i = $this->ordre-1; $i > 0; $i--){
        		if(isset($this->arrClass[$i]["vecteur"])){
	        		if(intval($this->arrClass[$i]["determinant"])!=0){
						$arrClass = $this->arrClass[$i]["determinant"];
						$pluriel = $this->arrClass[$i]["vecteur"]["pluriel"];
						$i=-1;        				
	        		}
        		}
        	}
        }
        */

    }

    this.init();
    
    }
}
export default { moteur };