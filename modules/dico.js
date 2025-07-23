import {concept} from './concept.js';
import {modal} from './modal.js';
import {moteur} from './moteur.js';
import {conjugaisons} from './conjugaisons.js';
import {parse} from '../node_modules/csv-parse/dist/esm/sync.js';
import {loader} from './loader.js';

export class dico {
    constructor(params) {
        var me = this;
        this.oeuvre = params.oeuvre ? params.oeuvre : false;
        this.d = params.d ? params.d : false;
        this.api = params.api ? params.api : false;
        this.omk = params.omk ? params.omk : false;
        this.remove = params.remove ? params.remove : false;
        this.onlyData = params.onlyData ? params.onlyData : false;
        this.tgtContent = params.tgtContent ? params.tgtContent : false;
        this.appUrl = params.appUrl; 
        this.loader = new loader();
        this.hot;
        this.concepts;
        var mainSlt, dicoHot, cptContent, mod = new modal(), userAllowed=false,
        m=new moteur({
            'api':me.api,
            'appUrl':me.appUrl,
            'oeuvre':me.oeuvre
          }),table;

        this.init = function () {
            me.loader.show();
            userAllowed = me.oeuvre.auth.userAdmin || me.oeuvre.auth.userAllowed(me.d.id_dico,me.oeuvre.dicosUti);
            //récupération de la table suivant le type
            for (const p in m.tables) {
                if(m.tables[p].type==(me.omk ? me.d["genex:hasType"][0]["@value"] : me.d.type))table=m.tables[p];
            }
            /*La mise à jour n'est pas nickel => on recré totalement la grid*/
            mainSlt = d3.select(me.tgtContent);
            mainSlt.selectAll('div').remove();
            /*construction du layout 
            */
            let row = mainSlt.append('div').attr('class','d-flex h-100 '+(table.content ? '':'w-100')),
            colL = row.append('div').attr('class','h-100 '+(table.content ? 'w-auto':'w-100'));
            if(userAllowed){
                //ajoute les outils
                let general = me.omk && me.d['genex:isGeneral'] ? true : false,
                    tools = `<div class="container-fluid">
                <a class="navbar-brand" href="#">${me.omk ? me.d['o:title'] : me.d.nom}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDico" aria-controls="navbarDico" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarDico">
                    <ul id="listBtnDico" class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item mx-1">
                            <button type="button" id="btnDicoAddItem" class="btn btn-sm btn-danger" ${general ? 'disabled':''}>
                                <i class="fa-regular fa-square-plus"></i>
                            </button>
                        </li>
                        <li class="nav-item mx-1">
                            <button type="button" id="btnDicoDel" class="btn btn-sm btn-danger" ${general ? 'disabled':''}>
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </li>
                        <li class="nav-item mx-1">
                            <button type="button" id="btnDicoImport" class="btn btn-sm btn-danger" ${general ? 'disabled':''}>
                                <i class="fa-solid fa-download"></i>
                            </button>
                        </li>                    
                        <li class="nav-item mx-1">
                            <button type="button" id="btnDicoExport" class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-file-export"></i>
                            </button>
                        </li>                    
                    </ul>
                </div>
                </div>`,
                toolsNav = colL.append('nav').attr('class','navbar navbar-expand-lg bg-light').html(tools);
                if(me.omk){
                    //ajoute le lien vers OmekaS
                    toolsNav.select("#listBtnDico").append('li').attr('class',"nav-item mx-2").append('a')
                        .attr('href',me.omk.getAdminLink(me.d))
                        .attr('target',"_blank")
                        .append('img').attr('src','asset/images/logos/OmekaS.png')
                            .style("margin-top","-4px")
                            .style("height","20px");
                }
            }
            //création de la modal spécifique à la table
            if(table.mAdd){
                table.mAdd = mod.add('modalAddDico'+table.type);
                table.mAdd.s.select('.modal-footer').selectAll('button').data([table]).join(
                  enter=>enter.append('button')
                    .attr('type',"button")
                    .attr('class',"btn btn-primary").html('Add new')
                    .on('click',addItem)                    
                );
                mainSlt.select("#btnDicoAddItem").on('click',showAddItem);
            }
            mainSlt.select("#btnDicoDel").on('click',verifDeleteDico);
            //création de la modal pour l'import
            if(table.mImp){
                table.mImp = mod.add('modalImportDico'+table.type);
                table.mImp.s.select('.modal-footer').selectAll('button').data([table]).join(
                  enter=>enter.append('button')
                    .attr('type',"button")
                    .attr('class',"btn btn-primary").html('Import')
                    .on('click',me.importDico)                    
                );
                mainSlt.select("#btnDicoImport").on('click',me.showImportDico);            
                mainSlt.select("#btnDicoExport").on('click',me.exportDico);            
            }

            //ajout la colone de résultat
            if(table.content)cptContent = row.append('div').attr('class','flex-fill h-100 vscroll').attr('id','dicoCptContent');            
            //ajoute le tableur
            dicoHot = colL.append('div').attr('class','clearfix')
                .attr('id','dicoHot');
            
            if(me.omk){
                getOmkData();              
                return;
            }

            me.api.list(table.t,{filter:'id_dico,eq,'+me.d.id_dico}).then(
                result=>{
                    me.data = result.records                                
                    showData();
                }
            ).catch (
                error=>console.log(error)
            );
        }

        function getOmkData(){
            let query = 'resource_class_id='+table.class+
                "&property[0][joiner]=and&property[0][property]="+me.omk.getPropId("genex:hasDico")+"&property[0][type]=res&property[0][text]="+me.d['o:id'];
            me.omk.loader.show();
            d3.json(me.omk.api.replace("api/","s/balpien/page/ajax")
                +"?json=1&helper=sql&action=getDicoItems&idDico="
                +me.d["o:id"]).then(data=>{
                userAllowed = true;
                me.data = data;                                
                showData();
            });              
        }

        function showData(){
            //me.hot.loadData(me.data);
            //création de la table
            if(me.data.length==0){ me.loader.hide();return;}
            let headers = Object.keys(me.data[0]),
                rectFooter = d3.select('footer').select('h3').node().getBoundingClientRect(),
                rectHeader = d3.select('header').node().getBoundingClientRect(),
                hCol = {columns: headers.map((h,i)=>{
                        if(me.omk) return h.indexOf('_') > 0 ? i : null
                        else return h.substring(0,3)=='id_' ? i : null
                    }).filter(k=>k!=null)
                };

            me.hot = new Handsontable(dicoHot.node(), {
                rowHeaders: true,
                data: me.data,
                colHeaders: headers,
                height: rectFooter.top-rectFooter.height-rectHeader.bottom,
                width: 375,//table.content ? '300' : '100%',
                licenseKey: 'non-commercial-and-evaluation',
                customBorders: true,
                dropdownMenu: true,
                multiColumnSorting: true,
                filters: true,
                selectionMode:'single',
                hiddenColumns: hCol,
                columns: getCellEditor(headers),
                allowInsertColumn: false,
                copyPaste: false,
                contextMenu: !userAllowed ? false : {
                    callback(key, selection, clickEvent) {
                      // Common callback for all options
                      console.log(key, selection, clickEvent);
                    },
                    items: {
                      remove_row: {
                        name(){
                            return `<button type="button" class="btn btn-sm btn-danger">
                            <i class="fa-regular fa-trash-can"></i>
                            </button>`;    
                        },
                        callback(key, s, e) { // Callback for specific option
                            let r = this.getDataAtRow(s[0].start.row);
                            verifDeleteItem(s,r);
                        }
                      },
                    }
                  },


            });
            me.hot.addHook('afterSelectionEnd', (r, c) => {
                showContent(me.hot.getDataAtRow(r));
            });
            me.hot.addHook('afterChange', (changes,s) => {
                if(me.omk) return; //pas de modification dans OmekaS
                changes.forEach(([r, p, oldValue, newValue]) => {
                    //mise à jour de l'item
                    let data = {};
                    data[p]=newValue;
                    me.api.update(table.t,me.data[r][table.pk],data).then(
                        rs=>{
                            console.log(rs);
                        }   
                    ).catch (
                        error=>console.log(error)
                    );            
                });    
            });

            if(me.appUrl.params && me.appUrl.params.has('id_concept'))showConcept(null,me.appUrl.params.get('id_concept'));
            if(me.appUrl.params && me.appUrl.params.has('id_conj'))showConjugaison(null,me.appUrl.params.get('id_conj'));   
            me.loader.hide();         
        }

        function showContent(d){
            if(table.content){
                switch (table.type) {
                    case 'concepts':
                        showConcept(d);                                
                        break;
                    case 'conjugaisons':
                        showConjugaison(d);                                
                        break;
                    }    
            }
        }

        function getCellEditor(headers){
            let editors = [];
            headers.forEach(h=>{
                switch (h) {
                  default:
                    editors.push({data:h, type: 'text'})                  
                    break;
                }
              })
            return editors;
        }
        this.exportDico = function(e,d){
            const a = document.createElement('a');
            const url = me.omk.api.replace("api/","s/balpien/page/ajax?json=1&helper=sql&action=exportDico&export=csv&idDico="+me.d['o:id']);
            a.href = url;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        this.showImportDico = function(e,d){
            if(table.mImp)table.mImp.m.show();
        }
        this.importDico = function(){
            const csvFile = document.getElementById("importDicoconceptsFile");
            const input = csvFile.files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                //récupère le texte
                const text = e.target.result;
                //transforme le csv en tableau
                const records = parse(text, {
                    columns: true,
                    skip_empty_lines: true,
                    trim: true
                  });
                if(me.omk) {
                    createConcepts(records,table.mImp.s.select('#resultImport'),0);
                }else {
                    records.forEach(r=>{
                        //récupération du concept   
                        //pour minimiser les appels à la base
                        if(!r.type){
                            let cpt = r.concept.split('_');
                            r.type = cpt[0];
                            r.concept = cpt[1];
                        }
                        let rs, dt, idItem, idConcept, cpt = me.data.filter(c=>c.type==r.type && c.lib == r.concept);
                        if(cpt.length==0){
                            idConcept = me.api.syncCreate(table.t, {'id_dico':me.d.id_dico,'type':r.type,'lib':r.concept});
                            me.data.push({'id_concept':idConcept,'id_dico':me.d.id_dico,'type':r.type,'lib':r.concept});
                        }else{
                            idConcept = cpt[0].id_concept;
                        } 
                        //ajoute l'item suivant le type
                        if(r.type && r.concept && r.valeur){
                            //vérifie l'existence
                            rs = me.api.syncList('gen_generateurs',['id_concept,eq,'+idConcept,'valeur,eq,'+r.valeur]);
                            dt = {'id_concept':idConcept,'valeur':r.valeur};
                            //ajoute ou update                           
                            if(rs.records.length==0)
                                idItem = me.api.syncCreate('gen_generateurs', dt);
                            else
                                idItem = me.api.syncUpdate('gen_generateurs', dt, rs.records[0].id_gen);
                        }
                        if(r.uri && r.concept && r.lib && r.format){
                            //vérifie l'existence
                            //{filter:['field1,modifier1,value1','field2,modifier2,value2']}); // AND
                            rs = me.api.syncList('gen_uris',{filter:['id_concept,eq,'+idConcept,'uri,eq,'+encodeURIComponent(r.uri)]});
                            dt = {'id_concept':idConcept,'uri':r.uri,'lib':r.lib,'format':r.format};
                            //ajoute ou update                           
                            if(rs.records.length==0)
                                idItem = me.api.syncCreate('gen_uris', dt);
                            else
                                idItem = me.api.syncUpdate('gen_uris', dt, rs.records[0].id_uri);
                        }
                    })
                }
                /*    
                d.hot.refreshDimensions();
                d.hot.updateSettings({ data: me.data } )
                */
                console.log('import OK');                  
            };
            reader.readAsText(input);
        }

        async function createConcepts(cpts,divResult,i,curCpt){
            if(i==0){
                await me.loader.show();
            }
            if(i>=cpts.length){
                divResult.append('div')
                    .attr("class","alert alert-success").attr("role","alert")
                    .text("FIN Traitement : "+(new Date().toLocaleTimeString("fr-FR")));
                //explode les concepts
                me.oeuvre.explodeConcept(curCpt);
                me.loader.hide(true);
                return;
            }
            if(cpts[i].concept || cpts[i].type){
                //explode les concepts
                if(curCpt)me.oeuvre.explodeConcept(curCpt);
                //création des concepts
                let r = cpts[i],
                    dt = {
                    'o:resource_class':'genex:Concept',
                    'o:resource_template':'genex_Concept',
                    'dcterms:title':r.concept ? r.concept : r.type_concept+'_'+r.concept,
                    'dcterms:description':r.description_concept,
                    'genex:hasType':r.type_concept,
                    'genex:hasDico':{'rid':me.d['o:id']},
                    'dcterms:identifier':me.d['o:id']+'_'+r.type_concept+'_'+r.concept,
                    },
                    dtO = {'rt':'genex_Concept','c':'genex:Concept','dt':{}};
                dtO.dt = dt;
                dtO.verif={'dcterms:identifier':dt['dcterms:identifier']};
                dtO['index'] = dt['dcterms:identifier'];
                //on crée le concept si il n'existe pas
                curCpt = await me.omk.getsetResource(dtO);
                divResult.append('div')
                    .attr("class","alert alert-primary").attr("role","alert")
                    .text("Traitement du concept "+i+" / "+cpts.length+" : "+(new Date().toLocaleTimeString("fr-FR")));
            }

            //création du terme
            await me.createTerm(curCpt, cpts[i], i, divResult);

            createConcepts(cpts,divResult,i+1,curCpt);
        }

        me.createTerm = async function (oCpt, r, i, divResult){

            //création du terme
            let dtO = {'rt':'genex_Term','c':'genex:Term','dt':{}}, dtAc = {},
                elision = r.elision ? '' : 'no',
                dt = {
                    'o:resource_class':'genex:Term',
                    'o:resource_template':'genex_Term',
                    'dcterms:title':r.title ? r.title : r.prefix ? r.prefix : r.generateur ? r.generateur : 'Term '+i,
                    'dcterms:description': r.description_term ? [r.description_term,JSON.stringify(r)] : JSON.stringify(r),
                    'genex:hasType':r.type_term,
                    'genex:hasGenerateur':r.generateur,
                    'genex:hasPrefix':r.prefix,
                    'lexinfo:gender':r.gender,
                    'genex:hasElision':r.hasElision,
                };
            dtAc[elision+'EliFemPlu'] = r.accordFemPlu;
            dtAc[elision+'EliFemSing'] = r.accordFemSing;
            dtAc[elision+'EliMasPlu'] = r.accordMasPlu;
            dtAc[elision+'EliMasSing'] = r.accordMasSing;
            dt['genex:hasAccord']= annoAccord(dtAc,elision);
            if(r.conjugaison){
                //récupère l'identifiant de conjugaison
                let conj = me.oeuvre.conjugaisons.filter(c=>c.title.replace("Modèle de conjugaison : ","")==r.conjugaison);
                if(conj.length>0){
                    dt['genex:hasConjugaison'] = {'rid':conj[0].id_conj};
                }else{
                    divResult.append('div').attr("class","alert alert-danger").attr("role","alert").text("Term "+i+" - ERREUR : cette conjugaison n'existe pas : "+r.conjugaison);
                }
            }
            //l'identifier est l'ensemble des données
            dt['dcterms:identifier']=JSON.stringify(dt);
            //sauf le rapport au concept
            dt['genex:hasConcept']={'rid':oCpt['o:id']};
            dtO.dt = dt;
            dtO.verif={'dcterms:identifier':dt['dcterms:identifier']};
            dtO['index'] = dt['dcterms:identifier'];
            // on crée le terme si il n'existe pas
            // ou on ajoute le lien au concept si toutes les valeurs sont identiques
            let o = await me.omk.getsetResource(dtO,[{'genex:hasConcept':{'rid':oCpt['o:id']}}]);
            divResult.append('div')
                .attr("class","alert alert-info").attr("role","alert")
                .text(dt['genex:hasType']+" "+i+" traité "+o["o:id"]+" - "+o["o:title"]+" : "+(new Date().toLocaleTimeString("fr-FR")));            
        }
        

        function annoAccord(d,elision){
            let anno = {
                'genex:hasElision':[{
                    "@value": elision=="no" ? "0":"1",
                    "type": "literal",
                    "property_id": me.omk.getPropId('genex:hasElision'),
                }],    
                'genex:accordFemSing':[{
                    "@value": d[elision+'EliFemSing'],
                    "type": "literal",
                    "property_id": me.omk.getPropId('genex:accordFemSing'),
                }],
                'genex:accordMasSing':[{
                    "@value": d[elision+'EliMasSing'],
                    "type": "literal",
                    "property_id": me.omk.getPropId('genex:accordMasSing'),
                }],
                'genex:accordFemPlu':[{
                    "@value": d[elision+'EliFemPlu'],
                    "type": "literal",
                    "property_id": me.omk.getPropId('genex:accordFemPlu'),
                }],
                'genex:accordMasPlu':[{
                    "@value": d[elision+'EliMasPlu'],
                    "type": "literal",
                    "property_id": me.omk.getPropId('genex:accordMasPlu'),
                }],
            }, 
            accord = {};
            accord.v = elision=="no" ? 'Accord sans élision':'Accord avec élision';
            accord.a=anno;
            return accord;
        }

        function showAddItem(e,d){
            if(table.mAdd)table.mAdd.m.show();
        }
         
        function addItem(){
            //récupère les valeurs
            let valeurs = {};
            table.mAdd.s.selectAll('.inptValue').nodes().forEach(n=>{
                if(n.hasAttribute("keycol"))
                    valeurs[n.getAttribute('keycol')]=n.value;
            });
            if(me.omk){
                //ajoute l'item dans OmekaS
                let dt = {
                    'o:resource_class':'genex:Concept',
                    'o:resource_template':'genex_Concept',
                    'dcterms:title':valeurs['type']+'_'+valeurs['lib'],
                    'genex:hasType':valeurs['type'],
                    'genex:hasDico':{'rid':me.d['o:id'],'type':'resource'},
                };
                me.omk.createItem(dt,item=>{
                    //ATTENTION l'ordre est important
                    let d = {
                        'id':item['o:id'],
                        'title':item['o:title'],
                        'resource_class_id':item['o:resource_class']['o:id'],
                        'resource_template_id': item['o:resource_template']['o:id'],
                        'local_name': valeurs['lib'],
                        'type':item["genex:hasType"][0]["@value"],
                    };
                    addItemGrid(d,table);
                })
            }else{
               me.api.create(table.t, valeurs).then(
                id=>{
                    //récupère l'item
                    me.api.read(table.t,id).then(
                        item=>{                            
                           addItemGrid(item,table);
                        }
                    );   
                }    
                ).catch (
                    error=>console.log(error)
                );            
            }
        }
        function addItemGrid(item,table){
            if(!me.hot){
                getOmkData();              
                return;
            }
            //ajoute l'item au tableur
            let i=0, row = me.hot.countRows();
            me.hot.alter('insert_row', row, 1);
            for (const p in item) {
                me.hot.setDataAtCell(row, i, item[p]);
                i++;
            }
            me.data.push(item);
            showContent(item);
            table.mAdd.m.hide();
        }
        function verifDeleteDico(){
            //vérifie le nombre de dico d'oeuvre
            let dicoOeuvre = me.oeuvre.dicos.filter(d=>d.general==0);
            if(dicoOeuvre.length >= 1){
                mod.setBody('<h3 class="bg-danger">You cannot delete this dictionary : it is the only one for this work</h3>');
                mod.setBoutons([{'name':"Close"}]);                
                mod.show();        
            }else{
                mod.setBody('<h3 class="bg-danger">Are you sure you want to delete this dictionary ?</h3>');
                mod.setBoutons([{'name':"Close"},
                    {'name':"Delete",'class':'btn-danger','fct':f=>me.delete(me.d)}
                    ])                
                mod.show();        
            }
        }
        
        function verifDeleteItem(s,d){
            mod.setTitle('Delete item');
            mod.setBody('<h3 class="bg-danger">Are you sure you want to delete this item?</h3>');
            mod.setBoutons([{'name':"Close"},
                {'name':"Delete",'class':'btn-danger','fct':f=>deleteItem(s,d)}
                ])                
            mod.show();    
        }
        function deleteItem(s,d){
            if(me.omk){
                //supprime l'item dans OmekaS
                d3.json(me.omk.api.replace("api/","s/balpien/page/ajax")
                    +"?json=1&helper=sql&action=deleteConcept&id="
                    +d[0]).then(e=>{
                    if(e.status=='ok'){
                        clearConcept(s);
                    }else{
                        console.log('error delete item',e);
                        mod.setTitle(e.error);
                        mod.setBody('<h3 class="bg-danger">'+e.message+'</h3>'+'<a href="'+e.link+'" target="_blank">Connexion</a>');
                        mod.setBoutons([{'name':"Close"},
                            {'name':"Delete",'class':'btn-danger','fct':f=>deleteItem(s,d)}
                        ])                
                    }
                }).catch(e=>{
                    console.log('error delete item',e);
                });
            }else {
                me.api.delete(table.t,d[0]).then(e=>{
                    clearConcept(s);
                });
            }
        }

        function clearConcept(s){
            me.hot.alter('remove_row', s[0].start.row, 1);
            mod.hide();
            cptContent.selectAll('nav').remove();
            cptContent.selectAll('ul').remove();
            cptContent.selectAll('div').remove();
        }

        function showConcept(d,id){
            if(d === undefined && id===null) return;
            if(!id)id=d[0];//le grid ne renvoie pas des tableaux associatifs
            if(!d || !d['id'])d=me.data.filter(r=>(me.omk ? r.id : r.id_concept)==id)[0];
            me.appUrl.change('id_concept',me.omk ? d.id : d.id_concept);

            let cpt=new concept({
                    'data':d,
                    'dico':d,
                    'oDico':me,
                    'omk':me.omk,
                    'oeuvre':me.oeuvre,
                    'api':me.api,
                    'tgtContent':cptContent,
                    'appUrl':me.appUrl
                });                
        }
        function showConjugaison(d,id){
            if(d === undefined && id===null) return;
            if(!id)id=d[0];//le grid ne renvoie pas des tableaux associatifs
            d=me.data.filter(r=>r.id_conj==id)[0];
            me.appUrl.change('id_conj',d.id_conj);
            let conj = new conjugaisons({'api':me.api,'cont':cptContent
                ,'v':d, oeuvre:me.oeuvre, 'appUrl':me.appUrl});
        }

        function deleteItems (){
            me.api.list('gen_concepts',{include:'id_concept',filter:'id_dico,eq,'+me.d.id_dico}).then(
                result=>{                    
                    if(result.records.length)me.api.delete('gen_concepts',result.records.map(r=>r.id_concept));
                }
            ).catch (
                error=>console.log(error)
            );

        }

        this.delete = function (d){
            if(me.omk){
                //supprime l'item dans OmekaS
                d3.json(me.omk.api.replace("api/","s/balpien/page/ajax")
                    +"?json=1&helper=sql&action=deleteDico&idDico="
                    +d["o:id"]).then(e=>{
                    if(e.status=='ok'){
                        me.oeuvre.showDicos(me.oeuvre.curOeuvre); 
                        mod.hide(); 
                    }else{
                        console.log('error delete dico',e);
                    }
                }).catch(e=>{
                    console.log('error delete dico',e);
                });
            }else {
                //plus nécessaire car base de donnée avec DELETE CASCADE
                //deleteItems() 
                me.api.delete('gen_dicos',me.d.id_dico).then(e=>{
                    console.log('dico delete',d);
                });
            }
        }

        this.getData = function(){
            me.api.list('gen_concepts',{filter:'id_dico,eq,'+me.d.id_dico}).then(
                result=>{
                    me.data = result.records
                    me.hot.loadData(me.data);
                    if(me.appUrl.params && me.appUrl.params.has('id_concept'))showConcept(null,me.appUrl.params.get('id_concept'));
                }
            ).catch (
                error=>console.log(error)
            );
        }

        //gestion des initialisation de l'objet
        if(this.remove) this.delete();
        if(this.onlyData) this.getData();
        else this.init();
    
    }
}