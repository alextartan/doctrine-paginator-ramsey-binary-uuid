<?php
declare(strict_types=1);

namespace AlexTartanTest\Paginator\Setup\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="AlexTartanTest\Paginator\Setup\Repository\UuidBasedRepository")
 * @ORM\Table(
 *     name="test_table1"
 * )
 */
class UuidBasedEntity
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
