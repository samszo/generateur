SELECT c.id_dico, d.nom 'nom du dico', COUNT(*) 'nb de concepts', c.type
FROM gen_concepts c
INNER JOIN gen_dicos d ON d.id_dico = c.id_dico
GROUP BY id_dico, type