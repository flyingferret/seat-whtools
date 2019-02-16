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


use Denngarr\Seat\Fitting\Models\Fitting;
use Denngarr\Seat\Fitting\Models\Sde\InvType;

use Seat\Eveapi\Models\Contracts\ContractDetail;
use FlyingFerret\Seat\WHTools\Models\Stocklvl;
use FlyingFerret\Seat\WHTools\Validation\StocklvlValidation;

use Seat\Eveapi\Models\Wallet\CharacterWalletTransaction;
use Seat\Web\Models\User;

use Yajra\DataTables\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Seat\Eveapi\Models\Character\CharacterInfo;

use DateTime;

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
    public function getStockingView()
    {
        $stock = $this->getStockList();
          
        $fitlist = $this->getFittingList();
        
        return view('whtools::stocking', compact('fitlist', 'stock','contracts'));
    }
    
    public function getStockLvls(){
        return Stocklvl::all();
    }
        
    
    public function getStockList(){
        $stocklvllist = $this->getStockLvls();
        $stock = [];
        
        if(count($stocklvllist)<= 0)
            return $stock;
        
        foreach($stocklvllist as $stocklvl){
            $ship = InvType::where('typeName', $stocklvl->fitting->shiptype)->first();

            $corporation_id = auth()->user()->character->corporation_id;
            
            $stock_contracts = [];
           
            $stock_contracts = ContractDetail::where('issuer_corporation_id','=',$corporation_id)
                ->where('title', 'LIKE', '%'.$stocklvl->fitting->shiptype.' '.$stocklvl->fitting->fitname.'%')
                ->where('for_corporation', '=', '1')
                ->where('status','LIKE','outstanding')
                ->get();
            
            array_push($stock, [
                'id' =>  $stocklvl->id,
                'minlvl' =>  $stocklvl->minLvl,
                'stock' =>  count($stock_contracts),
                'fitting_id' =>  $stocklvl ->fitting_id,
                'fitname' => $stocklvl->fitting->fitname,
                'shiptype' =>$stocklvl->fitting->shiptype,
                'typeID' => $ship->typeID
            ]);
        }
        return $stock;
        
        
    }
    
    public function saveStocking(StocklvlValidation $request){
        $stocklvl = Stocklvl::firstOrNew(['fitting_id'=>$request->selectedfit]);

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
    public function getBlueSalesView($startdate = null, $enddate = null)
    {
        if($startdate != null and $enddate != null){
            
            $daterange = ['start'=>$startdate,'end'=>$enddate];
            
        }else{
            $daterange = ['start'=>'2018-02-01T00:00:00.000Z','end'=>'2035-02-01T00:00:00.000Z'];
        }
        return view('whtools::bluesales', compact('daterange'));
    }    
    public function getBlueSalesData($startdate = null, $enddate = null)
    {
  
        /*$transactions = CharacterWalletTransaction::all();*/
        
        $transactions = $this->getBlueLootTransactions();
        if($startdate != null and $enddate != null){
            $startdate = new DateTime($startdate);
            $enddate = new DateTime($enddate);
            
            $transactions = $transactions->whereBetween('date',array($startdate,$enddate));
        }

        return DataTables::of($transactions)->editColumn('is_buy', function ($row) {
                return view('web::partials.transactionbuysell', compact('row'));
            })
            ->editColumn('unit_price', function ($row) {
                return number($row->unit_price);
            })
            ->editColumn('sum', function ($row) {
                return number($row->sum);
            })
            ->addColumn('item_view', function ($row) {
                return view('web::partials.transactiontype', compact('row'));
            })
            ->addColumn('total', function ($row) {
                return number($row->unit_price * $row->quantity);
            })
            ->addColumn('client_view', function ($row) {
                $character_id = $row->character_id;
                $character = CharacterInfo::find($row->client_id) ?: $row->client_id;
                return view('web::partials.character', compact('character', 'character_id'));
            })
            ->addColumn('seller_view', function ($row) {
                $character_id = $row->character_id;
                $character = CharacterInfo::find($row->character_id) ?: $row->character_id;
                return view('web::partials.character', compact('character', 'character_id'));
            })
            ->rawColumns(['is_buy', 'client_view', 'item_view','seller_view'])
            ->make(true);
        
    }

     public function getBlueLootTransactions($startdate = null, $enddate = null) : Builder
    {
         $bluelootIDs = [30747,30744,30745,30746,21572,30378,30377,30376,30375,21585,20110,30373,30370,30374,21570,21721,21722,21720,21723,21073,21584,30371,21586,34431];
        return CharacterWalletTransaction::with('client', 'type')
            ->select(DB::raw('
            *, CASE
                when character_wallet_transactions.location_id BETWEEN 66015148 AND 66015151 then
                    (SELECT s.stationName FROM staStations AS s
                      WHERE s.stationID=character_wallet_transactions.location_id-6000000)
                when character_wallet_transactions.location_id BETWEEN 66000000 AND 66014933 then
                    (SELECT s.stationName FROM staStations AS s
                      WHERE s.stationID=character_wallet_transactions.location_id-6000001)
                when character_wallet_transactions.location_id BETWEEN 66014934 AND 67999999 then
                    (SELECT d.name FROM `sovereignty_structures` AS c
                      JOIN universe_stations d ON c.structure_id = d.station_id
                      WHERE c.structure_id=character_wallet_transactions.location_id-6000000)
                when character_wallet_transactions.location_id BETWEEN 60014861 AND 60014928 then
                    (SELECT d.name FROM `sovereignty_structures` AS c
                      JOIN universe_stations d ON c.structure_id = d.station_id
                      WHERE c.structure_id=character_wallet_transactions.location_id)
                when character_wallet_transactions.location_id BETWEEN 60000000 AND 61000000 then
                    (SELECT s.stationName FROM staStations AS s
                      WHERE s.stationID=character_wallet_transactions.location_id)
                when character_wallet_transactions.location_id BETWEEN 61000000 AND 61001146 then
                    (SELECT d.name FROM `sovereignty_structures` AS c
                      JOIN universe_stations d ON c.structure_id = d.station_id
                      WHERE c.structure_id=character_wallet_transactions.location_id)
                when character_wallet_transactions.location_id > 61001146 then
                    (SELECT name FROM `universe_structures` AS c
                     WHERE c.structure_id = character_wallet_transactions.location_id)
                else (SELECT m.itemName FROM mapDenormalize AS m
                    WHERE m.itemID=character_wallet_transactions.location_id) end
                AS locationName'
            ))
            ->whereIn('type_id',$bluelootIDs)
            ->where('is_buy',False);
            
    }

    public function getBlueSaleTotalsData($startdate = null, $enddate = null)
    {

        $transactions = $this->getBlueLootTransactions()
        ->selectRaw('sum(unit_price*quantity) as sum')
        ->groupBy('character_id');
            
        if($startdate != null and $enddate != null){
            $startdate = new DateTime($startdate);
            $enddate = new DateTime($enddate);

            $transactions = $transactions->whereBetween('date',array($startdate,$enddate));
        }
        

        return DataTables::of($transactions)->editColumn('is_buy', function ($row) {
                return view('web::partials.transactionbuysell', compact('row'));
            })
            ->editColumn('unit_price', function ($row) {
                return number($row->unit_price);
            })
            ->editColumn('sum', function ($row) {
                return number($row->sum);
            })
            ->addColumn('item_view', function ($row) {
                return view('web::partials.transactiontype', compact('row'));
            })
            ->addColumn('total', function ($row) {
                return number($row->unit_price * $row->quantity);
            })
            ->addColumn('client_view', function ($row) {
                $character_id = $row->character_id;
                $character = CharacterInfo::find($row->client_id) ?: $row->client_id;
                return view('web::partials.character', compact('character', 'character_id'));
            })
            ->addColumn('seller_view', function ($row) {
                $character_id = $row->character_id;
                $character = CharacterInfo::find($row->character_id) ?: $row->character_id;
                return view('web::partials.character', compact('character', 'character_id'));
            })
            -addColumn('seller_main',function($row){
                
            })
            ->rawColumns(['is_buy', 'client_view', 'item_view','seller_view'])
            ->make(true); 
    }
    public function getBlueSaleTotalsView($startdate = null, $enddate = null)
    {
        if($startdate != null and $enddate != null){
            
            $daterange = ['start'=>$startdate,'end'=>$enddate];
            
        }else{
            $daterange = ['start'=>'2018-02-01T00:00:00.000Z','end'=>'2035-02-01T00:00:00.000Z'];
        }
        return view('whtools::bluesaletotals', compact('daterange'));
    } 

}
