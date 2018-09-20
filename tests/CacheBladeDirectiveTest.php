<?php

namespace Hkp22\Tests\CacheLaraViewFragments;

use Exception;
use Hkp22\CacheLaraViewFragments\CacheBladeDirective;
use Hkp22\CacheLaraViewFragments\FragmentCaching;
use Hkp22\Tests\CacheLaraViewFragments\Stubs\Models\UnCacheablePost;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;

class CacheBladeDirectiveTest extends TestCase
{
    protected $cache;

    /** @test **/
    public function it_can_cache_html_in_the_cache_directive()
    {
        $directive = $this->createNewCacheDirective();

        $post = $this->makePost();

        $isCached = $directive->setUp($post);

        $this->assertFalse($isCached);

        echo '<div>fragment</div>';

        $cachedFragment = $directive->tearDown();

        $this->assertEquals('<div>fragment</div>', $cachedFragment);

        $this->assertTrue($this->cache->has($post));
    }

    /** @test */
    public function it_can_use_a_string_as_the_cache_key()
    {
        $cache = $this->prophesize(FragmentCaching::class);

        $directive = new CacheBladeDirective($cache->reveal());

        $cache->has('foo')->shouldBeCalled();

        $directive->setUp('foo');

        ob_end_clean(); // Since we're not doing teardown.
    }

    /** @test */
    public function it_can_use_a_collection_as_the_cache_key()
    {
        $cache = $this->prophesize(FragmentCaching::class);

        $directive = new CacheBladeDirective($cache->reveal());

        $collection = collect(['one', 'two']);

        $cache->has(md5($collection))->shouldBeCalled();

        $directive->setUp($collection);

        ob_end_clean(); // Since we're not doing teardown.
    }

    /** @test */
    public function it_can_use_the_model_to_determine_the_cache_key()
    {
        $cache = $this->prophesize(FragmentCaching::class);

        $directive = new CacheBladeDirective($cache->reveal());

        $post = $this->makePost();

        $cache->has(get_class($post) . '/1-' . $post->updated_at->timestamp)->shouldBeCalled();

        $directive->setUp($post);

        ob_end_clean(); // Since we're not doing teardown.
    }

    /** @test */
    public function it_can_use_a_string_to_override_the_models_cache_key()
    {
        $cache = $this->prophesize(FragmentCaching::class);

        $directive = new CacheBladeDirective($cache->reveal());

        $cache->has('override-key')->shouldBeCalled();

        $directive->setUp($this->makePost(), 'override-key');

        ob_end_clean(); // Since we're not doing teardown.
    }

    /** @test **/
    public function it_throws_an_exception_if_it_cannot_determine_the_cache_key()
    {
        $this->expectException(Exception::class);

        $directive = $this->createNewCacheDirective();

        $unCacheablePost = new UnCacheablePost;
        $unCacheablePost->title = 'Some title';
        $unCacheablePost->save();

        $directive->setUp($unCacheablePost);

        ob_end_clean(); // Since we're not doing teardown.
    }

    protected function createNewCacheDirective()
    {
        $cacheStore = new Repository(new ArrayStore);

        $this->cache = new FragmentCaching($cacheStore);

        return new CacheBladeDirective($this->cache);
    }
}
