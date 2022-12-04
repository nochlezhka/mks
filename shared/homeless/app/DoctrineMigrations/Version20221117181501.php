<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221117181501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE client_field_option SET name='Общается' WHERE id=21");
        $this->addSql("UPDATE client_field_option SET name='Не общается' WHERE id=22");
        $this->addSql("UPDATE client_field_option SET name='Мошенничество/Вымогательство (с жильём)' WHERE id=29");
        $this->addSql("UPDATE client_field_option SET name='Переезд в поисках работы' WHERE id=34");
        $this->addSql("UPDATE client_field_option SET name='Желание путешествовать/переехать в другой город' WHERE id=35");
        $this->addSql("UPDATE client_field_option SET name='Рабочие дома' WHERE id=56");
        $this->addSql("UPDATE client_field_option SET name='Хостел/общежитие' WHERE id=74");
        $this->addSql("UPDATE client_field_option SET name='Рабочий дом' WHERE id=75");
        $this->addSql("UPDATE client_field_option SET name='Рабочие дома' WHERE id=93");
        $this->addSql("INSERT INTO client_field_option VALUE (942,16,null,null, 'Украли/обманули на крупную сумму денег',0,null,100,'2022-11-17 20:22:38','2022-11-17 20:22:38')");
        $this->addSql("INSERT INTO client_field_option VALUE (943,16,null,null, 'Утрата документов, удостоверяющих личность',0,null,100,'2022-11-17 20:22:38','2022-11-17 20:22:38')");
        $this->addSql("INSERT INTO client_field_option VALUE (944,16,null,null, 'Обманул работодатель',0,null,100,'2022-11-17 20:22:38','2022-11-17 20:22:38')");
        $this->addSql("INSERT INTO client_field_option VALUE (945,16,null,null, 'Военные действия',0,null,100,'2022-11-17 20:22:38','2022-11-17 20:22:38')");
        $this->addSql("DELETE FROM client_field WHERE id=34");
        $this->addSql("UPDATE client_field SET sort=4 WHERE id=24");
        $this->addSql("UPDATE client_field SET sort=5 WHERE id=25");
        $this->addSql("UPDATE client_field SET sort=6 WHERE id=26");
        $this->addSql("UPDATE client_field SET sort=7 WHERE id=27");
        $this->addSql("UPDATE client_field SET sort=8 WHERE id=9");
        $this->addSql("UPDATE client_field SET sort=9 WHERE id=2");
        $this->addSql("UPDATE client_field SET sort=10 WHERE id=14");
        $this->addSql("UPDATE client_field SET sort=11 WHERE id=4");
        $this->addSql("UPDATE client_field SET sort=12 WHERE id=5");
        $this->addSql("UPDATE client_field SET sort=13 WHERE id=13");
        $this->addSql("UPDATE client_field SET sort=14 WHERE id=32");
        $this->addSql("UPDATE client_field SET sort=15 WHERE id=33");
        $this->addSql("UPDATE client_field SET sort=16 WHERE id=6");
        $this->addSql("UPDATE client_field SET sort=17 WHERE id=7");
        $this->addSql("UPDATE client_field SET sort=18 WHERE id=8");
        $this->addSql("UPDATE client_field SET sort=19 WHERE id=28");
        $this->addSql("UPDATE client_field SET sort=20 WHERE id=29");
        $this->addSql("UPDATE client_field SET sort=21 WHERE id=16");
        $this->addSql("UPDATE client_field SET sort=22 WHERE id=17");
        $this->addSql("UPDATE client_field SET sort=23 WHERE id=20");
        $this->addSql("UPDATE client_field SET sort=24 WHERE id=21");
        $this->addSql("UPDATE client_field SET sort=25 WHERE id=22");
        $this->addSql("UPDATE client_field SET sort=26 WHERE id=18");
        $this->addSql("UPDATE client_field SET sort=27 WHERE id=19");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE client_field_option SET name='Видится' WHERE id=21");
        $this->addSql("UPDATE client_field_option SET name='Не видится' WHERE id=22");
        $this->addSql("UPDATE client_field_option SET name='Мошенничество/Вымогательство' WHERE id=29");
        $this->addSql("UPDATE client_field_option SET name='Трудовая миграция' WHERE id=34");
        $this->addSql("UPDATE client_field_option SET name='Беспричинно потянуло странствовать' WHERE id=35");
        $this->addSql("UPDATE client_field_option SET name='Ребцентры' WHERE id=56");
        $this->addSql("UPDATE client_field_option SET name='Общежитие' WHERE id=74");
        $this->addSql("UPDATE client_field_option SET name='Ребцентр' WHERE id=75");
        $this->addSql("UPDATE client_field_option SET name='Ребцентры' WHERE id=93");
        $this->addSql("DELETE FROM client_field_option where id=942");
        $this->addSql("DELETE FROM client_field_option where id=943");
        $this->addSql("DELETE FROM client_field_option where id=944");
        $this->addSql("DELETE FROM client_field_option where id=945");
        $this->addSql("INSERT INTO client_field VALUE (34,1,NULL,'Недавно освободился (менее 3 мес.)','nedavnoismls',1,2,0,0,NULL,NULL,11,'2017-11-22 16:50:09',NULL)");
        $this->addSql("INSERT INTO client_field_option VALUE (485,34,1,NULL,'Да',0,NULL,100,'2017-11-22 16:50:43',NULL)");
    }
}
