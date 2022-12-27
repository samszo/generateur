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
//ajoute la modal pour l'ajout d'item dans un dico
export let modalAddDicoItem = `
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Adding a new item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">                    
            <div class="input-group mb-3">
                <span class="input-group-text" id="itemLib">Wording</span>
                <input id="inpItemLib" type="text" class="form-control" placeholder="Wording" aria-label="name" aria-describedby="itemLib">
            </div>            
            <div class="input-group mb-3">
                <span class="input-group-text" id="itemType">Type</span>
                <input id="inpItemType" type="text" class="form-control" placeholder="Type" aria-label="name" aria-describedby="itemType">
            </div>            
        </div>                          
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button id='btnAddNewItem' type="button" class="btn btn-primary">Add new</button>
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
            <input class="conceptType" name="conceptType" type="hidden" value="Generators">
            <div class="form-floating">
                <textarea class="form-control inptValue" keycol='valeur' placeholder="Put the value of generator" id="genValue" style="height: 100px"></textarea>
                <label for="genValue">Value for the generator</label>
            </div>        
        </div>                          
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
`;
