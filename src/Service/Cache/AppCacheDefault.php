<?php
declare(strict_types=1);

namespace App\Service\Cache;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Class AppCacheDefault
 */
class AppCacheDefault extends CacheProxy
{
    public function __construct(CacheItemPoolInterface $cachePool)
    {
        parent::__construct($cachePool);
    }
}

