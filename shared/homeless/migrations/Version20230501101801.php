<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230501101801 extends AbstractMigration
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
                  replace(
                    replace(
                      replace(
                        content_header_left,
                        'format_date(\'Y\')', 'format_date(\'none\', \'Y\')'
                      ),
                      'format_date(\'MMMM\')', 'format_date(\'none\', \'MMMM\')'
                    ),
                    'format_date(\'d\')', 'format_date(\'none\', \'d\')'
                  ),
                  'format_date(\'dd . MM . YYYY\')', 'format_date(\'none\', \'dd . MM . YYYY\')'
                ),
                content_header_right = replace(content_header_right, 'format_date(\'dd . MM . YYYY\')', 'format_date(\'none\', \'dd . MM . YYYY\')'),
                content_body_right = replace(content_body_right, 'format_date(\'dd . MM . YYYY\')', 'format_date(\'none\', \'dd . MM . YYYY\')'),
                content_footer = replace(content_footer, 'format_date(\'dd . MM . YYYY\')', 'format_date(\'none\', \'dd . MM . YYYY\')')
            SQL);
    }

    public function down(Schema $schema): void
    {
    }
}
