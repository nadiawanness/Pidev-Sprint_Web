<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210309013356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE postuler (id INT AUTO_INCREMENT NOT NULL, offre_id INT DEFAULT NULL, recruteur_id INT DEFAULT NULL, INDEX IDX_8EC5A68D4CC8505A (offre_id), INDEX IDX_8EC5A68DBB0859F1 (recruteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE postuler ADD CONSTRAINT FK_8EC5A68D4CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)');
        $this->addSql('ALTER TABLE postuler ADD CONSTRAINT FK_8EC5A68DBB0859F1 FOREIGN KEY (recruteur_id) REFERENCES recruteur (id)');
        $this->addSql('ALTER TABLE offre DROP likes');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FE0957003 FOREIGN KEY (idcategoriy_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE postuler');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FE0957003');
        $this->addSql('ALTER TABLE offre ADD likes INT DEFAULT NULL');
    }
}
