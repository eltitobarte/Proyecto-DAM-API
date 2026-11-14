<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261030160821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administracion (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE administrador (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE alergias (id INT AUTO_INCREMENT NOT NULL, alergia VARCHAR(255) DEFAULT NULL, alumno_codigo INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE altas (id INT AUTO_INCREMENT NOT NULL, fecha_alta DATE NOT NULL, estado VARCHAR(255) NOT NULL, id_admin INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE alumnos (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, fecha_nacimiento DATE NOT NULL, aula_codigo INT NOT NULL, tutor_legal_codigo INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE aula (id INT AUTO_INCREMENT NOT NULL, aula_nombre VARCHAR(255) NOT NULL, profesor_nombre VARCHAR(255) NOT NULL, id_profesor INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE bajas (id INT AUTO_INCREMENT NOT NULL, fecha_baja DATE NOT NULL, estado VARCHAR(255) NOT NULL, id_admin INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE incidencias (id INT AUTO_INCREMENT NOT NULL, incidencia VARCHAR(255) DEFAULT NULL, registro_codigo INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE necesidades_apoyo_ed (id INT AUTO_INCREMENT NOT NULL, necesidad_apoyo VARCHAR(255) NOT NULL, alumno_codigo INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE profesor (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE registro_diario (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, comida VARCHAR(255) DEFAULT NULL, alumno_codigo INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE telefonos (id INT AUTO_INCREMENT NOT NULL, telefono VARCHAR(24) NOT NULL, id_usuario INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tutor_legal (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE usuarios (id INT AUTO_INCREMENT NOT NULL, rol VARCHAR(255) NOT NULL, contrasenya VARCHAR(8) NOT NULL, correo_electronico VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE administracion');
        $this->addSql('DROP TABLE administrador');
        $this->addSql('DROP TABLE alergias');
        $this->addSql('DROP TABLE altas');
        $this->addSql('DROP TABLE alumnos');
        $this->addSql('DROP TABLE aula');
        $this->addSql('DROP TABLE bajas');
        $this->addSql('DROP TABLE incidencias');
        $this->addSql('DROP TABLE necesidades_apoyo_ed');
        $this->addSql('DROP TABLE profesor');
        $this->addSql('DROP TABLE registro_diario');
        $this->addSql('DROP TABLE telefonos');
        $this->addSql('DROP TABLE tutor_legal');
        $this->addSql('DROP TABLE usuarios');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
