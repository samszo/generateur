select count(*) nb, t.id_conj, c.modele, t.lib, t.num, c.id_dico 
FROM gen_terminaisons t
inner join gen_conjugaisons c on c.id_conj = t.id_conj 
group by t.id_conj, c.id_dico 
having nb <> 38