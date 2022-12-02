import oConcept from '../modules/concept.js';

class dico {
    constructor(params) {
        var me = this;
        this.oeuvre = params.oeuvre ? params.oeuvre : false;
        this.dico = params.dico ? params.dico : false;
        this.api = params.api ? params.api : false;
        this.tgtContent = params.tgtContent ? params.tgtContent : false;
        this.appUrl = params.appUrl; 
        this.hot;
        this.concepts;
        var mainSlt, dicoHot, cptContent;
        this.init = function () {
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
            let row = mainSlt.append('div').attr('class','d-flex h-100');
            dicoHot = row.append('div').attr('class','w-auto h-100')
                .append('div').attr('class','clearfix')
                .attr('id','dicoHot');
            cptContent = row.append('div').attr('class','flex-fill h-100').attr('id','dicoCptContent');            

            me.api.list('gen_concepts',{filter:'id_dico,eq,'+me.dico.id_dico}).then(
                result=>{
                    me.concepts = result.records
                    //me.hot.loadData(me.concepts);
                    //création de la table
                    me.hot = new Handsontable(dicoHot.node(), {
                        rowHeaders: true,
                        colHeaders: true,
                        rowHeaders: true,
                        data:me.concepts,
                        colHeaders: ['id_concept','id_dico','lib','type'],                
                        height: '100%',
                        width: '300',
                        licenseKey: 'non-commercial-and-evaluation',
                        customBorders: true,
                        dropdownMenu: true,
                        multiColumnSorting: true,
                        filters: true,
                        hiddenColumns: {
                            // specify columns hidden by default
                            columns: [0, 1]
                        }
                    });
                    me.hot.addHook('afterSelection', (r, c) => {
                        showConcept(me.hot.getDataAtRow(r));
                    })


                    if(me.appUrl.params.has('id_concept'))showConcept(null,me.appUrl.params.get('id_concept'));
                }
            ).catch (
                error=>console.log(error)
            );
            //me.getItems(me.dico);
        }

        this.getItems = function (dico){
            me.dico=dico;
            //me.hot.updateData([]);
            me.api.list('gen_concepts',{filter:'id_dico,eq,'+me.dico.id_dico}).then(
                result=>{
                    me.concepts = result.records
                    me.hot.loadData(me.concepts);
                    if(me.appUrl.params.has('id_concept'))showConcept(null,me.appUrl.params.get('id_concept'));
                }
            ).catch (
                error=>console.log(error)
            );

        }

        function showConcept(d,id){
            if(!id)id=d[0];//le grid ne renvoie pas des tableaux associatifs
            d=me.concepts.filter(r=>r.id_concept==id)[0];
            me.appUrl.change('id_concept',d.id_concept);

            let cpt=new oConcept.concept({
                    'data':d,
                    'oeuvre':me.oeuvre,
                    'api':me.api,
                    'tgtContent':cptContent,
                    'appUrl':me.appUrl
                });                
        }


        this.init();
    
    }
}
export default { dico };