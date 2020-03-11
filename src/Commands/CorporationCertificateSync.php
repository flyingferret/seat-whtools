<?php
/**
 * Created by PhpStorm.
 * User: Mutterschiff
 * Date: 06.02.2018
 * Time: 23:22.
 */

namespace FlyingFerret\Seat\WHTools\Commands;

use Seat\Eveapi\Models\Corporation\CorporationInfo;
use FlyingFerret\Seat\WHTools\Jobs\CertificatesSync;
use Illuminate\Console\Command;

class CorporationCertificateSync extends Command
{

    protected $signature = 'seat-whtools:CorporationCertificates:sync {--corporation_ids= : The id list of characters in SeAT (using , as separator)}';

    protected $description = 'Fire a job which attempts to update certificate ranks for all characters in the give corporation';

    public function handle()
    {

        if (!is_null($this->option('corporation_ids'))) {
            // transform the argument list in an array
            $ids = explode(',', $this->option('corporation_ids'));
            $corporation_ids = collect();

            $corporations = CorporationInfo::whereIn('corporation_id', $ids)->get();

        } else {
            $corporations = CorporationInfo::all();
            $this->info('A synchronization job has been queued in order to update all Corporation Member certificates.');
        }
        foreach ($corporations as $corp) {
            dispatch(new CertificatesSync($corp));
        }
    }
}