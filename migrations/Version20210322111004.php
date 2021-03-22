<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210322111004 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FE0957003 FOREIGN KEY (idcategoriy_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE recruteur ADD reset_token VARCHAR(50) DEFAULT NULL, CHANGE nomsociete nomsociete VARCHAR(255) NOT NULL, CHANGE numsociete numsociete INT NOT NULL, CHANGE activation_token activation_token VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FE0957003');
        $this->addSql('ALTER TABLE recruteur DROP reset_token, CHANGE nomsociete nomsociete VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE numsociete numsociete INT DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
