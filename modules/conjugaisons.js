import {moteur} from '../modules/moteur.js';

export class conjugaisons {
    constructor(params) {
        var me = this;
        this.api = params.api;
        this.tgtContent = params.cont;
        this.progress = params.progress;
        this.v = params.v ? params.v : false;
        this.oeuvre = params.oeuvre;
        this.appUrl = params.appUrl ? params.appUrl : false;
        this.init = function () {
            if(!me.oeuvre.rsTerm)me.oeuvre.rsTerm = me.api.syncList('gen_terminaisons',{filter:['id_conj,eq,'+me.v.id_conj],order:'num,asc'}).records;
            //if(!me.oeuvre.rsProSuj)me.oeuvre.rsProSuj = me.api.syncList('gen_pronoms',{filter:['id_dico,eq,'+me.oeuvre.dicos.filter(d=>d.type=='pronoms')[0].id_dico,'type,eq,sujet'],order:'num,asc'}).records;
            //construction du tableau des conjugaisons
            me.tgtContent.selectAll('ul').remove();            
            me.tgtContent.selectAll('div').remove();            
            let temps = [{t:'present',num:1,data:[]},
                {t:'imperfect',num:2,data:[]},
                {t:'simple past',num:3,data:[]},
                {t:'future',num:4,data:[]},
                {t:'conditional present',num:5,data:[]},
                {t:'present subjunctive',num:6,data:[]},
                {t:'imperative',num:7,data:[]},
                {t:'present participle',num:8,data:[]},
                {t:'infinitive',num:9,data:[]}
                ],
            //interface avec tab
            navtabs = me.tgtContent.append('ul')
                .attr('class',"nav nav-pills mb-3")
                .attr('role',"tablist"),
            cont = me.tgtContent.append('div')
                .attr('class',"tab-content");
            navtabs.selectAll('li').data(temps).enter().append('li')
                .attr('class',"nav-item")
                .attr('role',"presentation")
                .append('button').attr('class',(d,i)=>{
                      let c = i==0 && d.data.length ? "nav-link active text-bg-danger" : 
                        d.data.length ? 'nav-link text-bg-secondary' : 'nav-link text-bg-secondarydark';
                      return c; 
                    })
                    .attr('data-bs-toggle',"pill")
                    .attr('data-bs-target',d=>"#tab-pane-temps"+d.num)
                    .attr('type',d=>"button")
                    .attr('role',d=>"tab")
                    .attr('aria-controls',d=>"tab-pane-temps"+d.num)
                    .attr('aria-selected',(d,i)=>i==0 && d.data.length ? "true":"false")
                    .attr('id',d=>"tab-temps"+d.num)
                    .html(d=>d.t)
                    .on('click',changeTab)
                    .each((d,i)=>d.tab = new bootstrap.Tab("#tab-temps"+d.num));
              
            //construction des contenus
            cont.selectAll('div').data(temps).enter().append('div')
                .attr('class',(d,i)=>i==0 ? "tab-pane fade show active" : "tab-pane fade")
                .attr('id',d=>"tab-pane-temps"+d.num)
                .attr('role',"tabpanel")
                .attr('aria-labelledby',d=>"tab-temps"+d.num)
                .attr('tabindex',0);
            //affichage du présent
            changeTab(null,temps[0]);            
        }
        function genereConjugaisons(d){
            let m = new moteur({'api':me.api,'oeuvre':me.oeuvre}),gen=[], v,
            pronoms = d.num == 8 || d.num == 9 ? [0] : [1,2,3,4,5,7];
            //récupère le premier verbe ayant le modèle
            if(me.v.modele){
                v = me.api.syncList('gen_verbes',{filter:'id_conj,eq,'+me.v.id_conj}).records[0];
            }else v = me.v;
            //génére la conjugaison du verbe
            //me.oeuvre.rsProSuj.forEach(s=>{
            //    gen = `[0${d.num}${s.num}00000|v_${me.v.id_concept}_${me.v.id_verbe}]`;
            pronoms.forEach(s=>{
                gen.push(`[0${d.num}${s}00000|v_${v.id_concept}_${v.id_verbe}]`);
            });
            me.oeuvre.wGen.postMessage({
                'g':gen,
                'dicos':me.oeuvre.dicos,
                'id_dico':me.oeuvre.curDico.d.id_dico,
                'apiUrl':me.oeuvre.auth.apiReadUrl
              });
            me.oeuvre.wGen.onmessage = function(event) {
                event.data.forEach(g=>{
                    d.data.push({
                        'id_trm':g.strct[0].terminaison[0].id_trm
                        ,'generator':g.gen,'conjugaison':g.texte                    
                        , 'terminaison':g.strct[0].terminaison[0].lib
                    });    
                }) 
                showConjugaisons(d);
                me.progress.destroy();
                tgtContent.select('#progressGenConcept').remove();
              };            
    

        }            
        function showConjugaisons(d, i){            
                if(d.data.length==0){
                    genereConjugaisons(d);
                    return;
                }
                let pane = d3.select("#tab-pane-temps"+d.num), 
                    cont = pane.append('div')
                        .attr('class',"container-fluid"),
                    headers = Object.keys(d.data[0]),
                    rect = me.tgtContent.select('.tab-content').node().getBoundingClientRect(),
                    div = cont.append('div').attr('class',"row").append('div').attr('class',"col-12")
                        .append('div').attr('class',"clearfix");   
                d.hot = new Handsontable(div.node(), {
                    data: d.data,
                    rowHeaders: true,
                    colHeaders: headers,
                    height: me.v.modele ? rect.height : (rect.height/3),
                    //width: rect.width,
                    rowHeights: 40,
                    selectionMode:'range',
                    manualRowResize: true,
                    renderAllRows: true,
                    className:'htJustify',
                    licenseKey: 'non-commercial-and-evaluation',                
                    customBorders: true,
                    multiColumnSorting: true,
                    filters: true,
                    allowInsertColumn: false,
                    copyPaste: false,
                    search: true,
                    editor: 'text',
                    hiddenColumns: {
                        // specify columns hidden by default
                        columns: headers.map((h,i)=>h.substring(0,3)=='id_' ? i : null).filter(k=>k!=null)
                    },
                });
                d.hot.addHook('afterChange', (changes,s) => {
                    changes?.forEach(([r, p, oldValue, newValue]) => {
                        if(p=='terminaison'){
                            //mise à jour de l'item
                            me.api.update('gen_terminaisons',d.data[r]['id_trm'],{'lib':newValue}).then(
                                rs=>{
                                    console.log(rs);
                                }   
                            ).catch (
                                error=>console.log(error)
                            );            
                        }
                    });    
                  });
    
        }
        function changeTab(e,d){
            d.tab.show();
            me.tgtContent.selectAll('.nav-link')
              .attr('class',(n,i)=>{
                  let c = n.t==d.t ? "nav-link active text-bg-danger" : 
                    n.data.length ? 'nav-link text-bg-secondary' : 'nav-link text-bg-secondarydark';
                  return c; 
                });
            if(d.hot){
              d.hot.refreshDimensions();
              d.hot.updateSettings({ data: d.data } )
            }else
                showConjugaisons(d);            
            //ajoute le paramètre à l'url
            me.appUrl.change('conjugaisonTab',d.t);
          }
  

        this.init();
    }
}