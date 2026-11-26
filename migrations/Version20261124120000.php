<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261124120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crea las tablas de eventos de calendario y de tokens de recuperación de contraseña.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE eventos (id INT AUTO_INCREMENT NOT NULL, titulo VARCHAR(255) NOT NULL, descripcion LONGTEXT DEFAULT NULL, inicio DATETIME NOT NULL, fin DATETIME NOT NULL, todo_el_dia TINYINT NOT NULL, tipo VARCHAR(50) NOT NULL, lugar VARCHAR(255) DEFAULT NULL, fecha_creacion DATETIME NOT NULL, aula_id INT DEFAULT NULL, creador_id INT DEFAULT NULL, INDEX IDX_6B23BD8FAD1A1255 (aula_id), INDEX IDX_6B23BD8F62F40C3D (creador_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE recuperacion_contrasenya (id INT AUTO_INCREMENT NOT NULL, token_hash VARCHAR(64) NOT NULL, fecha_solicitud DATETIME NOT NULL, fecha_expiracion DATETIME NOT NULL, usado TINYINT NOT NULL, usuario_id INT NOT NULL, UNIQUE INDEX UNIQ_A024310BB3BC57DA (token_hash), INDEX IDX_A024310BDB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE eventos ADD CONSTRAINT FK_6B23BD8FAD1A1255 FOREIGN KEY (aula_id) REFERENCES aula (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE eventos ADD CONSTRAINT FK_6B23BD8F62F40C3D FOREIGN KEY (creador_id) REFERENCES usuarios (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE recuperacion_contrasenya ADD CONSTRAINT FK_A024310BDB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE eventos DROP FOREIGN KEY FK_6B23BD8FAD1A1255');
        $this->addSql('ALTER TABLE eventos DROP FOREIGN KEY FK_6B23BD8F62F40C3D');
        $this->addSql('DROP TABLE eventos');
        $this->addSql('ALTER TABLE recuperacion_contrasenya DROP FOREIGN KEY FK_A024310BDB38439E');
        $this->addSql('DROP TABLE recuperacion_contrasenya');
    }
}
