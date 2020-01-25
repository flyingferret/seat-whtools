<?php


namespace FlyingFerret\Seat\WHTools\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'certID';

    protected $table = 'whtools-certificates';

    protected $fillable = ['name','description'];

    public function skills(){
        return $this->hasMany('FlyingFerret\Seat\WHTools\Models\CertificateSkill','certID','certID');
    }
}