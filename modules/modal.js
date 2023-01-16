export class modal {
    constructor(params={}) {
        var me = this;
        this.titre = params.titre ? params.titre : "Message";
        this.body = params.body ? params.body : "";
        this.boutons = params.boutons ? params.boutons : [{'name':"Close"}];
        var m, mBody, mFooter;
        this.init = function () {
            //ajoute la modal pour les messages
            let html = `
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">${me.titre}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    ${me.body}                    
                    </div>                          
                    <div class="modal-footer">
                    </div>
                </div>
                </div>
            `;
            d3.select('#modalGenerateur').remove();
            let sm = d3.select('body').append('div')
                .attr('id','modalGenerateur').attr('class','modal').attr('tabindex',-1);
            sm.html(html);
            m = new bootstrap.Modal('#modalGenerateur');
            mBody = sm.select('.modal-body');
            mFooter = sm.select('.modal-footer');
            me.setBoutons();
        }
        this.setBoutons = function(boutons=false){
            if(boutons)me.boutons=boutons;
            mFooter.selectAll('button').remove();
            me.boutons.forEach(b=>{
                switch (b.name) {
                    case 'Close':
                        mFooter.append('button').attr('type',"button").attr('class',"btn btn-secondary")
                            .attr('data-bs-dismiss',"modal").html(b.name);
                        break;                
                    default:
                        mFooter.append('button').attr('type',"button").attr('class',"btn "+b.class)
                            .on('click',b.fct).html(b.name);
                        break;
                }
            })
        }
        this.add = function(p){
            let s=d3.select('#'+p);
            //ajoute la modal si inexistant
            if(s.empty()){
                s = d3.select('body').append('div')
                    .attr('id',p).attr('class','modal').attr('tabindex',-1);
                s.html(eval(p));
            }
            return {'m':new bootstrap.Modal('#'+p),'s':s};
        }
        this.setBody = function(html){
            mBody.html(html);
        }
        this.show = function(){
            m.show();
        }
        this.hide = function(){
            m.hide();
        }

        this.init();
    }
}
//ajoute la modal pour l'ajout d'oeuvre'
export let modalAddOeuvre = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new work</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">                    
            <div class="input-group mb-3">
                <span class="input-group-text" id="oeuNom">Name</span>
                <input id="inpOeuNom" type="text" class="form-control" placeholder="Name" aria-label="name" aria-describedby="oeuNom">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="choixLangue">Lang</span>
                <div aria-describedby="choixLangue" class="m-2">
                <div class="form-check form-check-inline">
                    <input checked class="form-check-input" type="radio" name="oeuLangue" id="oeuLangueRadioFr" value="français">
                    <label class="form-check-label" for="oeuLangueRadioFr">French</label>
                </div>                        
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="oeuLangue" id="oeuLangueRadioEn" value="anglais">
                    <label class="form-check-label" for="oeuLangueRadioEn">English</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="oeuLangue" id="oeuLangueRadioEs" value="espagnol">
                    <label class="form-check-label" for="oeuLangueRadioEs">Espagnol</label>
                </div>
            </div>

            <div class="input-group my-3">
                <span class="input-group-text" id="choixLicence">Licence&nbsp;<i class="fa-brands fa-creative-commons"></i></span>
                <div aria-describedby="choixLicence" class="m-2">
                    <div class="form-check form-check-inline">
                        <input checked class="form-check-input" type="checkbox" name="oeuLicence" id="oeuLicenceCC0" value="CC0">
                        <label class="form-check-label" for="oeuLicenceCC0"><i class="fa-brands fa-creative-commons-zero"></i></label>
                    </div>                        
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="oeuLicence" id="oeuLicenceCCBY" value="BY">
                        <label class="form-check-label" for="oeuLicenceCCBY">
                        <i class="fa-brands fa-creative-commons-by"></i>
                        </label>
                    </div>                        
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="oeuLicence" id="oeuLicenceCCSA" value="SA">
                        <label class="form-check-label" for="oeuLicenceCCSA">
                        <i class="fa-brands fa-creative-commons-sa"></i>
                        </label>
                    </div>                        
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="oeuLicence" id="oeuLicenceCCND" value="ND">
                        <label class="form-check-label" for="oeuLicenceCCND">
                        <i class="fa-brands fa-creative-commons-nd"></i>
                        </label>
                    </div>                        
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="oeuLicence" id="oeuLicenceCCNC" value="NC">
                        <label class="form-check-label" for="oeuLicenceCCNC">
                        <i class="fa-brands fa-creative-commons-nc-eu"></i>
                        </label>
                    </div>                        
                </div>

                <div class="form-text">
                    <a href="https://creativecommons.org/licenses/?lang=en">
                    About The Licenses <i class="fa-brands fa-creative-commons"></i>
                    </a>
                </div>
            </div>
            
            
        </div>                          
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button id='btnaddNewOeuvre' type="button" class="btn btn-primary">Add new</button>

        </div>
    </div>
    </div>
`;
//ajoute la modal pour l'ajout d'un concept dans un dico
export let modalAddDicoconcepts = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new concept</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">                    
            <div class="input-group mb-3">
                <span class="input-group-text" id="itemLib">Label</span>
                <input keycol="lib" type="text" class="form-control inptValue" placeholder="label" aria-label="name" aria-describedby="itemLib">
            </div>            
            <div class="input-group mb-3">
                <span class="input-group-text" id="itemType">Type</span>
                <input keycol="type" type="text" class="form-control inptValue" placeholder="Type" aria-label="name" aria-describedby="itemType">
            </div>            
        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;
//ajoute la modal pour l'ajout d'un syntagme dans un dico
export let modalAddDicosyntagmes = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new syntagme</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">                    
            <div class="input-group mb-3">
                <span class="input-group-text" id="synNum">Num</span>
                <input keycol="num" type="text" class="form-control inptValue" placeholder="Num" aria-label="name" aria-describedby="synNum">
            </div>            
            <div class="input-group mb-3">
                <span class="input-group-text" id="synLib">Type</span>
                <input keycol="lib" type="text" class="form-control inptValue" placeholder="Type" aria-label="name" aria-describedby="synLib">
            </div>            
        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;

//modal pour l'ajout d'un generateur dans un concept
export let modalAddConceptGenerators = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new generator</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form-floating">
                <textarea class="form-control inptValue" keycol='valeur' placeholder="Put the value of generator" id="genValue" style="height: 100px"></textarea>
                <label for="genValue">Value for the generator</label>
            </div>        
        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;
//modal pour l'ajout d'un adjectif dans un concept
export let modalAddConceptAdjectives = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new adjective</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="input-group mb-3">
                <span class="input-group-text" id="adjChoixElision">Elision</span>
                <div aria-describedby="adjChoixElision" class="m-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input inptValue" keycol='elision' type="checkbox" name="adjElision" id="adjElisionY" >
                    <label class="form-check-label" for="adjElisionY">Yes</label>
                </div>                        
            </div>


            <div class="input-group my-3">
                <span class="input-group-text" id="adjPrefix">Prefix</span>
                <input type="text" class="form-control inptValue" keycol='prefix' placeholder="prefix" aria-label="name" aria-describedby="adjPrefix">
            </div>            
            <div class="input-group mb-3">
                <span class="input-group-text" id="adjf_s"><i class="fa-solid fa-venus"></i></span>
                <input type="text" class="form-control inptValue" keycol='f_s' aria-label="name" aria-describedby="adjf_s">
            </div>            
            <div class="input-group mb-3">
                <span class="input-group-text" id="adjf_p"><i class="fa-solid fa-venus-double"></i></span>
                <input type="text" class="form-control inptValue" keycol='f_p' aria-label="name" aria-describedby="adjf_p">
            </div>            
            <div class="input-group mb-3">
                <span class="input-group-text" id="adjm_s"><i class="fa-solid fa-mars"></i></span>
                <input type="text" class="form-control inptValue" keycol='m_s' aria-label="name" aria-describedby="adjm_s">
            </div>            
            <div class="input-group mb-3">
                <span class="input-group-text" id="adjm_p"><i class="fa-solid fa-mars-double"></i></span>
                <input type="text" class="form-control inptValue" keycol='m_p' aria-label="name" aria-describedby="adjm_p">
            </div>            
        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;
//modal pour l'ajout d'un adjectif dans un concept
export let modalAddConceptNouns = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new noun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="input-group mb-3">
                <span class="input-group-text" id="nounChoixElision">Elision</span>
                <div aria-describedby="nounChoixElision" class="m-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input inptValue" keycol='elision' type="checkbox" name="nounElision" id="nounElisionY" >
                    <label class="form-check-label" for="nounElisionY">Yes</label>
                </div>                        
            </div>


            <div class="input-group my-3">
                <span class="input-group-text" id="nounPrefix">Prefix</span>
                <input type="text" class="form-control inptValue" keycol='prefix' placeholder="prefix" aria-label="name" aria-describedby="nounPrefix">
            </div>
            
            
            <div class="input-group my-3">
                <span class="input-group-text" id="choixGenre">Gender</span>
                <div aria-describedby="choixGenre" class="m-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input inptValue" keycol='genre' type="radio" name="nounGenre" id="nounGenreF" value="2">
                        <label class="form-check-label" for="nounGenreF"><i class="fa-solid fa-venus"></i></label>
                    </div>                        
                    <div class="form-check form-check-inline">
                        <input class="form-check-input inptValue" keycol='genre' type="radio" name="nounGenre" id="nounGenreH" value="1">
                        <label class="form-check-label" for="nounGenreH"><i class="fa-solid fa-mars"></i></label>
                    </div>                        
                </div>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text" id="noun_s">Singular</span>
                <input type="text" class="form-control inptValue" keycol='s' aria-label="name" aria-describedby="noun_s">
            </div>            
            <div class="input-group mb-3">
                <span class="input-group-text" id="noun_p">Plural</span>
                <input type="text" class="form-control inptValue" keycol='p' aria-label="name" aria-describedby="noun_p">
            </div>            
        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;
//modal pour l'ajout d'un adjectif dans un concept
export let modalAddConceptSyntagms = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new syntagm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">


            <div class="input-group mb-3">
                <span class="input-group-text" id="syntagmNum">Num</span>
                <input type="text" class="form-control inptValue" keycol='num' placeholder="num" aria-label="num" aria-describedby="syntagmNum">
            </div>
            
            <div class="input-group mb-3">
                <span class="input-group-text" id="syntagmLib">Label</span>
                <input type="text" class="form-control inptValue" keycol='lib' placeholder="label" aria-label="lib" aria-describedby="syntagmLib">
            </div>

        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;
//modal pour l'ajout d'un verbe dans un concept
export let modalAddConceptVerbs = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new verb</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="input-group mb-3">
                <span class="input-group-text" id="verbChoixElision">Elision</span>
                <div aria-describedby="verbChoixElision" class="m-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input inptValue" keycol='elision' type="checkbox" name="verbElision" id="verbElisionY" >
                    <label class="form-check-label" for="verbElisionY">Yes</label>
                </div>                        
            </div>


            <div class="input-group my-3">
                <span class="input-group-text" id="verbPrefix">Prefix</span>
                <input type="text" class="form-control inptValue" keycol='prefix' placeholder="prefix" aria-label="name" aria-describedby="verbPrefix">
            </div>
            <div class="input-group mb-3">
                <label class="input-group-text" for="verbConj">Conjugation</label>
                <select class="form-select inptValue" keycol='id_conj' id="verbConj">
                </select>
            </div>
        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;
//modal pour l'ajout d'un verbe dans un concept
export let modalImportDicoconcepts = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Import concepts</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="frmImportDicoconcepts">
                <div class="mb-3">
                    <label for="formFile" class="form-label">Select CSV file</label>
                    <input class="form-control" type="file" id="importDicoconceptsFile" accept=".csv">
                </div>
            </form>
        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;
