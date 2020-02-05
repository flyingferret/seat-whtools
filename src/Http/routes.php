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
        Route::get('/config}', [
            'as'   => 'whtools.config',
            'uses' => 'WHtoolsController@getConfigView',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::post('/config/post', [
            'as'   => 'whtools.config.post',
            'uses' => 'WHtoolsController@postConfig'
        ]); 
        Route::get('/bluetaxpayments/data/', [
            'as'   => 'whtools.bluetaxpayments.data',
            'uses' => 'WHtoolsController@getTaxPaymentsData',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::get('/bluetaxpayments/data/bydate/{start}/{end}', [
            'as'   => 'whtools.bluetaxpayments.data.bydate',
            'uses' => 'WHtoolsController@getTaxPaymentsData',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::get('/bluetaxpayments', [
            'as'   => 'whtools.bluetaxpayments',
            'uses' => 'WHtoolsController@getTaxPaymentsView',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
    
        Route::get('/bluetaxpayments/{start}/{end}', [
            'as'   => 'whtools.bluetaxpayments.bydate',
            'uses' => 'WHtoolsController@getTaxPaymentsView',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::get('/bluetaxpaymenttotals', [
            'as'   => 'whtools.bluetaxpayment.totals',
            'uses' => 'WHtoolsController@getTaxPaymentTotalsView',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
    
        Route::get('/bluetaxpaymenttotals/{start}/{end}', [
            'as'   => 'whtools.bluetaxpayment.totals.bydate',
            'uses' => 'WHtoolsController@getTaxPaymentTotalsView',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::get('/bluetaxpaymenttotals/data/', [
            'as'   => 'whtools.bluetaxpayment.totals.data',
            'uses' => 'WHtoolsController@getTaxPaymentTotalsData',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        Route::get('/bluetaxpaymenttotals/data/bydate/{start}/{end}', [
            'as'   => 'whtools.bluetaxpayment.totals.data.bydate',
            'uses' => 'WHtoolsController@getTaxPaymentTotalsData',
            'middleware' => 'bouncer:whtools.bluetaxview'
        ]);
        //Routes for Skill Checker
        Route::get('/certificates/', [
        'as'   => 'whtools.certificates',
        'uses' => 'SkillCheckerController@getCertificatesView',
        'middleware' => 'bouncer:whtools.certview'
        ]);
        Route::post('/addCertificate', [
        'as'   => 'whtools.addCertificate',
        'uses' => 'SkillCheckerController@saveCertificate',
        'middleware' => 'bouncer:whtools.certManager'
        ]);
        Route::get('/skilllist', [
        'as'   => 'whtools.skilllist',
        'uses' => 'SkillCheckerController@getAllSkills',
        'middleware' => 'bouncer:whtools.certview'
        ]);
        Route::get('/getcertbyid/{id}', [
        'as'   => 'whtools.getcertbyid',
        'uses' => 'SkillCheckerController@getCertificateByID',
        'middleware' => 'bouncer:whtools.certview'
        ]);
        Route::get('/delcert/{id}', [
        'as'   => 'whtools.delcert',
        'uses' => 'SkillCheckerController@delCertificate',
        'middleware' => 'bouncer:whtools.certManager'
        ]);
        Route::get('/getcertedit/{id}',[
            'as'   => 'whtools.getcertedit',
            'uses' => 'SkillCheckerController@getCertEdit',
            'middleware' => 'bouncer:whtools.certManager'
        ]);

         Route::get('/getcharskills/{id}',[
            'as'   => 'whtools.getcharskills',
            'uses' => 'SkillCheckerController@getCharacterSkills',
            'middleware' => 'bouncer:whtools.certview'
        ]);
        Route::get('/getcharskills/{charID}/',[
            'as'   => 'whtools.getcharskills',
            'uses' => 'SkillCheckerController@getCharacterSkills',
            'middleware' => 'bouncer:whtools.certview'
        ]);
        Route::get('/getcharcert/{charID}/',[
        'as'   => 'whtools.getcharskills',
        'uses' => 'SkillCheckerController@getCharacterCerts',
        'middleware' => 'bouncer:whtools.certview'
        ]);
        Route::get('/getcorpcert/{corpID}/',[
        'as'   => 'whtools.getcharskills',
        'uses' => 'SkillCheckerController@getCorporationCertificates',
        'middleware' => 'bouncer:whtools.certview'
        ]);
    Route::get('/test/',[
        'as'   => 'whtools.test',
        'uses' => 'SkillCheckerController@test',
        'middleware' => 'bouncer:whtools.certview'
    ]);
    Route::get('/corpcertcoverchart/{id}',[
        'as'   => 'whtools.certCoverageChart',
        'uses' => 'SkillCheckerController@getCorporationCertificateCoverageChartData',
        'middleware' => 'bouncer:whtools.certview'
    ]);
    });
