<?php

namespace App\Providers;

use App\Services\AdminChatUnreadService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer(
            'layouts.admin',
            function ($view) {
                $user = auth()->user();
                $unreadCount = 0;

                if (
                    $user
                    && $user->role?->name === 'admin'
                ) {
                    $unreadCount = app(
                        AdminChatUnreadService::class
                    )->countForAdmin(
                        (int) $user->id
                    );
                }

                $view->with(
                    'adminUnreadChatCount',
                    $unreadCount
                );
            }
        );
    }
}
