<?php
namespace Milly\Sortable\Domain\Model;

use Milly\Tools\Service\ClassMappingService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Exception;
use Neos\Flow\Persistence\RepositoryInterface;

trait SortableModelTrait
{

    /**
     * @var RepositoryInterface
     * @Flow\Transient
     */
    protected $repository;

    /**
     * @var int
     */
    protected $sorting;


    /**
     * @throws Exception
     */
    protected function getRepository(): RepositoryInterface {
        if(!isset($this->repository)){
            $classMappingService = new ClassMappingService();
            $repositoryClassName = $classMappingService->getRepositoryClassByModel($this);
            $this->repository = new $repositoryClassName();
        }

        return $this->repository;
    }

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
        return $this->getRepository()->findNext($this) == null;
    }

    /**
     * @return bool
     */
    public function getIsFirst(): bool
    {
        return $this->getRepository()->findPrevious($this) == null;
    }

}
