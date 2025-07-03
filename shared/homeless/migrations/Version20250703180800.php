<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250703RemoveNoticeUserUnique extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove incorrect unique constraint on notice_user.notice_id for proper ManyToMany mapping';
    }

    public function up(Schema $schema): void
    {
        // For MySQL
        $this->addSql('ALTER TABLE notice_user DROP INDEX UNIQ_DFD1E3B87D540AB');
    }

    public function down(Schema $schema): void
    {
        // Revert the change by re-adding the unique constraint (if needed)
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DFD1E3B87D540AB ON notice_user (notice_id)');
    }
}
