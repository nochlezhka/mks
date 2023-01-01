<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200524160538 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $sql = "
            ALTER TABLE service_type ADD comment tinyint(1) NULL AFTER sync_id;
            ALTER TABLE service_type ADD amount tinyint(1) NULL AFTER sync_id;
        ";
        $this->addSql($sql);

        $this->addSql("UPDATE `service_type` set amount=1 where id in (14,16);");
        $this->addSql("UPDATE `service_type` set comment=1 where id in (3,4,17);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $sql = "
            ALTER TABLE service_type DROP COLUMN comment;
            ALTER TABLE service_type DROP COLUMN amount;
        ";
        $this->addSql($sql);
    }
}
