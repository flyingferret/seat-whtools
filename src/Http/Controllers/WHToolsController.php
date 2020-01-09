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
            
            $personal_stock_contracts = ContractDetail::where('issuer_corporation_id','=',$corporation_id)
                ->where('title', 'LIKE', '%'.$stocklvl->fitting->shiptype.' '.$stocklvl->fitting->fitname.'%')
                ->where('for_corporation', '=', '0')
                ->where('status','LIKE','outstanding')
                ->get();
            
            array_push($stock, [
                'id' =>  $stocklvl->id,
                'minlvl' =>  $stocklvl->minLvl,
                'stock' =>  count($stock_contracts),
                'personal_stock' => count($personal_stock_contracts),
                'fitting_id' =>  $stocklvl ->fitting_id,
                'fitname' => $stocklvl->fitting->fitname,
                'shiptype' =>$stocklvl->fitting->shiptype,
                'typeID' => $ship->typeID
            ]);
        }
        return $stock;
        
        
    }
    
    public function saveStocking(StocklvlValidation $request){
        $stocklvl = Stocklvl::firstOrNew(['fitting_id' =>$request->selectedfit]);

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
    public function getBlueSalesView()
    {
        $bluesales = $this->getBlueSales();
        
        return view('whtools::bluesales', compact('bluesales', 'stock','contracts'));
    }    
    public function getBlueSales()
    {
        $bluesales = [];
        $bluelootIDs = [30747,30744,30745,30746,21572,30378,30377,30376,30375,21585,20110,30373,30370,30374,21570,21721,21722,21720,21723,21073,21584,30371,21586,34431,45611];
        
        
        /*$transactions = CharacterWalletTransaction::all();*/
        
        $transactions = CharacterWalletTransaction::with('type')->get();
        $transactions = $transactions->whereIn('type.typeID',$bluelootIDs)
            ->where('is_buy',false)
            ->all();
        
        foreach($transactions as $trans){
            $mainCharacterInfo = User::find($trans->character_id)->group->main_character;
            array_push($bluesales,[
                'transaction_id'=>$trans->transaction_id,
                'maincharacter'=> $mainCharacterInfo->name,
                'maincorpID'=>$mainCharacterInfo->corporation_id,
                'transcharacterID'=>$trans->character_id,
                'date'=>$trans->date,
                'itemID'=>$trans->type->typeID,
                'quantity'=>$trans->quantity,
                'unitprice'=>($this->bd_nice_number($trans->unit_price)),
                'total'=>($this->bd_nice_number($trans->quantity * $trans->unit_price))
            ]);
        }
        
        return $bluesales;
    }
    function bd_nice_number($n) {
        // first strip any formatting;
        $n = (0+str_replace(",","",$n));
       
        // is this a number?
        if(!is_numeric($n)) return false;
       
        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),1).' Tril ISK';
        else if($n>1000000000) return round(($n/1000000000),1).' Bil ISK';
        else if($n>1000000) return round(($n/1000000),1).' Mil ISK';
        else if($n>1000) return round(($n/1000),1).'K ISK ';
       
        return number_format($n);
    }

}
