<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230326133836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move group roles to users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            update fos_user_user fuu
                join fos_user_user_group fuug on fuu.id = fuug.user_id
                join fos_user_group fug on fuug.group_id = fug.id
            set fuu.roles = fug.roles
            where locate('ROLE_SUPER_ADMIN', fuu.roles) = 0
            SQL);
    }

    public function down(Schema $schema): void
    {
    }
}
