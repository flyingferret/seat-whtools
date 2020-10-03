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
    'whtools' => [
        'name' => 'WHTools',
        'label' => 'whtools::seat.name',
        'icon' => 'fa fa-sun-o',
        'route_segment' => 'whtool',
        'permission' => ['whtools.stockview', 'whtools.taxview', 'whtools.certview'],
        'entries' => [
            [
                'name' => 'Stocking',
                'label' => 'whtools::seat.stocking',
                'icon' => 'fa fa-suitcase',
                'route' => 'whtools.stocking',
                'permission' => 'whtools.stockview'
            ],
            [
                'name' => 'Blue Sales',
                'label' => 'whtools::seat.bluetax',
                'icon' => 'fa fa-truck',
                'route' => 'whtools.bluesales',
                'permission' => 'whtools.bluetaxview'
            ],
            [
                'name' => 'config',
                'label' => 'web::seat.configuration',
                'icon' => 'fa fa-cog',
                'route' => 'whtools.config',
                'permission' => 'whtools.configuration'
            ],
            [
                'name' => 'Certificates',
                'label' => 'whtools::seat.certificates',
                'icon' => 'fa fa-book',
                'route' => 'whtools.certificates',
                'permission' => 'whtools.certview'
            ]/*,
            [
                'name' => 'Loot Calculator',
                'icon' => 'fa fa-money',
                'route' => 'whtools.lootcalc',
                'permission' => 'whtools.certview'
            ]*/

        ]
    ]

];
