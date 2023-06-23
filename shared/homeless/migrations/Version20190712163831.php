<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190712163831 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('alter table client_field_option drop foreign key FK_C1C82EB3443707B0;');
        $this->addSql('alter table client_field_option add constraint FK_C1C82EB3443707B0 foreign key (field_id) references client_field (id) on delete cascade');
        $this->addSql('alter table client_field_value drop foreign key FK_379BEBF4443707B0;');
        $this->addSql('alter table client_field_value add constraint FK_379BEBF4443707B0 foreign key (field_id) references client_field (id) on delete cascade;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('alter table client_field_option drop foreign key FK_C1C82EB3443707B0;');
        $this->addSql('alter table client_field_option add constraint FK_C1C82EB3443707B0 foreign key (field_id) references client_field (id)');
        $this->addSql('alter table client_field_value drop foreign key FK_379BEBF4443707B0;');
        $this->addSql('alter table client_field_value add constraint FK_379BEBF4443707B0 foreign key (field_id) references client_field (id)');
    }
}
