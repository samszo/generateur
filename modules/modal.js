export class modal {
    constructor(params={}) {
        var me = this;
        this.titre = params.titre ? params.titre : "Message";
        this.body = params.body ? params.body : "";
        this.boutons = params.boutons ? params.boutons : [{'name':"Close"}];
        this.mBody="";
        var m, mFooter, mTitle;
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
            this.mBody = sm.select('.modal-body');
            mFooter = sm.select('.modal-footer');
            mTitle = sm.select('.modal-title');
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
        this.add = function(p,size=""){
            //suprime la modal si elle existe pour éviter les objets en mémoire
            d3.select('#'+p).remove();
            //ajoute la modal si inexistant
            let s = d3.select('body').append('div')
                .attr('id',p).attr('class','modal '+size).attr('tabindex',-1);
            s.html(eval(p));
            return {'m':new bootstrap.Modal('#'+p),'s':s};
        }
        this.setBody = function(html){
            this.mBody.html(html);
        }
        this.setTitle = function(html){
            mTitle.html(html);
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
        <div class="modal-body bg-white text-dark">                    
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

//modal pour l'ajout d'un dictionnaire
export let modalAddDico = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new dictionnary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="input-group mb-3">
                <span class="input-group-text" id="dicoLib">Label</span>
                <input id="inptDicoLib" keycol="lib" type="text" class="form-control inptValue" placeholder="label" aria-label="name" aria-describedby="dicoLib">
            </div>            
        </div>                          
        <div class="modal-footer">
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

//modal pour le paramètrage des générateurs
export let modalParamsGen = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Parameters for generator</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="border border-black mt-3 px-2">
                <h6 class="modal-title">Determinant for conjugation</h6>
        <!--
        Position 0 : type de négation
        Position 1 : temps verbal
        Position 2 : pronoms sujets définis
        Positions 3 ET 4 : pronoms compléments
        Position 5 : ordre des pronoms sujets
        Position 6 : pronoms indéfinis
        Position 7 : Place du sujet dans la chaîne grammaticale
        -->                
                <div class="row">
                    <div class="col">         
                        <div class="input-group mb-1">                  
                            <label class="input-group-text" for="conjNeg">Négation</label>
                            <select class="form-select" id="conjNeg" >
                            </select>
                        </div>            
                        <div class="input-group mb-1">
                            <label class="input-group-text" for="conjTemps">Temps</label>
                            <select class="form-select" id="conjTemps">
                                <option value="1">indicatif présent</option>
                                <option value="2">indicatif imparfait</option>
                                <option value="3">passé simple</option>
                                <option value="4">futur simple</option>
                                <option value="5">conditionnel présent</option>
                                <option value="6">subjonctif présent</option>
                                <option value="7">impératif</option>
                                <option value="8">participe présent</option>
                                <option value="9">infinitif</option>
                            </select>
                        </div>            
                        <div class="input-group mb-1">
                            <label class="input-group-text" for="conjSujet">Sujet</label>
                            <select class="form-select" id="conjSujet">
                            </select>
                        </div>       
                        <div class="input-group mb-1">
                            <label class="input-group-text" for="conjSujetComp">Pronom complément</label>
                            <select class="form-select" id="conjSujetComp">
                            </select>
                        </div>       
                        
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="choixOrdreProSuj">Ordre des pronoms sujets</span>
                            <div aria-describedby="choixOrdreProSuj" class="form-control">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ordreProSuj" id="ordreProSujInv" value="1">
                                    <label class="form-check-label text-dark bg-white" for="ordreProSujInv">inverse</label>
                                </div>                        
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ordreProSuj" id="ordreProSujNormal" checked value="0">
                                    <label class="form-check-label text-dark bg-white" for="ordreProSujInv" value="1">normal</label>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-1">
                            <label class="input-group-text" for="conjSujetInd">Pronom indéfini</label>
                            <select class="form-select" id="conjSujetInd">
                            </select>
                        </div>       
                    </div>
                </div>            
                <div class="row mb-1">
                    <div class="col">
                        <button type="button" id="btnGenereDetConj" class="btn btn-sm btn-danger">
                        Genére déterminant
                        </button>
                    </div>
                    <div class="col">
                        <div id="detConjResult" class="form-control" >
                    </div>
                </div>                
            </div>



        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;


//modal pour l'ajout d'un term dans un concept
export let modalAddConceptTerms = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new term</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="creaTermResult" class="row"></div>
            <input type="hidden" id="termId" class="inptValue" keycol='id' value="" />
            <input type="hidden" class="inptValue" keycol='accord_id' value="" />
            <!-- input pour le titre du terme 
            <div class="input-group mb-1">
                <span class="input-group-text" id="lblTitle">Title</span>
                <input type="text" class="form-control inptValue" keycol='title' placeholder="title" aria-label="name" aria-describedby="lblTitle">
            </div>            
            -->
            <div class="input-group mb-1">
                <span class="input-group-text" id="lblDesc">Description</span>
                <input type="text" class="form-control inptValue" keycol='description' placeholder="description" aria-label="name" aria-describedby="lblDesc">
            </div>            
            <div class="input-group mb-1">
                <label class="input-group-text" for="termType">Type</label>
                <select class="form-select inptValue" keycol='type' id="termType">
                </select>
            </div>            
            <div class="input-group mb-1">
                <span class="input-group-text" id="lblhasPrefix">Prefix</span>
                <input type="text" class="form-control inptValue" keycol='prefix' placeholder="prefix" aria-label="name" aria-describedby="lblhasPrefix">
            </div>
            <div class="row">
                <div class="col">                
                    <div class="input-group">
                        <span class="input-group-text" id="choixGenre">Gender</span>
                        <div aria-describedby="choixGenre" class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input inptValue" keycol='genre' type="radio" name="nounGenre" id="nounGenreF" value="feminin">
                                <label class="form-check-label text-dark bg-white" for="nounGenreF"><i class="fa-solid fa-venus"></i></label>
                            </div>                        
                            <div class="form-check form-check-inline">
                                <input class="form-check-input inptValue" keycol='genre' type="radio" name="nounGenre" id="nounGenreH" value="masculin">
                                <label class="form-check-label text-dark bg-white" for="nounGenreH"><i class="fa-solid fa-mars"></i></label>
                            </div>
                            <!--                        
                            <div class="form-check form-check-inline">
                                <input class="form-check-input inptValue" keycol='genre' type="radio" name="nounGenre" id="nounGenreN" value="neutre">
                                <label class="form-check-label text-dark bg-white" for="nounGenreN"><i class="fa-solid fa-neuter"></i></label>
                            </div>
                            -->                        
                        </div>
                    </div>
                </div>
                <div class="col">         
                    <div class="input-group">
                        <label class="input-group-text" for="verbConj">Conjugation</label>
                        <select class="form-select inptValue" keycol='hasConjugaison' id="verbConj">
                            <option selected>Choose...</option>
                        </select>
                    </div>            
                </div>
            </div>            
            
            <div class="border border-black mt-3 px-2">
                <h6 class="modal-title">Generator</h6>
                <div class="btn-toolbar mb-1" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group me-2" role="group" aria-label="generation group">
                        <button type="button" id="btnGenereInModal" class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-shuffle"></i>
                        </button>
                    </div>
                    <div class="btn-group me-2" role="group" aria-label="generation parameters">
                        <button type="button" id="btnGenereParams" class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-sliders"></i>
                        </button>
                    </div>
                    
                    <div class="btn-group" role="group" aria-label="help group">
                        <button type="button" id="btnGenereHelp" class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-question"></i>
                        </button>
                    </div>
                </div>
                <div class="input-group mb-1">
                    <span class="input-group-text" id="lblGen">Text</span>
                    <textarea class="form-control inptValue" keycol='gen' placeholder="Put the value of generator" id="genValue" style="height: 100px"></textarea>
                </div>   
                <div class="input-group mb-1">
                    <!--
                    <span class="input-group-text" >Result</span>
                    <div class="form-control" id="genResultInModalOld" style="height: 100px;text-align:left;"></div>
                    -->
                    <div id="genResultInModal" style="color: black;"></span>
                </div>   
            </div>


            <div class="border border-black mt-3 px-2">
                <h6 class="modal-title">Agreements</h6>
                <div class="input-group mb-1">
                    <label class="input-group-text" id="choixElision">Elision</label>
                    <div aria-describedby="choixElision" class="form-control">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" keycol='hasElision' id="rElision1" value="1">
                            <label class="form-check-label text-dark bg-white" for="rElision1">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" keycol='hasElision' id="rElision0" value="0">
                            <label class="form-check-label text-dark bg-white" for="rElision0">No</label>
                        </div>                
                    </div>                
                </div>
                <div class="row">
                <div class="col">                
                <div class="input-group mb-1">
                    <span class="input-group-text" id="adjf_s"><i class="fa-solid fa-venus"></i></span>
                    <input type="text" class="form-control inptValue" keycol='accordFemSing' aria-label="name" aria-describedby="adjf_s">
                </div>            
                <div class="input-group mb-1">
                    <span class="input-group-text" id="adjf_p"><i class="fa-solid fa-venus-double"></i></span>
                    <input type="text" class="form-control inptValue" keycol='accordFemPlu' aria-label="name" aria-describedby="adjf_p">
                </div>            
                </div>
                <div class="col">
                <div class="input-group mb-1">
                    <span class="input-group-text" id="adjm_s"><i class="fa-solid fa-mars"></i></span>
                    <input type="text" class="form-control inptValue" keycol='accordMasSing' aria-label="name" aria-describedby="adjm_s">
                </div>            
                <div class="input-group mb-1">
                    <span class="input-group-text" id="adjm_p"><i class="fa-solid fa-mars-double"></i></span>
                    <input type="text" class="form-control inptValue" keycol='accordMasPlu' aria-label="name" aria-describedby="adjm_p">
                </div>           
                </div> 
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
//modal pour l'ajout d'un syntagme dans un concept
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
            <div id="resultImport" class="container-fluid" style="height: 300px;overflow-y: scroll;">
            </div>  
        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;

//modal pour l'ajout d'une uri dans un concept
export let modalAddConceptUris = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new uri</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">


            <div class="input-group mb-3">
                <span class="input-group-text" id="uriLib">Lib</span>
                <input type="text" class="form-control inptValue" keycol='lib' placeholder="lib" aria-label="lib" aria-describedby="uriLib">
            </div>
            
            <div class="input-group mb-3">
                <span class="input-group-text" id="uriUri">Uri</span>
                <input type="text" class="form-control inptValue" keycol='uri' placeholder="uri" aria-label="uri" aria-describedby="uriUri">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="uriFormat">Format</span>
                <input type="text" class="form-control inptValue" keycol='format' placeholder="format" aria-label="format" aria-describedby="uriFormat">
            </div>

        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;

//modal pour l'ajout d'une requête sparql uri dans un concept
export let modalAddConceptSparqls = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new uri</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">


            <div class="input-group mb-3">
                <span class="input-group-text" id="sparqlLib">Lib</span>
                <input type="text" class="form-control inptValue" keycol='lib' placeholder="lib" aria-label="lib" aria-describedby="uriLib">
            </div>
            
            <div class="input-group mb-3">
                <span class="input-group-text" id="sparqlQuery">Query</span>
                <textarea rows="10" cols="40" class="form-control inptValue" keycol='query' placeholder="query" aria-label="query" aria-describedby="sparqlQuery">
                </textarea>
            </div>
            
            <div class="input-group mb-3">
                <span class="input-group-text" id="sparqlEndpoint">Endpoint</span>
                <input type="text" class="form-control inptValue" keycol='endpoint' placeholder="endpoint" aria-label="endpoint" aria-describedby="sparqlEndpoint">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="sparqlFormat">Format</span>
                <input type="text" class="form-control inptValue" keycol='format' placeholder="format" aria-label="format" aria-describedby="sparqlFormat">
            </div>

        </div>                          
        <div class="modal-footer">
        </div>
    </div>
    </div>
`;
