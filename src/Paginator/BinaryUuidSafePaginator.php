<?php
declare(strict_types=1);

namespace AlexTartan\Paginator;

use AlexTartan\Helpers\ReflectionHelper;
use ArrayIterator;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\LimitSubqueryOutputWalker;
use Doctrine\ORM\Tools\Pagination\LimitSubqueryWalker;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Tools\Pagination\WhereInWalker;
use Ramsey\Uuid\UuidInterface;
use function array_map;
use function count;

/**
 * @method Query cloneQuery(Query $query)
 * @method bool useOutputWalker(Query $query)
 * @method void appendTreeWalker(Query $query, string $walkerClass)
 * @method void unbindUnusedQueryParams(Query $query)
 *
 * @property Query $query
 * @property bool $fetchJoinCollection
 */
class BinaryUuidSafePaginator extends Paginator
{
    public function __construct(Query $query, bool $fetchJoinCollection = true)
    {
        parent::__construct($query, $fetchJoinCollection);
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return ReflectionHelper::callPrivateMethod($this, $name, $arguments);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return ReflectionHelper::getPrivatePropertyValue($this, $name, Paginator::class);
    }

    public function getIterator(): ArrayIterator
    {
        $offset = $this->query->getFirstResult();
        $length = $this->query->getMaxResults();

        if ($this->fetchJoinCollection) {
            $subQuery = $this->cloneQuery($this->query);

            if ($this->useOutputWalker($subQuery)) {
                $subQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, LimitSubqueryOutputWalker::class);
            } else {
                $this->appendTreeWalker($subQuery, LimitSubqueryWalker::class);
                $this->unbindUnusedQueryParams($subQuery);
            }

            $subQuery->setFirstResult($offset)->setMaxResults($length);

            $ids = array_map('current', $subQuery->getScalarResult());
            foreach ($ids as $key => $id) {
                if ($id instanceof UuidInterface) {
                    $ids[$key] = $id->getBytes();
                }
            }

            $whereInQuery = $this->cloneQuery($this->query);
            // don't do this for an empty id array
            if (count($ids) === 0) {
                return new ArrayIterator([]);
            }

            $this->appendTreeWalker($whereInQuery, WhereInWalker::class);
            $whereInQuery->setHint(WhereInWalker::HINT_PAGINATOR_ID_COUNT, count($ids));
            $whereInQuery->setFirstResult(null)->setMaxResults(null);
            $whereInQuery->setParameter(WhereInWalker::PAGINATOR_ID_ALIAS, $ids);
            $whereInQuery->setCacheable($this->query->isCacheable());

            $result = $whereInQuery->getResult($this->query->getHydrationMode());
        } else {
            $result = $this->cloneQuery($this->query)
                           ->setMaxResults($length)
                           ->setFirstResult($offset)
                           ->setCacheable($this->query->isCacheable())
                           ->getResult($this->query->getHydrationMode());
        }

        return new ArrayIterator($result);
    }
}
