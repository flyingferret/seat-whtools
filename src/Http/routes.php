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
    'middleware' => ['web', 'auth', 'locale']
], function () {
    // Your route definitions go here.
    Route::get('/', [
        'as' => 'view',
        'uses' => 'WHtoolsController@getHome'
    ]);
    //Routes for Doctine stocking
    Route::get('/stocking', [
        'as' => 'whtools.stocking',
        'uses' => 'StockingController@getStockingView',
        'middleware' => 'can:whtools.stockview'
    ]);

    Route::post('/saveStocking', [
        'as' => 'whtools.saveStocking',
        'uses' => 'StockingController@saveStocking',
        'middleware' => 'can:whtools.stockedit'
    ]);
    Route::get('/delstockingbyid/{id}', [
        'uses' => 'StockingController@deleteStockingById',
        'middleware' => 'can:whtools.stockedit'
    ]);
    Route::get('/showContractIG/{id}/{token}', [
        'as' => 'whtools.test',
        'uses' => 'WHToolsController@testEseye',
        'middleware' => 'can:whtools.stockedit'
    ]);
    //routes for blue loot tax audits
    Route::get('/bluesales', [
        'as' => 'whtools.bluesales',
        'uses' => 'WHtoolsController@getBlueSalesView',
        'middleware' => 'can:whtools.bluetaxview'
    ]);

    Route::get('/bluesales/{start}/{end}', [
        'as' => 'whtools.bluesalesbydate',
        'uses' => 'WHtoolsController@getBlueSalesView',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/bluesales/data/', [
        'as' => 'whtools.bluesales.data',
        'uses' => 'WHtoolsController@getBlueSalesData',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/bluesales/data/bydate/{start}/{end}', [
        'as' => 'whtools.bluesales.databydate',
        'uses' => 'WHtoolsController@getBlueSalesData',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/bluesales/totals/data/bydate/{start}/{end}', [
        'as' => 'whtools.bluesaletotals.databydate',
        'uses' => 'WHtoolsController@getBlueSaleTotalsData',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/bluetotals/{start}/{end}', [
        'as' => 'whtools.bluetotals',
        'uses' => 'WHtoolsController@getBlueSaleTotalsView',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/config}', [
        'as' => 'whtools.config',
        'uses' => 'WHtoolsController@getConfigView',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::post('/config/post', [
        'as' => 'whtools.config.post',
        'uses' => 'WHtoolsController@postConfig'
    ]);
    Route::get('/bluetaxpayments/data/', [
        'as' => 'whtools.bluetaxpayments.data',
        'uses' => 'WHtoolsController@getTaxPaymentsData',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/bluetaxpayments/data/bydate/{start}/{end}', [
        'as' => 'whtools.bluetaxpayments.data.bydate',
        'uses' => 'WHtoolsController@getTaxPaymentsData',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/bluetaxpayments', [
        'as' => 'whtools.bluetaxpayments',
        'uses' => 'WHtoolsController@getTaxPaymentsView',
        'middleware' => 'can:whtools.bluetaxview'
    ]);

    Route::get('/bluetaxpayments/{start}/{end}', [
        'as' => 'whtools.bluetaxpayments.bydate',
        'uses' => 'WHtoolsController@getTaxPaymentsView',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/bluetaxpaymenttotals', [
        'as' => 'whtools.bluetaxpayment.totals',
        'uses' => 'WHtoolsController@getTaxPaymentTotalsView',
        'middleware' => 'can:whtools.bluetaxview'
    ]);

    Route::get('/bluetaxpaymenttotals/{start}/{end}', [
        'as' => 'whtools.bluetaxpayment.totals.bydate',
        'uses' => 'WHtoolsController@getTaxPaymentTotalsView',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/bluetaxpaymenttotals/data/', [
        'as' => 'whtools.bluetaxpayment.totals.data',
        'uses' => 'WHtoolsController@getTaxPaymentTotalsData',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    Route::get('/bluetaxpaymenttotals/data/bydate/{start}/{end}', [
        'as' => 'whtools.bluetaxpayment.totals.data.bydate',
        'uses' => 'WHtoolsController@getTaxPaymentTotalsData',
        'middleware' => 'can:whtools.bluetaxview'
    ]);
    //Routes for Skill Checker
    Route::get('/certificates/', [
        'as' => 'whtools.certificates',
        'uses' => 'CertificateController@getCertificatesView',
        'middleware' => 'can:whtools.certview'
    ]);
    Route::post('/addCertificate', [
        'as' => 'whtools.addCertificate',
        'uses' => 'CertificateController@saveCertificate',
        'middleware' => 'can:whtools.certManager'
    ]);
    Route::get('/skilllist', [
        'as' => 'whtools.skilllist',
        'uses' => 'CertificateController@getAllSkills',
        'middleware' => 'can:whtools.certview'
    ]);
    Route::get('/getcertbyid/{id}', [
        'as' => 'whtools.getcertbyid',
        'uses' => 'CertificateController@getCertificateByID',
        'middleware' => 'can:whtools.certview'
    ]);
    Route::get('/delcert/{id}', [
        'as' => 'whtools.delcert',
        'uses' => 'CertificateController@delCertificate',
        'middleware' => 'can:whtools.certManager'
    ]);
    Route::get('/getcertedit/{id}', [
        'as' => 'whtools.getcertedit',
        'uses' => 'CertificateController@getCertEdit',
        'middleware' => 'can:whtools.certManager'
    ]);

    Route::get('/getcharskills/{id}', [
        'as' => 'whtools.getcharskills',
        'uses' => 'CertificateController@getCharacterSkills',
        'middleware' => 'can:whtools.certview'
    ]);
    Route::get('/getcharskills/{charID}/', [
        'as' => 'whtools.getcharskills',
        'uses' => 'CertificateController@getCharacterSkills',
        'middleware' => 'can:whtools.certview'
    ]);
    Route::get('/getcharcert/{charID}/', [
        'as' => 'whtools.getcharskills',
        'uses' => 'CertificateController@getCharacterCerts',
        'middleware' => 'can:whtools.certview'
    ]);
    Route::get('/getcorpcert/{corpID}/', [
        'as' => 'whtools.getcharskills',
        'uses' => 'CertificateController@getCorporationCertificates',
        'middleware' => 'can:whtools.certview'
    ]);
    Route::get('/test/', [
        'as' => 'whtools.test',
        'uses' => 'CertificateController@test',
        'middleware' => 'can:whtools.certview'
    ]);
    Route::get('/corpcertcoverchart/{id}', [
        'as' => 'whtools.certCoverageChart',
        'uses' => 'CertificateController@getCorporationCertificateCoverageChartData',
        'middleware' => 'can:whtools.certview'
    ]);
});
