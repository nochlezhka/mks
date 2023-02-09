<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190712163831 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('alter table client_field_option drop foreign key FK_C1C82EB3443707B0;');
        $this->addSql('alter table client_field_option add constraint FK_C1C82EB3443707B0 foreign key (field_id) references client_field (id) on delete cascade');
        $this->addSql('alter table client_field_value drop foreign key FK_379BEBF4443707B0;');
        $this->addSql('alter table client_field_value add constraint FK_379BEBF4443707B0 foreign key (field_id) references client_field (id) on delete cascade;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('alter table client_field_option drop foreign key FK_C1C82EB3443707B0;');
        $this->addSql('alter table client_field_option add constraint FK_C1C82EB3443707B0 foreign key (field_id) references client_field (id)');
        $this->addSql('alter table client_field_value drop foreign key FK_379BEBF4443707B0;');
        $this->addSql('alter table client_field_value add constraint FK_379BEBF4443707B0 foreign key (field_id) references client_field (id)');
    }
}
