<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231225145329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment ADD is_virtual TINYINT(1) NOT NULL COMMENT \'Face-to-face or remote\', CHANGE is_adult is_adult TINYINT(1) NOT NULL COMMENT \'Adult or child\'');
        $this->addSql('ALTER TABLE schedule DROP is_virtual');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP is_virtual, CHANGE is_adult is_adult TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE schedule ADD is_virtual TINYINT(1) NOT NULL COMMENT \'Face-to-face or remote\'');
    }
}
