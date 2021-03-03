<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301224658 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre CHANGE categ_id idcategoriy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FE0957003 FOREIGN KEY (idcategoriy_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_AF86866FE0957003 ON offre (idcategoriy_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FE0957003');
        $this->addSql('DROP INDEX IDX_AF86866FE0957003 ON offre');
        $this->addSql('ALTER TABLE offre CHANGE idcategoriy_id categ_id INT DEFAULT NULL');
    }
}
