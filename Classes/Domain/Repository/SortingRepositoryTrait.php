<?php
namespace Milly\Sortable\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Exception;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Persistence\QueryInterface;
use Neos\Flow\Security\Policy\Role;
use Neos\Flow\Utility\Algorithms;

trait SortingRepositoryTrait
{
    /**
     * @param object|null $object
     * @return integer
     */
    public function getMaxSorting(object $object = null){
        $query = $this->createQuery();
        if ($object != null && method_exists($object, 'getSortingCondition')) {
            $query = $query->matching($object->getSortingCondition($query));
        }
        $result = $query->setOrderings(['sorting' => QueryInterface::ORDER_DESCENDING])->execute();
        return $result->count() ? $result->getFirst()->getSorting() : 0;
    }

    /**
     * Adds an object to this repository.
     *
     * @param object $object The object to add
     * @return void
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     * @api
     */
    public function add($object): void
    {
        $object->setSorting($this->getMaxSorting($object) + 1);
        parent::add($object);
    }

    /**
     *
     * @param object $object
     * @return void
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     * @api
     */
    public function sortUp($object): void
    {
        $this->swapSorting($object, $this->findPrevious($object));
    }

    /**
     *
     * @param object $object
     * @return void
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     * @api
     */
    public function sortDown($object): void
    {
        $this->swapSorting($object, $this->findNext($object));
    }

    /**
     *
     * @param object $object
     * @return object|null
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException
     * @api
     */
    public function findNext($object): ?object
    {
        $query = $this->createQuery();
        $conditions = [];
        $conditions[] = $query->greaterThan('sorting', $object->getSorting());
        if (method_exists($object, 'getSortingCondition')) {
            $conditions[] = $object->getSortingCondition($query);
        }
        $result = $query->matching($query->logicalAnd($conditions))
            ->setOrderings(['sorting' => QueryInterface::ORDER_ASCENDING])
            ->execute();
        return $result->getFirst();
    }

    /**
     *
     * @param object $object
     * @return object|null
     * @throws \Neos\Flow\Persistence\Exception\InvalidQueryException
     * @api
     */
    public function findPrevious($object): ?object
    {
        $query = $this->createQuery();
        $conditions = [];
        $conditions[] = $query->lessThan('sorting', $object->getSorting());
        if (method_exists($object, 'getSortingCondition')) {
            $conditions[] = $object->getSortingCondition($query);
        }
        $result = $query->matching($query->logicalAnd($conditions))
            ->setOrderings(['sorting' => QueryInterface::ORDER_DESCENDING])
            ->execute();
        return $result->getFirst();
    }

    /**
     *
     * @param object $object1
     * @param object $object2
     * @return void
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     * @api
     */
    protected function swapSorting(object $object1, object $object2): void
    {
        $index = $object1->getSorting();
        $object1->setSorting($object2->getSorting());
        $object2->setSorting($index);
        $this->update($object1);
        $this->update($object2);
    }

}
