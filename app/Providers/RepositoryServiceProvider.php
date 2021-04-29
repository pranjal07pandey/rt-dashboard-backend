<?php

namespace App\Providers;

use App\Repositories\DocketRepository;
use App\Repositories\Interfaces\DocketRepositoryInterfaces;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            DocketRepositoryInterfaces::class,
            DocketRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class

        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
