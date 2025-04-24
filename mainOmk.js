import {appUrl} from './modules/appUrl.js';
import {auth} from './modules/authOmk.js';
let a = new auth({'navbar':d3.select('#navbarMain'),
        mail:'samuel.szoniecky@univ-paris8.fr',
        apiOmk:'http://localhost/omk_generateur/api/',
        ident: 'rqzo8WXIejwWj3CtfPraTDOOzKaIgnPj',
        key:'OOCK0VvxeaE2BFdI8ZVsCC9nhy2hU3Yp',
    });
a.getUser(initOeuvre);
function initOeuvre(){
}
