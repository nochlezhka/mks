<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210507142410 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * add new field to detect whether notice is manual or not
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `notice` ADD `manual` TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE `shelter_history` ADD `contact_saved` TINYINT(1) DEFAULT 0');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `notice` DROP `manual`');
        $this->addSql('ALTER TABLE `shelter_history` DROP `contact_saved`');
    }
}
