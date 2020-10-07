

#Purge

DROP TABLE IF EXISTS INTERVIENT;
DROP TABLE IF EXISTS INTERVENTION;
DROP TABLE IF EXISTS MATERIEL;
DROP TABLE IF EXISTS LIEU;
DROP TABLE IF EXISTS MARQUE;
DROP TABLE IF EXISTS TYPE;
DROP TABLE IF EXISTS SPECIFICITE;
DROP TABLE IF EXISTS REFERENT;
DROP TABLE IF EXISTS STOCK.USER;
DROP TABLE IF EXISTS STATUT;



#Creation des diff�rentes tables

CREATE TABLE STATUT(
idStatut int not null auto_increment,
libelleStatut varchar(50),
PRIMARY key (idStatut)
);

CREATE TABLE MARQUE(
idMarque int not null auto_increment,
libelleMarque varchar(50),
Primary key(idMarque)
);

Create table TYPE(
idType int not null auto_increment,
libelleType varchar(100),
Primary key(idType)
);

Create table SPECIFICITE(
idSpecificite int not null auto_increment,
libelleSpe varchar(100),
Primary key(idSpecificite)
);
CREATE TABLE USER(
idUser int auto_increment not null,
username varchar(50) UNIQUE,
password varchar(100),
nom varchar(50),
prenom varchar(50),
mail varchar(100),
present int,
roles json NOT NULL,
PRIMARY key(idUser)
);
Create table LIEU(
idLieu int not null auto_increment,
libelleLieu varchar(200),
Primary key(idLieu)
);

Create table MATERIEL(
idMateriel int auto_increment not null,
numeroSerie varchar(50),
idStatut int,
nomMateriel varchar(200),
motsCles varchar(500),
idMarque int,
idLieu int,
idType int,
date varchar(50),
idSpecificite int,
idUser int,
supprimer varchar(50),
Primary key(idMateriel),
Foreign key(idMarque) REFERENCES MARQUE(idMarque),
Foreign key(idLieu) REFERENCES LIEU(idLieu),
Foreign key(idType) REFERENCES TYPE(idType),
Foreign key(idSpecificite) REFERENCES SPECIFICITE(idSpecificite),
Foreign key(idUser) REFERENCES USER(idUser),
FOREIGN key(idStatut) REFERENCES STATUT(idStatut)
);

CREATE TABLE INTERVENTION(
idIntervention int auto_increment not null,
idUser int,
idLieu int,
dateIntervention varchar(50),
statutInter varchar(50),
Primary key(idIntervention),
Foreign key(idUser) REFERENCES USER(idUser),
Foreign key(idLieu) REFERENCES  LIEU(idLieu)
);

Create table INTERVIENT(
idIntervention int,
idMateriel int,
idLieuDepart int,
idLieuArrive int,
dateAffectation varchar(50),
idStatut int,
Constraint PK_Intervient Primary key(idIntervention,idMateriel),
Foreign key(idIntervention) REFERENCES INTERVENTION(idIntervention),
Foreign key(idMateriel) REFERENCES MATERIEL(idMateriel),
Foreign key(idLieuDepart) REFERENCES LIEU(idLieu),
Foreign key(idLieuArrive) REFERENCES LIEU(idLieu),
FOREIGN key(idStatut) REFERENCES STATUT(idStatut)
);

INSERT INTO `lieu` (`idLieu`, `libelleLieu`) VALUES
(1, 'amancey.ce'),
(2, 'audincourt.annexe-champs'),
(3, 'audincourt.ase-pmi'),
(4, 'audincourt.ce-eams'),
(5, 'audincourt.cms-gare'),
(6, 'baume-les-dames.bal-ce'),
(7, 'baume-les-dames.cms'),
(8, 'besançon.13-15-pref'),
(9, 'besançon.18-pref-drmg-ds'),
(10, 'besançon.23-nodier-pec'),
(11, 'besançon.3bis-lussac'),
(12, 'besançon.3-lussac'),
(13, 'besançon.8-nodier'),
(14, 'besançon.archives-dpt'),
(15, 'besançon.cde-bosquet'),
(16, 'besançon.cde-chaille'),
(17, 'besançon.cdef-torcols'),
(18, 'besançon.cdef-wyrsch'),
(19, 'besançon.centre-planif'),
(20, 'besançon.cmps-pmi-palente'),
(21, 'besançon.cms-bacchus'),
(22, 'besançon.cms-montrapon'),
(23, 'besançon.cms-past-pec-planoise'),
(24, 'besançon.cms-st-claude'),
(25, 'besançon.cms-st-ferjeux'),
(26, 'besançon.cms-tristan-b'),
(27, 'besançon.datacenter'),
(28, 'besançon.fort-griffon'),
(29, 'besançon.hotel'),
(30, 'besançon.lvd'),
(31, 'besançon.medecine'),
(32, 'besançon.mediatheque-dpt'),
(33, 'besançon.parc-routier'),
(34, 'besançon.sta-clairiere'),
(35, 'besançon.uef-fontaine-argent'),
(36, 'bethoncourt.cms'),
(37, 'chalezeule.ce'),
(38, 'clerval.cms'),
(39, 'devecey.cms'),
(40, 'etupes.cms'),
(41, 'exincourt.acc-urg'),
(42, 'exincourt.cdef'),
(43, 'flagey.ferme'),
(44, 'franois.ce'),
(45, 'grand-charmont.cms'),
(46, 'isle-sur-le-doubs.bal-ce-cms'),
(47, 'le-russey.ce-perm'),
(48, 'levier.ce'),
(49, 'maiche.bal-ce'),
(50, 'maiche.cms'),
(51, 'mandeure.cms'),
(52, 'montbeliard.cms-chiffogne'),
(53, 'montbeliard.cms-petit-chenois'),
(54, 'montbeliard.maison-dpt'),
(55, 'montbeliard.parc-bal-ce-cer'),
(56, 'montbeliard.planif-schiffle'),
(57, 'montbeliard.quasar-ase'),
(58, 'montbeliard.sta'),
(59, 'montbeliard.syndic'),
(60, 'morteau.ce'),
(61, 'morteau.cms'),
(62, 'morteau.past'),
(63, 'mouthe.ce-perm'),
(64, 'novillars.cms'),
(65, 'orchamps-vennes.bal-ce'),
(66, 'ornans.atelier'),
(67, 'ornans.bal-ce'),
(68, 'ornans.cms'),
(69, 'ornans.musee'),
(70, 'pontarlier.bal-egr'),
(71, 'pontarlier.cms-magnin'),
(72, 'pontarlier.cms-planif'),
(73, 'pontarlier.maison-dpt-phd'),
(74, 'pontarlier.parc-sta-ce'),
(75, 'pontarlier.phd'),
(76, 'pont-de-roide.ce'),
(77, 'pont-de-roide.cms'),
(78, 'quingey.ce'),
(79, 'quingey.cms'),
(80, 'rougemont.cms-ce'),
(81, 'saint-hippolyte.ce'),
(82, 'saint-vit.cms'),
(83, 'sancey-le-grand.ce'),
(84, 'saone.ce'),
(85, 'saone.cms'),
(86, 'seloncourt.cms'),
(87, 'serre-les-sapins.cms'),
(88, 'valdahon.ce'),
(89, 'valdahon.cms'),
(90, 'valentigney.buis'),
(91, 'valentigney.cms-zac');

INSERT INTO `statut` (`idStatut`, `libelleStatut`) VALUES
(1, 'En stock'),
(2, 'Déployer sur site'),
(3, 'SAV'),
(4, 'A réformer');


INSERT INTO `user` (`idUser`, `username`, `password`, `nom`, `prenom`, `mail`, `present`, `roles`) VALUES
(null, 'root', '$2y$13$zOVIZB349uFFo9PaVkvkCucDrb9VI53sNiHm6fh8Yu./mwFAnTV0m', 'ROOT', ' ', '', 1, '[\"ROLE_ADMIN\"]'),
(null, 'public', '$2y$13$gbYqM2ZqR/byb2Qh3rfQpebHQ1yIw2O3.pIOqq1KRujWrhZxxFhmq', 'public', ' ', ' ', 1, '[\"ROLE_PUBLIC\"]');

#MDP root : rootroot1234
#MDP public : testtest1234*

