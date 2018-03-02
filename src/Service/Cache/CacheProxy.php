<?php
declare(strict_types=1);

namespace App\Service\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Class CacheProxy
 */
abstract class CacheProxy implements CacheItemPoolInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cachePool;

    /**
     * @param CacheItemPoolInterface $cachePool
     */
    public function __construct(CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }

    /**
     * @param string $key
     *
     * @return CacheItemInterface
     * @throws InvalidArgumentException
     */
    public function getItem($key)
    {
        return $this->cachePool->getItem($key);
    }

    /**
     * @param array $keys
     *
     * @return array|\Traversable
     * @throws InvalidArgumentException
     */
    public function getItems(array $keys = [])
    {
        return $this->cachePool->getItems($keys);
    }

    /**
     * @param string $key
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function hasItem($key)
    {
        return $this->cachePool->hasItem($key);
    }

    /**
     * @return bool
     */
    public function clear()
    {
        return $this->cachePool->clear();
    }

    /**
     * @param string $key
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function deleteItem($key)
    {
        return $this->cachePool->deleteItem($key);
    }

    /**
     * @param array $keys
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function deleteItems(array $keys)
    {
        return $this->cachePool->deleteItems($keys);
    }

    /**
     * @param CacheItemInterface $item
     *
     * @return bool
     */
    public function save(CacheItemInterface $item)
    {
        return $this->cachePool->save($item);
    }

    /**
     * @param CacheItemInterface $item
     *
     * @return bool
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->cachePool->saveDeferred($item);
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return $this->cachePool->commit();
    }
}

