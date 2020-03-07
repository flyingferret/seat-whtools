<?php


namespace FlyingFerret\Seat\WHTools\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateSkill extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'skillID';

    protected $table = 'whtools-certificates_skills';

    protected $fillable = ['skillID', 'requiredLvl', 'certRank'];

    public function certificates()
    {
        return $this->hasMany('FlyingFerret\Seat\WHTools\Models\Certificate', 'certID', 'certID');
    }
}