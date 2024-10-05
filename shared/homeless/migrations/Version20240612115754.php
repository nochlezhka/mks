<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240612115754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate users roles';
    }

    public function up(Schema $schema): void
    {
        /** @var array<array{'id': scalar, 'roles': string}> $rows */
        $rows = $this->connection->fetchAllAssociative('SELECT id, roles FROM fos_user_user');
        foreach ($rows as $row) {
            $id = $row['id'];
            $roles = json_encode(unserialize($row['roles']));
            $this->connection->executeQuery('UPDATE fos_user_user SET roles = :roles WHERE id = :id', ['roles' => $roles, 'id' => $id]);
        }
    }
}
