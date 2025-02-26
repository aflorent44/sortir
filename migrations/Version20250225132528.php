<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225132528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campus CHANGE name name VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE user ADD campus_id INT NOT NULL, DROP mail, CHANGE email email VARCHAR(200) NOT NULL, CHANGE phone_number phone_number VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649AF5D55E1 ON user (campus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AF5D55E1');
        $this->addSql('DROP INDEX IDX_8D93D649AF5D55E1 ON user');
        $this->addSql('ALTER TABLE user ADD mail VARCHAR(150) NOT NULL, DROP campus_id, CHANGE email email VARCHAR(180) NOT NULL, CHANGE phone_number phone_number INT NOT NULL');
        $this->addSql('ALTER TABLE campus CHANGE name name VARCHAR(255) NOT NULL');
    }
}
