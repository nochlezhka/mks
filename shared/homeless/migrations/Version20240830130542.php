<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240830130542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update format_date arguments (twig 3.12)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            UPDATE certificate_type
            SET content_header_left = replace(
                  content_header_left,
                  'format_date(\'none\',', 'format_date(pattern:'
                ),
                content_header_right = replace(
                  content_header_right,
                  'format_date(\'none\',', 'format_date(pattern:'
                ),
                content_body_right = replace(
                  content_body_right,
                  'format_date(\'none\',', 'format_date(pattern:'
                ),
                content_footer = replace(
                  content_footer,
                  'format_date(\'none\',', 'format_date(pattern:'
                )
            SQL);
    }

    public function down(Schema $schema): void {}
}
