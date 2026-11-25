<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20181203012633 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql("create table resident_questionnaire
(
	id int auto_increment,
	client_id int not null,
	type_id int not null,
	is_dwelling int(1) null,
	room_type_id int null,
	is_work int(1) null,
	is_work_official int(1) null,
	is_work_constant int(1) null,
	changed_jobs_count_id int null,
	reason_for_transition_ids varchar(64) null,
	reason_for_petition_ids varchar(64) null,
	constraint resident_questionnaire_pk
		primary key (id),
	constraint resident_questionnaire_client_id_fk
		foreign key (client_id) references client (id)
			on update cascade on delete cascade
);
");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
    }
}
