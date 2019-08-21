<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190712163832 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO homeless.menu_item (created_by_id, updated_by_id, name, code, enabled, sync_id, sort, created_at, updated_at) VALUES (null, null, \'Напоминания\', \'notifications\', 1, null, 100, \'2019-07-24 16:12:41\', null);');
        $this->addSql('INSERT INTO homeless.menu_item (created_by_id, updated_by_id, name, code, enabled, sync_id, sort, created_at, updated_at) VALUES (null, null, \'Статус "бездомный/не бездомный"\', \'status_homeless\', 0, null, 100, \'2019-07-24 16:12:41\', null);');
        $this->addSql('INSERT INTO homeless.menu_item (created_by_id, updated_by_id, name, code, enabled, sync_id, sort, created_at, updated_at) VALUES (null, null, \'Анкета проживавшего\', \'questionnaire_living\', 0, null, 100, \'2019-07-24 16:12:41\', null);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
