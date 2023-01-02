import {concept} from '../modules/concept.js';
import {modal} from '../modules/modal.js';
import {modalAddDicoItem} from '../modules/modal.js';
import {moteur} from '../modules/moteur.js';

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
        var mainSlt, dicoHot, cptContent, mAddItem, mAddItemBody,mMessage=new modal(),
        m=new moteur({
            'api':me.api,
            'appUrl':me.appUrl,
            'oeuvre':me.oeuvre
          }),table;

        this.init = function () {
            //récupération de la table suivant le type
            for (const p in m.tables) {
                if(m.tables[p].type==me.d.type)table=m.tables[p];
            }
            /*La mise à jour n'est pas nickel => on recré totalement la grid*/
            mainSlt = d3.select(me.tgtContent);
            mainSlt.selectAll('div').remove();
            /*construction du layout 
            let row = mainSlt.append('div').attr('class','container-fluid h-100')
                .append('div').attr('class','row justify-content-md-center h-100');
            dicoHot = row.append('div')
                .attr('class','col float-start')
                //.attr('class',"w-auto h-100 float-start")
                .append('div')
                .attr('id','dicoHot');
            //cptContent = mainSlt.append('div').attr('class','w-auto h-100 float-end').attr('id','dicoCptContent');
            cptContent = row.append('div').attr('class','col').attr('id','dicoCptContent');
            */
            let row = mainSlt.append('div').attr('class','d-flex h-100'),
            colL = row.append('div').attr('class','w-auto h-100'),
            //ajoute les outils
            tools = `<div class="container-fluid">
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
                </ul>
              </div>
            </div>`,
            toolsNav = colL.append('nav').attr('class','navbar navbar-expand-lg bg-light').html(tools);
            mAddItemBody=d3.select('#modalDicoAddItem');
            //ajoute les modals si inexistant
            if(mAddItemBody.empty()){
                mAddItemBody = d3.select('body').append('div')
                    .attr('id','modalDicoAddItem').attr('class','modal').attr('tabindex',-1);
                mAddItemBody.html(modalAddDicoItem);
            }
            mAddItem = new bootstrap.Modal('#modalDicoAddItem');
            mainSlt.select("#btnDicoAddItem").on('click',e=>mAddItem.show());
            mAddItemBody.select("#btnAddNewItem").on('click',addItems);
            mainSlt.select("#btnDicoDel").on('click',verifDeleteDico);
            //ajout la colone de résultat
            cptContent = row.append('div').attr('class','flex-fill h-100 vscroll').attr('id','dicoCptContent');            
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
                        width: '300',
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
                        contextMenu: {
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
                    me.hot.addHook('afterSelection', (r, c) => {
                        showConcept(me.hot.getDataAtRow(r));
                    });
                    me.hot.addHook('afterChange', (changes,s) => {
                        changes?.forEach(([r, p, oldValue, newValue]) => {
                            //mise à jour de l'item
                            let data = {};
                            data[p]=newValue;
                            me.api.update(table.t,me.data[r][0],data).then(
                                rs=>{
                                    console.log(rs);
                                }   
                            ).catch (
                                error=>console.log(error)
                            );            
                        });    
                    });
        
        


                    if(me.appUrl.params && me.appUrl.params.has('id_concept'))showConcept(null,me.appUrl.params.get('id_concept'));
                }
            ).catch (
                error=>console.log(error)
            );
            //me.getItems(me.d);
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
  
        function addItems(){
            let lib = mAddItemBody.select("#inpItemLib").node().value,
                type = mAddItemBody.select("#inpItemType").node().value;
            me.api.create('gen_concepts', {'lib':lib,'type':type,'id_dico':me.d.id_dico}).then(
                id=>{
                    //récupère l'item
                    me.api.read('gen_concepts',id).then(
                        item=>{                            
                            //ajoute l'item au tableur
                            let i=0, row = me.hot.countRows();
                            me.hot.alter('insert_row', row, 1);
                            for (const p in item) {
                                me.hot.setDataAtCell(row, i, item[p]);
                                i++;
                            }
                            me.data.push(item);
                            showConcept(null,id);
                            mAddItem.hide();
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
                mMessage.setBody('<h3>You cannot delete this dictionary : it is the only one for this work</h3>');
                mMessage.setBoutons([{'name':"Close"}]);                
                mMessage.show();        
            }else{
                mMessage.setBody('<h3>Are you sure you want to delete this dictionary ?</h3>');
                mMessage.setBoutons([{'name':"Close"},
                    {'name':"Delete",'class':'btn-danger','fct':f=>me.delete(me.d)}
                    ])                
                mMessage.show();        
            }
        }
        
        function verifDeleteItem(s,d){
            mMessage.setBody('<h3>Are you sure you want to delete this item?</h3>');
            mMessage.setBoutons([{'name':"Close"},
                {'name':"Delete",'class':'btn-danger','fct':f=>deleteItem(s,d)}
                ])                
            mMessage.show();    
        }
        function deleteItem(s,d){
            me.api.delete('gen_concepts',d[0]).then(e=>{
                me.hot.alter('remove_row', s[0].start.row, 1);
                mMessage.hide();    
            });
        }

        this.getItems = function (d){
            me.d=d;
            //me.hot.updateData([]);
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
                me.oeuvre.showOeuvre(null,me.oeuvre.curOeuvre);
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