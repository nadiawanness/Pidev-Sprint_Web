<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210319150238 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certificat (id INT AUTO_INCREMENT NOT NULL, test_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_27448F771E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE certificat_recruteur (certificat_id INT NOT NULL, recruteur_id INT NOT NULL, INDEX IDX_6CFB5CEBFA55BACF (certificat_id), INDEX IDX_6CFB5CEBBB0859F1 (recruteur_id), PRIMARY KEY(certificat_id, recruteur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recruteur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, recruteur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, q1 VARCHAR(255) NOT NULL, r1 VARCHAR(255) NOT NULL, q2 VARCHAR(255) NOT NULL, r2 VARCHAR(255) NOT NULL, q3 VARCHAR(255) NOT NULL, r3 VARCHAR(255) NOT NULL, q4 VARCHAR(255) NOT NULL, r4 VARCHAR(255) NOT NULL, q5 VARCHAR(255) NOT NULL, r5 VARCHAR(255) NOT NULL, INDEX IDX_D87F7E0CBB0859F1 (recruteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certificat ADD CONSTRAINT FK_27448F771E5D0459 FOREIGN KEY (test_id) REFERENCES test (id)');
        $this->addSql('ALTER TABLE certificat_recruteur ADD CONSTRAINT FK_6CFB5CEBFA55BACF FOREIGN KEY (certificat_id) REFERENCES certificat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE certificat_recruteur ADD CONSTRAINT FK_6CFB5CEBBB0859F1 FOREIGN KEY (recruteur_id) REFERENCES recruteur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0CBB0859F1 FOREIGN KEY (recruteur_id) REFERENCES recruteur (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certificat_recruteur DROP FOREIGN KEY FK_6CFB5CEBFA55BACF');
        $this->addSql('ALTER TABLE certificat_recruteur DROP FOREIGN KEY FK_6CFB5CEBBB0859F1');
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0CBB0859F1');
        $this->addSql('ALTER TABLE certificat DROP FOREIGN KEY FK_27448F771E5D0459');
        $this->addSql('DROP TABLE certificat');
        $this->addSql('DROP TABLE certificat_recruteur');
        $this->addSql('DROP TABLE recruteur');
        $this->addSql('DROP TABLE test');
    }
}
