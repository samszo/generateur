import o from './modules/oeuvres.js';
import u from './modules/appUrl.js';
import a from './modules/auth.js';
/*pour un fonctionnement avec sqlite
TODO : gérer le chargement d'un fichier sqlite
TODO : gérer l'enregistremnet de la base dans un fichier cf. https://github.com/sql-js/sql.js/blob/master/examples/GUI/gui.js#L122
TODO : gérer la synchronisation avec une base centralisée
TODO : développer le CRUD
import {sql} from './modules/sql.js';
let s = new sql();
*/
let
auth = new a.auth({'navbar':d3.select('#navbarMain'),
        apiOmk:'http://localhost/omk_arcanes/api/',
        //
        //
    }),
oe = new o.oeuvres({
    'auth':auth,
    'tgtMenu':document.getElementById('menuOeuvres'),
    'tgtList':document.getElementById('listDicos'),
    'tgtContent':document.getElementById('contentDetails'),
    'appUrl':new u.appUrl({
        'tgtIn':d3.select("#inptUrl").node(),
        'tgtBtn':d3.select("#url-addon"),
        'url':new URL(document.location)
    })
});
