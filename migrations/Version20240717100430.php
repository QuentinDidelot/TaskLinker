<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240717100430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etiquette ADD projet_id INT NOT NULL');
        $this->addSql('ALTER TABLE etiquette ADD CONSTRAINT FK_1E0E195AC18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('CREATE INDEX IDX_1E0E195AC18272 ON etiquette (projet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etiquette DROP FOREIGN KEY FK_1E0E195AC18272');
        $this->addSql('DROP INDEX IDX_1E0E195AC18272 ON etiquette');
        $this->addSql('ALTER TABLE etiquette DROP projet_id');
    }
}
