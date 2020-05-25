<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200524160538 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = "
            ALTER TABLE service_type ADD comment tinyint(1) NULL AFTER sync_id;
            ALTER TABLE service_type ADD amount tinyint(1) NULL AFTER sync_id;
        ";
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = "
            ALTER TABLE service_type DROP COLUMN comment;
            ALTER TABLE service_type DROP COLUMN amount;
        ";
        $this->addSql($sql);
    }
}
