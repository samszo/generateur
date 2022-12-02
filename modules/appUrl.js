class appUrl {
    constructor(params) {
        var me = this;
        this.tgtIn = params.tgtIn ? params.tgtIn : false;
        this.tgtBtn = params.tgtBtn ? params.tgtBtn : false;
        this.url = params.url ? params.url : false; 
        this.params = this.url.searchParams
    
        this.init = function () {
            me.tgtIn.value=me.url.search;
            me.tgtBtn.on('click',e=>{
                let u = document.location.href.split("?"),
                    l=u[0]+'?'+me.tgtIn.value;
                window.open(l, "_blank");
            });
        }

        this.change = function (k,v){
            let sp = new URLSearchParams(me.tgtIn.value);
            sp.set(k, v);
            me.tgtIn.value = sp.toString();                                
        }            
        this.init();
    
    }
}
export default { appUrl };