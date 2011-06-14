SELECT count(*) nb, c.id_conj
FROM gen_conjugaisons c
INNER JOIN gen_terminaisons t ON t.id_conj = c.id_conj
WHERE c.id_dico = 11
GROUP BY c.id_conj