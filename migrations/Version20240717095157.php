<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240717095157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creneau ADD tache_id INT DEFAULT NULL, ADD employe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5FD2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id)');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5F1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('CREATE INDEX IDX_F9668B5FD2235D39 ON creneau (tache_id)');
        $this->addSql('CREATE INDEX IDX_F9668B5F1B65292 ON creneau (employe_id)');
        $this->addSql('ALTER TABLE tache ADD employe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720751B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('CREATE INDEX IDX_938720751B65292 ON tache (employe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creneau DROP FOREIGN KEY FK_F9668B5FD2235D39');
        $this->addSql('ALTER TABLE creneau DROP FOREIGN KEY FK_F9668B5F1B65292');
        $this->addSql('DROP INDEX IDX_F9668B5FD2235D39 ON creneau');
        $this->addSql('DROP INDEX IDX_F9668B5F1B65292 ON creneau');
        $this->addSql('ALTER TABLE creneau DROP tache_id, DROP employe_id');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720751B65292');
        $this->addSql('DROP INDEX IDX_938720751B65292 ON tache');
        $this->addSql('ALTER TABLE tache DROP employe_id');
    }
}
