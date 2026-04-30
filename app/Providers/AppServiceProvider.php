<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;
use App\Observers\ActivityLogObserver;
use App\Helpers\StaticDataHelper;
use App\Models\RoleRoute;
use Livewire\Livewire;
use App\Http\Livewire\ChatComponent;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * ✅ Automatically attach ActivityLogObserver to all models in app/Models
         * This includes all models that extend Model or Authenticatable.
         */
        $modelsPath = app_path('Models');

        if (File::isDirectory($modelsPath)) {
            $modelFiles = File::allFiles($modelsPath);

            foreach ($modelFiles as $file) {
                $className = 'App\\Models\\' . pathinfo($file->getFilename(), PATHINFO_FILENAME);

                if (class_exists($className)) {
                    // Only attach observer to Eloquent Models (not ActivityLog itself)
                    if (
                        is_subclass_of($className, \Illuminate\Database\Eloquent\Model::class)
                        && $className !== 'App\\Models\\ActivityLog'
                    ) {
                        $className::observe(ActivityLogObserver::class);
                    }
                }
            }
        }

        // ✅ Share static data with all views
        view()->share('cc_items', StaticDataHelper::citizenCornerData());
        view()->share('pps_items', StaticDataHelper::pastProjectsData());

        View::composer('*', function ($view) {
            $user = auth()->user();
            $allowedRoutes = [];

            if ($user) {
                $allowedRoutes = Cache::remember(
                    "role_routes_{$user->role_id}",
                    now()->addMinutes(10),
                    fn () => RoleRoute::where('role_id', $user->role_id)->pluck('route_name')->all()
                );
            }

            $view->with('allowedRoutes', $allowedRoutes);
            $view->with('sidebarUser', $user);
        });

        // ✅ Register Livewire components
        Livewire::component('chat-component', ChatComponent::class);
    }
}
