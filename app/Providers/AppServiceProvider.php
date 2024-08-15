<?php

namespace App\Providers;

use App\Models\Module;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        View::composer('*', function ($view) {
            if(Auth::check()){
                $items = Collection::make();

                $modules = Module::with('roles', 'submodules.permission')->get();

                foreach ($modules as $module) {
                    $moduleRoles = $module->roles->pluck('name');

                    if ($moduleRoles->intersect(Auth::user()->getRoleNames())->isNotEmpty()) {
                        $submodules = $module->submodules->filter(function ($submodule) {
                            return Auth::user()->hasDirectPermission($submodule->permission->name);
                        });

                        if (!$submodules->isEmpty()) {
                            $items->push((object) [
                                'name' => $module->name,
                                'icon' => $module->icon,
                                'submodules' => $submodules->map(function ($submodule) {
                                    return (object) [
                                        'name' => $submodule->name,
                                        'url' => $submodule->url,
                                        'icon' => $submodule->icon,
                                        'permission' => $submodule->permission->name,
                                    ];
                                }),
                            ]);
                        }
                    }
                }

                View::share(['items' => $items]);
            }
        });
    }
}
