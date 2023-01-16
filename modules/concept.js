import {JSONEditor} from '../node_modules/vanilla-jsoneditor/index.js'
import {getIn, parseFrom} from '../node_modules/immutable-json-patch/lib/esm/index.js'
import {moteur} from '../modules/moteur.js';
import {modal} from '../modules/modal.js';
import {conjugaisons} from '../modules/conjugaisons.js';

export class concept {
    constructor(params) {
        var me = this;
        this.oeuvre = params.oeuvre ? params.oeuvre : false;
        this.dico = params.dico ? params.dico : false;
        this.api = params.api ? params.api : false;
        this.data = params.data ? params.data : false;
        this.tgtContent = params.tgtContent ? params.tgtContent : false;
        this.appUrl = params.appUrl ? params.appUrl : false;
        this.sync = params.sync ? params.sync : false;
        this.remove = params.remove ? params.remove : false;
        this.linkData;
        this.conjData;
        this.jsEditor;
        this.jsPath;
        var m=new modal(), contResult, contHeight, userAllowed, progress;
        this.init = function () {
            me.linkData=[
                {n:'Adjectives',t:'gen_adjectifs',k:'id_adj',data:[],mAdd:true},
                {n:'Generators',t:'gen_generateurs',k:'id_gen',data:[],mAdd:true},
                {n:'Nouns',t:'gen_substantifs',k:'id_sub',data:[],mAdd:true},
                //uniquement dans le dictionnaire général {n:'Syntagms',t:'gen_syntagmes',k:'id_syn',data:[],mAdd:true},
                {n:'Verbs',t:'gen_verbes',k:'id_verbe',data:[],mAdd:true},
            ];
            if(me.sync)getSyncLinkData();
            else{
              userAllowed = me.oeuvre.auth.userAdmin || me.oeuvre.auth.userAllowed(me.dico.id_dico,me.oeuvre.dicosUti);
              getLinkData();
              //construction des modals pour chaque type de lien              
              me.linkData.forEach(ld=>{
                if(ld.mAdd){
                  ld.mAdd = m.add('modalAddConcept'+ld.n);                  
                  ld.mAdd.s.select('.modal-footer').selectAll('button').remove();
                  ld.mAdd.s.select('.modal-footer').selectAll('button').data([ld]).enter().append('button')
                      .attr('type',"button")
                      .attr('class',"btn btn-primary").html('Add new')
                      .on('click',addItem);
                  if(ld.n=="Verbs"){
                    //ajoute les options de conjugaison
                    me.conjData = me.oeuvre.getConjugaisons();
                    ld.mAdd.s.select('#verbConj').selectAll('option').data(
                      [{'id_conj':-1,'modele':'choose a conjugation model'}].concat(me.conjData)
                      ).join(
                      enter=>enter.append('option')
                        .attr('value',c=>c.id_conj)
                        .html(c=>c.modele)                    
                    );
                  }
                }
              })
            } 
        }
        function getSyncLinkData(){
          //ATTENTION il peut y avoir le même concept dans plusieurs dictionnaires
          me.data.forEach(cpt=>{
            me.linkData.forEach(d=>{
              d.data = me.api.syncList(d.t,{filter:'id_concept,eq,'+cpt.id_concept}).records;
            });  
          })
          return me.linkData;
        }

        function getLinkData(){
            let p = [];
            me.linkData.forEach(d=>p.push(me.api.list(d.t,{filter:'id_concept,eq,'+me.data.id_concept})));
            Promise.all(p).then((values) => {
                let pl=[];
                values.forEach((v,i)=>{
                  if(me.linkData[i].n=='Verbs'){
                    //ajout du nom du modèle
                    v.records.forEach(r=>r.modele=me.conjData.filter(c=>c.id_conj==r.id_conj)[0].modele);
                  }
                  me.linkData[i].data=v.records;
                });
                showLinkData();
            });
        }
        function showLinkData(){
            //construction des tab
            me.tgtContent.selectAll('nav').remove();
            me.tgtContent.selectAll('ul').remove();
            me.tgtContent.selectAll('div').remove();
            //ajoute les outils
            let tools = `<div class="container-fluid">
              <a class="navbar-brand" href="#">[${me.data.type+'_'+me.data.lib}]</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarConcept" aria-controls="navbarConcept" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarConcept">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item mx-2">
                    <button type="button" id="btnGenere" class="btn btn-sm btn-danger">
                        <i class="fa-solid fa-shuffle"></i>
                    </button>
                  </li>`
            if(userAllowed){
              tools += `<li class="nav-item mx-2">
                  <button type="button" class="btn btn-sm btn-danger">
                      <i class="fa-regular fa-trash-can"></i>
                  </button>
                </li>
                <li class="nav-item dropdown mx-2">
                  <button type="button" class="btn btn-sm btn-danger dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-regular fa-square-plus"></i>
                  </button>
                  <ul class="dropdown-menu" id="ddmAddCptItem" >
                  </ul>
                </li>`;
            }
            tools += `           
                </ul>
              </div>
            </div>`;
            let toolsNav = me.tgtContent.append('nav').attr('class','navbar navbar-expand-lg bg-light').html(tools);
            toolsNav.select('#ddmAddCptItem').selectAll('li').data(me.linkData).enter().append('li')
              .append('a').attr('class',"dropdown-item").html(ld=>ld.n).on('click',showAddItem);         

            //construction de la barre de nav
            let cont, navtabs = me.tgtContent.append('ul')
                .attr('class',"nav nav-pills mb-3")
                .attr('role',"tablist"),
            rectTN = toolsNav.node().getBoundingClientRect(),
            rect = me.tgtContent.node().getBoundingClientRect();
            contHeight=((rect.height/2)-rectTN.height);
            cont = me.tgtContent.append('div')
                .attr('class',"tab-content").style('height',(contHeight/1.5)+'px');
            navtabs.selectAll('li').data(me.linkData).enter().append('li')
                .attr('class',"nav-item")
                .attr('role',"presentation")
                .append('button').attr('class',(d,i)=>{
                      let c = i==0 && d.data.length ? "nav-link active text-bg-danger" : 
                        d.data.length ? 'nav-link text-bg-secondary' : 'nav-link text-bg-secondarydark';
                      return c; 
                    })
                    .attr('data-bs-toggle',"pill")
                    .attr('data-bs-target',d=>"#tab-pane-"+d.t)
                    .attr('type',d=>"button")
                    .attr('role',d=>"tab")
                    .attr('aria-controls',d=>"tab-pane-"+d.t)
                    .attr('aria-selected',(d,i)=>i==0 && d.data.length ? "true":"false")
                    .attr('id',d=>"tab-"+d.t)
                    .html(d=>d.n)
                    .on('click',changeTab)
                    .each((d,i)=>d.tab = new bootstrap.Tab('#tab-'+d.t));
            //construction des contenus
            cont.selectAll('div').data(me.linkData).enter().append('div')
                .attr('class',(d,i)=>i==0 ? "tab-pane fade show active" : "tab-pane fade")
                .attr('id',d=>"tab-pane-"+d.t)
                .attr('role',"tabpanel")
                .attr('aria-labelledby',d=>"tab-"+d.t)
                .attr('tabindex',0)
                //.each(showLinkDataContent)
                ;
            //construction du div de résultat
            contResult = me.tgtContent.append('div').style('height',contHeight+'px');            
            //ajout des évenements
            d3.select('#btnGenere').on('click',e=>genere(e,me.data,'concept'));
            //vérification du passage de paramètre
            if(me.appUrl.params && me.appUrl.params.has('linkDataTab')){
              let dt = me.linkData.filter(ld=>ld.n==me.appUrl.params.get('linkDataTab'))[0];
              changeTab(null,dt);
            }else{
              //affiche la première tab avec du contenu            
              changeTab(null,me.linkData.filter(ld=>ld.data.length)[0]);                
            }
        }
        function showAddItem(e,d){
          if(d.mAdd)d.mAdd.m.show();
        }
        function addItem(e,d){          
          //récupère les valeurs
          let valeurs = {};
          d.mAdd.s.selectAll('.inptValue').nodes().forEach(n=>{
            if(n.hasAttribute("keycol"))
              if(n.getAttribute('type')=="checkbox"){
                  valeurs[n.getAttribute('keycol')]=n.checked ? 1 : 0;
              }else if(n.getAttribute('keycol')=='genre'){
                if(valeurs[n.getAttribute('keycol')]===undefined)
                  valeurs[n.getAttribute('keycol')]=n.checked ? n.getAttribute('value') : undefined;
              }else 
                valeurs[n.getAttribute('keycol')]=n.value;
          });
          valeurs.id_concept=me.data.id_concept;
          //création de l'item
          me.api.create(d.t,valeurs).then(
            id=>{
                //récupère l'item
                me.api.read(d.t,id).then(
                    item=>{
                      d.data.push(item);                            
                      d.mAdd.m.hide();
                      changeTab(null,d);
                    }
                );   
            }    
          ).catch (
              error=>console.log(error)
          );            
        }
        function changeTab(e,d){
          contResult.selectAll('div').remove();
          me.tgtContent.selectAll('.tab-pane').attr('class','tab-pane fade');
          me.tgtContent.select('#tab-pane-'+d.t).attr('class','tab-pane fade active show');
          d.tab.show();
          me.tgtContent.selectAll('.nav-link')
            .attr('class',(n,i)=>{
                let c = n.t==d.t ? "nav-link text-bg-danger" : 
                  n.data.length ? 'nav-link text-bg-secondary' : 'nav-link text-bg-secondarydark';
                return c; 
              });
          if(d.hot){
            d.hot.refreshDimensions();
            d.hot.updateSettings({ data: d.data } )
          }else
            showLinkDataContent(d);            
          //ajoute le paramètre à l'url
          me.appUrl.change('linkDataTab',d.n);
        }
        function showLinkDataContent(d, i){
            if(d.data.length==0)return;
            let pane = d3.select("#tab-pane-"+d.t), cont = pane.append('div')
                .attr('class',"container-fluid");
            //création de la table
            let headers = Object.keys(d.data[0]),
                rect = me.tgtContent.select('.tab-content').node().getBoundingClientRect(),
                div = cont.append('div').attr('class',"row").append('div').attr('class',"col-12")
                    .append('div').attr('class',"clearfix");   
                d.hot = new Handsontable(div.node(), {
                    data: d.data,
                    rowHeaders: true,
                    colHeaders: headers,
                    height: (rect.height),
                    //width: rect.width,
                    rowHeights: 40,
                    selectionMode:'single',
                    manualRowResize: true,
                    colWidths: headers.length == 3 ? rect.width-100 : undefined,
                    renderAllRows: true,
                    className:'htJustify',
                    licenseKey: 'non-commercial-and-evaluation',
                    renderAllRows:true,
                    customBorders: true,
                    dropdownMenu: true,
                    multiColumnSorting: true,
                    filters: true,
                    hiddenColumns: {
                        // specify columns hidden by default
                        columns: headers.map((h,i)=>h.substring(0,3)=='id_' ? i : null).filter(k=>k!=null)
                    },
                    editor: 'text',
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
                                verifDeleteItem(this,s);
                            }
                          },
                          generer: { // Own custom option
                            name() { // `name` can be a string or a function
                              return `<button type="button" id="btnGenereItem" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-shuffle"></i>
                                    </button>`;
                            },
                            callback(key, s, e) { // Callback for specific option
                                let r = this.getDataAtRow(s[0].start.row);
                                genere(e,r,d);
                            }
                          }
                        }
                      },
                    search: true,    
                });
              d.hot.addHook('afterSelection', (r, c) => {
                let dr = d.hot.getDataAtRow(r),
                  cols = d.hot.getColHeader();
                cols.forEach((col,i)=>{
                  if(col.substring(0,2)=='id')me.appUrl.change(col,dr[i]);
                })
              });
              d.hot.addHook('afterChange', (changes,s) => {
                changes?.forEach(([r, p, oldValue, newValue]) => {
                  //mise à jour de l'item
                  let data = {};
                  data[p]=newValue;
                  me.api.update(d.t,d.data[r][d.k],data).then(
                      rs=>{
                        console.log(rs);
                      }   
                  ).catch (
                      error=>console.log(error)
                  );            
                });    
              });

              if(me.appUrl.params && me.appUrl.params.has('id_gen')){
                const search = d.hot.getPlugin('search');
                const queryResult = search.query(me.appUrl.params.get('id_gen'));
                queryResult.forEach(r=>{
                  if(r.col==0)d.hot.selectCell(r.row, r.col);
                })
              }
        }
        function getCellEditor(headers){
          let editors = [];
          headers.forEach(h=>{
              switch (h) {
                case 'elision':
                  editors.push({data:h, type: 'checkbox',uncheckedTemplate: '0',checkedTemplate: '1'})                  
                  break;              
                default:
                  editors.push({data:h, type: 'text'})                  
                  break;
              }
            })
          return editors;
        }
        function verifDeleteItem(h, s){
          if(userAllowed){
            m.setBody('<h3>Are you sure you want to delete this item?</h3>');
            m.setBoutons([{'name':"Close"},
                {'name':"Delete",'class':'btn-danger','fct':f=>deleteItem(h, s)}
                ]);                
          }else{
            m.setBody('<h3>You are not authorized to delete this item</h3>');
            m.setBoutons([{'name':"Close"}]);                
          }
          m.show();    
        }
        function deleteItem(h,s){
            let r = h.getDataAtRow(s[0].start.row),
              k = h.getColHeader()[0],
              ld = me.linkData.filter(l=>l.k==k);
            me.api.delete(ld[0].t,r[0]).then(e=>{
                h.alter('remove_row', s[0].start.row, 1);
                m.hide();    
            });
        }


        function changeJsonEditor(u,p,r){
          let allowChangeKey=['lib'], item, path = parseFrom(r.patchResult.redo[0].path),
            key = path[path.length-1];
          if(allowChangeKey.includes(key)){
            item = getIn(u.json,path.slice(0, -1));
          }          
        }
        function addChampResult(d){
            //ajoute les champs de résultats
            contResult.selectAll('div').remove();
            let htmlResult = `<div class="row h-100">
            <div class="col">
              <h4>Generated texts</h4>
              <div class="progress" id="progressGenConcept">
              </div>
              <div id="genText${d.n}"></div>
            </div>`;

            if(d=='concept' || d.t=="gen_generateurs"){
              htmlResult += `<div class="col">
                      <h4>Generation structure</h4>
                      <div id="genStrct"></div>
                    </div>
                  </div>`;
              contResult.html(htmlResult);
              me.jsEditor = new JSONEditor({
                target: document.getElementById("genStrct"),
                props: {
                  mode: 'tree',
                  onChange:changeJsonEditor 
                  /*(updatedContent, previousContent, { contentErrors, patchResult }) => {
                    // content is an object { json: JSONValue } | { text: string }
                    console.log('onChange', { updatedContent, previousContent, contentErrors, patchResult })
                    content = updatedContent
                  }*/
                }
              })
              //écouteur pour les modifications
              contResult.selectAll(".jse-value").on('onchange',changeJsonEditor);
            }else{
              htmlResult += `</div>`;
              contResult.html(htmlResult);
            }

            //ajoute le progresse bar
            progress = new ProgressBar.Circle(contResult.select('#progressGenConcept').node(), {
              color: '#aaa',
              // This has to be the same size as the maximum width to
              // prevent clipping
              strokeWidth: 4,
              easing: 'easeInOut',
              text: {
                autoStyleContainer: false,
                value:'0',
                style: {
                  // Text color.
                  position: 'absolute',
                  left: '50%',
                  top: '50%',
                  fontSize: '2rem',
                  padding: 0,
                  margin: 0,
                  // You can specify styles which will be browser prefixed
                  transform: {
                      prefix: true,
                      value: 'translate(-50%, -50%)'
                  }                
                },
              }
            });
            //progress.text.style.fontFamily = '"Raleway", Helvetica, sans-serif';
            //progress.text.style.fontSize = '2rem';
            progressLoop(1,0);
        }
        function progressLoop(num,val){
          let dur = 3;
          progress.set(0);
          progress.animate(1, {
            duration: dur*1000,
            from: { color: 'rgb(0,255,0)', width: 4+num },
            to: { color: 'rgb(255,0,0)', width: 4+num },
            step: function(state, circle) {
              circle.path.setAttribute('stroke', state.color);
              circle.path.setAttribute('stroke-width', state.width);
              circle.setText(toHoursMinutesSeconds((circle.value()*1000*dur)+val));
            }
        }, function() {
            //console.log('progressLoop'+num);
            if(progress.svg){
              progressLoop(num+1,dur*1000+val);  
            }
          });
        }

        function toHoursMinutesSeconds(milliSeconds) {
          const totalSeconds = Math.floor(milliSeconds / 1000);
          const totalMinutes = Math.floor(totalSeconds / 60);

          const seconds = totalSeconds % 60;
          const millis = Math.floor(milliSeconds - seconds*1000);
          const hours = Math.floor(totalMinutes / 60);
          const minutes = totalMinutes % 60;
 
          return hours+":"+minutes+":"+seconds+":"+millis;
        }

        function showGen(d,g,view){
          me.oeuvre.wGen.postMessage({
            'g':g,
            'dicos':me.oeuvre.dicos,
            'id_dico':me.oeuvre.curDico.d.id_dico,
            'apiUrl':me.oeuvre.auth.apiReadUrl
          });
          me.oeuvre.wGen.onmessage = function(event) {
            me.tgtContent.select("#genText"+d.n).html(event.data.texte);    
            switch (view) {
              case 'jsEditor':
                me.jsEditor.set({json:event.data.strct});                
                break;            
              case 'Handsontable':
                let div = me.tgtContent.select("#genText"+d.n),
                  hot = new Handsontable(div.node(), {data:event.data,height:contHeight,licenseKey: 'non-commercial-and-evaluation'});                
                break;            
              }
            progress.destroy();
            contResult.select('#progressGenConcept').remove();
          };            

        }


        function genere(e,r,d){
          let conj,a,formes,rs;
          addChampResult(d);
          if(d=='concept'){
            showGen(d,`[${r.type}_${r.lib}]`,'jsEditor');           
            return;
          }
          if(!r[d.k])r=d.data.filter(i=>i[d.k]==r[0])[0];
          me.appUrl.change(d.k,r[d.k]);
          switch (d.n) {
            case 'Generators':
              showGen(d,r.valeur,'jsEditor');           
              break;            
            case 'Verbs':
              conj = new conjugaisons({'api':me.api,'cont':me.tgtContent.select("#genText"+d.n)
                ,'v':r, oeuvre:me.oeuvre, 'appUrl':me.appUrl, 'progress':progress});
              break;            
            case 'Adjectives':
              //génère les différentes formes de l'adjectif
              a = `a_${r.id_concept}_${r.id_adj}`; rs = []; formes = [{'dtm':12,'sub':'m_joie'},{'dtm':12,'sub':'m_bonheur'}];
              formes.forEach(f=>{
                rs.push(`[${f.dtm}|${a}@${f.sub}]`);                  
                rs.push(`[${(f.dtm+50)}|${a}@${f.sub}]`);
                rs.push(`[${f.dtm}|${f.sub}][=1|${a}]`);
                rs.push(`[${(f.dtm+50)}|${f.sub}][=1|${a}]`);
              });
              showGen(d,rs,'Handsontable');           
              break;            
            case 'Nouns':
              //génère les différentes formes du substantif
              a = `m_${r.id_concept}_${r.id_sub}`; rs = []; formes = [{'dtm':12}];
              formes.forEach(f=>{
                rs.push(`[${f.dtm}|${a}]`);
                rs.push(`[${(f.dtm+50)}|${a}]`);
              });
              showGen(d,rs,'Handsontable');           
              break;            
          }
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

        me.data.forEach(cpt=>{
          me.linkData.forEach(d=>{
            me.api.delete(d.t,cpt.id_concept);
          });
          me.api.delete('gen_concepts',cpt.id_concept);
        })
      }


      if(this.remove) this.delete();
      else this.init();
  
    }
}
