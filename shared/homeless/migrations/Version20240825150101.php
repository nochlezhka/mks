<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240825150101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update format_date arguments';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            UPDATE certificate_type
            SET content_header_left = replace(
                  content_header_left,
                  'format_date(\'none\', \'dd . MM . YYYY\')', 'format_date(\'none\', \'dd . MM . yyyy\')'
                ),
                content_header_right = replace(
                  content_header_right,
                  'format_date(\'none\', \'dd . MM . YYYY\')', 'format_date(\'none\', \'dd . MM . yyyy\')'
                ),
                content_body_right = replace(
                  content_body_right,
                  'format_date(\'none\', \'dd . MM . YYYY\')', 'format_date(\'none\', \'dd . MM . yyyy\')'
                ),
                content_footer = replace(
                  content_footer,
                  'format_date(\'none\', \'dd . MM . YYYY\')', 'format_date(\'none\', \'dd . MM . yyyy\')'
                )
            SQL);
    }

    public function down(Schema $schema): void {}
}
