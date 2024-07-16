<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716133659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etiquette_tache (etiquette_id INT NOT NULL, tache_id INT NOT NULL, INDEX IDX_2F3DEBC27BD2EA57 (etiquette_id), INDEX IDX_2F3DEBC2D2235D39 (tache_id), PRIMARY KEY(etiquette_id, tache_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE etiquette_tache ADD CONSTRAINT FK_2F3DEBC27BD2EA57 FOREIGN KEY (etiquette_id) REFERENCES etiquette (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etiquette_tache ADD CONSTRAINT FK_2F3DEBC2D2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etiquette_tache DROP FOREIGN KEY FK_2F3DEBC27BD2EA57');
        $this->addSql('ALTER TABLE etiquette_tache DROP FOREIGN KEY FK_2F3DEBC2D2235D39');
        $this->addSql('DROP TABLE etiquette_tache');
    }
}
