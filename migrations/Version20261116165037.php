<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261116165037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, id_usuario INT NOT NULL, nombre_usuario VARCHAR(255) NOT NULL, mensaje LONGTEXT NOT NULL, fecha_envio DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE administracion CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE administracion ADD CONSTRAINT FK_5213623EBF396750 FOREIGN KEY (id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE administrador CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE administrador ADD CONSTRAINT FK_44F9A521BF396750 FOREIGN KEY (id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profesor CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE profesor ADD CONSTRAINT FK_5B7406D9BF396750 FOREIGN KEY (id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutor_legal CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE tutor_legal ADD CONSTRAINT FK_32F7B2E2BF396750 FOREIGN KEY (id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usuarios CHANGE rol rol VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE chat');
        $this->addSql('ALTER TABLE administracion DROP FOREIGN KEY FK_5213623EBF396750');
        $this->addSql('ALTER TABLE administracion CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE administrador DROP FOREIGN KEY FK_44F9A521BF396750');
        $this->addSql('ALTER TABLE administrador CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE profesor DROP FOREIGN KEY FK_5B7406D9BF396750');
        $this->addSql('ALTER TABLE profesor CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE tutor_legal DROP FOREIGN KEY FK_32F7B2E2BF396750');
        $this->addSql('ALTER TABLE tutor_legal CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE usuarios CHANGE rol rol VARCHAR(255) NOT NULL');
    }
}
