<?php

namespace Hkp22\CacheLaraViewFragments;

class CacheBladeDirective
{
    /**
     * The cache instance.
     *
     * @var FragmentCaching
     */
    protected $cache;
    /**
     * A list of model cache keys.
     *
     * @param array $keys
     */
    protected $keys = [];

    /**
     * Create a new instance.
     *
     * @param FragmentCaching $cache
     */
    public function __construct(FragmentCaching $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the @cache setup.
     *
     * @param mixed       $model
     * @param string|null $key
     */
    public function setUp($model, $key = null)
    {
        ob_start();

        $this->keys[] = $key = $this->normalizeKey($model, $key);

        return $this->cache->has($key);
    }

    /**
     * Handle the @endcache teardown.
     */
    public function tearDown()
    {
        $html = ob_get_clean();

        return $this->cache->put(array_pop($this->keys), $html);
    }

    /**
     * Normalize the cache key.
     *
     * @param mixed       $item
     * @param string|null $key
     */
    protected function normalizeKey($item, $key = null)
    {
        if (is_string($item) || is_string($key)) {
            return is_string($item) ? $item : $key;
        }

        if (is_object($item) && method_exists($item, 'getCacheKey')) {
            return $item->getCacheKey();
        }

        if ($item instanceof \Illuminate\Support\Collection) {
            return md5($item);
        }

        throw new \Exception('Could not determine an appropriate cache key.');
    }
}
