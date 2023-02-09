<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20221114115621 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shelter_leaving_reason (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, sync_id INT DEFAULT NULL, sort INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5835C6BBB03A8386 (created_by_id), INDEX IDX_5835C6BB896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shelter_leaving_reason ADD CONSTRAINT FK_5835C6BBB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE shelter_leaving_reason ADD CONSTRAINT FK_5835C6BB896DBBDE FOREIGN KEY (updated_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE shelter_history CHANGE COLUMN leaving_reason leaving_reason_id int(11)');


        $this->addSql("INSERT INTO shelter_leaving_reason(id,name) VALUES (1,'Съехал в съемную комнату (поддерживаемое проживание)'),
  (2,'Съехал в съемную комнату или хостел'),
  (3,'Выселен за нарушение правил'),
  (4,'Покинул приют по собственному желанию'),
  (5,'Умер'),
  (6,'Съехал на работу с проживанием'),
  (7,'Уехал в другой город'),
  (8,'Съехал к родственникам друзьям'),
  (9,'Съехал в интернат или в гос.стационар для людей с инвалидностью'),
  (10,'Съехал в наш приют для пожилых'),
  (11,'Съехал в ДНП или в другие приюты')");


        $this->addSql('ALTER TABLE shelter_history ADD CONSTRAINT FK_D04221A04325C869 FOREIGN KEY (leaving_reason_id) REFERENCES shelter_leaving_reason (id)');
        $this->addSql('CREATE INDEX IDX_D04221A04325C869 ON shelter_history (leaving_reason_id)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shelter_history DROP FOREIGN KEY FK_D04221A04325C869');
        $this->addSql('ALTER TABLE shelter_history DROP INDEX IDX_D04221A04325C869');
        $this->addSql('DROP TABLE shelter_leaving_reason');
    }
}
