<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231217094910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL COMMENT \'Unique identifier of the schedule\', date DATETIME NOT NULL COMMENT \'Day of appointment\', is_available TINYINT(1) NOT NULL, is_virtual TINYINT(1) NOT NULL COMMENT \'Face-to-face or remote\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'List of appointment days\' ');
        $this->addSql('ALTER TABLE patient CHANGE locality locality VARCHAR(64) DEFAULT NULL, CHANGE postal postal VARCHAR(64) DEFAULT NULL, CHANGE doctor doctor VARCHAR(64) DEFAULT NULL, CHANGE phone phone VARCHAR(64) DEFAULT NULL, CHANGE origin origin VARCHAR(64) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE schedule');
        $this->addSql('ALTER TABLE patient CHANGE locality locality VARCHAR(255) DEFAULT NULL, CHANGE postal postal VARCHAR(255) DEFAULT NULL, CHANGE doctor doctor VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE origin origin VARCHAR(255) NOT NULL');
    }
}
