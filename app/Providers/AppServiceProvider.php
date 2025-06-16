<?php

namespace App\Providers;

use Barryvdh\Debugbar\Facades\Debugbar as FacadesDebugbar;
use Debugbar;
use DebugBar\DebugBar as DebugBarDebugBar;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

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
        // Debugbar::disable();
        Builder::macro('search', function ($fields, $string){
            $this->where(function ($query) use ($fields, $string) {
                foreach ($fields as $field) {
                    $query->orWhere($field, 'like', '%'.$string.'%');
                }
            });
            return $this;
        });

        Paginator::defaultView('pagination::default');
 
        Paginator::defaultSimpleView('pagination::simple-default');
    }
}
