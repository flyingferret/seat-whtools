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

use Seat\Eveapi\Models\Wallet\CharacterWalletTransaction;
use Seat\Web\Models\User;

use Yajra\DataTables\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Seat\Eveapi\Models\Character\CharacterInfo;

use DateTime;
use GuzzleHttp\Client;
use Parsedown;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\Wallet\CorporationWalletJournal;


/**
 * Class HomeController
 * @package Author\Seat\YourPackage\Http\Controllers
 */
class WHtoolsController extends Controller
{

    /**
     * @return \Illuminate\View\View
     */
    public function getHome()
    {

        return view('whtools::whtools');
    }

    #TODO enbale the use of user configurations
    public function getConfigView()
    {
        $changelog = $this->getChangelog();
        $corporationsInfo = CorporationInfo::all();
        $corps = [];

        foreach ($corporationsInfo as $c) {
            array_push($corps, [
                'name' => $c->name,
                'id' => $c->corporation_id
            ]);
        }

        return view('whtools::config', compact('changelog', 'corps'));
    }

    private function getChangelog(): string
    {
        try {
            $response = (new Client())
                ->request('GET', "https://raw.githubusercontent.com/flyingferret/seat-whtools/master/CHANGELOG.md");
            if ($response->getStatusCode() != 200) {
                return 'Error while fetching changelog';
            }
            $parser = new Parsedown();
            return $parser->parse($response->getBody());
        } catch (RequestException $e) {
            return 'Error while fetching changelog';
        }
    }

    /*add validation*/
    public function postConfig()
    {
        setting(['whtools.bluetax.percentage', request('whtools-tax-percentage')], true);
        setting(['whtools.bluetax.collector', request('whtools-tax-collector')], true);

        return redirect()->route('whtools.config');
    }

    #TODO Move to own controller
    public function getBlueSalesView($startdate = null, $enddate = null)
    {
        if ($startdate != null and $enddate != null) {

            $daterange = ['start' => $startdate, 'end' => $enddate];

        } else {
            $daterange = ['start' => '2018-10-01T00:00:00.000Z', 'end' => '2035-02-01T00:00:00.000Z'];
        }
        return view('whtools::bluesales', compact('daterange'));
    }

    #TODO Move to own controller
    public function getBlueSalesData($startdate = null, $enddate = null)
    {
        $transactions = $this->getBlueLootTransactions();
        if ($startdate != null and $enddate != null) {
            $startdate = new DateTime($startdate);
            $enddate = new DateTime($enddate);

            $transactions = $transactions->whereBetween('date', array($startdate, $enddate));
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
                $character = User::find($row->character_id) ?: $row->character_id;
                return view('web::partials.character', compact('character', 'character_id'));
            })
            ->rawColumns(['is_buy', 'client_view', 'item_view', 'seller_view'])
            ->make(true);

    }

    #TODO Move to own controller
    public function getBlueLootTransactions(): Builder
    {
        $bluelootIDs = [30747, 30744, 30745, 30746, 21572, 30378, 30377, 30376, 30375, 21585, 20110, 30373, 30370, 30374, 21570, 21721, 21722, 21720, 21723, 21073, 21584, 30371, 21586, 34431];
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
            ->select('id', 'character_id', 'transaction_id', 'date', 'type_id', 'location_id', 'unit_price', 'quantity', 'client_id', 'is_buy', 'is_personal', 'journal_ref_id')
            ->whereIn('type_id', $bluelootIDs)
            ->where('is_buy', False)
            ->join('users', 'users.id', 'character_id')
            ->selectRaw('unit_price*quantity as sum');

    }

    #TODO Move to own controller
    public function getBlueSaleTotalsData($startdate = null, $enddate = null)
    {

        $transactions = $this->getBlueLootTransactions()
            ->selectRaw('sum(quantity*unit_price) as total , users.group_id')
            ->groupBy('users.group_id');

        if ($startdate != null and $enddate != null) {
            $startdate = new DateTime($startdate);
            $enddate = new DateTime($enddate);

            $transactions = $transactions->whereBetween('date', array($startdate, $enddate));
        }


        return DataTables::of($transactions)
            ->editColumn('is_buy', function ($row) {
                return view('web::partials.transactionbuysell', compact('row'));
            })
            ->editColumn('unit_price', function ($row) {
                return number($row->unit_price);
            })
            ->editColumn('total', function ($row) {
                return number($row->total);
            })
            ->addColumn('item_view', function ($row) {
                return view('web::partials.transactiontype', compact('row'));
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
            ->addColumn('main_view', function ($row) {
                $character = User::find($row->character_id);
                $character = User::find($character->group->main_character_id);
                $character_id = $character->id;
                return view('web::partials.character', compact('character', 'character_id'));
            })
            ->rawColumns(['is_buy', 'client_view', 'item_view', 'seller_view', 'main_view'])
            ->make(true);
    }

    #TODO Move to own controller
    public function getBlueSaleTotalsView($startdate = null, $enddate = null)
    {
        if ($startdate != null and $enddate != null) {

            $daterange = ['start' => $startdate, 'end' => $enddate];

        } else {
            $daterange = ['start' => '2018-10-01T00:00:00.000Z', 'end' => '2035-02-01T00:00:00.000Z'];
        }
        return view('whtools::bluesaletotals', compact('daterange'));
    }

    #TODO Move to own controller
    public function getTaxPayments()
    {
        return CorporationWalletJournal::
        with('first_party', 'second_party')
            ->where('ref_type', '=', 'player_donation')
            ->where('reason', 'like', '%tax%')
            ->select('corporation_id', 'division', 'date', 'ref_type', 'first_party_id', 'second_party_id', 'amount', 'balance', 'reason')
            ->leftjoin('users', 'users.id', 'first_party_id')
            ->leftjoin('user_settings', 'user_settings.group_id', 'users.group_id');


    }

    #TODO Move to own controller
    public function getTaxPaymentsData($startdate = null, $enddate = null)
    {
        $taxPayments = $this->getTaxPayments()
            ->groupBy('users.group_id');
        if ($startdate != null and $enddate != null) {
            $startdate = new DateTime($startdate);
            $enddate = new DateTime($enddate);

            $taxPayments = $taxPayments->whereBetween('date', array($startdate, $enddate));
        }

        return DataTables::of($taxPayments)
            ->editColumn('first_party_id', function ($row) {
                $character_id = $row->character_id;
                if (optional($row->first_party)->category === 'character') {
                    $character = CharacterInfo::find($row->first_party_id) ?: $row->first_party_id;
                    return view('web::partials.character', compact('character', 'character_id'));
                }
                if (optional($row->first_party)->category === 'corporation') {
                    $corporation = CorporationInfo::find($row->first_party_id) ?: $row->first_party_id;
                    return view('web::partials.corporation', compact('corporation', 'character_id'));
                }
                return view('web::partials.unknown', [
                    'unknown_id' => $row->first_party_id,
                    'character_id' => $character_id,
                ]);
            })
            ->addColumn('main_character', function ($row) {
                $character = User::find($row->character_id);
                if (!is_null($character)) {
                    return $character->group->main_character_id;
                } else {
                    return 0;
                }
            })
            ->addColumn('main_view', function ($row) {
                $character_id = $row->main_character;
                if ($row->main_character == 0) {
                    return view('web::partials.unknown', [
                        'unknown_id' => $row->first_party_id,
                        'character_id' => $character_id,
                    ]);
                }
                $character = CharacterInfo::find($character_id());
                return view('web::partials.character', compact('character', 'character_id'));

            })
            ->editColumn('amount', function ($row) {
                return number($row->amount);
            })
            ->rawColumns(['first_party_id', 'main_view'])
            ->make(true);
    }

    #TODO Move to own controller
    public function getTaxPaymentsView($startdate = null, $enddate = null)
    {
        if ($startdate != null and $enddate != null) {

            $daterange = ['start' => $startdate, 'end' => $enddate];

        } else {
            $daterange = ['start' => '2018-10-01T00:00:00.000Z', 'end' => '2035-02-01T00:00:00.000Z'];
        }
        return view('whtools::bluetaxpayments', compact('daterange'));
    }

    #TODO Move to own controller
    public function getTaxPaymentTotalsData($startdate = null, $enddate = null)
    {
        $taxPayments = $this->getTaxPayments()
            ->groupBy('users.group_id')
            ->selectRaw('sum(amount) as total_payments , users.group_id');

        $transactions = $this->getBlueLootTransactions()
            ->selectRaw('sum(quantity*unit_price) as total , users.group_id')
            ->groupBy('users.group_id');

        $combined = $transactions->leftjoin($taxPayments, 'users.group_id');

        if ($startdate != null and $enddate != null) {
            $startdate = new DateTime($startdate);
            $enddate = new DateTime($enddate);

            $taxPayments = $taxPayments->whereBetween('date', array($startdate, $enddate));
        }

        return DataTables::of($taxPayments)
            ->editColumn('first_party_id', function ($row) {
                $character_id = $row->character_id;
                if (optional($row->first_party)->category === 'character') {
                    $character = CharacterInfo::find($row->first_party_id) ?: $row->first_party_id;
                    return view('web::partials.character', compact('character', 'character_id'));
                }
                if (optional($row->first_party)->category === 'corporation') {
                    $corporation = CorporationInfo::find($row->first_party_id) ?: $row->first_party_id;
                    return view('web::partials.corporation', compact('corporation', 'character_id'));
                }
                return view('web::partials.unknown', [
                    'unknown_id' => $row->first_party_id,
                    'character_id' => $character_id,
                ]);
            })
            ->addColumn('main_view', function ($row) {
                $character = User::find($row->first_party_id);
                if ($character) {
                    $character = User::find($character->group->main_character_id);
                    $character_id = $character->group->getMainCharacterIdAttribute();
                    return view('web::partials.character', compact('character', 'character_id'));
                } else {
                    return 'Not on SeAT';
                }

            })
            ->addColumn('main_character_name', function ($row) {
                $user = User::find($row->first_party_id);
                if (!is_null($user)) {
                    return CharacterInfo::find(User::find($row->first_party_id)->group->main_character_id)->name;
                }
                return "Not on seat";
            })
            ->editColumn('total_payments', function ($row) {
                return number($row->total_payments);
            })
            ->rawColumns(['first_party_id', 'main_view', 'character', 'total_payments', 'sum'])
            ->make(true);
    }

    #TODO Move to own controller
    public function getTaxPaymentTotalsView($startdate = null, $enddate = null)
    {
        if ($startdate != null and $enddate != null) {

            $daterange = ['start' => $startdate, 'end' => $enddate];

        } else {
            $daterange = ['start' => '2018-10-01T00:00:00.000Z', 'end' => '2035-02-01T00:00:00.000Z'];
        }
        return view('whtools::bluetaxpaymenttotals', compact('daterange'));
    }
}
