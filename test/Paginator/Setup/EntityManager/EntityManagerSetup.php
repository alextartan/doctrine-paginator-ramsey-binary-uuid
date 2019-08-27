<?php
declare(strict_types=1);

namespace AlexTartanTest\Paginator\Setup\EntityManager;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Doctrine\UuidType;

class EntityManagerSetup
{
    public static function create(): EntityManager
    {
        $isDevMode = true;
        $config    = Setup::createAnnotationMetadataConfiguration(
            [__DIR__ . '/../Entity/'],
            $isDevMode,
            __DIR__ . '/../../../data/proxies/',
            null,
            false
        );

        $conn = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        $entityManager = EntityManager::create($conn, $config);

        $entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('uuid_binary', 'binary');
        if (!Type::hasType('uuid')) {
            Type::addType('uuid', UuidType::class);
            Type::addType('uuid_binary', UuidBinaryType::class);
        }

        return $entityManager;
    }

    public static function generateEmptySchema(EntityManager $entityManager): void
    {
        $schemaTool = new SchemaTool($entityManager);
        $metadata   = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}
