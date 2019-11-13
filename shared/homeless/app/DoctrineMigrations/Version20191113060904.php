<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20191113060904 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            INSERT INTO `client_form`(id, created_by_id, updated_by_id, name, sync_id, sort, created_at, updated_at)
            VALUES (1,1,NULL,\'Анкета проживавшего\',NULL,100,\'2019-11-12 23:08:46\',NULL);
        ');
        $this->addSql('
            INSERT INTO `client_form_field`(id, form_id, created_by_id, updated_by_id, name, type, options, required, sync_id, sort, created_at, updated_at)
            VALUES
                (1,1,1,NULL,\'Тип\',2,\'3 месяца\r\n6 месяцев\r\n1 год\',0,NULL,100,\'2019-11-12 23:44:40\',NULL),
                (2,1,1,NULL,\'Проживает в жилом помещении\',3,NULL,0,NULL,101,\'2019-11-12 23:45:15\',NULL),
                (3,1,1,NULL,\'Тип помещения\',2,\'Снимает комнату\r\nСнимает койку\r\nСнимает квартиру\r\nУ знакомых\r\nПрямо на работе\r\nРебцентр\r\nднп, гос. учреждения\r\nСвоё жилье\r\nОбщежитие от работы\r\nДругое (б-ца, гора, сестра)\',0,NULL,102,\'2019-11-12 23:46:33\',NULL),
                (4,1,1,NULL,\'Работает?\',3,NULL,0,NULL,103,\'2019-11-12 23:46:52\',NULL),
                (5,1,1,NULL,\'Официальная работа?\',3,NULL,0,NULL,104,\'2019-11-12 23:47:34\',NULL),
                (6,1,1,NULL,\'Постоянная работа?\',3,NULL,0,NULL,105,\'2019-11-12 23:47:50\',NULL),
                (7,1,1,NULL,\'Сколько сменил работ\',2,\'Не менял\r\n1\r\n2\r\n3 и более\',0,NULL,106,\'2019-11-12 23:48:18\',NULL),
                (8,1,1,NULL,\'Причина перехода на другую работу\',2,\'Лучшие условия труда (зарплата, соцпакет, месторасположение и пр.)\r\nБолее интересная деятельность\r\nКонфликты\r\nСокращение рабочего места\',0,NULL,107,\'2019-11-12 23:48:57\',NULL),
                (9,1,1,NULL,\'Причина обращения\',2,\'Только справка\r\nГуманитарная помощь\r\nРазовая консультация\r\nСопровождение\r\nПовторное заселение\',0,NULL,108,\'2019-11-12 23:49:51\',NULL);
        ');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            DELETE FROM `client_form_field` WHERE form_id = 1;
        ');
        $this->addSql('
            DELETE FROM `client_form` WHERE id = 1;
        ');
    }
}
