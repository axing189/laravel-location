<?php

/**
 * YFSNS社交网络服务系统
 *
 * Copyright (C) 2025 合肥音符信息科技有限公司
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Yfsns\LaravelLocation\Providers;

use Yfsns\LaravelLocation\Services\LocationManager;
use Yfsns\LaravelLocation\Services\LocationService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * 定位服务提供者.
 */
class LocationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/location.php',
            'location'
        );

        $this->app->singleton(LocationManager::class, function ($app) {
            return new LocationManager();
        });

        $this->app->singleton(LocationService::class, function ($app) {
            return new LocationService($app->make(LocationManager::class));
        });

        $this->app->alias(LocationService::class, 'location');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        Route::prefix('api/v1')
            ->middleware(['api'])
            ->group(function (): void {
                $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
            });

        $this->registerPublishing();
    }

    /**
     * 注册资源发布
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'laravel-location-migrations');

            $this->publishes([
                __DIR__ . '/../config/location.php' => config_path('location.php'),
            ], 'laravel-location-config');
        }
    }
}
