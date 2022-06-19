<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220619051513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop user fields that are no longer used';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fos_user_user DROP locked, DROP expired, DROP expires_at, DROP credentials_expired, DROP credentials_expire_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fos_user_user ADD locked TINYINT(1) NOT NULL, ADD expired TINYINT(1) NOT NULL, ADD expires_at DATETIME DEFAULT NULL, ADD credentials_expired TINYINT(1) NOT NULL, ADD credentials_expire_at DATETIME DEFAULT NULL');
    }
}
