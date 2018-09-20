<?php

namespace Hkp22\CacheLaraViewFragments;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;


class CacheLaraViewFragmentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        if ($this->app->isLocal()) {
            $kernel->pushMiddleware('Hkp22\CacheLaraViewFragments\Middleware\FlushViewCache');
        }

        Blade::directive('cache', function ($expression) {
            return "<?php if (! app('Hkp22\CacheLaraViewFragments\CacheBladeDirective')->setUp({$expression})) : ?>";
        });

        Blade::directive('endcache', function ($expression) {
            return "<?php endif; echo app('Hkp22\CacheLaraViewFragments\CacheBladeDirective')->tearDown() ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CacheBladeDirective::class);
    }
}
