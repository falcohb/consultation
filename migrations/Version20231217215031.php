<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231217215031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844B897366B');
        $this->addSql('DROP INDEX UNIQ_FE38F844B897366B ON appointment');
        $this->addSql('ALTER TABLE appointment CHANGE date_id schedule_id INT NOT NULL COMMENT \'Unique identifier of the schedule\'');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE38F844A40BC2D5 ON appointment (schedule_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844A40BC2D5');
        $this->addSql('DROP INDEX UNIQ_FE38F844A40BC2D5 ON appointment');
        $this->addSql('ALTER TABLE appointment CHANGE schedule_id date_id INT NOT NULL COMMENT \'Unique identifier of the schedule\'');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844B897366B FOREIGN KEY (date_id) REFERENCES schedule (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE38F844B897366B ON appointment (date_id)');
    }
}
