<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210324162829 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE certificat_recruteur');
        $this->addSql('ALTER TABLE certificat ADD idrecruteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE certificat ADD CONSTRAINT FK_27448F77FDA726F8 FOREIGN KEY (idrecruteur_id) REFERENCES recruteur (id)');
        $this->addSql('CREATE INDEX IDX_27448F77FDA726F8 ON certificat (idrecruteur_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certificat_recruteur (certificat_id INT NOT NULL, recruteur_id INT NOT NULL, INDEX IDX_6CFB5CEBFA55BACF (certificat_id), INDEX IDX_6CFB5CEBBB0859F1 (recruteur_id), PRIMARY KEY(certificat_id, recruteur_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE certificat_recruteur ADD CONSTRAINT FK_6CFB5CEBBB0859F1 FOREIGN KEY (recruteur_id) REFERENCES recruteur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE certificat_recruteur ADD CONSTRAINT FK_6CFB5CEBFA55BACF FOREIGN KEY (certificat_id) REFERENCES certificat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE certificat DROP FOREIGN KEY FK_27448F77FDA726F8');
        $this->addSql('DROP INDEX IDX_27448F77FDA726F8 ON certificat');
        $this->addSql('ALTER TABLE certificat DROP idrecruteur_id');
    }
}
