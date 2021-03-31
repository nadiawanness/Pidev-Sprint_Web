<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210331163447 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, recruteur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_CE606404BB0859F1 (recruteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404BB0859F1 FOREIGN KEY (recruteur_id) REFERENCES recruteur (id)');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FE0957003 FOREIGN KEY (idcategoriy_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FFDA726F8 FOREIGN KEY (idrecruteur_id) REFERENCES recruteur (id)');
        $this->addSql('CREATE INDEX IDX_AF86866FFDA726F8 ON offre (idrecruteur_id)');
        $this->addSql('ALTER TABLE postuler CHANGE accepte accepte VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE recruteur CHANGE nomsociete nomsociete VARCHAR(255) NOT NULL, CHANGE numsociete numsociete INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FE0957003');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FFDA726F8');
        $this->addSql('DROP INDEX IDX_AF86866FFDA726F8 ON offre');
        $this->addSql('ALTER TABLE postuler CHANGE accepte accepte VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'en_cours\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE recruteur CHANGE nomsociete nomsociete VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE numsociete numsociete INT DEFAULT NULL');
    }
}
