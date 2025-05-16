-- mysql --user='marie_dyer' --password='angelle12107' sales_dept < '/tmp/prospects.sql'

-- supprimer les lignes incohérentes
DELETE FROM gen_generateurs WHERE id_gen IN(select g.id_gen from gen_generateurs g
left join gen_concepts c ON g.id_concept = c.id_concept
where c.id_concept is null);

DELETE FROM gen_substantifs WHERE id_sub IN(
select s.id_sub from gen_substantifs s
left join gen_concepts c ON s.id_concept = c.id_concept
where c.id_concept is null
);
DELETE FROM gen_adjectifs WHERE id_adj IN(
select a.id_adj from gen_adjectifs a
left join gen_concepts c ON a.id_concept = c.id_concept
where c.id_concept is null
);

DELETE FROM gen_verbes WHERE id_verbe IN(
select v.id_verbe from gen_verbes v
left join gen_concepts c ON v.id_concept = c.id_concept
where c.id_concept is null
);

delete from gen_oeuvres_dicos_utis where id_oeu IN (
select odu.id_oeu from gen_oeuvres_dicos_utis odu
left join  gen_oeuvres o ON o.id_oeu = odu.id_oeu
where o.id_oeu is null);


DELETE FROM gen_concepts WHERE id_concept IN(
    select c.id_concept from gen_concepts c
left join gen_dicos d ON c.id_dico = d.id_dico
where d.id_dico is null
);



--passer les tables en innodb
ALTER TABLE gen_generateurs ENGINE=InnoDB;
ALTER TABLE gen_substantifs ENGINE=InnoDB;
ALTER TABLE gen_adjectifs ENGINE=InnoDB;
ALTER TABLE gen_verbes ENGINE=InnoDB;
ALTER TABLE gen_concepts ENGINE=InnoDB;
ALTER TABLE gen_dicos ENGINE=InnoDB;


--ajouter les contraintes
ALTER TABLE `gen_generateurs` ADD CONSTRAINT `fk_concepts_generateurs` FOREIGN KEY (`id_concept`) REFERENCES `gen_concepts`(`id_concept`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `gen_adjectifs` ADD CONSTRAINT `fk_concepts_adjectifs` FOREIGN KEY (`id_concept`) REFERENCES `gen_concepts`(`id_concept`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `gen_substantifs` ADD CONSTRAINT `fk_concepts_substantifs` FOREIGN KEY (`id_concept`) REFERENCES `gen_concepts`(`id_concept`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `gen_verbes` ADD CONSTRAINT `fk_concepts_verbes` FOREIGN KEY (`id_concept`) REFERENCES `gen_concepts`(`id_concept`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `gen_uris` ADD CONSTRAINT `fk_concepts_uris` FOREIGN KEY (`id_concept`) REFERENCES `gen_concepts`(`id_concept`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `gen_sparqls` ADD CONSTRAINT `fk_concepts_sparqls` FOREIGN KEY (`id_concept`) REFERENCES `gen_concepts`(`id_concept`) ON DELETE CASCADE ON UPDATE RESTRICT;

-- ALTER TABLE `gen_dicos` ADD CONSTRAINT `fk_dicos_id_concept` FOREIGN KEY (`id_dico`) REFERENCES `gen_concepts`(`id_dico`) ON DELETE CASCADE ON UPDATE RESTRICT;