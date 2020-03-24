<?php


namespace FlyingFerret\Seat\WHTools\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateRankLootFactor extends Model
{
    public $timestamps = true;

    protected $table = 'whtools-certificates_rank_loot_factor';

    protected $fillable = ['rank', 'factor'];

    public function certificate()
    {
        return $this->hasMany('FlyingFerret\Seat\WHTools\Models\Certificate', 'certID', 'certID');
    }

}