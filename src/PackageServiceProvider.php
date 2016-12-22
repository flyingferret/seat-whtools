<?php
/*
This file is part of SeAT

Copyright (C) 2015, 2016  Leon Jacobs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace Seat\Web;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class EveapiServiceProvider
 * @package Seat\Eveapi
 */
class WebServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router)
    {

        // Include the Routes
        $this->add_routes();

        // Publish the JS & CSS, and Database migrations
        $this->add_publications();

        // Add the views for the 'web' namespace
        $this->add_views();

        // Include our translations
        $this->add_translations();

    }

    /**
     * Include the routes
     */
    public function add_routes()
    {

        if (!$this->app->routesAreCached())
            include __DIR__ . '/Http/routes.php';
    }

    /**
     * Set the paths for migrations and assets that
     * should be published to the main application
     */
    public function add_publications()
    {

        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ]);
    }

    /**
     * Set the path and namespace for the vies
     */
    public function add_views()
    {

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'yourpackage');
    }

    /**
     * Include the translations and set the namespace
     */
    public function add_translations()
    {

        $this->loadTranslationsFrom(__DIR__ . '/lang', 'yourpackage');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        // Merge the config with anything in the main app
        // Web package configurations
        $this->mergeConfigFrom(
            __DIR__ . '/Config/web.config.php', 'web.config');
        $this->mergeConfigFrom(
            __DIR__ . '/Config/web.permissions.php', 'web.permissions');
        $this->mergeConfigFrom(
            __DIR__ . '/Config/web.locale.php', 'web.locale');
        $this->mergeConfigFrom(
            __DIR__ . '/Config/web.supervisor.php', 'web.supervisor');

        // Menu Configurations
        $this->mergeConfigFrom(
            __DIR__ . '/Config/package.sidebar.php', 'package.sidebar');

    }

}
