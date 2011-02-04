SELECT count(*), id_dico
FROM gen_verbes v
WHERE v.id_conj = -1
GROUP BY v.id_dico