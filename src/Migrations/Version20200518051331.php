<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200518051331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA common');
        $this->addSql('CREATE SCHEMA util_rel');
        $this->addSql('CREATE SCHEMA product_data');
        $this->addSql('CREATE SCHEMA product_essence');
        $this->addSql('CREATE SCHEMA product_group');
        $this->addSql('CREATE SCHEMA product_property');
        $this->addSql('CREATE SCHEMA product_type');
        $this->addSql('CREATE SCHEMA virtual_page_resource');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SCHEMA common');
        $this->addSql('DROP SCHEMA util_rel');
        $this->addSql('DROP SCHEMA product_data');
        $this->addSql('DROP SCHEMA product_essence');
        $this->addSql('DROP SCHEMA product_group');
        $this->addSql('DROP SCHEMA product_property');
        $this->addSql('DROP SCHEMA product_type');
        $this->addSql('DROP SCHEMA virtual_page_resource');
    }
}
