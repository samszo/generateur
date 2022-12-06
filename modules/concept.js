import oMoteur from '../modules/moteur.js';
class concept {
    constructor(params) {
        var me = this;
        this.oeuvre = params.oeuvre ? params.oeuvre : false;
        this.dico = params.dico ? params.dico : false;
        this.api = params.api ? params.api : false;
        this.data = params.data ? params.data : false;
        this.tgtContent = params.tgtContent ? params.tgtContent : false;
        this.appUrl = params.appUrl ? params.appUrl : false;
        this.sync = params.sync ? params.sync : false;
        this.m = params.m ? params.m : new oMoteur.moteur({
            'api':this.api,
            'tgtContent':this.tgtContent,
            'appUrl':this.appUrl,
            'oeuvre':me.oeuvre
        });    
        this.linkData;
        this.init = function () {
            me.linkData=[
                {t:'gen_concepts_adjectifs',n:'Adjectifs',lt:'gen_adjectifs',k:'id_adj',data:[]},
                {t:'gen_concepts_generateurs',n:'Générateurs',lt:'gen_generateurs',k:'id_gen',data:[]},
                {t:'gen_concepts_substantifs',n:'Substantifs',lt:'gen_substantifs',k:'id_sub',data:[]},
                {t:'gen_concepts_syntagmes',n:'Syntagmes',lt:'gen_syntagmes',k:'id_syn',data:[]},
                {t:'gen_concepts_verbes',n:'Verbes',lt:'gen_verbes',k:'id_verbe',data:[]},
            ];
            if(me.sync)getSyncLinkData();
            else getLinkData();
        }
        function getSyncLinkData(){
          let rs, rsL;
          //ATTENTION il peut y avoir le même concept dans plusieurs dictionnaires
          me.data.forEach(cpt=>{
            me.linkData.forEach(d=>{
              rs = me.api.syncList(d.t,{filter:'id_concept,eq,'+cpt.id_concept});
              if(rs.records.length){
                let idP=[],ids = rs.records.map(r=>r[d.k]);
                //pagination tous les 50 
                ids.forEach((id,j)=>{
                    idP.push(id); 
                    if(idP.length==50){
                      rsL = me.api.syncRead(d.lt,idP);
                      rsL = Array.isArray(rsL) ? rsL : [rsL];
                      d.data=d.data.concat(rsL.map(obj => ({ ...obj, concept:cpt})));
                      idP=[];
                    }
                });
                if(idP.length){
                  rsL = me.api.syncRead(d.lt,idP);
                  rsL = Array.isArray(rsL) ? rsL : [rsL];
                  d.data=d.data.concat(rsL.map(obj => ({ ...obj, concept:cpt})));
                }
              }
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
                    if(v.records.length){
                        let idP=[],ids = v.records.map(r=>r[me.linkData[i].k]);
                        //pagination tous les 50 
                        ids.forEach((id,j)=>{
                            idP.push(id); 
                            if(idP.length==50){
                                pl.push({'i':i, p:me.api.read(me.linkData[i].lt,idP)})
                                idP=[];
                            }
                        });
                        if(idP.length)pl.push({'i':i, p:me.api.read(me.linkData[i].lt,idP)});

                        /*une requête par id    
                        ids.forEach(id=>pl.push({'i':i, p:me.api.read(me.linkData[i].lt,id)}))
                        */
                        /*une seule requête pour tous les ids
                        pl.push({'i':i, p:me.api.read(me.linkData[i].lt,ids)});
                        */
                    }
                });
                Promise.all(pl.map(d=>d.p)).then((vals) => {
                    vals.forEach((val,j)=>{
                        //me.linkData[pl[j].i].data=Array.isArray(val) ? val : [val];
                        //
                        if(Array.isArray(val))
                            val.forEach(v=>me.linkData[pl[j].i].data.push(v));
                        else
                            me.linkData[pl[j].i].data.push(val);
                    });
                    showLinkData();
                });    
            });
        }
        function showLinkData(){
            //construction des tab
            me.tgtContent.selectAll('nav').remove();
            me.tgtContent.selectAll('ul').remove();
            me.tgtContent.selectAll('div').remove();
            let dataLink = me.linkData.filter(d=>{
                return d.data.length
            }),
                //ajoute les outils
                tools = `<div class="container-fluid">
                  <a class="navbar-brand" href="#">${me.data.type+' '+me.data.lib}</a>
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarConcept" aria-controls="navbarConcept" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarConcept">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                      <li class="nav-item mx-2">
                        <button type="button" id="btnGenere" class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-shuffle"></i>
                        </button>
                      </li>
                      <li class="nav-item mx-2">
                        <button type="button" class="btn btn-sm btn-danger">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                      </li>
                      <li class="nav-item dropdown mx-2">
                        <button type="button" class="btn btn-sm btn-danger dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-square-plus"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#">Adjectifs</a></li>
                          <li><a class="dropdown-item" href="#">Générateurs</a></li>
                          <li><a class="dropdown-item" href="#">Substantifs</a></li>
                          <li><a class="dropdown-item" href="#">Syntagmes</a></li>
                          <li><a class="dropdown-item" href="#">Verbes</a></li>
                        </ul>
                      </li>
                    </ul>
                  </div>
                </div>`,
                toolsNav = me.tgtContent.append('nav').attr('class','navbar navbar-expand-lg bg-light').html(tools),
            //construction de la barre de nav
            navtabs = me.tgtContent.append('ul')
                .attr('class',"nav nav-pills mb-3")
                .attr('role',"tablist"),
            cont = me.tgtContent.append('div')
                .attr('class',"tab-content h-100");
            navtabs.selectAll('li').data(dataLink).enter().append('li')
                .attr('class',"nav-item")
                .attr('role',"presentation")
                .append('button').attr('class',(d,i)=>i==0 ? "nav-link active" : "nav-link")
                    .attr('data-bs-toggle',"pill")
                    .attr('data-bs-target',d=>"#tab-pane-"+d.lt)
                    .attr('type',d=>"button")
                    .attr('role',d=>"tab")
                    .attr('aria-controls',d=>"tab-pane-"+d.lt)
                    .attr('aria-selected',(d,i)=>i==0 ? "true":"false")
                    .attr('id',d=>"tab-"+d.lt)
                    .html(d=>d.n)
                    .on('click',changeTab);
            //construction des contenus
            cont.selectAll('div').data(dataLink).enter().append('div')
                .attr('class',(d,i)=>i==0 ? "tab-pane fade show active" : "tab-pane fade")
                .attr('id',d=>"tab-pane-"+d.lt)
                .attr('role',"tabpanel")
                .attr('aria-labelledby',d=>"tab-"+d.lt)
                .attr('tabindex',0)
                //.html(d=>"tab-"+d.lt)
                .each(showLinkDataContent)
                ;
            //ajout des évenements
            d3.select('#btnGenere').on('click',e=>genere(e,me.data));
        }
        function changeTab(e,d){
            d.hot.refreshDimensions();
        }
        function showLinkDataContent(d, i){
            let pane = d3.select("#tab-pane-"+d.lt), cont = pane.append('div')
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
                    height: (rect.height/2),
                    width: rect.width,
                    rowHeights: 40,
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
                        columns: [0, 1]
                    },
                    allowInsertColumn: false,
                    copyPaste: true,
                    contextMenu: {
                        callback(key, selection, clickEvent) {
                          // Common callback for all options
                          console.log(key, selection, clickEvent);
                        },
                        items: {
                          row_below: {
                            name(){
                                return `<button type="button" class="btn btn-sm btn-danger">
                                <i class="fa-regular fa-square-plus"></i>
                                </button>`;    
                            }
                          },
                          remove_row: {
                            name(){
                                return `<button type="button" class="btn btn-sm btn-danger">
                                <i class="fa-regular fa-trash-can"></i>
                                </button>`;    
                            }
                          },
                          copy: {
                            name(){
                                return `<button type="button" class="btn btn-sm btn-danger">
                                <i class="fa-regular fa-copy"></i>
                                </button>`;    
                            }                            
                          },
                          /*
                          paste: {
                            name: 'Coller'
                          },
                          importer: { // Own custom option
                            name() { // `name` can be a string or a function
                              return '<i>Importer</i>'; // Name can contain HTML
                            },
                            callback(key, selection, clickEvent) { // Callback for specific option
                              setTimeout(() => {
                                alert('Hello world!'); // Fire alert after menu close (with timeout)
                              }, 0);
                            }
                          }
                          */
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
                      }
                });

        }
        function genere(e,r,d){
            if(!r[d.k])r=d.data.filter(i=>i[d.k]==r[0])[0];
            me.appUrl.change(d.k,r[d.k]);
            me.m.genere(r);

        }

        this.init();
    
    }
}
export default {concept};