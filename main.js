import {oeuvres} from './modules/oeuvres.js';
import {appUrl} from './modules/appUrl.js';
import {auth} from './modules/auth.js';
/*pour un fonctionnement avec sqlite
TODO : gérer le chargement d'un fichier sqlite
TODO : gérer l'enregistremnet de la base dans un fichier cf. https://github.com/sql-js/sql.js/blob/master/examples/GUI/gui.js#L122
TODO : gérer la synchronisation avec une base centralisée
TODO : développer le CRUD
import {sql} from './modules/sql.js';
let s = new sql();
*/
let
worker = new Worker('wGenerateur.js',{ type: "module" });
let a = new auth({'navbar':d3.select('#navbarMain'),
        apiOmk:'http://localhost/omk_arcanes/api/',
        ident: 'ZOCgyR2932SMs81hokqlQ8a8wLQvrDgt',
        key: 'tS4EcOmUEQaW0cQFzND7Gtk60wkW0RRc',
        mail:'samuel.szoniecky@univ-paris8.fr'
        /*
        genstory
        apiOmk:'https://genstory.jardindesconnaissances.fr/api/',
        key_identity: tvKOUDlEJZ5x8kMzMmxyvCQ5IliEDuBI
        key_credential: KjcFiTN4NST6jo0iE45uJUF3OlWQwMl1        
        mail:'samuel.szoniecky@univ-paris8.fr'
        */
    });
a.getUser(initOeuvre);
function initOeuvre(){
    let oe = new oeuvres({
        'auth':a,
        'wGen':worker,
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
