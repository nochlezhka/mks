<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\DoctrineExtensions\Query\Mysql\CustomPart;
use Doctrine\DBAL\Types\JsonType;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine, ContainerConfigurator $container): void {
    $container->parameters()
        ->set('env(DB_HOST)', 'localhost')
        ->set('env(DB_PORT)', '3306')
        ->set('env(DB_NAME)', 'homeless')
        ->set('env(DB_USER)', 'homeless')
        ->set('env(DB_PASSWORD)', 'password')
    ;

    /** @var \Symfony\Config\Doctrine\DbalConfig $dbal */
    $dbal = $doctrine->dbal();
    $dbal->connection('default')
        ->driver('pdo_mysql')
        ->host(env('DB_HOST'))
        ->port(env('DB_PORT'))
        ->dbname(env('DB_NAME'))
        ->user(env('DB_USER'))
        ->password(env('DB_PASSWORD'))
        ->serverVersion('8.0.29')
        ->charset('UTF8')
    ;
    $dbal->type('json')
        ->class(JsonType::class)
    ;

    /** @var \Symfony\Config\Doctrine\OrmConfig $orm */
    $orm = $doctrine->orm();
    $orm->controllerResolver()
        ->autoMapping(true)
    ;
    $defaultEntityManager = $orm->entityManager('default')
        ->namingStrategy('doctrine.orm.naming_strategy.underscore_number_aware')
        ->autoMapping(true)
        ->reportFieldsWhereDeclared(true)
    ;
    $defaultEntityManager->dql()
        ->stringFunction('custom_part', CustomPart::class)
    ;
    $defaultEntityManager->mapping('App')
        ->isBundle(false)
        ->type('attribute')
        ->dir(param('kernel.project_dir').'/src/Entity')
        ->prefix('App\Entity')
        ->alias('App')
    ;
};
