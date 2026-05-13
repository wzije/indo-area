<?php

namespace Wzije\IndoArea\Models;

use Illuminate\Database\Eloquent\Model;


class Village extends Model
{

    protected $connection = 'sqlite_indo_area';

    protected $table = 'villages';

    public function subDistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'village_sub_district_code', 'sub_district_code');
    }
}
