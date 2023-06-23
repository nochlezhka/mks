<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230413182237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove user groups';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fos_user_group DROP FOREIGN KEY FK_583D1F3E896DBBDE');
        $this->addSql('ALTER TABLE fos_user_group DROP FOREIGN KEY FK_583D1F3EB03A8386');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447A76ED395');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447FE54D947');
        $this->addSql('DROP TABLE fos_user_group');
        $this->addSql('DROP TABLE fos_user_user_group');

        $this->addSql("UPDATE fos_user_user fuu SET fuu.roles = if(locate('ROLE_SUPER_ADMIN', fuu.roles) = 0, 'a:0:{}', 'a:1:{i:0;s:16:\"ROLE_SUPER_ADMIN\";}')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE fos_user_group (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, roles LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci` COMMENT \'(DC2Type:array)\', sync_id INT DEFAULT NULL, sort INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, code VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, UNIQUE INDEX UNIQ_583D1F3E5E237E06 (name), INDEX IDX_583D1F3EB03A8386 (created_by_id), INDEX IDX_583D1F3E896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE fos_user_group ADD CONSTRAINT FK_583D1F3E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES fos_user_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE fos_user_group ADD CONSTRAINT FK_583D1F3EB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_user_group (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
