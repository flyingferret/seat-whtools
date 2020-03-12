<?php


namespace FlyingFerret\Seat\WHTools\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateRankLootFactor extends Model
{
    public $timestamps = true;

    protected $primaryKey = 'id';

    protected $table = 'whtools-certificates_rank_loot_factor';

    protected $fillable = ['id', 'certID', 'rank', 'factor'];

    public function certificate()
    {
        return $this->hasOne('FlyingFerret\Seat\WHTools\Models\Certificate', 'certID', 'certID');
    }

}