<?php
declare(strict_types=1);

namespace AlexTartanTest\Paginator;

use AlexTartan\Paginator\BinaryUuidSafePaginator;
use AlexTartanTest\Paginator\Setup\Entity\UuidBasedEntity;
use AlexTartanTest\Paginator\Setup\EntityManager\EntityManagerSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    /** @var EntityManager */
    private $em;

    public function setUp(): void
    {
        parent::setUp();

        $em = EntityManagerSetup::create();
        EntityManagerSetup::generateEmptySchema($em);

        $e1 = new UuidBasedEntity();
        $e2 = new UuidBasedEntity();
        $e3 = new UuidBasedEntity();
        $e4 = new UuidBasedEntity();
        $e5 = new UuidBasedEntity();

        $em->persist($e1);
        $em->persist($e2);
        $em->persist($e3);
        $em->persist($e4);
        $em->persist($e5);
        $em->flush();

        $this->em = $em;
    }

    public function testStandardPaginatorDoesNotWork(): void
    {
        $repo = $this->em->getRepository(UuidBasedEntity::class);

        self::assertCount(5, $repo->findAll());

        $query = $repo->createQueryBuilder('ube')
                      ->setMaxResults(2)
                      ->getQuery();

        $p = new Paginator($query);
        self::assertSame(0, $p->getIterator()->count());
    }

    public function testBinaryUuidSafePaginatorWorks(): void
    {
        $repo = $this->em->getRepository(UuidBasedEntity::class);

        self::assertCount(5, $repo->findAll());

        $query = $repo->createQueryBuilder('ube')
                      ->setMaxResults(2)
                      ->getQuery();

        $p = new BinaryUuidSafePaginator($query);
        self::assertSame(2, $p->getIterator()->count());
    }
}
