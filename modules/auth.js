import jscrudapi from '../node_modules/js-crud-api/index.js';
export class auth {
    constructor(params) {
        var me = this;
        this.modal;
        this.m;
        this.navbar = params.navbar ? params.navbar : 'navbarMain';
        this.apiOmk = params.apiOmk ? params.apiOmk : false; 
        this.apiUrl = params.apiUrl ? params.apiUrl : 'api.php'; 
        this.apiReadUrl = params.apiReadUrl ? params.apiReadUrl : 'apiRead.php'; 
        this.api;
        this.mail = params.mail ? params.mail : false;
        this.ident = params.ident ? params.ident : false;
        this.key = params.key ? params.key : false;
        this.userAdmin=false;
        this.user=false;
        var iconIn='<i class="fas fa-sign-in-alt"></i>', 
            iconOut='<i class="fa-solid fa-right-from-bracket"></i>',
            btnLogin, nameLogin, alertAuth, alertMail, alertServer, alertUnknown;
        
        this.userAllowed = function (idDico, dicosUti) {
            return dicosUti.filter(d=>d.id_dico==idDico && d.uti_id == me.user.id).length;
        }
        
        this.init = function () {
            //création des éléments html
            let htmlNavBar = `<div class="btn-group">
                    <div id="userLogin" class="me-2">Anonymous</div>                                        
                    <button id="btnAddUser" style="visibility:hidden;" title="Add user" class="btn btn-outline-danger" ><i class="fa-solid fa-user-plus"></i></button>
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
                        <span class="input-group-text" id="serverIcon"><i class="fa-solid fa-server"></i></span>
                        <input id="authServer" type="text" class="form-control" placeholder="Server" aria-label="Email" aria-describedby="serverIcon">
                        </div>

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

                        <div class="collapse" id="alertServer">
                            <div  class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <div id='errorMessage' class='mx-1'>
                                Server does not exist !
                                </div>
                            </div>
                        </div>

                        <div class="collapse" id="alertUnknown">
                            <div  class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <div id='errorMessage' class='mx-1'>
                                This user is unknown.
                                Please contact the administrator.                                
                                </div>
                            </div>
                        </div>
                        

                    </div>                          
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button id='btnCheck' style="visibility:visible;" type="button" class="btn btn-primary">Check</button>
                        <button id='btnSaveUser' style="visibility:hidden;"  type="button" class="btn btn-danger">Save</button>
                    </div>
                </div>
                </div>
            `;
            me.m = d3.select('body').append('div')
                .attr('id','modalAuth').attr('class','modal').attr('tabindex',-1);
            me.m.html(htmlModal);
            me.modal = new bootstrap.Modal('#modalAuth');
            alertAuth = new bootstrap.Collapse('#alertAuth', {toggle: false});
            alertMail = new bootstrap.Collapse('#alertMail', {toggle: false});
            alertServer = new bootstrap.Collapse('#alertServer', {toggle: false});
            alertUnknown = new bootstrap.Collapse('#alertUnknown', {toggle: false});
            alertAuth.hide();
            alertMail.hide();
            alertServer.hide();
            alertUnknown.hide();
            //gestion des événements
            me.m.selectAll("input").on('change',e=>{
                alertAuth.hide();
                alertMail.hide();                    
                alertServer.hide();
                alertUnknown.hide();                    
            });                                                                                    
            nameLogin = me.navbar.select("#userLogin");
            btnLogin = me.navbar.select("#btnLogin");
            btnLogin.on('click',e=>{
                if(btnLogin.attr('class')=='btn btn-outline-success')me.modal.show();
                else{
                    me.mail="";
                    me.ident="";
                    me.key="";
                    me.apiOmk="";
                    me.user=false;                    
                    nameLogin.html('Anonymous');
                    btnLogin.attr('class','btn btn-outline-success');
                }
            });                                                                                    
            me.m.select("#btnCheck").on('click',e=>{
                getUrlAuth();
                me.getUser(null);
            });                                                                                    
            me.navbar.select("#btnAddUser").on('click',e=>{
                showAddUser();
            });                                                                                    
        }
        function showAddUser(){
            me.m.select("#btnCheck").style("visibility","hidden");
            me.m.select("#btnSaveUser").style("visibility","visible")
                .on('click',addUser);            
            me.modal.show();
        }
        function addUser(){
            let url = me.m.select("#authServer").node().value,
                mail = me.m.select("#authMail").node().value,
                ident = me.m.select("#authIdent").node().value,
                key = me.m.select("#authPwd").node().value;
            url += url.slice(-1)=='/' ? "" : "/";
            url+='users?email='+mail+'&key_identity='+ident+'&key_credential='+key;                
            d3.json(url).then((data) => {
                if(data.length==0)alertMail.show();
                else {
                    let user = data[0];
                    me.api.create('flux_uti', {
                        'login':user['o:name'],
                        'flux':user["@id"],
                        'flux_id':user["o:id"],
                        'flux_api_ident':ident,
                        'email':user["o:email"],
                        'role':user["o:role"]
                    }).then(id=>{
                        me.m.select("#btnCheck").style("visibility","visible");
                        me.m.select("#btnSaveUser").style("visibility","hidden");            
                        me.modal.hide();                                    
                    }).catch (error=>console.log(error));
                }
            });
        }

        this.getUser = function (cb){
            if(!me.mail || !me.ident || !me.key){
                me.api = jscrudapi(me.apiReadUrl);
                cb();                
                return;
            };
            //vérifie la connexion
            d3.json(getUrlAuth()).then((data) => {
                if(data.length==0)alertMail.show();
                else {
                    me.api = jscrudapi(me.apiUrl,{headers:{'X-API-Key':me.ident,"Content-Type":"application/json"}})
                    me.user = data[0];
                    //récupère l'utilisateur omeka dans la base generateur
                    me.api.list('flux_uti',{filter:['login,eq,'+me.user['o:name'],'flux,eq,'+me.user["@id"]]}).then(
                        rs=>{
                            if(rs.records.length){
                                me.userAdmin = me.user["o:role"] == 'global_admin';            
                                nameLogin.html(me.user['o:name']);
                                btnLogin.attr('class','btn btn-danger').html(iconOut);                        
                                me.user.id=rs.records[0].uti_id;
                                me.modal.hide();
                                if(me.userAdmin){
                                    me.navbar.select('#btnAddUser').style('visibility','visible');
                                }
                                cb();
                            }                         
                        }
                    )
                    .catch((e) => {
                        switch (e.code) {
                            case 1012:
                                alertUnknown.show();                                
                                break;                        
                            default:
                                alertAuth.show();
                                break;
                        }
                        me.user = false;                                                                     
                    }); 
                }
            }).catch(error=>{
                console.log(error);
                me.mail="";
                me.ident="";
                me.key="";
                me.apiOmk="";
                me.user=false;                    
            });                               
        }

        function getUrlAuth(){

            let url = me.apiOmk ? me.apiOmk : me.m.select("#authServer").node().value;
            me.mail = me.mail ? me.mail : me.m.select("#authMail").node().value;
            me.ident = me.ident ? me.ident : me.m.select("#authIdent").node().value;
            me.key = me.key ? me.key : me.m.select("#authPwd").node().value;
            url += url.slice(-1)=='/' ? "" : "/";
            url+='users?email=';
            return url+me.mail+'&key_identity='+me.ident+'&key_credential='+me.key;                
        }
        this.init();
    }
}
