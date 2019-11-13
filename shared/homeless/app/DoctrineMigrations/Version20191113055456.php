<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Создание таблиц для редактируемых форм
 */
class Version20191113055456 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE client_form_response_value (
                id INT AUTO_INCREMENT NOT NULL,
                client_form_response_id INT NOT NULL,
                client_form_field_id INT NOT NULL,
                created_by_id INT DEFAULT NULL,
                updated_by_id INT DEFAULT NULL,
                value LONGTEXT NOT NULL,
                sync_id INT DEFAULT NULL,
                sort INT DEFAULT NULL,
                created_at DATETIME DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                INDEX IDX_A619F21128513DAA (client_form_response_id),
                INDEX IDX_A619F21182BBB8C0 (client_form_field_id),
                INDEX IDX_A619F211B03A8386 (created_by_id),
                INDEX IDX_A619F211896DBBDE (updated_by_id),
                UNIQUE INDEX client_form_response_uniq (client_form_response_id, client_form_field_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE client_form (
                id INT AUTO_INCREMENT NOT NULL,
                created_by_id INT DEFAULT NULL,
                updated_by_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                sync_id INT DEFAULT NULL,
                sort INT DEFAULT NULL,
                created_at DATETIME DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                INDEX IDX_83143E2DB03A8386 (created_by_id),
                INDEX IDX_83143E2D896DBBDE (updated_by_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE client_form_field (
                id INT AUTO_INCREMENT NOT NULL,
                form_id INT NOT NULL,
                created_by_id INT DEFAULT NULL,
                updated_by_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                type INT NOT NULL,
                options LONGTEXT DEFAULT NULL,
                required TINYINT(1) NOT NULL,
                sync_id INT DEFAULT NULL,
                sort INT DEFAULT NULL,
                created_at DATETIME DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                INDEX IDX_48E6DCDA5FF69B7D (form_id),
                INDEX IDX_48E6DCDAB03A8386 (created_by_id),
                INDEX IDX_48E6DCDA896DBBDE (updated_by_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE client_form_response (
                id INT AUTO_INCREMENT NOT NULL,
                client_id INT NOT NULL,
                form_id INT NOT NULL,
                created_by_id INT DEFAULT NULL,
                updated_by_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                sync_id INT DEFAULT NULL,
                sort INT DEFAULT NULL,
                created_at DATETIME DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                INDEX IDX_F8F7B48B19EB6921 (client_id),
                INDEX IDX_F8F7B48B5FF69B7D (form_id),
                INDEX IDX_F8F7B48BB03A8386 (created_by_id),
                INDEX IDX_F8F7B48B896DBBDE (updated_by_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE client_form_response_value ADD CONSTRAINT FK_A619F21128513DAA
                FOREIGN KEY (client_form_response_id) REFERENCES client_form_response (id)');
        $this->addSql('
            ALTER TABLE client_form_response_value ADD CONSTRAINT FK_A619F21182BBB8C0
                FOREIGN KEY (client_form_field_id) REFERENCES client_form_field (id)');
        $this->addSql('
            ALTER TABLE client_form_response_value ADD CONSTRAINT FK_A619F211B03A8386
                FOREIGN KEY (created_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('
            ALTER TABLE client_form_response_value ADD CONSTRAINT FK_A619F211896DBBDE
                FOREIGN KEY (updated_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('
            ALTER TABLE client_form ADD CONSTRAINT FK_83143E2DB03A8386
                FOREIGN KEY (created_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('
            ALTER TABLE client_form ADD CONSTRAINT FK_83143E2D896DBBDE
                FOREIGN KEY (updated_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('
            ALTER TABLE client_form_field ADD CONSTRAINT FK_48E6DCDA5FF69B7D
                FOREIGN KEY (form_id) REFERENCES client_form (id)');
        $this->addSql('
            ALTER TABLE client_form_field ADD CONSTRAINT FK_48E6DCDAB03A8386
                FOREIGN KEY (created_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('
            ALTER TABLE client_form_field ADD CONSTRAINT FK_48E6DCDA896DBBDE
                FOREIGN KEY (updated_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('
            ALTER TABLE client_form_response ADD CONSTRAINT FK_F8F7B48B19EB6921
                FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('
            ALTER TABLE client_form_response ADD CONSTRAINT FK_F8F7B48B5FF69B7D
                FOREIGN KEY (form_id) REFERENCES client_form (id)');
        $this->addSql('
            ALTER TABLE client_form_response ADD CONSTRAINT FK_F8F7B48BB03A8386
                FOREIGN KEY (created_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('
            ALTER TABLE client_form_response ADD CONSTRAINT FK_F8F7B48B896DBBDE
                FOREIGN KEY (updated_by_id) REFERENCES fos_user_user (id)');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client_form_field DROP FOREIGN KEY FK_48E6DCDA5FF69B7D');
        $this->addSql('ALTER TABLE client_form_response DROP FOREIGN KEY FK_F8F7B48B5FF69B7D');
        $this->addSql('ALTER TABLE client_form_response_value DROP FOREIGN KEY FK_A619F21182BBB8C0');
        $this->addSql('ALTER TABLE client_form_response_value DROP FOREIGN KEY FK_A619F21128513DAA');
        $this->addSql('DROP TABLE client_form_response_value');
        $this->addSql('DROP TABLE client_form');
        $this->addSql('DROP TABLE client_form_field');
        $this->addSql('DROP TABLE client_form_response');
    }
}
