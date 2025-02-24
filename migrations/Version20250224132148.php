<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224132148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campus (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_campus (event_id INT NOT NULL, campus_id INT NOT NULL, INDEX IDX_AF901F4371F7E88B (event_id), INDEX IDX_AF901F43AF5D55E1 (campus_id), PRIMARY KEY(event_id, campus_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_campus ADD CONSTRAINT FK_AF901F4371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_campus ADD CONSTRAINT FK_AF901F43AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event CHANGE status status VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_campus DROP FOREIGN KEY FK_AF901F4371F7E88B');
        $this->addSql('ALTER TABLE event_campus DROP FOREIGN KEY FK_AF901F43AF5D55E1');
        $this->addSql('DROP TABLE campus');
        $this->addSql('DROP TABLE event_campus');
        $this->addSql('DROP TABLE status');
        $this->addSql('ALTER TABLE event CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
