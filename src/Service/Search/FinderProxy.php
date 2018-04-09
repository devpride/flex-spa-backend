<?php
declare(strict_types=1);

namespace App\Service\Search;

use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

/**
 * Class FinderProxy
 */
abstract class FinderProxy implements PaginatedFinderInterface
{
    /**
     * @var PaginatedFinderInterface
     */
    protected $finder;

    /**
     * @param PaginatedFinderInterface $finder
     */
    public function __construct(PaginatedFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param mixed $query
     * @param null  $limit
     * @param array $options
     *
     * @return array
     */
    public function find($query, $limit = null, $options = [])
    {
        return $this->finder->find($query, $limit, $options);
    }

    /**
     * @param mixed $query
     * @param array $options
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function findPaginated($query, $options = [])
    {
        return $this->finder->findPaginated($query, $options);
    }

    /**
     * @param mixed $query
     * @param array $options
     *
     * @return \FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface
     */
    public function createPaginatorAdapter($query, $options = [])
    {
        return $this->finder->createPaginatorAdapter($query, $options);
    }

    /**
     * @param mixed $query
     *
     * @return \FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface
     */
    public function createHybridPaginatorAdapter($query)
    {
        return $this->finder->createHybridPaginatorAdapter($query);
    }

    /**
     * @param mixed $query
     * @param array $options
     *
     * @return \FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface
     */
    public function createRawPaginatorAdapter($query, $options = [])
    {
        return $this->finder->createRawPaginatorAdapter($query, $options);
    }
}

