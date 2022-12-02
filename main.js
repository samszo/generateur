import o from './modules/oeuvres.js';
import u from './modules/appUrl.js';
let oe = new o.oeuvres({
    'tgtMenu':document.getElementById('menuOeuvres'),
    'tgtList':document.getElementById('listDicos'),
    'tgtContent':document.getElementById('contentDetails'),
    'appUrl':new u.appUrl({
        'tgtIn':d3.select("#inptUrl").node(),
        'tgtBtn':d3.select("#url-addon"),
        'url':new URL(document.location)
    })
});    
