<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\News;
use App\Models\User;
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
        Gate::define('view', function (User $user, News $news) {
            return $user->id === $news->user_id;
        });
        Gate::define('update', function (User $user, News $news) {
            return $user->id === $news->user_id;
        });
        Gate::define('delete', function (User $user, News $news) {
            return $user->id === $news->user_id;
        });
    }
}
