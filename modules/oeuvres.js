//import { JSONEditor } from '../node_modules/vanilla-jsoneditor/index.js'
import oDico from '../modules/dico.js';
import jscrudapi from '../node_modules/js-crud-api/index.js';

class oeuvres {
    constructor(params) {
        var me = this;
        this.tgtMenu = params.tgtMenu
        this.tgtList = params.tgtList
        this.tgtContent = params.tgtContent
        this.appUrl = params.appUrl ? params.appUrl : false; 
        this.apiUrl = params.apiUrl ? params.apiUrl : 'api.php';
        this.api = jscrudapi(this.apiUrl);
        this.editor;
        this.curDico;
        this.oeuvres;
        this.dicos;
        this.init = function () {
            getOeuvres();
        }
        function getOeuvres(){
            me.api.list('gen_oeuvres').then(
                result=>{
                    me.oeuvres = result.records;
                    d3.select(me.tgtMenu).selectAll('li').data(me.oeuvres).enter().append('li')
                        .append('button')
                        .attr('type', "button")
                        .attr('class',"dropdown-item")
                        .attr('id',d=>d.id_oeu)
                        .on('click',me.showOeuvre)
                        .html(d=>d.lib);
                    if(me.appUrl.params.has('id_oeu'))me.showOeuvre(null,null,me.appUrl.params.get('id_oeu'));
                }
            ).catch (
                error=>console.log(error)
            );        
        }
        this.showOeuvre = function (e,oeu,id){
            if(id)oeu=me.oeuvres.filter(r=>r.id_oeu==id)[0];
            else me.appUrl.params=false;
            me.appUrl.change('id_oeu',oeu.id_oeu);
            d3.select(me.tgtContent).selectAll('div').remove();
            d3.select(me.tgtList).selectAll('h1').data([oeu])
            .join(
                enter => enter.append('h1').html(d=>d.lib),
                update => update.html(d=>d.lib)
              );
            showDicos(oeu)
        }
        function showDicos(oeu){
            //récupère les dicos de l'oeuvre
            me.api.list('gen_oeuvres_dicos_utis',{filter:'id_oeu,eq,'+oeu.id_oeu}).then(
                result=>{
                    let ids=[]; 
                    result.records.forEach(d => {
                        if(!ids.includes(d.id_dico))ids.push(d.id_dico);
                    });
                    me.api.read('gen_dicos',ids).then(
                        result=>{
                            me.dicos=result;
                            /*
                            if(!me.editor){
                                let edt = d3.select(me.tgtContent).append('div').attr('id','editDico').attr('class','editJSON');
                                me.editor = new JSONEditor({
                                    target: edt.node(),
                                })
                            }
                            me.editor.set({json:dicos})
                            */
                            d3.select(me.tgtList).selectAll('.gDicos').remove();
                            let gDicos = d3.group(me.dicos, d => d.general);
                            d3.select(me.tgtList).selectAll('.gDicos')
                                .data(Array.from(gDicos))
                                .join(
                                    enter => {
                                        let div = enter.append('div')
                                        .attr('id',d=>'dicos'+d[0]?'Gen':'Oeu').attr('class','gDicos')
                                        div.append('h3').html(d=>d[0] ? 'Dicos généraux' : 'Dicos oeuvres')
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
            if(me.appUrl.params.has('id_dico'))showDico(null,null,me.appUrl.params.get('id_dico'));
          
        }
        function showDico(e,dico,id){
            if(id)dico=me.dicos.filter(r=>r.id_dico==id)[0];
            me.appUrl.change('id_dico',dico.id_dico);
            me.curDico=new oDico.dico({
                    'oeuvre':me,
                    'dico':dico,
                    'api':me.api,
                    'tgtContent':me.tgtContent,
                    'appUrl':me.appUrl
                });                    
        }

        this.searchClass = function(t,q){
            let rs=[], r, f;
            //création des requêtes pour chaque dictionnaire de concept
            me.dicos.forEach(d=>{
                if(d.type==t.type){
                    f = {filter:['id_dico,eq,'+d.id_dico]}
                    q.forEach(i=>f.filter.push(i));
                    r = me.api.syncList(t.t,f);
                    r.records.forEach(d=>rs.push(d));
                }
            }); 
            return rs;
           
        }
        this.init();
    }
}
export default { oeuvres };