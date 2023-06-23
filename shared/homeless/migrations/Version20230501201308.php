<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230501201308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'On client and user delete';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE client_form_response DROP FOREIGN KEY FK_F8F7B48B19EB6921');
        $this->addSql('ALTER TABLE client_form_response ADD CONSTRAINT FK_F8F7B48B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_form_response_value DROP FOREIGN KEY FK_A619F21119EB6921');
        $this->addSql('ALTER TABLE client_form_response_value ADD CONSTRAINT FK_A619F21119EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704B19EB6921');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE history_download DROP FOREIGN KEY FK_38E1EE3AA76ED395');
        $this->addSql('ALTER TABLE history_download ADD CONSTRAINT FK_38E1EE3AA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C219EB6921');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C219EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE client_form_response DROP FOREIGN KEY FK_F8F7B48B19EB6921');
        $this->addSql('ALTER TABLE client_form_response ADD CONSTRAINT FK_F8F7B48B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE client_form_response_value DROP FOREIGN KEY FK_A619F21119EB6921');
        $this->addSql('ALTER TABLE client_form_response_value ADD CONSTRAINT FK_A619F21119EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704B19EB6921');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE history_download DROP FOREIGN KEY FK_38E1EE3AA76ED395');
        $this->addSql('ALTER TABLE history_download ADD CONSTRAINT FK_38E1EE3AA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C219EB6921');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C219EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
