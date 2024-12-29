<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use App\Helpers\ImageUploadHelper;
use Kreait\Firebase\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ImageUploadHelper::class, function ($app) {
            // Get Firebase Storage instance from the container
            $firebaseStorage = $app->make(Storage::class);
            return new ImageUploadHelper($firebaseStorage);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('nurseAdmin.editBeds', function ($view) {
            $patients = DB::select("
                SELECT id, name, ic_number, email, gender, address, blood_type, contact_number, emergency_contact
                FROM users 
                WHERE role = 'patient' 
                ORDER BY name ASC
            ");
            
            $view->with('patients', $patients);
        });

        Blade::component('components.task', 'task');
    }
}
