<?php

namespace Wzije\IndoArea\Models;

use Illuminate\Database\Eloquent\Model;
use Wzije\IndoArea\Models\SubDistrict;


class City extends Model
{

    protected $connection = 'sqlite_indo_area';

    protected $table = 'cities';

    public function districts()
    {
        return $this->hasMany(SubDistrict::class, 'sub_district_city_code', 'city_code');
    }

    public function city()
    {
        return $this->belongsTo(Province::class, "city_province_code", "province_code");
    }
}
