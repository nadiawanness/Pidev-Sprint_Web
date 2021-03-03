<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301191151 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commenter ADD forum_id INT NOT NULL');
        $this->addSql('ALTER TABLE commenter ADD CONSTRAINT FK_AB751D0A29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id)');
        $this->addSql('CREATE INDEX IDX_AB751D0A29CCBAD0 ON commenter (forum_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commenter DROP FOREIGN KEY FK_AB751D0A29CCBAD0');
        $this->addSql('DROP INDEX IDX_AB751D0A29CCBAD0 ON commenter');
        $this->addSql('ALTER TABLE commenter DROP forum_id');
    }
}
