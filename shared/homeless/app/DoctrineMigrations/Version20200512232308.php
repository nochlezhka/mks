<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200512232308 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE branches ADD org_name_short VARCHAR(255) DEFAULT NULL, ADD org_name VARCHAR(255) DEFAULT NULL, ADD org_description VARCHAR(255) DEFAULT NULL, ADD org_description_short VARCHAR(255) DEFAULT NULL, ADD org_city VARCHAR(255) DEFAULT NULL, ADD org_contacts_full LONGTEXT DEFAULT NULL, ADD dispensary_name VARCHAR(255) DEFAULT NULL, ADD dispensary_address VARCHAR(255) DEFAULT NULL, ADD dispensary_phone VARCHAR(255) DEFAULT NULL, ADD employment_name VARCHAR(255) DEFAULT NULL, ADD employment_address VARCHAR(255) DEFAULT NULL, ADD employment_inspection VARCHAR(255) DEFAULT NULL, ADD sanitation_name VARCHAR(255) DEFAULT NULL, ADD sanitation_address VARCHAR(255) DEFAULT NULL, ADD sanitation_time VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE branches DROP org_name_short, DROP org_name, DROP org_description, DROP org_description_short, DROP org_city, DROP org_contacts_full, DROP dispensary_name, DROP dispensary_address, DROP dispensary_phone, DROP employment_name, DROP employment_address, DROP employment_inspection, DROP sanitation_name, DROP sanitation_address, DROP sanitation_time');
    }
}
