<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240717090743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etiquette_projet (etiquette_id INT NOT NULL, projet_id INT NOT NULL, INDEX IDX_5AC004D7BD2EA57 (etiquette_id), INDEX IDX_5AC004DC18272 (projet_id), PRIMARY KEY(etiquette_id, projet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE etiquette_projet ADD CONSTRAINT FK_5AC004D7BD2EA57 FOREIGN KEY (etiquette_id) REFERENCES etiquette (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etiquette_projet ADD CONSTRAINT FK_5AC004DC18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etiquette_projet DROP FOREIGN KEY FK_5AC004D7BD2EA57');
        $this->addSql('ALTER TABLE etiquette_projet DROP FOREIGN KEY FK_5AC004DC18272');
        $this->addSql('DROP TABLE etiquette_projet');
    }
}
