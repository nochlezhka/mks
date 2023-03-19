<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\ClientForm;
use App\Entity\ClientFormField;
use App\Entity\ClientFormResponseValue;
use App\Util\ClientFormUtil;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Создание новой настраиваемой формы наподобие ResidentQuestionnaire
 */
class Version20191113060904 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $qnrFormId = ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID;
        $this->addSql("
            INSERT INTO `client_form`(id, created_by_id, updated_by_id, name, sync_id, sort, created_at, updated_at)
            VALUES ($qnrFormId,1,NULL,'Анкета проживавшего',NULL,100,'2019-11-12 23:08:46',NULL);
        ");

        $qnrTypeFieldId = ClientFormField::RESIDENT_QUESTIONNAIRE_TYPE_FIELD_ID;
        $qnrTypes = ClientFormUtil::arrayToOptionsText([
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_3_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_6_MONTHS,
            ClientFormResponseValue::RESIDENT_QUESTIONNAIRE_TYPE_1_YEAR,
        ]);
        $qnrTypes = str_replace("\n", "\\n", $qnrTypes);
        $fieldTypeOption = ClientFormField::TYPE_OPTION;
        $fieldTypeCheckbox = ClientFormField::TYPE_CHECKBOX;
        $fieldFlagFixed = ClientFormField::FLAG_FIXED;
        $fieldFlagRequired = ClientFormField::FLAG_REQUIRED;
        $fieldFlagMiltiselect = ClientFormField::FLAG_MULTISELECT;
        $this->addSql("
            INSERT INTO `client_form_field`(id, form_id, created_by_id, updated_by_id, name, type, options, flags, sync_id, sort, created_at, updated_at)
            VALUES
                ($qnrTypeFieldId,$qnrFormId,1,NULL,'Тип',$fieldTypeOption,'$qnrTypes','$fieldFlagFixed,$fieldFlagRequired',NULL,100,'2019-11-12 23:44:40',NULL),
                (2,$qnrFormId,1,NULL,'Проживает в жилом помещении',$fieldTypeCheckbox,NULL,NULL,NULL,101,'2019-11-12 23:45:15',NULL),
                (3,$qnrFormId,1,NULL,'Тип помещения',$fieldTypeOption,'Снимает комнату\\nСнимает койку\\nСнимает квартиру\\nУ знакомых\\nПрямо на работе\\nРебцентр\\nднп, гос. учреждения\\nСвоё жилье\\nОбщежитие от работы\\nДругое (б-ца, гора, сестра)',NULL,NULL,102,'2019-11-12 23:46:33',NULL),
                (4,$qnrFormId,1,NULL,'Работает?',$fieldTypeCheckbox,NULL,NULL,NULL,103,'2019-11-12 23:46:52',NULL),
                (5,$qnrFormId,1,NULL,'Официальная работа?',$fieldTypeCheckbox,NULL,NULL,NULL,104,'2019-11-12 23:47:34',NULL),
                (6,$qnrFormId,1,NULL,'Постоянная работа?',$fieldTypeCheckbox,NULL,NULL,NULL,105,'2019-11-12 23:47:50',NULL),
                (7,$qnrFormId,1,NULL,'Сколько сменил работ',$fieldTypeOption,'Не менял\\n1\\n2\\n3 и более',NULL,NULL,106,'2019-11-12 23:48:18',NULL),
                (8,$qnrFormId,1,NULL,'Причина перехода на другую работу',$fieldTypeOption,'Лучшие условия труда (зарплата, соцпакет, месторасположение и пр.)\\nБолее интересная деятельность\\nКонфликты\\nСокращение рабочего места','$fieldFlagMiltiselect',NULL,107,'2019-11-12 23:48:57',NULL),
                (9,$qnrFormId,1,NULL,'Причина обращения',$fieldTypeOption,'Только справка\\nГуманитарная помощь\\nРазовая консультация\\nСопровождение\\nПовторное заселение','$fieldFlagMiltiselect',NULL,108,'2019-11-12 23:49:51',NULL);
        ");
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $qnrFormId = ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID;
        $this->addSql("
            DELETE FROM `client_form_field` WHERE form_id = $qnrFormId;
        ");
        $this->addSql("
            DELETE FROM `client_form` WHERE id = $qnrFormId;
        ");
    }
}
