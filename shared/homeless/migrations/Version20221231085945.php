<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221231085945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make salt field nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE fos_user_user MODIFY COLUMN salt varchar(255) null");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE fos_user_user MODIFY COLUMN salt varchar(255) not null");
    }
}
