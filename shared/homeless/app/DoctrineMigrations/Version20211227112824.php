<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20211227112824 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `client_field` (`id`, `created_by_id`, `name`, `code`, `enabled`, `type`, `required`, `multiple`, `sort`, `created_at`, `updated_at`) VALUES (36,NULL,'Последняя регистрация в РФ','lastRfResidence',1,2,0,0, 101, current_timestamp(),current_timestamp()), (37, NUll, 'Регион', 'geoRegion', 1,2,0,0,102, current_timestamp(), current_timestamp())");

        $this->addSql("INSERT INTO `client_field_option` (`field_id`, `created_by_id`, `name`, `not_single`, `sort`, `created_at`, `updated_at`) VALUES
        (36, 1, 'Да', 0, 1, current_timestamp(), NULL),
        (36, 1, 'Нет', 0, 2, current_timestamp(), NULL),
        (37, 1, 'Адыгея', 0, 1, current_timestamp(), NULL),
        (37, 1, 'Алтай', 0, 2, current_timestamp(), NULL),
        (37, 1, 'Алтайский край', 0, 3, current_timestamp(), NULL),
        (37, 1, 'Амурская область', 0, 4, current_timestamp(), NULL),
        (37, 1, 'Архангельская область', 0, 5, current_timestamp(), NULL),
        (37, 1, 'Астраханская область', 0, 6, current_timestamp(), NULL),
        (37, 1, 'Башкортостан', 0, 7, current_timestamp(), NULL),
        (37, 1, 'Белгородская область', 0, 8, current_timestamp(), NULL),
        (37, 1, 'Брянская область', 0, 9, current_timestamp(), NULL),
        (37, 1, 'Бурятия', 0, 10, current_timestamp(), NULL),
        (37, 1, 'Владимирская область', 0, 11, current_timestamp(), NULL),
        (37, 1, 'Волгоградская область', 0, 12, current_timestamp(), NULL),
        (37, 1, 'Вологодская область', 0, 13, current_timestamp(), NULL),
        (37, 1, 'Воронежская область', 0, 14, current_timestamp(), NULL),
        (37, 1, 'Дагестан', 0, 15, current_timestamp(), NULL),
        (37, 1, 'Еврейская АО', 0, 16, current_timestamp(), NULL),
        (37, 1, 'Забайкальский край', 0, 17, current_timestamp(), NULL),
        (37, 1, 'Ивановская область', 0, 18, current_timestamp(), NULL),
        (37, 1, 'Ингушетия', 0, 19, current_timestamp(), NULL),
        (37, 1, 'Иркутская область', 0, 20, current_timestamp(), NULL),
        (37, 1, 'Кабардино-Балкария', 0, 21, current_timestamp(), NULL),
        (37, 1, 'Калининградская область', 0, 22, current_timestamp(), NULL),
        (37, 1, 'Калмыкия', 0, 23, current_timestamp(), NULL),
        (37, 1, 'Калужская область', 0, 24, current_timestamp(), NULL),
        (37, 1, 'Камчатский край', 0, 25, current_timestamp(), NULL),
        (37, 1, 'Карачаево-Черкессия', 0, 26, current_timestamp(), NULL),
        (37, 1, 'Карелия', 0, 27, current_timestamp(), NULL),
        (37, 1, 'Кемеровская область', 0, 28, current_timestamp(), NULL),
        (37, 1, 'Кировская область', 0, 29, current_timestamp(), NULL),
        (37, 1, 'Коми', 0, 30, current_timestamp(), NULL),
        (37, 1, 'Костромская область', 0, 31, current_timestamp(), NULL),
        (37, 1, 'Краснодарский край', 0, 32, current_timestamp(), NULL),
        (37, 1, 'Красноярский край', 0, 33, current_timestamp(), NULL),
        (37, 1, 'Курганская область', 0, 34, current_timestamp(), NULL),
        (37, 1, 'Курская область', 0, 35, current_timestamp(), NULL),
        (37, 1, 'Ленинградская область', 0, 36, current_timestamp(), NULL),
        (37, 1, 'Липецкая область', 0, 37, current_timestamp(), NULL),
        (37, 1, 'Магаданская область', 0, 38, current_timestamp(), NULL),
        (37, 1, 'Марий Эл', 0, 39, current_timestamp(), NULL),
        (37, 1, 'Мордовия', 0, 40, current_timestamp(), NULL),
        (37, 1, 'Москва', 0, 41, current_timestamp(), NULL),
        (37, 1, 'Московская область', 0, 42, current_timestamp(), NULL),
        (37, 1, 'Мурманская область', 0, 43, current_timestamp(), NULL),
        (37, 1, 'Ненецкий АО', 0, 44, current_timestamp(), NULL),
        (37, 1, 'Нижегородская область', 0, 45, current_timestamp(), NULL),
        (37, 1, 'Новгородская область', 0, 46, current_timestamp(), NULL),
        (37, 1, 'Новосибирская область', 0, 47, current_timestamp(), NULL),
        (37, 1, 'Омская область', 0, 48, current_timestamp(), NULL),
        (37, 1, 'Оренбургская область', 0, 49, current_timestamp(), NULL),
        (37, 1, 'Орловская область', 0, 50, current_timestamp(), NULL),
        (37, 1, 'Пензенская область', 0, 51, current_timestamp(), NULL),
        (37, 1, 'Пермский край', 0, 52, current_timestamp(), NULL),
        (37, 1, 'Приморский край', 0, 53, current_timestamp(), NULL),
        (37, 1, 'Псковская область', 0, 54, current_timestamp(), NULL),
        (37, 1, 'Ростовская область', 0, 55, current_timestamp(), NULL),
        (37, 1, 'Рязанская область', 0, 56, current_timestamp(), NULL),
        (37, 1, 'Самарская область', 0, 57, current_timestamp(), NULL),
        (37, 1, 'Санкт-Петербург', 0, 58, current_timestamp(), NULL),
        (37, 1, 'Саратовская область', 0, 59, current_timestamp(), NULL),
        (37, 1, 'Саха (Якутия)', 0, 60, current_timestamp(), NULL),
        (37, 1, 'Сахалинская область', 0, 61, current_timestamp(), NULL),
        (37, 1, 'Свердловская область', 0, 62, current_timestamp(), NULL),
        (37, 1, 'Северная Осетия - Алания', 0,63, current_timestamp(), NULL),
        (37, 1, 'Смоленская область', 0, 64, current_timestamp(), NULL),
        (37, 1, 'Ставропольский край', 0, 65, current_timestamp(), NULL),
        (37, 1, 'Тамбовская область', 0, 66, current_timestamp(), NULL),
        (37, 1, 'Татарстан', 0, 67, current_timestamp(), NULL),
        (37, 1, 'Тверская область', 0, 68, current_timestamp(), NULL),
        (37, 1, 'Томская область', 0, 69, current_timestamp(), NULL),
        (37, 1, 'Тульская область', 0, 70, current_timestamp(), NULL),
        (37, 1, 'Тыва', 0, 71, current_timestamp(), NULL),
        (37, 1, 'Тюменская область', 0, 72, current_timestamp(), NULL),
        (37, 1, 'Удмуртия', 0, 73, current_timestamp(), NULL),
        (37, 1, 'Ульяновская область', 0, 74, current_timestamp(), NULL),
        (37, 1, 'Хабаровский край', 0, 75, current_timestamp(), NULL),
        (37, 1, 'Хакасия', 0, 76, current_timestamp(), NULL),
        (37, 1, 'Ханты-Мансийский АО - Югра', 0, 77, current_timestamp(), NULL),
        (37, 1, 'Челябинская область', 0, 78, current_timestamp(), NULL),
        (37, 1, 'Чеченская республика', 0, 79, current_timestamp(), NULL),
        (37, 1, 'Чувашская республика', 0, 80, current_timestamp(), NULL),
        (37, 1, 'Чукотский АО', 0, 81, current_timestamp(), NULL),
        (37, 1, 'Ямало-Ненецкий АО', 0, 82, current_timestamp(), NULL),
        (37, 1, 'Ярославская область', 0, 83, current_timestamp(), NULL),
        (37, 1, 'Крым', 0, 84, current_timestamp(), NULL),
        (37, 1, 'Севастополь', 0, 85, current_timestamp(), NULL)
        ");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM `client_field_value` WHERE `field_id` = 36');
        $this->addSql('DELETE FROM `client_field_value` WHERE `field_id` = 37');

        $this->addSql('DELETE FROM `client_field_option` WHERE `field_id` = 36');
        $this->addSql('DELETE FROM `client_field_option` WHERE `field_id` = 37');

        $this->addSql('DELETE FROM `client_field` WHERE `id` = 36');
        $this->addSql('DELETE FROM `client_field` WHERE `id` = 37');
    }
}
