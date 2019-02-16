<?php
/*
This file is part of SeAT

Copyright (C) 2015, 2017  Leon Jacobs

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

// Namespace all of the routes for this package.

Route::group([
    'namespace' => 'FlyingFerret\Seat\WHTools\Http\Controllers',
    'prefix' => 'whtools',
    'middleware' => ['web', 'auth']
], function () {
        // Your route definitions go here.
        Route::get('/', [
            'as'   => 'view',
            'uses' => 'WHtoolsController@getHome'
        ]);
        //Routes for Doctine stocking
        Route::get('/stocking', [
            'as'   => 'whtools.stocking',
            'uses' => 'WHtoolsController@getStockingView',
            'middleware' => 'bouncer:whtools.stockview'
        ]);

        Route::post('/saveStocking', [
            'as'   => 'whtools.saveStocking',
            'uses' => 'WHToolsController@saveStocking',
            'middleware' => 'bouncer:whtools.stockedit'
        ]);
        Route::get('/delstockingbyid/{id}', [
            'uses' => 'WHToolsController@deleteStockingById',
            'middleware' => 'bouncer:whtools.stockedit'
        ]);
        Route::get('/showContractIG/{id}/{token}', [
            'as'=>'whtools.test',
            'uses' => 'WHToolsController@testEseye',
            'middleware' => 'bouncer:whtools.stockedit'
        ]);
        //routes for blue loot tax audits
        Route::get('/bluesales', [
            'as'   => 'whtools.bluesales',
            'uses' => 'WHtoolsController@getBlueSalesView',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
    
        Route::get('/bluesales/{start}/{end}', [
            'as'   => 'whtools.bluesalesbydate',
            'uses' => 'WHtoolsController@getBlueSalesView',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::get('/bluesales/data/', [
            'as'   => 'whtools.bluesales.data',
            'uses' => 'WHtoolsController@getBlueSalesData',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::get('/bluesales/data/bydate/{start}/{end}', [
            'as'   => 'whtools.bluesales.databydate',
            'uses' => 'WHtoolsController@getBlueSalesData',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::get('/bluesales/totals/data/bydate/{start}/{end}', [
            'as'   => 'whtools.bluesaletotals.databydate',
            'uses' => 'WHtoolsController@getBlueSaleTotalsData',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::get('/bluetotals/{start}/{end}', [
            'as'   => 'whtools.bluetotals',
            'uses' => 'WHtoolsController@getBlueSaleTotalsView',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);


    });
