<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181203190521 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("update fos_user_group SET roles = 'a:30:{i:0;s:17:\"ROLE_SONATA_ADMIN\";i:1;s:26:\"ROLE_APP_CLIENT_ADMIN_EDIT\";i:2;s:26:\"ROLE_APP_CLIENT_ADMIN_LIST\";i:3;s:28:\"ROLE_APP_CLIENT_ADMIN_CREATE\";i:4;s:26:\"ROLE_APP_CLIENT_ADMIN_VIEW\";i:5;s:28:\"ROLE_APP_CLIENT_ADMIN_EXPORT\";i:6;s:31:\"ROLE_APP_CLIENT_FIELD_ADMIN_ALL\";i:7;s:38:\"ROLE_APP_CLIENT_FIELD_OPTION_ADMIN_ALL\";i:8;s:23:\"ROLE_APP_NOTE_ADMIN_ALL\";i:9;s:26:\"ROLE_APP_SERVICE_ADMIN_ALL\";i:10;s:25:\"ROLE_APP_REGION_ADMIN_ALL\";i:11;s:27:\"ROLE_APP_DISTRICT_ADMIN_ALL\";i:12;s:27:\"ROLE_APP_DOCUMENT_ADMIN_ALL\";i:13;s:32:\"ROLE_APP_DOCUMENT_FILE_ADMIN_ALL\";i:16;s:27:\"ROLE_APP_CONTRACT_ADMIN_ALL\";i:17;s:32:\"ROLE_APP_CONTRACT_ITEM_ADMIN_ALL\";i:18;s:34:\"ROLE_APP_SHELTER_HISTORY_ADMIN_ALL\";i:19;s:30:\"ROLE_APP_CERTIFICATE_ADMIN_ALL\";i:20;s:37:\"ROLE_APP_GENERATED_DOCUMENT_ADMIN_ALL\";i:21;s:25:\"ROLE_APP_NOTICE_ADMIN_ALL\";i:22;s:31:\"ROLE_APP_SERVICE_TYPE_ADMIN_ALL\";i:23;s:37:\"ROLE_APP_CONTRACT_ITEM_TYPE_ADMIN_ALL\";i:24;s:42:\"ROLE_APP_GENERATED_DOCUMENT_TYPE_ADMIN_ALL\";i:25;s:48:\"ROLE_APP_GENERATED_DOCUMENT_START_TEXT_ADMIN_ALL\";i:26;s:46:\"ROLE_APP_GENERATED_DOCUMENT_END_TEXT_ADMIN_ALL\";i:27;s:35:\"ROLE_APP_CERTIFICATE_TYPE_ADMIN_ALL\";i:28;s:32:\"ROLE_SONATA_USER_ADMIN_USER_EDIT\";i:29;s:28:\"ROLE_APP_MENU_ITEM_ADMIN_ALL\";i:30;s:35:\"ROLE_APP_HISTORY_DOWNLOAD_ADMIN_ALL\";i:31;s:41:\"ROLE_APP_RESIDENT_QUESTIONNAIRE_ADMIN_ALL\";}' WHERE name = 'Сотрудники'");

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
