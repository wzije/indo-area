<?php

namespace Wzije\IndoArea\Models;

use Illuminate\Database\Eloquent\Model;
use Wzije\IndoArea\Models\SubDistrict;


class Regency extends Model
{

    protected $connection = 'sqlite_indo_area';

    protected $table = 'reg_regencies';

    public function districts()
    {
        return $this->hasMany(District::class, 'regency_id', 'id');
    }
}
