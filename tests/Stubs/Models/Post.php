<?php

namespace Hkp22\Tests\CacheLaraViewFragments\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Hkp22\CacheLaraViewFragments\Cacheable;

class Post extends Model
{
    use Cacheable;
}
