<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Message;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        View::composer('doctor.layout', function ($view) {
            $numberOfNotifications = 0;
            if(auth()->check()) {
                $numberOfNotifications = Message::where('receiver_id', auth()->id())
                                             ->where('is_read', false)
                                             ->count();
            }
            
            $view->with('numberOfNotifications', $numberOfNotifications);
        });
    }
} 