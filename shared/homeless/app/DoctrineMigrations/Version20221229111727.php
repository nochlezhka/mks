<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20221229111727 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `service_type` (`id`, `created_by_id`, `updated_by_id`, `name`, `pay`, `document`, `sync_id`, `sort`, `created_at`, `updated_at`)
VALUES
	(45, 1, NULL, 'Посуда', NULL, NULL, NULL, 100, '2022-06-06 09:44:08', NULL),
	(46, 1, NULL, 'Подарок', NULL, NULL, NULL, 100, '2022-06-06 09:44:08', NULL);
");

        $this->addSql("UPDATE service_type SET name='Аксессуары' WHERE id=22");
        $this->addSql("UPDATE service_type SET name='Костыли/трости' WHERE id=39");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
