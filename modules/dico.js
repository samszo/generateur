import {concept} from '../modules/concept.js';
import {modal} from '../modules/modal.js';
import {moteur} from '../modules/moteur.js';
import {conjugaisons} from '../modules/conjugaisons.js';
import {parse} from '../node_modules/csv-parse/dist/esm/sync.js';

export class dico {
    constructor(params) {
        var me = this;
        this.oeuvre = params.oeuvre ? params.oeuvre : false;
        this.d = params.d ? params.d : false;
        this.api = params.api ? params.api : false;
        this.remove = params.remove ? params.remove : false;
        this.onlyData = params.onlyData ? params.onlyData : false;
        this.tgtContent = params.tgtContent ? params.tgtContent : false;
        this.appUrl = params.appUrl; 
        this.hot;
        this.concepts;
        var mainSlt, dicoHot, cptContent, mod = new modal(), userAllowed=false,
        m=new moteur({
            'api':me.api,
            'appUrl':me.appUrl,
            'oeuvre':me.oeuvre
          }),table;

        this.init = function () {
            userAllowed = me.oeuvre.auth.userAdmin || me.oeuvre.auth.userAllowed(me.d.id_dico,me.oeuvre.dicosUti);
            //récupération de la table suivant le type
            for (const p in m.tables) {
                if(m.tables[p].type==me.d.type)table=m.tables[p];
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
                let tools = `<div class="container-fluid">
                <a class="navbar-brand" href="#">${me.d.nom}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDico" aria-controls="navbarDico" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarDico">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item mx-2">
                        <button type="button" id="btnDicoAddItem" class="btn btn-sm btn-danger">
                            <i class="fa-regular fa-square-plus"></i>
                        </button>
                    </li>
                    <li class="nav-item mx-2">
                        <button type="button" id="btnDicoDel" class="btn btn-sm btn-danger">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </li>
                    <li class="nav-item mx-2">
                        <button type="button" id="btnDicoImport" class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-upload"></i>
                        </button>
                    </li>                    
                    </ul>
                </div>
                </div>`,
                toolsNav = colL.append('nav').attr('class','navbar navbar-expand-lg bg-light').html(tools);
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
                    .on('click',importDico)                    
                );
                mainSlt.select("#btnDicoImport").on('click',showImportDico);            
            }

            //ajout la colone de résultat
            if(table.content)cptContent = row.append('div').attr('class','flex-fill h-100 vscroll').attr('id','dicoCptContent');            
            //ajoute le tableur
            dicoHot = colL.append('div').attr('class','clearfix')
                .attr('id','dicoHot');
            me.api.list(table.t,{filter:'id_dico,eq,'+me.d.id_dico}).then(
                result=>{
                    me.data = result.records
                    //me.hot.loadData(me.data);
                    //création de la table
                    let headers = Object.keys(me.data[0]),
                        rectFooter = d3.select('footer').select('h3').node().getBoundingClientRect(),
                        rectHeader = d3.select('header').node().getBoundingClientRect();

                    me.hot = new Handsontable(dicoHot.node(), {
                        colHeaders: true,
                        rowHeaders: true,
                        data:me.data,
                        colHeaders: headers,
                        height: rectFooter.top-rectFooter.height-rectHeader.bottom,
                        width: table.content ? '300' : '100%',
                        licenseKey: 'non-commercial-and-evaluation',
                        customBorders: true,
                        dropdownMenu: true,
                        multiColumnSorting: true,
                        filters: true,
                        selectionMode:'single',
                        hiddenColumns: {
                            // specify columns hidden by default
                            columns: headers.map((h,i)=>h.substring(0,3)=='id_' ? i : null).filter(k=>k!=null)
                        },
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
                        changes?.forEach(([r, p, oldValue, newValue]) => {
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
                }
            ).catch (
                error=>console.log(error)
            );
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
        function showImportDico(e,d){
            if(table.mImp)table.mImp.m.show();
        }
        function importDico(){
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
                //ajoute les données dans le dico
                me.api.create(table.t, {'nom':nom,'type':'concepts','langue':lang,'general':0,'licence':licence}).then(
                    idDico=>{
                        //ajoute le lien entre l'oeuvre, le dico et l'utilisateur
                        me.api.create('gen_oeuvres_dicos_utis', {'id_oeu':idOeu,'id_dico':idDico,'uti_id':me.auth.user.id});
                    }
                );   

                console.log('import OK');                  
            };
            reader.readAsText(input);
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
            valeurs.id_dico=me.d.id_dico;
            me.api.create(table.t, valeurs).then(
                id=>{
                    //récupère l'item
                    me.api.read(table.t,id).then(
                        item=>{                            
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
                    );   
                }    
            ).catch (
                error=>console.log(error)
            );            
        }
        function verifDeleteDico(){
            //vérifie le nombre de dico d'oeuvre
            let dicoOeuvre = me.oeuvre.dicos.filter(d=>d.general==0);
            if(dicoOeuvre.length >= 1){
                mod.setBody('<h3>You cannot delete this dictionary : it is the only one for this work</h3>');
                mod.setBoutons([{'name':"Close"}]);                
                mod.show();        
            }else{
                mod.setBody('<h3>Are you sure you want to delete this dictionary ?</h3>');
                mod.setBoutons([{'name':"Close"},
                    {'name':"Delete",'class':'btn-danger','fct':f=>me.delete(me.d)}
                    ])                
                mod.show();        
            }
        }
        
        function verifDeleteItem(s,d){
            mod.setBody('<h3>Are you sure you want to delete this item?</h3>');
            mod.setBoutons([{'name':"Close"},
                {'name':"Delete",'class':'btn-danger','fct':f=>deleteItem(s,d)}
                ])                
                mod.show();    
        }
        function deleteItem(s,d){
            me.api.delete(table.t,d[0]).then(e=>{
                me.hot.alter('remove_row', s[0].start.row, 1);
                mod.hide();    
            });
        }

        function showConcept(d,id){
            if(d === undefined && id===null) return;
            if(!id)id=d[0];//le grid ne renvoie pas des tableaux associatifs
            d=me.data.filter(r=>r.id_concept==id)[0];
            me.appUrl.change('id_concept',d.id_concept);

            let cpt=new concept({
                    'data':d,
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
            //plus nécessaire car base de donnée avec DELETE CASCADE
            //deleteItems() 
            me.api.delete('gen_dicos',me.d.id_dico).then(e=>{
                console.log('dico delete',d);
            });
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