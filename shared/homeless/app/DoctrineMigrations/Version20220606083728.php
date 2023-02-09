<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20220606083728 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT IGNORE INTO `service_type` (`id`, `created_by_id`, `updated_by_id`, `name`, `pay`, `document`, `sync_id`, `sort`, `created_at`, `updated_at`)
                            VALUES(39, 1, NULL, 'Средства реабилитации', NULL, NULL, NULL, 100, NOW(), NULL)");


        $this->addSql('CREATE TABLE branch_delivery_item (branch_id INT NOT NULL, delivery_item_id INT NOT NULL, INDEX IDX_F16F0F8EDCD6CC49 (branch_id), INDEX IDX_F16F0F8E1871ED80 (delivery_item_id), PRIMARY KEY(branch_id, delivery_item_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE branch_delivery_item ADD CONSTRAINT FK_F16F0F8EDCD6CC49 FOREIGN KEY (branch_id) REFERENCES branches (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE branch_delivery_item ADD CONSTRAINT FK_F16F0F8E1871ED80 FOREIGN KEY (delivery_item_id) REFERENCES delivery_item (id) ON DELETE CASCADE');

        $this->addSql('INSERT INTO branch_delivery_item(branch_id,delivery_item_id)
                        SELECT 1,id FROM delivery_item');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE branch_delivery_item');
        $this->addSql("DELETE FROM `service_type` WHERE id=39");
    }
}
