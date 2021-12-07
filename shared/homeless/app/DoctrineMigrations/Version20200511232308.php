<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Создание таблицы `branches`
 */
class Version20200511232308 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE branches (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, sync_id INT DEFAULT NULL, sort INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D760D16FB03A8386 (created_by_id), INDEX IDX_D760D16F896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;');

        $this->addSql('ALTER TABLE branches ADD CONSTRAINT FK_D760D16FB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user_user (id);');
        $this->addSql('ALTER TABLE branches ADD CONSTRAINT FK_D760D16F896DBBDE FOREIGN KEY (updated_by_id) REFERENCES fos_user_user (id);');
        $this->addSql('ALTER TABLE fos_user_user ADD branch_id INT DEFAULT NULL, CHANGE updated_by_id updated_by_id INT DEFAULT NULL, CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE position_id position_id INT DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE expires_at expires_at DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE credentials_expire_at credentials_expire_at DATETIME DEFAULT NULL, CHANGE date_of_birth date_of_birth DATETIME DEFAULT NULL, CHANGE firstname firstname VARCHAR(64) DEFAULT NULL, CHANGE lastname lastname VARCHAR(64) DEFAULT NULL, CHANGE website website VARCHAR(64) DEFAULT NULL, CHANGE biography biography VARCHAR(1000) DEFAULT NULL, CHANGE gender gender VARCHAR(1) DEFAULT NULL, CHANGE locale locale VARCHAR(8) DEFAULT NULL, CHANGE timezone timezone VARCHAR(64) DEFAULT NULL, CHANGE phone phone VARCHAR(64) DEFAULT NULL, CHANGE facebook_uid facebook_uid VARCHAR(255) DEFAULT NULL, CHANGE facebook_name facebook_name VARCHAR(255) DEFAULT NULL, CHANGE facebook_data facebook_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE twitter_uid twitter_uid VARCHAR(255) DEFAULT NULL, CHANGE twitter_name twitter_name VARCHAR(255) DEFAULT NULL, CHANGE twitter_data twitter_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE gplus_uid gplus_uid VARCHAR(255) DEFAULT NULL, CHANGE gplus_name gplus_name VARCHAR(255) DEFAULT NULL, CHANGE gplus_data gplus_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE token token VARCHAR(255) DEFAULT NULL, CHANGE two_step_code two_step_code VARCHAR(255) DEFAULT NULL, CHANGE sync_id sync_id INT DEFAULT NULL, CHANGE sort sort INT DEFAULT NULL, CHANGE middlename middlename VARCHAR(255) DEFAULT NULL, CHANGE proxy_date proxy_date DATE DEFAULT NULL, CHANGE proxy_num proxy_num VARCHAR(255) DEFAULT NULL;');
        $this->addSql('ALTER TABLE fos_user_user ADD CONSTRAINT FK_C560D761DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branches (id);');
        $this->addSql('CREATE INDEX IDX_C560D761DCD6CC49 ON fos_user_user (branch_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user DROP FOREIGN KEY FK_C560D761DCD6CC49');
        $this->addSql('ALTER TABLE fos_user_user DROP branch_id');

        $this->addSql('ALTER TABLE branches DROP FOREIGN KEY FK_D760D16F896DBBDE');
        $this->addSql('ALTER TABLE branches DROP FOREIGN KEY FK_D760D16FB03A8386');
        $this->addSql('DROP TABLE branches');
    }
}
