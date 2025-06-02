<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
        Blade::directive('wordLimiter', function($expression, $numeberOfWord = 25, $postFix = '...'){
            return "<?php echo Str::words({$expression},{$numeberOfWord},'{$postFix}'); ?>";
        });
    }
}
