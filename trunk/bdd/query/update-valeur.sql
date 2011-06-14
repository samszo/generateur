update gen_generateurs
SET valeur = REPLACE(valeur, '{TRAV_01}', '') 
where valeur like '%{TRAV_01}%'