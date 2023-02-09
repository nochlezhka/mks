<?php

namespace Application\Migrations;

use AppBundle\Entity\ClientFormField;
use AppBundle\Entity\ClientFormResponseValue;
use AppBundle\Util\ClientFormUtil;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Добавление нового типа "на момент выезда" в анкету проживающего.
 * Инициализацию полей этой анкеты можно посмотреть в миграции Version20191113060904
 */
class Version20210506111007 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $qnrTypeFieldId = ClientFormField::RESIDENT_QUESTIONNAIRE_TYPE_FIELD_ID;
        $qnrTypes = ClientFormUtil::arrayToOptionsText([
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_3_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_6_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_1_YEAR,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_2_YEARS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_WHEN_LEAVING
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
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $qnrTypeFieldId = ClientFormField::RESIDENT_QUESTIONNAIRE_TYPE_FIELD_ID;
        $qnrTypes = ClientFormUtil::arrayToOptionsText([
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_3_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_6_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_1_YEAR,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_2_YEARS
        ]);
        $qnrTypes = str_replace("\n", "\\n", $qnrTypes);
        $this->addSql("
            UPDATE client_form_field
            SET options = '$qnrTypes'
            WHERE id = $qnrTypeFieldId
        ");
    }
}
