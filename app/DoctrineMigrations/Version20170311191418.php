<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170311191418 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expire_at DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, ai TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1483A5E9A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_1483A5E9C05FB297 (confirmation_token), UNIQUE INDEX UNIQ_1483A5E9989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(200) NOT NULL, status SMALLINT UNSIGNED NOT NULL, create_at DATETIME NOT NULL, run_at DATETIME DEFAULT NULL, last DATETIME DEFAULT NULL, size SMALLINT UNSIGNED NOT NULL, tour LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', max_player SMALLINT UNSIGNED NOT NULL, options LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', grid LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_FF232B31989D9B62 (slug), INDEX IDX_FF232B3161220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE players (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, ai TINYINT(1) NOT NULL, life SMALLINT NOT NULL, score INT NOT NULL, color VARCHAR(6) NOT NULL, team SMALLINT DEFAULT NULL, boats LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', position SMALLINT NOT NULL, INDEX IDX_264E43A6E48FD905 (game_id), INDEX IDX_264E43A6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3161220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A6E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3161220EA6');
        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A6A76ED395');
        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A6E48FD905');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE players');
    }
}
