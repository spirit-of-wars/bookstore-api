<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519053251 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA auth');
        $this->addSql('CREATE TABLE common.users (id SERIAL NOT NULL, email VARCHAR(255) DEFAULT NULL, is_confirmed BOOLEAN DEFAULT NULL, is_active BOOLEAN DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, spent INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE auth.confirm_link (id SERIAL NOT NULL, refresh_token_id INT DEFAULT NULL, access_token_id INT DEFAULT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, is_activated BOOLEAN DEFAULT NULL, expire TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_338A0260F765F60E ON auth.confirm_link (refresh_token_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_338A02602CCB2688 ON auth.confirm_link (access_token_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_338A0260A76ED395 ON auth.confirm_link (user_id)');
        $this->addSql('CREATE TABLE auth.refresh_token (id SERIAL NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, activated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, expire TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B7BCAFB6A76ED395 ON auth.refresh_token (user_id)');
        $this->addSql('CREATE TABLE auth.access_token (id SERIAL NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, activated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, expire TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FD8716DA76ED395 ON auth.access_token (user_id)');
        $this->addSql('CREATE TABLE auth.role (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE util_rel.auth__role__user (role_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(role_id, user_id))');
        $this->addSql('CREATE INDEX IDX_427B6D3DD60322AC ON util_rel.auth__role__user (role_id)');
        $this->addSql('CREATE INDEX IDX_427B6D3DA76ED395 ON util_rel.auth__role__user (user_id)');
        $this->addSql('ALTER TABLE auth.confirm_link ADD CONSTRAINT FK_338A0260F765F60E FOREIGN KEY (refresh_token_id) REFERENCES auth.refresh_token (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.confirm_link ADD CONSTRAINT FK_338A02602CCB2688 FOREIGN KEY (access_token_id) REFERENCES auth.access_token (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.confirm_link ADD CONSTRAINT FK_338A0260A76ED395 FOREIGN KEY (user_id) REFERENCES common.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.refresh_token ADD CONSTRAINT FK_B7BCAFB6A76ED395 FOREIGN KEY (user_id) REFERENCES common.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth.access_token ADD CONSTRAINT FK_2FD8716DA76ED395 FOREIGN KEY (user_id) REFERENCES common.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.auth__role__user ADD CONSTRAINT FK_427B6D3DD60322AC FOREIGN KEY (role_id) REFERENCES auth.role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.auth__role__user ADD CONSTRAINT FK_427B6D3DA76ED395 FOREIGN KEY (user_id) REFERENCES common.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('INSERT INTO common.users(email, is_confirmed, is_active, firstname, surname, spent, created_at, updated_at)VALUES(\'guest@guest.loc\', true, true, null, null, 0, \'2020-05-18 12:00:00\', \'2020-05-18 12:00:00\')');
        $this->addSql('INSERT INTO auth.role(name, created_at, updated_at)VALUES(\'super_admin\', \'2020-05-18 12:00:00\', \'2020-05-18 12:00:00\')');
        $this->addSql('INSERT INTO auth.role(name, created_at, updated_at)VALUES(\'admin\', \'2020-05-18 12:00:00\', \'2020-05-18 12:00:00\')');
        $this->addSql('INSERT INTO auth.role(name, created_at, updated_at)VALUES(\'client\', \'2020-05-18 12:00:00\', \'2020-05-18 12:00:00\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE auth.confirm_link DROP CONSTRAINT FK_338A0260A76ED395');
        $this->addSql('ALTER TABLE auth.refresh_token DROP CONSTRAINT FK_B7BCAFB6A76ED395');
        $this->addSql('ALTER TABLE auth.access_token DROP CONSTRAINT FK_2FD8716DA76ED395');
        $this->addSql('ALTER TABLE util_rel.auth__role__user DROP CONSTRAINT FK_427B6D3DA76ED395');
        $this->addSql('ALTER TABLE auth.confirm_link DROP CONSTRAINT FK_338A0260F765F60E');
        $this->addSql('ALTER TABLE auth.confirm_link DROP CONSTRAINT FK_338A02602CCB2688');
        $this->addSql('ALTER TABLE util_rel.auth__role__user DROP CONSTRAINT FK_427B6D3DD60322AC');
        $this->addSql('DROP TABLE common.users');
        $this->addSql('DROP TABLE auth.confirm_link');
        $this->addSql('DROP TABLE auth.refresh_token');
        $this->addSql('DROP TABLE auth.access_token');
        $this->addSql('DROP TABLE auth.role');
        $this->addSql('DROP TABLE util_rel.auth__role__user');
        $this->addSql('DROP SCHEMA auth');
    }
}
