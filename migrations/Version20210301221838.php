<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301221838 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre ADD categ_id INT DEFAULT NULL, DROP categ');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FE8175B12 FOREIGN KEY (categ_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_AF86866FE8175B12 ON offre (categ_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FE8175B12');
        $this->addSql('DROP INDEX IDX_AF86866FE8175B12 ON offre');
        $this->addSql('ALTER TABLE offre ADD categ VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP categ_id');
    }
}
