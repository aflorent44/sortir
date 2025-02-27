<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227082603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event CHANGE host_id host_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event_campus DROP FOREIGN KEY FK_AF901F4371F7E88B');
        $this->addSql('ALTER TABLE event_campus ADD CONSTRAINT FK_AF901F4371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(200) NOT NULL, CHANGE phone_number phone_number VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event CHANGE host_id host_id INT NOT NULL');
        $this->addSql('ALTER TABLE event_campus DROP FOREIGN KEY FK_AF901F4371F7E88B');
        $this->addSql('ALTER TABLE event_campus ADD CONSTRAINT FK_AF901F4371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(150) NOT NULL, CHANGE phone_number phone_number VARCHAR(10) NOT NULL');
    }
}
