<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261106173327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alergenos (id INT AUTO_INCREMENT NOT NULL, alergeno VARCHAR(50) NOT NULL, comida_codigo INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE comida (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, menu VARCHAR(255) NOT NULL, comida_codigo INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE tutor_legal_profesor (tutor_legal_id INT NOT NULL, profesor_id INT NOT NULL, INDEX IDX_1DCC99FF390D6A49 (tutor_legal_id), INDEX IDX_1DCC99FFE52BD977 (profesor_id), PRIMARY KEY (tutor_legal_id, profesor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE tutor_legal_profesor ADD CONSTRAINT FK_1DCC99FF390D6A49 FOREIGN KEY (tutor_legal_id) REFERENCES tutor_legal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutor_legal_profesor ADD CONSTRAINT FK_1DCC99FFE52BD977 FOREIGN KEY (profesor_id) REFERENCES profesor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE altas CHANGE id_admin id_admin_id INT NOT NULL');
        $this->addSql('ALTER TABLE altas ADD CONSTRAINT FK_F5925BB734F06E85 FOREIGN KEY (id_admin_id) REFERENCES administracion (id)');
        $this->addSql('CREATE INDEX IDX_F5925BB734F06E85 ON altas (id_admin_id)');
        $this->addSql('ALTER TABLE alumnos ADD tiene_id INT NOT NULL, ADD aula_codigo_id INT NOT NULL, ADD tutor_legal_codigo_id INT NOT NULL, ADD recibe_id INT NOT NULL, DROP aula_codigo, DROP tutor_legal_codigo');
        $this->addSql('ALTER TABLE alumnos ADD CONSTRAINT FK_5EC5A6ABB8D505E3 FOREIGN KEY (tiene_id) REFERENCES registro_diario (id)');
        $this->addSql('ALTER TABLE alumnos ADD CONSTRAINT FK_5EC5A6AB7EDF0483 FOREIGN KEY (aula_codigo_id) REFERENCES aula (id)');
        $this->addSql('ALTER TABLE alumnos ADD CONSTRAINT FK_5EC5A6ABD93240F4 FOREIGN KEY (tutor_legal_codigo_id) REFERENCES tutor_legal (id)');
        $this->addSql('ALTER TABLE alumnos ADD CONSTRAINT FK_5EC5A6AB43F866F6 FOREIGN KEY (recibe_id) REFERENCES comida (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5EC5A6ABB8D505E3 ON alumnos (tiene_id)');
        $this->addSql('CREATE INDEX IDX_5EC5A6AB7EDF0483 ON alumnos (aula_codigo_id)');
        $this->addSql('CREATE INDEX IDX_5EC5A6ABD93240F4 ON alumnos (tutor_legal_codigo_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5EC5A6AB43F866F6 ON alumnos (recibe_id)');
        $this->addSql('ALTER TABLE aula DROP profesor_nombre, DROP id_profesor');
        $this->addSql('ALTER TABLE bajas CHANGE id_admin id_admin_id INT NOT NULL');
        $this->addSql('ALTER TABLE bajas ADD CONSTRAINT FK_56E077C034F06E85 FOREIGN KEY (id_admin_id) REFERENCES administracion (id)');
        $this->addSql('CREATE INDEX IDX_56E077C034F06E85 ON bajas (id_admin_id)');
        $this->addSql('ALTER TABLE profesor ADD imparte_id INT NOT NULL');
        $this->addSql('ALTER TABLE profesor ADD CONSTRAINT FK_5B7406D93D850628 FOREIGN KEY (imparte_id) REFERENCES aula (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5B7406D93D850628 ON profesor (imparte_id)');
        $this->addSql('ALTER TABLE registro_diario DROP alumno_codigo');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tutor_legal_profesor DROP FOREIGN KEY FK_1DCC99FF390D6A49');
        $this->addSql('ALTER TABLE tutor_legal_profesor DROP FOREIGN KEY FK_1DCC99FFE52BD977');
        $this->addSql('DROP TABLE alergenos');
        $this->addSql('DROP TABLE comida');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE tutor_legal_profesor');
        $this->addSql('ALTER TABLE altas DROP FOREIGN KEY FK_F5925BB734F06E85');
        $this->addSql('DROP INDEX IDX_F5925BB734F06E85 ON altas');
        $this->addSql('ALTER TABLE altas CHANGE id_admin_id id_admin INT NOT NULL');
        $this->addSql('ALTER TABLE alumnos DROP FOREIGN KEY FK_5EC5A6ABB8D505E3');
        $this->addSql('ALTER TABLE alumnos DROP FOREIGN KEY FK_5EC5A6AB7EDF0483');
        $this->addSql('ALTER TABLE alumnos DROP FOREIGN KEY FK_5EC5A6ABD93240F4');
        $this->addSql('ALTER TABLE alumnos DROP FOREIGN KEY FK_5EC5A6AB43F866F6');
        $this->addSql('DROP INDEX UNIQ_5EC5A6ABB8D505E3 ON alumnos');
        $this->addSql('DROP INDEX IDX_5EC5A6AB7EDF0483 ON alumnos');
        $this->addSql('DROP INDEX IDX_5EC5A6ABD93240F4 ON alumnos');
        $this->addSql('DROP INDEX UNIQ_5EC5A6AB43F866F6 ON alumnos');
        $this->addSql('ALTER TABLE alumnos ADD aula_codigo INT NOT NULL, ADD tutor_legal_codigo INT NOT NULL, DROP tiene_id, DROP aula_codigo_id, DROP tutor_legal_codigo_id, DROP recibe_id');
        $this->addSql('ALTER TABLE aula ADD profesor_nombre VARCHAR(255) NOT NULL, ADD id_profesor INT NOT NULL');
        $this->addSql('ALTER TABLE bajas DROP FOREIGN KEY FK_56E077C034F06E85');
        $this->addSql('DROP INDEX IDX_56E077C034F06E85 ON bajas');
        $this->addSql('ALTER TABLE bajas CHANGE id_admin_id id_admin INT NOT NULL');
        $this->addSql('ALTER TABLE profesor DROP FOREIGN KEY FK_5B7406D93D850628');
        $this->addSql('DROP INDEX UNIQ_5B7406D93D850628 ON profesor');
        $this->addSql('ALTER TABLE profesor DROP imparte_id');
        $this->addSql('ALTER TABLE registro_diario ADD alumno_codigo INT NOT NULL');
    }
}
