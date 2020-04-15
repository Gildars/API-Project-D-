<?php

namespace App\Providers;

use App\Modules\Character\Application\Contracts\CharacterRepositoryInterface;
use App\Modules\Character\Application\Contracts\LocationRepositoryInterface;
use App\Modules\Character\Application\Contracts\CharacterClassRepositoryInterface;
use App\Modules\Character\Infrastructure\Repositories\CharacterRepository;
use App\Modules\Character\Infrastructure\Repositories\LocationRepository;
use App\Modules\Equipment\Application\Contracts\InventoryRepositoryInterface;
use App\Modules\Equipment\Application\Contracts\ItemPrototypeRepositoryInterface;
use App\Modules\Equipment\Application\Contracts\ItemRepositoryInterface;
use App\Modules\Equipment\Infrastructure\Repositories\InventoryRepository;
use App\Modules\Equipment\Infrastructure\Repositories\ItemPrototypeRepository;
use App\Modules\Equipment\Infrastructure\Repositories\ItemRepository;
use App\Modules\Character\Infrastructure\Repositories\CharacterClassRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepositoryInterfaces();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        date_default_timezone_set(env('TIME_ZONE', 'Europe/Kiev'));
    }

    protected function registerRepositoryInterfaces(): self
    {
        $this->app->bind(
            InventoryRepositoryInterface::class,
            InventoryRepository::class
        );

        $this->app->bind(
            LocationRepositoryInterface::class,
            LocationRepository::class
        );

        $this->app->bind(
            CharacterClassRepositoryInterface::class,
            CharacterClassRepository::class
        );

        $this->app->bind(
            CharacterRepositoryInterface::class,
            CharacterRepository::class
        );

        $this->app->bind(
            ItemPrototypeRepositoryInterface::class,
            ItemPrototypeRepository::class
        );

        $this->app->bind(
            ItemRepositoryInterface::class,
            ItemRepository::class
        );

        $this->app->bind(
            BattleRepositoryInterface::class,
            BattleRepository::class
        );


        return $this;
    }
}
