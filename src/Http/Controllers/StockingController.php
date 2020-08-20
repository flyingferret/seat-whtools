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

use Denngarr\Seat\Fitting\Http\Controllers\FittingController;
use Denngarr\Seat\Fitting\Models\Sde\InvType;
use FlyingFerret\Seat\WHTools\Models\Stocklvl;
use FlyingFerret\Seat\WHTools\Validation\StocklvlValidation;
use Seat\Eveapi\Models\Contracts\ContractDetail;

class StockingController extends FittingController
{
    public function getStockingView()
    {
        $stock = $this->getStockList();

        $fitlist = $this->getFittingList();

        return view('whtools::stocking', compact('fitlist', 'stock'));
    }

    public function getStockList()
    {
        $stocklvllist = Stocklvl::all();
        $stock = [];

        if ($stocklvllist->isEmpty())
            return $stock;

        $corporation_id = auth()->user()->main_character->affiliation->corporation->entity_id;

        foreach ($stocklvllist as $stocklvl) {
            $ship = InvType::where('typeName', $stocklvl->fitting->shiptype)->first();

            //Contracts made to the corp but by corp members not on behalf of the corp
            $member_stock_contracts = ContractDetail::where('issuer_corporation_id', '=', $corporation_id)
                ->where('title', 'LIKE', '%' . ($stocklvl->fitting->shiptype) . ' ' . trim($stocklvl->fitting->fitname) . '%')
                ->where('for_corporation', '=', '0')
                ->where('status', 'LIKE', 'outstanding')
                ->get();
            //Contracts made to the corp by corp members on behalf of the corp
            $stock_contracts = ContractDetail::where('issuer_corporation_id', '=', $corporation_id)
                ->where('title', 'LIKE', '%' . ($stocklvl->fitting->shiptype) . ' ' . trim($stocklvl->fitting->fitname) . '%')
                ->where('for_corporation', '=', '1')
                ->where('status', 'LIKE', 'outstanding')
                ->get();

            $totalContractsValue = 0;

            foreach ($stock_contracts as $contract) {
                $totalContractsValue += $contract->price;
            }

            array_push($stock, [
                'id' => $stocklvl->id,
                'minlvl' => $stocklvl->minLvl,
                'stock' => $stock_contracts->count(),
                'members_stock' => $member_stock_contracts->count(),
                'fitting_id' => $stocklvl->fitting_id,
                'fitname' => $stocklvl->fitting->fitname,
                'shiptype' => $stocklvl->fitting->shiptype,
                'typeID' => $ship->typeID,
                'totalContractsValue' => $totalContractsValue
            ]);
        }
        return $stock;
    }

    public function saveStocking(StocklvlValidation $request)
    {
        $stocklvl = Stocklvl::firstOrNew(['fitting_id' => $request->selectedfit]);

        $stocklvl->minLvl = $request->minlvl;
        $stocklvl->fitting_id = $request->selectedfit;
        $stocklvl->save();

        $stock = $this->getStockList();
        $fitlist = $this->getFittingList();

        return view('whtools::stocking', compact('fitlist', 'stock'));
    }

    public function deleteStockingById($id)
    {
        Stocklvl::destroy($id);

        return "Success";
    }
}