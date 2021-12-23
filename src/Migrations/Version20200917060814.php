<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200917060814 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE util_rel.product_essence__essence__resource (essence_id INT NOT NULL, resource_id INT NOT NULL, PRIMARY KEY(essence_id, resource_id))');
        $this->addSql('CREATE INDEX IDX_97EDD6FD9FAFD5C7 ON util_rel.product_essence__essence__resource (essence_id)');
        $this->addSql('CREATE INDEX IDX_97EDD6FD89329D25 ON util_rel.product_essence__essence__resource (resource_id)');
        $this->addSql('CREATE TABLE util_rel.product__resource (product_id INT NOT NULL, resource_id INT NOT NULL, PRIMARY KEY(product_id, resource_id))');
        $this->addSql('CREATE INDEX IDX_2AEDECB24584665A ON util_rel.product__resource (product_id)');
        $this->addSql('CREATE INDEX IDX_2AEDECB289329D25 ON util_rel.product__resource (resource_id)');
        $this->addSql('CREATE TABLE common.resource (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, assigment VARCHAR(255) DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, format VARCHAR(255) DEFAULT NULL, res_properties TEXT DEFAULT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN common.resource.res_properties IS \'(DC2Type:dict)\'');
        $this->addSql('CREATE TABLE util_rel.resource__virtual_page_resource__banner (resource_id INT NOT NULL, banner_id INT NOT NULL, PRIMARY KEY(resource_id, banner_id))');
        $this->addSql('CREATE INDEX IDX_F78EDFEF89329D25 ON util_rel.resource__virtual_page_resource__banner (resource_id)');
        $this->addSql('CREATE INDEX IDX_F78EDFEF684EC833 ON util_rel.resource__virtual_page_resource__banner (banner_id)');

        $this->addSql('ALTER TABLE util_rel.product_essence__essence__resource ADD CONSTRAINT FK_97EDD6FD9FAFD5C7 FOREIGN KEY (essence_id) REFERENCES product_essence.essence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.product_essence__essence__resource ADD CONSTRAINT FK_97EDD6FD89329D25 FOREIGN KEY (resource_id) REFERENCES common.resource (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.product__resource ADD CONSTRAINT FK_2AEDECB24584665A FOREIGN KEY (product_id) REFERENCES common.product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.product__resource ADD CONSTRAINT FK_2AEDECB289329D25 FOREIGN KEY (resource_id) REFERENCES common.resource (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.resource__virtual_page_resource__banner ADD CONSTRAINT FK_F78EDFEF89329D25 FOREIGN KEY (resource_id) REFERENCES common.resource (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.resource__virtual_page_resource__banner ADD CONSTRAINT FK_F78EDFEF684EC833 FOREIGN KEY (banner_id) REFERENCES virtual_page_resource.banner (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE util_rel.virtual_page_resource__banner__virtual_page_resource__banner_sh');
        $this->addSql('DROP TABLE util_rel.product__product_data__source');
        $this->addSql('ALTER TABLE product_group.category ALTER name SET NOT NULL');
        $this->addSql('ALTER TABLE common.creative_partner DROP CONSTRAINT FK_D2813ECFF98F144A');
        $this->addSql('ALTER TABLE common.creative_partner ADD CONSTRAINT FK_D2813ECFF98F144A FOREIGN KEY (logo_id) REFERENCES common.resource (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE virtual_page_resource.factoid DROP CONSTRAINT FK_7D22BCE93DA5256D');
        $this->addSql('ALTER TABLE virtual_page_resource.factoid ADD CONSTRAINT FK_7D22BCE93DA5256D FOREIGN KEY (image_id) REFERENCES common.resource (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE common.virtual_page DROP CONSTRAINT FK_CC09F4E554B9D732');
        $this->addSql('ALTER TABLE common.virtual_page ADD position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE common.virtual_page ADD CONSTRAINT FK_CC09F4E554B9D732 FOREIGN KEY (icon_id) REFERENCES common.resource (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        //remove source
        $this->addSql('DROP TABLE util_rel.product_data__source__product_essence__essence');
        $this->addSql('DROP TABLE product_data.source CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE common.creative_partner DROP CONSTRAINT FK_D2813ECFF98F144A');
        $this->addSql('ALTER TABLE util_rel.product_essence__essence__resource DROP CONSTRAINT FK_97EDD6FD89329D25');
        $this->addSql('ALTER TABLE virtual_page_resource.factoid DROP CONSTRAINT FK_7D22BCE93DA5256D');
        $this->addSql('ALTER TABLE util_rel.product__resource DROP CONSTRAINT FK_2AEDECB289329D25');
        $this->addSql('ALTER TABLE util_rel.resource__virtual_page_resource__banner DROP CONSTRAINT FK_F78EDFEF89329D25');
        $this->addSql('ALTER TABLE common.virtual_page DROP CONSTRAINT FK_CC09F4E554B9D732');
        $this->addSql('CREATE TABLE util_rel.virtual_page_resource__banner__virtual_page_resource__banner_sh (banner_id INT NOT NULL, banner_shelf_id INT NOT NULL, PRIMARY KEY(banner_id, banner_shelf_id))');
        $this->addSql('CREATE INDEX idx_35b301887e2b35e3 ON util_rel.virtual_page_resource__banner__virtual_page_resource__banner_sh (banner_shelf_id)');
        $this->addSql('CREATE INDEX idx_35b30188684ec833 ON util_rel.virtual_page_resource__banner__virtual_page_resource__banner_sh (banner_id)');
        $this->addSql('CREATE TABLE util_rel.product__product_data__source (product_id INT NOT NULL, source_id INT NOT NULL, PRIMARY KEY(product_id, source_id))');
        $this->addSql('CREATE INDEX idx_752918214584665a ON util_rel.product__product_data__source (product_id)');
        $this->addSql('CREATE INDEX idx_75291821953c1c61 ON util_rel.product__product_data__source (source_id)');
        $this->addSql('ALTER TABLE util_rel.virtual_page_resource__banner__virtual_page_resource__banner_sh ADD CONSTRAINT fk_35b30188684ec833 FOREIGN KEY (banner_id) REFERENCES virtual_page_resource.banner (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.virtual_page_resource__banner__virtual_page_resource__banner_sh ADD CONSTRAINT fk_35b301887e2b35e3 FOREIGN KEY (banner_shelf_id) REFERENCES virtual_page_resource.banner_shelf (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.product__product_data__source ADD CONSTRAINT fk_752918214584665a FOREIGN KEY (product_id) REFERENCES common.product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE util_rel.product__product_data__source ADD CONSTRAINT fk_75291821953c1c61 FOREIGN KEY (source_id) REFERENCES product_data.source (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE util_rel.virtual_page_resource__banner__virtual_page_resource__banner_shelf');
        $this->addSql('DROP TABLE util_rel.product_essence__essence__resource');
        $this->addSql('DROP TABLE util_rel.product__resource');
        $this->addSql('DROP TABLE common.resource');
        $this->addSql('DROP TABLE util_rel.resource__virtual_page_resource__banner');
        $this->addSql('ALTER TABLE product_group.category ALTER name DROP NOT NULL');
        $this->addSql('ALTER TABLE common.creative_partner DROP CONSTRAINT fk_d2813ecff98f144a');
        $this->addSql('ALTER TABLE common.creative_partner ADD CONSTRAINT fk_d2813ecff98f144a FOREIGN KEY (logo_id) REFERENCES product_data.source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE virtual_page_resource.factoid DROP CONSTRAINT fk_7d22bce93da5256d');
        $this->addSql('ALTER TABLE virtual_page_resource.factoid ADD CONSTRAINT fk_7d22bce93da5256d FOREIGN KEY (image_id) REFERENCES product_data.source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE common.virtual_page DROP CONSTRAINT fk_cc09f4e554b9d732');
        $this->addSql('ALTER TABLE common.virtual_page DROP position');
        $this->addSql('ALTER TABLE common.virtual_page ADD CONSTRAINT fk_cc09f4e554b9d732 FOREIGN KEY (icon_id) REFERENCES product_data.source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
