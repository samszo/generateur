SELECT g.id_dico, d.nom 'nom du dico', COUNT(*) 'nb de concepts'
FROM gen_generateurs g
LEFT JOIN gen_dicos d ON d.id_dico = g.id_dico
WHERE d.id_dico IS NULL
GROUP BY g.id_dico