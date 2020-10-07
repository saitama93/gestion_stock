<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201007120516 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE intervention (idIntervention INT AUTO_INCREMENT NOT NULL, dateIntervention VARCHAR(50) DEFAULT NULL, statutInter VARCHAR(50) DEFAULT NULL, idUser INT DEFAULT NULL, idLieu INT DEFAULT NULL, INDEX idUser (idUser), INDEX idLieu (idLieu), PRIMARY KEY(idIntervention)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intervient (dateAffectation VARCHAR(50) DEFAULT NULL, idIntervention INT NOT NULL, idMateriel INT NOT NULL, idLieuDepart INT DEFAULT NULL, idLieuArrive INT DEFAULT NULL, idStatut INT DEFAULT NULL, INDEX idStatut (idStatut), INDEX idLieuDepart (idLieuDepart), INDEX idLieuArrive (idLieuArrive), INDEX idMateriel (idMateriel), INDEX IDX_3F111A398F86B05A (idIntervention), PRIMARY KEY(idIntervention, idMateriel)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieu (idLieu INT AUTO_INCREMENT NOT NULL, libelleLieu VARCHAR(200) DEFAULT NULL, PRIMARY KEY(idLieu)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque (idMarque INT AUTO_INCREMENT NOT NULL, libelleMarque VARCHAR(50) DEFAULT NULL, PRIMARY KEY(idMarque)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE materiel (idMateriel INT AUTO_INCREMENT NOT NULL, numeroSerie VARCHAR(50) DEFAULT NULL, nomMateriel VARCHAR(200) DEFAULT NULL, motsCles VARCHAR(500) DEFAULT NULL, date VARCHAR(50) DEFAULT NULL, supprimer VARCHAR(50) DEFAULT NULL, idMarque INT DEFAULT NULL, idLieu INT DEFAULT NULL, idType INT DEFAULT NULL, idSpecificite INT DEFAULT NULL, idUser INT DEFAULT NULL, idStatut INT DEFAULT NULL, INDEX idUser (idUser), INDEX idType (idType), INDEX idMarque (idMarque), INDEX idStatut (idStatut), INDEX idSpecificite (idSpecificite), INDEX idLieu (idLieu), PRIMARY KEY(idMateriel)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specificite (idSpecificite INT AUTO_INCREMENT NOT NULL, libelleSpe VARCHAR(100) DEFAULT NULL, PRIMARY KEY(idSpecificite)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut (idStatut INT AUTO_INCREMENT NOT NULL, libelleStatut VARCHAR(50) DEFAULT NULL, PRIMARY KEY(idStatut)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (idType INT AUTO_INCREMENT NOT NULL, libelleType VARCHAR(100) DEFAULT NULL, PRIMARY KEY(idType)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (idUser INT AUTO_INCREMENT NOT NULL, username VARCHAR(50) DEFAULT NULL, password VARCHAR(100) DEFAULT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, mail VARCHAR(100) NOT NULL, present INT NOT NULL, roles JSON NOT NULL, UNIQUE INDEX username (username), PRIMARY KEY(idUser)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABFE6E88D7 FOREIGN KEY (idUser) REFERENCES user (idUser)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB5CAA23C7 FOREIGN KEY (idLieu) REFERENCES lieu (idLieu)');
        $this->addSql('ALTER TABLE intervient ADD CONSTRAINT FK_3F111A398F86B05A FOREIGN KEY (idIntervention) REFERENCES intervention (idIntervention)');
        $this->addSql('ALTER TABLE intervient ADD CONSTRAINT FK_3F111A394B719ACA FOREIGN KEY (idMateriel) REFERENCES materiel (idMateriel)');
        $this->addSql('ALTER TABLE intervient ADD CONSTRAINT FK_3F111A39D16EEB53 FOREIGN KEY (idLieuDepart) REFERENCES lieu (idLieu)');
        $this->addSql('ALTER TABLE intervient ADD CONSTRAINT FK_3F111A39F9A54DB7 FOREIGN KEY (idLieuArrive) REFERENCES lieu (idLieu)');
        $this->addSql('ALTER TABLE intervient ADD CONSTRAINT FK_3F111A3986755825 FOREIGN KEY (idStatut) REFERENCES statut (idStatut)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B091397E3954 FOREIGN KEY (idMarque) REFERENCES marque (idMarque)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B0915CAA23C7 FOREIGN KEY (idLieu) REFERENCES lieu (idLieu)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B091FF2309B7 FOREIGN KEY (idType) REFERENCES type (idType)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B091BE7CED67 FOREIGN KEY (idSpecificite) REFERENCES specificite (idSpecificite)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B091FE6E88D7 FOREIGN KEY (idUser) REFERENCES user (idUser)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B09186755825 FOREIGN KEY (idStatut) REFERENCES statut (idStatut)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervient DROP FOREIGN KEY FK_3F111A398F86B05A');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB5CAA23C7');
        $this->addSql('ALTER TABLE intervient DROP FOREIGN KEY FK_3F111A39D16EEB53');
        $this->addSql('ALTER TABLE intervient DROP FOREIGN KEY FK_3F111A39F9A54DB7');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B0915CAA23C7');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B091397E3954');
        $this->addSql('ALTER TABLE intervient DROP FOREIGN KEY FK_3F111A394B719ACA');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B091BE7CED67');
        $this->addSql('ALTER TABLE intervient DROP FOREIGN KEY FK_3F111A3986755825');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B09186755825');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B091FF2309B7');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABFE6E88D7');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B091FE6E88D7');
        $this->addSql('DROP TABLE intervention');
        $this->addSql('DROP TABLE intervient');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE materiel');
        $this->addSql('DROP TABLE specificite');
        $this->addSql('DROP TABLE statut');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE user');
    }
}
