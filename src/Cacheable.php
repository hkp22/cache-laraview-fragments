<?php

namespace Hkp22\CacheLaraViewFragments;

trait Cacheable
{
    public function getCacheKey()
    {
        return sprintf(
            '%s/%s-%s',
            get_class($this),
            $this->id,
            $this->updated_at->timestamp
        );
    }
}
