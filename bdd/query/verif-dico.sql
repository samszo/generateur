SELECT COUNT(*) nb, id_dico, c.type
FROM gen_concepts c
WHERE id_dico IN (17,22) 
GROUP BY id_dico, type