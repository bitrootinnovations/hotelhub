<?php

namespace App\Providers;
use View;
use Illuminate\Support\ServiceProvider;
use Auth;
use App\Models\PermissionMaster;
use Illuminate\Support\Facades\Session;


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

        view()->composer('*', function($view)
    {
        $files = glob(storage_path('framework/views/*'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        if (file_exists(base_path('bootstrap/cache/config.php'))) {
            unlink(base_path('bootstrap/cache/config.php'));
        }
        if (file_exists(base_path('bootstrap/cache/routes-v7.php'))) {
            unlink(base_path('bootstrap/cache/routes-v7.php'));
        }
        
       
        $base_url = rtrim(config('app.url'), '/');

        if (Auth::check()) {
            Session::put('base_url', $base_url);

            $user = Auth::user();

            if ($user->role_id == 1) {
                // Admin — full access to everything
                $userPermissions = collect([]);
            } else {
                // Keyed by menu_name, stores full permission row
                $userPermissions = PermissionMaster::where('role_id', $user->role_id)
                    ->get()
                    ->keyBy('menu_name');
            }

            View::share('isAdmin',      $user->role_id == 1);
            View::share('isClientUser', !is_null($user->client_id));
            View::share('userPermissions', $userPermissions ?? collect([]));
        } else {
            View::share('isAdmin',      false);
            View::share('isClientUser', false);
            View::share('userPermissions', collect([]));
        }

        View::share('base_url', $base_url);
    });
    
    }
    
}
