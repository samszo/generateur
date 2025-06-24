// import { add } from 'immutable-json-patch/lib/esm/immutableJSONPatch.js';
import {dico} from './dico.js';
import {modal} from './modal.js';
import {modalAddOeuvre} from './modal.js';
import jscrudapi from '../node_modules/js-crud-api/index.js';
import {loader} from './loader.js';

export class oeuvres {
    constructor(params) {
        var me = this;
        this.tgtMenu = params.tgtMenu
        this.tgtList = params.tgtList
        this.tgtContent = params.tgtContent
        this.appUrl = params.appUrl ? params.appUrl : false; 
        this.apiUrl = params.apiUrl ? params.apiUrl : 'api.php'; 
        this.auth = params.auth ? params.auth : false;
        this.wGen = params.wGen ? params.wGen : false;
        this.api = this.auth.api;
        this.curOeuvre;
        this.curDico;
        this.oeuvres;
        this.dicos=[];
        this.dicosUti=[];
        this.conjugaisons = false;
        this.loader = new loader();
        var mAdd=new modal(),mMessage=new modal(), mAddOeuvre, mAddOeuvreBody;
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
            if(me.auth.omk){
                //création du dictionnaire d'oeuvre
                let dtDico = {
                    'o:resource_class':'genex:Dictionnaire',
                    'o:resource_template':'genex_dictionnaire',
                    'dcterms:title':"DS_"+nom,
                    'genex:hasType':'concepts',
                };
                me.auth.omk.createItem(dtDico, i=>{
                    console.log('Dico créé',i);
                    //ajoute l'oeuvre dans OmekaS
                    let dt = {
                        'o:resource_class':'genex:Oeuvre',
                        'o:resource_template':'genex_oeuvre',
                        'dcterms:title':nom,
                        'dcterms:license':licence,
                        'dcterms:language':lang,
                        'genex:hasDico':[
                            {'rid':27,'type':'resource'},
                            {'rid':28,'type':'resource'},
                            {'rid':29,'type':'resource'},
                            {'rid':30,'type':'resource'},
                            {'rid':36,'type':'resource'},
                            {'rid':37,'type':'resource'},
                            {'rid':i["o:id"],'type':'resource'}
                        ]
                    };
                    me.auth.omk.createItem(dt,item=>{
                        console.log('Oeuvre créée',item);
                        if(me.appUrl.params && me.appUrl.params.has('id_oeu'))
                            me.appUrl.change('id_oeu',item["o:id"]);
                        else
                            me.appUrl.set('id_oeu',item["o:id"]);
                        getOeuvres();
                        mAddOeuvre.hide();
                    })                                               
                });                
            }else{            
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
        }
        function getOeuvres(){
            //gestion avec omk
            if(me.auth.omk){
                me.auth.omk.getAllItems('resource_class_id='+me.auth.omk.getClassByTerm('genex:Oeuvre')["o:id"],function(data){
                    me.oeuvres = data;
                    me.oeuvres.unshift(
                        {'id_oeu':-1,'o:title':'New work'}, 
                        {'id_oeu':-2,'o:title':'<hr class="dropdown-divider">'}
                    );
                    me.tgtMenu.selectAll('li').data(me.oeuvres).enter().append('li')
                        .append('button')
                        .attr('type', "button")
                        .attr('class',"dropdown-item")
                        .attr('id',d=>d["o:id_oeu"])
                        .on('click',me.showOeuvre)
                        .html(d=>d["o:title"]);
                    if(me.appUrl.params && me.appUrl.params.has('id_oeu'))me.showOeuvre(null,null,me.appUrl.params.get('id_oeu'));
                });
                return
            }
            me.api.list('gen_oeuvres').then(
                result=>{
                    me.oeuvres = result.records;
                    //ajoute le bouton de création
                    me.oeuvres.unshift(
                        {'id_oeu':-1,'lib':'New work'}, 
                        {'id_oeu':-2,'lib':'<hr class="dropdown-divider">'}
                    );
                    me.tgtMenu.selectAll('li').data(me.oeuvres).enter().append('li')
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
            if(id)oeu=me.oeuvres.filter(r=>(me.auth.omk ? r['o:id'] : r.id_oeu)==id)[0];
            else me.appUrl.params=false;
            if(oeu.id_oeu==-1)me.addOeuvre();
            else if(oeu.id_oeu==-2)return;
            else{
                me.curOeuvre=oeu;
                me.getConjugaisons();
                me.appUrl.change('id_oeu',me.auth.omk ? oeu['o:id'] : oeu.id_oeu);
                d3.select(me.tgtContent).selectAll('div').remove();
                let list = d3.select(me.tgtList)
                list.select('h1').remove();
                let tools = me.auth.userAdmin || oeu.uti_id == me.auth.user.id ?
                    '<button id="btnDeleteOeuvre" type="button" class="btn btn-danger btn-sm mx-2"><i class="fa-solid fa-trash-can"></i></button>'
                    : "";

                list.append('h1').html(
                    (me.auth.omk ? oeu['o:title'] : oeu.lib)+tools
                );
                if(tools)list.select('#btnDeleteOeuvre').on('click',verifDeleteOeuvre);
                me.showDicos(oeu);
            }
        }
        function verifDeleteOeuvre(){
            let b = '<h3 class="alert alert-danger">Attention the deletion of the work leads to the deletion of : </h3>';
            //vérifie les usages de l'oeuvre
            if(me.auth.omk){
                d3.json(me.auth.omk.api.replace("api/","s/balpien/page/ajax")
                    +"?json=1&helper=sql&action=getOeuvreUses&idOeu="+me.curOeuvre["o:id"]).then(
                    data=>{
                        if(data[0].nbDico==0){
                            mMessage.setBody('<h3 class="alert alert-success">There are no uses of this work</h3>');
                        }else{
                            mMessage.setBody(b+'<ul id="lstDeleteItems"></ul>');
                            let gMessage = d3.group(data, d => d.class);
                            mMessage.mBody.select('#lstDeleteItems').selectAll('li').data(gMessage).enter()
                                .append('li').attr("class","text-start alert alert-warning").attr("role","alert").html(d=>{
                                    return '<strong>'+d[0]+'</strong> : '+d[1][0].nbItem+' item'+(d[1][0].nbItem > 1 ? 's' : '');
                                });
                            mMessage.mBody.append('div').attr("class","alert alert-danger").attr("role","alert").html("This action is irreversible");

                        }
                        mMessage.setBoutons([{'name':"Close"},
                            {'name':"Delete All",'class':'btn-danger','fct':me.delete}
                            ])                
                        mMessage.show();

                    })
            }else{
                me.api.stats('uses','oeuvre',me.curOeuvre.id_oeu).then(
                    data=>{
                        b+='<h4>'+data[0].nbDico+' dictionaries</h4>';
                        b+='<h4>'+data[0].nbConcept+' concept'+(data[0].nbConcept > 0 ? 's' : '')+'</h4>';
                        mMessage.setBody(b);
                        mMessage.setBoutons([{'name':"Close"},
                            {'name':"Delete All",'class':'btn-danger','fct':me.delete}
                            ])                
                        mMessage.show();    
                    }
                ).catch (
                    error=>{
                        mMessage.setBody('<h3>Impossible to know the uses of the work</h3><p>'+error+'</p>');
                        mMessage.setBoutons([{'name':"Close"}])                
                        mMessage.show();
                    }
                );
            }                    
        }
        this.delete = function(){
            console.log('removeOeuvreVerif');

            if(me.auth.omk){
                d3.json(me.auth.omk.api.replace("api","s/balpien/page/ajax")+"?json=1&helper=sql&action=deleteOeuvre&idOeu="+me.curOeuvre["o:id"]).then(data=>{
                    d3.select('#listDicos').select('h1').remove();                
                    d3.select('#listDicos').selectAll('div').remove();                
                    me.init();
                    mMessage.hide();
                    window.location.reload();
                })
                return;
            }

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
                me.init();
                mMessage.hide();
            });

        }
        function getStat(){
            console.log('removeOeuvreVerif');
        }
        this.addOeuvre = function(){
            if(!me.auth.user){
                mMessage.setBody('<h3>Log in to create a work</h3>');
                mMessage.setBoutons([{'name':"Close"}])                
                mMessage.show();
            }else{
                mAddOeuvre.show();
            }
        }
        this.showDicos = function (oeu){
            d3.select(me.tgtList).selectAll('.gDicos').remove();
            //récupère les dicos de l'oeuvre
            //gestion avec omk
            if(me.auth.omk){
                me.auth.omk.getAllItems('filter[0][join]=and&filter[0][field][]=genex:hasDico&filter[0][type]=lres&filter[0][val]='+oeu["o:id"],function(data){
                    me.dicos = data;
                    d3.select(me.tgtList).selectAll('.gDicos').remove();
                    let gDicos = d3.group(me.dicos, d => d["genex:hasType"][0]["@value"]);
                    d3.select(me.tgtList).selectAll('.gDicos')
                        .data(Array.from(gDicos))
                        .join(
                            enter => {
                                let div = enter.append('div')
                                    .attr('id',d=>{
                                        return 'dicos'+d[0]=="général" ? 'Gen':'Oeu'
                                    }).attr('class','gDicos'),
                                    btn = `<button type="button" id="btnDicoAdd" class="btn btn-sm btn-danger ms-2">
                                            <i class="fa-regular fa-square-plus"></i>
                                        </button>`;
                                div.append('h3').html(d=>d[0]=="général" ? 'general dictionaries' : 'work dictionaries'+btn);
                                div.append('ul').attr('class','list-group').call(showListedico);
                                div.select('#btnDicoAdd').on('click',addNewDico);
                            },
                            //update => update.selectAll('ul').call(showListedico)
                        );
                     me.loader.hide(true);                    
                });
                return
            }
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
                            me.dicos=result.filter(d=>d);
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

        function addNewDico(){
            let m = mAdd.add('modalAddDico');                  
            m.s.select('.modal-footer').selectAll('button').remove();
            m.s.select('.modal-footer').append('button')
                    .attr('type',"button")
                    .attr('class',"btn btn-primary").html('Add new')
                    .on('click',function(){
                        let name = m.s.select('#inptDicoLib').node().value,
                            dt = {
                                'o:resource_class':'genex:Dictionnaire',
                                'o:resource_template':'genex_dictionnaire',
                                'dcterms:title':name,
                                'genex:hasType':'concepts',
                            };
                        me.auth.omk.createItem(dt, i=>{
                            console.log('Dico créé',i);
                            //ajoute le lien entre l'oeuvre, le dico et l'utilisateur
                            me.auth.omk.updateRessource(me.curOeuvre["o:id"],{'genex:hasDico':{'rid':i["o:id"]}},'items', null, 'PUT',rs=>{
                                me.showDicos(me.curOeuvre);
                                showDico(null,null,i["o:id"]);
                                m.m.hide();
                            }, me.curOeuvre);                                               
                    });
                });
            m.m.show();
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
                            .attr('id',d=>'dico'+me.auth.omk ? d['o:id'] : d.id_dico);
                        li.append('label').attr('class','form-check-label')
                            .attr('for',d=>'dico'+me.auth.omk ? d['o:id'] : d.id_dico)
                            .html(d=>me.auth.omk ? d['o:title'] : d.nom/*+' ('+d.id_dico+')'*/);
                    },
                    update => {
                        let li = update.selectAll('li')
                        li.selectAll('input').attr('id',d=>'dico'+me.auth.omk ? d['o:id'] : d.id_dico);
                        li.selectAll('label')                        
                            .attr('for',d=>'dico'+me.auth.omk ? d['o:id'] : d.id_dico)
                            .html(d=>me.auth.omk ? d['o:title'] : d.nom/*+' ('+d.id_dico+')'*/);
                    },
                    exit => exit.remove()
                );
            if(me.appUrl.params && me.appUrl.params.has('id_dico'))showDico(null,null,me.appUrl.params.get('id_dico'));
          
        }
        function showDico(e,d,id){
            if(id)d=me.dicos.filter(r=>(me.auth.omk ? r['o:id'] : r.id_dico)==id)[0];
            else me.appUrl.changes([{k:'id_oeu',v:me.auth.omk ? me.curOeuvre['o:id'] : me.curOeuvre.id_oeu}]);
            me.appUrl.change('id_dico',me.auth.omk ? d['o:id'] : d.id_dico);
            me.curDico=new dico({
                    'oeuvre':me,
                    'd':d,
                    'api':me.api,
                    'omk':me.auth.omk,
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
                if(me.auth.omk){
                    d3.json(me.auth.omk.api.replace("api","s/balpien/page/ajax")+"?json=1&helper=sql&action=getConjModels&idOeu="+me.curOeuvre["o:id"]).then(data=>{
                        me.conjugaisons = data
                    })
                }else{
                    me.conjugaisons = me.searchClass({'type':'conjugaisons','t':'gen_conjugaisons'},[],'modele,asc');
                }
            }
            return me.conjugaisons;
        }

        this.explodeConcept = async function(cpt){
            //explose les concepts
            return await d3.json(me.auth.omk.api.replace("api/","s/balpien/page/ajax")
                    +"?json=1&helper=sql&action=explodeConcept&idConcept="
                    +(cpt["o:id"] ? cpt["o:id"] : cpt));
        }


        this.init();
    }
}
