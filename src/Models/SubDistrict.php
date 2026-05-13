<?php

namespace Wzije\IndoArea\Models;

use Illuminate\Database\Eloquent\Model;


class SubDistrict extends Model
{

    protected $connection = 'sqlite_indo_area';

    protected $table = 'subsdistricts';

    public function city()
    {
        return $this->belongsTo(City::class, "sub_district_city_code", "city_code");
    }

    public function villages()
    {
        return $this->hasMany(Village::class, "village_sub_district_code", "sub_district_code");
    }
}
