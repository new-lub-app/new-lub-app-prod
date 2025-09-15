<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250914223153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote ADD porduct_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4FFB5E786 FOREIGN KEY (porduct_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_6B71CBF4FFB5E786 ON quote (porduct_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4FFB5E786');
        $this->addSql('DROP INDEX IDX_6B71CBF4FFB5E786 ON quote');
        $this->addSql('ALTER TABLE quote DROP porduct_id');
    }
}
