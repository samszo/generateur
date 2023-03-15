import {dico} from '../modules/dico.js';
import {modal} from '../modules/modal.js';
import {modalAddOeuvre} from '../modules/modal.js';
import jscrudapi from '../node_modules/js-crud-api/index.js';

export class oeuvres {
    constructor(params) {
        var me = this;
        this.tgtMenu = params.tgtMenu
        this.tgtList = params.tgtList
        this.tgtContent = params.tgtContent
        this.appUrl = params.appUrl ? params.appUrl : false; 
        this.apiUrl = params.apiUrl ? params.apiUrl : 'api.php'; 
        this.apiStatsUrl = this.apiUrl+'/apiStats/'; 
        this.auth = params.auth ? params.auth : false;
        this.wGen = params.wGen ? params.wGen : false;
        this.api = this.auth.api;
        this.curOeuvre;
        this.curDico;
        this.oeuvres;
        this.dicos=[];
        this.dicosUti=[];
        var mAdd,mMessage=new modal(), mAddOeuvre, mAddOeuvreBody;
        this.init = function () {
            getOeuvres();
            //ajoute la modal pour l'ajout d'oeuvre'
            let m = d3.select('body').append('div')
                .attr('id','modalOeuvreAdd').attr('class','modal').attr('tabindex',-1);
            m.html(modalAddOeuvre);
            mAddOeuvreBody = m.select('.modal-body');
            mAddOeuvre = new bootstrap.Modal('#modalOeuvreAdd');
            //gestion des événements
            d3.select('#btnaddNewOeuvre').on('click',addNewOeuvre)        

        }
        function addNewOeuvre(){
            let nom = mAddOeuvreBody.select("#inpOeuNom").node().value,
            licence = mAddOeuvreBody.node().querySelector('input[name="oeuLicence"]:checked').value,
            lang = mAddOeuvreBody.node().querySelector('input[name="oeuLangue"]:checked').value;
            me.api.create('gen_oeuvres', {'lib':nom,'licence':licence, 'uti_id':me.auth.user.id}).then(
                idOeu=>{
                    //ajoute le dictionnaire de l'oeuvre
                    me.api.create('gen_dicos', {'nom':nom,'type':'concepts','langue':lang,'general':0,'licence':licence}).then(
                        idDico=>{
                            //ajoute le lien entre l'oeuvre, le dico et l'utilisateur
                            me.api.create('gen_oeuvres_dicos_utis', {'id_oeu':idOeu,'id_dico':idDico,'uti_id':me.auth.user.id});
                        }
                    );   
                    //ajout des dictionnaires généraux à l'oeuvre
                    me.api.list('gen_dicos',{filter:['langue,eq,'+lang,'general,eq,1']}).then(
                        result=>{
                            let inserts=[]; 
                            result.records.forEach(d => {
                                inserts.push({'id_oeu':idOeu,'id_dico':d.id_dico,'uti_id':me.auth.user.id});
                            });
                            me.api.create('gen_oeuvres_dicos_utis', inserts);
                        }
                    );                         
                    me.api.read('gen_oeuvres',idOeu).then(
                        oeu=>{
                            //affiche l'oeuvre
                            me.oeuvres.push(oeu);
                            me.showOeuvre(null,oeu);
                            mAddOeuvre.hide();
                        });

                }    
            ).catch (
                error=>console.log(error)
            );
        }
        function getOeuvres(){
            me.api.list('gen_oeuvres').then(
                result=>{
                    me.oeuvres = result.records;
                    //ajoute le bouton de création
                    me.oeuvres.unshift(
                        {'id_oeu':-1,'lib':'New work'}, 
                        {'id_oeu':-2,'lib':'<hr class="dropdown-divider">'}
                    );
                    d3.select(me.tgtMenu).selectAll('li').data(me.oeuvres).enter().append('li')
                        .append('button')
                        .attr('type', "button")
                        .attr('class',"dropdown-item")
                        .attr('id',d=>d.id_oeu)
                        .on('click',me.showOeuvre)
                        .html(d=>d.lib);
                    if(me.appUrl.params && me.appUrl.params.has('id_oeu'))me.showOeuvre(null,null,me.appUrl.params.get('id_oeu'));
                }
            ).catch (
                error=>{
                    mMessage.setBody('<h3>This work does not exist</h3><p>'+error+'</p>');
                    mMessage.setBoutons([{'name':"Close"}])                
                    mMessage.show();
                }
            );        
        }
        this.showOeuvre = function (e,oeu,id){
            if(id)oeu=me.oeuvres.filter(r=>r.id_oeu==id)[0];
            else me.appUrl.params=false;
            if(oeu.id_oeu==-1)addOeuvre();
            else if(oeu.id_oeu==-2)return;
            else{
                me.curOeuvre=oeu;
                me.appUrl.change('id_oeu',oeu.id_oeu);
                d3.select(me.tgtContent).selectAll('div').remove();
                let list = d3.select(me.tgtList)
                list.select('h1').remove();
                let tools = me.auth.userAdmin || oeu.uti_id == me.auth.user.id ?
                    '<button id="btnDeleteOeuvre" type="button" class="btn btn-danger btn-sm mx-2"><i class="fa-solid fa-trash-can"></i></button>'
                    : "";

                list.append('h1').html(
                    oeu.lib+tools
                );
                if(tools)list.select('#btnDeleteOeuvre').on('click',verifDeleteOeuvre);
                showDicos(oeu);
            }
        }
        function verifDeleteOeuvre(){
            let b = '<h3>Attention the deletion of the work leads to the deletion of : </h3>';
            //vérifie les usages de l'oeuvre
            d3.json(me.apiStatsUrl+'uses/oeuvre/'+me.curOeuvre.id_oeu).then(data=>{
                b+='<h4>'+data[0].nbDico+' dictionaries</h4>';
                b+='<h4>'+data[0].nbConcept+' concept'+(data[0].nbConcept > 0 ? 's' : '')+'</h4>';
                mMessage.setBody(b);
                mMessage.setBoutons([{'name':"Close"},
                    {'name':"Delete All",'class':'btn-danger','fct':me.delete}
                    ])                
                mMessage.show();    
            });
        }
        this.delete = function(){
            console.log('removeOeuvreVerif');
            //construction des suppressions
            let p = [];
            me.dicos.forEach(d=>{
                //on ne supprime que les dictionnaires d'oeuvre
                if(d.general==0)p.push(new dico({'d':d,'api':me.api,'remove':true}));
            });
            //on supprime l'oeuvre et ses liens
            p.push(me.api.delete('gen_oeuvres',me.curOeuvre.id_oeu));

            Promise.all(p).then((values) => {
                d3.select('#listDicos').select('h1').remove();                
                d3.select('#listDicos').selectAll('div').remove();                
                this.init();
                mMessage.hide();
            });

        }
        function getStat(){
            console.log('removeOeuvreVerif');
        }
        function addOeuvre(){
            if(!me.auth.user){
                mMessage.setBody('<h3>Log in to create a work</h3>');
                mMessage.setBoutons([{'name':"Close"}])                
                mMessage.show();
            }else{
                mAddOeuvre.show();
            }
        }
        function showDicos(oeu){
            //récupère les dicos de l'oeuvre
            me.api.list('gen_oeuvres_dicos_utis',{filter:'id_oeu,eq,'+oeu.id_oeu}).then(
                result=>{
                    let ids=[];
                    me.dicosUti = result.records; 
                    me.dicosUti.forEach(d => {
                        if(!ids.includes(d.id_dico))ids.push(d.id_dico);
                    });                    
                    if(ids.length==0)return;
                    me.api.read('gen_dicos',ids).then(
                        result=>{
                            me.dicos=result;
                            d3.select(me.tgtList).selectAll('.gDicos').remove();
                            let gDicos = d3.group(me.dicos, d => d.general);
                            d3.select(me.tgtList).selectAll('.gDicos')
                                .data(Array.from(gDicos))
                                .join(
                                    enter => {
                                        let div = enter.append('div')
                                        .attr('id',d=>'dicos'+d[0]?'Gen':'Oeu').attr('class','gDicos')
                                        div.append('h3').html(d=>d[0] ? 'general dictionaries' : 'work dictionaries')
                                        div.append('ul').attr('class','list-group').call(showListedico)
                                    },
                                    //update => update.selectAll('ul').call(showListedico)
                                );
                       
                        }
                    ).catch (
                        error=>console.log(error)
                    );        
                }
            ).catch (
                error=>console.log(error)
            );        
        }
        function showListedico(slct){
            slct.selectAll('li')
                .data(d=>d[1])
                .join(
                    enter => {
                        let li = enter.append('li')
                            .attr('class','list-group-item')
                            .on('click',showDico);
                        li.append('input').attr('class','form-check-input me-1')
                            .attr('type','radio')
                            .attr('name','listeDicos')
                            .attr('id',d=>'dico'+d.id_dico);
                        li.append('label').attr('class','form-check-label')
                            .attr('for',d=>'dico'+d.id_dico)
                            .html(d=>d.nom/*+' ('+d.id_dico+')'*/);
                    },
                    update => {
                        let li = update.selectAll('li')
                        li.selectAll('input').attr('id',d=>'dico'+d.id_dico);
                        li.selectAll('label')                        
                            .attr('for',d=>'dico'+d.id_dico)
                            .html(d=>d.nom/*+' ('+d.id_dico+')'*/);
                    },
                    exit => exit.remove()
                );
            if(me.appUrl.params && me.appUrl.params.has('id_dico'))showDico(null,null,me.appUrl.params.get('id_dico'));
          
        }
        function showDico(e,d,id){
            if(id)d=me.dicos.filter(r=>r.id_dico==id)[0];
            else me.appUrl.changes([{k:'id_oeu',v:me.curOeuvre.id_oeu}]);
            me.appUrl.change('id_dico',d.id_dico);
            me.curDico=new dico({
                    'oeuvre':me,
                    'd':d,
                    'api':me.api,
                    'tgtContent':me.tgtContent,
                    'appUrl':me.appUrl
                });                    
        }

        this.searchClass = function(t,q,o){
            let rs=[], r, f, 
            //création des requêtes pour chaque dictionnaire général du même type
            dicosFiltre = me.dicos.filter(d=>(d.type==t.type && d.general) || (d.id_dico ==  me.curDico.d.id_dico));            
            dicosFiltre.forEach(d=>{
                f = {filter:['id_dico,eq,'+d.id_dico]};
                if(o)f.order=o;
                q.forEach(i=>f.filter.push(i));
                r = me.api.syncList(t.t,f);
                r.records.forEach(d=>rs.push(d));
            }); 
            return rs;           
        }

        this.getConjugaisons = function(){
            if(!me.conjugaisons){
                me.conjugaisons = me.searchClass({'type':'conjugaisons','t':'gen_conjugaisons'},[],'modele,asc');
            }
            return me.conjugaisons;
        }

        this.init();
    }
}
