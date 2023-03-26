<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181203012632 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO fos_user_group (name, roles, code) VALUES ('Волонтёр', 'a:6:{i:0;s:17:\"ROLE_SONATA_ADMIN\";i:1;s:26:\"ROLE_APP_CLIENT_ADMIN_LIST\";i:2;s:26:\"ROLE_APP_CLIENT_ADMIN_VIEW\";i:3;s:26:\"ROLE_APP_SERVICE_ADMIN_ALL\";i:4;s:32:\"ROLE_APP_SERVICE_TYPE_ADMIN_LIST\";i:5;s:32:\"ROLE_SONATA_USER_ADMIN_USER_VIEW\";}', 'volontery')");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
