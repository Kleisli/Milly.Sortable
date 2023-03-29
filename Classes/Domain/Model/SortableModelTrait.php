<?php
namespace Milly\Sortable\Domain\Model;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Exception;
use Neos\Flow\Persistence\RepositoryInterface;

trait SortableModelTrait
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var int
     */
    protected $sorting;

    /**
     * @return int
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * @return bool
     */
    public function getIsLast(): bool
    {
        return $this->repository->findNext($this) == null;
    }

    /**
     * @return bool
     */
    public function getIsFirst(): bool
    {
        return $this->repository->findPrevious($this) == null;
    }

}
