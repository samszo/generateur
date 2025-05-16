 SELECT count(*) nb, count(distinct term_id) nbTerm, code_id FROM `gen_ordre` group by code_id ORDER BY `nb` DESC
-- SELECT count(*) nb, gen_ids FROM `gen_flux` group by gen_ids ORDER BY length(gen_ids) DESC, `nb` DESC
-- SELECT count(*) nb, term_id FROM `gen_flux` group by term_id ORDER BY `nb` DESC
-- SELECT count(*) nb, value, gen_ids, ordre, conditionnel FROM `gen_flux` group by value, gen_ids, ordre, conditionnel ORDER BY `nb` DESC
-- SELECT count(*) nb, value, gen_ids, determinant_id FROM `gen_flux` group by value, gen_ids, determinant_id ORDER BY `nb` DESC
-- SELECT count(*) nb, oeuvre_id,concept_id,term_id,value,determinant,determinant_id,gen_ids,ordre,conditionnel FROM `gen_flux` group by oeuvre_id,concept_id,term_id,value,determinant,determinant_id,gen_ids,ordre,conditionnel ORDER BY `nb` DESC
-- SELECT concept_id, count(DISTINCT term_id) nbTerm, count(term_id) nbStep, MAX(ordre) maxStep FROM `gen_flux` GROUP BY concept_id;
/*
SELECT DISTINCT
    f.term_id, f.ordre, c.id
FROM
    gen_flux f
        INNER JOIN
    gen_code c ON c.value = f.value;
*/