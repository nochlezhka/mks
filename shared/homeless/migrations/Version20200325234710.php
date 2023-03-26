<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\ClientFormField;
use App\Entity\ClientFormResponseValue;
use App\Util\ClientFormUtil;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Добавление нового типа "2 года" в анкету проживающего.
 *
 * Инициализацию полей этой анкеты можно посмотреть в миграции Version20191113060904
 */
class Version20200325234710 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $qnrTypeFieldId = ClientFormField::RESIDENT_QUESTIONNAIRE_TYPE_FIELD_ID;
        $qnrTypes = ClientFormUtil::arrayToOptionsText([
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_3_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_6_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_1_YEAR,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_2_YEARS,
        ]);
        $qnrTypes = str_replace("\n", "\\n", $qnrTypes);
        $this->addSql("
            UPDATE client_form_field
            SET options = '$qnrTypes'
            WHERE id = $qnrTypeFieldId
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $qnrTypeFieldId = ClientFormField::RESIDENT_QUESTIONNAIRE_TYPE_FIELD_ID;
        $qnrTypes = ClientFormUtil::arrayToOptionsText([
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_3_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_6_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_1_YEAR
        ]);
        $qnrTypes = str_replace("\n", "\\n", $qnrTypes);
        $this->addSql("
            UPDATE client_form_field
            SET options = '$qnrTypes'
            WHERE id = $qnrTypeFieldId
        ");
    }
}
