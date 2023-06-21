<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230621231828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Required user first and last name';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE fos_user_user SET firstname = COALESCE(firstname, 'имя не задано'), lastname = COALESCE(lastname, 'фамилия не задана')");
        $this->addSql('ALTER TABLE fos_user_user CHANGE firstname firstname VARCHAR(255) NOT NULL, CHANGE lastname lastname VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fos_user_user CHANGE lastname lastname VARCHAR(255) DEFAULT NULL, CHANGE firstname firstname VARCHAR(255) DEFAULT NULL');
    }
}
