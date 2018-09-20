# Cache Laraview Fragments

Laravel view fragment caching support.

## Installation

### Composer

Download package into the project using Composer.

```bash
composer require hkp22/cache-laraview-fragments
```

### Registering Service Provider
> Laravel 5.5 (or higher) uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

For Laravel 5.4 or earlier releases version include the service provider within `app/config/app.php`:

```php
'providers' => [
    Hkp22\CacheLaraViewFragments\CacheLaraViewFragmentServiceProvider::class,
],
```

### Cache Driver
This package has used Laravel tags (like `Cache::tags('foo')`) to store cache. So, you must use the Laravel cache driver which supports tagging, such as `Memcached` and `Redis`.

Make sure required `CACHE_DRIVER` is used in `.env` file.
```
CACHE_DRIVER=redis
```

## Usage
After package installation, you may use `@cache` Blade directive in your views.

```html
@cache('my-cache-key')
    <div>
        <h1>Hello World</h1>
    </div>
@endcache
```

In production environment, this will cache the HTML fragment "forever." For local development, on the other hand, it will automatically flush the relevant cache for you each time you refresh the page.

#### Clear Cache
Now because your production server will cache the fragments forever, You should add a step to deployment process that clears the relevant cache.

```php
Cache::tags('views')->flush();
```

### Caching Models

Consider the following example:
```
@cache($post)
    <article>
        <h2>{{ $post->title }}></h2>
        <p>Written By: {{ $post->author->username }}</p>

        <div class="body">{{ $post->body }}</div>
    </article>
@endcache
```
In this above example, `$post` object is passed to the `@cache` directive rather than a string. It will look for a `getCacheKey()` method on the model and this method is defined in `Hkp22\CacheLaraViewFragments\Cacheable` trait. So, use this trait in the model.

```php
use Hkp22\CacheLaraViewFragments\Cacheable;

class Post extends Eloquent
{
    use Cacheable;
}
```
The cache key for model will include the object's `id` and `updated_at` timestamp: `App\Post/1-1537459253`.

> Note: Because `updated_at` timestamp used into the cache key, So whenever post is updated, the cache key will change.

#### Touching
Consider the following example:
**resources/views/posts/_post.blade.php**

```html
@cache($post)
    <article>
        <h2>{{ $post->title }}</h2>

        <ul>
            @foreach ($post->comments as $comment)
                @include ('posts/_comment')
            @endforeach
        </ul>
    </article>
@endcache
```
**resources/views/posts/_comment.blade.php**

```html
@cache($post)
    <li>{{ $post->body }}</li>
@endcache
```
In this above example whenever a new comment is created or existing comment is updated. It will not change the view HTML because view HTML is fetched from cache. 

To fix this need to add `$touches` to the child model (Comment model in this case). So, whenever a new comment is created or updated, it would update the parent's `updated_at` timestamp. 

So, in this case `Comment` model should like this:

```php
<?php

namespace App;

use Hkp22\CacheLaraViewFragments\Cacheable;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use Cacheable;

    protected $touches = ['post'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
```

Here the `$touches = ['post']` instructs Laravel to update the `post` relationship's timestamps each time the comment is updated.

### Caching Collections
You may also cache the collection.

```html
@cache($posts)
    @foreach ($posts as $post)
        @include ('post')
    @endforeach
@endcache
```