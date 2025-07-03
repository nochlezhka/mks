<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250703224455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unnecessary unique index from notice_user.notice_id';
    }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notice_user DROP FOREIGN KEY FK_DFD1E3B87D540AB');
        $this->addSql('DROP INDEX UNIQ_DFD1E3B87D540AB ON notice_user');
        $this->addSql('ALTER TABLE notice_user ADD CONSTRAINT FK_DFD1E3B87D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notice_user DROP FOREIGN KEY FK_DFD1E3B87D540AB');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DFD1E3B87D540AB ON notice_user (notice_id)');
        $this->addSql('ALTER TABLE notice_user ADD CONSTRAINT FK_DFD1E3B87D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
    }
}

