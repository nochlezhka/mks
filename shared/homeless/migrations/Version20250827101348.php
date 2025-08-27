<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250827101348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Non unique notice_user.notice_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE notice_user DROP INDEX UNIQ_DFD1E3B87D540AB, ADD INDEX IDX_DFD1E3B87D540AB (notice_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE notice_user DROP INDEX IDX_DFD1E3B87D540AB, ADD UNIQUE INDEX UNIQ_DFD1E3B87D540AB (notice_id)
        SQL);
    }
}
