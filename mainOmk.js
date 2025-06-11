import {appUrl} from './modules/appUrl.js';
import {auth} from './modules/authOmk.js';
import {oeuvres} from './modules/oeuvres.js';
import {pa} from './modules/authParams.js';

let a = new auth(pa);
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
