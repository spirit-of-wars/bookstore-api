<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201021084949 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE common.product ALTER life_cycle_status TYPE VARCHAR(25)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE common.product ALTER life_cycle_status TYPE INT');
    }
}
