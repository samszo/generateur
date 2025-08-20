import {JSONEditor} from '../node_modules/vanilla-jsoneditor/index.js'
import {getIn, parseFrom} from '../node_modules/immutable-json-patch/lib/esm/index.js'
import {modal} from './modal.js';
import {conjugaisons} from './conjugaisons.js';
import { WBK } from '../node_modules/wikibase-sdk/dist/index.js';
import {writeGen} from './writeGen.js';

export class concept {
    constructor(params) {
        var me = this;
        this.oeuvre = params.oeuvre ? params.oeuvre : false;
        this.dico = params.dico ? params.dico : false;
        this.oDico = params.oDico ? params.oDico : false;
        this.api = params.api ? params.api : false;
        this.data = params.data ? params.data : false;
        this.omk = params.omk ? params.omk : false;
        this.tgtContent = params.tgtContent ? params.tgtContent : false;
        this.appUrl = params.appUrl ? params.appUrl : false;
        this.sync = params.sync ? params.sync : false;
        this.remove = params.remove ? params.remove : false;
        this.linkData;
        this.conjData;
        this.jsEditor;
        this.jsPath;
        this.wdk;
        this.wGen;
        var m=new modal(), mParamsGen, contResult, contHeight, userAllowed, progress;
        

        this.init = function () {

          me.wdk = WBK({
            instance: 'https://www.wikidata.org',
            sparqlEndpoint: 'https://query.wikidata.org/sparql'
          });

          d3.json(me.omk.api.replace("api","s/balpien/page/ajax")+"?json=1&helper=sql&action=getConceptTerms&idCpt="+me.data.id).then(data=>{
            userAllowed = true;
            me.linkData=[
              {'class':me.omk.getClassByTerm('genex:Term'),data:[],mAdd:true},
              //uniquement dans le dictionnaire général {n:'Syntagms',t:'gen_syntagmes',k:'id_syn',data:[],mAdd:true},
              //{n:'Verbs',t:'gen_verbes',k:'id_verbe',data:[],mAdd:true},
              {n:'Uris',t:'gen_uris',k:'id_uri',data:[],mAdd:true},
              {n:'Sparqls',t:'gen_sparqls',k:'id_sparql',data:[],mAdd:true},
            ];
            //construction des modals pour chaque type d'item              
            let grpData = d3.group(data,d=>d["resource_class_id"]+""), 
            btnParams = [{"t":"Create","f":addItem},{"t":"Update","f":addItem},{"t":"Delete","f":verifDeleteItem}];
            me.linkData.forEach(ld=>{
              if(ld.class){
                ld.t = ld.class["o:id"]+"";
                ld.n = ld.class["o:local_name"];
                ld.mAdd = m.add('modalAddConcept'+ld.class["o:local_name"]+"s","modal-lg");                  
                ld.mAdd.s.select('.modal-footer').selectAll('button').remove();                
                ld.mAdd.s.select('.modal-footer').selectAll('button').data([ld,ld,ld]).enter().append('button')
                    .attr('id',(d,i)=>{
                      d.a = btnParams[i].f;
                      return "btnAction"+d.n+i
                    })
                    .attr('type',"button")
                    .attr('class',"btn btn-danger").html((d,i)=>btnParams[i].t)
                    .on('click',runAction);
                //ajoute les options
                if(ld.n=="Term"){
                  // de conjugaison
                  me.conjData = me.oeuvre.getConjugaisons();
                  ld.mAdd.s.select('#verbConj').selectAll('option').data(
                    [{'id_conj':-1,'modele':'choose a conjugation model'}].concat(me.conjData)
                    ).join(
                    enter=>enter.append('option')
                      .attr('value',c=>c.id)
                      .html(c=>c.title.replace("Modèle de conjugaison : ",""))                    
                  );
                  // de type
                  let termTypes = ["adjectif","generateur","nombre","substantif","syntagme","verbe"];
                  ld.mAdd.s.select('#termType').selectAll('option').data(termTypes).join(
                    enter=>enter.append('option')
                      .attr('value',t=>t)
                      .html(t=>t)                    
                  );
                  // de textcomplete pour le générateur
                  me.wGen = new writeGen({'oeuvre':me.oeuvre,'omk':me.omk,'textarea':ld.mAdd.s.select('#genValue')});
                  // de génération
                  setParamGen(ld);
                }
                if(grpData.has(ld.t)){
                  ld.data = grpData.get(ld.t);
                }else{
                  ld.data = [];
                }
              }
            });
            showLinkData();
          }); 
        }

        function runAction(e,d){
          d.a(e,d);
        }

        async function setParamGen(ld){

            //de paramètres de génération
            mParamsGen = m.add('modalParamsGen');
            if(me.oeuvre.dataDico["négations"][0].data.length==0) await me.oDico.setData(me.oeuvre.dataDico["négations"][0].idDico,"négations");
            mParamsGen.s.select('#conjNeg').selectAll('option').data(
                [{'num':-1,'lib':'Choose...'}].concat(me.oeuvre.dataDico["négations"][0].data)
                )
              .join(
                enter=>enter.append('option')
                  .attr('value',d=>d.num)
                  .html(d=>d.lib)                    
              );
            if(me.oeuvre.dataDico["pronoms"][0].data.length==0) await me.oDico.setData(me.oeuvre.dataDico["pronoms"][0].idDico,"pronoms");
            mParamsGen.s.select('#conjSujet').selectAll('option').data(
                me.oeuvre.dataDico["pronoms"][0].data.filter(p=>p.type=="sujet")
            ).join(
              enter=>enter.append('option')
                .attr('value',d=>d.num)
                .html(d=>d.lib+ " - "+d.elision)                   
            );
            mParamsGen.s.select('#conjSujetComp').selectAll('option').data(
              [{'num':-1,'lib':'Choose...','elision':''}].concat(me.oeuvre.dataDico["pronoms"][0].data.filter(p=>p.type=="complément"))
            ).join(
              enter=>enter.append('option')
                .attr('value',d=>d.num)
                .html(d=>d.lib+ " - "+d.elision)                   
            );
            mParamsGen.s.select('#conjSujetInd').selectAll('option').data(
              [{'num':-1,'lib':'Choose...','elision':''}].concat(me.oeuvre.dataDico["pronoms"][0].data.filter(p=>p.type=="sujet indéfini"))
            ).join(
              enter=>enter.append('option')
                .attr('value',d=>d.num)
                .html(d=>d.lib+ " - "+d.elision)                   
            );
            
            mParamsGen.s.select('#btnGenereDetConj').on('click',e=>{
                /*construction du déterminant
                Position 0 : type de négation
                Position 1 : temps verbal
                Position 2 : pronoms sujets définis
                Positions 3 ET 4 : pronoms compléments
                Position 5 : ordre des pronoms sujets
                Position 6 : pronoms indéfinis
                Position 7 : Place du sujet dans la chaîne grammaticale
                */                
                let sujet = mParamsGen.s.select('#conjSujet').node().value,
                    temps = mParamsGen.s.select('#conjTemps').node().value,
                    sujetComp = mParamsGen.s.select('#conjSujetComp').node().value,
                    sujetInd = mParamsGen.s.select('#conjSujetInd').node().value,
                    neg = mParamsGen.s.select('#conjNeg').node().value, 
                    ordre = mParamsGen.s.select('#ordreProSujInv').node().checked;                     
                mParamsGen.s.select("#detConjResult").html(
                  (neg==-1 ? 0 : neg)
                  +temps
                  +sujet
                  +(sujetComp==-1 ? "00" : (sujetComp.length==1 ? "0"+sujetComp : sujetComp))
                  +(ordre ? 1 : 0)
                  +(sujetInd==-1 ? "0" : sujetInd)
                  +"|"
                );
            });
            
            //gestion des événements du bloc de génération
            ld.mAdd.s.select('#btnGenereInModal').on('click',e=>{
              addChampResult("concept",ld.mAdd.s.select('#genResultInModal'));
              addItem(e,ld,d=>{
                showGen(d,{'term':d["o:id"],'concept':me.data.id},'jsEditor',ld.mAdd.s.select('#genTextconcept'));           
              });                     
            });
            ld.mAdd.s.select('#btnGenereParams').on('click',e=>{
                mParamsGen.m.show();
            });
            ld.mAdd.s.select('#btnGenereHelp').on('click',e=>{
              const textarea = ld.mAdd.s.select('#genValue').node();
              const selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
              console.log('Selected text:', selectedText);
              if(!selectedText){
                m.setBody('<h3  class="bg-danger">Please select a text for information.</h3>');
                m.setBoutons([{'name':"Close"}]);                
                m.show();   
              }
            });

        }

        async function getSparql(d){
          let SPARQL = `SELECT ?item ?itemLabel WHERE {
            SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE]". }
            {
              SELECT ?item (MD5(CONCAT(str(?item),str(RAND()))) as ?random) WHERE {
                ?item p:P106 ?statement0.
                ?statement0 (ps:P106/(wdt:P279*)) wd:Q1028181.
                ?item p:P734 ?statement1.
                ?statement1 (ps:P734/(wdt:P279*)) _:anyValueP734.
              }
              ORDER BY ?random
          LIMIT 1
            }
          }`;

          const url = me.wdk.sparqlQuery(SPARQL)
          const results = await fetch(url)
            .then(res => res.text())
            .catch(function(error) {
              progress.destroy();
              contResult.select('#progressGenConcept').remove();
              me.tgtContent.select("#genText"+d.n).html('Sparql error: ' + error.message);      
            });
          return results;
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
              <a class="navbar-brand" href="#">[${me.data.title}]</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarConcept" aria-controls="navbarConcept" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarConcept">
                <ul id="listBtnCpt" class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item mx-1">
                    <button type="button" id="btnGenere" class="btn btn-sm btn-danger">
                        <i class="fa-solid fa-shuffle"></i>
                    </button>
                </li>                
                <li class="nav-item mx-1">
                    <button type="button" id="btnCptExport" class="btn btn-sm btn-danger">
                        <i class="fa-solid fa-file-export"></i>
                    </button>
                </li>
                `;
            if(userAllowed){
              tools += `
                <li class="nav-item dropdown mx-2">
                  <button type="button" class="btn btn-sm btn-danger dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-regular fa-square-plus"></i>
                  </button>
                  <ul class="dropdown-menu" id="ddmAddCptItem" >
                  </ul>
                </li>`;
            }
              tools += `
                <li class="nav-item mx-1">
                    <button type="button" id="btnCptGenerateEmbed" class="btn btn-sm btn-danger">
                      &lt;/&gt;
                    </button>
                </li>`;
            tools += `           
                </ul>
              </div>
            </div>`;
            let toolsNav = me.tgtContent.append('nav').attr('class','navbar navbar-expand-lg bg-light').html(tools);
            toolsNav.select('#ddmAddCptItem').selectAll('li').data(me.linkData).enter().append('li')
              .append('a').attr('class',"dropdown-item").html(ld=>ld.n).on('click',showAddItem);

            toolsNav.select("#btnCptExport").on('click',me.exportCpt);            
            toolsNav.select("#btnCptGenerateEmbed").on('click',embedGenerateCpt);            
            
  
            if(me.omk){
              //ajoute le lien vers OmekaS
              toolsNav.select("#listBtnCpt").append('li').attr('class',"nav-item mx-2").append('a')
                  .attr('href',me.omk.getAdminLink(false,me.data.id,"o:Item"))
                  .attr('target',"_blank")
                  .append('img').attr('src','asset/images/logos/OmekaS.png')
                      .style("margin-top","-4px")
                      .style("height","20px");
            }         

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
            d3.select('#btnGenereOld').on('click',e=>genereOld(e,me.data,'concept'));
            d3.select('#btnGenereTest').on('click',genereTest);
            //vérification du passage de paramètre
            if(me.appUrl.params && me.appUrl.params.has('linkDataTab')){
              let dt = me.linkData.filter(ld=>ld.n==me.appUrl.params.get('linkDataTab'))[0];
              changeTab(null,dt);
            }else{
              //affiche la première tab avec du contenu            
              changeTab(null,me.linkData.filter(ld=>ld.data.length)[0]);                
            }
        }
        this.exportCpt = function(e,d){
            const a = document.createElement('a');
            const url = me.omk.api.replace("api/","s/balpien/page/ajax?json=1&helper=sql&action=exportCpt&export=csv&idCpt="+me.data.id);
            a.href = url;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function embedGenerateCpt(e,d){
          let url = me.omk.api.replace("api","s/balpien/page/ajax")+"?json=1&helper=generate&idConcept="+me.data.id;
          const embedCode = `<button id="btnAddText${me.data.id}" type="button" onclick="getDataFor${me.data.id}()">Générer</button>
          <div id="displayText${me.data.id}" style="margin-top:10px; color: #333;"></div>    
<script>
    function getDataFor${me.data.id}() {
        const u = "${url}";
        const c = document.getElementById('displayText${me.data.id}');
        fetch(u)
            .then(async response => c.textContent = await response.text())
            .catch(error => c.textContent=error);
    }
    getDataFor${me.data.id}();
</script> `;
          // Affiche le code embed dans un div HTML pour un aperçu visuel
          m.setBody(`<div class="text-bg-light text-start"><h5>Copy this code to generate this concept:</h5>
          <pre class="overflow-y-scroll overflow-x-scroll text-bg-dark p-3" style="height:300px;">${embedCode.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</pre>
          </div>`);
          m.setTitle("Embed Code To Generate Concept");
          m.setBoutons([{ name: "Close" }]);
          m.show();
        }
        
        function showUpdateItem(d, id){
          if(d.mAdd){
            d.mAdd.s.select(".modal-title").html("Update term")//Adding a new term
            d.mAdd.s.select('#btnAction'+d.n+"0").style('display','none');0
            d.mAdd.s.select('#btnAction'+d.n+"1").style('display','inline-block');
            d.mAdd.s.select('#btnAction'+d.n+"2").style('display','inline-block');
            let item = d.data.filter(i=>i.id==id)[0], accords = [];
            if(item.accords){
              item.accords.split(',').forEach(a=>{
                let vals = a.split(' : ');
                accords[vals[0]] = vals[1];
              })
            }
            d.mAdd.s.selectAll('.inptValue').nodes().forEach(n=>{
                if(n.hasAttribute("keycol")){
                  console.log(n.getAttribute("keycol")+' '+n.value);
                  switch (n.getAttribute('keycol')) {
                    case 'genre':
                      n.checked = item[n.getAttribute('keycol')] ? 1 : 0;                    
                      break;                  
                    case 'hasElision':
                      n.checked =  accords[n.getAttribute('keycol')] ? 1 : 0;                    
                      break;                  
                    case 'accordFemSing':
                    case 'accordMasSing':
                    case 'accordFemPlu':
                    case 'accordMasPlu':
                      n.value =  accords[n.getAttribute('keycol')] ? accords[n.getAttribute('keycol')] : "";                    
                      break;                  
                    default:
                      n.value = item[n.getAttribute('keycol')] ? item[n.getAttribute('keycol')] : "";
                      break;
                  }
                }
            });
            d.mAdd.m.show();
          }          
        }
        function showAddItem(e,d){
          if(d.mAdd){
            d.mAdd.s.select(".modal-title").html("Adding a new term")
            d.mAdd.s.select('#btnAction'+d.n+"0").style('display','inline-block');
            d.mAdd.s.select('#btnAction'+d.n+"1").style('display','none');
            d.mAdd.s.select('#btnAction'+d.n+"2").style('display','none');
            d.mAdd.s.selectAll('.inptValue').nodes().forEach(n=>{
              n.checked = false;
              n.value = "";
            });
            d.mAdd.m.show();
          }
        }        
        function addItem(e,d,cb=null){          
          //récupère les valeurs
          let valeurs = {};
          d.mAdd.s.selectAll('.inptValue').nodes().forEach(n=>{
            if(n.hasAttribute("keycol"))
              console.log(n.getAttribute("keycol")+' '+n.value);
              if(n.getAttribute('type')=="checkbox"){
                  valeurs[n.getAttribute('keycol')]=n.checked ? 1 : 0;
              }else if(n.getAttribute('keycol')=='genre' || n.getAttribute('keycol')=='elision'){
                if(valeurs[n.getAttribute('keycol')]===undefined)
                  valeurs[n.getAttribute('keycol')]=n.checked ? n.getAttribute('value') : undefined;
              }else if(n.getAttribute('keycol')=='hasConjugaison'){
                if(n.selectedIndex != -1){
                  valeurs[n.getAttribute('keycol')]=n.value;
                  valeurs.libConjugaison = n.options[n.selectedIndex].text;
                }
              }else 
                valeurs[n.getAttribute('keycol')]=n.value;
          });
          valeurs.id_concept= me.data.id;
          //création de l'item
          let title =  valeurs.type+':'
                  +(valeurs.gen ? valeurs.gen : "")
                  +' - '+(valeurs.prefix ? valeurs.prefix : "")
                  +' - '+(valeurs.gender?valeurs.gender:"")
                  +' - '+(valeurs.elision?valeurs.hasElision:"")
                  +' - '+(valeurs.libConjugaison?valeurs.libConjugaison:"")
                  +' - '+(valeurs.accordFemSing?valeurs.accordFemSing:"")
                  +'_'+(valeurs.accordFemPlu?valeurs.accordFemPlu:"")
                  +'_'+(valeurs.accordMasSing?valeurs.accordMasSing:"")
                  +'_'+(valeurs.accordMasPlu?valeurs.accordMasPlu:""),
            r = {'elision':valeurs.elision ? 0 : 1,
              'title' : title,
              'prefix' : valeurs.prefix,
              'description_term':valeurs.description,
              'type_term':valeurs.type,
              'hasConjugaison':valeurs.hasConjugaison,
              'generateur':valeurs.gen,
              'gender':valeurs.gender,
              'fem_plu':valeurs.accordFemPlu,
              'fem_sin':valeurs.accordFemSing,
              'mas_plu':valeurs.accordMasSing,
              'mas_sin':valeurs.accordMasPlu            
            };

          if(d.mAdd.s.select(".modal-title").html()=="Update term")
            r.id = valeurs.id;
          me.oDico.createTerm({'o:id':me.data.id}, r, 1, !cb ? d.mAdd.s.select('#creaTermResult') : null,cb);
        }
        function changeTab(e,d){
          contResult.selectAll('div').remove();
          if(!d)return false;
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
                .attr('class',"container-fluid"),
                /*
                headers = me.omk ? me.omk.getPropsHeader(d.data[0]) : Object.keys(d.data[0]),
                showProps = ["o:id","dcterms:title","genex:hasType","genex:hasElision","genex:hasPrefix","genex:hasAccord","lexinfo:gender","genex:hasConjugaison"],
                hCol = {columns: headers.map((h,i)=>{
                        if(me.omk) return showProps.includes(h['o:term']) ? null : i
                        else return h.substring(0,3)=='id_' ? i : null
                    }).filter(k=>k!=null)
                },
                gridData = me.omk ? me.omk.getDataForGrid(d.data,headers): d.data,
                */
                headers = Object.keys(d.data[0]),
                hCol = {columns: headers.map((h,i)=>{
                        if(me.omk) return h.indexOf('_') > 0 ? i : null
                        else return h.substring(0,3)=='id_' ? i : null
                    }).filter(k=>k!=null)
                },
                gridData = d.data,
                rect = me.tgtContent.select('.tab-content').node().getBoundingClientRect(),
                div = cont.append('div').attr('class',"row").append('div').attr('class',"col-12")
                    .append('div').attr('class',"clearfix");   
                d.hot = new Handsontable(div.node(), {
                    data: gridData,
                    rowHeaders: true,
                    colHeaders: headers,//me.omk ? headers.map(h=>h["o:label"]): headers,
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
                    hiddenColumns: hCol,
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
                                if(!me.omk)genere(e,r[0],'term');
                                else genere(e,r,d);
                            }
                          },
                          omkAdmin: { // Own custom option
                            name() { // `name` can be a string or a function
                              return `<img src="asset/images/logos/OmekaS.png" style="margin-top:-4px;height:20px"</img>`;
                            },
                            callback(key, s, e) { // Callback for specific option
                              if(!me.omk)return;
                              let r = this.getDataAtRow(s[0].start.row);
                              window.open(me.omk.getAdminLink(null,r[0],"o:Item"), '_blank');
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
                  if(col.substring(0,2)=='id'){
                    showUpdateItem(d, dr[i]);
                    me.appUrl.change(col,dr[i]);
                  }
                })
              });
              /*
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
              */

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
                  /*if(me.omk)
                    editors.push({data:h['o:label'], type: 'text'})
                  else*/
                    editors.push({data:h, type: 'text'})                  
                  break;
              }
            })
          return editors;
        }
        function verifDeleteItem(e,d){
          if(userAllowed){
            m.setBody('<h3  class="bg-danger">Are you sure you want to delete this item?</h3>');
            m.setBoutons([{'name':"Close"},
                {'name':"Delete",'class':'btn-danger','fct':f=>deleteItem(e,d)}
                ]);                
          }else{
            m.setBody('<h3  class="bg-danger">You are not authorized to delete this item</h3>');
            m.setBoutons([{'name':"Close"}]);                
          }
          m.show();    
        }
        function deleteItem(e,d){
          let id = d.mAdd.s.select('#termId').node().value;
          d3.json(me.omk.api.replace("api/","s/balpien/page/ajax")
              +"?json=1&helper=sql&action=deleteConcept&id="
              +id).then(e=>{
              if(e.status=='ok'){
                m.hide();
                d.mAdd.m.hide();    
                me.init();                
              }else{
                  console.log('error delete item',e);
              }
          }).catch(e=>{
              console.log('error delete item',e);
          });
        }

        function changeJsonEditor(u,p,r){
          let allowChangeKey=['lib','term_id','cpt_id'], item, path = parseFrom(r.patchResult.redo[0].path),
            key = path[path.length-1], link;
          if(allowChangeKey.includes(key)){
            item = getIn(u.json,path.slice(0, -1));
            console.log('changeJsonEditor',item);
            if(me.omk){
              link = me.omk.getAdminLink(false,item[u.json,path[u.json,path.length-1]],"o:Item")
              window.open(link, '_blank');
            }
          }          
        }

        function addChampResult(d,result=null){
          if(!result)result=contResult;
            //ajoute les champs de résultats
            result.selectAll('div').remove();
            let htmlResult = `<div class="row">
            <div class="col-6">
              <h6>Texts</h6>
              <div class="progress" id="progressGenConcept">
              </div>
              <div class="overflow-y-scroll overflow-x-scroll" id="genText${d.n ? d.n : d}" style="height:300px;width:100%;text-align:left;"></div>
            </div>`;

            if(d=='concept' || d.n=="Term"  || d.t=="gen_generateurs" || d.t=="gen_uris"){
              htmlResult += `<div class="col-6 pe-2">
                      <h6>Structure</h6>
                      <div id="genStrct" style="height:300px"></div>
                    </div>
                  </div>`;
              result.html(htmlResult);
              me.jsEditor = new JSONEditor({
                target: result.select("#genStrct").node(),
                props: {
                  mode: 'tree',
                  onChange:changeJsonEditor, 
                  /*(updatedContent, previousContent, { contentErrors, patchResult }) => {
                    // content is an object { json: JSONValue } | { text: string }
                    console.log('onChange', { updatedContent, previousContent, contentErrors, patchResult })
                    content = updatedContent
                  }*/
                }
              })
              //écouteur pour les modifications
            }else{
              htmlResult += `</div>`;
              result.html(htmlResult);
            }

            //ajoute le progresse bar
            progress = new ProgressBar.Circle(result.select('#progressGenConcept').node(), {
              color: '#aaa',
              // This has to be the same size as the maximum width to
              // prevent clipping
              strokeWidth: 4,
              easing: false,
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

        function showGen(d,g,view,result=false){
          if(me.omk){
            let url = me.omk.api.replace("api","s/balpien/page/ajax")+"?json=1&helper=generate&structure=1&explode=1&"
              +(g.term ? "idTerm="+g.term+"&idConcept="+g.concept : "idConcept="+g.id);
            fetch(url)
              .then(async response => {
                if (!response.ok) {
                  let r = await response.text();
                  showResultGen(d,{'error':r},view,result);
                }else  return response.json();
              })
              .then(data => {
                console.log(data);
                showResultGen(d, data, view, result);
              });
            /*
            d3.json(url).then(data=>{
              console.log(data);
              showResultGen(d,data,view,result);
            }).catch(function(error) {
              showResultGen(d,{'error':error},view,result);
            });
            */
          }else{
            me.oeuvre.wGen.postMessage({
              'g':g,
              'dicos':me.oeuvre.dicos,
              //'id_oeu':me.oeuvre.curOeuvre.id_oeu,
              'id_dico':me.oeuvre.curDico.d.id_dico,
              'apiUrl':me.oeuvre.auth.apiReadUrl
            });
            me.oeuvre.wGen.onmessage = function(event) {
              showResultGen(d,event.data,view);
            };
            me.oeuvre.wGen.onerror = function(error) {
              progress.destroy();
              contResult.select('#progressGenConcept').remove();
              me.tgtContent.select("#genText"+d.n).html('Generateur error: ' + error.message);    
            };
          }
        }

        function showResultGen(d,data,view,divResult){          
          divResult = divResult ? divResult : me.tgtContent.select("#genText"+(d.n?d.n:d)); 
          if(data.error){
            divResult.html(data.error);
            if(progress && progress.path)progress.destroy();
            d3.select(divResult.node().parentNode).select('#progressGenConcept').remove();          
            return;
          } 
          switch (view) {
            case 'jsEditor':
              me.jsEditor.set({json:data.strct});                
              break;            
            case 'Handsontable':
              let div = me.tgtContent.select("#genText"+d.n),
                hot = new Handsontable(div.node(), {data:data,height:contHeight,licenseKey: 'non-commercial-and-evaluation'});                
              break;            
            default:
              me.jsEditor.set({json:data.strct});                
          }
          divResult.html(data.texte);    
          if(progress && progress.path)progress.destroy();
          d3.select(divResult.node().parentNode).select('#progressGenConcept').remove();          
        }

        function showSparql(d,g){
          getSparql(d).then(r=>{
            me.jsEditor.set({json:r});                
            progress.destroy();
            contResult.select('#progressGenConcept').remove();
            me.tgtContent.select("#genText"+d.n).html(r);    
          });
        }

        function showUri(d,r){
            me.jsEditor.set({json:r});                
            progress.destroy();
            contResult.select('#progressGenConcept').remove();
            me.tgtContent.select("#genText"+d.n).html(r.title);    
        }

        function genereOld(e,r,d){
          addChampResult(d);
          if(d=='concept'){
            if(!r.omk)r.omk = me.omk.getItem(r.id);
            if(me.oeuvre.curOeuvre["dcterms:identifier"] && r.omk["dcterms:identifier"]){
              let oeu = me.oeuvre.curOeuvre["dcterms:identifier"][0]['@value'], 
                gen = r.omk["dcterms:identifier"][0]['@value'];
              d3.text(`https://artnum.univ-paris8.fr/balpe/generateur/services/api.php?oeu=${oeu}&cpt=${gen}`).then(
                data=>{
                  me.tgtContent.select("#genText"+d.n).html(data);         
                  progress.destroy();
                  contResult.select('#progressGenConcept').remove();
                }
              ).catch(
                error=>{
                  progress.destroy();
                  contResult.select('#progressGenConcept').remove();
                  me.tgtContent.select("#genText"+d.n).html('Generateur error: ' + error.message);    
                }
              );              
            }
          }
        }

        function genereTest(e){
          addChampResult('concept');
          me.oeuvre.explodeConcept(me.data.id).then(rs=>{
            if(rs.length==0){
              me.tgtContent.select("#genTextjsEditor").html('No test generated');
              return;
            }
            showResultGen(me.data,{'strct':rs},'jsEditor');
            /*constrution des tests
            let tests = [
              {'title':'Adjectif <i class="fa-solid fa-venus">','gen':'[12|m_joie@f_joie][=1|a_m_joie]','type':'Adjective','id_concept':me.data.id_concept},
              {'title':'<i class="fa-solid fa-mars">','gen':'[12|m_bonheur@f_bonheur][=1|a_m_bonheur]','type':'Adjective','id_concept':me.data.id_concept},
              {'title':'<i class="fa-solid fa-ellipsis">','gen':'[12|m_joie@f_joie][=1|a_m_joie][=1|a_f_joie]','type':'Adjective','id_concept':me.data.id_concept};
            rs.forEach(r=>{
              let test = {'title':r.title,'type':r.type,'id':r.id_concept};
              if(r.type=='Term')test.term = r.id_term;
              tests.push(test);
            });
            */           
          });
        }

        function genere(e,r,d){
          let conj,a,formes,rs;
          addChampResult(d);
          if(d=='concept'){
            showGen(d,me.omk ? r : `[${r.type}_${r.title}]`,'jsEditor');           
            return;
          }
          if(r[4]=='Term'){
            showGen(d,{'term':r[0],'concept':me.data.id},'jsEditor');           
            return;
          }
          if(!r[d.k])r=d.data.filter(i=>i[d.k]==r[0])[0];
          me.appUrl.change(d.k,r[d.k]);
          switch (d.n) {
            case 'Uris':
              showUri(d,r);           
              break;            
            case 'Sparqls':
              getSparql();
              showGen(d,r.valeur,'jsEditor');           
              break;            
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
