<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170708175308 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE inventory ADD game_id INT DEFAULT NULL, CHANGE options options LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
        $this->addSql('CREATE INDEX IDX_B12D4A36E48FD905 ON inventory (game_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A36E48FD905');
        $this->addSql('DROP INDEX IDX_B12D4A36E48FD905 ON inventory');
        $this->addSql('ALTER TABLE inventory DROP game_id, CHANGE options options LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
