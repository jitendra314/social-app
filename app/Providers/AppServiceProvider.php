<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\FriendRequest;
use Illuminate\Pagination\Paginator;

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
        View::composer('*', function ($view) {
            $pendingRequestCount = 0;

            if (Auth::check()) {
                $pendingRequestCount = FriendRequest::where('receiver_id', Auth::id())
                    ->where('accepted', false)
                    ->count();
            }

            $view->with('pendingRequestCount', $pendingRequestCount);
        });

        Paginator::useBootstrap();
    }
}
