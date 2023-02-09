<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200815154819 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE delivery_item (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, category INT NOT NULL, limit_days INT NOT NULL, sync_id INT DEFAULT NULL, sort INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_CE87ED84B03A8386 (created_by_id), INDEX IDX_CE87ED84896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, delivery_item_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, delivered_at DATE NOT NULL, sync_id INT DEFAULT NULL, sort INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3781EC1019EB6921 (client_id), INDEX IDX_3781EC101871ED80 (delivery_item_id), INDEX IDX_3781EC10B03A8386 (created_by_id), INDEX IDX_3781EC10896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE delivery_item ADD CONSTRAINT FK_CE87ED84B03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE delivery_item ADD CONSTRAINT FK_CE87ED84896DBBDE FOREIGN KEY (updated_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC1019EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC101871ED80 FOREIGN KEY (delivery_item_id) REFERENCES delivery_item (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC10B03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC10896DBBDE FOREIGN KEY (updated_by_id) REFERENCES fos_user_user (id)');

        $this->addSql("INSERT INTO delivery_item (name, category, limit_days,created_at) VALUES
  ('Футболка', 3, 30, NOW()),
  ('Майка', 3, 30,  NOW()),
  ('Джинсы', 3, 60, NOW()),
  ('Брюки', 3, 60,  NOW()),
  ('Спортивные штаны', 3, 60, NOW()),
  ('Трико', 3, 60, NOW()),
  ('Юбка' , 3, 60, NOW()),

  ('Свитер', 3, 60,  NOW()),
  ('Бадлон', 3, 60, NOW()),
  ('Кофта', 3, 60, NOW()),
  ('Рубашка' , 3, 60, NOW()),

  ('Шапка', 3, 60,  NOW()),
  ('Перчатки', 3, 60, NOW()),
  ('Кепка', 3, 60, NOW()),
  ('Ремень', 3, 60, NOW()),

  ('Нижнее бельё', 3, 7, NOW()),
  ('Носки', 3, 7, NOW()),

  ('Верхняя одежда', 3, 120, NOW()),

  ('Шампунь', 17, 30, NOW()),
  ('Мыло', 17, 30, NOW()),
  ('Крем', 17, 30, NOW()),
  ('Зубная паста', 17, 30, NOW()),
  ('Щётка', 17, 30, NOW()),

  ('Костыли/трость', 22, 180, NOW())
");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC101871ED80');
        $this->addSql('DROP TABLE delivery_item');
        $this->addSql('DROP TABLE delivery');
    }
}
