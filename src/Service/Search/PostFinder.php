<?php
declare(strict_types=1);

namespace App\Service\Search;

use App\Entity\Post;

/**
 * Class PostFinder
 *
 * @method Post[] find($query, $limit = null, $options = [])
 */
class PostFinder extends FinderProxy
{
    //implement or extend some custom post finder logic here
}

