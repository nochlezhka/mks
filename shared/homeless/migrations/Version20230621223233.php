<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230621223233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove meta and outdated resident_questionnaire';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE meta');
        $this->addSql('ALTER TABLE resident_questionnaire DROP FOREIGN KEY FK_D177694B19EB6921');
        $this->addSql('DROP TABLE resident_questionnaire');
        $this->addSql('DROP INDEX UNIQ_F8F7B48B60D093B1 ON client_form_response');
        $this->addSql('ALTER TABLE client_form_response DROP resident_questionnaire_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE meta (id INT AUTO_INCREMENT NOT NULL, `key` VARCHAR(32) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, value LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, UNIQUE INDEX UNIQ_D7F214354E645A7E (`key`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE resident_questionnaire (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, type_id INT DEFAULT NULL, is_dwelling TINYINT(1) NOT NULL, room_type_id INT DEFAULT NULL, is_work TINYINT(1) NOT NULL, is_work_official TINYINT(1) NOT NULL, is_work_constant TINYINT(1) NOT NULL, changed_jobs_count_id INT DEFAULT NULL, reason_for_transition_ids LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, reason_for_petition_ids LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_D177694B19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE resident_questionnaire ADD CONSTRAINT FK_D177694B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_form_response ADD resident_questionnaire_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F8F7B48B60D093B1 ON client_form_response (resident_questionnaire_id)');
    }
}
