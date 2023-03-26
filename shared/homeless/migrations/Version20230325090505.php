<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230325090505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Boolean not null';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE certificate_type SET downloadable = 0 WHERE downloadable IS NULL');
        $this->addSql('UPDATE certificate_type SET show_photo = 0 WHERE show_photo IS NULL');
        $this->addSql('UPDATE certificate_type SET show_date = 0 WHERE show_date IS NULL');
        $this->addSql('ALTER TABLE certificate_type CHANGE downloadable downloadable TINYINT(1) NOT NULL, CHANGE show_photo show_photo TINYINT(1) NOT NULL, CHANGE show_date show_date TINYINT(1) NOT NULL');

        $this->addSql('UPDATE client SET is_homeless = 1 WHERE is_homeless IS NULL');
        $this->addSql('ALTER TABLE client CHANGE is_homeless is_homeless TINYINT(1) DEFAULT 1 NOT NULL');

        $this->addSql('UPDATE client_field SET enabled = 1 WHERE enabled IS NULL');
        $this->addSql('UPDATE client_field SET required = 0 WHERE required IS NULL');
        $this->addSql('UPDATE client_field SET multiple = 0 WHERE multiple IS NULL');
        $this->addSql('UPDATE client_field SET mandatory_for_homeless = 0 WHERE mandatory_for_homeless IS NULL');
        $this->addSql('UPDATE client_field SET enabled_for_homeless = 1 WHERE enabled_for_homeless IS NULL');
        $this->addSql('ALTER TABLE client_field CHANGE enabled enabled TINYINT(1) DEFAULT 1 NOT NULL, CHANGE required required TINYINT(1) NOT NULL, CHANGE multiple multiple TINYINT(1) NOT NULL, CHANGE mandatory_for_homeless mandatory_for_homeless TINYINT(1) NOT NULL, CHANGE enabled_for_homeless enabled_for_homeless TINYINT(1) DEFAULT 1 NOT NULL');

        $this->addSql('UPDATE client_field_option SET not_single = 0 WHERE not_single IS NULL');
        $this->addSql('ALTER TABLE client_field_option CHANGE not_single not_single TINYINT(1) NOT NULL');

        $this->addSql('UPDATE menu_item SET enabled = 1 WHERE enabled IS NULL');
        $this->addSql('ALTER TABLE menu_item CHANGE enabled enabled TINYINT(1) DEFAULT 1 NOT NULL');

        $this->addSql('UPDATE note SET important = 0 WHERE important IS NULL');
        $this->addSql('ALTER TABLE note CHANGE important important TINYINT(1) NOT NULL');

        $this->addSql('UPDATE resident_questionnaire SET is_dwelling = 0 WHERE is_dwelling IS NULL');
        $this->addSql('UPDATE resident_questionnaire SET is_work = 0 WHERE is_work IS NULL');
        $this->addSql('UPDATE resident_questionnaire SET is_work_official = 0 WHERE is_work_official IS NULL');
        $this->addSql('UPDATE resident_questionnaire SET is_work_constant = 0 WHERE is_work_constant IS NULL');
        $this->addSql('ALTER TABLE resident_questionnaire CHANGE is_dwelling is_dwelling TINYINT(1) NOT NULL, CHANGE is_work is_work TINYINT(1) NOT NULL, CHANGE is_work_official is_work_official TINYINT(1) NOT NULL, CHANGE is_work_constant is_work_constant TINYINT(1) NOT NULL');

        $this->addSql('UPDATE service_type SET pay = 0 WHERE pay IS NULL');
        $this->addSql('UPDATE service_type SET document = 0 WHERE document IS NULL');
        $this->addSql('UPDATE service_type SET amount = 0 WHERE amount IS NULL');
        $this->addSql('UPDATE service_type SET comment = 0 WHERE comment IS NULL');
        $this->addSql('ALTER TABLE service_type CHANGE pay pay TINYINT(1) NOT NULL, CHANGE document document TINYINT(1) NOT NULL, CHANGE amount amount TINYINT(1) NOT NULL, CHANGE comment comment TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE certificate_type CHANGE downloadable downloadable TINYINT(1) DEFAULT NULL, CHANGE show_photo show_photo TINYINT(1) DEFAULT NULL, CHANGE show_date show_date TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE is_homeless is_homeless TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE client_field CHANGE enabled enabled TINYINT(1) DEFAULT NULL, CHANGE enabled_for_homeless enabled_for_homeless TINYINT(1) DEFAULT NULL, CHANGE required required TINYINT(1) DEFAULT NULL, CHANGE mandatory_for_homeless mandatory_for_homeless TINYINT(1) DEFAULT NULL, CHANGE multiple multiple TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE client_field_option CHANGE not_single not_single TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE menu_item CHANGE enabled enabled TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE note CHANGE important important TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE resident_questionnaire CHANGE is_dwelling is_dwelling TINYINT(1) DEFAULT NULL, CHANGE is_work is_work TINYINT(1) DEFAULT NULL, CHANGE is_work_official is_work_official TINYINT(1) DEFAULT NULL, CHANGE is_work_constant is_work_constant TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE service_type CHANGE pay pay TINYINT(1) DEFAULT NULL, CHANGE document document TINYINT(1) DEFAULT NULL, CHANGE amount amount TINYINT(1) DEFAULT NULL, CHANGE comment comment TINYINT(1) DEFAULT NULL');
    }
}
