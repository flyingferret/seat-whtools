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

return [
    // Integrating with the SeAT menu is defined here.
    // Refer to the web package for a structure reference.
    'whtools' => [
        'name' => 'WHTools',
        'icon' => 'fa fa-sun-o',
        'route_segment' => 'whtool',
        'permission'=>['whtools.stockview','whtools.taxview'],        
        'entries' => [
            'whtools'=>[
                    'name' => 'Stocking',
                    'icon' => 'fa fa-suitcase',
                    'route' => 'whtools.stocking',
                    'permission'=>'whtools.stockview'
                ],
                
            ]
        ]

    ];


/*return [
    'doctrine' => [
        'name' => 'Doctrines & Fittings',
        'permission' => 'fitting.doctrineview',
        'route_segment' => 'fitting',
        'icon' => 'fa-rocket',
        'entries'       => [
            'fitting' => [
                'name' => 'Fittings',
                'icon' => 'fa-rocket',
                'route_segment' => 'fitting',
                'route' => 'fitting.view',
                'permission' => 'fitting.view'
            ],
            'doctrine' => [
                'name' => 'Doctrine',
                'icon' => 'fa-list',
                'route_segment' => 'fitting',
                'route' => 'fitting.doctrineview',
                'permission' => 'fitting.doctrineview'
            ],
            'doctrinereport' => [
                'name' => 'Doctrine Report',
                'icon' => 'fa-pie-chart',
                'route_segment' => 'fitting',
                'route' => 'fitting.doctrinereport',
                'permission' => 'fitting.reportview'
            ],
        ]
    ]
];*/