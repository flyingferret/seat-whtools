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

namespace FlyingFerret\Seat\WHTools\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Denngarr\Seat\Fitting\Http\Controllers\FittingController;

use Seat\Services\Repositories\Character\Info;
use Seat\Services\Repositories\Character\Skills;
use Seat\Services\Repositories\Configuration\UserRespository;


use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Denngarr\Seat\Fitting\Helpers\CalculateConstants;
use Denngarr\Seat\Fitting\Helpers\CalculateEft;
use Denngarr\Seat\Fitting\Models\Fitting;
use Denngarr\Seat\Fitting\Models\Doctrine;
use Denngarr\Seat\Fitting\Models\Sde\InvType;
use Denngarr\Seat\Fitting\Models\Sde\DgmTypeAttributes;
use Denngarr\Seat\Fitting\Validation\FittingValidation;
use Denngarr\Seat\Fitting\Validation\DoctrineValidation;

/**
 * Class HomeController
 * @package Author\Seat\YourPackage\Http\Controllers
 */
class WHtoolsController extends FittingController
{

    /**
     * @return \Illuminate\View\View
     */
    public function getHome()
    {

        return view('whtools::whtools');
    }
    public function getFittingView()
    {
        $corps = [];
        $fitlist = $this->getFittingList();
        if (auth()->user()->hasSuperUser()) {
            $corpnames = CorporationInfo::all();
        } else {
            $corpids = CharacterInfo::whereIn('character_id', auth()->user()->associatedCharacterIds())->select('corporation_id')->get()->toArray();
            $corpnames = CorporationInfo::whereIn('corporation_id', $corpids)->get();
        }
        foreach ($corpnames as $corp) {
          $corps[$corp->corporation_id] = $corp->name;
        }
        return view('whtools::stocking', compact('fitlist', 'corps'));
    }

}
