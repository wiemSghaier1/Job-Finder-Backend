<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414105235 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE formation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rate_id_seq CASCADE');
        $this->addSql('DROP TABLE job_seeker_test');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE rate');
        $this->addSql('ALTER TABLE post ADD datepub DATE NOT NULL');
        $this->addSql('ALTER TABLE post ADD tags TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN post.tags IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE formation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rate_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE job_seeker_test (job_seeker_id INT NOT NULL, test_id INT NOT NULL, PRIMARY KEY(job_seeker_id, test_id))');
        $this->addSql('CREATE INDEX idx_1f8c1fe71e5d0459 ON job_seeker_test (test_id)');
        $this->addSql('CREATE INDEX idx_1f8c1fe7c2c5baa3 ON job_seeker_test (job_seeker_id)');
        $this->addSql('CREATE TABLE formation (id INT NOT NULL, formation_id INT DEFAULT NULL, diplome VARCHAR(255) NOT NULL, specialite VARCHAR(255) NOT NULL, date_d DATE NOT NULL, date_f DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_404021bf5200282e ON formation (formation_id)');
        $this->addSql('CREATE TABLE rate (id INT NOT NULL, rate DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE job_seeker_test ADD CONSTRAINT fk_1f8c1fe7c2c5baa3 FOREIGN KEY (job_seeker_id) REFERENCES job_seeker (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE job_seeker_test ADD CONSTRAINT fk_1f8c1fe71e5d0459 FOREIGN KEY (test_id) REFERENCES test (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT fk_404021bf5200282e FOREIGN KEY (formation_id) REFERENCES cv (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post DROP datepub');
        $this->addSql('ALTER TABLE post DROP tags');
    }
}
