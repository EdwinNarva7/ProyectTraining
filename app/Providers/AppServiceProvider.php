<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('hm', function ($expression) {
            return "<?php "
                . "\$__val = ($expression); "
                . "\$__totalMinutes = (int) round((\$__val ?? 0) * 60); "
                . "\$__h = intdiv(\$__totalMinutes, 60); "
                . "\$__m = \$__totalMinutes % 60; "
                . "\$__out = (\$__h ? \$__h . 'h' : '') . (\$__m ? (\$__h ? ' ' : '') . \$__m . 'm' : (\$__h ? '' : '0m')); "
                . "echo \$__out; ?>"
            ;
        });

        Blade::directive('hmMinutes', function ($expression) {
            return "<?php "
                . "\$__min = (int) (\$__tmp = ($expression)); "
                . "\$__h = intdiv(\$__min, 60); "
                . "\$__m = \$__min % 60; "
                . "\$__out = (\$__h ? \$__h . 'h' : '') . (\$__m ? (\$__h ? ' ' : '') . \$__m . 'm' : (\$__h ? '' : '0m')); "
                . "echo \$__out; ?>"
            ;
        });
    }
}
