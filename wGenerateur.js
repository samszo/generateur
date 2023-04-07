import {moteur} from './modules/moteur.js';

onmessage = function(event) {
  let rs=[], 
  m = new moteur({'apiUrl':event.data.apiUrl,'dicos':event.data.dicos,'id_dico':event.data.id_dico,'id_oeu':event.data.id_oeu});
  //m = new moteur({'apiUrl':event.data.apiUrl,'dicos':event.data.dicos,'id_dico':event.data.id_dico});

  if(Array.isArray(event.data.g)){
    event.data.g.forEach(v=>{
      m.strct=[];
      m.genere(v,true,true);
      rs.push({'gen':v,'texte':m.texte,'strct':m.strct});  
    })
    postMessage(rs);
  }else{
    m.genere(event.data.g,true,true);
    postMessage({'gen':event.data.g,'texte':m.texte,'strct':m.strct});
  }
  return;
};