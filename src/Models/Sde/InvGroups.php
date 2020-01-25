<?php


namespace FlyingFerret\Seat\WHTools\Models\Sde;

use Illuminate\Database\Eloquent\Model;

class InvGroups extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'invGroups';

    protected $primaryKey = 'groupID, categoryID';
}