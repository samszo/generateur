import jscrudapi from '../node_modules/js-crud-api/index.js';
class auth {
    constructor(params) {
        var me = this;
        this.modal;
        this.m;
        this.navbar = params.navbar ? params.navbar : 'navbarMain';
        this.apiOmk = params.apiOmk;
        this.apiUrl = params.apiUrl ? params.apiUrl : 'api.php'; 
        this.api = jscrudapi(this.apiUrl);
        this.mail = params.mail ? params.mail : false;
        this.ident = params.ident ? params.ident : false;
        this.key = params.key ? params.key : 'navbarMain';
        this.user=false;
        var iconIn='<i class="fas fa-sign-in-alt"></i>', 
            iconOut='<i class="fa-solid fa-right-from-bracket"></i>',
            btnLogin, nameLogin;
        this.init = function () {
            //création des éléments html
            let htmlNavBar = `<div class="btn-group">
                    <div id="userLogin" class="me-2">Anonymous</div>                                        
                    <button id="btnLogin" title="Connexion" class="btn btn-outline-success" >${iconIn}</button>                                            
                </div>`;
            me.navbar.append('li').attr('class',"nav-item ms-2 me-1").html(htmlNavBar);
            let htmlModal = `
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="input-group mb-3">
                        <span class="input-group-text" id="mailIcon"><i class="fa-solid fa-at"></i></span>
                        <input id="authMail" type="text" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="mailIcon">
                        </div>

                        <div class="input-group mb-3">
                        <span class="input-group-text" id="identIcon"><i class="fa-solid fa-fingerprint"></i></i></span>
                        <input id="authIdent" type="password" class="form-control" placeholder="Identity" aria-describedby="identIcon">
                        </div>
                        

                        <div class="input-group mb-3">
                        <span class="input-group-text" id="mdpIcon"><i class="fa-solid fa-key"></i></span>
                        <input id="authPwd" type="password" class="form-control" placeholder="Key" aria-describedby="mdpIcon">
                        </div>

                        <div class="collapse" id="alertAuth">
                            <div  class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <div id='errorMessage' class='mx-1'>
                                identity or credential are wrong !
                                </div>
                            </div>
                        </div>

                        <div class="collapse" id="alertMail">
                            <div  class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <div id='errorMessage' class='mx-1'>
                                The user does not exist !
                                </div>
                            </div>
                        </div>

                    </div>                          
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id='btnCheck' type="button" class="btn btn-primary">Check</button>
                    </div>
                </div>
                </div>
            `;
            me.m = d3.select('body').append('div')
                .attr('id','modalAuth').attr('class','modal').attr('tabindex',-1);
            me.m.html(htmlModal);
            me.modal = new bootstrap.Modal('#modalAuth');
            let alertAuth = new bootstrap.Collapse('#alertAuth', {toggle: false}),
            alertMail = new bootstrap.Collapse('#alertMail', {toggle: false});
            alertAuth.hide();
            alertMail.hide();
            //gestion des événements
            me.m.selectAll("input").on('change',e=>{
                alertAuth.hide();
                alertMail.hide();                    
            });                                                                                    
            nameLogin = me.navbar.select("#userLogin");
            btnLogin = me.navbar.select("#btnLogin");
            btnLogin.on('click',e=>{
                if(btnLogin.attr('class')=='btn btn-outline-success')me.modal.show();
                else{
                    me.mail="";
                    me.ident="";
                    me.key="";
                    me.user=false;
                    nameLogin.html('Anonymous');
                    btnLogin.attr('class','btn btn-outline-success');
                }
            });                                                                                    
            me.m.select("#btnCheck").on('click',e=>{
                getUser();
            });                                                                                    
            if(me.mail && me.ident && me.key)getUser();
        }
        function getUser(){
            //véririfie la connexion
            d3.json(getUrlAuth())
            .then((data) => {
                if(data.length==0)alertMail.show();
                else {
                    me.user = data[0]
                    nameLogin.html(me.user['o:name']);
                    btnLogin.attr('class','btn btn-danger').html(iconOut);                        
                    me.modal.hide();
                    //récupère l'utilisateur omeka dans la base generateur
                    me.api.list('flux_uti',{filter:['login,eq,'+me.user['o:name'],'flux,eq,'+me.user["@id"]]}).then(
                        rs=>{
                            if(rs.length)me.user.id=rs[0].uti_id;
                            else{
                                me.api.create('flux_uti', {
                                    'login':me.user['o:name'],
                                    'flux':me.user["@id"],
                                    'email':me.user["o:email"],
                                    'role':me.user["o:role"]
                                }).then(id=>me.user.id=id)
                                .catch (error=>console.log(error));
                            }                         
                        }
                    )
                    .catch((e) => {
                        alertAuth.show();
                    }); 
                }
            });                               
        }

        function getUrlAuth(){
            let url = me.apiOmk+'users?email=';
            me.mail = me.mail ? me.mail : me.m.select("#authMail").node().value;
            me.ident = me.ident ? me.ident : me.m.select("#authIdent").node().value;
            me.key = me.key ? me.key : me.m.select("#authPwd").node().value;
            return url+me.mail+'&key_identity='+me.ident+'&key_credential='+me.key;                
        }
        this.init();
    }
}
export default { auth };