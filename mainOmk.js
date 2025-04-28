import {appUrl} from './modules/appUrl.js';
import {auth} from './modules/authOmk.js';
import {oeuvres} from './modules/oeuvres.js';

let a = new auth({'navbar':d3.select('#navbarMain'),
        mail:'samuel.szoniecky@univ-paris8.fr',
        apiOmk:'http://localhost/omk_generateur/api/',
        ident: 'rqzo8WXIejwWj3CtfPraTDOOzKaIgnPj',
        key:'OOCK0VvxeaE2BFdI8ZVsCC9nhy2hU3Yp',
    });
a.getUser(initOeuvre);
function initOeuvre(){
    let oe = new oeuvres({
        'auth':a,
        'tgtMenu':document.getElementById('menuOeuvres'),
        'tgtList':document.getElementById('listDicos'),
        'tgtContent':document.getElementById('contentDetails'),
        'appUrl':new appUrl({
            'tgtIn':d3.select("#inptUrl").node(),
            'tgtBtn':d3.select("#url-addon"),
            'url':new URL(document.location)
        })
    });
}
