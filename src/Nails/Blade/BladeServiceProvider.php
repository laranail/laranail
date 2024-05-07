<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Blade;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerDirectives();
    }


    /**
     * Register all directives.
     *
     * @return void
     */
    public function registerDirectives(): void
    {
        collect(Directives::directives())->each(function ($item, $key) {
            Blade::directive($key, $item);
        });
    }


}