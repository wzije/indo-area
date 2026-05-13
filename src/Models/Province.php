<?php

namespace Wzije\IndoArea\Models;

use Illuminate\Database\Eloquent\Model;


class Province extends Model
{

    protected $connection = 'sqlite_indo_area';

    protected $table = 'provinces';

    public function cities()
    {
        return $this->hasMany(City::class, 'city_province_code', "province_code");
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'province_country_code', 'country_code');
    }
}
