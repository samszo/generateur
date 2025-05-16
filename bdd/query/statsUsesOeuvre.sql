select count(distinct odu.id_dico) nbDico
    , count(distinct c.id_concept) nbConcept 
from gen_oeuvres o
inner join gen_oeuvres_dicos_utis odu on odu.id_oeu = o.id_oeu
inner join gen_dicos d on d.id_dico = odu.id_dico AND d.general = 0
left join gen_concepts c on c.id_dico = d.id_dico
where o.id_oeu = 65
;