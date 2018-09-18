<?php

namespace Hkp22\Tests\CacheLaraViewFragments;

use Hkp22\Tests\CacheLaraViewFragments\Stubs\Models\Post;

class CacheableTest extends TestCase
{
    /** @test */
    public function it_gets_a_unique_cache_key_for_an_eloquent_model()
    {
        $model = $this->makePost();

        $postClass = get_class(new Post);

        $this->assertEquals(
            $postClass . '/1-' . $model->updated_at->timestamp,
            $model->getCacheKey()
        );
    }
}
