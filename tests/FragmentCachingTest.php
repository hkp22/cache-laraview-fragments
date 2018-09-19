<?php

namespace Hkp22\Tests\CacheLaraViewFragments;

use Hkp22\CacheLaraViewFragments\FragmentCaching;
use Hkp22\Tests\CacheLaraViewFragments\Stubs\Models\Post;

class FragmentCachingTest extends TestCase
{
    /** @test */
    public function it_caches_the_given_key()
    {
        $post = $this->makePost();
    
        $cache = new \Illuminate\Cache\Repository(
            new \Illuminate\Cache\ArrayStore
        );
        $cache = new FragmentCaching($cache);

        $cache->put($post->getCacheKey(), '<div>view fragment</div>');
        
        $this->assertTrue($cache->has($post->getCacheKey()));
        $this->assertTrue($cache->has($post));
    }
}
